@extends('layouts.app')

@section('title', 'Редакция на тип бизнес — '.config('app.name'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h1 class="h4 mb-0">Редактиране на тип</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary">Начало</a>
                    <a href="{{ route('business-types.index') }}" class="btn btn-sm btn-outline-secondary">Списък</a>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('business-types.update', $type) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Име на типа</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $type->name) }}" class="form-control @error('name') is-invalid @enderror" required maxlength="255" autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Запази</button>
                    </form>
                    <hr class="my-4">
                    <form method="POST" action="{{ route('business-types.destroy', $type) }}" onsubmit="return confirm('Изтриване на типа „{{ $type->name }}“? Разрешено само ако няма бизнеси с този тип.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i> Изтрий типа</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
