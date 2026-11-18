"""
Проверка утечки данных между train и test — повод: CEFR-SP частично
состоит из Wiki-Auto и Newsela-Auto (выровненные упрощения одного и того
же предложения на разных уровнях сложности). При случайном
(стратифицированном) перемешивании такие варианты могли разъехаться по
train и test, завысив метрики на стратифицированном разбиении.

Ничего не меняет: официальные и стратифицированные сплиты, финальная
модель — не трогаются. Отдельный TfidfVectorizer только для этой проверки
(не тот объект, что в пайплайне модели).
"""

import sys
from pathlib import Path

import numpy as np
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics import classification_report, cohen_kappa_score, f1_score
from sklearn.metrics.pairwise import cosine_similarity

ROOT = Path(__file__).resolve().parent.parent
sys.path.insert(0, str(ROOT / "src"))

from train_baseline import build_pipeline  # noqa: E402
from metrics import LEVELS, score_predictions  # noqa: E402

DATA_DIR = ROOT / "data"
REPORTS_DIR = ROOT / "reports"

A1 = LEVELS.index("A1")
C2 = LEVELS.index("C2")


def line(title):
    print("\n" + "=" * 78)
    print(title)
    print("=" * 78)


def max_train_similarity(train_texts: list[str], test_texts: list[str]):
    """Для каждого test-предложения — максимальное косинусное сходство
    (TF-IDF, word 1-2gram, свой векторизатор) с любым train-предложением,
    и индекс этого train-предложения."""
    vec = TfidfVectorizer(analyzer="word", ngram_range=(1, 2), min_df=1)
    vec.fit(train_texts + test_texts)
    train_matrix = vec.transform(train_texts)
    test_matrix = vec.transform(test_texts)

    sims = cosine_similarity(test_matrix, train_matrix)  # (n_test, n_train)
    max_sim = sims.max(axis=1)
    argmax_idx = sims.argmax(axis=1)
    return max_sim, argmax_idx


def report_similarity(name: str, train_texts: list[str], test_texts: list[str]) -> np.ndarray:
    line(f"Сходство test-train: {name}")
    max_sim, argmax_idx = max_train_similarity(train_texts, test_texts)

    print(f"test: {len(test_texts)} предложений, train: {len(train_texts)} предложений")
    print("Распределение максимального сходства (перцентили):")
    for p in [0, 10, 25, 50, 75, 90, 95, 99, 100]:
        print(f"  p{p:>3}: {np.percentile(max_sim, p):.3f}")

    for thr in [0.9, 0.8, 0.7]:
        n = int((max_sim > thr).sum())
        print(f"test-предложений со сходством > {thr}: {n} ({n / len(test_texts):.1%})")

    print("\nТоп-5 пар с самым высоким сходством:")
    top5 = np.argsort(-max_sim)[:5]
    for rank, i in enumerate(top5, start=1):
        j = argmax_idx[i]
        print(f"\n  #{rank} sim={max_sim[i]:.3f}")
        print(f"    TEST : {test_texts[i]!r}")
        print(f"    TRAIN: {train_texts[j]!r}")

    return max_sim


def eval_subset(pipeline, X, y, label: str):
    if len(X) == 0:
        print(f"{label}: 0 примеров — метрики не считаются")
        return
    pred = pipeline.predict(X)
    scores = score_predictions(y, pred)
    rep = classification_report(y, pred, labels=LEVELS, zero_division=0, output_dict=True)
    print(
        f"{label}: n={len(X)}  accuracy={scores['accuracy']:.3f}  macro_f1={scores['macro_f1']:.3f}  "
        f"QWK={scores['quadratic_weighted_kappa']:.3f}  F1(A1)={rep['A1']['f1-score']:.3f} (support={int(rep['A1']['support'])})  "
        f"F1(C2)={rep['C2']['f1-score']:.3f} (support={int(rep['C2']['support'])})"
    )
    return scores, rep


