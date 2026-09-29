<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('notifications')) {
            if (!Schema::hasColumn('notifications', 'notifiable_type')) {
                if (Schema::hasTable('old_custom_notifications')) {
                    Schema::dropIfExists('notifications');
                } else {
                    Schema::rename('notifications', 'old_custom_notifications');
                }
            }
        }

        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->index(['notifiable_id', 'notifiable_type', 'read_at']);
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
