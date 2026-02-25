<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();

            // Translatable fields (JSON)
            $table->json('customer_name')->nullable()->comment('Customer name per language');
            $table->json('customer_position')->nullable()->comment('Customer job title per language');
            $table->json('customer_company')->nullable()->comment('Company name per language');
            $table->json('testimonial_text')->nullable()->comment('Testimonial body per language');

            // Non-translatable
            $table->unsignedTinyInteger('rating')->default(5)->comment('1–5 stars');
            $table->string('video_url')->nullable()->comment('Optional video testimonial URL');
            $table->boolean('featured')->default(false);

            // Display
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');

            // Note: customer avatar managed via Spatie Media Library (collection: testimonial_avatars)

            $table->timestamps();

            $table->index(['order', 'status']);
            $table->index(['featured', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
