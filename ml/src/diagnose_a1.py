"""
Диагностика: почему класс A1 не распознаётся (recall=0 на test у финальной
модели). Ничего не меняет и не сохраняет — только читает уже посчитанные
train/validation/test и печатает разбор. Финальная модель
(ml/models/cefr_model.joblib) не трогается; вариант D переобучается здесь
заново (тем же кодом, что и train_baseline.py) только для сравнения.

Важное уточнение по именованию (в задаче на диагностику имена C/D были
перепутаны местами): в РЕАЛЬНОМ коде train_baseline.py —
  C_complexity_balanced   = TF-IDF + признаки сложности + class_weight='balanced'
  D_complexity_unbalanced = TF-IDF + признаки сложности + class_weight=None
Ниже везде используются эти настоящие имена/настройки из кода, а не
формулировка из задачи — иначе разбор был бы про несуществующую модель.
"""

import sys
import json
from pathlib import Path

import joblib
import numpy as np
import pandas as pd
from sklearn.metrics import (
    cohen_kappa_score,
    confusion_matrix,
    f1_score,
    precision_recall_fscore_support,
)
from sklearn.utils.class_weight import compute_class_weight

ROOT = Path(__file__).resolve().parent.parent
sys.path.insert(0, str(ROOT / "src"))

from train_baseline import build_pipeline  # noqa: E402
from metrics import LEVELS  # noqa: E402

DATA_DIR = ROOT / "data"
MODELS_DIR = ROOT / "models"
REPORTS_DIR = ROOT / "reports"

A1 = LEVELS.index("A1")
A2 = LEVELS.index("A2")


def line(title):
    print("\n" + "=" * 78)
    print(title)
    print("=" * 78)


