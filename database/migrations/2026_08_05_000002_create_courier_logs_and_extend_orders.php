<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'courier_name')) {
                $table->string('courier_name')->nullable()->after('shipping_zone_id');
            }
            if (!Schema::hasColumn('orders', 'consignment_id')) {
                $table->string('consignment_id')->nullable()->after('courier_name');
            }
            if (!Schema::hasColumn('orders', 'tracking_code')) {
                $table->string('tracking_code')->nullable()->after('consignment_id');
            }
            if (!Schema::hasColumn('orders', 'courier_status')) {
                $table->string('courier_status')->default('unassigned')->after('tracking_code');
            }
            if (!Schema::hasColumn('orders', 'last_courier_sync_at')) {
                $table->timestamp('last_courier_sync_at')->nullable()->after('courier_status');
            }
        });

        if (!Schema::hasTable('courier_logs')) {
            Schema::create('courier_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->string('courier_name')->default('steadfast');
                $table->string('action'); // e.g. parcel_create, track, balance_check, status_sync
                $table->json('request_payload')->nullable();
                $table->json('response_payload')->nullable();
                $table->integer('status_code')->nullable();
                $table->text('error_message')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['order_id', 'courier_name']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_logs');

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'last_courier_sync_at')) {
                $table->dropColumn('last_courier_sync_at');
            }
            if (Schema::hasColumn('orders', 'courier_status')) {
                $table->dropColumn('courier_status');
            }
            if (Schema::hasColumn('orders', 'tracking_code')) {
                $table->dropColumn('tracking_code');
            }
            if (Schema::hasColumn('orders', 'consignment_id')) {
                $table->dropColumn('consignment_id');
            }
            if (Schema::hasColumn('orders', 'courier_name')) {
                $table->dropColumn('courier_name');
            }
        });
    }
};
