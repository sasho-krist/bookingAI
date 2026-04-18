<footer class="border-top bg-body mt-auto py-4">
    <div class="container">
        <div class="row row-cols-1 row-cols-lg-2 g-3 align-items-center small">
            <div class="text-body-secondary">
                Всички права запазени.
                Създадено от <a href="https://sasho-dev.com/portfolio/" class="link-body-emphasis text-decoration-none" target="_blank" rel="noopener noreferrer">sasho-dev</a>.
            </div>
            <div class="text-lg-end">
                <nav class="d-flex flex-wrap gap-3 justify-content-lg-end justify-content-start column-gap-3 row-gap-2" aria-label="Подвални връзки">
                    @guest
                        <a href="{{ route('login') }}" class="link-secondary text-decoration-none">Вход</a>
                        <a href="{{ route('register') }}" class="link-secondary text-decoration-none">Регистрация</a>
                    @endguest
                    <a href="{{ route('landing') }}" class="link-secondary text-decoration-none">Начало</a>
                    <a href="{{ route('legal.privacy') }}" class="link-secondary text-decoration-none">Политика за поверителност</a>
                    <a href="{{ route('legal.terms') }}" class="link-secondary text-decoration-none">Условия за ползване</a>
                    <a href="{{ route('legal.faq') }}" class="link-secondary text-decoration-none">ЧЗВ</a>
                </nav>
            </div>
        </div>
    </div>
</footer>
