@extends('layouts.app')

@section('title', '注文履歴')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">

    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-900">注文履歴</h1>
        <a href="{{ route('mypage.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">
            ← マイページへ
        </a>
    </div>

    @if ($orders->isEmpty())
        <div class="text-center py-20 text-gray-400">
            <p class="mb-4">まだ注文がありません。</p>
            <a href="{{ route('products.index') }}"
               class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-6 py-2.5 rounded-xl transition">
                商品一覧へ
            </a>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-6 py-3 font-medium text-gray-500">注文番号</th>
                        <th class="text-left px-6 py-3 font-medium text-gray-500 hidden sm:table-cell">注文日</th>
                        <th class="text-right px-6 py-3 font-medium text-gray-500">金額</th>
                        <th class="text-center px-6 py-3 font-medium text-gray-500 w-28">状態</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($orders as $order)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('mypage.orders.show', $order) }}"
                                   class="font-medium text-indigo-600 hover:underline text-xs tracking-wide">
                                    {{ $order->order_number }}
                                </a>
                                {{-- モバイル用注文日 --}}
                                <p class="text-xs text-gray-400 mt-0.5 sm:hidden">
                                    {{ $order->created_at->format('Y/m/d') }}
                                </p>
                            </td>
                            <td class="px-6 py-4 text-gray-500 hidden sm:table-cell">
                                {{ $order->created_at->format('Y/m/d H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-gray-800">
                                {{ $order->formatted_total_price }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->status_color }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($orders->hasPages())
                <div class="px-6 py-4 border-t bg-gray-50">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
