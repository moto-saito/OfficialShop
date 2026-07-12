<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '管理画面') | {{ config('app.name') }} Admin</title>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    @stack('head')
</head>
<body class="admin_layouts_app_body">

    {{-- サイドバー --}}
    <aside class="admin_layouts_app_sidebar">
        <div class="admin_layouts_app_sidebar-header">
            <p class="admin_layouts_app_sidebar-eyebrow">Admin</p>
            <a href="{{ route('admin.dashboard') }}" class="admin_layouts_app_sidebar-title">{{ config('app.name') }}</a>
        </div>
        <nav class="admin_layouts_app_nav">
            <a href="{{ route('admin.dashboard') }}"
               class="admin_layouts_app_nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="admin_layouts_app_nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                ダッシュボード
            </a>

            <p class="admin_layouts_app_nav-heading">コンテンツ</p>

            <a href="{{ route('admin.products.index') }}"
               class="admin_layouts_app_nav-link {{ request()->routeIs('admin.products.*') ? 'is-active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="admin_layouts_app_nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
                商品管理
            </a>

            <a href="{{ route('admin.news.index') }}"
               class="admin_layouts_app_nav-link {{ request()->routeIs('admin.news.*') ? 'is-active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="admin_layouts_app_nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6"/>
                </svg>
                お知らせ管理
            </a>

            <p class="admin_layouts_app_nav-heading">受注</p>

            <a href="{{ route('admin.orders.index') }}"
               class="admin_layouts_app_nav-link {{ request()->routeIs('admin.orders.*') ? 'is-active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="admin_layouts_app_nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                注文管理
            </a>
        </nav>
        <div class="admin_layouts_app_sidebar-footer">
            <a href="/" target="_blank" class="admin_layouts_app_footer-link">
                <svg xmlns="http://www.w3.org/2000/svg" class="admin_layouts_app_footer-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                サイトを見る
            </a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="admin_layouts_app_footer-logout">
                    <svg xmlns="http://www.w3.org/2000/svg" class="admin_layouts_app_footer-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    ログアウト
                </button>
            </form>
        </div>
    </aside>

    {{-- メインコンテンツ --}}
    <div class="admin_layouts_app_content">
        <header class="admin_layouts_app_topbar">
            <h1 class="admin_layouts_app_topbar-title">@yield('title', '管理画面')</h1>
            <div class="admin_layouts_app_topbar-actions">
                <span class="admin_layouts_app_topbar-user">{{ auth('admin')->user()->name ?? '' }}</span>
                @yield('header-action')
            </div>
        </header>

        <main class="admin_layouts_app_main">

            @if (session('success'))
                <div class="common_flash-success">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>
</html>
