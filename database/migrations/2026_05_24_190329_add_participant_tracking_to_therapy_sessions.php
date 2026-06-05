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
        Schema::table('therapy_sessions', function (Blueprint $table) {
            // Secure room name - long, random, unguessable (64 characters)
            $table->string('secure_room_name', 100)->nullable()->after('meeting_link');
            
            // Participant tracking
            $table->boolean('specialist_joined')->default(false)->after('secure_room_name');
            $table->boolean('patient_joined')->default(false)->after('specialist_joined');
            $table->timestamp('specialist_joined_at')->nullable()->after('patient_joined');
            $table->timestamp('patient_joined_at')->nullable()->after('specialist_joined_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('therapy_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'secure_room_name',
                'specialist_joined',
                'patient_joined',
                'specialist_joined_at',
                'patient_joined_at'
            ]);
        });
    }
};
