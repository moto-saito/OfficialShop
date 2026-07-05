<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // 会員注文の場合に紐付くユーザー（将来的にゲスト注文も許容できるよう nullable）
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('order_number')->unique(); // ORD-YYYYMMDD-XXXXXXXX 形式

            $table->unsignedInteger('total_price');

            // 注文ステータス
            $table->enum('status', ['pending', 'processing', 'shipped', 'completed', 'cancelled'])
                  ->default('pending');

            // 決済ステータス（決済機能は別途実装）
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');

            // 配送先情報（注文時点の情報をスナップショットとして保存）
            $table->string('recipient_name');
            $table->string('postal_code', 8);
            $table->string('prefecture', 10);
            $table->string('address');
            $table->string('phone_number', 20);
            $table->string('email');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
