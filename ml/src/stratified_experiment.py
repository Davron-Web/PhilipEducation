"""
Диагностика перекоса классов между train/validation/test (см. п.4 прошлой
диагностики A1). НЕ заменяет официальные сплиты CEFR-SP — сохраняет
стратифицированное разбиение ОТДЕЛЬНО (ml/data/stratified_*.csv) и ничего
не перезаписывает поверх ml/data/{train,validation,test}.csv или
ml/models/cefr_model.joblib.

Делает по порядку:
  1. Стратифицированное разбиение всех 10004 строк 70/15/15 (stratify по
     level_code, random_state=42) — сравнение с официальным по источникам.
  2. Обучает C (class_weight='balanced') и D (class_weight=None) на новом
     train, отбирает по тому же правилу (pick_best), что и train_baseline.py.
  3. Полные метрики выбранной модели на новом test + строка A1 confusion matrix.
  4. Stratified 5-fold CV по всем 10004 строкам для конфигурации C
     (финальной по основному, официальному разбиению) — среднее ± std.
"""

import sys
from pathlib import Path

import joblib
import numpy as np
import pandas as pd
from sklearn.metrics import classification_report, cohen_kappa_score, confusion_matrix, f1_score
from sklearn.model_selection import StratifiedKFold, train_test_split

ROOT = Path(__file__).resolve().parent.parent
sys.path.insert(0, str(ROOT / "src"))

from train_baseline import build_pipeline, pick_best  # noqa: E402
from metrics import LEVELS, score_predictions  # noqa: E402

DATA_DIR = ROOT / "data"
REPORTS_DIR = ROOT / "reports"

A1 = LEVELS.index("A1")
C2 = LEVELS.index("C2")


def line(title):
    print("\n" + "=" * 78)
    print(title)
    print("=" * 78)


