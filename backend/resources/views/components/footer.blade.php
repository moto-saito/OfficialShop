<footer class="bg-gray-900 text-gray-400 mt-auto">
    <div class="max-w-6xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- 企業情報 --}}
            <div>
                <p class="text-white font-bold text-lg mb-2">{{ config('app.name') }}</p>
                <p class="text-sm text-gray-500">山一醤油製造所</p>
            </div>

            {{-- 連絡先 --}}
            <div>
                <p class="text-sm font-semibold text-gray-300 uppercase tracking-wider mb-3">お問い合わせ</p>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <a href="tel:0120-000-000" class="hover:text-white transition">0120-000-000</a>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <a href="mailto:info@official-shop.example.com" class="hover:text-white transition">
                            info@official-shop.example.com
                        </a>
                    </li>
                </ul>
                <p class="mt-3 text-xs text-gray-600">受付時間：平日 10:00〜18:00</p>
            </div>

            {{-- ナビゲーション --}}
            <div>
                <p class="text-sm font-semibold text-gray-300 uppercase tracking-wider mb-3">メニュー</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="/" class="hover:text-white transition">TOP</a></li>
                    <li><a href="{{ route('news.index') }}" class="hover:text-white transition">お知らせ</a></li>
                    <li><a href="{{ route('products.index') }}" class="hover:text-white transition">商品一覧</a></li>
                    <li><a href="{{ route('cart.index') }}" class="hover:text-white transition">カート</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-10 pt-6 border-t border-gray-800 text-center text-xs text-gray-600">
            &copy; {{ date('Y') }} 山一醤油製造所 All Rights Reserved.
        </div>
    </div>
</footer>
