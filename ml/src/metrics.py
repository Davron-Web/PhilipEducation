"""
Общие метрики для отбора конфигурации на validation (train_baseline.py) и
финальной проверки на test (evaluate.py) — вынесены в одно место, чтобы
обе стадии считали одинаково и не разъезжались в формулах.
"""

from sklearn.metrics import accuracy_score, cohen_kappa_score, f1_score, recall_score

LEVELS = ["A1", "A2", "B1", "B2", "C1", "C2"]
LEVEL_INDEX = {code: i for i, code in enumerate(LEVELS)}


def adjacent_accuracy(y_true, y_pred) -> float:
    """Доля предсказаний, отличающихся от истинного уровня не больше чем на
    один шаг по шкале (A1..C2 — ordinal-шкала, а не независимые категории:
    спутать B1 с B2 — не то же самое, что спутать B1 с C2)."""
    diffs = [abs(LEVEL_INDEX[t] - LEVEL_INDEX[p]) for t, p in zip(y_true, y_pred)]
    return sum(d <= 1 for d in diffs) / len(diffs)


def score_predictions(y_true, y_pred) -> dict:
    """
    accuracy, macro F1, adjacent accuracy, quadratic weighted kappa и recall
    по крайним, самым проблемным классам (A1, C2 — меньше всего примеров в
    train, см. class_distribution.csv) — считаем recall именно по ним
    отдельно, а не только macro-усреднённо, чтобы видеть, не убила ли
    выбранная конфигурация редкие классы полностью (recall=0).
    """
    recalls = recall_score(y_true, y_pred, labels=LEVELS, average=None, zero_division=0)
    recall_by_level = dict(zip(LEVELS, recalls))

    return {
        "accuracy": accuracy_score(y_true, y_pred),
        "macro_f1": f1_score(y_true, y_pred, average="macro", zero_division=0),
        "adjacent_accuracy": adjacent_accuracy(y_true, y_pred),
        "quadratic_weighted_kappa": cohen_kappa_score(y_true, y_pred, labels=LEVELS, weights="quadratic"),
        "recall_A1": float(recall_by_level["A1"]),
        "recall_C2": float(recall_by_level["C2"]),
    }
