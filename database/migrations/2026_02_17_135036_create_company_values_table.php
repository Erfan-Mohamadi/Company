<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_values', function (Blueprint $table) {
            $table->id();

            // Translatable fields (JSON)
            $table->json('title')->nullable()->comment('Value title per language');
            $table->json('description')->nullable()->comment('Rich description per language');

            // Non-translatable
            $table->string('icon')->nullable()->comment('Heroicon name or SVG class');
            // Note: banner image managed via Spatie Media Library
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');

            $table->timestamps();

            $table->index(['order', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_values');
    }
};
