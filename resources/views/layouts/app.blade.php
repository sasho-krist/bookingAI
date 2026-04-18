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
    <style>
        .app-sidebar {
            width: 16.5rem;
            min-height: 100vh;
        }
        @media (min-width: 768px) {
            .app-sidebar {
                position: sticky;
                top: 0;
                align-self: flex-start;
                max-height: 100vh;
                overflow-y: auto;
            }
        }
        @media (max-width: 767.98px) {
            .app-sidebar {
                width: 100%;
                min-height: auto;
            }
        }
        .app-brand-logo {
            width: 1.75rem;
            height: 1.75rem;
            flex-shrink: 0;
        }
        .app-sidebar-user-avatar {
            width: 2.5rem;
            height: 2.5rem;
            flex-shrink: 0;
            object-fit: cover;
        }
        .app-sidebar-user-initial {
            width: 2.5rem;
            height: 2.5rem;
            flex-shrink: 0;
            font-size: 1rem;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-body-secondary">
    <div class="d-flex flex-column flex-md-row min-vh-100">
        <aside class="app-sidebar border-end bg-body-tertiary d-flex flex-column flex-shrink-0">
            <div class="p-3 border-bottom">
                <a href="{{ route('home') }}" class="text-decoration-none text-body fw-semibold d-flex align-items-center gap-2">
                    <img src="{{ asset('images/logo.svg') }}" width="28" height="28" class="app-brand-logo" alt="">
                    <span>{{ config('app.name') }}</span>
                </a>
            </div>
            <nav class="nav flex-column gap-1 p-3 flex-grow-1">
                @php
                    $navUser = auth()->user();
                    $navFirst = $navUser->firstName();
                    $navInitial = mb_strtoupper(mb_substr($navFirst, 0, 1));
                @endphp
                <a class="nav-link rounded d-flex align-items-center gap-2 mb-2 pb-2 border-bottom border-secondary-subtle @if(request()->routeIs('profile.*')) active bg-primary-subtle text-primary-emphasis @endif" href="{{ route('profile.edit') }}">
                    @if ($navUser->avatarUrl())
                        <img src="{{ $navUser->avatarUrl() }}" alt="" width="40" height="40" class="rounded-circle border app-sidebar-user-avatar bg-body-secondary">
                    @else
                        <span class="rounded-circle bg-primary-subtle text-primary-emphasis border d-inline-flex align-items-center justify-content-center fw-semibold app-sidebar-user-initial">{{ $navInitial !== '' ? $navInitial : '?' }}</span>
                    @endif
                    <span class="text-truncate">{{ $navFirst }}</span>
                </a>
                <a class="nav-link rounded d-flex align-items-center gap-2 @if(request()->routeIs('home')) active bg-primary-subtle text-primary-emphasis @endif" href="{{ route('home') }}">
                    <i class="bi bi-speedometer2"></i> Начало
                </a>
                <a class="nav-link rounded d-flex align-items-center gap-2 @if(request()->routeIs('bookings.index')) active bg-primary-subtle text-primary-emphasis @endif" href="{{ route('bookings.index') }}">
                    <i class="bi bi-calendar-check"></i> Резервации
                </a>
                <a class="nav-link rounded d-flex align-items-center gap-2 @if(request()->routeIs('bookings.create')) active bg-primary-subtle text-primary-emphasis @endif" href="{{ route('bookings.create') }}">
                    <i class="bi bi-plus-circle"></i> Нова резервация
                </a>
                <hr class="my-2 opacity-25">
                <a class="nav-link rounded d-flex align-items-center gap-2 @if(request()->routeIs('businesses.*')) active bg-primary-subtle text-primary-emphasis @endif" href="{{ route('businesses.index') }}">
                    <i class="bi bi-building"></i> Бизнеси
                </a>
                <a class="nav-link rounded d-flex align-items-center gap-2 @if(request()->routeIs('business-types.*')) active bg-primary-subtle text-primary-emphasis @endif" href="{{ route('business-types.index') }}">
                    <i class="bi bi-tags"></i> Типове бизнес
                </a>
                <a class="nav-link rounded d-flex align-items-center gap-2 @if(request()->routeIs('venues.index') || request()->routeIs('venues.show') || request()->routeIs('venues.services.*') || request()->routeIs('venues.business-hours.*') || request()->routeIs('venues.edit') || request()->routeIs('venues.update') || request()->routeIs('venues.destroy')) active bg-primary-subtle text-primary-emphasis @endif" href="{{ route('venues.index') }}">
                    <i class="bi bi-shop"></i> Локации
                </a>
                <a class="nav-link rounded d-flex align-items-center gap-2 @if(request()->routeIs('setup.*')) active bg-primary-subtle text-primary-emphasis @endif" href="{{ route('setup.index') }}">
                    <i class="bi bi-list-check"></i> Първоначална настройка
                </a>
                <a class="nav-link rounded d-flex align-items-center gap-2 @if(request()->routeIs('api.docs')) active bg-primary-subtle text-primary-emphasis @endif" href="{{ route('api.docs') }}">
                    <i class="bi bi-code-slash"></i> API документация
                </a>
                <a class="nav-link rounded d-flex align-items-center gap-2 @if(request()->routeIs('businesses.create') || request()->routeIs('businesses.store')) active bg-primary-subtle text-primary-emphasis @endif" href="{{ route('businesses.create') }}">
                    <i class="bi bi-building-add"></i> Нов бизнес
                </a>
            </nav>
            <div class="p-3 border-top mt-auto d-flex flex-column gap-3">
                <div>
                    <div class="small text-body-secondary mb-1">Тема</div>
                    @include('partials.nav-theme-buttons', ['wrapperClass' => 'w-100'])
                </div>
                <div>
                    <div class="small text-body-secondary mb-1">Изглед</div>
                    <div class="btn-group w-100" role="group" aria-label="Изглед">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="view-table" title="Таблица">
                            <i class="bi bi-table"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="view-card" title="Карти">
                            <i class="bi bi-grid-3x2-gap"></i>
                        </button>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="d-grid">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-right me-1"></i> Изход
                    </button>
                </form>
            </div>
        </aside>
        <main class="flex-grow-1 overflow-auto">
            <div class="container-fluid py-4 px-3 px-lg-4">
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Затвори"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Затвори"></button>
                    </div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @include('partials.theme-switch-script')
    <script>
        (function () {
            var VIEW_KEY = 'appViewMode';

            function defaultCardForPath(path) {
                var pages = ['home', 'bookings', 'venues'];
                for (var i = 0; i < pages.length; i++) {
                    var p = pages[i];
                    if (path === '/' + p || path.endsWith('/' + p)) {
                        return true;
                    }
                }
                return false;
            }

            function getViewMode() {
                var v = localStorage.getItem(VIEW_KEY);
                if (v === null || v === '') {
                    // New domain has no localStorage — default cards on listing pages (works under /bookingAI/… too).
                    var path = (window.location.pathname || '').replace(/\/+$/, '') || '/';
                    return defaultCardForPath(path) ? 'card' : 'table';
                }
                return v === 'card' ? 'card' : 'table';
            }

            function setViewMode(mode) {
                localStorage.setItem(VIEW_KEY, mode);
                applyViewMode(mode);
                syncViewButtons();
            }

            function syncViewButtons() {
                var v = getViewMode();
                var bt = document.getElementById('view-table');
                var bc = document.getElementById('view-card');
                if (bt) bt.classList.toggle('active', v === 'table');
                if (bc) bc.classList.toggle('active', v === 'card');
            }

            function applyViewMode(mode) {
                document.querySelectorAll('[data-show-when]').forEach(function (el) {
                    var w = el.getAttribute('data-show-when');
                    el.classList.toggle('d-none', w !== mode);
                });
            }

            document.getElementById('view-table')?.addEventListener('click', function () { setViewMode('table'); });
            document.getElementById('view-card')?.addEventListener('click', function () { setViewMode('card'); });

            applyViewMode(getViewMode());
            syncViewButtons();
        })();
    </script>
    @stack('scripts')
    @include('partials.cookie-banner')
</body>
</html>
