@extends('layouts.app')

@section('title', 'Редакция на бизнес — '.config('app.name'))

@section('content')
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Начало</a></li>
                <li class="breadcrumb-item"><a href="{{ route('businesses.show', $business) }}">{{ $business->name }}</a></li>
                <li class="breadcrumb-item active">Редакция</li>
            </ol>
        </nav>
        <h1 class="h3 mb-1">Редакция на бизнес</h1>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('businesses.update', $business) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Име на бизнеса</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $business->name) }}" class="form-control @error('name') is-invalid @enderror" required maxlength="255" autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="business_type_id" class="form-label">Тип бизнес</label>
                            <select name="business_type_id" id="business_type_id" class="form-select @error('business_type_id') is-invalid @enderror" required>
                                @foreach ($businessTypes as $type)
                                    <option value="{{ $type->id }}" @selected(old('business_type_id', $business->business_type_id) == $type->id)>{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('business_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Имейл за контакт</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $business->email) }}" class="form-control @error('email') is-invalid @enderror" maxlength="255" autocomplete="email" placeholder="по избор">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="phone" class="form-label">Телефон за контакт</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $business->phone) }}" class="form-control @error('phone') is-invalid @enderror" maxlength="64" autocomplete="tel" placeholder="по избор">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Запази</button>
                        <a href="{{ route('businesses.show', $business) }}" class="btn btn-link">Отказ</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
