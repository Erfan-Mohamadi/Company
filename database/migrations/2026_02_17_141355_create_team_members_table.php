<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();

            // Translatable
            $table->json('name')->nullable()->comment('Name per language');
            $table->json('position')->nullable()->comment('Position per language');
            $table->json('bio')->nullable()->comment('Bio per language (max 500 chars)');

            // Non-translatable
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('facebook_url')->nullable();
            // Note: image managed via Spatie Media Library
            $table->json('skills')->nullable()->comment('[{name, level}]');
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');

            $table->timestamps();

            $table->index(['department_id', 'order', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
