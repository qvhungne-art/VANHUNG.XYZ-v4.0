<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('mmo_categories')) {
            Schema::create('mmo_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('thumbnail')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('mmo_products')) {
            Schema::create('mmo_products', function (Blueprint $table) {
                $table->id();
                $table->foreignId('mmo_category_id')->constrained()->cascadeOnDelete();
                $table->string('sku')->unique();
                $table->string('title');
                $table->string('platform', 60);
                $table->string('country', 20)->default('VN');
                $table->string('format_spec');
                $table->string('warranty_policy');
                $table->unsignedBigInteger('price');
                $table->unsignedBigInteger('original_price')->nullable();
                $table->enum('status', ['available', 'sold_out', 'locked'])->default('available');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('mmo_purchases')) {
            Schema::create('mmo_purchases', function (Blueprint $table) {
                $table->id();
                $table->string('order_code')->unique();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('mmo_product_id')->constrained()->cascadeOnDelete();
                $table->unsignedInteger('quantity');
                $table->unsignedBigInteger('unit_price');
                $table->unsignedBigInteger('discount_amount')->default(0);
                $table->unsignedBigInteger('total_amount');
                $table->string('discount_code', 50)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('mmo_product_items')) {
            Schema::create('mmo_product_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('mmo_product_id')->constrained()->cascadeOnDelete();
                $table->foreignId('mmo_purchase_id')->nullable()->constrained('mmo_purchases')->nullOnDelete();
                $table->text('credential_payload');
                $table->enum('status', ['available', 'sold', 'locked'])->default('available');
                $table->foreignId('buyer_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('sold_at')->nullable();
                $table->timestamps();
                $table->index(['mmo_product_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mmo_product_items');
        Schema::dropIfExists('mmo_purchases');
        Schema::dropIfExists('mmo_products');
        Schema::dropIfExists('mmo_categories');
    }
};
