<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("orders", function (Blueprint $table) {
            // 売上管理（決済日時での期間集計）のために追加。決済ステータス自体は既存の payment_status を使用する
            $table->timestamp("paid_at")->nullable()->after("payment_status");
        });

        // 既存の支払済み注文は、決済日時の情報を保持していないため updated_at で近似的に補完する
        DB::table("orders")
            ->where("payment_status", "paid")
            ->whereNull("paid_at")
            ->update(["paid_at" => DB::raw("updated_at")]);
    }

    public function down(): void
    {
        Schema::table("orders", function (Blueprint $table) {
            $table->dropColumn("paid_at");
        });
    }
};
