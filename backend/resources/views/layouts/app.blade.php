<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    @stack('head')
</head>
<body class="layouts_app_body">

    <x-header />

    <main class="layouts_app_main">
        @yield('content')
    </main>

    <x-footer />

    @stack('scripts')
</body>
</html>
