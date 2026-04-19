@extends('layouts.landing')

@section('title', 'Оптимизирайте резервациите си с BookingAI')

@section('meta_description', 'BookingAI е иновативно уеб приложение за управление на резервации с Laravel 13 и PHP 8.3+. Подобрете клиентското преживяване с AI решения.')

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'WebSite',
            '@id' => url('/').'#website',
            'url' => url('/'),
            'name' => config('app.name'),
            'inLanguage' => 'bg-BG',
            'description' => config('seo.description'),
        ],
        [
            '@type' => 'SoftwareApplication',
            'name' => config('app.name'),
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Уеб браузър',
        ],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@section('content')
    <section class="py-5 bg-body border-bottom">
        <div class="container py-lg-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <p class="text-primary-emphasis small fw-semibold mb-2"><i class="bi bi-stars me-1"></i> BookingAI резервации · Laravel 13 · PHP 8.3+</p>
                    <h1 class="display-6 fw-bold mb-3">Оптимизирайте резервациите с BookingAI за салони, студия и услуги на място</h1>
                    <p class="lead text-body-secondary mb-4">
                        Уеб приложение за управление на резервации с няколко бизнеса и локации (заведения): услуги с продължителност, работно време по дни и календар. AI препоръки за свободни часове, прогноза на натовареност и помощ при пренасочване — при конфигуриран OpenAI ключ в средата.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-4"><i class="bi bi-person-plus me-2"></i>Създай акаунт</a>
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg">Вход</a>
                        @else
                            <a href="{{ route('home') }}" class="btn btn-primary btn-lg px-4"><i class="bi bi-speedometer2 me-2"></i>Към таблото</a>
                            <a href="{{ route('setup.index') }}" class="btn btn-outline-secondary btn-lg">Първоначална настройка</a>
                        @endguest
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card shadow-sm border-0 bg-body-tertiary">
                        <div class="card-body p-4">
                            <h2 class="h6 mb-3">След регистрация</h2>
                            <ul class="list-unstyled mb-0 small text-body-secondary">
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> стъпков съветник: бизнес → локация → услуга → работно време</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> резервации, клиенти и календар по локации</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> REST API с токен за интеграции и автоматизации</li>
                                <li class="mb-0"><i class="bi bi-check-circle text-success me-2"></i> документация за API в приложението (след вход)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="py-5 scroll-margin-top">
        <div class="container">
            <h2 class="h3 fw-bold mb-2 text-center">Функции</h2>
            <p class="text-body-secondary text-center mb-5 mx-auto" style="max-width: 40rem">Всичко необходимо да организирате записани часове и да подобрите графика с данни и AI.</p>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary-emphasis p-3 mb-3"><i class="bi bi-building fs-4"></i></div>
                            <h3 class="h5">Бизнеси и локации</h3>
                            <p class="small text-body-secondary mb-0">Няколко бизнеса, локации с часова зона, работно време и услуги с продължителност.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary-emphasis p-3 mb-3"><i class="bi bi-calendar-check fs-4"></i></div>
                            <h3 class="h5">Резервации</h3>
                            <p class="small text-body-secondary mb-0">Създаване и проследяване на резервации по услуга, статус и клиент.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary-emphasis p-3 mb-3"><i class="bi bi-stars fs-4"></i></div>
                            <h3 class="h5">AI помощник</h3>
                            <p class="small text-body-secondary mb-0">Препоръки за слотове, прогноза на натовареност и чат контекст по локация (при наличен API ключ).</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary-emphasis p-3 mb-3"><i class="bi bi-people fs-4"></i></div>
                            <h3 class="h5">Клиенти</h3>
                            <p class="small text-body-secondary mb-0">Регистър на клиенти за връзка с резервации и история.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary-emphasis p-3 mb-3"><i class="bi bi-clock fs-4"></i></div>
                            <h3 class="h5">Работно време</h3>
                            <p class="small text-body-secondary mb-0">График по дни от седмицата за реалистични препоръки и календар.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary-emphasis p-3 mb-3"><i class="bi bi-shield-lock fs-4"></i></div>
                            <h3 class="h5">Достъп</h3>
                            <p class="small text-body-secondary mb-0">Акаунти с вход и защитен API — вижте политиката за поверителност и условията.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="venues" class="py-5 bg-body-tertiary border-top border-bottom scroll-margin-top">
        <div class="container">
            <h2 class="h3 fw-bold mb-2 text-center">Локации и заведения в един акаунт</h2>
            <p class="text-body-secondary text-center mb-5 mx-auto" style="max-width: 42rem">
                Ясно разделение между бизнеси и локации улеснява собствениците на няколко обекта: всеки обект има часова зона, работно време и списък услуги — клиентите ви виждат организиран график, а екипът следва статусите на резервациите от таблото.
            </p>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h3 class="h6 fw-semibold"><i class="bi bi-shop text-primary me-2"></i>Много локации</h3>
                            <p class="small text-body-secondary mb-0">Добавяйте нови заведения или клонове, управлявайте ги централно и превключвайте контекста между локации без объркване.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h3 class="h6 fw-semibold"><i class="bi bi-card-list text-primary me-2"></i>Услуги и продължителност</h3>
                            <p class="small text-body-secondary mb-0">Дефинирайте услуги с времетраене и графика, за да предлагате реалистични свободни часове и да намалите двойните записвания.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h3 class="h6 fw-semibold"><i class="bi bi-funnel text-primary me-2"></i>Филтри и преглед</h3>
                            <p class="small text-body-secondary mb-0">В приложението филтрирайте резервации по локация, услуга и статус; на началната страница намерете бързо ключовите функции и API.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="trust" class="py-5 scroll-margin-top">
        <div class="container">
            <h2 class="h3 fw-bold mb-2 text-center">Доверие, сигурност и интерфейс на български</h2>
            <p class="text-body-secondary text-center mb-5 mx-auto" style="max-width: 40rem">
                Прозрачността расте с ясни политики и удобен интерфейс — тъмна и светла тема, навигация на български и съветник за първоначална настройка.
            </p>
            <div class="row g-4 justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border shadow-sm">
                        <div class="card-body">
                            <p class="small text-body-secondary mb-2"><i class="bi bi-quote text-primary fs-5"></i></p>
                            <p class="mb-2 fw-semibold">По-малко хаос в графика</p>
                            <p class="small text-body-secondary mb-0">Централен изглед на резервациите по локации помага екипът да реагира навреме и да намали конфликтите в часа.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border shadow-sm">
                        <div class="card-body">
                            <p class="small text-body-secondary mb-2"><i class="bi bi-quote text-primary fs-5"></i></p>
                            <p class="mb-2 fw-semibold">Интеграции чрез API</p>
                            <p class="small text-body-secondary mb-0">REST API с Laravel Sanctum и Bearer токен позволява автоматизации и връзка с външни системи, когато бизнесът ви порасне.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border shadow-sm">
                        <div class="card-body">
                            <p class="small text-body-secondary mb-2"><i class="bi bi-quote text-primary fs-5"></i></p>
                            <p class="mb-2 fw-semibold">Легална основа</p>
                            <p class="small text-body-secondary mb-3">Запознайте се с <a href="{{ route('legal.privacy') }}" class="fw-semibold">политиката за поверителност</a> и <a href="{{ route('legal.terms') }}" class="fw-semibold">условията за ползване</a> преди масови резервации.</p>
                            <a href="{{ route('legal.faq') }}" class="small fw-semibold">Често задавани въпроси →</a>
                        </div>
                    </div>
                </div>
            </div>
            @include('partials.reviews-integration')
        </div>
    </section>

    <section id="api" class="py-5 bg-body-tertiary border-top border-bottom scroll-margin-top">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <h2 class="h3 fw-bold mb-3 text-center">API за интеграции</h2>
                    <p class="text-body-secondary text-center mb-4">
                        REST API под префикс <code class="user-select-all">{{ url('/api/v1') }}</code> — автентикация с персонален токен (Laravel Sanctum): взимате токен с имейл и парола, после изпращате <code>Authorization: Bearer …</code>.
                    </p>
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <ul class="small text-body-secondary mb-3 ps-3">
                                <li class="mb-2">типове бизнес, бизнеси, локации, услуги, клиенти, резервации</li>
                                <li class="mb-2">AI крайни точки за слотове, натовареност, пренасочване и др.</li>
                                <li class="mb-0">пълен списък с методи и примери — страницата <strong>API документация</strong> в приложението (след вход)</li>
                            </ul>
                            @guest
                                <p class="small mb-0"><a href="{{ route('register') }}" class="fw-semibold">Регистрирайте се</a>, за да получите достъп до таблото и към документацията за разработчици.</p>
                            @else
                                <a href="{{ route('api.docs') }}" class="btn btn-primary"><i class="bi bi-code-slash me-2"></i>Отвори API документацията</a>
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container text-center">
            <h2 class="h4 fw-bold mb-3">Готови ли сте?</h2>
            <p class="text-body-secondary mb-4 mx-auto" style="max-width: 28rem">Създайте профил или влезте, за да управлявате резервациите от едно място.</p>
            @guest
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Регистрация</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg">Вход</a>
                </div>
            @else
                <a href="{{ route('home') }}" class="btn btn-primary btn-lg">Към таблото</a>
            @endguest
        </div>
    </section>
@endsection
