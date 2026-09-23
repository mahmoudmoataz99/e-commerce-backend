<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // NOTE: this is a simple/fake payment methods table just for demo purposes
    // it does NOT store real card numbers (that would be super unsafe / illegal (PCI compliance))
    // in a real app you would use something like Stripe and only store the token they give you
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('label'); // "Visa", "Mastercard", "Apple Pay"
            $table->string('last4')->nullable(); // just last 4 digits for display, e.g "4242"
            $table->string('type')->default('card'); // card or apple
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
