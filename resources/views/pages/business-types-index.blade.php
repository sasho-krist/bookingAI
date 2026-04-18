@extends('layouts.app')

@section('title', 'Типове бизнес — '.config('app.name'))

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-0">Типове бизнес</h1>
            <p class="small text-body-secondary mb-0 mt-1"><a href="{{ route('home') }}">Начало</a> · <a href="{{ route('businesses.index') }}">Бизнеси</a></p>
        </div>
        <a href="{{ route('business-types.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Добави тип
        </a>
    </div>

    <div class="card shadow-sm">
        <ul class="list-group list-group-flush">
            @forelse ($types as $type)
                <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <span class="fw-semibold">{{ $type->name }}</span>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="badge text-bg-secondary">{{ $type->businesses_count }} бизнеса</span>
                        <a href="{{ route('business-types.edit', $type) }}" class="btn btn-sm btn-outline-secondary">Редактирай</a>
                        <form method="POST" action="{{ route('business-types.destroy', $type) }}" class="d-inline" onsubmit="return confirm('Изтриване на типа? Не се допуска, ако има свързани бизнеси.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Изтрий</button>
                        </form>
                    </div>
                </li>
            @empty
                <li class="list-group-item text-body-secondary">Няма записи. <a href="{{ route('business-types.create') }}">Добавете тип</a>.</li>
            @endforelse
        </ul>
    </div>
@endsection
