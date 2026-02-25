<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_logos', function (Blueprint $table) {
            $table->id();

            // Translatable fields (JSON)
            $table->json('name')->nullable()->comment('Client / partner name per language');
            $table->json('description')->nullable()->comment('Short description per language');

            // Non-translatable
            $table->string('type')->default('client')->comment('client, partner, sponsor, supplier, distributor');
            $table->string('website_url')->nullable();
            $table->boolean('featured')->default(false);

            // Display
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');

            // Note: logo managed via Spatie Media Library (collection: client_logo)

            $table->timestamps();

            $table->index(['order', 'status']);
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_logos');
    }
};
