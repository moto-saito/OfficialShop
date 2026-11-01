<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingFee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ShippingFeeTest extends TestCase
{
    use RefreshDatabase;

    private string $sessionId;

    protected function setUp(): void
    {
        parent::setUp();

        // カートはセッションIDで紐付くため、リクエスト間で同じセッションIDを使う
        $this->sessionId = Str::random(40);
        $this->withCookie(config('session.cookie'), $this->sessionId);
    }

    private function makeAdmin(): Admin
    {
        return Admin::create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);
    }

    private function putProductInCart(int $price, int $quantity): void
    {
        $product = Product::create([
            'name' => 'テスト醤油',
            'price' => $price,
            'status' => Product::STATUS_SELLING,
        ]);

        Cart::create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'session_id' => $this->sessionId,
        ]);
    }

    private function buyerInfo(array $overrides = []): array
    {
        return array_merge([
            'recipient_name' => '山田 太郎',
            'postal_code' => '123-4567',
            'prefecture' => '北海道',
            'address' => '札幌市1-2-3',
            'phone_number' => '090-1234-5678',
            'email' => 'taro@example.com',
        ], $overrides);
    }

    private function allFees(int $default): array
    {
        return collect(ShippingFee::PREFECTURES)->mapWithKeys(fn ($p) => [$p => $default])->all();
    }

    public function test_migration_creates_all_prefectures_with_zero_fee(): void
    {
        $this->assertSame(47, ShippingFee::count());
        $this->assertSame(0, ShippingFee::feeFor('東京都'));
    }

    public function test_admin_can_update_shipping_fees(): void
    {
        $fees = $this->allFees(800);
        $fees['北海道'] = 1500;
        $fees['沖縄県'] = 2000;

        $this->actingAs($this->makeAdmin(), 'admin')
            ->put(route('admin.shipping-fees.update'), ['fees' => $fees])
            ->assertRedirect(route('admin.shipping-fees.edit'));

        $this->assertSame(1500, ShippingFee::feeFor('北海道'));
        $this->assertSame(2000, ShippingFee::feeFor('沖縄県'));
        $this->assertSame(800, ShippingFee::feeFor('東京都'));
    }

    public function test_admin_update_rejects_negative_fee(): void
    {
        $fees = $this->allFees(800);
        $fees['東京都'] = -1;

        $this->actingAs($this->makeAdmin(), 'admin')
            ->put(route('admin.shipping-fees.update'), ['fees' => $fees])
            ->assertSessionHasErrors('fees.東京都');

        $this->assertSame(0, ShippingFee::feeFor('東京都'));
    }

    public function test_admin_edit_page_is_displayed(): void
    {
        $this->actingAs($this->makeAdmin(), 'admin')
            ->get(route('admin.shipping-fees.edit'))
            ->assertOk()
            ->assertSee('送料設定')
            ->assertSee('鹿児島県');
    }

    public function test_confirm_page_shows_subtotal_and_shipping_fee(): void
    {
        ShippingFee::where('prefecture', '北海道')->update(['fee' => 1200]);
        $this->putProductInCart(1000, 2);

        $this->actingAs(User::factory()->create())
            ->post(route('checkout.confirm'), $this->buyerInfo())
            ->assertOk()
            ->assertSeeInOrder(['商品小計', '¥2,000', '送料（北海道）', '¥1,200', '¥3,200']);
    }

    public function test_order_total_includes_shipping_fee_of_destination(): void
    {
        ShippingFee::where('prefecture', '北海道')->update(['fee' => 1200]);
        $this->putProductInCart(1000, 2);
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('checkout.confirm'), $this->buyerInfo());
        $this->actingAs($user)->post(route('checkout.store'))
            ->assertRedirect(route('checkout.complete'));

        $order = Order::sole();
        $this->assertSame(1200, $order->shipping_fee);
        $this->assertSame(3200, $order->total_price);
        $this->assertSame(2000, $order->subtotal_price);
    }

    public function test_checkout_rejects_unknown_prefecture(): void
    {
        $this->putProductInCart(1000, 1);

        $this->actingAs(User::factory()->create())
            ->post(route('checkout.confirm'), $this->buyerInfo(['prefecture' => '架空県']))
            ->assertSessionHasErrors('prefecture');
    }
}
