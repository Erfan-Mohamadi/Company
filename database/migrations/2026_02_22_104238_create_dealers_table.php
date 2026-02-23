<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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
            $table->string('partnership_type')->nullable()->comment('Authorized|Exclusive|General');
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->unsignedTinyInteger('rating')->nullable()->comment('1-5 stars');
            $table->integer('order')->default(0);
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->index(['country', 'status', 'contract_end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealers');
    }
};
