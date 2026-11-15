<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StoreInfo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderB2CsvTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(Admin::create(['name' => '管理者', 'email' => 'admin@example.com', 'password' => 'password']), 'admin');

        config(['yamato.b2' => array_merge(config('yamato.b2'), [
            'slip_type'      => '5',               // 着払い（既定）
            'customer_code'  => '',
            'freight_number' => '01',
            'item_name'      => '',
            'header_row'     => true,
            'sender' => [
                'name'     => '山一醤油製造所',
                'phone'    => '0120-000-000',
                'postal'   => '1000001',
                'address'  => '東京都千代田区千代田１－１',
                'building' => '',
            ],
        ])]);
    }

    private function makeOrder(array $overrides = [], array $itemNames = ['濃口醤油']): Order
    {
        $order = Order::create(array_merge([
            'order_number'   => 'ORD-20261115-ABCD1234',
            'total_price'    => 1000,
            'status'         => 'pending',
            'payment_status' => 'paid',
            'recipient_name' => '山田 太郎',
            'postal_code'    => '１２３－４５６７',
            'prefecture'     => '東京都',
            'address'        => '渋谷区神南１－２－３',
            'phone_number'   => '０９０－１２３４－５６７８',
            'email'          => 'taro@example.com',
        ], $overrides));
        foreach ($itemNames as $name) {
            OrderItem::create(['order_id' => $order->id, 'product_name' => $name, 'price' => 500, 'quantity' => 1, 'subtotal' => 500]);
        }
        return $order;
    }

    /** CSV（Shift_JIS）を UTF-8 にして、各行を「項目No.（1始まり）=> 値」の配列にする */
    private function parse(string $csv): array
    {
        $lines = preg_split('/\r\n/', rtrim(mb_convert_encoding($csv, 'UTF-8', 'SJIS-win'), "\r\n"));

        return array_map(fn ($line) => array_combine(range(1, 97), str_getcsv($line, ',', '"', '')), $lines);
    }

    public function test_送り状CSVをShift_JISで出力し必須項目が入る(): void
    {
        $order = $this->makeOrder([], ['濃口醤油', 'あけがらし', 'みそ']);

        $response = $this->get(route('admin.orders.b2csv', $order));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=Shift_JIS');
        $response->assertHeader('Content-Disposition', 'attachment; filename=b2_ORD20261115ABCD1234.csv');
        $csv = $response->getContent();
        $this->assertFalse(mb_check_encoding($csv, 'UTF-8'), 'UTF-8 ではなく Shift_JIS で出力されていること');
        $this->assertTrue(mb_check_encoding($csv, 'SJIS-win'));
        $this->assertStringContainsString("\r\n", $csv);

        [$header, $row] = $this->parse($csv);
        $this->assertCount(97, $row);
        $this->assertSame('お客様管理番号', $header[1]);
        $this->assertSame('ORD20261115ABCD1234', $row[1]);
        $this->assertSame('5', $row[2]);                         // 着払い
        $this->assertSame(now()->format('Y/m/d'), $row[5]);       // 出荷予定日
        $this->assertSame('090-1234-5678', $row[9]);              // 全角→半角
        $this->assertSame('123-4567', $row[11]);
        $this->assertSame('東京都渋谷区神南１－２－３', $row[12]);
        $this->assertSame('山田 太郎', $row[16]);
        $this->assertSame('様', $row[18]);
        $this->assertSame('0120-000-000', $row[20]);
        $this->assertSame('100-0001', $row[22]);
        $this->assertSame('東京都千代田区千代田１－１', $row[23]);
        $this->assertSame('山一醤油製造所', $row[25]);
        $this->assertSame('濃口醤油 他2点', $row[28]);
        // 着払いはご請求先顧客コード・分類コード・運賃管理番号を入れない（非入力項目）
        $this->assertSame('', $row[40]);
        $this->assertSame('', $row[41]);
        $this->assertSame('', $row[42]);
        $this->assertSame('', $row[4]);                           // 伝票番号は空
    }

    public function test_長い住所は空白の位置で建物名欄に分ける(): void
    {
        $order = $this->makeOrder(['address' => '渋谷区神南一丁目二番三号神南ビジネスセンター前 グランドメゾン渋谷神南１０１号室']);

        [, $row] = $this->parse($this->get(route('admin.orders.b2csv', $order))->getContent());

        $this->assertSame('東京都渋谷区神南一丁目二番三号神南ビジネスセンター前', $row[12]);
        $this->assertSame('グランドメゾン渋谷神南１０１号室', $row[13]);
    }

    public function test_送り状に収まらない住所や使えない文字はエラーで理由を表示(): void
    {
        $order = $this->makeOrder([
            'address'        => str_repeat('長い住所', 20),
            'recipient_name' => '山田 太郎😀',
        ]);

        $this->get(route('admin.orders.b2csv', $order))
            ->assertRedirect(route('admin.orders.show', $order))
            ->assertSessionHas('error', fn ($messages) =>
                in_array('お届け先住所が長すぎて送り状に収まりません（住所 全角32文字＋建物名 全角16文字まで）。B2クラウドで直接入力してください。', $messages)
                && in_array('お届け先名に送り状で使えない文字（絵文字・一部の旧字体など）が含まれています。', $messages));

        $this->get(route('admin.orders.show', $order))->assertSee('B2クラウド用CSVを出力できません。');
    }

    public function test_着払いは顧客コードが未設定でも出力でき_設定していても送り状には入れない(): void
    {
        config(['yamato.b2.customer_code' => '012345678901']);

        [, $row] = $this->parse($this->get(route('admin.orders.b2csv', $this->makeOrder()))->getContent());

        $this->assertSame('5', $row[2]);
        $this->assertSame('', $row[40]);
        $this->assertSame('', $row[42]);
    }

    public function test_発払いではご請求先顧客コードと運賃管理番号を入れる(): void
    {
        config(['yamato.b2.slip_type' => '0', 'yamato.b2.customer_code' => '012345678901']);

        [, $row] = $this->parse($this->get(route('admin.orders.b2csv', $this->makeOrder()))->getContent());

        $this->assertSame('0', $row[2]);
        $this->assertSame('012345678901', $row[40]);
        $this->assertSame('01', $row[42]);
    }

    public function test_送り状種類が不正ならエラー(): void
    {
        config(['yamato.b2.slip_type' => 'Z']);

        $this->get(route('admin.orders.b2csv', $this->makeOrder()))
            ->assertSessionHas('error', fn ($messages) =>
                in_array('送り状種類（YAMATO_B2_SLIP_TYPE）は 0〜9 または A で設定してください（5:着払い, 0:発払い など）。', $messages));
    }

    public function test_設定が未入力ならどの設定が足りないか表示(): void
    {
        // 発払いでは顧客コードが必須
        config(['yamato.b2.slip_type' => '0', 'yamato.b2.customer_code' => null, 'yamato.b2.sender.phone' => '']);
        $order = $this->makeOrder();

        $this->get(route('admin.orders.b2csv', $order))
            ->assertRedirect()
            ->assertSessionHas('error', fn ($messages) =>
                in_array('ご請求先顧客コード（YAMATO_B2_CUSTOMER_CODE）が設定されていません。', $messages)
                && in_array('ご依頼主の電話番号（管理画面「店舗・工場情報」の「送り状のご依頼主」で設定）が入力されていません。', $messages));
    }

    public function test_タイトル行なしにもできる(): void
    {
        config(['yamato.b2.header_row' => false]);
        $csv = mb_convert_encoding($this->get(route('admin.orders.b2csv', $this->makeOrder()))->getContent(), 'UTF-8', 'SJIS-win');

        $this->assertStringNotContainsString('お客様管理番号', $csv);
        $this->assertCount(1, preg_split('/\r\n/', rtrim($csv, "\r\n")));
    }

    public function test_注文詳細の購入者情報にボタンがあり未ログインでは出力できない(): void
    {
        $order = $this->makeOrder();
        $this->get(route('admin.orders.show', $order))
            ->assertSee(route('admin.orders.b2csv', $order), false)
            ->assertSee('B2クラウド用CSV');

        auth('admin')->logout();
        $this->get(route('admin.orders.b2csv', $order))->assertRedirect(route('admin.login'));
    }

    public function test_ご依頼主は店舗情報の値を優先し空の項目だけenvを使う(): void
    {
        StoreInfo::current()->update([
            'sender_name'     => '山一醤油 本店',
            'sender_phone'    => '03-1111-2222',
            'sender_postal'   => '150-0041',
            'sender_address'  => '東京都渋谷区神南１－１－１',
            'sender_building' => null,
        ]);
        config(['yamato.b2.sender.building' => 'ENVビル']);

        [, $row] = $this->parse($this->get(route('admin.orders.b2csv', $this->makeOrder()))->getContent());

        $this->assertSame('山一醤油 本店', $row[25]);
        $this->assertSame('03-1111-2222', $row[20]);
        $this->assertSame('150-0041', $row[22]);
        $this->assertSame('東京都渋谷区神南１－１－１', $row[23]);
        $this->assertSame('ENVビル', $row[24]); // 店舗情報が空なので .env の値
    }
}
