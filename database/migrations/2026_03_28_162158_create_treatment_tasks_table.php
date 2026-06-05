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
        Schema::create('treatment_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('treatment_plans')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->date('due_date')->nullable();
            $table->integer('points_reward')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['plan_id', 'is_completed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_tasks');
    }
};
