@extends('layouts.app')

@section('title', 'Бизнеси — '.config('app.name'))

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-0">Бизнеси</h1>
            <p class="small text-body-secondary mb-0 mt-1"><a href="{{ route('home') }}">Начало</a> · <a href="{{ route('business-types.index') }}">Типове бизнес</a></p>
        </div>
        <a href="{{ route('businesses.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-building-add me-1"></i> Нов бизнес
        </a>
    </div>

    <div class="card shadow-sm">
        <ul class="list-group list-group-flush">
            @forelse ($businesses as $business)
                <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="flex-grow-1 min-w-0">
                        <a href="{{ route('businesses.show', $business) }}" class="fw-semibold link-body-emphasis text-decoration-none">{{ $business->name }}</a>
                        <div class="small text-body-secondary mt-1">
                            <span class="badge text-bg-secondary">{{ $business->businessType->name }}</span>
                            <span class="ms-1">{{ $business->venues_count }} локации</span>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 flex-shrink-0">
                        <a href="{{ route('businesses.edit', $business) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil me-1"></i> Редактирай
                        </a>
                        <form method="POST" action="{{ route('businesses.destroy', $business) }}" class="d-inline" onsubmit="return confirm('Изтриване на бизнеса? Локациите ще останат, но без връзка към този бизнес.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i> Изтрий</button>
                        </form>
                    </div>
                </li>
            @empty
                <li class="list-group-item text-body-secondary">
                    Няма бизнеси. <a href="{{ route('businesses.create') }}">Създайте бизнес</a> или ползвайте <a href="{{ route('setup.index') }}">първоначална настройка</a>.
                </li>
            @endforelse
        </ul>
    </div>
@endsection
