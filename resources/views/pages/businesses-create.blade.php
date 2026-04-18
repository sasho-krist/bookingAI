@extends('layouts.app')

@section('title', 'Нов бизнес — '.config('app.name'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h1 class="h4 mb-0">Нов бизнес</h1>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary">Начало</a>
                    <a href="{{ route('venues.index') }}" class="btn btn-sm btn-outline-secondary">Локации</a>
                    <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-outline-secondary">Резервации</a>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    @if ($businessTypes->isEmpty())
                        <div class="alert alert-warning">Първо добавете <a href="{{ route('business-types.create') }}">тип бизнес</a>.</div>
                    @else
                        <form method="POST" action="{{ route('businesses.store') }}">
                            @csrf
                            @if (request('return') === 'booking')
                                <input type="hidden" name="return" value="booking">
                            @endif
                            <div class="mb-3">
                                <label for="name" class="form-label">Име на бизнеса</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required maxlength="255" autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="business_type_id" class="form-label">Тип бизнес</label>
                                <select name="business_type_id" id="business_type_id" class="form-select @error('business_type_id') is-invalid @enderror" required>
                                    <option value="">— изберете —</option>
                                    @foreach ($businessTypes as $type)
                                        <option value="{{ $type->id }}" @selected(old('business_type_id') == $type->id)>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                                @error('business_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Имейл за контакт</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" maxlength="255" autocomplete="email" placeholder="по избор">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Не е задължително. Показва се на началната страница до бизнеса.</div>
                            </div>
                            <div class="mb-4">
                                <label for="phone" class="form-label">Телефон за контакт</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" maxlength="64" autocomplete="tel" placeholder="по избор">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Създай бизнес</button>
                            @if (request('return') === 'booking')
                                <a href="{{ route('bookings.create') }}" class="btn btn-link">Назад към новата резервация</a>
                            @endif
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
