<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    /** 1商品に登録できる画像の最大枚数 */
    private const MAX_IMAGES = 10;

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);
        $this->assertImageCount(0, 0, count($request->file('images', [])));

        DB::transaction(function () use ($request, $validated) {
            $product = Product::create($validated);
            $this->syncImages($product, $request);
        });

        return redirect()->route('admin.products.index')->with('success', '商品を登録しました。');
    }

    public function edit(Product $product)
    {
        $product->load('images');

        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $this->validateProduct($request);

        $current = $product->images()->count();
        $deleting = $product->images()->whereIn('id', $request->input('delete_images', []))->count();
        $this->assertImageCount($current, $deleting, count($request->file('images', [])));

        DB::transaction(function () use ($request, $product, $validated) {
            $product->update($validated);
            $this->syncImages($product, $request);
        });

        return redirect()->route('admin.products.index')->with('success', '商品を更新しました。');
    }

    public function destroy(Product $product)
    {
        $paths = $product->images()->pluck('path')->push($product->image_path)->filter()->unique()->all();

        $product->delete(); // product_images は外部キーの cascade で削除される

        Storage::disk('public')->delete($paths);

        return redirect()->route('admin.products.index')->with('success', '商品を削除しました。');
    }

    private function validateProduct(Request $request): array
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'price'            => 'required|integer|min:0',
            'status'           => 'required|in:selling,soldout,hidden',
            'images'           => 'nullable|array',            // 追加する画像（末尾に追加）
            'images.*'         => 'image|max:2048',
            'replace_images'   => 'nullable|array',            // 既存画像の差し替え [画像ID => ファイル]
            'replace_images.*' => 'image|max:2048',
            'delete_images'    => 'nullable|array',            // 削除する既存画像の ID
            'delete_images.*'  => 'integer',
        ], [
            'images.*.image'         => '追加する画像には画像ファイルを選択してください。',
            'images.*.max'           => '画像は1枚あたり 2MB までです。',
            'replace_images.*.image' => '変更する画像には画像ファイルを選択してください。',
            'replace_images.*.max'   => '画像は1枚あたり 2MB までです。',
        ]);

        return Arr::only($validated, ['name', 'description', 'price', 'status']);
    }

    /** 変更後の枚数が上限を超えないか確認する */
    private function assertImageCount(int $current, int $deleting, int $adding): void
    {
        if ($current - $deleting + $adding > self::MAX_IMAGES) {
            throw ValidationException::withMessages([
                'images' => '商品画像は' . self::MAX_IMAGES . '枚まで登録できます。',
            ]);
        }
    }

    /**
     * 商品画像を更新する（削除 → 差し替え → 末尾に追加 の順）。
     * 並び順を詰め直し、1枚目を products.image_path（一覧・カート・注文 PDF などのメイン画像）にする。
     */
    private function syncImages(Product $product, Request $request): void
    {
        $disk = Storage::disk('public');
        $stored = [];   // 今回保存したファイル（失敗したら消す）
        $obsolete = []; // 不要になったファイル（成功したら消す）

        try {
            $images = $product->images()->get()->keyBy('id');

            // 削除（他の商品の画像 ID は無視する）
            foreach ($request->input('delete_images', []) as $id) {
                if ($image = $images->pull((int) $id)) {
                    $obsolete[] = $image->path;
                    $image->delete();
                }
            }

            // 差し替え
            foreach ($request->file('replace_images', []) as $id => $file) {
                if ($image = $images->get((int) $id)) {
                    $obsolete[] = $image->path;
                    $stored[] = $path = $file->store('products', 'public');
                    $image->update(['path' => $path]);
                }
            }

            // 末尾に追加（選択した順）
            $order = $images->max('sort_order') ?? -1;
            foreach ($request->file('images', []) as $file) {
                $stored[] = $path = $file->store('products', 'public');
                $product->images()->create(['path' => $path, 'sort_order' => ++$order]);
            }

            // 並び順を 0 から詰め直し、メイン画像を更新
            $ordered = $product->images()->get();
            foreach ($ordered as $i => $image) {
                if ((int) $image->sort_order !== $i) {
                    $image->update(['sort_order' => $i]);
                }
            }
            $mainPath = $ordered->first()?->path;
            if ($product->image_path && $product->image_path !== $mainPath && !$ordered->contains('path', $product->image_path)) {
                $obsolete[] = $product->image_path; // 旧仕様の image_path だけに残っていた画像
            }
            $product->update(['image_path' => $mainPath]);
        } catch (\Throwable $e) {
            $disk->delete($stored);
            throw $e;
        }

        // DB の更新が確定してから古いファイルを消す（途中で失敗しても画像が消えないように）
        $obsolete = array_values(array_unique(array_filter($obsolete)));
        if ($obsolete) {
            DB::afterCommit(fn () => $disk->delete($obsolete));
        }
    }
}
