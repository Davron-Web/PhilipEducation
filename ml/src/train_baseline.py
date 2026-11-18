"""
Обучает и сравнивает несколько конфигураций TF-IDF + LogisticRegression,
выбирает лучшую по macro F1 на validation (с поправкой на recall A1 —
см. pick_best()) и сохраняет её в ml/models/.

Почему не нейросеть (DistilBERT) — см. ml/README.md, раздел «Почему не
дообучали DistilBERT»: в этом окружении сеть отдаёт ~25 КБ/с на любой CDN
(PyPI, HuggingFace, GitHub — проверено вручную), а torch+transformers+веса
модели — это уже несколько сотен МБ, то есть часы одной только загрузки.
Baseline на TF-IDF — не компромисс «раз нет мощного железа», а сознательный
выбор под конкретное ограничение среды.

Экспериментальная сетка (отбор — по macro F1 на validation, ФИНАЛЬНАЯ
проверка — только на test в evaluate.py):

  A. TF-IDF(word 1-2gram)                                + LogReg(class_weight=None)
  B. TF-IDF(word 1-2gram)                                + LogReg(class_weight='balanced')
  C. TF-IDF(word 1-2gram) + признаки сложности (scaled)  + LogReg(class_weight='balanced')
  D. TF-IDF(word 1-2gram) + признаки сложности (scaled)  + LogReg(class_weight=None)

A vs B показывает, помогает ли балансировка классов при перекосе выборки
без признаков сложности. C vs D — то же самое, но с признаками сложности:
это чище, чем A vs B, потому что различается только один параметр
(class_weight), а не два сразу.
B vs C показывает, добавляют ли признаки сложности текста что-то поверх
TF-IDF (гипотеза задания).

Признаки сложности (ветка "complexity") масштабируются StandardScaler()
внутри FeatureUnion — TF-IDF-ветка не трогается. Это НЕ то же самое, что
масштабировать всю матрицу целиком (пробовали раньше — портило качество,
см. ml/reports/scaler_experiment.json и results.md): признаки сложности —
это 6 плотных чисел с очень разным масштабом (word_count ~сотни,
lexical_diversity ~0-1), TF-IDF же и так на сравнимом масштабе за счёт
sublinear_tf. Масштабировать стоило только то, что реально в этом
нуждалось.
"""

import csv
import json
import warnings
from pathlib import Path

import joblib
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
from sklearn.pipeline import FeatureUnion, Pipeline
from sklearn.preprocessing import StandardScaler

from features import ComplexityFeaturizer
from metrics import LEVELS, score_predictions

ROOT = Path(__file__).resolve().parent.parent
DATA_DIR = ROOT / "data"
MODELS_DIR = ROOT / "models"
REPORTS_DIR = ROOT / "reports"

METRIC_COLUMNS = ["accuracy", "macro_f1", "adjacent_accuracy", "quadratic_weighted_kappa", "recall_A1", "recall_C2"]

# «Близкие значения» macro F1 для tie-break при выборе финальной модели —
# см. pick_best(). 0.01 — примерно та величина, в пределах которой в наших
# прогонах колебался результат одной и той же конфигурации между запусками
# (см. C_tfidf_plus_complexity: 0.379 в первом прогоне, 0.377 после
# исправления ConvergenceWarning) — то есть разница меньше этого порога
# неотличима от шума обучения, и предпочесть модель с ненулевым recall на
# A1 в этих пределах осмысленно.
CLOSE_MACRO_F1_THRESHOLD = 0.01


def build_pipeline(*, class_weight, with_complexity: bool) -> Pipeline:
    tfidf = TfidfVectorizer(
        analyzer="word",
        ngram_range=(1, 2),
        min_df=2,
        max_features=20000,
        sublinear_tf=True,
    )

    if with_complexity:
        # StandardScaler() (с центрированием — с_mean=True по умолчанию)
        # безопасен здесь: ComplexityFeaturizer выдаёт маленькую ПЛОТНУЮ
        # матрицу (6 чисел), не разреженную TF-IDF — центрирование её не
        # "разрежает" и не ломается на ней, в отличие от смешанного случая.
        complexity_branch = Pipeline([
            ("complexity", ComplexityFeaturizer()),
            ("scale", StandardScaler()),
        ])
        features = FeatureUnion([("tfidf", tfidf), ("complexity", complexity_branch)])
    else:
        features = FeatureUnion([("tfidf", tfidf)])

    # multi_class="multinomial" раньше указывали явно; в новых версиях
    # scikit-learn (1.7+) параметр убран — solver='lbfgs' (по умолчанию)
    # сам всегда решает многоклассовую задачу как multinomial.
    #
    # max_iter=5000 (не 2000) — чинит ConvergenceWarning без побочных
    # эффектов, см. комментарий выше про масштабирование только complexity
    # и ml/reports/results.md, раздел «Исправление ConvergenceWarning».
    clf = LogisticRegression(
        max_iter=5000,
        class_weight=class_weight,
    )

    return Pipeline([("features", features), ("clf", clf)])


