@extends('layouts.app')

@section('title', 'Първоначална настройка — локация')

@section('content')
    @include('pages.setup.partials.progress', ['step' => $step])

    <div class="mb-3">
        <h1 class="h4 mb-1">Стъпка 2: Добавете локация</h1>
        <p class="text-body-secondary small mb-0">Бизнес: <strong>{{ $business->name }}</strong> ({{ $business->businessType->name }})</p>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('setup.storeLocation') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Име на локацията <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required maxlength="255" autofocus placeholder="Напр. Салон — център">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Категория / бележка</label>
                            <input type="text" name="type" id="type" value="{{ old('type', 'generic') }}" class="form-control @error('type') is-invalid @enderror" maxlength="64" placeholder="salon, clinic, auto …">
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Вътрешен таг (по избор).</div>
                        </div>
                        <div class="mb-4">
                            <label for="timezone" class="form-label">Часова зона <span class="text-danger">*</span></label>
                            <select name="timezone" id="timezone" class="form-select @error('timezone') is-invalid @enderror" required>
                                @foreach (['Europe/Sofia', 'Europe/London', 'UTC'] as $tz)
                                    <option value="{{ $tz }}" @selected(old('timezone', 'Europe/Sofia') === $tz)>{{ $tz }}</option>
                                @endforeach
                            </select>
                            @error('timezone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Напред: услуга</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