def main():
    # ---- официальные сплиты ----
    off_train = pd.read_csv(DATA_DIR / "train.csv")
    off_test = pd.read_csv(DATA_DIR / "test.csv")

    # ---- стратифицированные сплиты (из stratified_experiment.py) ----
    strat_train = pd.read_csv(DATA_DIR / "stratified_train.csv")
    strat_test = pd.read_csv(DATA_DIR / "stratified_test.csv")

    # -------------------------------------------------------------- 1/2 ---
    sim_strat = report_similarity(
        "СТРАТИФИЦИРОВАННОЕ разбиение (stratified_test vs stratified_train)",
        strat_train["text"].tolist(), strat_test["text"].tolist(),
    )
    sim_off = report_similarity(
        "ОФИЦИАЛЬНОЕ разбиение (test vs train) — для сравнения",
        off_train["text"].tolist(), off_test["text"].tolist(),
    )

    # ----------------------------------------------------------------- 3 ---
    line("3. Финальная конфигурация на стратифицированном train — метрики на test целиком и на низкосходном подмножестве")

    X_train_s = strat_train["text"].tolist()
    y_train_s = strat_train["level_code"].tolist()
    X_test_s = strat_test["text"].tolist()
    y_test_s = strat_test["level_code"].tolist()

    print("Обучаю C_complexity_balanced на stratified train...")
    pipeline = build_pipeline(class_weight="balanced", with_complexity=True)
    pipeline.fit(X_train_s, y_train_s)

    strat_test_df = strat_test.copy()
    strat_test_df["max_sim_to_train"] = sim_strat

    print()
    full_scores, full_rep = eval_subset(pipeline, X_test_s, y_test_s, "а) весь stratified test")

    low_sim_mask = strat_test_df["max_sim_to_train"] < 0.7
    X_low = strat_test_df.loc[low_sim_mask, "text"].tolist()
    y_low = strat_test_df.loc[low_sim_mask, "level_code"].tolist()
    low_scores, low_rep = eval_subset(pipeline, X_low, y_low, "б) только сходство < 0.7")

    high_sim_mask = ~low_sim_mask
    print(f"\n(для контроля) в) только сходство >= 0.7, n={int(high_sim_mask.sum())}:")
    eval_subset(pipeline, strat_test_df.loc[high_sim_mask, "text"].tolist(), strat_test_df.loc[high_sim_mask, "level_code"].tolist(), "в) сходство >= 0.7")

    # ---- сохраняем всё для results.md/README.md и для точных цифр в ответе ----
    import json

    result = {
        "stratified_similarity": {
            "n_test": len(sim_strat),
            "n_train": len(strat_train),
            "percentiles": {str(p): float(np.percentile(sim_strat, p)) for p in [0, 10, 25, 50, 75, 90, 95, 99, 100]},
            "n_above_0.9": int((sim_strat > 0.9).sum()),
            "n_above_0.8": int((sim_strat > 0.8).sum()),
            "n_above_0.7": int((sim_strat > 0.7).sum()),
        },
        "official_similarity": {
            "n_test": len(sim_off),
            "n_train": len(off_train),
            "percentiles": {str(p): float(np.percentile(sim_off, p)) for p in [0, 10, 25, 50, 75, 90, 95, 99, 100]},
            "n_above_0.9": int((sim_off > 0.9).sum()),
            "n_above_0.8": int((sim_off > 0.8).sum()),
            "n_above_0.7": int((sim_off > 0.7).sum()),
        },
        "stratified_test_full": full_scores,
        "stratified_test_low_similarity_lt_0.7": {**low_scores, "n": len(X_low)} if low_scores else None,
        "stratified_test_full_n": len(X_test_s),
    }
    (REPORTS_DIR / "leakage_check.json").write_text(json.dumps(result, indent=2, ensure_ascii=False), encoding="utf-8")
    print(f"\nСохранено: ml/reports/leakage_check.json")


if __name__ == "__main__":
    main()