def main():
    train = pd.read_csv(DATA_DIR / "train.csv")
    val = pd.read_csv(DATA_DIR / "validation.csv")
    test = pd.read_csv(DATA_DIR / "test.csv")

    X_train, y_train = train["text"].tolist(), train["level_code"].tolist()
    X_val, y_val = val["text"].tolist(), val["level_code"].tolist()
    X_test, y_test = test["text"].tolist(), test["level_code"].tolist()

    # ---------------------------------------------------------------- 1 ---
    line("1. Какая конфигурация выбрана финальной и по какому правилу")
    info = json.loads((MODELS_DIR / "model_info.json").read_text(encoding="utf-8"))
    selection = pd.read_csv(REPORTS_DIR / "model_selection.csv")
    print(f"best_config (model_info.json) = {info['best_config']}")
    print(
        "Правило отбора (pick_best() в train_baseline.py): максимум macro_f1 на "
        "validation; ЕСЛИ разница с ближайшим конкурентом < 0.01 И у конкурента "
        "recall_A1 > 0, а у лидера recall_A1 == 0 — берём конкурента вместо лидера."
    )
    print("\nТаблица отбора (ml/reports/model_selection.csv):")
    print(selection.to_string(index=False))
    c_row = selection[selection["config"] == "C_complexity_balanced"].iloc[0]
    d_row = selection[selection["config"] == "D_complexity_unbalanced"].iloc[0]
    print(f"\nC_complexity_balanced (class_weight='balanced'): macro_f1={c_row['macro_f1']:.4f}, recall_A1={c_row['recall_A1']:.3f}")
    print(f"D_complexity_unbalanced (class_weight=None):      macro_f1={d_row['macro_f1']:.4f}, recall_A1={d_row['recall_A1']:.3f}")
    gap = c_row["macro_f1"] - d_row["macro_f1"]
    print(f"Разница macro_f1 (C - D) = {gap:.4f} ({'< 0.01, tie-break мог сработать' if abs(gap) < 0.01 else '>= 0.01, выбор по чистому macro_f1, tie-break не нужен'})")
    print(f"=> Выбран {info['best_config']} — {'по прямому macro_f1' if abs(gap) >= 0.01 or c_row['macro_f1'] >= d_row['macro_f1'] else 'по tie-break'}.")

    # Обучаем оба варианта заново (train_baseline.py сохраняет только
    # победителя, вариант D в файле не остаётся) — нужны для пп. 2-7.
    print("\nПереобучаю C и D локально для дальнейшего разбора (это не меняет ml/models/cefr_model.joblib)...")
    pipeline_C = build_pipeline(class_weight="balanced", with_complexity=True)
    pipeline_C.fit(X_train, y_train)
    pipeline_D = build_pipeline(class_weight=None, with_complexity=True)
    pipeline_D.fit(X_train, y_train)
    final_pipeline = joblib.load(MODELS_DIR / "cefr_model.joblib")

    # ---------------------------------------------------------------- 2 ---
    line("2. Вариант D (class_weight=None) на validation — метрики по A1")
    pred_D_val = pipeline_D.predict(X_val)
    precision, recall, f1, support = precision_recall_fscore_support(
        y_val, pred_D_val, labels=LEVELS, zero_division=0
    )
    print(f"A1: precision={precision[A1]:.3f}  recall={recall[A1]:.3f}  f1={f1[A1]:.3f}  (истинных A1 в validation: {int(support[A1])})")
    n_pred_a1_D = int((np.array(pred_D_val) == "A1").sum())
    print(f"Сколько раз модель D вообще предсказала A1 на validation: {n_pred_a1_D}")

    # ---------------------------------------------------------------- 3 ---
    line("3. class_weight реально доходит до классификатора? (вариант D)")
    params_D = pipeline_D.get_params()
    print("pipeline_D.get_params()['clf__class_weight'] =", repr(params_D.get("clf__class_weight")))
    clf_D = pipeline_D.named_steps["clf"]
    print("clf_D.class_weight (после fit) =", repr(clf_D.class_weight))
    print("clf_D.classes_ =", list(clf_D.classes_))

    print("\nДля сравнения — вариант C (class_weight='balanced'):")
    params_C = pipeline_C.get_params()
    print("pipeline_C.get_params()['clf__class_weight'] =", repr(params_C.get("clf__class_weight")))
    clf_C = pipeline_C.named_steps["clf"]
    print("clf_C.class_weight (после fit) =", repr(clf_C.class_weight))

    actual_weights = compute_class_weight("balanced", classes=np.array(LEVELS), y=np.array(y_train))
    print("\nФактические веса классов sklearn.utils.class_weight.compute_class_weight('balanced', classes=LEVELS, y=y_train):")
    for lvl, w in zip(LEVELS, actual_weights):
        print(f"  {lvl}: {w:.3f}")
    print(
        "(Эти веса применяются ТОЛЬКО у C — у D class_weight=None, поэтому "
        "все классы в функции потерь имеют вес 1.0 независимо от частоты.)"
    )

    # ---------------------------------------------------------------- 4 ---
    line("4. Количество примеров каждого уровня в train / validation / test")
    dist = pd.read_csv(REPORTS_DIR / "class_distribution.csv", index_col=0)
    print(dist.to_string())

    # ---------------------------------------------------------------- 5 ---
    line("5. Строка A1 из confusion matrix на test — финальная модель и вариант D")
    pred_final_test = final_pipeline.predict(X_test)
    pred_D_test = pipeline_D.predict(X_test)

    cm_final = confusion_matrix(y_test, pred_final_test, labels=LEVELS)
    cm_D = confusion_matrix(y_test, pred_D_test, labels=LEVELS)

    row_df = pd.DataFrame(
        [cm_final[A1], cm_D[A1]],
        columns=LEVELS,
        index=[f"Финальная ({info['best_config']})", "D (class_weight=None)"],
    )
    print("Истинные A1-предложения (test) распределились по предсказанным уровням:")
    print(row_df.to_string())

    # ---------------------------------------------------------------- 6 ---
    line("6. Средняя предсказанная вероятность класса A1 (вариант D, validation)")
    proba_D_val = pipeline_D.predict_proba(X_val)
    classes_D = list(clf_D.classes_)
    a1_col = classes_D.index("A1")
    y_val_arr = np.array(y_val)

    mean_proba_true_a1 = proba_D_val[y_val_arr == "A1", a1_col].mean()
    mean_proba_true_a2 = proba_D_val[y_val_arr == "A2", a1_col].mean()
    print(f"P(A1) в среднем для НАСТОЯЩИХ A1-предложений: {mean_proba_true_a1:.4f}")
    print(f"P(A1) в среднем для НАСТОЯЩИХ A2-предложений: {mean_proba_true_a2:.4f}")
    print(f"Отношение: {mean_proba_true_a1 / mean_proba_true_a2:.2f}x" if mean_proba_true_a2 > 0 else "")

    # ---------------------------------------------------------------- 7 ---
    line("7. Сдвиг порога: P(A1) * коэффициент перед argmax (вариант D, validation)")
    print(f"{'коэфф.':>7} {'recall_A1':>10} {'precision_A1':>13} {'macro_F1':>9} {'QWK':>7}")
    for m in [1.5, 2, 3, 4]:
        adjusted = proba_D_val.copy()
        adjusted[:, a1_col] *= m
        pred_idx = adjusted.argmax(axis=1)
        pred_labels = [classes_D[i] for i in pred_idx]

        p, r, f, s = precision_recall_fscore_support(y_val, pred_labels, labels=LEVELS, zero_division=0)
        macro_f1_shifted = f1_score(y_val, pred_labels, average="macro", zero_division=0)
        qwk_shifted = cohen_kappa_score(y_val, pred_labels, labels=LEVELS, weights="quadratic")
        print(f"{m:>7} {r[A1]:>10.3f} {p[A1]:>13.3f} {macro_f1_shifted:>9.3f} {qwk_shifted:>7.3f}")

    print("\n(Без сдвига, коэфф.=1.0, для сравнения — из п.2:)")
    macro_f1_base = f1_score(y_val, pred_D_val, average="macro", zero_division=0)
    qwk_base = cohen_kappa_score(y_val, pred_D_val, labels=LEVELS, weights="quadratic")
    print(f"{1.0:>7} {recall[A1]:>10.3f} {precision[A1]:>13.3f} {macro_f1_base:>9.3f} {qwk_base:>7.3f}")

    print("\n(Тест НЕ трогали — по заданию сдвиг проверяется только на validation.)")


if __name__ == "__main__":
    main()
