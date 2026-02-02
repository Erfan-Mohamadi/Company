<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->index();           // hero, about, contact, etc.
            $table->string('name')->unique();           // hero_title, hero_image, etc.
            $table->string('label');                    // نمایش به کاربر
            $table->string('type');                     // text, textarea, number, image, video
            $table->longText('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
