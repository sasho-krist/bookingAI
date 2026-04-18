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
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                <img src="{{ asset('images/logo.svg') }}" width="28" height="28" alt="" class="d-inline-block">
                <span>{{ config('app.name') }}</span>
            </a>
            <div class="navbar-nav ms-auto flex-row gap-2 align-items-center">
                @guest
                    <a class="nav-link text-white" href="{{ route('login') }}">Вход</a>
                    <a class="nav-link text-white" href="{{ route('register') }}">Регистрация</a>
                @else
                    <a class="nav-link text-white" href="{{ route('home') }}">Начало</a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">Изход</button>
                    </form>
                @endguest
            </div>
        </div>
    </nav>

    <main class="flex-grow-1 d-flex align-items-center py-4">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
