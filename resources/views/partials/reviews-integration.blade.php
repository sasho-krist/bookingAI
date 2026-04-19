@php
    $tp = config('reviews.trustpilot');
    $google = config('reviews.google');
    $trustpilotWidget = ($tp['enabled'] ?? false)
        && filled($tp['business_unit_id'] ?? null)
        && filled($tp['template_id'] ?? null);
    $trustpilotFallbackLink = filled($tp['profile_url'] ?? null);
    $googleReview = filled($google['review_url'] ?? null);
    $googleMaps = filled($google['maps_url'] ?? null);
    $tpHref = $trustpilotFallbackLink ? $tp['profile_url'] : 'https://www.trustpilot.com/';
@endphp

@if ($trustpilotWidget || $trustpilotFallbackLink || $googleReview || $googleMaps)
    <div id="reviews" class="row justify-content-center mt-5 pt-4 border-top border-secondary-subtle scroll-margin-top">
        <div class="col-lg-10">
            <h3 class="h5 text-center mb-2">Отзиви в Trustpilot и Google</h3>
            <p class="text-body-secondary small text-center mb-4 mx-auto" style="max-width: 36rem">
                Отзивите се оставят в Trustpilot или Google след натискане на бутона — не се въвежда текст директно в тази страница. Ако ползвате тестов адрес (<code class="user-select-all">localhost</code>), виджетът понякога остава празен; тествайте на регистрирания домейн (напр. <code class="user-select-all">sasho-dev.com</code>).
            </p>

            @if ($trustpilotWidget)
                @once
                    @push('head')
                        <script type="text/javascript" src="https://widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async></script>
                    @endpush
                @endonce
                <div class="trustpilot-widget mb-3 mx-auto"
                     data-locale="{{ $tp['locale'] }}"
                     data-template-id="{{ $tp['template_id'] }}"
                     data-businessunit-id="{{ $tp['business_unit_id'] }}"
                     data-style-height="{{ $tp['style_height'] }}"
                     data-style-width="{{ $tp['style_width'] }}"
                     @if(filled($tp['widget_theme'] ?? null)) data-theme="{{ $tp['widget_theme'] }}" @endif
                     @if(filled($tp['widget_token'] ?? null)) data-token="{{ $tp['widget_token'] }}" @endif
                    >
                    <a href="{{ $tpHref }}" target="_blank" rel="noopener noreferrer">Trustpilot</a>
                </div>
                @if ($trustpilotFallbackLink)
                    <p class="text-center small text-body-secondary mb-2">
                        <a href="{{ $tp['profile_url'] }}" class="link-body-emphasis" target="_blank" rel="noopener noreferrer">Оставете отзив в Trustpilot (директна връзка)</a>
                    </p>
                @endif
            @elseif ($trustpilotFallbackLink)
                <div class="text-center mb-4">
                    <a href="{{ $tp['profile_url'] }}" class="btn btn-outline-secondary" target="_blank" rel="noopener noreferrer">
                        <i class="bi bi-star me-2"></i>Вижте ни в Trustpilot
                    </a>
                </div>
            @endif

            <div class="d-flex flex-wrap gap-2 justify-content-center align-items-center">
                @if ($googleReview)
                    <a href="{{ $google['review_url'] }}" class="btn btn-outline-primary" target="_blank" rel="noopener noreferrer">
                        <i class="bi bi-google me-2"></i>Отзив в Google
                    </a>
                @endif
                @if ($googleMaps)
                    <a href="{{ $google['maps_url'] }}" class="btn btn-outline-secondary" target="_blank" rel="noopener noreferrer">
                        <i class="bi bi-geo-alt me-2"></i>Google Maps
                    </a>
                @endif
            </div>
        </div>
    </div>
@endif
