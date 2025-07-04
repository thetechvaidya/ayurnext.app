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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number', 20)->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone_number');
            $table->unsignedInteger('level')->default(1)->after('avatar');
            $table->unsignedInteger('experience_points')->default(0)->after('level');
            $table->unsignedInteger('daily_streak')->default(0)->after('experience_points');
            $table->date('last_quiz_date')->nullable()->after('daily_streak');
            
            // Add indexes for performance
            $table->index('level');
            $table->index('experience_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['level']);
            $table->dropIndex(['experience_points']);
            $table->dropColumn([
                'phone_number',
                'avatar',
                'level',
                'experience_points',
                'daily_streak',
                'last_quiz_date'
            ]);
        });
    }
};
