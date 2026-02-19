<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leadership_team', function (Blueprint $table) {
            $table->id();

            // Translatable
            $table->json('name')->nullable()->comment('Name per language');
            $table->json('position')->nullable()->comment('Position/title per language');
            $table->json('short_bio')->nullable()->comment('Short bio per language (max 300 chars)');
            $table->json('long_bio')->nullable()->comment('Long rich bio per language');

            // Non-translatable
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('twitter_url')->nullable();
            // Note: image managed via Spatie Media Library
            $table->json('achievements')->nullable()->comment('[{title, year, description}]');
            $table->integer('order')->default(0);
            $table->boolean('featured')->default(false);
            $table->string('status')->default('draft');

            $table->timestamps();

            $table->index(['department_id', 'featured', 'order', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leadership_team');
    }
};
