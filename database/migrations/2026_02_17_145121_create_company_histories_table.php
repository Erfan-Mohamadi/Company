<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_histories', function (Blueprint $table) {
            $table->id();
            $table->json('title')->nullable()->comment('Title per language');
            $table->json('description')->nullable()->comment('Rich description per language');
            $table->unsignedSmallInteger('year')->nullable()->comment('e.g. 2005');
            $table->unsignedTinyInteger('month')->nullable()->comment('1-12, optional');
            $table->string('achievement_type')->nullable()->comment('founding|product_launch|expansion|award|partnership|other');
            $table->string('icon')->nullable()->comment('Heroicon name');
            // Note: image managed via Spatie Media Library
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->index(['year', 'month', 'achievement_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_histories');
    }
};
