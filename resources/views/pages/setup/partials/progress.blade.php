@props(['step' => 1])

@php
    $labels = ['Бизнес', 'Локация', 'Услуга', 'Работно време'];
@endphp

<div class="card shadow-sm mb-4">
    <div class="card-body py-3">
        <div class="row row-cols-2 row-cols-lg-4 g-2 small text-center">
            @foreach ($labels as $i => $label)
                @php $n = $i + 1; @endphp
                <div class="col">
                    <div class="rounded border p-2 h-100 {{ $step >= $n ? 'border-primary bg-primary-subtle' : 'border-secondary-subtle text-body-secondary' }}">
                        <span class="fw-semibold">{{ $n }}.</span> {{ $label }}
                        @if ($step > $n)
                            <span class="d-block text-success small mt-1"><i class="bi bi-check-circle"></i></span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top flex-wrap gap-2">
            <span class="text-body-secondary small">Стъпка {{ $step }} от 4</span>
            <div class="d-flex gap-2">
                <a href="{{ route('setup.index') }}" class="btn btn-sm btn-outline-secondary">Към текущата стъпка</a>
                <form method="POST" action="{{ route('setup.reset') }}" class="d-inline" onsubmit="return confirm('Нулиране на прогреса и започване отначало?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">Отначало</button>
                </form>
            </div>
        </div>
    </div>
</div>
