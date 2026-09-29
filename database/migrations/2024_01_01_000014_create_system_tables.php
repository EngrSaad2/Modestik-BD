<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->longText('value')->nullable();
                $table->string('group')->default('general');
                $table->timestamps();

                $table->index('group');
            });
        }

        if (!Schema::hasTable('media')) {
            Schema::create('media', function (Blueprint $table) {
                $table->id();
                $table->string('filename');
                $table->string('path');
                $table->string('disk')->default('public');
                $table->string('mime_type')->nullable();
                $table->unsignedBigInteger('size')->default(0);
                $table->string('alt')->nullable();
                $table->string('folder')->default('general');
                $table->timestamps();

                $table->index('folder');
            });
        }

        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('action');
                $table->string('model')->nullable();
                $table->unsignedBigInteger('model_id')->nullable();
                $table->json('data')->nullable();
                $table->string('ip')->nullable();
                $table->string('user_agent')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'created_at']);
                $table->index(['model', 'model_id']);
            });
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

        if (!Schema::hasTable('wallet_transactions')) {
            Schema::create('wallet_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->enum('type', ['credit', 'debit'])->default('credit');
                $table->decimal('amount', 10, 2);
                $table->decimal('balance', 10, 2)->default(0);
                $table->text('description')->nullable();
                $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
                $table->timestamps();

                $table->index('user_id');
            });
        }

        if (!Schema::hasTable('loyalty_points')) {
            Schema::create('loyalty_points', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->integer('points');
                $table->enum('type', ['earned', 'redeemed', 'expired'])->default('earned');
                $table->text('description')->nullable();
                $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
                $table->timestamps();

                $table->index('user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_points');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('media');
        Schema::dropIfExists('settings');
    }
};
