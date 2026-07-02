<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex items-center justify-between h-16">

            {{-- ロゴ --}}
            <a href="/" class="text-xl font-bold tracking-tight text-gray-900 flex-shrink-0">
                {{ config('app.name') }}
            </a>

            {{-- PC ナビ --}}
            <nav class="hidden md:flex items-center gap-6">
                <a href="/"
                   class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition
                          {{ request()->is('/') ? 'text-indigo-600 border-b-2 border-indigo-600 pb-0.5' : '' }}">
                    TOP
                </a>
                <a href="{{ route('news.index') }}"
                   class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition
                          {{ request()->routeIs('news.*') ? 'text-indigo-600 border-b-2 border-indigo-600 pb-0.5' : '' }}">
                    お知らせ
                </a>
                <a href="{{ route('products.index') }}"
                   class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition
                          {{ request()->routeIs('products.*') ? 'text-indigo-600 border-b-2 border-indigo-600 pb-0.5' : '' }}">
                    商品一覧
                </a>
                <a href="{{ route('cart.index') }}"
                   class="relative flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-indigo-600 transition
                          {{ request()->routeIs('cart.*') ? 'text-indigo-600' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-10H5.4M7 13l-1.4 7h12.8M10 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/>
                    </svg>
                    カート
                </a>

                {{-- 仕切り --}}
                <span class="text-gray-200">|</span>

                {{-- 認証リンク（PC） --}}
                @auth
                    <a href="{{ route('mypage.index') }}"
                       class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition
                              {{ request()->routeIs('mypage.*') ? 'text-indigo-600' : '' }}">
                        マイページ
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="text-sm font-medium text-gray-500 hover:text-red-500 transition">
                            ログアウト
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition">
                        ログイン
                    </a>
                    <a href="{{ route('register') }}"
                       class="text-sm font-medium bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1.5 rounded-lg transition">
                        新規会員登録
                    </a>
                @endauth
            </nav>

            {{-- ハンバーガーボタン（モバイル） --}}
            <button id="menu-btn"
                    class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition"
                    aria-label="メニューを開く">
                <svg id="icon-open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg id="icon-close" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- モバイルメニュー --}}
    <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white">
        <nav class="max-w-6xl mx-auto px-4 py-3 flex flex-col gap-1">
            <a href="/"
               class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition
                      {{ request()->is('/') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                TOP
            </a>
            <a href="{{ route('news.index') }}"
               class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition
                      {{ request()->routeIs('news.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                お知らせ
            </a>
            <a href="{{ route('products.index') }}"
               class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition
                      {{ request()->routeIs('products.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                商品一覧
            </a>
            <a href="{{ route('cart.index') }}"
               class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition
                      {{ request()->routeIs('cart.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-10H5.4M7 13l-1.4 7h12.8M10 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/>
                </svg>
                カート
            </a>

            {{-- 仕切り --}}
            <div class="my-1 border-t border-gray-100"></div>

            {{-- 認証リンク（モバイル） --}}
            @auth
                <a href="{{ route('mypage.index') }}"
                   class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition
                          {{ request()->routeIs('mypage.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    マイページ
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-red-50 hover:text-red-600 transition text-left">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        ログアウト
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                   class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition">
                    ログイン
                </a>
                <a href="{{ route('register') }}"
                   class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700 transition">
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
