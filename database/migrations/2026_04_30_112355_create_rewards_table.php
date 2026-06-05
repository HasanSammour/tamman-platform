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
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // {"en": "Reward Name", "ar": "اسم المكافأة"}
            $table->integer('points_needed');
            $table->enum('type', ['credit', 'free_session', 'donate']);
            $table->enum('session_type', ['video', 'audio', 'text'])->nullable();
            $table->decimal('credit_amount', 10, 2)->nullable();
            $table->json('description')->nullable(); // {"en": "Description", "ar": "الوصف"}
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
