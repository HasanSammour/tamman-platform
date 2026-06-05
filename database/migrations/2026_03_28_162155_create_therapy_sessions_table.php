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
        Schema::create('therapy_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('specialist_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('session_datetime');
            $table->integer('duration_minutes')->default(60);
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled', 'no_show'])->default('scheduled');
            $table->enum('session_type', ['video', 'audio', 'text']);
            $table->string('meeting_link', 500)->nullable();
            $table->text('notes')->nullable();
            $table->integer('points_awarded')->default(0);
            $table->boolean('is_paid_by_credit')->default(false);
            $table->timestamps();
            
            $table->index(['session_datetime', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('therapy_sessions');
    }
};
