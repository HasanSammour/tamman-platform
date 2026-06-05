<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('therapy_sessions', function (Blueprint $table) {
            $table->boolean('is_free')->default(false)->after('is_paid_by_credit');
            $table->foreignId('reward_redemption_id')->nullable()->after('is_free')->constrained('reward_redemptions')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('therapy_sessions', function (Blueprint $table) {
            $table->dropForeign(['reward_redemption_id']);
            $table->dropColumn(['is_free', 'reward_redemption_id']);
        });
    }
};
