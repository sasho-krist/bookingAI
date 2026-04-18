<div id="cookie-consent-banner" class="position-fixed bottom-0 start-0 end-0 d-none p-3 pt-0" style="z-index: 1080;" role="dialog" aria-live="polite" aria-label="Информация за бисквитки">
    <div class="container">
        <div class="card shadow-lg border mb-0">
            <div class="card-body p-3 p-md-4">
                <div class="row align-items-start g-3">
                    <div class="col-md flex-grow-1">
                        <p class="small mb-2 fw-semibold text-body"><i class="bi bi-cookie me-1"></i> Бисквитки и локално съхранение</p>
                        <p class="small text-body-secondary mb-0">
                            Използваме необходими технологии за сигурност и работа на сайта (напр. сесия при вход). Запазваме предпочитания като тема във вашия браузър.
                            Повече информация — в <a href="{{ route('legal.privacy') }}" class="link-body-emphasis">политиката за поверителност</a>.
                        </p>
                    </div>
                    <div class="col-md-auto d-flex flex-wrap gap-2 justify-content-md-end align-items-center">
                        <button type="button" id="cookie-consent-essential" class="btn btn-outline-secondary btn-sm">
                            Само необходимите
                        </button>
                        <button type="button" id="cookie-consent-accept" class="btn btn-primary btn-sm">
                            Приемам
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
(function () {
    var KEY = 'bookingCookieConsent';
    var VERSION = '1';

    function readChoice() {
        try {
            var raw = localStorage.getItem(KEY);
            if (!raw) return null;
            var data = JSON.parse(raw);
            if (!data || data.v !== VERSION) return null;
            return data.choice || null;
        } catch (e) {
            return null;
        }
    }

    function saveChoice(choice) {
        try {
            localStorage.setItem(KEY, JSON.stringify({ v: VERSION, choice: choice, at: Date.now() }));
        } catch (e) {}
    }

    function hideBanner() {
        var el = document.getElementById('cookie-consent-banner');
        if (el) el.classList.add('d-none');
    }

    function showBanner() {
        var el = document.getElementById('cookie-consent-banner');
        if (el) el.classList.remove('d-none');
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (readChoice() !== null) return;

        showBanner();

        document.getElementById('cookie-consent-accept')?.addEventListener('click', function () {
            saveChoice('all');
            hideBanner();
        });

        document.getElementById('cookie-consent-essential')?.addEventListener('click', function () {
            saveChoice('essential');
            hideBanner();
        });
    });
})();
</script>
