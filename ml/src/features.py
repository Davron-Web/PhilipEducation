"""
Признаки сложности текста — не про то, КАКИЕ слова употреблены (это уже
берёт на себя TF-IDF), а про то, НАСКОЛЬКО сложно текст построен. Идея:
уровень B2 отличается от A1 не только словарём, но и длиной предложений,
разнообразием лексики, долей длинных/редких слов.

ComplexityFeaturizer — обычный sklearn-трансформер (fit/transform), поэтому
его можно поставить рядом с TfidfVectorizer в FeatureUnion и обучать одним
Pipeline.
"""

import re

import numpy as np
from sklearn.base import BaseEstimator, TransformerMixin

_WORD_RE = re.compile(r"[A-Za-z']+")
_SENTENCE_SPLIT_RE = re.compile(r"[.!?]+")


def _split_sentences(text: str) -> list[str]:
    parts = [s.strip() for s in _SENTENCE_SPLIT_RE.split(text)]
    return [s for s in parts if s]


def _words(text: str) -> list[str]:
    return _WORD_RE.findall(text.lower())


class ComplexityFeaturizer(BaseEstimator, TransformerMixin):
    """
    На вход — список текстов (строк), на выход — числовая матрица
    (n_samples, n_features) с признаками сложности:

      0. mean_sentence_len   — средняя длина предложения в словах
      1. mean_word_len       — средняя длина слова в буквах
      2. lexical_diversity   — type-token ratio: уникальные слова / все слова
      3. long_word_ratio     — доля слов длиной от 7 букв
      4. sentence_count      — сколько предложений в тексте
      5. word_count          — сколько слов всего

    fit() ничего не «учит» — признаки считаются по формулам, не по
    статистике обучающей выборки, но метод нужен для совместимости с
    интерфейсом sklearn (Pipeline/FeatureUnion вызывают fit перед transform).
    """

    n_features_ = 6

    def fit(self, X, y=None):
        return self

    def transform(self, X):
        rows = [self._features_for(text) for text in X]
        return np.array(rows, dtype=np.float64)

    def _features_for(self, text: str) -> list[float]:
        text = text or ""
        words = _words(text)
        sentences = _split_sentences(text) or [text]

        word_count = len(words)
        sentence_count = max(1, len(sentences))

        mean_sentence_len = word_count / sentence_count
        mean_word_len = (sum(len(w) for w in words) / word_count) if word_count else 0.0
        lexical_diversity = (len(set(words)) / word_count) if word_count else 0.0
        long_word_ratio = (sum(1 for w in words if len(w) >= 7) / word_count) if word_count else 0.0

        return [
            mean_sentence_len,
            mean_word_len,
            lexical_diversity,
            long_word_ratio,
            float(sentence_count),
            float(word_count),
        ]

    def get_feature_names_out(self, input_features=None):
        return np.array(
            [
                "mean_sentence_len",
                "mean_word_len",
                "lexical_diversity",
                "long_word_ratio",
                "sentence_count",
                "word_count",
            ]
        )
