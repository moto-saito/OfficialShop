@php
    $ingredientRows = old('ingredients', isset($recipe)
        ? $recipe->ingredients->map(fn ($i) => ['name' => $i->name, 'amount' => $i->amount])->toArray()
        : []);

    $stepRows = old('steps', isset($recipe)
        ? $recipe->steps->map(fn ($s) => ['body' => $s->body, 'existing_image' => $s->image_path])->toArray()
        : []);
@endphp

{{-- タイトル --}}
<div>
    <label class="common_admin-form-label">
        タイトル <span class="common_required-mark">*</span>
    </label>
    <input type="text" name="title"
           value="{{ old('title', $recipe->title ?? '') }}"
           class="common_admin-form-input @error('title') is-invalid @enderror"
           placeholder="レシピタイトル">
    @error('title')
        <p class="common_admin-form-error">{{ $message }}</p>
    @enderror
</div>

{{-- 人数分 --}}
<div>
    <label class="common_admin-form-label">人数分</label>
    <input type="text" name="servings"
           value="{{ old('servings', $recipe->servings ?? '') }}"
           class="common_admin-form-input common_admin-form-input--auto @error('servings') is-invalid @enderror"
           placeholder="例: 2">
    <p class="common_admin-form-hint">「〇人分」の〇の部分を入力してください</p>
    @error('servings')
        <p class="common_admin-form-error">{{ $message }}</p>
    @enderror
</div>

{{-- 紹介文 --}}
<div>
    <label class="common_admin-form-label">紹介文</label>
    <textarea name="content" rows="4"
              class="common_admin-form-input @error('content') is-invalid @enderror"
              placeholder="レシピの紹介・コツやポイントなど">{{ old('content', $recipe->content ?? '') }}</textarea>
    @error('content')
        <p class="common_admin-form-error">{{ $message }}</p>
    @enderror
</div>

{{-- メイン画像 --}}
<div>
    <label class="common_admin-form-label">メイン画像</label>

    @if (!empty($recipe->image_path))
        <div class="admin_recipes_form_image-preview-wrap">
            <img src="{{ asset('storage/' . $recipe->image_path) }}"
                 alt="現在の画像"
                 class="admin_recipes_form_image-preview">
            <p class="common_admin-form-hint">新しい画像をアップロードすると置き換わります</p>
        </div>
    @endif

    <input type="file" name="image" accept="image/*"
           class="admin_recipes_form_file-input">
    <p class="common_admin-form-hint">JPEG・PNG・GIF・WebP（最大 2MB）</p>
    @error('image')
        <p class="common_admin-form-error">{{ $message }}</p>
    @enderror
</div>

{{-- 材料 --}}
<div>
    <label class="admin_recipes_form_section-label">材料</label>

    <div id="ingredients-list" class="admin_recipes_form_ingredients-list">
        @forelse ($ingredientRows as $i => $ingredient)
            <div class="admin_recipes_form_ingredient-row">
                <input type="text" name="ingredients[{{ $i }}][name]"
                       value="{{ $ingredient['name'] ?? '' }}"
                       placeholder="材料名（例: 醤油）"
                       class="common_admin-form-input admin_recipes_form_ingredient-name">
                <input type="text" name="ingredients[{{ $i }}][amount]"
                       value="{{ $ingredient['amount'] ?? '' }}"
                       placeholder="分量（例: 大さじ2）"
                       class="common_admin-form-input admin_recipes_form_ingredient-amount">
                <button type="button" class="admin_recipes_form_row-remove">削除</button>
            </div>
        @empty
            <div class="admin_recipes_form_ingredient-row">
                <input type="text" name="ingredients[0][name]"
                       placeholder="材料名（例: 醤油）"
                       class="common_admin-form-input admin_recipes_form_ingredient-name">
                <input type="text" name="ingredients[0][amount]"
                       placeholder="分量（例: 大さじ2）"
                       class="common_admin-form-input admin_recipes_form_ingredient-amount">
                <button type="button" class="admin_recipes_form_row-remove">削除</button>
            </div>
        @endforelse
    </div>

    <button type="button" id="add-ingredient" class="admin_recipes_form_row-add">
        ＋ 材料を追加
    </button>
    @error('ingredients.*.name')
        <p class="common_admin-form-error">{{ $message }}</p>
    @enderror
</div>

