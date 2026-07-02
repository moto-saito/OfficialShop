<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 注文・配送先情報と連携するための住所系カラム
            $table->string('postal_code', 8)->nullable()->after('password');   // 例: 123-4567
            $table->string('prefecture', 10)->nullable()->after('postal_code'); // 例: 東京都
            $table->string('address')->nullable()->after('prefecture');          // 市区町村以降
            $table->string('phone_number', 20)->nullable()->after('address');   // 例: 090-1234-5678
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['postal_code', 'prefecture', 'address', 'phone_number']);
        });
    }
};
