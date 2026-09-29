<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'meta_keywords')) {
                $table->text('meta_keywords')->nullable()->after('meta_description');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'meta_keywords')) {
                $table->text('meta_keywords')->nullable()->after('meta_description');
            }
        });

        Schema::table('product_variants', function (Blueprint $table) {
            if (!Schema::hasColumn('product_variants', 'cost')) {
                $table->decimal('cost', 10, 2)->nullable()->after('price');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'cost')) {
                $table->decimal('cost', 10, 2)->default(0)->after('price');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'courier_settlement_status')) {
                $table->string('courier_settlement_status')->default('unsettled')->after('payment_status');
            }
            if (!Schema::hasColumn('orders', 'courier_settled_at')) {
                $table->timestamp('courier_settled_at')->nullable()->after('courier_settlement_status');
            }
            if (!Schema::hasColumn('orders', 'courier_settled_by_id')) {
                $table->foreignId('courier_settled_by_id')->nullable()->constrained('users')->nullOnDelete()->after('courier_settled_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'meta_keywords')) {
                $table->dropColumn('meta_keywords');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'meta_keywords')) {
                $table->dropColumn('meta_keywords');
            }
        });

        Schema::table('product_variants', function (Blueprint $table) {
            if (Schema::hasColumn('product_variants', 'cost')) {
                $table->dropColumn('cost');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'cost')) {
                $table->dropColumn('cost');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'courier_settled_by_id')) {
                $table->dropForeign(['courier_settled_by_id']);
                $table->dropColumn('courier_settled_by_id');
            }
            if (Schema::hasColumn('orders', 'courier_settled_at')) {
                $table->dropColumn('courier_settled_at');
            }
            if (Schema::hasColumn('orders', 'courier_settlement_status')) {
                $table->dropColumn('courier_settlement_status');
            }
        });
    }
};
