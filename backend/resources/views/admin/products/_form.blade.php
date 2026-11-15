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

{{-- 商品画像（最大10枚。2枚以上は商品詳細ページでスライド表示。1枚目がメイン画像） --}}
@php $currentImages = isset($product) ? $product->images : collect(); @endphp
<div data-image-manager data-max="10">
    <label class="common_admin-form-label">商品画像</label>

    <ol class="admin_products_form_image-list" data-image-list>
        {{-- 登録済みの画像：1枚ずつ変更・削除できる --}}
        @foreach ($currentImages as $image)
            <li class="admin_products_form_image-item" data-image-item data-existing>
                <img src="{{ Storage::disk('public')->url($image->path) }}"
                     data-original-src="{{ Storage::disk('public')->url($image->path) }}"
                     alt="登録済みの商品画像" class="admin_products_form_image-preview">
                <span class="admin_products_form_image-number" data-image-number></span>
                <span class="admin_products_form_image-badge" data-image-badge hidden></span>

                <div class="admin_products_form_image-actions">
                    <label class="admin_products_form_image-button">
                        変更
                        <input type="file" name="replace_images[{{ $image->id }}]" accept="image/*" class="admin_products_form_visually-hidden" data-replace-input>
                    </label>
                    <button type="button" class="admin_products_form_image-button admin_products_form_image-button--danger" data-delete-toggle>削除</button>
                </div>
                <input type="hidden" name="delete_images[]" value="{{ $image->id }}" disabled data-delete-input>
            </li>
        @endforeach
    </ol>

    {{-- 追加：「画像を追加」を押すたびに1枚ずつ末尾に追加される --}}
    <label class="admin_products_form_add-button" data-add-button>
        画像を追加
        <input type="file" accept="image/*" class="admin_products_form_visually-hidden" data-add-input>
    </label>
    {{-- 追加した画像をまとめて送信するための入力（JS で中身を組み立てる） --}}
    <input type="file" name="images[]" multiple hidden data-new-files>

    <p class="common_admin-form-hint">
        JPEG・PNG・GIF・WebP（1枚 2MB まで、最大 10 枚）。表示は番号順で、1枚目が一覧などのメイン画像になります。<br>
        変更・削除・追加は「保存」を押したときに反映されます。
    </p>

    @foreach (['images', 'images.*', 'replace_images.*'] as $field)
        @if ($errors->has($field))
            <p class="common_admin-form-error">{{ $errors->first($field) }}</p>
        @endif
    @endforeach
</div>

@push('scripts')
<script>
// 商品画像の管理（既存画像の変更・削除、新しい画像の1枚ずつ追加）
document.querySelectorAll('[data-image-manager]').forEach((manager) => {
    const max = Number(manager.dataset.max);
    const list = manager.querySelector('[data-image-list]');
    const addButton = manager.querySelector('[data-add-button]');
    const addInput = manager.querySelector('[data-add-input]');
    const newFilesInput = manager.querySelector('[data-new-files]');
    const newFiles = []; // { file, url }

    // 番号（表示順）と、追加ボタンの有効・無効を更新する
    function refresh() {
        let n = 0;
        list.querySelectorAll('[data-image-item]').forEach((item) => {
            const deleted = item.classList.contains('is-deleted');
            item.querySelector('[data-image-number]').textContent = deleted ? '' : ++n;
            item.querySelector('[data-image-number]').hidden = deleted;
        });
        const full = n >= max;
        addButton.classList.toggle('is-disabled', full);
        addInput.disabled = full;
        addButton.title = full ? `画像は${max}枚までです` : '';
    }

    function setBadge(item, text) {
        const badge = item.querySelector('[data-image-badge]');
        badge.textContent = text;
        badge.hidden = !text;
    }

    // 追加した画像を images[] の入力に反映する
    function syncNewFiles() {
        const dt = new DataTransfer();
        newFiles.forEach(({ file }) => dt.items.add(file));
        newFilesInput.files = dt.files;
    }

    // ── 既存画像：変更 ──
    list.querySelectorAll('[data-existing]').forEach((item) => {
        const img = item.querySelector('img');
        const replaceInput = item.querySelector('[data-replace-input]');
        const deleteInput = item.querySelector('[data-delete-input]');
        const deleteToggle = item.querySelector('[data-delete-toggle]');
        let previewUrl = null;

        replaceInput.addEventListener('change', () => {
            if (previewUrl) URL.revokeObjectURL(previewUrl);
            const file = replaceInput.files[0];
            previewUrl = file ? URL.createObjectURL(file) : null;
            img.src = previewUrl || img.dataset.originalSrc;
            setBadge(item, file ? '変更' : '');
        });

        // ── 既存画像：削除（保存前なら元に戻せる）──
        deleteToggle.addEventListener('click', () => {
            const deleting = !item.classList.contains('is-deleted');
            if (!deleting && activeCount() >= max) {
                alert(`画像は${max}枚までです。先に他の画像を削除してください。`);
                return;
            }
            item.classList.toggle('is-deleted', deleting);
            deleteInput.disabled = !deleting;
            replaceInput.disabled = deleting;
            deleteToggle.textContent = deleting ? '元に戻す' : '削除';
            setBadge(item, deleting ? '削除' : (replaceInput.files.length ? '変更' : ''));
            refresh();
        });
    });

    function activeCount() {
        return list.querySelectorAll('[data-image-item]:not(.is-deleted)').length;
    }

    // ── 新しい画像：1枚ずつ末尾に追加 ──
    addInput.addEventListener('change', () => {
        const file = addInput.files[0];
        addInput.value = ''; // 同じファイルをもう一度選べるようにリセット
        if (!file) return;

        const entry = { file, url: URL.createObjectURL(file) };
        newFiles.push(entry);
        syncNewFiles();

        const item = document.createElement('li');
        item.className = 'admin_products_form_image-item';
        item.dataset.imageItem = '';
        item.innerHTML = `
            <img src="${entry.url}" alt="追加する画像" class="admin_products_form_image-preview">
            <span class="admin_products_form_image-number" data-image-number></span>
            <span class="admin_products_form_image-badge admin_products_form_image-badge--new" data-image-badge>追加</span>
            <div class="admin_products_form_image-actions">
                <button type="button" class="admin_products_form_image-button admin_products_form_image-button--danger">取消</button>
            </div>`;
        item.querySelector('button').addEventListener('click', () => {
            newFiles.splice(newFiles.indexOf(entry), 1);
            URL.revokeObjectURL(entry.url);
            syncNewFiles();
            item.remove();
            refresh();
        });
        list.appendChild(item);
        refresh();
    });

    refresh();
});
</script>
@endpush

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
