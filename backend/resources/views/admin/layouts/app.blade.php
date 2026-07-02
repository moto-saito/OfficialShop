<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '管理画面') | {{ config('app.name') }} Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex">

    {{-- サイドバー --}}
    <aside class="w-56 bg-gray-900 text-gray-300 flex flex-col flex-shrink-0 min-h-screen">
        <div class="px-6 py-5 border-b border-gray-700">
            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Admin</p>
            <a href="{{ route('admin.products.index') }}" class="text-white font-bold text-lg leading-tight">{{ config('app.name') }}</a>
        </div>
        <nav class="flex-1 py-4">
            <p class="px-6 py-2 text-xs text-gray-500 uppercase tracking-wider">コンテンツ</p>

            <a href="{{ route('admin.products.index') }}"
               class="flex items-center gap-3 px-6 py-2.5 text-sm hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('admin.products.*') ? 'bg-gray-800 text-white' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
                商品管理
            </a>

            <a href="{{ route('admin.news.index') }}"
               class="flex items-center gap-3 px-6 py-2.5 text-sm hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('admin.news.*') ? 'bg-gray-800 text-white' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6"/>
                </svg>
                お知らせ管理
            </a>
        </nav>
        <div class="px-6 py-4 border-t border-gray-700 space-y-3">
            <a href="/" target="_blank" class="text-xs text-gray-500 hover:text-white transition flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                サイトを見る
            </a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="text-xs text-gray-500 hover:text-white transition flex items-center gap-1 w-full text-left">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    ログアウト
                </button>
            </form>
        </div>
    </aside>

    {{-- メインコンテンツ --}}
    <div class="flex-1 flex flex-col min-h-screen overflow-auto">
        <header class="bg-white border-b px-8 py-4 flex items-center justify-between">
            <h1 class="text-lg font-semibold text-gray-800">@yield('title', '管理画面')</h1>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500">{{ auth('admin')->user()->name ?? '' }}</span>
                @yield('header-action')
            </div>
        </header>

        <main class="flex-1 p-8">

            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>
</html>
