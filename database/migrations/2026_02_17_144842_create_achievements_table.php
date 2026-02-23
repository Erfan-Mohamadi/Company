<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ---- certifications ----
        // Note: certificate_image and certificate_file managed via Spatie Media Library
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->json('title')->nullable();
            $table->json('description')->nullable();
            $table->string('certification_body')->nullable();
            $table->string('certificate_number')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('verification_url')->nullable();
            $table->boolean('featured')->default(false);
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->index(['featured', 'status', 'expiry_date']);
        });

        // ---- licenses ----
        // Note: license_file managed via Spatie Media Library
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->json('license_name')->nullable();
            $table->json('description')->nullable();
            $table->string('license_number')->nullable()->unique();
            $table->string('license_type')->nullable();
            $table->json('issuing_authority')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->index(['license_type', 'status', 'expiry_date']);
        });

        // ---- awards ----
        // Note: image and certificate_file managed via Spatie Media Library
        Schema::create('awards', function (Blueprint $table) {
            $table->id();
            $table->json('title')->nullable();
            $table->json('description')->nullable();
            $table->json('awarding_body')->nullable();
            $table->date('award_date')->nullable();
            $table->json('category')->nullable();
            $table->boolean('featured')->default(false);
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->index(['featured', 'status']);
        });

        // ---- accreditations ----
        // Note: logo and certificate managed via Spatie Media Library
        Schema::create('accreditations', function (Blueprint $table) {
            $table->id();
            $table->json('organization_name')->nullable();
            $table->json('description')->nullable();
            $table->json('accreditation_type')->nullable();
            $table->string('membership_number')->nullable();
            $table->date('member_since')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('verification_url')->nullable();
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->index(['status', 'end_date']);
        });

        // ---- representation_letters ----
        // Note: document_file managed via Spatie Media Library
        Schema::create('representation_letters', function (Blueprint $table) {
            $table->id();
            $table->json('header')->nullable();
            $table->json('description')->nullable();
            $table->json('company_name')->nullable();
            $table->json('representative_name')->nullable();
            $table->json('territory')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->index(['status', 'expiry_date']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('representation_letters');
        Schema::dropIfExists('accreditations');
        Schema::dropIfExists('awards');
        Schema::dropIfExists('licenses');
        Schema::dropIfExists('certifications');
    }
};
