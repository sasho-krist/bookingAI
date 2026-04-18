@extends('layouts.app')

@section('title', $business->name.' — '.config('app.name'))

@section('content')
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Начало</a></li>
                <li class="breadcrumb-item active">{{ $business->name }}</li>
            </ol>
        </nav>
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h1 class="h3 mb-1">{{ $business->name }}</h1>
                <p class="small mb-0 text-body">Тип: {{ $business->businessType->name }}</p>
                @if (filled($business->email) || filled($business->phone))
                    <p class="small mb-0 mt-2">
                        @if (filled($business->email))
                            <a href="mailto:{{ $business->email }}" class="link-body-emphasis text-decoration-none me-3"><i class="bi bi-envelope me-1"></i>{{ $business->email }}</a>
                        @endif
                        @if (filled($business->phone))
                            <a href="tel:{{ preg_replace('/\s+/', '', $business->phone) }}" class="link-body-emphasis text-decoration-none"><i class="bi bi-telephone me-1"></i>{{ $business->phone }}</a>
                        @endif
                    </p>
                @endif
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('businesses.edit', $business) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-pencil me-1"></i> Редактирай бизнес
                </a>
                <a href="{{ route('businesses.locations.create', $business) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Нова локация
                </a>
                <form method="POST" action="{{ route('businesses.destroy', $business) }}" class="d-inline" onsubmit="return confirm('Изтриване на бизнеса? Локациите няма да се изтрият, но ще се откачат от този бизнес.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i> Изтрий бизнес</button>
                </form>
            </div>
        </div>
        <p class="small mb-4">
            <a href="{{ route('venues.index') }}" class="link-body-emphasis">Всички локации</a>
            · <a href="{{ route('bookings.index') }}" class="link-body-emphasis">Резервации</a>
            · <a href="{{ route('bookings.create') }}" class="link-body-emphasis">Нова резервация</a>
        </p>
    </div>

    @php
        $bhLabels = ['mon' => 'Пн', 'tue' => 'Вт', 'wed' => 'Ср', 'thu' => 'Чт', 'fri' => 'Пт', 'sat' => 'Сб', 'sun' => 'Нд'];
    @endphp

    <div id="grafik" class="card shadow-sm mb-4" style="scroll-margin-top: 5rem;">
        <div class="card-header fw-semibold">График и работно време</div>
        <div class="card-body p-0">
            @forelse ($business->venues as $venue)
                @php $bh = $venue->business_hours; @endphp
                <div class="border-bottom p-3 @if($loop->last) border-bottom-0 @endif">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <span class="fw-semibold">{{ $venue->name }}</span>
                            <span class="text-body-secondary small ms-2">{{ $venue->timezone }}</span>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('venues.show', $venue) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-calendar3 me-1"></i> Календар
                            </a>
                            <a href="{{ route('venues.business-hours.edit', $venue) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-clock me-1"></i> Часове
                            </a>
                        </div>
                    </div>
                    @if ($venue->services->isNotEmpty())
                        <p class="small mb-2"><span class="text-body-secondary">Услуги:</span> {{ $venue->services->pluck('name')->implode(', ') }}</p>
                    @endif
                    @if (! is_array($bh) || count($bh) === 0)
                        <p class="small text-body-secondary mb-0">Няма зададено работно време за тази локация.</p>
                    @else
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($bhLabels as $key => $abbr)
                                @continue(! isset($bh[$key]))
                                @php $pair = $bh[$key]; @endphp
                                <span class="badge rounded-pill text-bg-light text-dark border">
                                    {{ $abbr }}
                                    @if (is_array($pair) && count($pair) >= 2)
                                        {{ $pair[0] }}–{{ $pair[1] }}
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-3 text-body-secondary small">Няма локации към този бизнес.</div>
            @endforelse
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header fw-semibold">Локации ({{ $business->venues->count() }})</div>
        <ul class="list-group list-group-flush">
            @forelse ($business->venues as $venue)
                <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <a href="{{ route('venues.show', $venue) }}" class="fw-semibold link-body-emphasis text-decoration-none">{{ $venue->name }}</a>
                        <span class="text-body-secondary small ms-2">{{ $venue->timezone }}</span>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="badge text-bg-light text-dark">{{ $venue->services_count }} услуги</span>
                        <span class="badge text-bg-light text-dark">{{ $venue->bookings_count }} резервации</span>
                        <a href="{{ route('venues.edit', $venue) }}" class="btn btn-sm btn-outline-secondary">Редактирай</a>
                        <form method="POST" action="{{ route('venues.destroy', $venue) }}" class="d-inline" onsubmit="return confirm('Изтриване на локацията и всички нейни услуги и резервации?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Изтрий</button>
                        </form>
                    </div>
                </li>
            @empty
                <li class="list-group-item text-body-secondary">Няма локации. <a href="{{ route('businesses.locations.create', $business) }}">Добавете локация</a>.</li>
            @endforelse
        </ul>
    </div>
@endsection
