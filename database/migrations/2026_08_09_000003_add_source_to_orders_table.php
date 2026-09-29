<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('orders', 'source')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('source', 50)->default('website')->after('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('orders', 'source')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('source');
            });
        }
    }
};
