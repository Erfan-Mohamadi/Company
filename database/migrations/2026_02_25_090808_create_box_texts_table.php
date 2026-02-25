<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('box_texts', function (Blueprint $table) {
            $table->id();

            // Translatable fields (JSON)
            $table->json('header')->nullable()->comment('Section header per language');
            $table->json('description')->nullable()->comment('Rich text description per language');

            // Display
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');

            $table->timestamps();

            $table->index(['order', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('box_texts');
    }
};
