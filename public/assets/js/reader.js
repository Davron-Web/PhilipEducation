/**
 * Book reader: reads page text aloud via speechSynthesis, highlighting the
 * current word (karaoke-style) as it's spoken. Supports a continuous mode
 * and a sentence-by-sentence practice mode where the student repeats each
 * sentence out loud before continuing. Vanilla JS only.
 */
function initReader(config) {
    const contentEl = document.getElementById('readerContent');
    if (!contentEl) return;

    const playBtn = document.getElementById('playBtn');
    const pauseBtn = document.getElementById('pauseBtn');
    const stopBtn = document.getElementById('stopBtn');
    const speedBtns = document.querySelectorAll('.speed-btn');
    const modeBtns = document.querySelectorAll('.mode-btn');
    const practiceBar = document.getElementById('practiceBar');
    const repeatBtn = document.getElementById('repeatBtn');
    const nextSentenceBtn = document.getElementById('nextSentenceBtn');
    const fontDecrease = document.getElementById('fontDecrease');
    const fontIncrease = document.getElementById('fontIncrease');

    const speechSupported = 'speechSynthesis' in window;

    let sentences = [];
    let currentIndex = -1;
    let mode = 'continuous';
    let rate = 0.9;
    let playing = false;
    let paused = false;
    let voice = null;
    let fontSize = 1.05;

    function pickVoice() {
        if (!speechSupported) return;
        const voices = window.speechSynthesis.getVoices();
        voice = voices.find(function (v) { return v.lang === 'en-US'; })
            || voices.find(function (v) { return v.lang === 'en-GB'; })
            || voices.find(function (v) { return v.lang && v.lang.indexOf('en') === 0; })
            || null;
    }

    if (speechSupported) {
        pickVoice();
        window.speechSynthesis.onvoiceschanged = pickVoice;
    }

    function tokenize() {
        sentences = [];
        const paragraphs = contentEl.querySelectorAll('.reader-paragraph');

        paragraphs.forEach(function (p) {
            const raw = p.textContent.replace(/\s+/g, ' ').trim();
            if (!raw) return;

            const rawSentences = raw.match(/[^.!?]+[.!?]+(\s+|$)|[^.!?]+$/g) || [raw];
            p.innerHTML = '';

            rawSentences.forEach(function (sentText) {
                const trimmed = sentText.trim();
                if (!trimmed) return;

                const sentenceSpan = document.createElement('span');
                sentenceSpan.className = 'sentence';

                const words = trimmed.split(/\s+/);
                const wordEntries = [];
                let cursor = 0;

                words.forEach(function (word, i) {
                    const wordSpan = document.createElement('span');
                    wordSpan.className = 'w';
                    wordSpan.textContent = word;
                    wordSpan.dataset.start = String(cursor);
                    wordSpan.addEventListener('click', function () {
                        if (typeof pronounce === 'function') {
                            pronounce(word.replace(/[.,!?;:"'()]/g, ''), null);
                        }
                    });
                    sentenceSpan.appendChild(wordSpan);
                    wordEntries.push({ el: wordSpan, start: cursor, timer: null });

                    cursor += word.length;
                    if (i < words.length - 1) {
                        sentenceSpan.appendChild(document.createTextNode(' '));
                        cursor += 1;
                    }
                });

                p.appendChild(sentenceSpan);
                p.appendChild(document.createTextNode(' '));

                sentences.push({ el: sentenceSpan, words: wordEntries, text: trimmed });
            });
        });
    }

    function clearHighlight() {
        contentEl.querySelectorAll('.w.speaking').forEach(function (el) {
            el.classList.remove('speaking');
        });
    }

    function highlightWordAt(sentence, charIndex) {
        let target = null;
        for (let i = 0; i < sentence.words.length; i++) {
            const w = sentence.words[i];
            const next = sentence.words[i + 1];
            if (charIndex >= w.start && (!next || charIndex < next.start)) {
                target = w;
                break;
            }
        }
        if (target) {
            clearHighlight();
            target.el.classList.add('speaking');
            target.el.scrollIntoView({ block: 'center', behavior: 'smooth' });
        }
    }

    function estimateDuration(text, currentRate) {
        const words = text.split(/\s+/).length;
        const wordsPerMinute = 150 * currentRate;
        return Math.max(400, (words / wordsPerMinute) * 60000);
    }

    function startFallbackHighlight(sentence) {
        const durationMs = estimateDuration(sentence.text, rate);
        const perWord = durationMs / Math.max(1, sentence.words.length);
        sentence.words.forEach(function (w, i) {
            w.timer = setTimeout(function () {
                clearHighlight();
                w.el.classList.add('speaking');
                w.el.scrollIntoView({ block: 'center', behavior: 'smooth' });
            }, perWord * i);
        });
    }

    function clearFallbackTimers(sentence) {
        sentence.words.forEach(function (w) {
            if (w.timer) {
                clearTimeout(w.timer);
                w.timer = null;
            }
        });
    }

    function showPracticeBar() {
        if (!practiceBar) return;
        practiceBar.classList.remove('d-none');
        practiceBar.classList.add('d-flex');
    }

    function hidePracticeBar() {
        if (!practiceBar) return;
        practiceBar.classList.add('d-none');
        practiceBar.classList.remove('d-flex');
    }

    function updateButtons() {
        playBtn.disabled = playing && !paused;
        pauseBtn.disabled = !playing || paused;
        stopBtn.disabled = !playing;
        playBtn.innerHTML = paused
            ? '<i class="bi bi-play-fill"></i> Resume'
            : '<i class="bi bi-play-fill"></i> Read';
    }

    function finishReading() {
        playing = false;
        paused = false;
        currentIndex = -1;
        clearHighlight();
        hidePracticeBar();
        updateButtons();
    }

    function speakSentence(index) {
        if (!speechSupported) return;

        if (index < 0 || index >= sentences.length) {
            finishReading();
            return;
        }

        currentIndex = index;
        const sentence = sentences[index];

        const utterance = new SpeechSynthesisUtterance(sentence.text);
        utterance.lang = 'en-US';
        utterance.rate = rate;
        if (voice) utterance.voice = voice;

        let boundaryFired = false;
        let fallbackStarted = false;
        const fallbackTimeout = setTimeout(function () {
            if (!boundaryFired) {
                fallbackStarted = true;
                startFallbackHighlight(sentence);
            }
        }, 300);

        utterance.onboundary = function (e) {
            boundaryFired = true;
            clearTimeout(fallbackTimeout);
            if (fallbackStarted) {
                clearFallbackTimers(sentence);
                fallbackStarted = false;
            }
            if (typeof e.charIndex === 'number') {
                highlightWordAt(sentence, e.charIndex);
            }
        };

        utterance.onend = function () {
            clearTimeout(fallbackTimeout);
            clearFallbackTimers(sentence);
            if (!playing) return;

            if (mode === 'sentence') {
                showPracticeBar();
                updateButtons();
            } else {
                clearHighlight();
                speakSentence(currentIndex + 1);
            }
        };

        utterance.onerror = function () {
            clearTimeout(fallbackTimeout);
            clearFallbackTimers(sentence);
        };

        window.speechSynthesis.speak(utterance);
    }

    if (!speechSupported) {
        playBtn.disabled = true;
        pauseBtn.disabled = true;
        stopBtn.disabled = true;
    } else {
        playBtn.addEventListener('click', function () {
            if (paused) {
                paused = false;
                window.speechSynthesis.resume();
                updateButtons();
                return;
            }
            window.speechSynthesis.cancel();
            hidePracticeBar();
            playing = true;
            paused = false;
            updateButtons();
            speakSentence(currentIndex >= 0 && currentIndex < sentences.length ? currentIndex : 0);
        });

        pauseBtn.addEventListener('click', function () {
            if (!playing || paused) return;
            paused = true;
            window.speechSynthesis.pause();
            updateButtons();
        });

        stopBtn.addEventListener('click', function () {
            playing = false;
            paused = false;
            window.speechSynthesis.cancel();
            clearHighlight();
            hidePracticeBar();
            currentIndex = -1;
            updateButtons();
        });

        repeatBtn.addEventListener('click', function () {
            hidePracticeBar();
            window.speechSynthesis.cancel();
            speakSentence(currentIndex);
        });

        nextSentenceBtn.addEventListener('click', function () {
            hidePracticeBar();
            clearHighlight();
            speakSentence(currentIndex + 1);
        });
    }

    speedBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            rate = parseFloat(btn.dataset.rate);
            speedBtns.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
        });
    });

    modeBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            mode = btn.dataset.mode;
            modeBtns.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
            if (mode === 'continuous') hidePracticeBar();
        });
    });

    if (fontDecrease && fontIncrease) {
        fontDecrease.addEventListener('click', function () {
            fontSize = Math.max(0.85, fontSize - 0.1);
            contentEl.style.fontSize = fontSize + 'rem';
        });
        fontIncrease.addEventListener('click', function () {
            fontSize = Math.min(1.6, fontSize + 0.1);
            contentEl.style.fontSize = fontSize + 'rem';
        });
    }

    window.addEventListener('beforeunload', function () {
        if (speechSupported) window.speechSynthesis.cancel();
    });

    function saveProgress() {
        if (!config || !config.saveUrl) return;
        const tokenEl = document.querySelector('meta[name="csrf-token"]');
        if (!tokenEl) return;

        fetch(config.saveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': tokenEl.getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({ page: config.page }),
        }).catch(function () {});
    }

    tokenize();
    updateButtons();
    hidePracticeBar();
    saveProgress();
}
