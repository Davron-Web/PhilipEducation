/**
 * Word pronunciation: plays a real mp3 if available, otherwise falls back
 * to the browser's built-in speech synthesis. Playback only ever starts
 * from a user click (autoplay policies block anything else anyway).
 */
let preferredVoice = null;

/**
 * Cloud/neural voices (Chrome's "Google US English", Edge's "... Online
 * (Natural)") sound far more human than the classic offline system voices
 * (e.g. the default "Microsoft David"), so prefer those by name when the
 * browser exposes them; fall back to locale closeness otherwise.
 */
function pickBestEnglishVoice() {
    if (!('speechSynthesis' in window)) return null;

    const englishVoices = window.speechSynthesis.getVoices()
        .filter(function (v) { return v.lang && v.lang.toLowerCase().indexOf('en') === 0; });
    if (!englishVoices.length) return null;

    const qualityPatterns = [/google/i, /online/i, /neural/i, /zira|aria|jenny|ava/i];
    for (const pattern of qualityPatterns) {
        const match = englishVoices.find(function (v) { return pattern.test(v.name); });
        if (match) return match;
    }

    return englishVoices.find(function (v) { return v.lang === 'en-US'; })
        || englishVoices.find(function (v) { return v.lang === 'en-GB'; })
        || englishVoices[0];
}

function refreshPreferredVoice() {
    preferredVoice = pickBestEnglishVoice();
}

if ('speechSynthesis' in window) {
    refreshPreferredVoice();
    window.speechSynthesis.onvoiceschanged = refreshPreferredVoice;
}

function speak(word) {
    if (!('speechSynthesis' in window)) {
        return;
    }

    window.speechSynthesis.cancel();

    const utterance = new SpeechSynthesisUtterance(word);
    utterance.lang = 'en-US';
    utterance.rate = 0.95;
    if (preferredVoice) {
        utterance.voice = preferredVoice;
    }

    window.speechSynthesis.speak(utterance);
}

function pronounce(word, audioUrl) {
    if (!audioUrl) {
        speak(word);
        return;
    }

    let fellBack = false;
    const fallback = () => {
        if (fellBack) return;
        fellBack = true;
        speak(word);
    };

    const audio = new Audio(audioUrl);
    audio.addEventListener('error', fallback);
    audio.play().catch(fallback);
}
