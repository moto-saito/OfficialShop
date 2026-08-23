<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("company_achievements", function (Blueprint $table) {
            $table->id();
            $table->foreignId("company_info_id")->constrained()->cascadeOnDelete();
            $table->string("title");
            $table->text("description")->nullable();
            $table->unsignedInteger("sort_order")->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("company_achievements");
    }
};
