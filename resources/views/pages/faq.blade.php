@extends('layouts.landing')

@section('title', 'Често задавани въпроси — '.config('app.name'))

@section('content')
    <div class="container py-5" style="max-width: 46rem;">
        <h1 class="h3 mb-4">Често задавани въпроси (ЧЗВ)</h1>
        <p class="text-body-secondary small mb-4">Кратки отговори за типични сценарии. За правни въпроси вижте <a href="{{ route('legal.privacy') }}">политиката за поверителност</a> и <a href="{{ route('legal.terms') }}">условията за ползване</a>.</p>

        <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">
                        Как да започна?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                    <div class="accordion-body small text-body-secondary">
                        Регистрирайте се с имейл и парола, влезте в таблото и следвайте <strong>Първоначална настройка</strong> или създайте бизнес, локация и услуги ръчно от менюто.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
                        Работи ли AI без OpenAI ключ?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body small text-body-secondary">
                        AI препоръките и чатът изискват валиден API ключ и настройки в средата (напр. <code>OPENAI_API_KEY</code> в <code>.env</code>). Останалата част от приложението работи без това.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
                        Как работи API достъпът?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body small text-body-secondary">
                        Издайте токен чрез <code>POST /api/v1/auth/token</code> с имейл и парола, след което изпращайте <code>Authorization: Bearer …</code> към маршрутите под <code>/api/v1</code>. Пълен списък е в страницата <strong>API документация</strong> след вход в приложението.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">
                        Къде са данните ми?
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body small text-body-secondary">
                        Данните се съхраняват в базата, конфигурирана за вашата инсталация (напр. MySQL/SQLite). Прегледайте политиката за поверителност и мерките за сигурност на вашия хостинг.
                    </div>
                </div>
            </div>
        </div>

        <p class="mt-4 mb-0 small"><a href="{{ route('landing') }}" class="link-body-emphasis">← Начална страница</a></p>
    </div>
@endsection
