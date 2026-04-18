@extends('layouts.app')

@section('title', 'Нов тип бизнес — '.config('app.name'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h4 mb-0">Нов тип бизнес</h1>
                <div class="d-flex gap-2 flex-wrap">
                    @if (request('return') === 'setup-business')
                        <a href="{{ route('setup.business') }}" class="btn btn-sm btn-outline-secondary">Към настройката</a>
                    @else
                        <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary">Начало</a>
                        <a href="{{ route('business-types.index') }}" class="btn btn-sm btn-outline-secondary">Списък</a>
                    @endif
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <p class="small text-body-secondary">Напр. „Фризьорски салон“, „Автосервиз“, „Клиника“. После избирате типа при създаване на бизнес.</p>
                    <form method="POST" action="{{ route('business-types.store') }}">
                        @csrf
                        @if (request('return') === 'setup-business')
                            <input type="hidden" name="return" value="setup-business">
                        @endif
                        <div class="mb-3">
                            <label for="name" class="form-label">Име на типа</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required maxlength="255" autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Запази</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
