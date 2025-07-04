<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            [
                'name' => 'Ayurveda Fundamentals',
                'description' => 'Basic principles and foundations of Ayurveda',
                'icon' => 'fundamentals.png',
                'color_code' => '#4CAF50',
                'is_active' => true,
            ],
            [
                'name' => 'Anatomy & Physiology',
                'description' => 'Human anatomy and physiological systems',
                'icon' => 'anatomy.png',
                'color_code' => '#2196F3',
                'is_active' => true,
            ],
            [
                'name' => 'Pharmacology',
                'description' => 'Ayurvedic medicines and their properties',
                'icon' => 'pharmacy.png',
                'color_code' => '#FF9800',
                'is_active' => true,
            ],
            [
                'name' => 'Clinical Medicine',
                'description' => 'Diagnosis and treatment methods',
                'icon' => 'clinical.png',
                'color_code' => '#F44336',
                'is_active' => true,
            ],
            [
                'name' => 'Surgery',
                'description' => 'Surgical procedures in Ayurveda',
                'icon' => 'surgery.png',
                'color_code' => '#9C27B0',
                'is_active' => true,
            ],
            [
                'name' => 'Pediatrics',
                'description' => 'Ayurvedic treatment for children',
                'icon' => 'pediatrics.png',
                'color_code' => '#00BCD4',
                'is_active' => true,
            ],
            [
                'name' => 'Gynecology',
                'description' => 'Women health and Ayurvedic treatment',
                'icon' => 'gynecology.png',
                'color_code' => '#E91E63',
                'is_active' => true,
            ],
            [
                'name' => 'Psychiatry',
                'description' => 'Mental health in Ayurveda',
                'icon' => 'psychiatry.png',
                'color_code' => '#795548',
                'is_active' => true,
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}
