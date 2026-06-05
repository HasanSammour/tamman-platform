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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            
            // Participants
            $table->foreignId('participant_one')->constrained('users')->onDelete('cascade');
            $table->foreignId('participant_two')->constrained('users')->onDelete('cascade');
            
            $table->foreignId('therapy_session_id')->nullable()->constrained('therapy_sessions')->nullOnDelete();
            $table->boolean('is_text_session')->default(false);

            // Add new lock columns
            $table->boolean('is_locked')->default(false);
            $table->timestamp('locked_at')->nullable();
            
            // Last message preview (denormalized for performance)
            $table->text('last_message')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->foreignId('last_message_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Unread counts (denormalized for performance)
            $table->integer('unread_count_p_one')->default(0);
            $table->integer('unread_count_p_two')->default(0);
            
            $table->timestamps();
            
            // Unique constraint to prevent duplicate conversations
            $table->unique(['participant_one', 'participant_two'], 'unique_conversation');
            
            // Indexes
            $table->index(['participant_one', 'is_locked']);
            $table->index(['participant_two', 'is_locked']);
            $table->index('last_message_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
