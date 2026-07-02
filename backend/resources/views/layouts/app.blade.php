<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('head')
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">

    <x-header />

    <main class="flex-1">
        @yield('content')
    </main>

    <x-footer />

    @stack('scripts')
</body>
</html>
