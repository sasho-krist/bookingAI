@extends('layouts.app')

@section('title', 'REST API — '.config('app.name'))

@section('content')
    <div class="mx-auto" style="max-width: 58rem;">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h1 class="h3 mb-2">REST API</h1>
                <p class="text-body-secondary small mb-0">Всички маршрути са под <code class="user-select-all">/api/v1</code>. Връщат JSON. Неаутентикираните заявки получават <code>401</code> (освен издаване на токен).</p>
            </div>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">Към началото</a>
        </div>

        <p class="small text-body-secondary mb-4">Базов адрес: <code class="user-select-all">{{ $baseUrl }}</code></p>

        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">Автентикация (Laravel Sanctum)</div>
            <div class="card-body">
                <p class="mb-2">1) Вземете токен с имейл и парола на акаунта:</p>
                <pre class="bg-body-tertiary border rounded p-3 small overflow-x-auto mb-3"><code>POST {{ $baseUrl }}/auth/token
Content-Type: application/json
Accept: application/json

{
  "email": "ваш@имейл",
  "password": "••••••••",
  "device_name": "мобилно-приложение"
}</code></pre>
                <p class="mb-2">Отговор: <code>token</code> (низ) — изпращайте го във всяка защитена заявка:</p>
                <pre class="bg-body-tertiary border rounded p-3 small overflow-x-auto mb-0"><code>Authorization: Bearer &lt;token&gt;
Accept: application/json
Content-Type: application/json</code></pre>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">Сесия / токен</div>
            <ul class="list-group list-group-flush small">
                <li class="list-group-item"><code>POST /auth/token</code> — публичен, връща токен</li>
                <li class="list-group-item"><code>POST /auth/logout</code> — анулира текущия токен (с <code>Authorization</code>)</li>
            </ul>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">Типове бизнес</div>
            <ul class="list-group list-group-flush small">
                <li class="list-group-item"><code>GET /business-types</code> — списък</li>
                <li class="list-group-item"><code>POST /business-types</code> — създаване: <code>name</code></li>
                <li class="list-group-item"><code>PUT /business-types/{id}</code> — <code>name</code></li>
                <li class="list-group-item"><code>DELETE /business-types/{id}</code> — ако няма бизнеси с този тип</li>
            </ul>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">Бизнеси</div>
            <ul class="list-group list-group-flush small">
                <li class="list-group-item"><code>GET /businesses</code> — вашите достъпни бизнеси (+ брой локации)</li>
                <li class="list-group-item"><code>POST /businesses</code> — <code>name</code>, <code>business_type_id</code>, по желание <code>email</code>, <code>phone</code></li>
                <li class="list-group-item"><code>GET /businesses/{id}</code> — детайли с локации</li>
                <li class="list-group-item"><code>PUT /businesses/{id}</code> — актуализация</li>
                <li class="list-group-item"><code>DELETE /businesses/{id}</code> — изтриване на бизнеса</li>
                <li class="list-group-item"><code>POST /businesses/{id}/venues</code> — нова локация: <code>name</code>, <code>timezone</code>, по желание <code>type</code></li>
            </ul>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">Локации (venues)</div>
            <ul class="list-group list-group-flush small mb-3">
                <li class="list-group-item"><code>GET /venues</code> — локации, до които потребителят има достъп</li>
                <li class="list-group-item"><code>POST /venues</code> — <code>name</code>, по желание <code>type</code>, <code>timezone</code>, <code>business_hours</code>, <code>business_id</code></li>
                <li class="list-group-item"><code>GET /venues/{id}</code></li>
                <li class="list-group-item"><code>PUT /venues/{id}</code> — <code>name</code>, <code>timezone</code>, по желание <code>type</code></li>
                <li class="list-group-item"><code>DELETE /venues/{id}</code></li>
                <li class="list-group-item"><code>PUT /venues/{id}/business-hours</code> — работно време (като уеб формата): обект <code>days</code> с ключове <code>mon</code> … <code>sun</code>, всеки с <code>active</code> (bool), <code>open</code>, <code>close</code> във формат <code>H:i</code>.</li>
            </ul>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">Услуги</div>
            <ul class="list-group list-group-flush small">
                <li class="list-group-item"><code>GET /venues/{venueId}/services</code></li>
                <li class="list-group-item"><code>POST /venues/{venueId}/services</code> — <code>name</code>, <code>duration_minutes</code></li>
                <li class="list-group-item"><code>GET /venues/{venueId}/services/{serviceId}</code></li>
                <li class="list-group-item"><code>PUT /venues/{venueId}/services/{serviceId}</code></li>
                <li class="list-group-item"><code>DELETE /venues/{venueId}/services/{serviceId}</code></li>
            </ul>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">Клиенти</div>
            <ul class="list-group list-group-flush small">
                <li class="list-group-item"><code>GET /customers</code> — до 500 записа</li>
                <li class="list-group-item"><code>POST /customers</code> — <code>name</code>, по желание <code>email</code>, <code>phone</code></li>
                <li class="list-group-item"><code>PUT /customers/{id}</code> — актуализация на полета</li>
            </ul>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">Резервации</div>
            <ul class="list-group list-group-flush small">
                <li class="list-group-item"><code>GET /bookings</code> — по желание филтри <code>from</code>, <code>to</code>, <code>venue_id</code>, <code>limit</code></li>
                <li class="list-group-item"><code>GET /venues/{venueId}/bookings</code> — за една локация (+ <code>from</code>/<code>to</code>)</li>
                <li class="list-group-item"><code>POST /venues/{venueId}/bookings</code> — <code>service_id</code>, <code>starts_at</code>, по желание <code>customer_id</code>, <code>status</code>, <code>notes</code></li>
                <li class="list-group-item"><code>PATCH /bookings/{id}</code> — частично: <code>starts_at</code>, <code>status</code>, <code>attended</code>, <code>notes</code></li>
                <li class="list-group-item"><code>DELETE /bookings/{id}</code></li>
            </ul>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">AI (изисква настроен OpenAI в .env)</div>
            <ul class="list-group list-group-flush small mb-0">
                <li class="list-group-item"><code>POST /ai/slots</code> — тяло: <code>venue_id</code>, по желание <code>service_id</code>, <code>preferred_date</code>, …</li>
                <li class="list-group-item"><code>POST /ai/load-forecast</code> — <code>venue_id</code>, …</li>
                <li class="list-group-item"><code>POST /ai/reschedule</code> — <code>venue_id</code>, <code>problem</code>, …</li>
                <li class="list-group-item"><code>POST /ai/chat</code> — <code>venue_id</code>, <code>messages[]</code></li>
                <li class="list-group-item"><code>POST /ai/no-show</code> — <code>booking_id</code>, …</li>
            </ul>
            <div class="card-footer small text-body-secondary">За локации и резервации са приложени същите правила за достъп като уеб приложението.</div>
        </div>

        <div class="alert alert-info small mb-0">
            Отговори обикновено са във вида <code>{ "data": … }</code>. При грешка от валидация — <code>422</code> с подробности в <code>errors</code>.
        </div>
    </div>
@endsection
