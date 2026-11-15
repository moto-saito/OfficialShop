<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPdfTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrder(int $itemCount): Order
    {
        $order = Order::create([
            'order_number' => 'ORD-TEST-0001',
            'total_price' => 1000 * $itemCount,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'recipient_name' => '山田 太郎',
            'postal_code' => '123-4567',
            'prefecture' => '東京都',
            'address' => '渋谷区1-2-3',
            'phone_number' => '090-1234-5678',
            'email' => 'taro@example.com',
        ]);

        for ($i = 1; $i <= $itemCount; $i++) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_name' => "テスト商品{$i}",
                'price' => 1000,
                'quantity' => 1,
                'subtotal' => 1000,
            ]);
        }

        return $order;
    }

    private function pageCount(string $pdf): int
    {
        preg_match_all('#/Type\s*/Page[^s]#', $pdf, $m);
        return count($m[0]);
    }

    private function actingAsAdmin(): self
    {
        $admin = Admin::create(['name' => '管理者', 'email' => 'admin@example.com', 'password' => 'password']);
        return $this->actingAs($admin, 'admin');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $order = $this->makeOrder(1);

        $this->get(route('admin.orders.pdf', $order))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_download_order_pdf(): void
    {
        $order = $this->makeOrder(3);

        $response = $this->actingAsAdmin()->get(route('admin.orders.pdf', $order));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertDownload('order_ORD-TEST-0001.pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
        $this->assertSame(1, $this->pageCount($response->getContent()));
    }

    public function test_order_with_many_items_fits_on_one_page(): void
    {
        $order = $this->makeOrder(60);

        $response = $this->actingAsAdmin()->get(route('admin.orders.pdf', $order));

        $response->assertOk();
        $this->assertSame(1, $this->pageCount($response->getContent()));
    }

    public function test_order_detail_page_has_pdf_button(): void
    {
        $order = $this->makeOrder(1);

        $this->actingAsAdmin()->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee(route('admin.orders.pdf', $order), false)
            ->assertSee('PDF出力');
    }
}
