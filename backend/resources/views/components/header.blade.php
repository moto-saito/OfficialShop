<header class="header_bar">
    <div class="header_container">
        <div class="header_row">

            {{-- ロゴ --}}
            <a href="/" class="header_logo">
                {{ config('app.name') }}
            </a>

            {{-- PC ナビ --}}
            <nav class="header_nav-desktop">
                <a href="/"
                   class="header_nav-link {{ request()->is('/') ? 'is-active' : '' }}">
                    TOP
                </a>
                <a href="{{ route('news.index') }}"
                   class="header_nav-link {{ request()->routeIs('news.*') ? 'is-active' : '' }}">
                    お知らせ
                </a>
                <a href="{{ route('products.index') }}"
                   class="header_nav-link {{ request()->routeIs('products.*') ? 'is-active' : '' }}">
                    商品一覧
                </a>
                <a href="{{ route('cart.index') }}"
                   class="header_cart-link {{ request()->routeIs('cart.*') ? 'is-active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="header_nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-10H5.4M7 13l-1.4 7h12.8M10 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/>
                    </svg>
                    カート
                </a>

                {{-- 仕切り --}}
                <span class="header_divider">|</span>

                {{-- 認証リンク（PC） --}}
                @auth
                    <a href="{{ route('mypage.index') }}"
                       class="header_cart-link {{ request()->routeIs('mypage.*') ? 'is-active' : '' }}">
                        マイページ
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="header_logout-form">
                        @csrf
                        <button type="submit"
                                class="header_logout-button">
                            ログアウト
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="header_nav-link">
                        ログイン
                    </a>
                    <a href="{{ route('register') }}"
                       class="header_register-link">
                        新規会員登録
                    </a>
                @endauth
            </nav>

            {{-- ハンバーガーボタン（モバイル） --}}
            <button id="menu-btn"
                    class="header_menu-button"
                    aria-label="メニューを開く">
                <svg id="icon-open" xmlns="http://www.w3.org/2000/svg" class="header_menu-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg id="icon-close" xmlns="http://www.w3.org/2000/svg" class="header_menu-icon hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- モバイルメニュー --}}
    <div id="mobile-menu" class="header_mobile-menu hidden">
        <nav class="header_mobile-nav">
            <a href="/"
               class="header_mobile-link {{ request()->is('/') ? 'is-active' : '' }}">
                TOP
            </a>
            <a href="{{ route('news.index') }}"
               class="header_mobile-link {{ request()->routeIs('news.*') ? 'is-active' : '' }}">
                お知らせ
            </a>
            <a href="{{ route('products.index') }}"
               class="header_mobile-link {{ request()->routeIs('products.*') ? 'is-active' : '' }}">
                商品一覧
            </a>
            <a href="{{ route('cart.index') }}"
               class="header_mobile-link {{ request()->routeIs('cart.*') ? 'is-active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="header_mobile-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-10H5.4M7 13l-1.4 7h12.8M10 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/>
                </svg>
                カート
            </a>

            {{-- 仕切り --}}
            <div class="header_mobile-divider"></div>

            {{-- 認証リンク（モバイル） --}}
            @auth
                <a href="{{ route('mypage.index') }}"
                   class="header_mobile-link {{ request()->routeIs('mypage.*') ? 'is-active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="header_mobile-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    マイページ
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="header_mobile-logout">
                        <svg xmlns="http://www.w3.org/2000/svg" class="header_mobile-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        ログアウト
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                   class="header_mobile-link">
                    ログイン
                </a>
                <a href="{{ route('register') }}"
                   class="header_mobile-register">
                    新規会員登録
                </a>
            @endauth
        </nav>
    </div>
</header>

<script>
(function () {
    const btn  = document.getElementById('menu-btn');
    const menu = document.getElementById('mobile-menu');
    const open = document.getElementById('icon-open');
    const close = document.getElementById('icon-close');
    btn.addEventListener('click', function () {
        const isHidden = menu.classList.toggle('hidden');
        open.classList.toggle('hidden', !isHidden);
        close.classList.toggle('hidden', isHidden);
    });
})();
</script>
