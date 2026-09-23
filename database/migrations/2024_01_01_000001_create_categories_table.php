<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // categories are things like Tops, Bottoms, Footwear etc
    // this matches the CATEGORIES array that was hardcoded in the app
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            // slug is the short id like "tops", "bottoms" - used in the app already
            $table->string('slug')->unique();
            $table->string('name');
            // icon name so the frontend knows which lucide icon to show
            $table->string('icon')->default('Sparkles');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
