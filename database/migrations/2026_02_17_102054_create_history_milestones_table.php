<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('history_milestones', function (Blueprint $table) {
            $table->id();

            // Translatable
            $table->json('title')->nullable();
            $table->json('description')->nullable();

            // Shared
            $table->year('year')->nullable();  // e.g. 2010
            $table->text('event_type')->nullable();  // founding, expansion, award, etc.
            $table->string('image')->nullable();  // optional image path or media ref
            $table->integer('order')->default(999);
            $table->string('status')->default('draft');

            $table->timestamps();

            $table->index(['year', 'order', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history_milestones');
    }
};
