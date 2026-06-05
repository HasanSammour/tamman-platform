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
            // Add conversation_id column after id
            $table->foreignId('conversation_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            
            // Add other columns
            $table->boolean('is_system_message')->default(false)->after('content');
            $table->boolean('is_deleted_by_sender')->default(false)->after('is_read');
            $table->boolean('is_deleted_by_receiver')->default(false)->after('is_deleted_by_sender');
            $table->timestamp('edited_at')->nullable()->after('updated_at');
            
            // Indexes
            $table->index('conversation_id');
            $table->index(['conversation_id', 'created_at']);
            $table->index(['sender_id', 'is_deleted_by_sender']);
            $table->index(['receiver_id', 'is_deleted_by_receiver']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['conversation_id']);
            
            // Drop columns
            $table->dropColumn([
                'conversation_id',
                'is_system_message',
                'is_deleted_by_sender',
                'is_deleted_by_receiver',
                'edited_at'
            ]);
            
            // Note: Indexes are automatically dropped when columns are dropped
        });
    }
};
