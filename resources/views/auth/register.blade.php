@extends('layouts.auth')

@section('title', 'Регистрация — '.config('app.name'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h4 mb-3 text-center">Регистрация</h1>
                    <form method="POST" action="{{ route('register') }}" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Име</label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name') }}"
                                autocomplete="name"
                                class="form-control @error('name') is-invalid @enderror"
                                required
                                autofocus
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Имейл</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                class="form-control @error('email') is-invalid @enderror"
                                required
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <x-password-input
                            name="password"
                            label="Парола"
                            autocomplete="new-password"
                            required
                        />

                        <x-password-input
                            name="password_confirmation"
                            id="password_confirmation"
                            label="Потвърди паролата"
                            autocomplete="new-password"
                            required
                        />

                        <button type="submit" class="btn btn-primary w-100">Създай акаунт</button>
                    </form>
                    <p class="text-center text-muted small mt-3 mb-0">
                        Вече имате акаунт?
                        <a href="{{ route('login') }}">Вход</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
