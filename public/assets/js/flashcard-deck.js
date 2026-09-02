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
