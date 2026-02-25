<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_to_actions', function (Blueprint $table) {
            $table->id();

            // Translatable fields (JSON)
            $table->json('title')->nullable()->comment('CTA title per language');
            $table->json('description')->nullable()->comment('CTA description per language');
            $table->json('button_text')->nullable()->comment('Button label per language');

            // Non-translatable
            $table->string('button_link')->nullable();
            $table->string('button_style')->default('primary')->comment('primary, secondary, outline, white');
            $table->string('background_color')->nullable()->comment('Hex color, used when no image');
            $table->unsignedTinyInteger('overlay_opacity')->default(50)->comment('0–100, applied over background image');

            // Display
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');

            // Note: background images managed via Spatie Media Library (collection: cta_backgrounds)

            $table->timestamps();

            $table->index(['order', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_to_actions');
    }
};
