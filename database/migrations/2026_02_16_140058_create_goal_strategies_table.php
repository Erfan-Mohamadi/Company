<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goal_strategies', function (Blueprint $table) {
            $table->id();

            // Translatable fields (JSON)
            $table->json('title')->nullable()->comment('Goal/Strategy title per language');
            $table->json('description')->nullable()->comment('Detailed explanation per language');
            $table->json('short_summary')->nullable()->comment('Short teaser / one-liner per language');

            // Shared / non-translatable
            $table->string('type')->default('goal')->comment('goal | strategy | objective | milestone');
            $table->integer('order')->default(0);
            $table->string('icon')->nullable()->comment('Heroicon name or SVG class');
            $table->string('status')->default('draft');

            $table->timestamps();

            $table->index(['type', 'order', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goal_strategies');
    }
};
