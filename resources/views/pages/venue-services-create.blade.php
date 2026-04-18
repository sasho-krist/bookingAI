@extends('layouts.app')

@section('title', 'Нова услуга — '.$venue->name)

@section('content')
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Начало</a></li>
                @if ($venue->business)
                    <li class="breadcrumb-item"><a href="{{ route('businesses.show', $venue->business) }}">{{ $venue->business->name }}</a></li>
                @endif
                <li class="breadcrumb-item"><a href="{{ route('venues.show', $venue) }}">{{ $venue->name }}</a></li>
                <li class="breadcrumb-item active">Нова услуга</li>
            </ol>
        </nav>
        <h1 class="h3 mb-1">Нова услуга</h1>
        <p class="text-body-secondary small mb-0">
            Локация: <strong>{{ $venue->name }}</strong>
            @if ($venue->business)
                · Бизнес: {{ $venue->business->name }}
            @endif
        </p>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('venues.services.store', $venue) }}">
                        @csrf
                        @if (request('return') === 'booking')
                            <input type="hidden" name="return" value="booking">
                        @endif
                        <div class="mb-3">
                            <label for="name" class="form-label">Име на услугата <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required maxlength="255" autofocus placeholder="Напр. Подстригване">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="duration_minutes" class="form-label">Продължителност (минути) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', 30) }}" class="form-control @error('duration_minutes') is-invalid @enderror" required min="5" max="1440" step="5">
                            @error('duration_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Запази услугата</button>
                        <a href="{{ request('return') === 'booking' ? route('bookings.create') : route('venues.show', $venue) }}" class="btn btn-link">Отказ</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
