<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('why_choose_us', function (Blueprint $table) {
            $table->id();

            // Translatable fields
            $table->json('title')->nullable()->comment('Main title per language');
            $table->json('short_description')->nullable()->comment('Short intro/teaser per language');
            $table->json('items')->nullable()->comment('Array of advantage items with title & description per language');

            // Shared fields
            $table->string('icon')->nullable()->comment('Heroicon or custom icon for the section');
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');

            $table->timestamps();

            $table->index(['order', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('why_choose_us');
    }
};
