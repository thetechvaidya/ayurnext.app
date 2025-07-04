<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Achievement;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $achievements = [
            [
                'name' => 'First Steps',
                'description' => 'Complete your first quiz',
                'icon' => 'first_quiz.png',
                'points_required' => 0,
                'badge_color' => '#FFD700',
                'category' => 'progress',
                'is_active' => true,
            ],
            [
                'name' => 'Quick Learner',
                'description' => 'Complete 10 quizzes',
                'icon' => 'quick_learner.png',
                'points_required' => 100,
                'badge_color' => '#C0C0C0',
                'category' => 'progress',
                'is_active' => true,
            ],
            [
                'name' => 'Dedicated Student',
                'description' => 'Complete 50 quizzes',
                'icon' => 'dedicated.png',
                'points_required' => 500,
                'badge_color' => '#CD7F32',
                'category' => 'progress',
                'is_active' => true,
            ],
            [
                'name' => 'Perfect Score',
                'description' => 'Get 100% in any quiz',
                'icon' => 'perfect.png',
                'points_required' => 0,
                'badge_color' => '#FFD700',
                'category' => 'performance',
                'is_active' => true,
            ],
            [
                'name' => 'Speed Demon',
                'description' => 'Complete a quiz in under 5 minutes',
                'icon' => 'speed.png',
                'points_required' => 0,
                'badge_color' => '#FF4444',
                'category' => 'performance',
                'is_active' => true,
            ],
            [
                'name' => 'Week Warrior',
                'description' => 'Maintain 7-day streak',
                'icon' => 'week_streak.png',
                'points_required' => 0,
                'badge_color' => '#4CAF50',
                'category' => 'consistency',
                'is_active' => true,
            ],
            [
                'name' => 'Month Master',
                'description' => 'Maintain 30-day streak',
                'icon' => 'month_streak.png',
                'points_required' => 0,
                'badge_color' => '#2196F3',
                'category' => 'consistency',
                'is_active' => true,
            ],
            [
                'name' => 'Level Up',
                'description' => 'Reach Level 5',
                'icon' => 'level_5.png',
                'points_required' => 500,
                'badge_color' => '#9C27B0',
                'category' => 'milestone',
                'is_active' => true,
            ],
            [
                'name' => 'Expert',
                'description' => 'Reach Level 10',
                'icon' => 'level_10.png',
                'points_required' => 1000,
                'badge_color' => '#FF9800',
                'category' => 'milestone',
                'is_active' => true,
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::create($achievement);
        }
    }
}
