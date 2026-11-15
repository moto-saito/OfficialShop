<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->actingAs(Admin::create(['name' => '管理者', 'email' => 'admin@example.com', 'password' => 'password']), 'admin');
    }

    private function payload(array $images = []): array
    {
        return ['name' => '醤油', 'description' => '説明', 'price' => 1000, 'status' => 'selling', 'images' => $images];
    }

    private function files(int $n): array
    {
        return array_map(fn ($i) => UploadedFile::fake()->image("p{$i}.jpg", 400, 400), range(1, $n));
    }

    public function test_複数画像を選択順に登録し1枚目がメイン画像になる(): void
    {
        $this->post(route('admin.products.store'), $this->payload($this->files(3)))->assertRedirect(route('admin.products.index'));

        $product = Product::with('images')->first();
        $this->assertCount(3, $product->images);
        $this->assertSame($product->images[0]->path, $product->image_path);
        $this->assertSame([0, 1, 2], $product->images->pluck('sort_order')->all());
        foreach ($product->images as $image) {
            Storage::disk('public')->assertExists($image->path);
        }
    }

    private function makeProductWithImages(int $n): Product
    {
        $this->post(route('admin.products.store'), $this->payload($this->files($n)));

        return Product::with('images')->latest('id')->first();
    }

    public function test_追加した画像は末尾に1枚ずつ追加され既存の画像はそのまま(): void
    {
        $product = $this->makeProductWithImages(2);
        $before = $product->images->pluck('path')->all();

        $this->put(route('admin.products.update', $product), $this->payload($this->files(1)))->assertRedirect();

        $product->refresh()->load('images');
        $this->assertCount(3, $product->images);
        $this->assertSame($before, $product->images->take(2)->pluck('path')->all());
        $this->assertSame([0, 1, 2], $product->images->pluck('sort_order')->all());
        $this->assertSame($before[0], $product->image_path);
    }

    public function test_既存の画像を1枚だけ変更できる(): void
    {
        $product = $this->makeProductWithImages(3);
        [$first, $second, $third] = $product->images->all();

        $this->put(route('admin.products.update', $product), $this->payload() + [
            'replace_images' => [$second->id => UploadedFile::fake()->image('new.jpg')],
        ])->assertRedirect();

        $product->refresh()->load('images');
        $this->assertCount(3, $product->images);
        $this->assertSame($first->path, $product->images[0]->path);
        $this->assertNotSame($second->path, $product->images[1]->path);
        $this->assertSame($third->path, $product->images[2]->path);
        Storage::disk('public')->assertMissing($second->path);
        Storage::disk('public')->assertExists($product->images[1]->path);
    }

    public function test_既存の画像を削除すると詰めて並び1枚目がメインになる(): void
    {
        $product = $this->makeProductWithImages(3);
        [$first, $second, $third] = $product->images->all();

        $this->put(route('admin.products.update', $product), $this->payload() + ['delete_images' => [$first->id]])->assertRedirect();

        $product->refresh()->load('images');
        $this->assertSame([$second->path, $third->path], $product->images->pluck('path')->all());
        $this->assertSame([0, 1], $product->images->pluck('sort_order')->all());
        $this->assertSame($second->path, $product->image_path);
        Storage::disk('public')->assertMissing($first->path);
    }

    public function test_全部削除するとメイン画像も空になる(): void
    {
        $product = $this->makeProductWithImages(2);

        $this->put(route('admin.products.update', $product), $this->payload() + [
            'delete_images' => $product->images->pluck('id')->all(),
        ])->assertRedirect();

        $product->refresh();
        $this->assertCount(0, $product->images);
        $this->assertNull($product->image_path);
    }

    public function test_他の商品の画像IDは削除変更できない(): void
    {
        $other = $this->makeProductWithImages(1);
        $product = $this->makeProductWithImages(1);
        $otherImage = $other->images->first();

        $this->put(route('admin.products.update', $product), $this->payload() + [
            'delete_images' => [$otherImage->id],
            'replace_images' => [$otherImage->id => UploadedFile::fake()->image('x.jpg')],
        ])->assertRedirect();

        $this->assertDatabaseHas('product_images', ['id' => $otherImage->id, 'path' => $otherImage->path]);
        Storage::disk('public')->assertExists($otherImage->path);
    }

    public function test_画像を選択せずに更新すると今の画像のまま(): void
    {
        $product = $this->makeProductWithImages(2);

        $this->put(route('admin.products.update', $product), array_merge($this->payload(), ['name' => '新しい名前']))->assertRedirect();

        $product->refresh()->load('images');
        $this->assertSame('新しい名前', $product->name);
        $this->assertCount(2, $product->images);
    }

    public function test_合計が10枚を超える追加はエラー_削除と合わせて10枚以内ならOK(): void
    {
        $product = $this->makeProductWithImages(9);

        $this->put(route('admin.products.update', $product), $this->payload($this->files(2)))
            ->assertSessionHasErrors(['images' => '商品画像は10枚まで登録できます。']);
        $this->assertCount(9, $product->refresh()->images);

        $this->put(route('admin.products.update', $product), $this->payload($this->files(2)) + [
            'delete_images' => [$product->images->first()->id],
        ])->assertSessionHasNoErrors();
        $this->assertCount(10, $product->refresh()->images);
    }

    public function test_11枚以上や画像以外はエラーになる(): void
    {
        $this->post(route('admin.products.store'), $this->payload($this->files(11)))
            ->assertSessionHasErrors(['images' => '商品画像は10枚まで登録できます。']);

        $this->post(route('admin.products.store'), $this->payload([UploadedFile::fake()->create('a.pdf', 10, 'application/pdf')]))
            ->assertSessionHasErrors('images.0');

        $this->assertSame(0, Product::count());
    }

    public function test_商品を削除すると画像ファイルも削除される(): void
    {
        $this->post(route('admin.products.store'), $this->payload($this->files(2)));
        $product = Product::with('images')->first();
        $paths = $product->images->pluck('path')->all();

        $this->delete(route('admin.products.destroy', $product))->assertRedirect();

        $this->assertSame(0, Product::count());
        $this->assertDatabaseCount('product_images', 0);
        foreach ($paths as $path) {
            Storage::disk('public')->assertMissing($path);
        }
    }

    public function test_商品詳細は2枚以上でスライド_1枚は画像だけ(): void
    {
        $this->post(route('admin.products.store'), $this->payload($this->files(3)));
        $multi = Product::first();
        $this->get(route('products.show', $multi))
            ->assertOk()
            ->assertSee('class="products_show_slider"', false)
            ->assertSee('（3枚目）');

        $this->post(route('admin.products.store'), array_merge($this->payload($this->files(1)), ['name' => '味噌']));
        $single = Product::where('name', '味噌')->first();
        $this->get(route('products.show', $single))
            ->assertOk()
            ->assertDontSee('class="products_show_slider"', false)
            ->assertDontSee('商品画像のスライド')
            ->assertSee(Storage::disk('public')->url($single->image_path), false);
    }
}
