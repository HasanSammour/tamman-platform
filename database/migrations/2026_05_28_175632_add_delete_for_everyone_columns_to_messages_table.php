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
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('is_deleted_for_everyone')->default(false)->after('is_deleted_by_receiver');
            $table->timestamp('deleted_for_everyone_at')->nullable()->after('is_deleted_for_everyone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['is_deleted_for_everyone', 'deleted_for_everyone_at']);
        });
    }
};
