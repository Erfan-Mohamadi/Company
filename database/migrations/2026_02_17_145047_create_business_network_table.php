<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ---- customers ----
        // Note: logo managed via Spatie Media Library
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->json('name')->nullable();
            $table->json('testimonial_text')->nullable();
            $table->json('project_description')->nullable();
            $table->json('author_name')->nullable();
            $table->json('author_position')->nullable();
            $table->string('website_url')->nullable();
            $table->string('industry')->nullable();
            $table->string('country')->nullable();
            $table->boolean('featured')->default(false);
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->index(['featured', 'industry', 'status']);
        });

        // ---- partners ----
        // Note: logo managed via Spatie Media Library
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->json('partner_name')->nullable();
            $table->json('description')->nullable();
            $table->string('website_url')->nullable();
            $table->string('partnership_type')->nullable()->comment('technology|distribution|strategic|other');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('featured')->default(false);
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->index(['partnership_type', 'featured', 'status']);
        });

        // ---- suppliers ----
        // Note: logo managed via Spatie Media Library
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->json('supplier_name')->nullable();
            $table->string('supply_category')->nullable();
            $table->string('website_url')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->unsignedTinyInteger('rating')->nullable()->comment('1-5 stars');
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->index(['supply_category', 'country', 'status']);
        });

        // ---- dealers ----
        // Note: logo managed via Spatie Media Library
        Schema::create('dealers', function (Blueprint $table) {
            $table->id();
            $table->json('dealer_name')->nullable();
            $table->json('territory')->nullable();
            $table->string('dealer_code')->nullable()->unique();
            $table->string('website_url')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->unsignedTinyInteger('rating')->nullable()->comment('1-5 stars');
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->index(['country', 'status', 'contract_end_date']);
        });

        // ---- export_markets ----
        // ExportMarket has no media
        Schema::create('export_markets', function (Blueprint $table) {
            $table->id();
            $table->json('country_name')->nullable();
            $table->json('region')->nullable();
            $table->string('country_code', 10)->nullable();
            $table->string('continent')->nullable();
            $table->unsignedBigInteger('export_volume')->nullable()->comment('Units exported');
            $table->decimal('export_value', 15, 2)->nullable()->comment('USD value');
            $table->json('main_products')->nullable();
            $table->unsignedInteger('distributors_count')->default(0);
            $table->unsignedSmallInteger('start_year')->nullable();
            $table->decimal('growth_rate', 5, 2)->nullable()->comment('Percentage');
            $table->json('map_coordinates')->nullable()->comment('{lat, lng}');
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->index(['continent', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('export_markets');
        Schema::dropIfExists('dealers');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('customers');
    }
};
