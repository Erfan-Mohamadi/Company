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
            $table->date('date')->nullable();
            $table->string('achievement_type')->nullable()->comment('founding|product_launch|expansion|award|partnership|other');
            $table->string('icon')->nullable()->comment('Heroicon name');
            // Note: image managed via Spatie Media Library
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->index(['date', 'achievement_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_histories');
    }
};
