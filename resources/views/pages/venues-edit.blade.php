@extends('layouts.app')

@section('title', 'Редакция на локация — '.$venue->name)

@section('content')
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Начало</a></li>
                @if ($venue->business)
                    <li class="breadcrumb-item"><a href="{{ route('businesses.show', $venue->business) }}">{{ $venue->business->name }}</a></li>
                @endif
                <li class="breadcrumb-item"><a href="{{ route('venues.show', $venue) }}">{{ $venue->name }}</a></li>
                <li class="breadcrumb-item active">Редакция</li>
            </ol>
        </nav>
        <h1 class="h3 mb-1">Редакция на локация</h1>
        <p class="text-body-secondary small mb-0">
            Линкове:
            <a href="{{ route('venues.show', $venue) }}">Детайли и календар</a>,
            <a href="{{ route('venues.services.create', $venue) }}">Добави услуга</a>,
            <a href="{{ route('venues.business-hours.edit', $venue) }}">Работно време</a>,
            <a href="{{ route('bookings.create') }}">Нова резервация</a>
        </p>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('venues.update', $venue) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Име на локацията <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $venue->name) }}" class="form-control @error('name') is-invalid @enderror" required maxlength="255" autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Категория / бележка</label>
                            <input type="text" name="type" id="type" value="{{ old('type', $venue->type) }}" class="form-control @error('type') is-invalid @enderror" maxlength="64">
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="timezone" class="form-label">Часова зона <span class="text-danger">*</span></label>
                            <select name="timezone" id="timezone" class="form-select @error('timezone') is-invalid @enderror" required>
                                @foreach (['Europe/Sofia', 'Europe/London', 'UTC'] as $tz)
                                    <option value="{{ $tz }}" @selected(old('timezone', $venue->timezone) === $tz)>{{ $tz }}</option>
                                @endforeach
                            </select>
                            @error('timezone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Запази</button>
                        <a href="{{ route('venues.show', $venue) }}" class="btn btn-link">Отказ</a>
                    </form>
                    <hr class="my-4">
                    <form method="POST" action="{{ route('venues.destroy', $venue) }}" onsubmit="return confirm('Това ще изтрие локацията и всички услуги и резервации към нея. Сигурни ли сте?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i> Изтрий локацията</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
