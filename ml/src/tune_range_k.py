"""
Правило показа диапазона уровней (шаг 2.3): берём самый вероятный
уровень; если у более вероятного из двух соседей (по шкале A1..C2)
вероятность >= k * вероятность лучшего — показываем диапазон из ДВУХ
соседних уровней (никогда не шире). Подбираем k на официальном
validation, финальная проверка выбранного k — один раз на test.
"""

import json
import sys
from pathlib import Path

import joblib
import pandas as pd

ROOT = Path(__file__).resolve().parent.parent
sys.path.insert(0, str(ROOT / "src"))

from metrics import LEVELS  # noqa: E402
from range_rule import decide_range  # noqa: E402

DATA_DIR = ROOT / "data"
MODELS_DIR = ROOT / "models"
REPORTS_DIR = ROOT / "reports"

A1 = "A1"
C2 = "C2"


def evaluate_k(pipeline, X, y_true, k: float) -> dict:
    proba_matrix = pipeline.predict_proba(X)
    classes = list(pipeline.classes_)

    n = len(X)
    n_ranges = 0
    n_covered = 0
    a1_total = a1_covered = 0
    c2_total = c2_covered = 0

    for i in range(n):
        probabilities = {cls: float(p) for cls, p in zip(classes, proba_matrix[i])}
        best, low, high = decide_range(probabilities, k)
        true_level = y_true[i]

        if low is not None:
            n_ranges += 1
            covered = true_level in (low, high)
        else:
            covered = true_level == best

        if covered:
            n_covered += 1

        if true_level == A1:
            a1_total += 1
            a1_covered += int(covered)
        if true_level == C2:
            c2_total += 1
            c2_covered += int(covered)

    return {
        "k": k,
        "range_share": n_ranges / n,
        "coverage": n_covered / n,
        "coverage_A1": a1_covered / a1_total if a1_total else None,
        "coverage_C2": c2_covered / c2_total if c2_total else None,
        "n_A1": a1_total,
        "n_C2": c2_total,
    }


def main() -> None:
    pipeline = joblib.load(MODELS_DIR / "cefr_model.joblib")

    val = pd.read_csv(DATA_DIR / "validation.csv")
    X_val, y_val = val["text"].tolist(), val["level_code"].tolist()

    print("=" * 90)
    print("Подбор k на официальном validation")
    print("=" * 90)
    print(f"{'k':>5} {'доля диапазонов':>17} {'покрытие':>10} {'покрытие A1':>12} {'покрытие C2':>12}")

    rows = []
    for k in [0.3, 0.4, 0.5, 0.6, 0.7]:
        m = evaluate_k(pipeline, X_val, y_val, k)
        rows.append(m)
        print(f"{k:>5} {m['range_share']:>17.3f} {m['coverage']:>10.3f} {m['coverage_A1']:>12.3f} {m['coverage_C2']:>12.3f}")

    selection_df = pd.DataFrame(rows)
    selection_df.to_csv(REPORTS_DIR / "range_k_selection_validation.csv", index=False)

    # --- выбор k: ориентир "диапазонов не больше 60% ответов", дальше —
    # среди подходящих максимизируем покрытие (и особенно A1/C2, у них
    # больше всего шансов получить пользу от диапазона).
    eligible = [r for r in rows if r["range_share"] <= 0.60]
    if not eligible:
        eligible = rows  # если все варианты дают >60% диапазонов — берём с минимальной долей
    chosen = max(eligible, key=lambda r: r["coverage"])
    chosen_k = chosen["k"]

    print(f"\nВыбран k = {chosen_k} (см. объяснение в ml/README.md / results.md).")

    # --- одна проверка на test для выбранного k ---
    test = pd.read_csv(DATA_DIR / "test.csv")
    X_test, y_test = test["text"].tolist(), test["level_code"].tolist()
    test_metrics = evaluate_k(pipeline, X_test, y_test, chosen_k)

    print("\n" + "=" * 90)
    print(f"Проверка выбранного k={chosen_k} на официальном test (один раз, без повторного подбора)")
    print("=" * 90)
    print(f"доля диапазонов: {test_metrics['range_share']:.3f}")
    print(f"покрытие: {test_metrics['coverage']:.3f}")
    print(f"покрытие A1: {test_metrics['coverage_A1']:.3f}  (n={test_metrics['n_A1']})")
    print(f"покрытие C2: {test_metrics['coverage_C2']:.3f}  (n={test_metrics['n_C2']})")

    (REPORTS_DIR / "range_k_selection_test.json").write_text(
        json.dumps(test_metrics, indent=2, ensure_ascii=False), encoding="utf-8"
    )

    # --- сохраняем k в model_info.json, ничего лишнего не перезаписывая ---
    model_info_path = MODELS_DIR / "model_info.json"
    info = json.loads(model_info_path.read_text(encoding="utf-8"))
    info["range_rule"] = {
        "k": chosen_k,
        "description": "Диапазон из двух соседних уровней, если P(более вероятного соседа) >= k * P(лучшего уровня); иначе один уровень.",
        "validation_selection": rows,
        "test_check": test_metrics,
    }
    model_info_path.write_text(json.dumps(info, indent=2, ensure_ascii=False), encoding="utf-8")
    print(f"\nСохранено: ml/models/model_info.json (поле range_rule), "
          f"ml/reports/range_k_selection_validation.csv, ml/reports/range_k_selection_test.json")


if __name__ == "__main__":
    main()
