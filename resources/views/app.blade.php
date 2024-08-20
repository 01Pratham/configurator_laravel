<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} | {{ modify(Route::currentRouteName()) }} </title>
    <link rel="shortcut icon" href="{{ asset('assets/dist/img/logo.png') }}" type="image/x-icon">
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    @include('inc.CSS')
    @include('inc.JS')
</head>

<body class="sidebar-mini layout-fixed sidebar-collapse except" data-new-gr-c-s-check-loaded="14.1111.0"
    data-gr-ext-installed style="height: auto;">
    <div id="app" class="except">
        @yield('content')
    </div>
</body>

</html>
