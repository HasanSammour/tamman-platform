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
        Schema::create('specialist_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('license_number')->unique();
            $table->string('specialization');
            $table->text('qualifications');
            $table->text('bio')->nullable();
            $table->text('application_notes')->nullable();
            $table->decimal('consultation_fee', 10, 2);
            $table->string('languages')->nullable();
            $table->integer('experience_years')->nullable();
            $table->string('certificate_file')->nullable();                        
            $table->string('license_file')->nullable();
            $table->float('rating_avg')->default(0);
            $table->integer('total_sessions')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->enum('application_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('applied_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialist_profiles');
    }
};
