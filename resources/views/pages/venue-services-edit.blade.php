@extends('layouts.app')

@section('title', 'Редакция на услуга — '.$service->name)

@section('content')
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Начало</a></li>
                <li class="breadcrumb-item"><a href="{{ route('venues.show', $venue) }}">{{ $venue->name }}</a></li>
                <li class="breadcrumb-item active">{{ $service->name }}</li>
            </ol>
        </nav>
        <h1 class="h3 mb-1">Редакция на услуга</h1>
        <p class="text-body-secondary small mb-0">
            Локация: <strong>{{ $venue->name }}</strong>
            @if ($venue->business)
                · <a href="{{ route('businesses.show', $venue->business) }}">{{ $venue->business->name }}</a>
            @endif
        </p>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('venues.services.update', [$venue, $service]) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Име на услугата <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $service->name) }}" class="form-control @error('name') is-invalid @enderror" required maxlength="255" autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="duration_minutes" class="form-label">Продължителност (минути) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $service->duration_minutes) }}" class="form-control @error('duration_minutes') is-invalid @enderror" required min="5" max="1440" step="5">
                            @error('duration_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Запази</button>
                        <a href="{{ route('venues.show', $venue) }}" class="btn btn-link">Отказ</a>
                    </form>
                    <hr class="my-4">
                    <form method="POST" action="{{ route('venues.services.destroy', [$venue, $service]) }}" onsubmit="return confirm('Изтриване на услугата и свързаните резервации?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i> Изтрий услугата</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
