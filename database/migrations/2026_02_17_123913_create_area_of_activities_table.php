<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('area_of_activities', function (Blueprint $table) {
            $table->id();

            // Translatable fields (JSON)
            $table->json('title')->nullable()->comment('Area title per language');
            $table->json('short_description')->nullable()->comment('Short description per language (max 200 chars)');
            $table->json('description')->nullable()->comment('Full rich description per language');
            $table->json('meta_description')->nullable()->comment('SEO meta description per language');

            // Non-translatable
            $table->string('slug')->unique()->nullable();
            $table->string('icon')->nullable()->comment('Heroicon name or SVG class');
            $table->json('industries')->nullable()->comment('List of industries [{name, description}]');
            // Note: images managed via Spatie Media Library (no image column needed)
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');

            $table->timestamps();

            $table->index(['order', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area_of_activities');
    }
};
