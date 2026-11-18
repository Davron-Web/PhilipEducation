"""
Готовит датасет для обучения: сводит два мнения экспертов-аннотаторов
к одной итоговой метке CEFR-уровня, считает статистику по классам
(перекос классов, согласие экспертов) и сохраняет чистые CSV для
train/validation/test.

Вход:  ml/data_raw/{train,validation,test}.csv (text, annotator1, annotator2)
Выход: ml/data/{train,validation,test}.csv (text, level_num, level_code)
       ml/reports/class_distribution.csv
       ml/reports/annotator_agreement.txt
"""

import math
from pathlib import Path

import pandas as pd

ROOT = Path(__file__).resolve().parent.parent
RAW_DIR = ROOT / "data_raw"
DATA_DIR = ROOT / "data"
REPORTS_DIR = ROOT / "reports"

# "1" -> A1 ... "6" -> C2, как задано в README исходного корпуса CEFR-SP.
LEVEL_CODES = {1: "A1", 2: "A2", 3: "B1", 4: "B2", 5: "C1", 6: "C2"}


def resolve_gold_label(a1: int, a2: int) -> int:
    """
    Сводит оценки двух экспертов к одной метке.

    Совпали — берём как есть. Разошлись на один уровень (частый случай:
    соседние уровни CEFR — не жёсткая граница, а плавный переход) — берём
    среднее, округляя вверх (math.floor(x + 0.5)), а не банковское
    округление Python: направление округления должно быть предсказуемым
    и объяснимым на защите, а не зависеть от чётности числа.
    """
    return math.floor((a1 + a2) / 2 + 0.5)


def load_split(name: str) -> pd.DataFrame:
    df = pd.read_csv(RAW_DIR / f"{name}.csv")
    df["level_num"] = df.apply(
        lambda row: resolve_gold_label(int(row["annotator1"]), int(row["annotator2"])), axis=1
    )
    df["level_code"] = df["level_num"].map(LEVEL_CODES)
    df["split"] = name
    return df


def main() -> None:
    DATA_DIR.mkdir(parents=True, exist_ok=True)
    REPORTS_DIR.mkdir(parents=True, exist_ok=True)

    splits = {name: load_split(name) for name in ["train", "validation", "test"]}
    combined = pd.concat(splits.values(), ignore_index=True)

    # --- Согласие экспертов: насколько вообще надёжна разметка ---
    # diff — расстояние между оценками annotator1 и annotator2 в уровнях
    # CEFR (0 = совпали, 1 = разошлись на один уровень, и т.д.).
    diff = (combined["annotator1"] - combined["annotator2"]).abs()
    exact_match = diff == 0
    off_by_one = diff == 1
    off_by_two_plus = diff >= 2
    any_disagreement = diff != 0
    # sanity-check: три бакета должны точно покрывать всех, без пропусков
    # и пересечений — иначе где-то ошибка в масках выше.
    assert int(exact_match.sum() + off_by_one.sum() + off_by_two_plus.sum()) == len(combined)

    agreement_lines = [
        f"Всего предложений: {len(combined)}",
        f"Эксперты совпали полностью (|annotator1 - annotator2| = 0): {exact_match.sum()} ({exact_match.mean():.1%})",
        f"Разошлись РОВНО на 1 уровень (|annotator1 - annotator2| = 1): {off_by_one.sum()} ({off_by_one.mean():.1%})",
        f"Разошлись на 2+ уровня (|annotator1 - annotator2| >= 2): {off_by_two_plus.sum()} ({off_by_two_plus.mean():.1%})",
        f"Итого НЕСОВПАДЕНИЙ любых (совпали НЕ полностью): {any_disagreement.sum()} ({any_disagreement.mean():.1%})"
        + (" — совпадает с «разошлись на 1 уровень», т.к. случаев расхождения на 2+ уровня в данных нет"
           if off_by_two_plus.sum() == 0 else ""),
    ]
    agreement_text = "\n".join(agreement_lines)
    (REPORTS_DIR / "annotator_agreement.txt").write_text(agreement_text, encoding="utf-8")
    print(agreement_text)

    # --- Перекос классов по каждому сплиту ---
    dist = (
        combined.groupby(["split", "level_code"]).size().unstack(fill_value=0)
        .reindex(columns=[LEVEL_CODES[i] for i in range(1, 7)], fill_value=0)
        .reindex(["train", "validation", "test"])
    )
    dist.to_csv(REPORTS_DIR / "class_distribution.csv")
    print("\nРаспределение классов по сплитам:")
    print(dist)

    # --- Сохраняем чистые сплиты для обучения ---
    for name, df in splits.items():
        out = df[["text", "level_num", "level_code"]]
        out.to_csv(DATA_DIR / f"{name}.csv", index=False)
        print(f"{name}: {len(out)} строк -> data/{name}.csv")


if __name__ == "__main__":
    main()
