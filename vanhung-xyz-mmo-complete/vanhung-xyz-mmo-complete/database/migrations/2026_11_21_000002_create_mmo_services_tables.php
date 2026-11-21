<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('mmo_services')) {
            Schema::create('mmo_services', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->enum('service_type', ['seeding', 'ads_agency', 'tool_setup'])->default('seeding');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('mmo_service_packages')) {
            Schema::create('mmo_service_packages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('mmo_service_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->unsignedBigInteger('unit_price');
                $table->string('unit_label', 30)->default('đơn vị');
                $table->unsignedInteger('min_qty')->default(1);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('mmo_service_orders')) {
            Schema::create('mmo_service_orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_code')->unique();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('mmo_service_package_id')->constrained()->cascadeOnDelete();
                $table->string('target_link');
                $table->unsignedInteger('quantity');
                $table->unsignedBigInteger('total_amount');
                $table->enum('status', ['processing', 'success', 'error'])->default('processing');
                $table->text('user_note')->nullable();
                $table->text('admin_note')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mmo_service_orders');
        Schema::dropIfExists('mmo_service_packages');
        Schema::dropIfExists('mmo_services');
    }
};
