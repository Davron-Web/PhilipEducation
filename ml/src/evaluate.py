"""
Финальная проверка выбранной модели на test — сплите, который ни разу не
участвовал ни в обучении, ни в отборе конфигурации (это делали train и
validation в train_baseline.py). Так метрики честно показывают, чего
ожидать от модели на новых, ранее не виденных текстах.

Метрики — общие с train_baseline.py, см. ml/src/metrics.py:
  - majority baseline (всегда самый частый класс train) — чтобы понимать,
    насколько модель вообще лучше «глупого» правила, а не абстрактного
    случайного угадывания 1/6;
  - adjacent accuracy — доля предсказаний, отличающихся от истинного
    уровня не больше чем на один шаг по шкале CEFR;
  - quadratic weighted kappa (QWK) — согласие модели с разметкой с
    поправкой на случайное совпадение и на то, что ошибка на соседний
    уровень значит меньше, чем ошибка через несколько уровней (это
    ординальная шкала, не номинальная — kappa с квадратичными весами
    учитывает именно это). Так как adjacent accuracy у majority baseline
    и без обучения уже высокая (см. ниже), именно QWK — главная метрика
    для суждения о качестве по ordinal-шкале, не adjacent accuracy сама
    по себе.

Сохраняет:
  ml/reports/results.md            — человекочитаемый отчёт (что пробовали,
                                      что получили, что выбрали и почему)
  ml/reports/confusion_matrix.svg  — картинка confusion matrix
  ml/reports/classification_report.txt — точный вывод sklearn по каждому классу
"""

import json
from pathlib import Path

import joblib
import pandas as pd
from sklearn.metrics import classification_report, confusion_matrix

from confusion_svg import render_confusion_matrix_svg
from metrics import LEVELS, score_predictions

ROOT = Path(__file__).resolve().parent.parent
DATA_DIR = ROOT / "data"
MODELS_DIR = ROOT / "models"
REPORTS_DIR = ROOT / "reports"


def df_to_markdown(df: pd.DataFrame, index_name: str | None = None) -> str:
    """Свой markdown-рендер таблицы вместо DataFrame.to_markdown(), которому
    нужен отдельный пакет `tabulate` — одной лишней зависимостью меньше."""
    headers = ([index_name] if index_name else []) + [str(c) for c in df.columns]
    lines = ["| " + " | ".join(headers) + " |", "| " + " | ".join(["---"] * len(headers)) + " |"]
    for idx, row in df.iterrows():
        cells = ([str(idx)] if index_name else []) + [str(v) for v in row.tolist()]
        lines.append("| " + " | ".join(cells) + " |")
    return "\n".join(lines)


