<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>注文詳細 {{ $order->order_number }}</title>
<style>
    /* 日本語フォント（Docker イメージに fonts-ipafont-gothic を導入済み）。
       IPAゴシックには太字が無いため、bold も同じファイルを割り当てて欧文フォントへのフォールバックを防ぐ */
    @font-face { font-family: 'ipag'; font-style: normal; font-weight: normal; src: url('{{ $fontPath }}') format('truetype'); }
    @font-face { font-family: 'ipag'; font-style: normal; font-weight: bold;   src: url('{{ $fontPath }}') format('truetype'); }

    @page { size: A4 portrait; margin: 12mm; }

    /* 1ページに収めるため、全体の大きさは body の font-size（$scale）だけで調整し、他は em 指定にする */
    body { font-family: 'ipag', sans-serif; font-size: {{ round(10 * $scale, 2) }}pt; color: #1f2937; line-height: 1.5; margin: 0; }

    h1 { font-size: 1.6em; margin: 0 0 .2em; }
    .issued { color: #6b7280; font-size: .85em; margin-bottom: 1em; }

    .section { border: 1px solid #d1d5db; border-radius: 4px; padding: .8em 1em; margin-bottom: 1em; }
    .section-title { font-size: 1.15em; font-weight: bold; border-bottom: 1px solid #e5e7eb; padding-bottom: .3em; margin: 0 0 .5em; }

    .order-number-label { color: #6b7280; font-size: .85em; margin: 0; }
    .order-number { font-size: 1.4em; font-weight: bold; margin: 0 0 .3em; }
    .badge { display: inline-block; border: 1px solid #9ca3af; border-radius: 3px; padding: .05em .6em; font-size: .85em; margin-right: .4em; }

    table.info { width: 100%; border-collapse: collapse; }
    table.info th { width: 8em; text-align: left; font-weight: normal; color: #6b7280; padding: .25em 0; vertical-align: top; }
    table.info td { padding: .25em 0; word-break: break-all; }

    table.items { width: 100%; border-collapse: collapse; margin-bottom: .6em; }
    table.items th { background: #f3f4f6; text-align: left; font-weight: bold; padding: .35em .5em; border-bottom: 1px solid #d1d5db; }
    table.items td { padding: .35em .5em; border-bottom: 1px solid #e5e7eb; vertical-align: middle; }
    table.items .num { text-align: right; white-space: nowrap; }
    .thumb { width: 3.5em; height: 3.5em; }
    .thumb-placeholder { width: 3.5em; height: 3.5em; background: #e5e7eb; }

    table.totals { width: 50%; margin-left: 50%; border-collapse: collapse; }
    table.totals th { text-align: left; font-weight: normal; color: #4b5563; padding: .2em 0; }
    table.totals td { text-align: right; padding: .2em 0; }
    table.totals tr.grand th, table.totals tr.grand td { font-weight: bold; font-size: 1.2em; border-top: 1px solid #9ca3af; padding-top: .4em; }
</style>
</head>
<body>

<h1>注文詳細</h1>
<p class="issued">出力日時：{{ now()->format('Y年m月d日 H:i') }}</p>

{{-- 注文情報 --}}
<div class="section">
    <p class="order-number-label">注文番号</p>
    <p class="order-number">{{ $order->order_number }}</p>
    <span class="badge">{{ $order->status_label }}</span>
    <span class="badge">{{ $order->payment_status === 'paid' ? '支払済' : '未払い' }}</span>
    <table class="info" style="margin-top: .5em;">
        <tr><th>注文日</th><td>{{ $order->created_at->format('Y年m月d日 H:i') }}</td></tr>
    </table>
</div>

{{-- 購入者情報 --}}
<div class="section">
    <p class="section-title">購入者情報</p>
    <table class="info">
        <tr><th>氏名</th><td>{{ $order->recipient_name }}</td></tr>
        <tr><th>郵便番号</th><td>〒 {{ $order->postal_code }}</td></tr>
        <tr><th>都道府県</th><td>{{ $order->prefecture }}</td></tr>
        <tr><th>住所</th><td>{{ $order->address }}</td></tr>
        <tr><th>電話番号</th><td>{{ $order->phone_number }}</td></tr>
        <tr><th>メールアドレス</th><td>{{ $order->email }}</td></tr>
    </table>
</div>

{{-- 商品一覧 --}}
<div class="section">
    <p class="section-title">商品一覧</p>
    <table class="items">
        <thead>
            <tr>
                <th style="width: 4.5em;">画像</th>
                <th>商品名</th>
                <th class="num">単価</th>
                <th class="num">数量</th>
                <th class="num">小計</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>
                        @if (!empty($images[$item->id]))
                            <img src="{{ $images[$item->id] }}" class="thumb" alt="">
                        @else
                            <div class="thumb-placeholder"></div>
                        @endif
                    </td>
                    <td>{{ $item->product_name }}</td>
                    <td class="num">{{ $item->formatted_price }}</td>
                    <td class="num">{{ $item->quantity }}</td>
                    <td class="num">{{ $item->formatted_subtotal }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <table class="totals">
        <tr><th>商品小計</th><td>{{ $order->formatted_subtotal_price }}</td></tr>
        <tr><th>送料（{{ $order->prefecture }}）</th><td>{{ $order->formatted_shipping_fee }}</td></tr>
        <tr class="grand"><th>合計金額</th><td>{{ $order->formatted_total_price }}</td></tr>
    </table>
</div>

</body>
</html>
