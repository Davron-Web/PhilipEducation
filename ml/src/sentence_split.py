"""
Разбиение текста на предложения — без внешних загрузок (nltk punkt и
подобное не используем: сеть в этом окружении медленная/может быть
недоступна, а модели токенизации — лишний вес ради одной функции).
Обычные регулярки: сначала "защищаем" то, что похоже на конец
предложения, но им не является (сокращения, десятичные числа,
многоточие), затем режем по оставшимся . ! ?, потом снимаем защиту.

Не претендует на лингвистическую полноту — покрывает частые случаи
(см. ml/tests/test_sentence_split.py), этого достаточно для текста
ученика в 1-5 предложений на странице «Определи свой уровень».
"""

import re

# Сокращения с ОДНОЙ точкой в конце — просто не считаем эту точку концом
# предложения. Без учёта регистра.
_SIMPLE_ABBREVIATIONS = [
    "mr", "mrs", "ms", "dr", "prof", "sr", "jr", "st", "vs",
    "etc", "no", "vol", "fig", "approx", "cf", "al", "co", "inc", "ltd",
]

# Двусоставные сокращения с точкой ВНУТРИ и в конце ("e.g.", "i.e.") —
# защищаем обе точки сразу, отдельно от простого списка выше.
_TWO_PART_ABBREVIATIONS = ["e.g", "i.e"]

# Нулевой байт — маркер "здесь точка, но не конец предложения". В обычном
# пользовательском тексте не встречается, поэтому безопасен как плейсхолдер.
_PROTECTED_PERIOD = "\x00"


def _protect(text: str) -> str:
    # Многоточие -> один символ (юникодный «…»), чтобы три точки подряд не
    # превратились в границы между "предложениями" из пустоты.
    text = re.sub(r"\.{2,}", "…", text)

    # Десятичные числа: точка между цифрами не завершает предложение (3.5).
    text = re.sub(r"(?<=\d)\.(?=\d)", _PROTECTED_PERIOD, text)

    # Двусоставные сокращения — обе точки защищаем одним проходом.
    for abbr in _TWO_PART_ABBREVIATIONS:
        pattern = re.compile(r"\b" + re.escape(abbr) + r"\.", re.IGNORECASE)
        text = pattern.sub(abbr.replace(".", _PROTECTED_PERIOD) + _PROTECTED_PERIOD, text)

    # Обычные сокращения — одна точка в конце.
    pattern = re.compile(
        r"\b(" + "|".join(re.escape(a) for a in _SIMPLE_ABBREVIATIONS) + r")\.",
        re.IGNORECASE,
    )
    text = pattern.sub(lambda m: m.group(1) + _PROTECTED_PERIOD, text)

    return text


def split_sentences(text: str) -> list[str]:
    """
    Возвращает список непустых предложений. Пустая строка / строка из
    одних пробелов -> пустой список. Текст без завершающей пунктуации в
    конце — последний кусок всё равно возвращается как предложение.
    """
    if not text:
        return []

    normalized = re.sub(r"\s+", " ", text).strip()
    if not normalized:
        return []

    protected = _protect(normalized)

    # Режем по . ! ? и многоточию (уже свёрнутому в «…»), за которыми
    # следует пробел — то, что не защищено выше, действительно конец
    # предложения.
    pieces = re.split(r"(?<=[.!?…])\s+", protected)

    sentences = []
    for piece in pieces:
        restored = piece.replace(_PROTECTED_PERIOD, ".").replace("…", "...")
        restored = restored.strip()
        if restored:
            sentences.append(restored)

    return sentences
