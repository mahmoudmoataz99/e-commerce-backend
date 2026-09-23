<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * These indexes were picked by reading every controller and matching
     * indexes to the actual WHERE / ORDER BY / JOIN patterns used, e.g.:
     *
     *  - Address/PaymentMethod/CartItem/Order/AppNotification controllers all
     *    do ->where('user_id', $request->user()->id) on every request
     *  - OrderController + NotificationController add ->orderBy('created_at', 'desc')
     *    on top of that user_id filter -> composite index serves both at once
     *  - ProductController does whereHas('category', fn q => $q->where('slug', ...))
     *    which joins products.category_id -> categories.id
     *  - ProductController does whereJsonContains('tags', $tag)
     *  - CartController does a 4-column lookup (user_id, product_id, size, color)
     *    to find an existing cart line before inserting a duplicate
     *
     * Note: foreignId()->constrained() only auto-creates an index on MySQL.
     * On Postgres and SQLite it just creates the FK constraint, not an index,
     * so several of these are "missing" indexes rather than duplicates.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // used by ProductController::index() whereHas('category', ...)
            $table->index('category_id', 'products_category_id_index');
        });

        Schema::table('addresses', function (Blueprint $table) {
            // every address query is scoped by ->where('user_id', ...)
            $table->index('user_id', 'addresses_user_id_index');
        });

        Schema::table('payment_methods', function (Blueprint $table) {
            $table->index('user_id', 'payment_methods_user_id_index');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            // CartController::store() looks up an existing row by
            // user_id + product_id (+ size/color), and CartController::index()
            // filters by user_id alone -> this composite covers both
            $table->index(['user_id', 'product_id'], 'cart_items_user_id_product_id_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            // OrderController::index() = where('user_id', ...)->orderBy('created_at', 'desc')
            $table->index(['user_id', 'created_at'], 'orders_user_id_created_at_index');
        });

        Schema::table('order_items', function (Blueprint $table) {
            // Order::with('items') triggers a WHERE order_id IN (...) for every order list/show
            $table->index('order_id', 'order_items_order_id_index');
            $table->index('product_id', 'order_items_product_id_index');
        });

        Schema::table('app_notifications', function (Blueprint $table) {
            // NotificationController::index() = where('user_id')->orderBy('created_at', 'desc')
            $table->index(['user_id', 'created_at'], 'app_notifications_user_id_created_at_index');
            // markAllRead() = where('user_id')->whereNull('read_at')
            $table->index(['user_id', 'read_at'], 'app_notifications_user_id_read_at_index');
        });

        // Postgres-only: tags is queried with whereJsonContains('tags', $tag).
        // A plain btree index can't be used for JSON containment lookups,
        // so on Postgres we switch the column to jsonb and add a GIN index.
        // (SQLite/MySQL just fall back to the JSON scan they already do -
        // there's no equivalent index type available for them here.)
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE products ALTER COLUMN tags TYPE jsonb USING tags::jsonb');
            DB::statement('CREATE INDEX products_tags_gin_index ON products USING GIN (tags)');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS products_tags_gin_index');
            DB::statement('ALTER TABLE products ALTER COLUMN tags TYPE json USING tags::json');
        }

        Schema::table('app_notifications', function (Blueprint $table) {
            $table->dropIndex('app_notifications_user_id_created_at_index');
            $table->dropIndex('app_notifications_user_id_read_at_index');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('order_items_order_id_index');
            $table->dropIndex('order_items_product_id_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_user_id_created_at_index');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex('cart_items_user_id_product_id_index');
        });

        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropIndex('payment_methods_user_id_index');
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->dropIndex('addresses_user_id_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_category_id_index');
        });
    }
};
