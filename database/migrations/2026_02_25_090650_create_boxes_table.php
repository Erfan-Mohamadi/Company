<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boxes', function (Blueprint $table) {
            $table->id();

            // Translatable fields (JSON)
            $table->json('header')->nullable()->comment('Box header per language');
            $table->json('description')->nullable()->comment('Box description per language');

            // Non-translatable
            $table->string('link_url')->nullable();
            $table->string('box_type')->default('icon')->comment('icon, image, icon+image');
            $table->string('icon')->nullable()->comment('Heroicon name or SVG class');
            $table->string('background_color')->nullable()->comment('Hex color value');
            $table->string('text_color')->nullable()->comment('Hex color value');

            // Display
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');

            // Note: images managed via Spatie Media Library (collection: box_images)

            $table->timestamps();

            $table->index(['order', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boxes');
    }
};
