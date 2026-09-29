<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('carts')) {
            Schema::table('carts', function (Blueprint $table) {
                if (!Schema::hasColumn('carts', 'customer_name')) {
                    $table->string('customer_name')->nullable()->after('coupon_id');
                }
                if (!Schema::hasColumn('carts', 'phone')) {
                    $table->string('phone')->nullable()->after('customer_name');
                }
                if (!Schema::hasColumn('carts', 'email')) {
                    $table->string('email')->nullable()->after('phone');
                }
                if (!Schema::hasColumn('carts', 'address')) {
                    $table->text('address')->nullable()->after('email');
                }
                if (!Schema::hasColumn('carts', 'division')) {
                    $table->string('division')->nullable()->after('address');
                }
                if (!Schema::hasColumn('carts', 'district')) {
                    $table->string('district')->nullable()->after('division');
                }
                if (!Schema::hasColumn('carts', 'thana')) {
                    $table->string('thana')->nullable()->after('district');
                }
                if (!Schema::hasColumn('carts', 'stage')) {
                    $table->string('stage')->default('cart_created')->after('thana');
                }
                if (!Schema::hasColumn('carts', 'crm_status')) {
                    $table->string('crm_status')->default('new')->after('stage');
                }
                if (!Schema::hasColumn('carts', 'assigned_staff_id')) {
                    $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->nullOnDelete()->after('crm_status');
                }
                if (!Schema::hasColumn('carts', 'recovered_order_id')) {
                    $table->foreignId('recovered_order_id')->nullable()->constrained('orders')->nullOnDelete()->after('assigned_staff_id');
                }
                if (!Schema::hasColumn('carts', 'recovery_token')) {
                    $table->string('recovery_token')->nullable()->unique()->after('recovered_order_id');
                }
                if (!Schema::hasColumn('carts', 'ip_address')) {
                    $table->string('ip_address')->nullable()->after('recovery_token');
                }
                if (!Schema::hasColumn('carts', 'user_agent')) {
                    $table->text('user_agent')->nullable()->after('ip_address');
                }
                if (!Schema::hasColumn('carts', 'traffic_source')) {
                    $table->string('traffic_source')->nullable()->after('user_agent');
                }
                if (!Schema::hasColumn('carts', 'last_activity_at')) {
                    $table->timestamp('last_activity_at')->nullable()->after('traffic_source');
                }
                if (!Schema::hasColumn('carts', 'abandoned_at')) {
                    $table->timestamp('abandoned_at')->nullable()->after('last_activity_at');
                }
                if (!Schema::hasColumn('carts', 'recovered_at')) {
                    $table->timestamp('recovered_at')->nullable()->after('abandoned_at');
                }
                if (!Schema::hasColumn('carts', 'recovered_by_id')) {
                    $table->foreignId('recovered_by_id')->nullable()->constrained('users')->nullOnDelete()->after('recovered_at');
                }
                if (!Schema::hasColumn('carts', 'notes')) {
                    $table->text('notes')->nullable()->after('recovered_by_id');
                }
            });
        }

        if (!Schema::hasTable('abandoned_cart_logs')) {
            Schema::create('abandoned_cart_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cart_id')->constrained('carts')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action');
                $table->text('description')->nullable();
                $table->timestamps();

                $table->index('cart_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('abandoned_cart_logs');

        if (Schema::hasTable('carts')) {
            Schema::table('carts', function (Blueprint $table) {
                $columns = [
                    'customer_name', 'phone', 'email', 'address', 'division', 'district', 'thana',
                    'stage', 'crm_status', 'assigned_staff_id', 'recovered_order_id', 'recovery_token',
                    'ip_address', 'user_agent', 'traffic_source', 'last_activity_at', 'abandoned_at',
                    'recovered_at', 'recovered_by_id', 'notes'
                ];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('carts', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
