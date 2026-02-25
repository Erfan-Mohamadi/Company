<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();

            // Translatable fields (JSON)
            $table->json('title')->nullable()->comment('Slider title per language');
            $table->json('subtitle')->nullable()->comment('Slider subtitle per language');
            $table->json('description')->nullable()->comment('Slider description per language');
            $table->json('link_text')->nullable()->comment('Button label per language');

            // Non-translatable
            $table->string('link_url')->nullable();
            $table->string('button_style')->default('primary')->comment('primary, secondary, outline, ghost');
            $table->string('video_url')->nullable()->comment('Optional video URL, overrides image');
            $table->string('animation_type')->default('fade')->comment('fade, slide, zoom, none');
            $table->unsignedInteger('display_duration')->default(5000)->comment('Milliseconds');

            // Scheduling
            $table->timestamp('start_date')->nullable()->comment('Null = always active');
            $table->timestamp('end_date')->nullable();

            // Display
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');

            // Note: images managed via Spatie Media Library (collection: slider_media)

            $table->timestamps();

            $table->index(['order', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
