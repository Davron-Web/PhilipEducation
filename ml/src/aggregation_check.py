"""
Проверка перехода на агрегацию по предложениям (см. разведку: сайт сейчас
отправляет модели весь текст ученика одним куском, а модель обучена на
отдельных предложениях — это расхождение с обучающим распределением).

Два прогона на официальном test:

  1. Регрессионная проверка: раз строки CEFR-SP — уже отдельные
     предложения, split_sentences() на них должен вернуть ровно одно
     предложение (то же самое), а значит новый путь (агрегация по
     предложениям) обязан давать те же предсказания, что и старый
     (одна строка = один вызов predict_proba). Если это не так —
     где-то баг в сплиттере или агрегации.

  2. «Одним куском» vs «разбить и усреднить»: склеиваем по 3-5
     предложений ОДНОГО уровня в псевдо-тексты (имитация ввода
     ученика на сайте) и сравниваем accuracy двух способов оценки
     против известного (одинакового для всех кусков) истинного уровня.
"""

import random
import sys
from pathlib import Path

import joblib
import numpy as np
import pandas as pd

ROOT = Path(__file__).resolve().parent.parent
sys.path.insert(0, str(ROOT / "src"))

from metrics import LEVELS  # noqa: E402
from sentence_split import split_sentences  # noqa: E402

DATA_DIR = ROOT / "data"
MODELS_DIR = ROOT / "models"
REPORTS_DIR = ROOT / "reports"


def line(title):
    print("\n" + "=" * 78)
    print(title)
    print("=" * 78)


def predict_proba_row(pipeline, text: str) -> dict:
    proba = pipeline.predict_proba([text])[0]
    classes = list(pipeline.classes_)
    return {cls: float(p) for cls, p in zip(classes, proba)}


def predict_proba_aggregated(pipeline, text: str) -> tuple[dict, int]:
    """Разбивает на предложения, усредняет вероятности по ним (шаг 2.2)."""
    sentences = split_sentences(text)
    if not sentences:
        sentences = [text]

    proba_matrix = pipeline.predict_proba(sentences)
    classes = list(pipeline.classes_)
    mean_proba = proba_matrix.mean(axis=0)
    return {cls: float(p) for cls, p in zip(classes, mean_proba)}, len(sentences)


