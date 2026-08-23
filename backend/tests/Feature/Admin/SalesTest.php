<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SalesTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): Admin
    {
        return Admin::create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);
    }

    /**
     * @param array<int, array{name: string, price: int, quantity: int}> $items
     */
    private function makeOrder(array $overrides = [], array $items = [['name' => 'テスト商品', 'price' => 1000, 'quantity' => 1]]): Order
    {
        $totalPrice = collect($items)->sum(fn ($item) => $item['price'] * $item['quantity']);

        $order = Order::create(array_merge([
            'order_number' => 'ORD-' . uniqid(),
            'total_price' => $totalPrice,
            'status' => 'completed',
            'payment_status' => 'paid',
            'recipient_name' => '山田 太郎',
            'postal_code' => '123-4567',
            'prefecture' => '東京都',
            'address' => '渋谷区1-2-3',
            'phone_number' => '090-1234-5678',
            'email' => 'taro@example.com',
        ], $overrides));

        foreach ($items as $item) {
            $order->items()->create([
                'product_name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity'],
            ]);
        }

        // created_at / paid_at はテストのために明示的に上書きする
        $order->forceFill([
            'created_at' => $overrides['created_at'] ?? now(),
            'paid_at' => array_key_exists('paid_at', $overrides) ? $overrides['paid_at'] : now(),
        ])->save();

        return $order->fresh();
    }

    public function test_売上管理ページが表示される(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.sales.index'));

        $response->assertStatus(200);
        $response->assertSee('売上管理');
    }

    public function test_開始日終了日で指定期間の決済だけ取得される(): void
    {
        $admin = $this->makeAdmin();

        $inRange = $this->makeOrder(['order_number' => 'ORD-IN', 'paid_at' => '2026-04-15 10:00:00']);
        $outOfRange = $this->makeOrder(['order_number' => 'ORD-OUT', 'paid_at' => '2026-05-01 10:00:00']);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.sales.index', [
            'date_from' => '2026-04-01',
            'date_to' => '2026-04-30',
        ]));

        $response->assertStatus(200);
        $response->assertSee($inRange->order_number);
        $response->assertDontSee($outOfRange->order_number);
    }

    public function test_売上合計と決済件数が正しく計算される(): void
    {
        $admin = $this->makeAdmin();

        $this->makeOrder(['paid_at' => '2026-04-10 10:00:00'], [['name' => '商品A', 'price' => 1000, 'quantity' => 2]]);
        $this->makeOrder(['paid_at' => '2026-04-20 10:00:00'], [['name' => '商品B', 'price' => 3000, 'quantity' => 1]]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.sales.index', [
            'date_from' => '2026-04-01',
            'date_to' => '2026-04-30',
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('summary', function ($summary) {
            return $summary['gross_sales'] === 5000
                && $summary['payment_count'] === 2;
        });
    }

    public function test_未決済の注文は売上に含まれない(): void
    {
        $admin = $this->makeAdmin();

        $this->makeOrder([
            'order_number' => 'ORD-UNPAID',
            'payment_status' => 'unpaid',
            'status' => 'pending',
            'paid_at' => null,
        ], [['name' => '未決済商品', 'price' => 5000, 'quantity' => 1]]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.sales.index', [
            'date_from' => now()->startOfMonth()->format('Y-m-d'),
            'date_to' => now()->endOfMonth()->format('Y-m-d'),
        ]));

        $response->assertViewHas('summary', function ($summary) {
            return $summary['gross_sales'] === 0
                && $summary['payment_count'] === 0;
        });
    }

    public function test_キャンセル返金の扱いが正しい(): void
    {
        $admin = $this->makeAdmin();

        // 支払済み・通常注文
        $this->makeOrder(['paid_at' => '2026-04-05 10:00:00', 'status' => 'completed'], [
            ['name' => '通常注文', 'price' => 4000, 'quantity' => 1],
        ]);

        // 支払済みだがキャンセル＝返金扱い
        $this->makeOrder(['paid_at' => '2026-04-06 10:00:00', 'status' => 'cancelled'], [
            ['name' => 'キャンセル注文', 'price' => 1500, 'quantity' => 1],
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.sales.index', [
            'date_from' => '2026-04-01',
            'date_to' => '2026-04-30',
        ]));

        $response->assertViewHas('summary', function ($summary) {
            return $summary['gross_sales'] === 5500
                && $summary['refunded_amount'] === 1500
                && $summary['refunded_count'] === 1
                && $summary['net_sales'] === 4000;
        });
    }

    public function test_決済一覧が表示される(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder(['paid_at' => now()], [
            ['name' => '商品X', 'price' => 2000, 'quantity' => 3],
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.sales.index'));

        $response->assertStatus(200);
        $response->assertSee($order->order_number);
        $response->assertSee('商品X');
        $response->assertSee($order->recipient_name);
    }

    public function test_ページネーションが動作する(): void
    {
        $admin = $this->makeAdmin();

        for ($i = 0; $i < 25; $i++) {
            $this->makeOrder(['order_number' => "ORD-PAGE-{$i}", 'paid_at' => now()]);
        }

        $response = $this->actingAs($admin, 'admin')->get(route('admin.sales.index'));
        $response->assertStatus(200);
        $response->assertViewHas('orders', fn ($orders) => $orders->total() === 25 && $orders->count() === 20);

        $page2 = $this->actingAs($admin, 'admin')->get(route('admin.sales.index', ['page' => 2]));
        $page2->assertStatus(200);
        $page2->assertViewHas('orders', fn ($orders) => $orders->count() === 5);
    }

    public function test_CSV出力ができ検索期間の全件と一致する金額が出力される(): void
    {
        $admin = $this->makeAdmin();

        $order = $this->makeOrder(['paid_at' => '2026-04-12 10:00:00'], [
            ['name' => '商品Y', 'price' => 1200, 'quantity' => 2],
        ]);
        $this->makeOrder(['paid_at' => '2026-05-01 10:00:00']); // 期間外

        $response = $this->actingAs($admin, 'admin')->get(route('admin.sales.export', [
            'date_from' => '2026-04-01',
            'date_to' => '2026-04-30',
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        // BOM付きでExcelでの文字化けを防止している
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
        $this->assertStringContainsString($order->order_number, $content);
        $this->assertStringContainsString('商品Y', $content);
        $this->assertStringContainsString('2400', $content); // 商品小計 = 1200 × 2
        $this->assertStringNotContainsString('2026-05', $content);
    }

    public function test_一般ユーザーは売上管理画面へアクセスできない(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.sales.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_未認証ユーザーは売上管理画面へアクセスできない(): void
    {
        $response = $this->get(route('admin.sales.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_一般ユーザーはCSV出力URLへ直接アクセスできない(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.sales.export'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_未認証ユーザーはCSV出力URLへ直接アクセスできない(): void
    {
        $response = $this->get(route('admin.sales.export'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_一覧表示でN1クエリが発生しない(): void
    {
        $admin = $this->makeAdmin();

        for ($i = 0; $i < 5; $i++) {
            $this->makeOrder(['order_number' => "ORD-N1-{$i}", 'paid_at' => now()], [
                ['name' => '商品A', 'price' => 1000, 'quantity' => 1],
                ['name' => '商品B', 'price' => 2000, 'quantity' => 1],
            ]);
        }

        $queryCount = 0;
        DB::listen(function () use (&$queryCount) {
            $queryCount++;
        });

        $this->actingAs($admin, 'admin')->get(route('admin.sales.index'))->assertStatus(200);

        // 注文件数(5件)に比例してクエリが増えていないこと（N+1が発生していれば件数分クエリが増える）
        $this->assertLessThan(15, $queryCount);
    }
}
