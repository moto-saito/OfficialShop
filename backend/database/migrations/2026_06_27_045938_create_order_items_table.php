<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            // 商品が削除されても注文履歴が壊れないよう nullable + nullOnDelete
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();

            // 注文時点の商品情報をスナップショット保存（後から商品が変更されても履歴を保持）
            $table->string('product_name');
            $table->unsignedInteger('price');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('subtotal'); // price × quantity

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
