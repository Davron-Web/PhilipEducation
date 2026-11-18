"""
Локальный сервис определения уровня английского (CEFR) по тексту.

Специально на стандартной библиотеке (http.server), без FastAPI/uvicorn:
в этом окружении интернет отдаёт ~25 КБ/с на любой CDN (проверено на PyPI,
huggingface.co, GitHub), а FastAPI тянет за собой starlette + pydantic
(pydantic-core — скомпилированный wheel в несколько МБ), uvicorn — ещё
click/h11/websockets/httptools. Для одного эндпоинта POST /predict это
лишние ~10-15 минут установки без какой-либо пользы: http.server справляется
с той же задачей и уже есть в любой установке Python.

Запуск:  python ml/service/serve.py
Эндпоинт: POST http://127.0.0.1:8008/predict  {"text": "..."}
Ответ:    {"level": "B1", "range_low": "A2", "range_high": "B1",
           "probabilities": {"A1": 0.01, ..., "C2": 0.02}, "sentence_count": 3,
           "per_sentence": [{"text": "...", "level": "A2"}, ...]}
(range_low/range_high — null, если модель уверена в одном уровне; см.
ml/README.md, раздел про правило диапазона.)

Laravel обращается сюда через App\\Services\\CefrPredictionService (Http::).

Модель обучена на ОТДЕЛЬНЫХ предложениях (CEFR-SP), поэтому текст ученика
(1-5 предложений) разбивается на предложения (sentence_split.py) и
вероятности усредняются по ним — раньше весь текст скармливался одним
куском, что на псевдо-текстах из нескольких предложений роняло accuracy с
53% до 7% (признаки сложности типа "число слов" растут с длиной текста и
тянут длинные куски в сторону C2 независимо от реальной трудности — см.
ml/reports/aggregation_check.json и results.md).
"""

import json
import sys
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer
from pathlib import Path

import joblib

ROOT = Path(__file__).resolve().parent.parent
MODEL_PATH = ROOT / "models" / "cefr_model.joblib"
MODEL_INFO_PATH = ROOT / "models" / "model_info.json"
HOST = "127.0.0.1"
PORT = 8008

# features.py (нужен для unpickle пайплайна — в нём лежит ComplexityFeaturizer)
# и sentence_split.py — оба лежат в ml/src.
sys.path.insert(0, str(ROOT / "src"))

from range_rule import decide_range  # noqa: E402
from sentence_split import split_sentences  # noqa: E402

LEVELS = ["A1", "A2", "B1", "B2", "C1", "C2"]

# k по умолчанию — тот же, что был подобран в tune_range_k.py; реальное
# значение читается из model_info.json (не зашито в код, см. задание),
# это только запасной вариант на случай, если поле почему-то отсутствует.
DEFAULT_RANGE_K = 0.6


def load_model():
    if not MODEL_PATH.exists():
        print(f"Модель не найдена: {MODEL_PATH}")
        print("Сначала обучите её: python ml/src/train_baseline.py")
        sys.exit(1)
    return joblib.load(MODEL_PATH)


def load_range_k() -> float:
    if not MODEL_INFO_PATH.exists():
        return DEFAULT_RANGE_K
    try:
        info = json.loads(MODEL_INFO_PATH.read_text(encoding="utf-8"))
        return float(info["range_rule"]["k"])
    except (KeyError, ValueError, TypeError):
        return DEFAULT_RANGE_K


MODEL = load_model()
RANGE_K = load_range_k()


def _proba_dict(proba_row, classes) -> dict:
    result = {level: 0.0 for level in LEVELS}
    for cls, p in zip(classes, proba_row):
        result[cls] = float(p)
    return result


def predict(text: str) -> dict:
    text = (text or "").strip()
    if not text:
        raise ValueError("Пустой текст")

    sentences = split_sentences(text)
    if not sentences:
        sentences = [text]

    classes = list(MODEL.classes_)
    proba_matrix = MODEL.predict_proba(sentences)

    per_sentence = []
    for sentence, proba_row in zip(sentences, proba_matrix):
        sentence_proba = _proba_dict(proba_row, classes)
        sentence_level = max(sentence_proba, key=sentence_proba.get)
        per_sentence.append({"text": sentence, "level": sentence_level})

    mean_proba = proba_matrix.mean(axis=0)
    probabilities = {level: round(p, 4) for level, p in _proba_dict(mean_proba, classes).items()}

    best_level, range_low, range_high = decide_range(probabilities, RANGE_K)

    return {
        "level": best_level,
        "range_low": range_low,
        "range_high": range_high,
        "probabilities": probabilities,
        "sentence_count": len(sentences),
        "per_sentence": per_sentence,
    }


class Handler(BaseHTTPRequestHandler):
    def _send_json(self, status: int, payload: dict) -> None:
        body = json.dumps(payload, ensure_ascii=False).encode("utf-8")
        self.send_response(status)
        self.send_header("Content-Type", "application/json; charset=utf-8")
        self.send_header("Content-Length", str(len(body)))
        self.end_headers()
        self.wfile.write(body)

    def do_GET(self):
        if self.path == "/health":
            self._send_json(200, {"status": "ok", "model": str(MODEL_PATH.name)})
            return
        self._send_json(404, {"error": "not found"})

    def do_POST(self):
        if self.path != "/predict":
            self._send_json(404, {"error": "not found"})
            return

        length = int(self.headers.get("Content-Length", 0))
        raw = self.rfile.read(length) if length else b""

        try:
            data = json.loads(raw or b"{}")
            result = predict(data.get("text", ""))
        except ValueError as exc:
            self._send_json(400, {"error": str(exc)})
            return
        except Exception as exc:  # noqa: BLE001 — сервис не должен падать на плохом вводе
            self._send_json(500, {"error": f"internal error: {exc}"})
            return

        self._send_json(200, result)

    def log_message(self, format, *args):  # noqa: A002 — сигнатура BaseHTTPRequestHandler
        print(f"[serve.py] {self.address_string()} {format % args}")


def main() -> None:
    server = ThreadingHTTPServer((HOST, PORT), Handler)
    print(f"CEFR level-check service: http://{HOST}:{PORT}  (POST /predict, GET /health)")
    print("Остановить: Ctrl+C")
    try:
        server.serve_forever()
    except KeyboardInterrupt:
        print("\nОстановлено.")


if __name__ == "__main__":
    main()
