<?php

use App\Models\ShippingFee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("shipping_fees", function (Blueprint $table) {
            $table->id();
            $table->string("prefecture", 10)->unique();
            $table->unsignedInteger("fee")->default(0);
            $table->timestamps();
        });

        // 47都道府県分の行を初期作成する（送料は管理画面から設定する）
        $now = now();
        DB::table("shipping_fees")->insert(
            collect(ShippingFee::PREFECTURES)
                ->map(fn ($prefecture) => [
                    "prefecture" => $prefecture,
                    "fee"        => 0,
                    "created_at" => $now,
                    "updated_at" => $now,
                ])
                ->all()
        );
    }

    public function down(): void
    {
        Schema::dropIfExists("shipping_fees");
    }
};
