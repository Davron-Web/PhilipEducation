"""
Скачивает корпус CEFR-SP через Hugging Face Datasets Server (JSON API).

Почему не через `datasets`/`pyarrow`: в этом окружении очень медленный и
дорогой по времени интернет (~25 КБ/с на любой CDN — проверено на PyPI,
GitHub и huggingface.co). Библиотека pyarrow сама по себе весит ~28 МБ —
это лишние ~20 минут только на её установку, при том что сами данные
(корпус CEFR-SP) весят меньше 1 МБ. JSON-эндпоинт `datasets-server.huggingface.co`
отдаёт готовые строки постранично, без парсинга Parquet — это единственная
зависимость этого скрипта — модуль urllib из стандартной библиотеки Python.

Источник и лицензия (см. ml/README.md за подробностями):
  CEFR-SP (Arase, Uchida, Kajiwara. "CEFR-Based Sentence-Difficulty
  Annotation and Assessment", EMNLP 2022) — 17 тыс. английских предложений
  с CEFR-разметкой (A1-C2) от двух экспертов-филологов.
  Зеркало с готовыми train/validation/test сплитами:
  https://huggingface.co/datasets/edesaras/CEFR-Sentence-Level-Annotations
  (источник помечен как MIT, но исходный корпус yukiar/CEFR-SP на GitHub
  распространяется по CC BY-SA 3.0 (часть Wiki-Auto) и CC BY-NC-SA 4.0
  (часть SCoRE) — используем именно эти условия, MIT-метка зеркала не
  отменяет лицензию исходных данных).

Метки в исходном корпусе — целые числа от двух независимых экспертов:
  1=A1, 2=A2, 3=B1, 4=B2, 5=C1, 6=C2 (см. README yukiar/CEFR-SP).

Результат: ml/data_raw/{train,validation,test}.csv с колонками
text, annotator1, annotator2 — сырой материал, финальную метку (с учётом
расхождений экспертов) считает prepare_dataset.py.
"""

import csv
import json
import time
import urllib.error
import urllib.request
from pathlib import Path

DATASET = "edesaras/CEFR-Sentence-Level-Annotations"
SPLITS = ["train", "validation", "test"]
PAGE_SIZE = 100
OUT_DIR = Path(__file__).resolve().parent.parent / "data_raw"


def fetch_page(split: str, offset: int, length: int) -> dict:
    url = (
        "https://datasets-server.huggingface.co/rows"
        f"?dataset={DATASET}&config=default&split={split}"
        f"&offset={offset}&length={length}"
    )
    # Датасет-сервер иногда отвечает 500 на «холодный» первый запрос к
    # сплиту — пока он готовит датасет на своей стороне; повторяем.
    last_error = None
    for attempt in range(5):
        try:
            with urllib.request.urlopen(url, timeout=30) as response:
                return json.load(response)
        except (urllib.error.HTTPError, urllib.error.URLError) as exc:
            last_error = exc
            time.sleep(2 * (attempt + 1))
    raise RuntimeError(f"Не удалось получить {url}: {last_error}")


def fetch_split(split: str) -> list[dict]:
    rows = []
    offset = 0
    while True:
        page = fetch_page(split, offset, PAGE_SIZE)
        page_rows = page["rows"]
        if not page_rows:
            break
        for item in page_rows:
            row = item["row"]
            rows.append(
                {
                    "text": row["text"],
                    "annotator1": row["Annotator I"],
                    "annotator2": row["Annotator II"],
                }
            )
        offset += len(page_rows)
        print(f"  {split}: {offset} строк")
        if len(page_rows) < PAGE_SIZE:
            break
    return rows


def main() -> None:
    OUT_DIR.mkdir(parents=True, exist_ok=True)

    for split in SPLITS:
        print(f"Скачиваю сплит '{split}'...")
        rows = fetch_split(split)

        out_path = OUT_DIR / f"{split}.csv"
        with out_path.open("w", newline="", encoding="utf-8") as f:
            writer = csv.DictWriter(f, fieldnames=["text", "annotator1", "annotator2"])
            writer.writeheader()
            writer.writerows(rows)

        print(f"  сохранено {len(rows)} строк -> {out_path}")


if __name__ == "__main__":
    main()
