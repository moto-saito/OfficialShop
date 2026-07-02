{{-- タイトル --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        タイトル <span class="text-red-500">*</span>
    </label>
    <input type="text" name="title"
           value="{{ old('title', $news->title ?? '') }}"
           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('title') border-red-400 @enderror"
           placeholder="お知らせタイトル">
    @error('title')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- 本文 --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">本文</label>
    <textarea name="content" rows="10"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('content') border-red-400 @enderror"
              placeholder="本文を入力してください">{{ old('content', $news->content ?? '') }}</textarea>
    @error('content')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- 画像 --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">画像</label>

    @if (!empty($news->image_path))
        <div class="mb-3">
            <img src="{{ asset('storage/' . $news->image_path) }}"
                 alt="現在の画像"
                 class="h-32 w-auto rounded-lg border object-cover">
            <p class="mt-1 text-xs text-gray-400">新しい画像をアップロードすると置き換わります</p>
        </div>
    @endif

    <input type="file" name="image" accept="image/*"
           class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
    <p class="mt-1 text-xs text-gray-400">JPEG・PNG・GIF・WebP（最大 2MB）</p>
    @error('image')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- ステータス --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">ステータス</label>
    <div class="flex gap-6">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="status" value="draft"
                   {{ old('status', $news->status ?? 'draft') === 'draft' ? 'checked' : '' }}
                   class="accent-indigo-600">
            <span class="text-sm text-gray-700">非公開（下書き）</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="status" value="published"
                   {{ old('status', $news->status ?? '') === 'published' ? 'checked' : '' }}
                   class="accent-indigo-600">
            <span class="text-sm text-gray-700">公開</span>
        </label>
    </div>
</div>

{{-- 公開日時 --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">公開日時</label>
    <input type="datetime-local" name="published_at"
           value="{{ old('published_at', isset($news->published_at) ? $news->published_at->format('Y-m-d\TH:i') : '') }}"
           class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('published_at') border-red-400 @enderror">
    <p class="mt-1 text-xs text-gray-400">未入力で「公開」にした場合は現在日時が設定されます</p>
    @error('published_at')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>
