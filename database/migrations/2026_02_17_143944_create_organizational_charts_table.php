<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizational_charts', function (Blueprint $table) {
            $table->id();

            // Translatable
            $table->json('description')->nullable()->comment('Description per language');

            // Non-translatable
            // Note: diagram image managed via Spatie Media Library
            $table->json('hierarchy_data')->nullable()->comment('Tree structure data for rendering');
            $table->string('status')->default('draft');

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizational_charts');
    }
};
