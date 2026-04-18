@extends('layouts.app')

@section('title', 'Профил — '.config('app.name'))

@section('content')
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Начало</a></li>
                <li class="breadcrumb-item active">Профил</li>
            </ol>
        </nav>
        <h1 class="h3 mb-1">Вашият профил</h1>
        <p class="text-body-secondary small mb-0">{{ $user->email }}</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold">Име и снимка</div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="d-flex align-items-start gap-3 mb-4">
                            @if ($user->avatarUrl())
                                <img src="{{ $user->avatarUrl() }}" alt="" width="72" height="72" class="rounded-circle border object-fit-cover flex-shrink-0 bg-body-secondary" style="object-fit: cover;">
                            @else
                                @php $ini = mb_strtoupper(mb_substr($user->firstName(), 0, 1)); @endphp
                                <div class="rounded-circle bg-primary-subtle text-primary-emphasis d-flex align-items-center justify-content-center flex-shrink-0 border" style="width: 72px; height: 72px; font-size: 1.5rem;">
                                    {{ $ini !== '' ? $ini : '?' }}
                                </div>
                            @endif
                            <div class="small text-body-secondary">
                                Препоръчително квадратно изображение (JPG, PNG, WebP до 2 MB).
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Име</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required maxlength="255" autocomplete="name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="avatar" class="form-label">Нова снимка / аватар</label>
                            <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png,image/gif,image/webp" class="form-control @error('avatar') is-invalid @enderror">
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if ($user->avatar_path)
                            <div class="form-check mb-4">
                                <input type="checkbox" name="remove_avatar" id="remove_avatar" value="1" class="form-check-input @error('remove_avatar') is-invalid @enderror">
                                <label class="form-check-label" for="remove_avatar">Премахни текущата снимка</label>
                            </div>
                        @endif

                        <button type="submit" class="btn btn-primary">Запази профила</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold">Промяна на парола</div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Текуща парола</label>
                            <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" required autocomplete="current-password">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Нова парола</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Повтори новата парола</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn btn-outline-primary">Смени паролата</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
