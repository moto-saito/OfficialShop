@extends('admin.layouts.app')

@section('title', '注文詳細')

@section('header-action')
    <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">
        ← 注文一覧へ戻る
    </a>
@endsection

@section('content')
<div class="max-w-3xl">

    {{-- 注文情報 --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b flex items-center justify-between flex-wrap gap-2">
            <div>
                <p class="text-xs text-gray-500 mb-0.5">注文番号</p>
                <p class="text-sm font-bold text-gray-800 tracking-wide">{{ $order->order_number }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->status_color }}">
                    {{ $order->status_label }}
                </span>
                @if ($order->payment_status === 'paid')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                        支払済
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                        未払い
                    </span>
                @endif
            </div>
        </div>
        <dl class="divide-y divide-gray-100 text-sm">
            <div class="px-6 py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">注文日</dt>
                <dd class="col-span-2 text-gray-900">{{ $order->created_at->format('Y年m月d日 H:i') }}</dd>
            </div>
        </dl>
    </div>

    {{-- 購入者情報 --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b">
            <h2 class="text-sm font-semibold text-gray-700">購入者情報</h2>
        </div>
        <dl class="divide-y divide-gray-100 text-sm">
            <div class="px-6 py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">氏名</dt>
                <dd class="col-span-2 text-gray-900">{{ $order->recipient_name }}</dd>
            </div>
            <div class="px-6 py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">郵便番号</dt>
                <dd class="col-span-2 text-gray-900">〒 {{ $order->postal_code }}</dd>
            </div>
            <div class="px-6 py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">都道府県</dt>
                <dd class="col-span-2 text-gray-900">{{ $order->prefecture }}</dd>
            </div>
            <div class="px-6 py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">住所</dt>
                <dd class="col-span-2 text-gray-900">{{ $order->address }}</dd>
            </div>
            <div class="px-6 py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">電話番号</dt>
                <dd class="col-span-2 text-gray-900">{{ $order->phone_number }}</dd>
            </div>
            <div class="px-6 py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">メールアドレス</dt>
                <dd class="col-span-2 text-gray-900 break-all">{{ $order->email }}</dd>
            </div>
        </dl>
    </div>

    {{-- 商品一覧 --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b">
            <h2 class="text-sm font-semibold text-gray-700">商品一覧</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-6 py-3 font-medium text-gray-500 w-20">画像</th>
                    <th class="text-left px-6 py-3 font-medium text-gray-500">商品名</th>
                    <th class="text-right px-6 py-3 font-medium text-gray-500 w-24">単価</th>
                    <th class="text-center px-6 py-3 font-medium text-gray-500 w-16">数量</th>
                    <th class="text-right px-6 py-3 font-medium text-gray-500 w-24">小計</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($order->items as $item)
                    <tr>
                        <td class="px-6 py-4">
                            @if ($item->product?->image_path)
                                <img src="{{ asset('storage/' . $item->product->image_path) }}"
                                     alt="{{ $item->product_name }}"
                                     class="h-12 w-12 object-cover rounded-lg border">
                            @else
                                <div class="h-12 w-12 bg-gray-100 rounded-lg border flex items-center justify-center">
                                    <svg class="h-5 w-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-800">{{ $item->product_name }}</td>
                        <td class="px-6 py-4 text-right text-gray-700">{{ $item->formatted_price }}</td>
                        <td class="px-6 py-4 text-center text-gray-600">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-right font-medium text-gray-800">{{ $item->formatted_subtotal }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 bg-gray-50 border-t flex items-center justify-between">
            <span class="text-sm font-semibold text-gray-700">合計金額</span>
            <span class="text-xl font-bold text-gray-900">{{ $order->formatted_total_price }}</span>
        </div>
    </div>

    {{-- ステータス変更 --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b">
            <h2 class="text-sm font-semibold text-gray-700">ステータス変更</h2>
        </div>
        <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="px-6 py-4">
            @csrf
            @method('PATCH')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">注文ステータス</label>
                    <select name="status" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach (['pending' => '受付', 'processing' => '準備中', 'shipped' => '発送済', 'completed' => '完了', 'cancelled' => 'キャンセル'] as $value => $label)
                            <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">決済ステータス</label>
                    <select name="payment_status" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach (['unpaid' => '未払い', 'paid' => '支払済'] as $value => $label)
                            <option value="{{ $value }}" @selected($order->payment_status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                更新する
            </button>
        </form>
    </div>

    {{-- 注文削除 --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h2 class="text-sm font-semibold text-gray-700">注文削除</h2>
        </div>
        <div class="px-6 py-4">
            <p class="text-xs text-gray-500 mb-3">誤登録などの理由で注文を削除する場合はこちらから削除できます。この操作は取り消せません。</p>
            <form method="POST" action="{{ route('admin.orders.destroy', $order) }}"
                  onsubmit="return confirm('注文番号「{{ $order->order_number }}」を削除しますか？\nこの操作は取り消せません。')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs px-4 py-2 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition">
                    この注文を削除する
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
