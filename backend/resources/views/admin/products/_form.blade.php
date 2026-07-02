{{-- 商品名 --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        商品名 <span class="text-red-500">*</span>
    </label>
    <input type="text" name="name"
           value="{{ old('name', $product->name ?? '') }}"
           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('name') border-red-400 @enderror"
           placeholder="商品名を入力">
    @error('name')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- 商品説明 --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">商品説明</label>
    <textarea name="description" rows="6"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('description') border-red-400 @enderror"
              placeholder="商品の説明を入力してください">{{ old('description', $product->description ?? '') }}</textarea>
    @error('description')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- 商品価格 --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        価格（円） <span class="text-red-500">*</span>
    </label>
    <div class="relative w-48">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-gray-500">¥</span>
        <input type="number" name="price" min="0"
               value="{{ old('price', $product->price ?? '') }}"
               class="w-full border border-gray-300 rounded-lg pl-7 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('price') border-red-400 @enderror"
               placeholder="0">
    </div>
    @error('price')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- 商品画像 --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">商品画像</label>

    @if (!empty($product->image_path))
        <div class="mb-3">
            <img src="{{ asset('storage/' . $product->image_path) }}"
                 alt="現在の商品画像"
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

{{-- 販売状態 --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        販売状態 <span class="text-red-500">*</span>
    </label>
    <select name="status"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('status') border-red-400 @enderror">
        @php $currentStatus = old('status', $product->status ?? 'selling'); @endphp
        <option value="selling"  {{ $currentStatus === 'selling'  ? 'selected' : '' }}>販売中</option>
        <option value="soldout"  {{ $currentStatus === 'soldout'  ? 'selected' : '' }}>売り切れ</option>
        <option value="hidden"   {{ $currentStatus === 'hidden'   ? 'selected' : '' }}>非公開</option>
    </select>
    @error('status')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>
