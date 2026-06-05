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
        Schema::create('test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('test_type', ['phq9', 'gad7', 'pcl5', 'isi', 'pss', 'cis']);
            $table->integer('score');
            $table->string('result_level', 50);
            $table->json('answers')->nullable();
            $table->date('test_date');
            $table->timestamps();
            
            $table->index(['user_id', 'test_type']);
            $table->index('test_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_results');
    }
};
