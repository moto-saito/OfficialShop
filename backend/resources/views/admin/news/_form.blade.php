{{-- タイトル --}}
<div>
    <label class="common_admin-form-label">
        タイトル <span class="common_required-mark">*</span>
    </label>
    <input type="text" name="title"
           value="{{ old('title', $news->title ?? '') }}"
           class="common_admin-form-input @error('title') is-invalid @enderror"
           placeholder="お知らせタイトル">
    @error('title')
        <p class="common_admin-form-error">{{ $message }}</p>
    @enderror
</div>

{{-- 本文 --}}
<div>
    <label class="common_admin-form-label">本文</label>
    <textarea name="content" rows="10"
              class="common_admin-form-input @error('content') is-invalid @enderror"
              placeholder="本文を入力してください">{{ old('content', $news->content ?? '') }}</textarea>
    @error('content')
        <p class="common_admin-form-error">{{ $message }}</p>
    @enderror
</div>

{{-- 画像 --}}
<div>
    <label class="common_admin-form-label">画像</label>

    @if (!empty($news->image_path))
        <div class="admin_news_form_image-preview-wrap">
            <img src="{{ asset('storage/' . $news->image_path) }}"
                 alt="現在の画像"
                 class="admin_news_form_image-preview">
            <p class="common_admin-form-hint">新しい画像をアップロードすると置き換わります</p>
        </div>
    @endif

    <input type="file" name="image" accept="image/*"
           class="admin_news_form_file-input">
    <p class="common_admin-form-hint">JPEG・PNG・GIF・WebP（最大 2MB）</p>
    @error('image')
        <p class="common_admin-form-error">{{ $message }}</p>
    @enderror
</div>

{{-- ステータス --}}
<div>
    <label class="admin_news_form_status-label">ステータス</label>
    <div class="admin_news_form_status-options">
        <label class="admin_news_form_status-option">
            <input type="radio" name="status" value="draft"
                   {{ old('status', $news->status ?? 'draft') === 'draft' ? 'checked' : '' }}
                   class="admin_news_form_status-radio">
            <span class="admin_news_form_status-option-label">非公開（下書き）</span>
        </label>
        <label class="admin_news_form_status-option">
            <input type="radio" name="status" value="published"
                   {{ old('status', $news->status ?? '') === 'published' ? 'checked' : '' }}
                   class="admin_news_form_status-radio">
            <span class="admin_news_form_status-option-label">公開</span>
        </label>
    </div>
</div>

{{-- 公開日時 --}}
<div>
    <label class="common_admin-form-label">公開日時</label>
    <input type="datetime-local" name="published_at"
           value="{{ old('published_at', isset($news->published_at) ? $news->published_at->format('Y-m-d\TH:i') : '') }}"
           class="common_admin-form-input common_admin-form-input--auto @error('published_at') is-invalid @enderror">
    <p class="common_admin-form-hint">未入力で「公開」にした場合は現在日時が設定されます</p>
    @error('published_at')
        <p class="common_admin-form-error">{{ $message }}</p>
    @enderror
</div>
