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
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedInteger('points_required')->default(0);
            $table->string('badge_color', 7)->default('#FFD700');
            $table->enum('category', ['progress', 'performance', 'consistency', 'milestone'])->default('progress');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Add indexes
            $table->index('name');
            $table->index('category');
            $table->index('points_required');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
