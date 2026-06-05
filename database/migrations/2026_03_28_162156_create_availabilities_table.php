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
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialist_id')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('day_of_week')->nullable()->comment('0-6: Sunday-Saturday');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_recurring')->default(true);
            $table->date('specific_date')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            
            $table->index(['specialist_id', 'day_of_week']);
            $table->index('specific_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availabilities');
    }
};
