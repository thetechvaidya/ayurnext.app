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
        Schema::create('daily_quizzes', function (Blueprint $table) {
            $table->id();
            $table->date('quiz_date')->unique();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->time('release_time')->default('06:00:00');
            $table->boolean('is_released')->default(false);
            $table->timestamps();
            
            // Add indexes
            $table->index('quiz_date');
            $table->index('quiz_id');
            $table->index('is_released');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_quizzes');
    }
};
