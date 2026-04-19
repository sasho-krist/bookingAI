@php
    $showOg = $showOg ?? false;
@endphp
<meta name="description" content="@yield('meta_description', config('seo.description'))">
<meta name="keywords" content="{{ config('seo.keywords') }}">
<meta name="author" content="{{ config('seo.author') }}">
<link rel="canonical" href="{{ url()->current() }}">
@if ($showOg)
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', config('app.name'))">
    <meta property="og:description" content="@yield('meta_description', config('seo.description'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="bg_BG">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="@yield('title', config('app.name'))">
    <meta name="twitter:description" content="@yield('meta_description', config('seo.description'))">
@endif
