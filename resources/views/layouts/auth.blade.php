<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.seo-meta', ['showOg' => false])
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" href="{{ asset('images/logo.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.svg') }}">
    <script>
        (function () {
            var theme = localStorage.getItem('appTheme');
            if (theme === null || theme === '') theme = 'dark';
            document.documentElement.setAttribute('data-bs-theme', theme === 'light' ? 'light' : 'dark');
        })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body class="bg-body-secondary min-vh-100 d-flex flex-column">
    <nav class="navbar navbar-expand-lg border-bottom bg-body shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-semibold" href="{{ url('/') }}">
                <img src="{{ asset('images/logo.svg') }}" width="28" height="28" alt="" class="d-inline-block">
                <span>{{ config('app.name') }}</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#authNav" aria-controls="authNav" aria-expanded="false" aria-label="Меню">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="authNav">
                <div class="navbar-nav ms-auto flex-column flex-lg-row gap-lg-2 align-items-lg-center mt-3 mt-lg-0">
                    @guest
                        <a class="nav-link" href="{{ route('login') }}">Вход</a>
                        <a class="nav-link" href="{{ route('register') }}">Регистрация</a>
                    @else
                        <a class="nav-link" href="{{ route('home') }}">Начало</a>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm">Изход</button>
                        </form>
                    @endguest
                    <div class="d-flex align-items-center gap-2 pt-2 pt-lg-0 border-top border-lg-0 mt-2 mt-lg-0 ps-lg-2">
                        <span class="small text-body-secondary d-none d-lg-inline">Тема</span>
                        @include('partials.nav-theme-buttons')
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1 d-flex align-items-center py-4">
        <div class="container">
            @yield('content')
        </div>
    </main>

    @include('partials.site-footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @include('partials.theme-switch-script')
    <script>
        document.querySelectorAll('[data-password-toggle]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = this.getAttribute('data-password-toggle');
                var input = document.getElementById(id);
                var icon = this.querySelector('i');
                if (!input || !icon) return;
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('bi-eye', 'bi-eye-slash');
                    this.setAttribute('aria-label', 'Скрий паролата');
                } else {
                    input.type = 'password';
                    icon.classList.replace('bi-eye-slash', 'bi-eye');
                    this.setAttribute('aria-label', 'Покажи паролата');
                }
            });
        });
    </script>
    @stack('scripts')
    @include('partials.cookie-banner')
</body>
</html>
