@extends('admin.layouts.app')

@section('title', 'お知らせ管理')

@section('header-action')
    <a href="{{ route('admin.news.create') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
        ＋ 新規作成
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-6 py-3 font-medium text-gray-500 w-16">ID</th>
                <th class="text-left px-6 py-3 font-medium text-gray-500">タイトル</th>
                <th class="text-left px-6 py-3 font-medium text-gray-500 w-32">ステータス</th>
                <th class="text-left px-6 py-3 font-medium text-gray-500 w-40">公開日時</th>
                <th class="text-left px-6 py-3 font-medium text-gray-500 w-40">作成日時</th>
                <th class="px-6 py-3 w-48"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($newsList as $news)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-400">{{ $news->id }}</td>

                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-800 truncate max-w-xs">{{ $news->title }}</p>
                    </td>

                    <td class="px-6 py-4">
                        @if ($news->status === 'published' && $news->published_at && $news->published_at->lte(now()))
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>公開中
                            </span>
                        @elseif ($news->status === 'published')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>予約済
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>非公開
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-gray-500">
                        {{ $news->published_at ? $news->published_at->format('Y/m/d H:i') : '―' }}
                    </td>

                    <td class="px-6 py-4 text-gray-500">
                        {{ $news->created_at->format('Y/m/d H:i') }}
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            {{-- 公開/非公開切り替え --}}
                            <form method="POST" action="{{ route('admin.news.toggle', $news) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="text-xs px-3 py-1.5 rounded-lg border transition
                                        {{ $news->status === 'published' ? 'border-yellow-300 text-yellow-700 hover:bg-yellow-50' : 'border-green-300 text-green-700 hover:bg-green-50' }}">
                                    {{ $news->status === 'published' ? '非公開にする' : '公開する' }}
                                </button>
                            </form>

                            {{-- 編集 --}}
                            <a href="{{ route('admin.news.edit', $news) }}"
                               class="text-xs px-3 py-1.5 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                                編集
                            </a>

                            {{-- 削除 --}}
                            <form method="POST" action="{{ route('admin.news.destroy', $news) }}"
                                  onsubmit="return confirm('「{{ $news->title }}」を削除しますか？')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-xs px-3 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition">
                                    削除
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center text-gray-400">
                        お知らせがありません
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($newsList->hasPages())
        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $newsList->links() }}
        </div>
    @endif
</div>
@endsection
