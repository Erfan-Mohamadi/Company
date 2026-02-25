<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statistics', function (Blueprint $table) {
            $table->id();

            // Translatable fields (JSON)
            $table->json('title')->nullable()->comment('Stat label per language, e.g. "Happy Clients"');
            $table->json('suffix')->nullable()->comment('Suffix per language, e.g. "+" or "Years"');
            $table->json('prefix')->nullable()->comment('Prefix per language, e.g. "$"');

            // Non-translatable
            $table->string('number')->comment('Stored as string to avoid scientific notation for large numbers');
            $table->string('icon')->nullable()->comment('Heroicon name or SVG class');
            $table->boolean('animation_enabled')->default(true)->comment('Count-up animation on scroll');

            // Display
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');

            $table->timestamps();

            $table->index(['order', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statistics');
    }
};
