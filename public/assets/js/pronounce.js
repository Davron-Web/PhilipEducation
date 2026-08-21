/**
 * Word pronunciation: plays a real mp3 if available, otherwise falls back
 * to the browser's built-in speech synthesis. Playback only ever starts
 * from a user click (autoplay policies block anything else anyway).
 */
function speak(word) {
    if (!('speechSynthesis' in window)) {
        return;
    }

    window.speechSynthesis.cancel();

    const utterance = new SpeechSynthesisUtterance(word);
    utterance.lang = 'en-US';
    utterance.rate = 0.95;

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
