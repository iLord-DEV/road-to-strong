// Progressive enhancement for habit taps: save in the background instead of
// reloading the page. Without JS the forms still work — the server then
// redirects back with a #habit-{field} anchor.
document.addEventListener('submit', (event) => {
    const form = event.target.closest('form[data-habit]');
    if (!form) return;

    event.preventDefault();

    const button = form.querySelector('button[type="submit"]');
    const wasPressed = button.getAttribute('aria-pressed') === 'true';

    // Optimistic update mirroring the server logic: tapping the selected
    // value clears it, anything else becomes the only selected option.
    const group = form.parentElement;
    group.querySelectorAll('button[aria-pressed]').forEach((other) => {
        other.setAttribute('aria-pressed', 'false');
    });
    button.setAttribute('aria-pressed', wasPressed ? 'false' : 'true');

    fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { Accept: 'text/html' },
    })
        .then((response) => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
        })
        .catch(() => {
            // Fall back to a full submission so the tap is not lost
            form.submit();
        });
});
