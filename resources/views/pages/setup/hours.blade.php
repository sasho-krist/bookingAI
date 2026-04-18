@extends('layouts.app')

@section('title', 'Първоначална настройка — работно време')

@section('content')
    @php
        $dayLabels = [
            'mon' => 'Понеделник',
            'tue' => 'Вторник',
            'wed' => 'Сряда',
            'thu' => 'Четвъртък',
            'fri' => 'Петък',
            'sat' => 'Събота',
            'sun' => 'Неделя',
        ];
    @endphp

    @include('pages.setup.partials.progress', ['step' => $step])

    <div class="mb-3">
        <h1 class="h4 mb-1">Стъпка 4: Работно време</h1>
        <p class="text-body-secondary small mb-0">
            Локация: <strong>{{ $venue->name }}</strong>
            · Часова зона: <strong>{{ $venue->timezone }}</strong>
        </p>
        <p class="small text-body-secondary mb-0 mt-2">
            Часовете са локални (HH:MM). Един интервал на ден; нощни смени през полунощ не се поддържат в тази версия.
        </p>
    </div>

    <div class="row">
        <div class="col-xl-9">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('setup.storeHours') }}">
                        @csrf

                        @include('partials.business-hours-days-fields', ['inputIdPrefix' => 'setup'])

                        <button type="submit" class="btn btn-primary">Завърши настройката</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
