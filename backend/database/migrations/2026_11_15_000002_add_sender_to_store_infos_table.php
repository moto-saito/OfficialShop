<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** 送り状（ヤマト運輸 B2クラウド）のご依頼主＝発送元の情報 */
    public function up(): void
    {
        Schema::table('store_infos', function (Blueprint $table) {
            $table->string('sender_name')->nullable()->after('map_image_path');
            $table->string('sender_phone', 20)->nullable()->after('sender_name');
            $table->string('sender_postal', 8)->nullable()->after('sender_phone');
            $table->string('sender_address')->nullable()->after('sender_postal');
            $table->string('sender_building')->nullable()->after('sender_address');
        });
    }

    public function down(): void
    {
        Schema::table('store_infos', function (Blueprint $table) {
            $table->dropColumn(['sender_name', 'sender_phone', 'sender_postal', 'sender_address', 'sender_building']);
        });
    }
};
