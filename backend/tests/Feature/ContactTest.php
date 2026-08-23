<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    private function validInquiryData(array $overrides = []): array
    {
        return array_merge([
            'name' => '山田 太郎',
            'email' => 'taro@example.com',
            'type' => 'product',
            'subject' => '商品について質問があります',
            'body' => '商品の原材料について教えてください。',
        ], $overrides);
    }

    public function test_問い合わせフォームが表示される(): void
    {
        $response = $this->get(route('contact.index'));

        $response->assertStatus(200);
        $response->assertSee('お問い合わせ');
    }

    public function test_正常な問い合わせを送信すると確認画面を経て完了画面が表示される(): void
    {
        $data = $this->validInquiryData();

        $confirmResponse = $this->post(route('contact.confirm'), $data);
        $confirmResponse->assertStatus(200);
        $confirmResponse->assertSee('商品について質問があります');

        $storeResponse = $this->post(route('contact.store'));
        $storeResponse->assertRedirect(route('contact.complete'));

        $completeResponse = $this->get(route('contact.complete'));
        $completeResponse->assertStatus(200);
        $completeResponse->assertSee('お問い合わせを受け付けました');
    }

    public function test_入力エラーが正しく表示される(): void
    {
        $response = $this->post(route('contact.confirm'), [
            'name' => '',
            'email' => 'invalid-email',
            'type' => 'invalid-type',
            'subject' => '',
            'body' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'type', 'subject', 'body']);
    }

    public function test_問い合わせがDBに保存され初期ステータスが未読になる(): void
    {
        $data = $this->validInquiryData();

        $this->post(route('contact.confirm'), $data);
        $this->post(route('contact.store'));

        $this->assertDatabaseHas('inquiries', [
            'name' => $data['name'],
            'email' => $data['email'],
            'type' => $data['type'],
            'subject' => $data['subject'],
            'body' => $data['body'],
            'status' => Inquiry::STATUS_UNREAD,
            'user_id' => null,
        ]);
    }

    public function test_ログイン中のユーザーが送信するとuser_idが記録される(): void
    {
        $user = User::factory()->create();
        $data = $this->validInquiryData();

        $this->actingAs($user)->post(route('contact.confirm'), $data);
        $this->actingAs($user)->post(route('contact.store'));

        $this->assertDatabaseHas('inquiries', [
            'email' => $data['email'],
            'user_id' => $user->id,
        ]);
    }

    public function test_確認画面を経ずに直接送信すると入力画面へリダイレクトされる(): void
    {
        $response = $this->post(route('contact.store'));

        $response->assertRedirect(route('contact.index'));
        $this->assertDatabaseCount('inquiries', 0);
    }
}
