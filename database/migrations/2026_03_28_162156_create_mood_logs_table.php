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
        Schema::create('mood_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('mood_value')->comment('1-10: 1=very low, 10=very high');
            $table->string('mood_label', 50);
            $table->text('notes')->nullable();
            $table->date('log_date');
            $table->timestamps();
            
            $table->unique(['user_id', 'log_date']);
            $table->index('log_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mood_logs');
    }
};
