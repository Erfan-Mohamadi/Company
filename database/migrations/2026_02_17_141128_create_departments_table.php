<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();

            // Translatable
            $table->json('name')->nullable()->comment('Department name per language');
            $table->json('description')->nullable()->comment('Description per language');

            // Non-translatable
            $table->string('location')->nullable();
            $table->unsignedInteger('employee_count')->default(0);
            $table->string('head_name')->nullable();
            $table->string('head_email')->nullable();
            $table->string('head_phone')->nullable();
            // Note: image managed via Spatie Media Library
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');

            $table->timestamps();

            $table->index(['order', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
