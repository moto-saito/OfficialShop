<footer class="footer">
    <div class="footer_container">
        <div class="footer_grid">

            {{-- 企業情報 --}}
            <div>
                <p class="footer_brand-name">{{ config('app.name') }}</p>
                <p class="footer_brand-sub">山一醤油製造所</p>

                {{-- SNSリンク --}}
                <div class="footer_sns-list">
                    <a href="#" target="_blank" rel="noopener noreferrer" aria-label="X（旧Twitter）" class="footer_sns-link">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="footer_sns-icon">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>
                    <a href="#" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="footer_sns-link">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="footer_sns-icon">
                            <path d="M12 2c2.717 0 3.056.01 4.122.06 1.065.05 1.79.217 2.428.465.66.254 1.216.598 1.772 1.153a4.908 4.908 0 011.153 1.772c.247.637.415 1.363.465 2.428.047 1.066.06 1.405.06 4.122 0 2.717-.01 3.056-.06 4.122-.05 1.065-.218 1.79-.465 2.428a4.883 4.883 0 01-1.153 1.772 4.915 4.915 0 01-1.772 1.153c-.637.247-1.363.415-2.428.465-1.066.047-1.405.06-4.122.06-2.717 0-3.056-.01-4.122-.06-1.065-.05-1.79-.218-2.428-.465a4.89 4.89 0 01-1.772-1.153 4.904 4.904 0 01-1.153-1.772c-.248-.637-.415-1.363-.465-2.428C2.013 15.056 2 14.717 2 12c0-2.717.01-3.056.06-4.122.05-1.066.217-1.79.465-2.428a4.88 4.88 0 011.153-1.772A4.897 4.897 0 015.45 2.525c.638-.248 1.362-.415 2.428-.465C8.944 2.013 9.283 2 12 2zm0 1.802c-2.67 0-2.987.01-4.04.059-.976.045-1.505.207-1.858.344-.466.181-.8.398-1.15.748-.35.35-.566.683-.747 1.15-.137.352-.3.882-.344 1.857-.05 1.053-.06 1.37-.06 4.04 0 2.67.01 2.987.06 4.04.045.975.207 1.505.344 1.857.181.466.398.8.748 1.15.35.35.683.566 1.15.747.352.137.882.3 1.857.344 1.053.05 1.37.06 4.04.06 2.67 0 2.987-.01 4.04-.06.976-.045 1.505-.207 1.858-.344.466-.181.8-.398 1.15-.748.35-.35.566-.683.747-1.15.137-.352.3-.882.344-1.857.05-1.053.06-1.37.06-4.04 0-2.67-.01-2.987-.06-4.04-.045-.975-.207-1.505-.344-1.857a3.09 3.09 0 00-.748-1.15 3.09 3.09 0 00-1.15-.747c-.352-.137-.882-.3-1.857-.344-1.053-.05-1.37-.06-4.04-.06zm0 4.595a5.603 5.603 0 110 11.206 5.603 5.603 0 010-11.206zm0 1.802a3.801 3.801 0 100 7.602 3.801 3.801 0 000-7.602zm5.834-1.802a1.31 1.31 0 11-2.62 0 1.31 1.31 0 012.62 0z"/>
                        </svg>
                    </a>
                </div>
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
                    <li><a href="{{ route('store.show') }}" class="footer_link">店舗・工場紹介</a></li>
                    <li><a href="{{ route('company.show') }}" class="footer_link">企業歴史・実績</a></li>
                    <li><a href="{{ route('contact.index') }}" class="footer_link">お問い合わせ</a></li>
                    <li><a href="{{ route('cart.index') }}" class="footer_link">カート</a></li>
                </ul>
            </div>
        </div>

        <div class="footer_bottom">
            &copy; {{ date('Y') }} 山一醤油製造所 All Rights Reserved.
        </div>
    </div>
</footer>
