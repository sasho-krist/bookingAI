@extends('layouts.app')

@section('title', 'Работно време — '.$venue->name)

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

    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Начало</a></li>
                <li class="breadcrumb-item"><a href="{{ route('venues.show', $venue) }}">{{ $venue->name }}</a></li>
                <li class="breadcrumb-item active">Работно време</li>
            </ol>
        </nav>
        <h1 class="h3 mb-1">Работно време</h1>
        <p class="text-body-secondary small mb-0">
            Локация: <strong>{{ $venue->name }}</strong>
            · Часова зона за резервации: <strong>{{ $venue->timezone }}</strong>
        </p>
        <p class="small text-body-secondary mb-0 mt-2">
            Часовете са в локален формат (HH:MM). Един интервал на ден; нощни смени през полунощ не се поддържат в тази версия.
        </p>
    </div>

    <div class="row">
        <div class="col-xl-9">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('venues.business-hours.update', $venue) }}">
                        @csrf
                        @method('PUT')

                        @include('partials.business-hours-days-fields', ['inputIdPrefix' => 'day'])

                        <button type="submit" class="btn btn-primary">Запази</button>
                        <a href="{{ route('venues.show', $venue) }}" class="btn btn-link">Отказ</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
