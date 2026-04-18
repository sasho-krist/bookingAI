@extends('layouts.app')

@section('title', 'Локации — '.config('app.name'))

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Локации</h1>
            <p class="text-body-secondary small mb-0">
                <a href="{{ route('home') }}">Начало</a> ·
                <a href="{{ route('bookings.index') }}">Резервации</a> ·
                <a href="{{ route('businesses.create') }}">Нов бизнес</a>
            </p>
        </div>
    </div>

    <div data-show-when="table">
        <div class="card shadow-sm">
            <div class="table-responsive mb-0 rounded">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Име</th>
                            <th>Тип</th>
                            <th>Часова зона</th>
                            <th class="text-end">Услуги</th>
                            <th class="text-end">Резервации</th>
                            <th class="text-end">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($venues as $venue)
                            <tr>
                                <td>{{ $venue->id }}</td>
                                <td>
                                    <a href="{{ route('venues.show', $venue) }}" class="fw-semibold link-body-emphasis text-decoration-none">{{ $venue->name }}</a>
                                    @if ($venue->business)
                                        <div class="small text-body-secondary">{{ $venue->business->name }}</div>
                                    @endif
                                </td>
                                <td><span class="badge text-bg-secondary">{{ $venue->type }}</span></td>
                                <td class="text-body-secondary small">{{ $venue->timezone }}</td>
                                <td class="text-end">{{ $venue->services_count }}</td>
                                <td class="text-end">{{ $venue->bookings_count }}</td>
                                <td class="text-end">
                                    <a href="{{ route('venues.edit', $venue) }}" class="btn btn-sm btn-outline-secondary">Редакция</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-body-secondary py-4">Няма записи.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div data-show-when="card" class="d-none">
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
            @forelse ($venues as $venue)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <a href="{{ route('venues.show', $venue) }}" class="h5 card-title mb-0 link-body-emphasis text-decoration-none">{{ $venue->name }}</a>
                                <span class="badge text-bg-primary">#{{ $venue->id }}</span>
                            </div>
                            @if ($venue->business)
                                <p class="small text-body-secondary mb-1">{{ $venue->business->name }}</p>
                            @endif
                            <p class="small text-body-secondary mb-2">{{ $venue->timezone }}</p>
                            <span class="badge text-bg-secondary">{{ $venue->type }}</span>
                        </div>
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between bg-transparent">
                                Услуги <span>{{ $venue->services_count }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between bg-transparent">
                                Резервации <span>{{ $venue->bookings_count }}</span>
                            </li>
                        </ul>
                        <div class="card-footer bg-transparent border-top-0">
                            <div class="d-flex flex-wrap gap-2 justify-content-between">
                                <a href="{{ route('venues.show', $venue) }}" class="btn btn-sm btn-primary">Отвори</a>
                                <a href="{{ route('venues.edit', $venue) }}" class="btn btn-sm btn-outline-secondary">Редакция</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-secondary mb-0">Няма локации.</div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
