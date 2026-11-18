"""
Юнит-тесты split_sentences() (ml/src/sentence_split.py). Запуск:
  python -m unittest discover -s ml/tests -v
(или просто: python ml/tests/test_sentence_split.py)
"""

import sys
import unittest
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent.parent / "src"))

from sentence_split import split_sentences  # noqa: E402


class TestSplitSentences(unittest.TestCase):
    def test_basic_multiple_sentences(self):
        text = "I like tea. She likes coffee. We are friends."
        self.assertEqual(
            split_sentences(text),
            ["I like tea.", "She likes coffee.", "We are friends."],
        )

    def test_single_sentence(self):
        text = "This is one sentence."
        self.assertEqual(split_sentences(text), ["This is one sentence."])

    def test_no_trailing_punctuation(self):
        text = "This sentence has no ending punctuation"
        self.assertEqual(split_sentences(text), ["This sentence has no ending punctuation"])

    def test_abbreviation_mr_does_not_split(self):
        text = "Mr. Smith went to the shop. He bought some milk."
        self.assertEqual(
            split_sentences(text),
            ["Mr. Smith went to the shop.", "He bought some milk."],
        )

    def test_abbreviation_dr_and_etc(self):
        text = "Dr. Brown studies birds, fish, etc. in the lab. He loves his job."
        self.assertEqual(
            split_sentences(text),
            ["Dr. Brown studies birds, fish, etc. in the lab.", "He loves his job."],
        )

    def test_two_part_abbreviations_eg_ie(self):
        text = "Bring warm clothes, e.g. a coat and gloves. I mean, i.e. anything wool."
        self.assertEqual(
            split_sentences(text),
            ["Bring warm clothes, e.g. a coat and gloves.", "I mean, i.e. anything wool."],
        )

    def test_decimal_numbers_do_not_split(self):
        text = "The price is 3.5 dollars. That is cheap."
        self.assertEqual(
            split_sentences(text),
            ["The price is 3.5 dollars.", "That is cheap."],
        )

    def test_ellipsis_does_not_produce_empty_fragments(self):
        text = "I was thinking... maybe not. Let's see."
        result = split_sentences(text)
        # Главное — многоточие (три точки подряд) не должно породить пустые
        # "предложения" между отдельными точками; по три точки внутри текста
        # или в конце считаем такой же границей, как один ".".
        self.assertEqual(result, ["I was thinking...", "maybe not.", "Let's see."])
        self.assertTrue(all(s.strip() for s in result))

    def test_question_and_exclamation_marks(self):
        text = "Are you coming? Yes! Let's go."
        self.assertEqual(split_sentences(text), ["Are you coming?", "Yes!", "Let's go."])

    def test_extra_whitespace_and_newlines(self):
        text = "First   sentence.\n\nSecond sentence.\t Third   one."
        self.assertEqual(
            split_sentences(text),
            ["First sentence.", "Second sentence.", "Third one."],
        )

    def test_empty_string(self):
        self.assertEqual(split_sentences(""), [])

    def test_whitespace_only_string(self):
        self.assertEqual(split_sentences("   \n\t  "), [])

    def test_multiple_abbreviations_in_one_sentence(self):
        text = "Mr. and Mrs. Jones, Dr. Lee, and Prof. Kim met at the office."
        self.assertEqual(
            split_sentences(text),
            ["Mr. and Mrs. Jones, Dr. Lee, and Prof. Kim met at the office."],
        )


if __name__ == "__main__":
    unittest.main()
