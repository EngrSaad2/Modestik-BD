<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Extend users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->enum('status', ['active', 'inactive', 'banned'])->default('active')->after('avatar');
            $table->softDeletes();
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label')->default('Home');
            $table->string('name');
            $table->string('phone');
            $table->string('division')->nullable();
            $table->string('district')->nullable();
            $table->string('area')->nullable();
            $table->text('address');
            $table->string('zip')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'avatar', 'status']);
            $table->dropSoftDeletes();
        });
    }
};
