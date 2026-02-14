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
        Schema::create('lang_website_keys', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Translation key identifier');
            $table->json('value')->nullable()->comment('Translations for all languages');
            $table->string('group')->nullable()->comment('Optional grouping for keys');
            $table->boolean('message')->default(false)->comment('Is this a user-facing message');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Indexes
            $table->index('key');
            $table->index('group');
            $table->index('message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lang_website_keys');
    }
};
