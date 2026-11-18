// Словарь/Выражения комбинируют несколько таких фабрик в одном x-data через
// Object.assign(flashcardDeck(...), quizDeck(...), {mode: 'list'}) — но
// Object.assign копирует значение геттера (get current(), get quizCurrent())
// ОДИН РАЗ в момент слияния, а не сам геттер: получившееся свойство
// застывает на исходном null навсегда, сколько бы раз ни менялись
// quizQuestions/index дальше. Внешне это выглядит как «Тест»/«Карточки»
// открываются, но вопрос/карточка так и не появляется. mergeAlpineData()
// копирует дескрипторы свойств (Object.getOwnPropertyDescriptors), поэтому
// геттеры остаются живыми геттерами и на итоговом объекте.
function mergeAlpineData() {
    var target = {};
    for (var i = 0; i < arguments.length; i++) {
        Object.defineProperties(target, Object.getOwnPropertyDescriptors(arguments[i]));
    }
    return target;
}

// Общая Alpine.js фабрика для режима флеш-карточек (Словарь и Выражения).
// progressEndpoint — базовый URL для отметки прогресса, например '/words'
// или '/expressions'; итоговый запрос уходит на `${progressEndpoint}/{id}/progress`.
function flashcardDeck(cards, progressEndpoint) {
    return {
        cards: cards,
        index: 0,
        flipped: false,
        get filteredCards() {
            if (this.filter === 'learned') return this.cards.filter(c => c.learned);
            if (this.filter === 'new') return this.cards.filter(c => !c.learned);
            return this.cards;
        },
        get current() {
            var list = this.filteredCards;
            if (!list.length) return null;
            if (this.index >= list.length) this.index = 0;
            return list[this.index];
        },
        flip() { this.flipped = !this.flipped; },
        next() {
            this.flipped = false;
            var list = this.filteredCards;
            this.index = list.length ? (this.index + 1) % list.length : 0;
        },
        async mark(learned) {
            var card = this.current;
            if (!card) return;
            card.learned = learned;
            try {
                await fetch(progressEndpoint + '/' + card.id + '/progress', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ learned: learned }),
                });
            } catch (e) {}
            this.next();
        },
    };
}

// Общая Alpine-фабрика для режима «Проверь себя» / «Тест» (квиз с
// вариантами перевода) — используется на страницах Выражений и коллекций
// Словаря. items — те же карточки, что и у flashcardDeck (id/word/
// translation); прогресс пишется в БД через POST `${progressEndpoint}/{id}/practice`.
function quizDeck(items, progressEndpoint) {
    return {
        quizPool: items,
        quizQuestions: [],
        quizIndex: 0,
        quizScore: 0,
        quizSelected: null,
        quizAnswered: false,
        quizDone: false,
        get quizCurrent() {
            return this.quizQuestions[this.quizIndex] || null;
        },
        startQuiz: function () {
            this.quizDone = false;
            this.quizIndex = 0;
            this.quizScore = 0;
            this.quizSelected = null;
            this.quizAnswered = false;

            var pool = this.quizPool.filter(function (c) { return c.translation && c.translation !== '—'; });
            var shuffled = pool.slice().sort(function () { return Math.random() - 0.5; });
            var count = Math.min(10, shuffled.length);
            var questions = [];

            for (var i = 0; i < count; i++) {
                var card = shuffled[i];
                var distractors = pool
                    .filter(function (c) { return c.id !== card.id; })
                    .sort(function () { return Math.random() - 0.5; })
                    .slice(0, 3)
                    .map(function (c) { return c.translation; });
                var options = distractors.concat([card.translation]).sort(function () { return Math.random() - 0.5; });
                questions.push({ id: card.id, word: card.word, correct: card.translation, options: options });
            }

            this.quizQuestions = questions;
        },
        answerQuiz: function (option) {
            if (this.quizAnswered || !this.quizCurrent) return;
            this.quizAnswered = true;
            this.quizSelected = option;

            var correct = option === this.quizCurrent.correct;
            if (correct) this.quizScore++;

            fetch(progressEndpoint + '/' + this.quizCurrent.id + '/practice', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ correct: correct }),
            }).catch(function () {});
        },
        nextQuiz: function () {
            if (this.quizIndex + 1 < this.quizQuestions.length) {
                this.quizIndex++;
                this.quizSelected = null;
                this.quizAnswered = false;
            } else {
                this.quizDone = true;
            }
        },
    };
}

// Тонкие обёртки над quizDeck() с уже подставленным эндпоинтом — старые
// шаблоны (expressions/index.blade.php) вызывают expressionQuiz(items) без
// второго аргумента, менять разметку там не нужно.
function expressionQuiz(items) {
    return quizDeck(items, '/expressions');
}

function wordQuiz(items) {
    return quizDeck(items, '/words');
}
