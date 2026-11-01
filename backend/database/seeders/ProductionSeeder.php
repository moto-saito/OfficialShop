<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * 本番用シーダー。起動のたびに実行されるため冪等にしている。
 *
 * - テストユーザーは作らない
 * - 管理者は環境変数 ADMIN_EMAIL / ADMIN_PASSWORD から「存在しない場合のみ」作成する
 *   （作成後に管理画面などでパスワードを変えても上書きしない）
 */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || ! $password) {
            $this->command?->warn('ADMIN_EMAIL / ADMIN_PASSWORD が未設定のため管理者は作成しません。');

            return;
        }

        Admin::firstOrCreate(
            ['email' => $email],
            [
                'name'     => env('ADMIN_NAME', '管理者'),
                'password' => Hash::make($password),
            ]
        );
    }
}
