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
        Schema::table('credit_transactions', function (Blueprint $table) {
            // Allow donor_id to be NULL (for credit requests from patients)
            $table->foreignId('donor_id')->nullable()->change();
            
            // Allow recipient_id to be NULL (for donations from donors)
            $table->foreignId('recipient_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table->foreignId('donor_id')->nullable(false)->change();
        $table->foreignId('recipient_id')->nullable(false)->change();
    }
};
