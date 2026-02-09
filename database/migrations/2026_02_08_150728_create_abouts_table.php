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
        Schema::create('abouts', function (Blueprint $table) {
            $table->id();
            $table->string('header');
            $table->longText('description')->nullable();
            $table->year('founded_year')->nullable();
            $table->string('founder_name')->nullable();
            $table->text('mission')->nullable();
            $table->text('vision')->nullable();
            $table->json('core_values')->nullable();           // repeater data
            $table->integer('employees_count')->default(0);
            $table->integer('locations_count')->default(0);
            $table->integer('clients_count')->default(0);
            $table->longText('founder_message')->nullable();
            $table->string('status')->default('draft');        // draft | published
            $table->json('extra')->nullable();                 // extra data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abouts');
    }
};
