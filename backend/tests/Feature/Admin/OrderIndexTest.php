<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderIndexTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrder(string $number, string $name): Order
    {
        return Order::create([
            'order_number' => $number,
            'total_price' => 1000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'recipient_name' => $name,
            'postal_code' => '123-4567',
            'prefecture' => '東京都',
            'address' => '渋谷区1-2-3',
            'phone_number' => '090-1234-5678',
            'email' => 'test@example.com',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(Admin::create(['name' => '管理者', 'email' => 'admin@example.com', 'password' => 'password']), 'admin');
        $this->makeOrder('ORD-AAA-001', '山田太郎');
        $this->makeOrder('ORD-BBB-002', '佐藤花子');
    }

    public function test_orders_are_not_listed_by_default(): void
    {
        $this->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee('「検索」を押すと')
            ->assertDontSee('ORD-AAA-001')
            ->assertDontSee('ORD-BBB-002');
    }

    public function test_only_matching_orders_are_listed_after_search(): void
    {
        $this->get(route('admin.orders.index', ['searched' => 1, 'recipient_name' => '山田']))
            ->assertOk()
            ->assertSee('ORD-AAA-001')
            ->assertDontSee('ORD-BBB-002');
    }

    public function test_search_without_conditions_lists_all_orders(): void
    {
        $this->get(route('admin.orders.index', ['searched' => 1]))
            ->assertOk()
            ->assertSee('ORD-AAA-001')
            ->assertSee('ORD-BBB-002');
    }

    public function test_url_with_conditions_is_treated_as_search(): void
    {
        $this->get(route('admin.orders.index', ['order_number' => 'BBB']))
            ->assertOk()
            ->assertSee('ORD-BBB-002')
            ->assertDontSee('ORD-AAA-001');
    }

    public function test_no_match_shows_empty_message(): void
    {
        $this->get(route('admin.orders.index', ['searched' => 1, 'recipient_name' => '該当なし']))
            ->assertOk()
            ->assertSee('該当する注文がありません');
    }
}
