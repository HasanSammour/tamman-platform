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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('phone');
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->text('profile_image')->nullable()->after('date_of_birth');
            $table->boolean('is_active')->default(true)->after('profile_image');
            $table->integer('total_points')->default(0)->after('is_active');
            $table->decimal('credit_balance', 10, 2)->default(0.00)->after('total_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'gender', 'date_of_birth', 'profile_image',
                'is_active', 'total_points', 'credit_balance'
            ]);
        });
    }
};
