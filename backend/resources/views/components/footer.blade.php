<footer class="footer">
    <div class="footer_container">
        <div class="footer_grid">

            {{-- 企業情報 --}}
            <div>
                <p class="footer_brand-name">{{ config('app.name') }}</p>
                <p class="footer_brand-sub">山一醤油製造所</p>
            </div>

            {{-- 連絡先 --}}
            <div>
                <p class="footer_heading">お問い合わせ</p>
                <ul class="footer_contact-list">
                    <li class="footer_contact-item">
                        <svg xmlns="http://www.w3.org/2000/svg" class="footer_contact-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <a href="tel:0120-000-000" class="footer_link">0120-000-000</a>
                    </li>
                    <li class="footer_contact-item">
                        <svg xmlns="http://www.w3.org/2000/svg" class="footer_contact-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <a href="mailto:info@official-shop.example.com" class="footer_link">
                            info@official-shop.example.com
                        </a>
                    </li>
                </ul>
                <p class="footer_hours">受付時間：平日 10:00〜18:00</p>
            </div>

            {{-- ナビゲーション --}}
            <div>
                <p class="footer_heading">メニュー</p>
                <ul class="footer_nav-list">
                    <li><a href="/" class="footer_link">TOP</a></li>
                    <li><a href="{{ route('news.index') }}" class="footer_link">お知らせ</a></li>
                    <li><a href="{{ route('products.index') }}" class="footer_link">商品一覧</a></li>
                    <li><a href="{{ route('cart.index') }}" class="footer_link">カート</a></li>
                </ul>
            </div>
        </div>

        <div class="footer_bottom">
            &copy; {{ date('Y') }} 山一醤油製造所 All Rights Reserved.
        </div>
    </div>
</footer>
