<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            <a class="navbar-brand d-flex align-items-center gap-2 fw-semibold" href="{{ route('landing') }}">
                <img src="{{ asset('images/logo.svg') }}" width="28" height="28" alt="" class="d-inline-block">
                {{ config('app.name') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#landingNav" aria-controls="landingNav" aria-expanded="false" aria-label="Меню">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="landingNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-1">
                    <li class="nav-item"><a class="nav-link" href="{{ url('/#features') }}">Функции</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/#api') }}">API</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('legal.faq') }}">ЧЗВ</a></li>
                    <li class="nav-item d-flex align-items-center ms-lg-2 mb-2 mb-lg-0 pt-2 pt-lg-0 border-top border-lg-0 mt-2 mt-lg-0">
                        <span class="small text-body-secondary me-2 d-none d-lg-inline">Тема</span>
                        @include('partials.nav-theme-buttons')
                    </li>
                    @auth
                        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}"><i class="bi bi-speedometer2 me-1"></i>Табло</a></li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary btn-sm">Изход</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Вход</a></li>
                        <li class="nav-item"><a class="btn btn-primary btn-sm ms-lg-2" href="{{ route('register') }}">Регистрация</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1">
        @yield('content')
    </main>

    @include('partials.landing-footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @include('partials.theme-switch-script')
    @stack('scripts')
    @include('partials.cookie-banner')
</body>
</html>