def main():
    train = pd.read_csv(DATA_DIR / "train.csv")
    val = pd.read_csv(DATA_DIR / "validation.csv")
    test = pd.read_csv(DATA_DIR / "test.csv")
    for df, name in [(train, "train"), (val, "validation"), (test, "test")]:
        df["split_orig"] = name
    combined = pd.concat([train, val, test], ignore_index=True)
    print(f"Всего строк (train+validation+test официального разбиения): {len(combined)}")

    # ------------------------------------------------------------ шаг 1 ---
    line("1. Стратифицированное разбиение 70/15/15 (stratify=level_code, random_state=42)")
    strat_train, temp = train_test_split(
        combined, test_size=0.30, stratify=combined["level_code"], random_state=42
    )
    strat_val, strat_test = train_test_split(
        temp, test_size=0.50, stratify=temp["level_code"], random_state=42
    )
    print(f"stratified train: {len(strat_train)} ({len(strat_train) / len(combined):.1%})")
    print(f"stratified validation: {len(strat_val)} ({len(strat_val) / len(combined):.1%})")
    print(f"stratified test: {len(strat_test)} ({len(strat_test) / len(combined):.1%})")

    for df, name in [(strat_train, "stratified_train"), (strat_val, "stratified_validation"), (strat_test, "stratified_test")]:
        out = df[["text", "level_num", "level_code"]].copy()
        out.to_csv(DATA_DIR / f"{name}.csv", index=False)
        print(f"  сохранено -> ml/data/{name}.csv")

    dist_strat = (
        pd.concat(
            [
                strat_train.assign(split="stratified_train"),
                strat_val.assign(split="stratified_validation"),
                strat_test.assign(split="stratified_test"),
            ]
        )
        .groupby(["split", "level_code"])
        .size()
        .unstack(fill_value=0)
        .reindex(columns=LEVELS, fill_value=0)
        .reindex(["stratified_train", "stratified_validation", "stratified_test"])
    )
    dist_pct = dist_strat.div(dist_strat.sum(axis=1), axis=0) * 100
    print("\nРаспределение классов, новое стратифицированное разбиение (доля, %):")
    print(dist_pct.round(2).to_string())
    dist_strat.to_csv(REPORTS_DIR / "class_distribution_stratified.csv")

    print("\nДля сравнения — официальное разбиение (доля, %), из class_distribution.csv:")
    dist_orig = pd.read_csv(REPORTS_DIR / "class_distribution.csv", index_col=0)
    dist_orig_pct = dist_orig.div(dist_orig.sum(axis=1), axis=0) * 100
    print(dist_orig_pct.round(2).to_string())

    # ------------------------------------------------------------ шаг 2 ---
    line("2. Колонка источника (корпуса) в данных")
    print(
        "В скачанных данных (ml/data_raw/*.csv, поля text/annotator1/annotator2 — "
        "ровно то, что отдаёт HF datasets-server для "
        "edesaras/CEFR-Sentence-Level-Annotations) колонки источника НЕТ и не было — "
        "проверено напрямую через API датасета: features = [text, Annotator I, "
        "Annotator II], больше ничего. Поэтому распределение уровней по источникам "
        "напрямую вывести нельзя — источник (Wiki-Auto / SCoRE / Newsela-Auto) "
        "в этом зеркале не сохранён построчно.\n"
        "Косвенно: сам факт, что официальное разбиение (шаг 1 выше) даёт разные "
        "доли классов в train/val/test, СОГЛАСУЕТСЯ с тем, что оригинальный "
        "репозиторий CEFR-SP делит train/validation/test по источникам, а не "
        "перемешивает случайно (см. п.1 текущей задачи и README.md исходного "
        "корпуса) — разные источники естественно дают разный состав уровней."
    )

    # ------------------------------------------------------------ шаг 3 ---
    line("3. (см. README.md исходного корпуса, процитировано отдельно в ответе)")

    # ------------------------------------------------------------ шаг 4 ---
    line("4. Обучение C и D на стратифицированном разбиении, отбор, метрики на новом test")

    X_train_s = strat_train["text"].tolist()
    y_train_s = strat_train["level_code"].tolist()
    X_val_s = strat_val["text"].tolist()
    y_val_s = strat_val["level_code"].tolist()
    X_test_s = strat_test["text"].tolist()
    y_test_s = strat_test["level_code"].tolist()

    configs = [
        ("C_complexity_balanced", dict(class_weight="balanced", with_complexity=True)),
        ("D_complexity_unbalanced", dict(class_weight=None, with_complexity=True)),
    ]

    rows = []
    pipelines = {}
    for name, params in configs:
        print(f"Обучаю {name} на стратифицированном train...")
        pipe = build_pipeline(**params)
        pipe.fit(X_train_s, y_train_s)
        pipelines[name] = pipe
        m = score_predictions(y_val_s, pipe.predict(X_val_s))
        rows.append({"config": name, **m})
        print(f"  validation (stratified): macro_f1={m['macro_f1']:.3f} recall_A1={m['recall_A1']:.3f} recall_C2={m['recall_C2']:.3f}")

    best_row = pick_best(rows)
    best_name = best_row["config"]
    print(f"\nВыбрана (то же правило pick_best): {best_name} (macro F1 на stratified validation = {best_row['macro_f1']:.3f})")
    best_pipeline = pipelines[best_name]

    pred_test_s = best_pipeline.predict(X_test_s)
    test_scores_s = score_predictions(y_test_s, pred_test_s)
    report_s = classification_report(y_test_s, pred_test_s, labels=LEVELS, digits=3, zero_division=0, output_dict=True)
    cm_s = confusion_matrix(y_test_s, pred_test_s, labels=LEVELS)

    print(f"\nМетрики {best_name} на stratified test:")
    print(f"  accuracy={test_scores_s['accuracy']:.3f}  macro_f1={test_scores_s['macro_f1']:.3f}  "
          f"adjacent_accuracy={test_scores_s['adjacent_accuracy']:.3f}  QWK={test_scores_s['quadratic_weighted_kappa']:.3f}")
    print("  per-class F1:")
    for lvl in LEVELS:
        print(f"    {lvl}: precision={report_s[lvl]['precision']:.3f} recall={report_s[lvl]['recall']:.3f} f1={report_s[lvl]['f1-score']:.3f} (support={int(report_s[lvl]['support'])})")
    print(f"\n  Строка A1 confusion matrix (stratified test), predicted -> {LEVELS}:")
    print(f"  {cm_s[A1].tolist()}")

    # Сохраняем результаты шага 4 для итоговой таблицы и results.md/README.md.
    stratified_result = {
        "best_config": best_name,
        "validation_macro_f1": best_row["macro_f1"],
        "test_metrics": test_scores_s,
        "test_report": report_s,
        "confusion_A1_row": cm_s[A1].tolist(),
        "confusion_labels": LEVELS,
    }
    import json
    (REPORTS_DIR / "stratified_experiment.json").write_text(
        json.dumps(stratified_result, indent=2, ensure_ascii=False), encoding="utf-8"
    )
    selection_s = pd.DataFrame(rows)
    selection_s.to_csv(REPORTS_DIR / "stratified_model_selection.csv", index=False)
    print(f"\nСохранено: ml/reports/stratified_experiment.json, ml/reports/stratified_model_selection.csv")

    # ------------------------------------------------------------ шаг 5 ---
    line("5. Stratified 5-fold CV по всем 10004 строкам — конфигурация C_complexity_balanced")

    X_all = combined["text"].to_numpy()
    y_all = combined["level_code"].to_numpy()

    skf = StratifiedKFold(n_splits=5, shuffle=True, random_state=42)
    fold_metrics = []
    for fold_idx, (train_idx, test_idx) in enumerate(skf.split(X_all, y_all), start=1):
        pipe = build_pipeline(class_weight="balanced", with_complexity=True)
        pipe.fit(X_all[train_idx].tolist(), y_all[train_idx].tolist())
        pred = pipe.predict(X_all[test_idx].tolist())
        y_true_fold = y_all[test_idx].tolist()

        macro_f1 = f1_score(y_true_fold, pred, average="macro", zero_division=0)
        qwk = cohen_kappa_score(y_true_fold, pred, labels=LEVELS, weights="quadratic")
        rep = classification_report(y_true_fold, pred, labels=LEVELS, zero_division=0, output_dict=True)
        f1_a1 = rep["A1"]["f1-score"]
        f1_c2 = rep["C2"]["f1-score"]

        fold_metrics.append({"fold": fold_idx, "macro_f1": macro_f1, "qwk": qwk, "f1_A1": f1_a1, "f1_C2": f1_c2})
        print(f"  fold {fold_idx}: macro_f1={macro_f1:.3f} QWK={qwk:.3f} F1_A1={f1_a1:.3f} F1_C2={f1_c2:.3f}")

    cv_df = pd.DataFrame(fold_metrics)
    cv_df.to_csv(REPORTS_DIR / "cv_5fold_results.csv", index=False)

    cv_summary = {
        "macro_f1_mean": cv_df["macro_f1"].mean(), "macro_f1_std": cv_df["macro_f1"].std(),
        "qwk_mean": cv_df["qwk"].mean(), "qwk_std": cv_df["qwk"].std(),
        "f1_A1_mean": cv_df["f1_A1"].mean(), "f1_A1_std": cv_df["f1_A1"].std(),
        "f1_C2_mean": cv_df["f1_C2"].mean(), "f1_C2_std": cv_df["f1_C2"].std(),
    }
    print("\n5-fold CV, среднее ± std:")
    print(f"  macro F1 = {cv_summary['macro_f1_mean']:.3f} ± {cv_summary['macro_f1_std']:.3f}")
    print(f"  QWK      = {cv_summary['qwk_mean']:.3f} ± {cv_summary['qwk_std']:.3f}")
    print(f"  F1(A1)   = {cv_summary['f1_A1_mean']:.3f} ± {cv_summary['f1_A1_std']:.3f}")
    print(f"  F1(C2)   = {cv_summary['f1_C2_mean']:.3f} ± {cv_summary['f1_C2_std']:.3f}")

    import json as json2
    (REPORTS_DIR / "cv_5fold_summary.json").write_text(json2.dumps(cv_summary, indent=2), encoding="utf-8")
    print("\nСохранено: ml/reports/cv_5fold_results.csv, ml/reports/cv_5fold_summary.json")


if __name__ == "__main__":
    main()
