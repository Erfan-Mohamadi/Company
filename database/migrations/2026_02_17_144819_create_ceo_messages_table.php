<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ceo_messages', function (Blueprint $table) {
            $table->id();

            // Translatable
            $table->json('title')->nullable()->comment('Message title per language');
            $table->json('message_text')->nullable()->comment('Rich message content per language');
            $table->json('ceo_name')->nullable()->comment('CEO name per language');
            $table->json('ceo_position')->nullable()->comment('CEO position per language');

            // Non-translatable
            // Note: ceo_image and ceo_signature managed via Spatie Media Library
            $table->string('video_url')->nullable()->comment('Video message URL');
            $table->string('status')->default('draft');

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ceo_messages');
    }
};
