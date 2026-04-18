@extends('layouts.app')

@section('title', $venue->name.' — '.config('app.name'))

@section('content')
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Начало</a></li>
                @if ($venue->business)
                    <li class="breadcrumb-item"><a href="{{ route('businesses.show', $venue->business) }}">{{ $venue->business->name }}</a></li>
                @endif
                <li class="breadcrumb-item"><a href="{{ route('venues.index') }}">Локации</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $venue->name }}</li>
            </ol>
        </nav>
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h1 class="h3 mb-1">{{ $venue->name }}</h1>
                @if ($venue->business)
                    <p class="text-body-secondary mb-0 small">
                        Бизнес: <a href="{{ route('businesses.show', $venue->business) }}" class="link-secondary fw-semibold">{{ $venue->business->name }}</a>
                        · Тип: {{ $venue->business->businessType->name }}
                    </p>
                @endif
                <p class="small text-body-secondary mb-0 mt-1">Часова зона: {{ $venue->timezone }} · Категория локация: {{ $venue->type }}</p>
                <p class="small mb-0 mt-2">
                    <a href="{{ route('bookings.index') }}">Всички резервации</a>
                    · <a href="{{ route('venues.business-hours.edit', $venue) }}">Работно време</a>
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('venues.edit', $venue) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-pencil me-1"></i> Редактирай локация
                </a>
                <a href="{{ route('bookings.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Нова резервация
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="fw-semibold">Услуги</span>
            <a href="{{ route('venues.services.create', $venue) }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-plus-lg me-1"></i> Добави услуга
            </a>
        </div>
        @if ($venue->services->isEmpty())
            <div class="card-body text-body-secondary small">
                Няма добавени услуги за тази локация. Добавете поне една, за да можете да правите резервации с избор на услуга.
            </div>
        @else
            <ul class="list-group list-group-flush">
                @foreach ($venue->services as $service)
                    <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <span class="fw-semibold">{{ $service->name }}</span>
                            <span class="text-body-secondary small ms-2">{{ $service->duration_minutes }} мин.</span>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('venues.services.edit', [$venue, $service]) }}" class="btn btn-outline-secondary">Редакция</a>
                            <form method="POST" action="{{ route('venues.services.destroy', [$venue, $service]) }}" class="d-inline" onsubmit="return confirm('Изтриване на услугата и свързаните резервации?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">Изтрий</button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    @php
        $bh = $venue->business_hours;
        $bhLabels = ['mon' => 'Пн', 'tue' => 'Вт', 'wed' => 'Ср', 'thu' => 'Чт', 'fri' => 'Пт', 'sat' => 'Сб', 'sun' => 'Нд'];
    @endphp
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="fw-semibold">Работно време</span>
            <a href="{{ route('venues.business-hours.edit', $venue) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-clock me-1"></i> {{ is_array($bh) && count($bh) ? 'Редактирай' : 'Задай часове' }}
            </a>
        </div>
        @if (! is_array($bh) || count($bh) === 0)
            <div class="card-body text-body-secondary small mb-0">
                Няма зададено работно време. Без него AI препоръките за слотове и натовареност разчитат само на общ контекст — задайте интервали поне за работните дни.
            </div>
        @else
            <ul class="list-group list-group-flush">
                @foreach ($bhLabels as $key => $abbr)
                    @continue(! isset($bh[$key]))
                    @php $pair = $bh[$key]; @endphp
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ $abbr }}</span>
                        @if (is_array($pair) && count($pair) >= 2)
                            <span class="text-body-secondary small">{{ $pair[0] }} – {{ $pair[1] }}</span>
                        @else
                            <span class="text-body-secondary small">—</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="fw-semibold">Календар — {{ $monthLabel }}</span>
            <div class="btn-group btn-group-sm">
                <a href="{{ route('venues.show', ['venue' => $venue, 'month' => $prevMonth]) }}" class="btn btn-outline-secondary">&laquo; Предишен</a>
                <a href="{{ route('venues.show', $venue) }}" class="btn btn-outline-secondary">Днес</a>
                <a href="{{ route('venues.show', ['venue' => $venue, 'month' => $nextMonth]) }}" class="btn btn-outline-secondary">Следващ &raquo;</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered text-center mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Пн</th>
                            <th>Вт</th>
                            <th>Ср</th>
                            <th>Чт</th>
                            <th>Пт</th>
                            <th>Сб</th>
                            <th>Нд</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($calendarWeeks as $week)
                            <tr>
                                @foreach ($week as $day)
                                    <td class="{{ $day['inMonth'] ? '' : 'text-body-tertiary bg-body-secondary' }} {{ $day['count'] > 0 ? 'table-warning' : '' }}" style="min-width: 4.5rem; height: 4rem;">
                                        <div class="fw-semibold">{{ $day['dayNum'] }}</div>
                                        @if ($day['inMonth'] && $day['count'] > 0)
                                            <span class="badge text-bg-primary">{{ $day['count'] }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

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
    <div class="card shadow-sm">
        <div class="card-header fw-semibold">Резервации за месеца ({{ $bookings->count() }})</div>
        <div class="list-group list-group-flush">
            @forelse ($bookings as $booking)
                <div class="list-group-item">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                        <div>
                            <span class="fw-semibold">{{ $booking->starts_at->timezone($venue->timezone)->format('d.m.Y H:i') }}</span>
                            <span class="text-body-secondary"> — {{ $booking->ends_at->timezone($venue->timezone)->format('H:i') }}</span>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="badge text-bg-{{ $statusBadgeClass($booking->status) }}">{{ $statusLabel($booking->status) }}</span>
                            <x-booking-actions :booking="$booking" />
                        </div>
                    </div>
                    <div class="small text-body-secondary mt-1">
                        {{ $booking->service?->name ?? '—' }}
                        @if ($booking->customer)
                            · {{ $booking->customer->name }}
                        @endif
                    </div>
                </div>
            @empty
                <div class="list-group-item text-body-secondary">Няма резервации за този месец.</div>
            @endforelse
        </div>
    </div>
@endsection
