<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("store_infos", function (Blueprint $table) {
            $table->id();
            $table->string("title")->default("店舗・工場紹介");
            $table->text("description")->nullable();
            $table->string("image_path")->nullable();
            $table->string("address")->nullable();
            $table->string("business_hours")->nullable();
            $table->text("access")->nullable();
            $table->string("map_image_path")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("store_infos");
    }
};
