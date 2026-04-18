@extends('layouts.app')

@section('title', 'Първоначална настройка — бизнес')

@section('content')
    @include('pages.setup.partials.progress', ['step' => $step])

    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <h1 class="h4 mb-3">Стъпка 1: Създайте бизнес</h1>
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    @if ($businessTypes->isEmpty())
                        <div class="alert alert-warning mb-0">Първо добавете <a href="{{ route('business-types.create', ['return' => 'setup-business']) }}">тип бизнес</a>, след което се върнете тук.</div>
                    @else
                        <form method="POST" action="{{ route('setup.storeBusiness') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Име на бизнеса</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required maxlength="255" autofocus placeholder="Напр. Студио Луна">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Телефон</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" maxlength="64" autocomplete="tel" placeholder="Напр. +359 88 …">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Имейл</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" maxlength="255" autocomplete="email" placeholder="contact@example.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
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
                                <p class="form-text small mb-0 mt-1">
                                    <a href="{{ route('business-types.create', ['return' => 'setup-business']) }}" class="link-secondary"><i class="bi bi-plus-lg"></i> Нов тип бизнес</a>
                                </p>
                            </div>
                            <button type="submit" class="btn btn-primary">Напред: локация</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
