<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mission_visions', function (Blueprint $table) {
            $table->id();

            // ─── Translatable fields (JSON columns) ───────────────────────────────
            $table->json('header')
                ->nullable()
                ->comment('Main section header / title per language');

            $table->json('vision_title')
                ->nullable()
                ->comment('Vision section title per language');

            $table->json('vision_text')
                ->nullable()
                ->comment('Detailed vision statement per language');

            $table->json('mission_title')
                ->nullable()
                ->comment('Mission section title per language');

            $table->json('mission_text')
                ->nullable()
                ->comment('Detailed mission statement per language');

            $table->json('short_description')
                ->nullable()
                ->comment('Short summary / teaser per language');

            // ─── Non-translatable / shared fields ─────────────────────────────────
            $table->string('video_url')
                ->nullable()
                ->comment('Optional external video URL (YouTube/Vimeo/etc.)');

            $table->string('status')
                ->default('draft')
                ->comment('draft | published');

            $table->timestamps();

            // Useful indexes
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mission_visions');
    }
};