def main() -> None:
    pipeline = joblib.load(MODELS_DIR / "cefr_model.joblib")
    test = pd.read_csv(DATA_DIR / "test.csv")
    texts = test["text"].tolist()
    true_levels = test["level_code"].tolist()

    # ------------------------------------------------------------ 1 ---
    line("1. Регрессионная проверка: официальный test, одна строка = одно предложение")

    mismatches = []
    multi_sentence_rows = 0
    for text, true_level in zip(texts, true_levels):
        old_proba = predict_proba_row(pipeline, text)
        old_level = max(old_proba, key=old_proba.get)

        new_proba, n_sentences = predict_proba_aggregated(pipeline, text)
        new_level = max(new_proba, key=new_proba.get)

        if n_sentences != 1:
            multi_sentence_rows += 1

        if old_level != new_level:
            mismatches.append((text, old_level, new_level, n_sentences))

    print(f"Всего строк test: {len(texts)}")
    print(f"Строк, где split_sentences() вернул НЕ ровно 1 предложение: {multi_sentence_rows}")
    print(f"Расхождений между старым и новым путём (argmax): {len(mismatches)} из {len(texts)} ({len(mismatches) / len(texts):.2%})")
    if mismatches:
        print("Примеры расхождений (до 5):")
        for text, old_level, new_level, n in mismatches[:5]:
            print(f"  n_sentences={n}  old={old_level}  new={new_level}  text={text!r}")

    # ------------------------------------------------------------ 2 ---
    line("2. «Одним куском» vs «разбить и усреднить» — псевдо-тексты из 3-5 предложений")

    rng = random.Random(42)
    by_level = {lvl: test.loc[test["level_code"] == lvl, "text"].tolist() for lvl in LEVELS}
    for lvl in LEVELS:
        rng.shuffle(by_level[lvl])

    pseudo_texts = []  # (text, true_level, n_sentences)
    for lvl in LEVELS:
        pool = by_level[lvl]
        i = 0
        while i < len(pool):
            n = rng.randint(3, 5)
            chunk = pool[i : i + n]
            i += n
            if len(chunk) < 3:
                continue  # последний неполный хвост короче 3 — пропускаем
            pseudo_texts.append((" ".join(chunk), lvl, len(chunk)))

    print(f"Построено псевдо-текстов: {len(pseudo_texts)} (по 3-5 предложений одного уровня каждый)")

    correct_whole = 0
    correct_split = 0
    per_level_whole = {lvl: [0, 0] for lvl in LEVELS}  # [correct, total]
    per_level_split = {lvl: [0, 0] for lvl in LEVELS}

    rows_out = []
    for text, true_level, n in pseudo_texts:
        whole_proba = predict_proba_row(pipeline, text)
        whole_pred = max(whole_proba, key=whole_proba.get)

        split_proba, n_sentences = predict_proba_aggregated(pipeline, text)
        split_pred = max(split_proba, key=split_proba.get)

        correct_whole += int(whole_pred == true_level)
        correct_split += int(split_pred == true_level)
        per_level_whole[true_level][0] += int(whole_pred == true_level)
        per_level_whole[true_level][1] += 1
        per_level_split[true_level][0] += int(split_pred == true_level)
        per_level_split[true_level][1] += 1

        rows_out.append(
            {
                "true_level": true_level,
                "n_sentences_joined": n,
                "n_sentences_detected": n_sentences,
                "whole_pred": whole_pred,
                "split_pred": split_pred,
            }
        )

    acc_whole = correct_whole / len(pseudo_texts)
    acc_split = correct_split / len(pseudo_texts)

    print(f"\nAccuracy «одним куском»:      {acc_whole:.3f} ({correct_whole}/{len(pseudo_texts)})")
    print(f"Accuracy «разбить и усреднить»: {acc_split:.3f} ({correct_split}/{len(pseudo_texts)})")
    print(f"Разница: {acc_split - acc_whole:+.3f}")

    print("\nПо уровням:")
    print(f"{'уровень':>8} {'n':>5} {'целиком':>10} {'по предл.':>10}")
    for lvl in LEVELS:
        w_correct, w_total = per_level_whole[lvl]
        s_correct, s_total = per_level_split[lvl]
        w_acc = w_correct / w_total if w_total else float("nan")
        s_acc = s_correct / s_total if s_total else float("nan")
        print(f"{lvl:>8} {w_total:>5} {w_acc:>10.3f} {s_acc:>10.3f}")

    pd.DataFrame(rows_out).to_csv(REPORTS_DIR / "aggregation_pseudo_texts.csv", index=False)

    import json

    summary = {
        "regression_check": {
            "n_total": len(texts),
            "n_multi_sentence_rows": multi_sentence_rows,
            "n_mismatches": len(mismatches),
            "mismatch_rate": len(mismatches) / len(texts),
        },
        "pseudo_text_comparison": {
            "n_pseudo_texts": len(pseudo_texts),
            "accuracy_whole_chunk": acc_whole,
            "accuracy_split_and_average": acc_split,
            "difference": acc_split - acc_whole,
            "per_level": {
                lvl: {
                    "n": per_level_whole[lvl][1],
                    "accuracy_whole": per_level_whole[lvl][0] / per_level_whole[lvl][1] if per_level_whole[lvl][1] else None,
                    "accuracy_split": per_level_split[lvl][0] / per_level_split[lvl][1] if per_level_split[lvl][1] else None,
                }
                for lvl in LEVELS
            },
        },
    }
    (REPORTS_DIR / "aggregation_check.json").write_text(json.dumps(summary, indent=2, ensure_ascii=False), encoding="utf-8")
    print(f"\nСохранено: ml/reports/aggregation_check.json, ml/reports/aggregation_pseudo_texts.csv")


if __name__ == "__main__":
    main()
