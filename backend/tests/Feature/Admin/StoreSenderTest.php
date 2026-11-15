<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\StoreInfo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreSenderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(Admin::create(['name' => '管理者', 'email' => 'admin@example.com', 'password' => 'password']), 'admin');
    }

    private function payload(array $sender): array
    {
        return array_merge(['title' => '店舗・工場紹介'], $sender);
    }

    public function test_店舗情報の画面にご依頼主の項目がある(): void
    {
        $this->get(route('admin.store.edit'))
            ->assertOk()
            ->assertSee('送り状のご依頼主（ヤマト運輸 B2クラウド）')
            ->assertSee('name="sender_name"', false)
            ->assertSee('name="sender_phone"', false)
            ->assertSee('name="sender_postal"', false)
            ->assertSee('name="sender_address"', false)
            ->assertSee('name="sender_building"', false);
    }

    public function test_ご依頼主を保存でき電話番号と郵便番号は半角に揃える(): void
    {
        $this->put(route('admin.store.update'), $this->payload([
            'sender_name'     => '山一醤油製造所',
            'sender_phone'    => '０３－１２３４－５６７８',
            'sender_postal'   => '１５００００１',
            'sender_address'  => '東京都渋谷区神宮前１－１－１',
            'sender_building' => '山一ビル２Ｆ',
        ]))->assertRedirect(route('admin.store.edit'))->assertSessionHasNoErrors();

        $store = StoreInfo::current();
        $this->assertSame('山一醤油製造所', $store->sender_name);
        $this->assertSame('03-1234-5678', $store->sender_phone);
        $this->assertSame('150-0001', $store->sender_postal);
        $this->assertSame('東京都渋谷区神宮前１－１－１', $store->sender_address);
        $this->assertSame('山一ビル２Ｆ', $store->sender_building);
    }

    public function test_送り状に収まらない長さや形式はエラー(): void
    {
        $this->put(route('admin.store.update'), $this->payload([
            'sender_name'    => str_repeat('長', 17),          // 全角17文字
            'sender_phone'   => '03-1234-5678 内線1',
            'sender_postal'  => '12345',
            'sender_address' => str_repeat('住', 33),          // 全角33文字
            'sender_building' => 'ビル😀',
        ]))->assertSessionHasErrors([
            'sender_name'     => 'ご依頼主名は全角16文字（半角32文字）以内で入力してください。',
            'sender_phone'    => '電話番号は数字とハイフンで入力してください。',
            'sender_postal'   => '郵便番号は7桁（例: 123-4567）で入力してください。',
            'sender_address'  => '住所は全角32文字（半角64文字）以内で入力してください。',
            'sender_building' => '建物名に送り状で使えない文字（絵文字・一部の旧字体など）が含まれています。',
        ]);
    }

    public function test_空欄でも保存できる(): void
    {
        $this->put(route('admin.store.update'), $this->payload([]))->assertSessionHasNoErrors();
        $this->assertNull(StoreInfo::current()->sender_name);
    }
}
