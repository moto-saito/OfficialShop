<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 商品画像を複数持てるようにする。
     * products.image_path は「1枚目（メイン画像）」として残し、一覧・カート等はそのまま使う。
     */
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'sort_order']);
        });

        // 既存の1枚画像を product_images に移す
        $now = now();
        DB::table('products')->whereNotNull('image_path')->orderBy('id')->each(function ($product) use ($now) {
            DB::table('product_images')->insert([
                'product_id' => $product->id,
                'path'       => $product->image_path,
                'sort_order' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
