<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductListTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduct(string $name, string $status): Product
    {
        return Product::create(['name' => $name, 'price' => 1000, 'status' => $status]);
    }

    public function test_list_shows_selling_and_sold_out_but_not_hidden_products(): void
    {
        $this->makeProduct('販売中醤油', Product::STATUS_SELLING);
        $this->makeProduct('売り切れ醤油', Product::STATUS_SOLDOUT);
        $this->makeProduct('非表示醤油', Product::STATUS_HIDDEN);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('販売中醤油')
            ->assertSee('売り切れ醤油')
            ->assertDontSee('非表示醤油')
            ->assertSee('products_index_sold-out-label', false)
            ->assertDontSee('カートに追加');
    }

    public function test_sold_out_product_detail_is_shown_and_hidden_is_404(): void
    {
        $soldOut = $this->makeProduct('売り切れ醤油', Product::STATUS_SOLDOUT);
        $hidden = $this->makeProduct('非表示醤油', Product::STATUS_HIDDEN);

        $this->get(route('products.show', $soldOut))->assertOk()->assertSee('売り切れ');
        $this->get(route('products.show', $hidden))->assertNotFound();
    }
}
