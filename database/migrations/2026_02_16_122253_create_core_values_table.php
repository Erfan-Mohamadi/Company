<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core_values', function (Blueprint $table) {
            $table->id();

            // Translatable
            $table->json('value_name')->nullable();
            $table->json('description')->nullable();

            // Shared
            $table->string('icon')->nullable()->comment('Heroicon name or custom icon class');
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');

            $table->timestamps();

            $table->index(['order', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core_values');
    }
};
