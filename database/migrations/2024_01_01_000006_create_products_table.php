<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tax_id')->nullable()->constrained('taxes')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique()->nullable();
            $table->string('barcode')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('min_order')->default(1);
            $table->integer('max_order')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_trending')->default(false);
            $table->boolean('is_flash_sale')->default(false);
            $table->decimal('flash_sale_price', 10, 2)->nullable();
            $table->timestamp('flash_sale_start')->nullable();
            $table->timestamp('flash_sale_end')->nullable();
            $table->boolean('has_variants')->default(false);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_image')->nullable();
            $table->json('structured_data')->nullable();
            $table->enum('status', ['active', 'inactive', 'draft'])->default('active');
            $table->unsignedBigInteger('views')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'status']);
            $table->index(['brand_id', 'status']);
            $table->index(['is_featured', 'status']);
            $table->index(['is_trending', 'status']);
            $table->index(['is_flash_sale', 'status']);
            $table->index('price');
            if (DB::getDriverName() === 'mysql') {
                $table->fullText(['name', 'short_description']);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
