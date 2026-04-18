@extends('layouts.app')

@section('title', 'Резервации — '.config('app.name'))

@section('content')
    @php
        $statusLabel = fn (?string $s) => match ($s) {
            'pending' => 'Чакаща',
            'confirmed' => 'Потвърдена',
            'cancelled' => 'Отменена',
            'completed' => 'Завършена',
            default => $s ?? '—',
        };
        $statusBadgeClass = fn (?string $s) => match ($s) {
            'pending' => 'warning',
            'confirmed' => 'success',
            'cancelled' => 'secondary',
            'completed' => 'primary',
            default => 'secondary',
        };
    @endphp
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h1 class="h3 mb-1">Резервации</h1>
            <p class="text-body-secondary small mb-0">
                Последни 50 записа. За статус „Чакаща“ са налични Одобри / Откажи.
                <span class="d-none d-md-inline">
                    · <a href="{{ route('home') }}">Начало</a>
                    · <a href="{{ route('venues.index') }}">Локации</a>
                </span>
                Изглед от страничната лента.
            </p>
        </div>
        <a href="{{ route('bookings.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Нова резервация
        </a>
    </div>

    <div data-show-when="table">
        <div class="card shadow-sm">
            <div class="table-responsive mb-0 rounded">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Начало</th>
                            <th>Локация</th>
                            <th>Услуга</th>
                            <th>Клиент</th>
                            <th>Статус</th>
                            <th class="text-end">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr>
                                <td>{{ $booking->id }}</td>
                                <td>{{ $booking->starts_at?->timezone(config('app.timezone'))->format('d.m.Y H:i') }}</td>
                                <td>
                                    @if ($booking->venue)
                                        <a href="{{ route('venues.show', $booking->venue) }}" class="link-body-emphasis text-decoration-none">{{ $booking->venue->name }}</a>
                                        @if ($booking->venue->business)
                                            <div class="small text-body-secondary">{{ $booking->venue->business->name }}</div>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if ($booking->service && $booking->venue)
                                        <a href="{{ route('venues.services.edit', [$booking->venue, $booking->service]) }}" class="link-secondary">{{ $booking->service->name }}</a>
                                    @else
                                        {{ $booking->service?->name ?? '—' }}
                                    @endif
                                </td>
                                <td>{{ $booking->customer?->name ?? '—' }}</td>
                                <td><span class="badge text-bg-{{ $statusBadgeClass($booking->status) }}">{{ $statusLabel($booking->status) }}</span></td>
                                <td class="text-end"><x-booking-actions :booking="$booking" /></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-body-secondary py-4">Няма резервации.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div data-show-when="card" class="d-none">
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
            @forelse ($bookings as $booking)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge text-bg-primary">#{{ $booking->id }}</span>
                                <span class="badge text-bg-{{ $statusBadgeClass($booking->status) }}">{{ $statusLabel($booking->status) }}</span>
                            </div>
                            <p class="fw-semibold mb-1">{{ $booking->starts_at?->timezone(config('app.timezone'))->format('d.m.Y H:i') }}</p>
                            <p class="small mb-1">
                                @if ($booking->venue)
                                    <a href="{{ route('venues.show', $booking->venue) }}" class="text-body-secondary text-decoration-none">{{ $booking->venue->name }}</a>
                                @else
                                    —
                                @endif
                            </p>
                            <p class="small mb-0">
                                @if ($booking->service && $booking->venue)
                                    <a href="{{ route('venues.services.edit', [$booking->venue, $booking->service]) }}" class="link-secondary">{{ $booking->service->name }}</a>
                                @else
                                    {{ $booking->service?->name ?? '—' }}
                                @endif
                            </p>
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <div class="small text-body-secondary mb-2">Клиент: {{ $booking->customer?->name ?? '—' }}</div>
                            <div class="d-flex justify-content-end"><x-booking-actions :booking="$booking" /></div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-secondary mb-0">Няма резервации.</div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
