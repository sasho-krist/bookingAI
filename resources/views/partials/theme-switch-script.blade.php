<script>
(function () {
    var THEME_KEY = 'appTheme';

    function getTheme() {
        var t = localStorage.getItem(THEME_KEY);
        if (t === null || t === '') return 'dark';
        return t === 'light' ? 'light' : 'dark';
    }

    function setTheme(theme) {
        localStorage.setItem(THEME_KEY, theme);
        document.documentElement.setAttribute('data-bs-theme', theme);
        syncThemeButtons();
    }

    function syncThemeButtons() {
        var t = getTheme();
        document.querySelectorAll('.js-theme-btn[data-app-theme="dark"]').forEach(function (btn) {
            btn.classList.toggle('active', t === 'dark');
        });
        document.querySelectorAll('.js-theme-btn[data-app-theme="light"]').forEach(function (btn) {
            btn.classList.toggle('active', t === 'light');
        });
    }

    document.querySelectorAll('.js-theme-btn').forEach(function (btn) {
        var th = btn.getAttribute('data-app-theme');
        if (th !== 'dark' && th !== 'light') return;
        btn.addEventListener('click', function () {
            setTheme(th);
        });
    });

    setTheme(getTheme());
})();
</script>
