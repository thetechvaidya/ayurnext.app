<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user if it doesn't exist
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Run seeders in order (due to foreign key constraints)
        $this->call([
            SubjectSeeder::class,      // Create subjects first
            AchievementSeeder::class,  // Create achievements
            TopicSeeder::class,        // Create topics (depends on subjects)
            QuestionSeeder::class,     // Create questions (depends on subjects and topics)
            QuizSeeder::class,         // Create quizzes (depends on questions)
        ]);
    }
}
