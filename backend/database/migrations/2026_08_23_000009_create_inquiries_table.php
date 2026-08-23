<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("inquiries", function (Blueprint $table) {
            $table->id();

            // ログイン会員からの問い合わせの場合に紐付くユーザー（未ログインの問い合わせも許容するため nullable）
            $table->foreignId("user_id")->nullable()->constrained()->nullOnDelete();

            $table->string("name");
            $table->string("email");

            // 問い合わせ種別・ステータスは今後の選択肢追加に対応しやすいよう enum ではなく string で保持する
            // 内部値は App\Models\Inquiry::TYPES / STATUSES を参照
            $table->string("type");
            $table->string("subject");
            $table->text("body");
            $table->string("status")->default("unread");

            $table->timestamps();

            $table->index("status");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("inquiries");
    }
};
