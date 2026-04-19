<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Trustpilot (TrustBox widget)
    |--------------------------------------------------------------------------
    |
    | В Trustpilot Business инструментите копирайте Business Unit ID и Template ID
    | за избрания виджет. Линкът към профила (profile_url) се показва във виджета и
    | като резервен бутон, ако виджетът е изключен.
    |
    */
    'trustpilot' => [
        'enabled' => env('TRUSTPILOT_ENABLED', false),
        'business_unit_id' => env('TRUSTPILOT_BUSINESS_UNIT_ID'),
        'template_id' => env('TRUSTPILOT_TEMPLATE_ID'),
        'locale' => env('TRUSTPILOT_LOCALE', 'bg-BG'),
        'profile_url' => env('TRUSTPILOT_PROFILE_URL'),
        /** Празно = без data-theme (напр. Review Collector). Друго: light / dark */
        'widget_theme' => env('TRUSTPILOT_WIDGET_THEME'),
        /** Review Collector често: 52px × 100% */
        'style_height' => env('TRUSTPILOT_STYLE_HEIGHT', '240px'),
        'style_width' => env('TRUSTPILOT_STYLE_WIDTH', '100%'),
        /** Някои TrustBox виджети изискват data-token от кода за вграждане */
        'widget_token' => env('TRUSTPILOT_WIDGET_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google (Business Profile / Maps)
    |--------------------------------------------------------------------------
    |
    | GOOGLE_REVIEW_URL — директна връзка „Оставете отзив“ или страница с отзиви.
    | GOOGLE_BUSINESS_MAP_URL — карта или списък с локация (по избор).
    |
    */
    'google' => [
        'review_url' => env('GOOGLE_REVIEW_URL'),
        'maps_url' => env('GOOGLE_BUSINESS_MAP_URL'),
    ],

];
