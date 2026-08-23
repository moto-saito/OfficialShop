<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryTest extends TestCase
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

    private function makeInquiry(array $overrides = []): Inquiry
    {
        return Inquiry::create(array_merge([
            'name' => '山田 太郎',
            'email' => 'taro@example.com',
            'type' => Inquiry::TYPE_PRODUCT,
            'subject' => '商品について質問があります',
            'body' => '商品の原材料について教えてください。',
            'status' => Inquiry::STATUS_UNREAD,
        ], $overrides));
    }

    public function test_管理者は問い合わせ一覧を確認できる(): void
    {
        $admin = $this->makeAdmin();
        $inquiry = $this->makeInquiry();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.inquiries.index'));

        $response->assertStatus(200);
        $response->assertSee($inquiry->subject);
    }

    public function test_管理者は問い合わせ詳細を確認できる(): void
    {
        $admin = $this->makeAdmin();
        $inquiry = $this->makeInquiry();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.inquiries.show', $inquiry));

        $response->assertStatus(200);
        $response->assertSee($inquiry->body);
        $response->assertSee($inquiry->email);
    }

    public function test_管理者はステータスを変更でき結果がDBに反映される(): void
    {
        $admin = $this->makeAdmin();
        $inquiry = $this->makeInquiry();

        $response = $this->actingAs($admin, 'admin')->patch(route('admin.inquiries.updateStatus', $inquiry), [
            'status' => Inquiry::STATUS_IN_PROGRESS,
        ]);

        $response->assertRedirect(route('admin.inquiries.show', $inquiry));
        $this->assertDatabaseHas('inquiries', [
            'id' => $inquiry->id,
            'status' => Inquiry::STATUS_IN_PROGRESS,
        ]);
    }

    public function test_不正なステータス値は登録できない(): void
    {
        $admin = $this->makeAdmin();
        $inquiry = $this->makeInquiry();

        $response = $this->actingAs($admin, 'admin')->patch(route('admin.inquiries.updateStatus', $inquiry), [
            'status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors(['status']);
        $this->assertDatabaseHas('inquiries', [
            'id' => $inquiry->id,
            'status' => Inquiry::STATUS_UNREAD,
        ]);
    }

    public function test_未認証ユーザーは問い合わせ一覧にアクセスできない(): void
    {
        $response = $this->get(route('admin.inquiries.index'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_未認証ユーザーは問い合わせ詳細にアクセスできない(): void
    {
        $inquiry = $this->makeInquiry();

        $response = $this->get(route('admin.inquiries.show', $inquiry));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_一般ユーザーは管理者ページへアクセスできない(): void
    {
        $user = User::factory()->create();
        $inquiry = $this->makeInquiry();

        $response = $this->actingAs($user)->get(route('admin.inquiries.index'));
        $response->assertRedirect(route('admin.login'));

        $response = $this->actingAs($user)->get(route('admin.inquiries.show', $inquiry));
        $response->assertRedirect(route('admin.login'));
    }
}
