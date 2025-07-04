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
        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('topic_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedInteger('total_questions_attempted')->default(0);
            $table->unsignedInteger('correct_answers')->default(0);
            $table->decimal('accuracy_percentage', 5, 2)->default(0.00);
            $table->timestamp('last_attempted_at')->nullable();
            $table->timestamps();
            
            // Add unique constraint for user-subject-topic combination
            $table->unique(['user_id', 'subject_id', 'topic_id']);
            
            // Add indexes
            $table->index('user_id');
            $table->index('subject_id');
            $table->index('topic_id');
            $table->index('accuracy_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_progress');
    }
};