def pick_best(rows: list[dict]) -> dict:
    """
    Лучшая конфигурация — по macro F1 на validation. Но если топ-1 и
    следующая по macro F1 конфигурация отличаются меньше чем на
    CLOSE_MACRO_F1_THRESHOLD (то есть практически неразличимы), а у топ-1
    recall на A1 нулевой (класс с наименьшим числом примеров — модель
    научилась его полностью игнорировать), а у "близкой" — нет, берём
    "близкую": нулевой recall на целом уровне — серьёзный практический
    изъян, который macro F1 сам по себе не обязан вылавливать конкретно.
    """
    ranked = sorted(rows, key=lambda r: r["macro_f1"], reverse=True)
    best = ranked[0]

    for candidate in ranked[1:]:
        if best["macro_f1"] - candidate["macro_f1"] > CLOSE_MACRO_F1_THRESHOLD:
            break
        if candidate["recall_A1"] > 0 and best["recall_A1"] == 0:
            best = candidate

    return best


def main() -> None:
    MODELS_DIR.mkdir(parents=True, exist_ok=True)
    REPORTS_DIR.mkdir(parents=True, exist_ok=True)

    train = pd.read_csv(DATA_DIR / "train.csv")
    val = pd.read_csv(DATA_DIR / "validation.csv")

    X_train, y_train = train["text"].tolist(), train["level_code"].tolist()
    X_val, y_val = val["text"].tolist(), val["level_code"].tolist()

    configs = [
        ("A_tfidf_unbalanced", dict(class_weight=None, with_complexity=False)),
        ("B_tfidf_balanced", dict(class_weight="balanced", with_complexity=False)),
        ("C_complexity_balanced", dict(class_weight="balanced", with_complexity=True)),
        ("D_complexity_unbalanced", dict(class_weight=None, with_complexity=True)),
    ]

    rows = []
    pipelines = {}

    for name, params in configs:
        print(f"Обучаю конфигурацию {name}...")

        with warnings.catch_warnings(record=True) as caught:
            warnings.simplefilter("always")
            pipeline = build_pipeline(**params)
            pipeline.fit(X_train, y_train)
            conv_warnings = [w for w in caught if "onverge" in str(w.message)]

        pipelines[name] = pipeline

        metrics = score_predictions(y_val, pipeline.predict(X_val))
        rows.append({"config": name, **metrics})

        warn_note = f" ⚠ ConvergenceWarning x{len(conv_warnings)}" if conv_warnings else " (без ConvergenceWarning)"
        print(
            f"  validation: accuracy={metrics['accuracy']:.3f} macro_f1={metrics['macro_f1']:.3f} "
            f"QWK={metrics['quadratic_weighted_kappa']:.3f} adjacent_acc={metrics['adjacent_accuracy']:.3f} "
            f"recall_A1={metrics['recall_A1']:.3f} recall_C2={metrics['recall_C2']:.3f}{warn_note}"
        )

    with (REPORTS_DIR / "model_selection.csv").open("w", newline="", encoding="utf-8") as f:
        writer = csv.DictWriter(f, fieldnames=["config", *METRIC_COLUMNS])
        writer.writeheader()
        writer.writerows(rows)

    best_row = pick_best(rows)
    best_name = best_row["config"]
    best_pipeline = pipelines[best_name]

    print(f"\nЛучшая конфигурация: {best_name} (macro F1 на validation = {best_row['macro_f1']:.3f})")

    model_path = MODELS_DIR / "cefr_model.joblib"
    joblib.dump(best_pipeline, model_path)

    info = {
        "best_config": best_name,
        "validation_metrics": best_row,
        "validation_macro_f1": best_row["macro_f1"],  # для обратной совместимости со старыми отчётами
        "levels": LEVELS,
        "sklearn_pipeline": "FeatureUnion(tfidf[, complexity->StandardScaler]) + LogisticRegression",
    }
    (MODELS_DIR / "model_info.json").write_text(json.dumps(info, indent=2, ensure_ascii=False), encoding="utf-8")

    print(f"Модель сохранена: {model_path}")


if __name__ == "__main__":
    main()
