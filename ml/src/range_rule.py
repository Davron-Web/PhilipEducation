"""
Правило показа диапазона уровней — общее для tune_range_k.py (подбор k)
и ml/service/serve.py (сама выдача на сайт), чтобы формула не разъезжалась
между "как подбирали" и "как реально работает".
"""

from metrics import LEVELS


def decide_range(probabilities: dict, k: float) -> tuple[str, str | None, str | None]:
    """
    Берём самый вероятный уровень (LEVELS = A1..C2). Если у более
    вероятного из двух соседей по шкале вероятность >= k * вероятность
    лучшего — показываем диапазон из ЭТИХ ДВУХ соседних уровней (никогда
    не шире двух). Иначе — один уровень (range_low/high = None).
    """
    best = max(probabilities, key=probabilities.get)
    idx = LEVELS.index(best)

    neighbors = []
    if idx > 0:
        neighbors.append(LEVELS[idx - 1])
    if idx < len(LEVELS) - 1:
        neighbors.append(LEVELS[idx + 1])

    if not neighbors:
        return best, None, None

    neighbor = max(neighbors, key=lambda lvl: probabilities[lvl])

    if probabilities[neighbor] >= k * probabilities[best]:
        pair = sorted([best, neighbor], key=LEVELS.index)
        return best, pair[0], pair[1]

    return best, None, None
