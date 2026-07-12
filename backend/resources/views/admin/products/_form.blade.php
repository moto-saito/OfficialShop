{{-- 商品名 --}}
<div>
    <label class="common_admin-form-label">
        商品名 <span class="common_required-mark">*</span>
    </label>
    <input type="text" name="name"
           value="{{ old('name', $product->name ?? '') }}"
           class="common_admin-form-input @error('name') is-invalid @enderror"
           placeholder="商品名を入力">
    @error('name')
        <p class="common_admin-form-error">{{ $message }}</p>
    @enderror
</div>

{{-- 商品説明 --}}
<div>
    <label class="common_admin-form-label">商品説明</label>
    <textarea name="description" rows="6"
              class="common_admin-form-input @error('description') is-invalid @enderror"
              placeholder="商品の説明を入力してください">{{ old('description', $product->description ?? '') }}</textarea>
    @error('description')
        <p class="common_admin-form-error">{{ $message }}</p>
    @enderror
</div>

{{-- 商品価格 --}}
<div>
    <label class="common_admin-form-label">
        価格（円） <span class="common_required-mark">*</span>
    </label>
    <div class="admin_products_form_price-wrap">
        <span class="admin_products_form_price-prefix">¥</span>
        <input type="number" name="price" min="0"
               value="{{ old('price', $product->price ?? '') }}"
               class="common_admin-form-input admin_products_form_price-input @error('price') is-invalid @enderror"
               placeholder="0">
    </div>
    @error('price')
        <p class="common_admin-form-error">{{ $message }}</p>
    @enderror
</div>

{{-- 商品画像 --}}
<div>
    <label class="common_admin-form-label">商品画像</label>

    @if (!empty($product->image_path))
        <div class="admin_products_form_image-preview-wrap">
            <img src="{{ asset('storage/' . $product->image_path) }}"
                 alt="現在の商品画像"
                 class="admin_products_form_image-preview">
            <p class="common_admin-form-hint">新しい画像をアップロードすると置き換わります</p>
        </div>
    @endif

    <input type="file" name="image" accept="image/*"
           class="admin_products_form_file-input">
    <p class="common_admin-form-hint">JPEG・PNG・GIF・WebP（最大 2MB）</p>
    @error('image')
        <p class="common_admin-form-error">{{ $message }}</p>
    @enderror
</div>

{{-- 販売状態 --}}
<div>
    <label class="common_admin-form-label">
        販売状態 <span class="common_required-mark">*</span>
    </label>
    <select name="status"
            class="common_admin-form-input common_admin-form-input--auto @error('status') is-invalid @enderror">
        @php $currentStatus = old('status', $product->status ?? 'selling'); @endphp
        <option value="selling"  {{ $currentStatus === 'selling'  ? 'selected' : '' }}>販売中</option>
        <option value="soldout"  {{ $currentStatus === 'soldout'  ? 'selected' : '' }}>売り切れ</option>
        <option value="hidden"   {{ $currentStatus === 'hidden'   ? 'selected' : '' }}>非公開</option>
    </select>
    @error('status')
        <p class="common_admin-form-error">{{ $message }}</p>
    @enderror
</div>