{{-- 作り方 --}}
<div>
    <label class="admin_recipes_form_section-label">作り方</label>

    <div id="steps-list" class="admin_recipes_form_steps-list">
        @forelse ($stepRows as $i => $step)
            <div class="admin_recipes_form_step-row">
                <div class="admin_recipes_form_step-number">{{ $i + 1 }}.</div>
                <div class="admin_recipes_form_step-fields">
                    <textarea name="steps[{{ $i }}][body]" rows="3"
                              class="common_admin-form-input"
                              placeholder="手順を入力してください">{{ $step['body'] ?? '' }}</textarea>

                    @if (!empty($step['existing_image']))
                        <div class="admin_recipes_form_image-preview-wrap">
                            <img src="{{ asset('storage/' . $step['existing_image']) }}"
                                 alt="現在の画像"
                                 class="admin_recipes_form_image-preview">
                            <p class="common_admin-form-hint">新しい画像をアップロードすると置き換わります</p>
                        </div>
                        <input type="hidden" name="steps[{{ $i }}][existing_image]" value="{{ $step['existing_image'] }}">
                    @endif

                    <input type="file" name="steps[{{ $i }}][image]" accept="image/*"
                           class="admin_recipes_form_file-input">
                </div>
                <button type="button" class="admin_recipes_form_row-remove">削除</button>
            </div>
        @empty
            <div class="admin_recipes_form_step-row">
                <div class="admin_recipes_form_step-number">1.</div>
                <div class="admin_recipes_form_step-fields">
                    <textarea name="steps[0][body]" rows="3"
                              class="common_admin-form-input"
                              placeholder="手順を入力してください"></textarea>
                    <input type="file" name="steps[0][image]" accept="image/*"
                           class="admin_recipes_form_file-input">
                </div>
                <button type="button" class="admin_recipes_form_row-remove">削除</button>
            </div>
        @endforelse
    </div>

    <button type="button" id="add-step" class="admin_recipes_form_row-add">
        ＋ 手順を追加
    </button>
    <p class="common_admin-form-hint">手順ごとに任意で写真を1枚添付できます（JPEG・PNG・GIF・WebP、最大2MB）</p>
    @error('steps.*.body')
        <p class="common_admin-form-error">{{ $message }}</p>
    @enderror
    @error('steps.*.image')
        <p class="common_admin-form-error">{{ $message }}</p>
    @enderror
</div>

{{-- ステータス --}}
<div>
    <label class="admin_recipes_form_status-label">ステータス</label>
    <div class="admin_recipes_form_status-options">
        <label class="admin_recipes_form_status-option">
            <input type="radio" name="status" value="draft"
                   {{ old('status', $recipe->status ?? 'draft') === 'draft' ? 'checked' : '' }}
                   class="admin_recipes_form_status-radio">
            <span class="admin_recipes_form_status-option-label">非公開（下書き）</span>
        </label>
        <label class="admin_recipes_form_status-option">
            <input type="radio" name="status" value="published"
                   {{ old('status', $recipe->status ?? '') === 'published' ? 'checked' : '' }}
                   class="admin_recipes_form_status-radio">
            <span class="admin_recipes_form_status-option-label">公開</span>
        </label>
    </div>
</div>

{{-- 公開日時 --}}
<div>
    <label class="common_admin-form-label">公開日時</label>
    <input type="datetime-local" name="published_at"
           value="{{ old('published_at', isset($recipe->published_at) ? $recipe->published_at->format('Y-m-d\TH:i') : '') }}"
           class="common_admin-form-input common_admin-form-input--auto @error('published_at') is-invalid @enderror">
    <p class="common_admin-form-hint">未入力で「公開」にした場合は現在日時が設定されます</p>
    @error('published_at')
        <p class="common_admin-form-error">{{ $message }}</p>
    @enderror
</div>

@push('scripts')
<script>
(function () {
    let ingredientIndex = {{ empty($ingredientRows) ? 1 : max(array_keys($ingredientRows)) + 1 }};
    let stepIndex        = {{ empty($stepRows) ? 1 : max(array_keys($stepRows)) + 1 }};

    const ingredientsList = document.getElementById('ingredients-list');
    const stepsList       = document.getElementById('steps-list');

    document.getElementById('add-ingredient').addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'admin_recipes_form_ingredient-row';
        row.innerHTML = `
            <input type="text" name="ingredients[${ingredientIndex}][name]" placeholder="材料名（例: 醤油）" class="common_admin-form-input admin_recipes_form_ingredient-name">
            <input type="text" name="ingredients[${ingredientIndex}][amount]" placeholder="分量（例: 大さじ2）" class="common_admin-form-input admin_recipes_form_ingredient-amount">
            <button type="button" class="admin_recipes_form_row-remove">削除</button>
        `;
        ingredientsList.appendChild(row);
        ingredientIndex++;
    });

    ingredientsList.addEventListener('click', function (e) {
        if (e.target.classList.contains('admin_recipes_form_row-remove')) {
            e.target.closest('.admin_recipes_form_ingredient-row').remove();
        }
    });

    function renumberSteps() {
        stepsList.querySelectorAll('.admin_recipes_form_step-number').forEach(function (el, i) {
            el.textContent = (i + 1) + '.';
        });
    }

    document.getElementById('add-step').addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'admin_recipes_form_step-row';
        row.innerHTML = `
            <div class="admin_recipes_form_step-number"></div>
            <div class="admin_recipes_form_step-fields">
                <textarea name="steps[${stepIndex}][body]" rows="3" class="common_admin-form-input" placeholder="手順を入力してください"></textarea>
                <input type="file" name="steps[${stepIndex}][image]" accept="image/*" class="admin_recipes_form_file-input">
            </div>
            <button type="button" class="admin_recipes_form_row-remove">削除</button>
        `;
        stepsList.appendChild(row);
        stepIndex++;
        renumberSteps();
    });

    stepsList.addEventListener('click', function (e) {
        if (e.target.classList.contains('admin_recipes_form_row-remove')) {
            e.target.closest('.admin_recipes_form_step-row').remove();
            renumberSteps();
        }
    });

    renumberSteps();
})();
</script>
@endpush
