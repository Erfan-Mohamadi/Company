<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('popups', function (Blueprint $table) {
            $table->id();

            // Translatable fields (JSON)
            $table->json('title')->nullable()->comment('Popup title per language');
            $table->json('content')->nullable()->comment('Popup rich content per language');

            // Non-translatable
            $table->string('popup_type')->default('announcement')->comment('announcement, newsletter, promotion, cookie_notice, age_gate');
            $table->string('trigger_type')->default('on_load')->comment('on_load, on_scroll, on_exit_intent, on_click, timed');
            $table->unsignedInteger('display_delay')->default(0)->comment('Delay in seconds before showing');
            $table->string('frequency')->default('once_per_session')->comment('once_per_session, once_per_day, once_per_week, always');
            $table->json('pages')->nullable()->comment('Array of URL patterns; null = show on all pages');

            // Scheduling
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();

            // Display
            $table->string('status')->default('draft');

            $table->timestamps();

            $table->index('status');
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('popups');
    }
};
