<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Subject;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createDailyQuiz();
        $this->createPracticeQuizzes();
        $this->createMockExam();
    }

    private function createDailyQuiz()
    {
        // Create daily quiz with mixed questions
        $quiz = Quiz::create([
            'title' => 'Daily Quiz - Ayurveda Basics',
            'description' => 'A quick 5-question daily quiz covering fundamental Ayurvedic concepts',
            'type' => 'daily',
            'total_questions' => 5,
            'time_limit' => 10, // 10 minutes
            'passing_score' => 60,
            'is_active' => true,
            'scheduled_at' => now(),
        ]);

        // Add 5 random basic questions
        $basicQuestions = Question::where('difficulty_level', 'basic')
            ->take(5)
            ->get();

        foreach ($basicQuestions as $index => $question) {
            $quiz->questions()->attach($question->id, [
                'order_number' => $index + 1,
            ]);
        }
    }

    private function createPracticeQuizzes()
    {
        $subjects = Subject::whereIn('name', [
            'Ayurveda Fundamentals',
            'Anatomy & Physiology',
            'Pharmacology',
            'Clinical Medicine'
        ])->get();

        foreach ($subjects as $subject) {
            // Basic practice quiz
            $basicQuiz = Quiz::create([
                'title' => "Practice Quiz - {$subject->name} (Basic)",
                'description' => "Practice basic concepts of {$subject->name}",
                'type' => 'practice',
                'total_questions' => 10,
                'time_limit' => 15,
                'passing_score' => 70,
                'is_active' => true,
            ]);

            $basicQuestions = Question::where('subject_id', $subject->id)
                ->where('difficulty_level', 'basic')
                ->get();

            foreach ($basicQuestions as $index => $question) {
                if ($index >= 10) break;
                $basicQuiz->questions()->attach($question->id, [
                    'order_number' => $index + 1,
                ]);
            }

            // Intermediate practice quiz
            $intermediateQuiz = Quiz::create([
                'title' => "Practice Quiz - {$subject->name} (Intermediate)",
                'description' => "Practice intermediate concepts of {$subject->name}",
                'type' => 'practice',
                'total_questions' => 10,
                'time_limit' => 20,
                'passing_score' => 75,
                'is_active' => true,
            ]);

            $intermediateQuestions = Question::where('subject_id', $subject->id)
                ->where('difficulty_level', 'intermediate')
                ->get();

            foreach ($intermediateQuestions as $index => $question) {
                if ($index >= 10) break;
                $intermediateQuiz->questions()->attach($question->id, [
                    'order_number' => $index + 1,
                ]);
            }
        }
    }

    private function createMockExam()
    {
        $mockExam = Quiz::create([
            'title' => 'Ayurveda Mock Exam',
            'description' => 'Comprehensive mock examination covering all major Ayurvedic subjects',
            'type' => 'mock_exam',
            'total_questions' => 50,
            'time_limit' => 90, // 1.5 hours
            'passing_score' => 80,
            'is_active' => true,
        ]);

        // Get mix of questions from all subjects and difficulty levels
        $questions = collect();
        
        // 20 basic questions
        $basicQuestions = Question::where('difficulty_level', 'basic')
            ->inRandomOrder()
            ->take(20)
            ->get();
        $questions = $questions->merge($basicQuestions);

        // 20 intermediate questions
        $intermediateQuestions = Question::where('difficulty_level', 'intermediate')
            ->inRandomOrder()
            ->take(20)
            ->get();
        $questions = $questions->merge($intermediateQuestions);

        // 10 advanced questions
        $advancedQuestions = Question::where('difficulty_level', 'advanced')
            ->inRandomOrder()
            ->take(10)
            ->get();
        $questions = $questions->merge($advancedQuestions);

        // Shuffle all questions and attach to quiz
        $shuffledQuestions = $questions->shuffle();
        foreach ($shuffledQuestions as $index => $question) {
            $mockExam->questions()->attach($question->id, [
                'order_number' => $index + 1,
            ]);
        }
    }
}
