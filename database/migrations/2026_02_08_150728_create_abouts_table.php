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

            // ─── Translatable fields (stored as JSON) ───────────────────────────────
            $table->json('header')->nullable()->comment('Company header / main title per language');
            $table->json('description')->nullable()->comment('Main company description per language');
            $table->json('founder_name')->nullable()->comment('Name of the founder per language');
            $table->json('mission')->nullable()->comment('Mission statement per language');
            $table->json('vision')->nullable()->comment('Vision statement per language');
            $table->json('founder_message')->nullable()->comment('Personal message from founder per language');

            // ─── JSON arrays (can also be translated if you want, but often kept shared) ──
            $table->json('core_values')->nullable()->comment('Array of core values (can be translated or shared)');
            $table->json('extra')->nullable()->comment('Flexible key-value metadata (can be per-language or global)');

            // ─── Non-translatable / shared fields ────────────────────────────────────
            $table->date('founded_date')->nullable()->comment('Company founding date (Gregorian)');
            $table->integer('employees_count')->default(0)->comment('Number of employees');
            $table->integer('locations_count')->default(0)->comment('Number of office locations / branches');
            $table->integer('clients_count')->default(0)->comment('Number of clients / customers');

            $table->string('status')->default('draft')->comment('draft | published');

            $table->timestamps();

            // Indexes that might be useful
            $table->index('status');
            $table->index('founded_date');
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
