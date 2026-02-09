<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mission_visions', function (Blueprint $table) {
            $table->id();
            $table->string('header')->nullable();
            $table->string('vision_title')->nullable();
            $table->longText('vision_text')->nullable();
            $table->string('mission_title')->nullable();
            $table->longText('mission_text')->nullable();
            $table->text('short_description')->nullable();
            $table->string('video_url')->nullable();
            $table->string('status')->default('draft'); // draft / published
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mission_visions');
    }
};
