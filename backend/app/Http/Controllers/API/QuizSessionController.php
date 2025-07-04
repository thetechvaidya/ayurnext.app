<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\QuizSession;
use App\Models\UserAnswer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class QuizSessionController extends Controller
{
    /**
     * Get current quiz session details.
     */
    public function show(Request $request, QuizSession $session)
    {
        $user = $request->user();

        // Ensure user owns this session
        if ($session->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to quiz session',
            ], 403);
        }

        $session->load(['quiz', 'userAnswers']);

        // Get current question number
        $answeredCount = $session->userAnswers->count();
        $currentQuestionNumber = $answeredCount + 1;

        // Get current question
        $currentQuestion = null;
        if ($currentQuestionNumber <= $session->total_questions) {
            $question = $session->quiz->questions()
                ->orderBy('quiz_questions.order_number')
                ->skip($answeredCount)
                ->first();

            if ($question) {
                $currentQuestion = [
                    'id' => $question->id,
                    'question_number' => $currentQuestionNumber,
                    'question_text' => $question->question_text,
                    'option_a' => $question->option_a,
                    'option_b' => $question->option_b,
                    'option_c' => $question->option_c,
                    'option_d' => $question->option_d,
                    'time_limit' => 60,
                ];
            }
        }

        // Calculate time remaining
        $timeRemaining = null;
        if ($session->quiz->time_limit && $session->started_at) {
            $expiresAt = $session->started_at->addMinutes($session->quiz->time_limit);
            $timeRemaining = max(0, now()->diffInSeconds($expiresAt, false));
        }

        // Get answered questions
        $answeredQuestions = $session->userAnswers->pluck('question_id')->toArray();
        $bookmarkedQuestions = $session->userAnswers->where('is_bookmarked', true)->pluck('question_id')->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'session_id' => $session->id,
                'status' => $session->status,
                'current_question_number' => $currentQuestionNumber,
                'total_questions' => $session->total_questions,
                'time_remaining' => $timeRemaining,
                'score' => $session->score,
                'current_question' => $currentQuestion,
                'answered_questions' => $answeredQuestions,
                'bookmarked_questions' => $bookmarkedQuestions,
            ],
        ]);
    }

    /**
     * Submit an answer for a question.
     */
    public function submitAnswer(Request $request, QuizSession $session)
    {
        $user = $request->user();

        // Ensure user owns this session
        if ($session->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to quiz session',
            ], 403);
        }

        // Check if session is still active
        if ($session->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'Quiz session is not active',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'question_id' => 'required|exists:questions,id',
            'selected_answer' => 'required|in:A,B,C,D',
            'time_taken' => 'sometimes|integer|min:0',
            'is_bookmarked' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $question = Question::find($request->question_id);
        
        // Check if question belongs to this quiz
        if (!$session->quiz->questions->contains($question->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Question does not belong to this quiz',
            ], 400);
        }

        // Check if already answered
        $existingAnswer = $session->userAnswers()
            ->where('question_id', $question->id)
            ->first();

        if ($existingAnswer) {
            return response()->json([
                'success' => false,
                'message' => 'Question already answered',
            ], 400);
        }

        // Determine if answer is correct
        $isCorrect = $question->isCorrectAnswer($request->selected_answer);
        $pointsEarned = $isCorrect ? 10 : 0;

        // Create user answer
        UserAnswer::create([
            'quiz_session_id' => $session->id,
            'question_id' => $question->id,
            'selected_answer' => strtoupper($request->selected_answer),
            'is_correct' => $isCorrect,
            'time_taken' => $request->get('time_taken', 0),
            'is_bookmarked' => $request->get('is_bookmarked', false),
        ]);

        // Update session score and progress
        $session->increment('score', $pointsEarned);
        if ($isCorrect) {
            $session->increment('correct_answers');
        }

        // Check if quiz is completed
        $answeredCount = $session->userAnswers()->count();
        $isCompleted = $answeredCount >= $session->total_questions;

        // Get next question
        $nextQuestion = null;
        if (!$isCompleted) {
            $nextQuestionModel = $session->quiz->questions()
                ->orderBy('quiz_questions.order_number')
                ->skip($answeredCount)
                ->first();

            if ($nextQuestionModel) {
                $nextQuestion = [
                    'id' => $nextQuestionModel->id,
                    'question_number' => $answeredCount + 1,
                    'question_text' => $nextQuestionModel->question_text,
                    'option_a' => $nextQuestionModel->option_a,
                    'option_b' => $nextQuestionModel->option_b,
                    'option_c' => $nextQuestionModel->option_c,
                    'option_d' => $nextQuestionModel->option_d,
                    'time_limit' => 60,
                ];
            }
        }

        // If completed, mark session as completed
        if ($isCompleted) {
            $session->markAsCompleted();
            
            // Award experience points to user
            $user = $session->user;
            $baseXP = 50; // Base XP for completing quiz
            $correctXP = $session->correct_answers * 10; // 10 XP per correct answer
            $bonusXP = ($session->correct_answers == $session->total_questions) ? 100 : 0; // Perfect score bonus
            
            $totalXP = $baseXP + $correctXP + $bonusXP;
            $user->increment('experience_points', $totalXP);
            
            // Level up logic
            $newLevel = $this->calculateLevel($user->experience_points);
            if ($newLevel > $user->level) {
                $user->update(['level' => $newLevel]);
            }

            // Update daily streak if this is the first quiz today
            if (!$user->last_quiz_date || $user->last_quiz_date->isToday() == false) {
                if ($user->last_quiz_date && $user->last_quiz_date->isYesterday()) {
                    $user->increment('daily_streak');
                } else {
                    $user->update(['daily_streak' => 1]);
                }
                $user->update(['last_quiz_date' => now()->toDateString()]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Answer submitted successfully',
            'data' => [
                'is_correct' => $isCorrect,
                'correct_answer' => $question->correct_answer,
                'explanation' => $question->explanation,
                'points_earned' => $pointsEarned,
                'current_score' => $session->fresh()->score,
                'next_question' => $nextQuestion,
                'is_completed' => $isCompleted,
            ],
        ]);
    }

    /**
     * Submit/Complete the quiz session.
     */
    public function submit(Request $request, QuizSession $session)
    {
        $user = $request->user();

        // Ensure user owns this session
        if ($session->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to quiz session',
            ], 403);
        }

        // Check if session is still active
        if ($session->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'Quiz session is not active',
            ], 400);
        }

        // Mark session as completed
        $session->markAsCompleted();
        $session->update(['time_taken' => $session->started_at->diffInSeconds(now())]);

        // Calculate final results
        $percentage = $session->percentage;
        $passed = $session->hasPassed();

        // Award experience points
        $user = $session->user;
        $baseXP = 50;
        $correctXP = $session->correct_answers * 10;
        $bonusXP = ($session->correct_answers == $session->total_questions) ? 100 : 0;
        
        $totalXP = $baseXP + $correctXP + $bonusXP;
        $experienceGained = $totalXP;
        
        $oldLevel = $user->level;
        $user->increment('experience_points', $totalXP);
        
        $newLevel = $this->calculateLevel($user->experience_points);
        if ($newLevel > $user->level) {
            $user->update(['level' => $newLevel]);
        }

        // Check for achievements (simplified version)
        $achievementsUnlocked = [];
        // TODO: Implement full achievement checking logic

        return response()->json([
            'success' => true,
            'message' => 'Quiz submitted successfully',
            'data' => [
                'session_id' => $session->id,
                'final_score' => $session->score,
                'total_questions' => $session->total_questions,
                'correct_answers' => $session->correct_answers,
                'time_taken' => $session->time_taken,
                'percentage' => $percentage,
                'passed' => $passed,
                'experience_gained' => $experienceGained,
                'new_level' => $newLevel,
                'level_up' => $newLevel > $oldLevel,
                'achievements_unlocked' => $achievementsUnlocked,
                'detailed_results' => route('api.quiz-sessions.results', $session),
            ],
        ]);
    }

    /**
     * Get detailed quiz results.
     */
    public function results(Request $request, QuizSession $session)
    {
        $user = $request->user();

        // Ensure user owns this session
        if ($session->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to quiz session',
            ], 403);
        }

        $session->load(['quiz', 'userAnswers.question.subject']);

        // Session summary
        $sessionSummary = [
            'quiz_title' => $session->quiz->title,
            'final_score' => $session->score,
            'percentage' => $session->percentage,
            'time_taken' => $session->time_taken,
            'rank' => null, // TODO: Calculate user rank
        ];

        // Question analysis
        $questionAnalysis = $session->userAnswers->map(function ($answer) {
            return [
                'question_id' => $answer->question->id,
                'question_text' => $answer->question->question_text,
                'selected_answer' => $answer->selected_answer,
                'correct_answer' => $answer->question->correct_answer,
                'is_correct' => $answer->is_correct,
                'time_taken' => $answer->time_taken,
                'explanation' => $answer->question->explanation,
                'is_bookmarked' => $answer->is_bookmarked,
            ];
        });

        // Subject-wise performance
        $subjectPerformance = $session->userAnswers
            ->groupBy('question.subject_id')
            ->map(function ($answers, $subjectId) {
                $subject = $answers->first()->question->subject;
                $correct = $answers->where('is_correct', true)->count();
                $total = $answers->count();

                return [
                    'subject_id' => $subjectId,
                    'subject_name' => $subject->name,
                    'questions_attempted' => $total,
                    'correct_answers' => $correct,
                    'accuracy' => $total > 0 ? round(($correct / $total) * 100, 1) : 0,
                ];
            })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'session_summary' => $sessionSummary,
                'question_analysis' => $questionAnalysis,
                'subject_wise_performance' => $subjectPerformance,
            ],
        ]);
    }

    /**
     * Calculate user level based on experience points.
     */
    private function calculateLevel($experiencePoints)
    {
        // Level calculation: Level 1: 0-100, Level 2: 101-250, Level 3: 251-500, etc.
        if ($experiencePoints <= 100) return 1;
        if ($experiencePoints <= 250) return 2;
        if ($experiencePoints <= 500) return 3;
        if ($experiencePoints <= 1000) return 4;
        if ($experiencePoints <= 1750) return 5;
        if ($experiencePoints <= 2750) return 6;
        if ($experiencePoints <= 4000) return 7;
        if ($experiencePoints <= 5500) return 8;
        if ($experiencePoints <= 7500) return 9;
        
        return min(10 + floor(($experiencePoints - 7500) / 2000), 50); // Max level 50
    }
}
