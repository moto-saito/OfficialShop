<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("orders", function (Blueprint $table) {
            // 注文時点の送料のスナップショット。total_price はこの送料を含んだ支払総額とする
            $table->unsignedInteger("shipping_fee")->default(0)->after("total_price");
        });
    }

    public function down(): void
    {
        Schema::table("orders", function (Blueprint $table) {
            $table->dropColumn("shipping_fee");
        });
    }
};
