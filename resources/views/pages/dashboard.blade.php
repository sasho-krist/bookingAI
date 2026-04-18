@extends('layouts.app')

@section('title', 'Начало — '.config('app.name'))

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Начало</h1>
            <p class="text-body-secondary mb-0">Здравейте, <strong>{{ Auth::user()->name }}</strong>. Преглед на локациите ви — клик върху карта отваря страницата на бизнеса с график и часове.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('businesses.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-building me-1"></i> Бизнеси
            </a>
            <a href="{{ route('venues.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-shop me-1"></i> Локации
            </a>
            <a href="{{ route('bookings.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Нова резервация
            </a>
        </div>
    </div>

    <div data-show-when="table">
        <div class="card shadow-sm">
            <div class="card-header">Локации (обобщение)</div>
            <div class="table-responsive mb-0 rounded-bottom">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Локация</th>
                            <th>Бизнес</th>
                            <th>Тип</th>
                            <th>Услуги</th>
                            <th>Часова зона</th>
                            <th class="text-end">Резервации</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($venues as $venue)
                            @php
                                $biz = $venue->business;
                                $href = $biz ? route('businesses.show', $biz).'#grafik' : route('venues.show', $venue);
                                $svcCount = $venue->services->count();
                                $svcPreview = $venue->services->take(3)->pluck('name')->implode(', ');
                                if ($svcCount > 3) {
                                    $svcPreview .= ' (+'.($svcCount - 3).')';
                                }
                            @endphp
                            <tr class="position-relative">
                                <td>
                                    <a href="{{ $href }}" class="fw-semibold link-body-emphasis text-decoration-none">{{ $venue->name }}</a>
                                </td>
                                <td>
                                    @if ($biz)
                                        <a href="{{ $href }}" class="link-secondary text-decoration-none d-inline-block">{{ $biz->name }}</a>
                                        @if (filled($biz->email) || filled($biz->phone))
                                            <div class="small text-body-secondary mt-1">
                                                @if (filled($biz->email))
                                                    <span class="d-block text-truncate" style="max-width: 14rem;"><i class="bi bi-envelope me-1"></i>{{ $biz->email }}</span>
                                                @endif
                                                @if (filled($biz->phone))
                                                    <span class="d-block"><i class="bi bi-telephone me-1"></i>{{ $biz->phone }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-body-secondary">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($biz)
                                        <span class="badge text-bg-secondary">{{ $biz->businessType->name }}</span>
                                    @else
                                        <span class="text-body-secondary">—</span>
                                    @endif
                                </td>
                                <td class="small">
                                    @if ($svcCount === 0)
                                        <span class="text-body-secondary">Няма</span>
                                    @else
                                        {{ $svcPreview ?: '—' }}
                                    @endif
                                </td>
                                <td class="text-body-secondary small">{{ $venue->timezone }}</td>
                                <td class="text-end">{{ $venue->bookings_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-body-secondary py-4">Няма локации. Използвайте <a href="{{ route('setup.index') }}">първоначална настройка</a> или добавете локация към бизнес.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div data-show-when="card" class="d-none">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
            @forelse ($venues as $venue)
                @php
                    $biz = $venue->business;
                    $href = $biz ? route('businesses.show', $biz).'#grafik' : route('venues.show', $venue);
                    $svcCount = $venue->services->count();
                    $svcPreview = $venue->services->take(4)->pluck('name')->implode(', ');
                    if ($svcCount > 4) {
                        $svcPreview .= '…';
                    }
                @endphp
                <div class="col">
                    <a href="{{ $href }}" class="text-decoration-none text-reset d-block h-100">
                        <div class="card h-100 shadow-sm border-secondary-subtle">
                            <div class="card-body d-flex flex-column">
                                @if ($biz)
                                    <p class="small text-primary-emphasis fw-semibold mb-1">{{ $biz->name }}</p>
                                    <span class="badge align-self-start text-bg-secondary mb-2">{{ $biz->businessType->name }}</span>
                                    @if (filled($biz->email) || filled($biz->phone))
                                        <div class="small text-body-secondary mb-2">
                                            @if (filled($biz->email))
                                                <span class="d-block text-truncate" title="{{ $biz->email }}"><i class="bi bi-envelope me-1"></i>{{ $biz->email }}</span>
                                            @endif
                                            @if (filled($biz->phone))
                                                <span class="d-block"><i class="bi bi-telephone me-1"></i>{{ $biz->phone }}</span>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                                <h2 class="h6 card-title mb-2">{{ $venue->name }}</h2>
                                <p class="card-text small text-body-secondary mb-2 flex-grow-1">
                                    @if ($svcCount === 0)
                                        <span class="text-body-secondary">Няма услуги</span>
                                    @else
                                        <span class="text-body-secondary">Услуги:</span> {{ $svcPreview }}
                                    @endif
                                </p>
                                <p class="card-text small text-body-secondary mb-0">{{ $venue->timezone }}</p>
                                <span class="badge align-self-start text-bg-light text-dark mt-2">{{ $venue->type }}</span>
                            </div>
                            <div class="card-footer bg-transparent border-top-0 text-body-secondary small pt-0">
                                Резервации: <strong>{{ $venue->bookings_count }}</strong>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-secondary mb-0">Няма локации за показване.</div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
