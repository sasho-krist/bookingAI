@extends('layouts.auth')

@section('title', 'Вход — '.config('app.name'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h4 mb-3 text-center">Вход</h1>
                    <form method="POST" action="{{ route('login') }}" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Имейл</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email') }}"
                                autocomplete="username"
                                class="form-control @error('email') is-invalid @enderror"
                                required
                                autofocus
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <x-password-input
                            name="password"
                            label="Парола"
                            autocomplete="current-password"
                            required
                        />

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="remember" id="remember" class="form-check-input" value="1" @checked(old('remember'))>
                            <label class="form-check-label" for="remember">Запомни ме</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Влез</button>
                    </form>
                    <p class="text-center text-muted small mt-3 mb-0">
                        Нямате акаунт?
                        <a href="{{ route('register') }}">Регистрация</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