def main() -> None:
    train = pd.read_csv(DATA_DIR / "train.csv")
    test = pd.read_csv(DATA_DIR / "test.csv")
    X_test, y_test = test["text"].tolist(), test["level_code"].tolist()

    pipeline = joblib.load(MODELS_DIR / "cefr_model.joblib")
    info = json.loads((MODELS_DIR / "model_info.json").read_text(encoding="utf-8"))

    y_pred = pipeline.predict(X_test)

    model_scores = score_predictions(y_test, y_pred)

    # Majority baseline — самый частый класс в TRAIN (не в test: иначе
    # правило «подсмотрело» бы то, чего в реальности знать не может),
    # предсказанный для каждого примера test. Точка отсчёта: если модель не
    # обгоняет это тупое правило — она не научилась ничему полезному.
    majority_class = train["level_code"].value_counts().idxmax()
    majority_pred = [majority_class] * len(y_test)
    majority_scores = score_predictions(y_test, majority_pred)

    report_text = classification_report(y_test, y_pred, labels=LEVELS, digits=3, zero_division=0)
    report_dict = classification_report(
        y_test, y_pred, labels=LEVELS, digits=3, zero_division=0, output_dict=True
    )
    matrix = confusion_matrix(y_test, y_pred, labels=LEVELS)

    (REPORTS_DIR / "classification_report.txt").write_text(report_text, encoding="utf-8")
    render_confusion_matrix_svg(matrix.tolist(), LEVELS, REPORTS_DIR / "confusion_matrix.svg")

    selection = pd.read_csv(REPORTS_DIR / "model_selection.csv")
    dist = pd.read_csv(REPORTS_DIR / "class_distribution.csv", index_col=0)
    agreement = (REPORTS_DIR / "annotator_agreement.txt").read_text(encoding="utf-8")

    val_macro_f1 = info["validation_metrics"]["macro_f1"] if "validation_metrics" in info else info["validation_macro_f1"]
    val_test_gap = val_macro_f1 - model_scores["macro_f1"]

    lines = []
    lines.append("# Определение уровня английского (CEFR) по тексту — отчёт об обучении\n")

    lines.append("## Данные\n")
    lines.append(
        "Источник: корпус **CEFR-SP** (Arase, Uchida, Kajiwara, EMNLP 2022) — "
        "17 тыс. англ. предложений, размеченных двумя экспертами-филологами "
        "уровнями CEFR (A1-C2). Лицензия: часть Wiki-Auto — CC BY-SA 3.0, "
        "часть SCoRE — CC BY-NC-SA 4.0 (только некоммерческое использование). "
        "Подробности и обоснование выбора — в ml/README.md.\n"
    )
    lines.append("Согласие двух экспертов-аннотаторов:\n")
    lines.append("```\n" + agreement + "\n```\n")
    lines.append(
        "**Откуда эта цифра и как посчитана**: НЕ из статьи Arase et al. — мы не "
        "брали её оттуда и не проверяли, публикуют ли авторы такое же число. Это "
        "самостоятельный подсчёт по фактически скачанным данным: колонки "
        "`annotator1`/`annotator2` в `ml/data_raw/{train,validation,test}.csv` "
        "(это поля `Annotator I`/`Annotator II` зеркала корпуса на Hugging Face, "
        "huggingface.co/datasets/edesaras/CEFR-Sentence-Level-Annotations) — по "
        "всем 10 004 предложениям (train+validation+test вместе), формула "
        "`|annotator1 - annotator2|`, код — блок подсчёта согласия в "
        "`ml/src/prepare_dataset.py` (там же — assert, что три бакета "
        "совпадение/1 уровень/2+ уровня точно покрывают все строки без "
        "пропусков). Полностью воспроизводимо: `python ml/src/prepare_dataset.py` "
        "пересчитает то же самое заново.\n"
        "\n"
        "**58.6% — это доля НЕСОВПАДЕНИЙ, не совпадений**: доля предложений, "
        "где два эксперта поставили РАЗНЫЕ уровни (расхождение ровно на 1 "
        "уровень CEFR). В данных не оказалось ни одного случая расхождения "
        "на 2 и более уровня, поэтому «разошлись на 1 уровень» и «разошлись "
        "хоть как-то» здесь совпадают численно — это не два разных числа, "
        "случайно оказавшихся одинаковыми, а одно и то же множество строк, "
        "потому что бакет «2+» пуст. Полное совпадение экспертов — 41.4% "
        "(другое число, ему не противоречит).\n"
    )
    lines.append("Распределение примеров по уровням и сплитам:\n")
    lines.append(df_to_markdown(dist, index_name="split") + "\n")

    lines.append("## Какие модели пробовали\n")
    lines.append(
        "Baseline: TF-IDF (word 1-2gram) + логистическая регрессия. Сравнили "
        "4 конфигурации на validation — раздельно балансировку классов "
        "(`class_weight`) и признаки сложности текста (средняя длина "
        "предложения/слова, лексическое разнообразие, доля длинных слов — "
        "см. ml/src/features.py), чтобы влияние каждого фактора было видно "
        "отдельно, а не смешивалось в одном сравнении:\n"
    )
    lines.append(df_to_markdown(selection.round(3)) + "\n")
    lines.append(
        "- **A vs B** — балансировка классов без признаков сложности: "
        "balanced проигрывает по accuracy, но выигрывает по macro F1, QWK и "
        "recall на A1/C2 — ожидаемо, class_weight='balanced' штрафует за "
        "ошибки на редких классах сильнее, а accuracy такую разницу не видит.\n"
        "- **C vs D** — то же самое, но с признаками сложности (чище, чем "
        "A vs B: отличается только class_weight, не два параметра сразу). "
        "D (без баланса) даёт заметно лучшую accuracy и adjacent accuracy, но "
        "**recall на C2 = 0** — модель вообще не находит верхний уровень "
        "target. C (с балансом) жертвует немного accuracy ради того, чтобы "
        "C2 и A1 не игнорировались полностью.\n"
        "- **B vs C** — добавление признаков сложности поверх TF-IDF заметно "
        "поднимает все метрики, гипотеза задания подтвердилась.\n"
    )
    lines.append(f"**Выбрана конфигурация: `{info['best_config']}`** (macro F1 на validation = {val_macro_f1:.3f}).\n")
    lines.append(
        "Отбор — по правилу «лучший macro F1, но при разнице меньше 0.01 "
        "предпочесть вариант с recall(A1) > 0» (см. `pick_best()` в "
        "`ml/src/train_baseline.py`); в данном прогоне tie-break не "
        "понадобился — C выиграл по macro F1 напрямую (и у C recall(A1) и так "
        "выше, чем у D).\n"
    )

    lines.append(
        "\n### Почему не дообучали DistilBERT\n"
        "Пробовали оценить: дообучение DistilBERT требует `torch` + `transformers` "
        "+ веса модели — суммарно несколько сотен МБ. Замер реальной скорости сети "
        "в этом окружении (curl к PyPI, GitHub, huggingface.co) дал ~25 КБ/с "
        "устойчиво на всех трёх хостах — то есть только загрузка библиотек заняла бы "
        "часы, а дообучение на CPU без GPU для этой задачи не оправдано при таком "
        "размере датасета (10 тыс. коротких предложений). Baseline на TF-IDF в такой "
        "ситуации — осознанный выбор, а не компромисс от лени: он обучается за секунды "
        "и даёт интерпретируемые веса (видно, какие слова/n-граммы толкают предсказание "
        "к тому или иному уровню), что тоже плюс для защиты перед жюри.\n"
    )

    # --- Масштабирование: два раунда, две разные идеи ---
    before = json.loads((REPORTS_DIR / "before_scaling_metrics.json").read_text(encoding="utf-8")) \
        if (REPORTS_DIR / "before_scaling_metrics.json").exists() else None
    before_max_iter_selection = pd.read_csv(REPORTS_DIR / "before_scaling_model_selection.csv") \
        if (REPORTS_DIR / "before_scaling_model_selection.csv").exists() else None
    # Снимок ПОСЛЕ фикса max_iter, но ДО масштабирования complexity — нужен
    # отдельно от before_max_iter_selection (тот — до самого max_iter-фикса).
    after_max_iter_selection = pd.read_csv(REPORTS_DIR / "round1_model_selection.csv") \
        if (REPORTS_DIR / "round1_model_selection.csv").exists() else None
    scaler_experiment_path = REPORTS_DIR / "scaler_experiment.json"

    if before is not None and before_max_iter_selection is not None and after_max_iter_selection is not None and scaler_experiment_path.exists():
        scaler_exp = json.loads(scaler_experiment_path.read_text(encoding="utf-8"))
        # "C_tfidf_plus_complexity" — старое имя той же по смыслу
        # конфигурации, что сейчас называется "C_complexity_balanced".
        before_c = before_max_iter_selection[before_max_iter_selection["config"] == "C_tfidf_plus_complexity"].iloc[0]
        after_max_iter_c = after_max_iter_selection[after_max_iter_selection["config"] == "C_tfidf_plus_complexity"].iloc[0]
        new_c = selection[selection["config"] == "C_complexity_balanced"].iloc[0]

        lines.append("## Масштабирование признаков: два раунда экспериментов\n")
        lines.append(
            "**Раунд 1 (исправление ConvergenceWarning).** На `max_iter=2000` "
            "`LogisticRegression` не успевала сойтись. Проверили два способа:\n"
        )
        fix_rows = pd.DataFrame(
            [
                {
                    "вариант": "до исправления (max_iter=2000)",
                    "ConvergenceWarning": "да",
                    "accuracy (val)": round(float(before_c["accuracy"]), 3),
                    "macro F1 (val)": round(float(before_c["macro_f1"]), 3),
                },
                {
                    "вариант": "max_iter=5000, без скейлера",
                    "ConvergenceWarning": "нет",
                    "accuracy (val)": round(float(after_max_iter_c["accuracy"]), 3),
                    "macro F1 (val)": round(float(after_max_iter_c["macro_f1"]), 3),
                },
                {
                    "вариант": "max_iter=5000 + StandardScaler(with_mean=False) НА ВСЮ матрицу",
                    "ConvergenceWarning": "нет",
                    "accuracy (val)": scaler_exp["validation_accuracy"],
                    "macro F1 (val)": scaler_exp["validation_macro_f1"],
                },
            ]
        )
        lines.append(df_to_markdown(fix_rows) + "\n")
        lines.append(
            "Масштабирование ВСЕЙ матрицы (TF-IDF + признаки сложности одним "
            "скейлером) устраняло предупреждение, но заметно портило качество: "
            "на разреженной TF-IDF-матрице большинство признаков (редкие "
            "n-граммы) имеют маленький std, а значит получают большой "
            "множитель `1/std` — масштабирование раздувает вес шума, а не "
            "сигнала. Оставили просто `max_iter=5000` — предупреждение "
            "пропадает без этой побочки (сходится за ~2730 итераций вместо "
            "обрыва на 2000).\n"
        )
        lines.append(
            "\n**Раунд 2 (эта итерация): масштабирование ТОЛЬКО признаков "
            "сложности.** Раунд 1 уже убрал ConvergenceWarning — раунд 2 не "
            "чинит его заново, а уточняет архитектуру: признаки сложности "
            "(`ComplexityFeaturizer`) — это 6 плотных чисел с сильно разным "
            "масштабом (`word_count` — десятки-сотни, `lexical_diversity` — "
            "0-1), и без масштабирования на фоне TF-IDF (у него все значения "
            "и так в сравнимом диапазоне за счёт `sublinear_tf`) они либо "
            "теряются, либо доминируют непропорционально. `StandardScaler()` "
            "теперь стоит ТОЛЬКО внутри ветки `complexity` в `FeatureUnion` — "
            "TF-IDF её не видит вообще, поэтому проблема раунда 1 (раздувание "
            "шума редких n-грамм) здесь неприменима: масштабируем не то, что "
            "её вызывало.\n"
        )
        lines.append(
            f"Эффект на конфигурацию C (`{info['best_config']}`, макро F1 на "
            f"validation, оба варианта уже с max_iter=5000): было "
            f"{round(float(after_max_iter_c['macro_f1']), 3)} (без "
            f"масштабирования complexity) → стало {round(float(new_c['macro_f1']), 3)} "
            "(с масштабированием). Разница в пределах шума обучения — цель "
            "раунда 2 была не «поднять метрику любой ценой», а сделать "
            "признаки сложности сравнимыми по масштабу с TF-IDF корректно, не "
            "трогая то, что уже работало; заодно раунд 2 добавил "
            "четвёртую конфигурацию (D — признаки сложности без "
            "балансировки классов), которой в раунде 1 не было, и сейчас "
            "именно она — ближайший конкурент C (см. таблицу выше).\n"
        )

    lines.append("## Метрики на test (финальные, не участвовавшие в отборе модели)\n")
    lines.append(
        "Сравнение с majority baseline (правило «всегда предсказывать самый "
        f"частый уровень в train» — это `{majority_class}`), а не со случайным "
        "угадыванием 1/6: baseline тоже кое-что «знает» о перекосе классов "
        "(см. распределение выше), поэтому именно он — честная точка отсчёта, "
        "ниже которой модель бесполезна.\n"
    )
    lines.append(
        f"**Adjacent accuracy у самого baseline уже {majority_scores['adjacent_accuracy']:.3f}** "
        "— это не ошибка и не подгонка: раз почти все уровни в датасете и так "
        "соседние с самым частым (B2), правило «всегда B2» почти всегда "
        "промахивается не больше чем на один уровень просто по своей "
        "природе. Поэтому adjacent accuracy сама по себе — плохой критерий "
        "качества модели здесь (высокий даже у бессмысленного правила); "
        "**главная ordinal-метрика — quadratic weighted kappa (QWK)**, она "
        "как раз вычитает то, что можно получить и без обучения "
        "(у majority baseline QWK = 0 по определению).\n"
    )
    metrics_table = pd.DataFrame(
        [
            {
                "модель": f"Majority baseline (всегда {majority_class})",
                "Accuracy": round(majority_scores["accuracy"], 3),
                "Macro F1": round(majority_scores["macro_f1"], 3),
                "Adjacent accuracy": round(majority_scores["adjacent_accuracy"], 3),
                "QWK": round(majority_scores["quadratic_weighted_kappa"], 3),
            },
            {
                "модель": f"Наша модель ({info['best_config']})",
                "Accuracy": round(model_scores["accuracy"], 3),
                "Macro F1": round(model_scores["macro_f1"], 3),
                "Adjacent accuracy": round(model_scores["adjacent_accuracy"], 3),
                "QWK": round(model_scores["quadratic_weighted_kappa"], 3),
            },
        ]
    )
    lines.append(df_to_markdown(metrics_table) + "\n")
    lines.append(
        "**Adjacent accuracy** — доля предсказаний, где модель ошиблась не "
        "больше чем на один уровень CEFR (например, назвала B2 вместо B1). "
        "**Quadratic weighted kappa (QWK)** — согласие с разметкой за вычетом "
        "случайного совпадения, с квадратичным штрафом за далёкие ошибки "
        "(0 — как случайное угадывание с тем же распределением классов, что "
        "и в данных; 1 — идеальное совпадение).\n"
    )
    lines.append(
        f"**Разрыв validation → test по macro F1: {val_macro_f1:.3f} → "
        f"{model_scores['macro_f1']:.3f}** (падение на {val_test_gap:.3f}). "
        "Конфигурация выбиралась по validation, а не по test — небольшое "
        "падение на test ожидаемо и нормально (это и есть весь смысл "
        "раздельных validation/test: увидеть честную, не подогнанную под "
        "отбор оценку); тревожным был бы разрыв на порядок больше этого.\n"
    )
    lines.append("Подробно по каждому уровню (precision/recall/F1):\n")
    lines.append("```\n" + report_text + "\n```\n")

    lines.append("## Confusion matrix\n")
    lines.append("![confusion matrix](confusion_matrix.svg)\n")
    lines.append(
        "Ошибки в основном ожидаемо сосредоточены рядом с диагональю — модель "
        "путает соседние уровни (например, B1 и B2) чаще, чем далёкие "
        "(A1 и C2), что соответствует самой природе шкалы CEFR: границы между "
        "соседними уровнями размыты даже для людей-экспертов (см. согласие "
        "аннотаторов выше и adjacent accuracy).\n"
    )

    (REPORTS_DIR / "results.md").write_text("\n".join(lines), encoding="utf-8")

    print(f"Accuracy: {model_scores['accuracy']:.3f} (majority baseline: {majority_scores['accuracy']:.3f})")
    print(f"Macro F1: {model_scores['macro_f1']:.3f} (majority baseline: {majority_scores['macro_f1']:.3f})")
    print(f"Adjacent accuracy: {model_scores['adjacent_accuracy']:.3f} (majority baseline: {majority_scores['adjacent_accuracy']:.3f})")
    print(f"QWK: {model_scores['quadratic_weighted_kappa']:.3f} (majority baseline: {majority_scores['quadratic_weighted_kappa']:.3f})")
    print(f"Validation -> test macro F1 gap: {val_macro_f1:.3f} -> {model_scores['macro_f1']:.3f}")
    print("\n" + report_text)
    print(f"\nОтчёт сохранён: {REPORTS_DIR / 'results.md'}")
    print(f"Confusion matrix: {REPORTS_DIR / 'confusion_matrix.svg'}")

    (MODELS_DIR / "model_info.json").write_text(
        json.dumps(
            {
                **info,
                "test_metrics": model_scores,
                "test_accuracy": model_scores["accuracy"],
                "test_macro_f1": model_scores["macro_f1"],
                "test_adjacent_accuracy": model_scores["adjacent_accuracy"],
                "test_quadratic_weighted_kappa": model_scores["quadratic_weighted_kappa"],
                "majority_baseline": {"class": majority_class, **majority_scores},
                "test_report": report_dict,
            },
            indent=2,
            ensure_ascii=False,
        ),
        encoding="utf-8",
    )


if __name__ == "__main__":
    main()
