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
            // Referral fields
            $table->string('referral_code', 20)->unique()->nullable()->after('credit_balance');
            $table->integer('referral_used_count')->default(0)->after('referral_code');
            $table->timestamp('last_referral_reset')->nullable()->after('referral_used_count');
            $table->foreignId('referred_by')->nullable()->after('last_referral_reset')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn(['referral_code', 'referral_used_count', 'last_referral_reset', 'referred_by']);       
        });
    }
};