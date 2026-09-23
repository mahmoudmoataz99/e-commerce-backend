<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // this is the human friendly order number shown in the app, like "ORD-12345678"
            $table->string('order_number')->unique();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            // just storing the address/payment as plain text snapshot
            // so if user deletes their address later, the order history still shows it
            $table->string('address');
            $table->string('payment_method');
            $table->string('promo_code')->nullable();
            $table->string('status')->default('processing'); // processing, shipped, delivered
            $table->timestamps();
        });

        // each order has a bunch of items in it, this is basically a copy
        // of the cart items at the time the order was placed
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            // saving name/image/price here too (snapshot) in case the product
            // gets changed or deleted later, order history should stay accurate
            $table->string('product_name');
            $table->string('product_image')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('quantity')->default(1);
            $table->string('selected_size')->nullable();
            $table->string('selected_color')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
