(() => {
    const field = document.getElementById('campus-director-name');
    if (!field) return;

    const storageKey = 'skedyul.campusDirectorName';
    try {
        field.value = localStorage.getItem(storageKey) || '';
        field.addEventListener('input', () => {
            localStorage.setItem(storageKey, field.value);
        });
    } catch (error) {
        // Keep the input usable when browser storage is disabled.
    }
})();
