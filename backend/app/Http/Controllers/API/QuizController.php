<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    /**
     * Get available quizzes.
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'sometimes|in:daily,practice,mock_exam,custom',
            'subject_id' => 'sometimes|exists:subjects,id',
            'difficulty' => 'sometimes|in:basic,intermediate,advanced',
            'page' => 'sometimes|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = Quiz::active()
            ->with(['questions' => function($query) {
                $query->select('questions.id', 'questions.subject_id', 'questions.difficulty_level');
            }]);

        // Apply filters
        if ($request->has('type')) {
            $query->byType($request->type);
        }

        if ($request->has('subject_id')) {
            $query->whereHas('questions', function($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            });
        }

        if ($request->has('difficulty')) {
            $query->whereHas('questions', function($q) use ($request) {
                $q->where('difficulty_level', $request->difficulty);
            });
        }

        $quizzes = $query->paginate(10);

        $data = $quizzes->map(function ($quiz) {
            // Calculate difficulty distribution
            $difficulties = $quiz->questions->groupBy('difficulty_level');
            $difficultyDistribution = [
                'basic' => $difficulties->get('basic', collect())->count(),
                'intermediate' => $difficulties->get('intermediate', collect())->count(),
                'advanced' => $difficulties->get('advanced', collect())->count(),
            ];

            // Get unique subjects
            $subjects = $quiz->questions->groupBy('subject_id')->map(function($questions, $subjectId) {
                $subject = $questions->first()->subject ?? null;
                return [
                    'id' => $subjectId,
                    'name' => $subject ? $subject->name : 'Unknown',
                    'questions_count' => $questions->count(),
                ];
            })->values();

            return [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'description' => $quiz->description,
                'type' => $quiz->type,
                'time_limit' => $quiz->time_limit,
                'total_questions' => $quiz->total_questions,
                'passing_score' => $quiz->passing_score,
                'difficulty_distribution' => $difficultyDistribution,
                'subjects' => $subjects,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'pagination' => [
                    'current_page' => $quizzes->currentPage(),
                    'total_pages' => $quizzes->lastPage(),
                    'per_page' => $quizzes->perPage(),
                    'total_items' => $quizzes->total(),
                ],
            ],
        ]);
    }

    /**
     * Get quiz details.
     */
    public function show(Quiz $quiz)
    {
        $quiz->load(['questions' => function($query) {
            $query->with('subject:id,name');
        }]);

        // Calculate difficulty distribution
        $difficulties = $quiz->questions->groupBy('difficulty_level');
        $difficultyDistribution = [
            'basic' => $difficulties->get('basic', collect())->count(),
            'intermediate' => $difficulties->get('intermediate', collect())->count(),
            'advanced' => $difficulties->get('advanced', collect())->count(),
        ];

        // Get subjects involved
        $subjects = $quiz->questions->groupBy('subject_id')->map(function($questions, $subjectId) {
            $subject = $questions->first()->subject;
            return [
                'id' => $subjectId,
                'name' => $subject->name,
                'questions_count' => $questions->count(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'description' => $quiz->description,
                'type' => $quiz->type,
                'time_limit' => $quiz->time_limit,
                'total_questions' => $quiz->total_questions,
                'passing_score' => $quiz->passing_score,
                'difficulty_distribution' => $difficultyDistribution,
                'subjects' => $subjects,
                'scheduled_at' => $quiz->scheduled_at,
            ],
        ]);
    }

    /**
     * Start a quiz session.
     */
    public function start(Request $request, Quiz $quiz)
    {
        $user = $request->user();

        // Check if user has an active session for this quiz
        $activeSession = $user->quizSessions()
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->first();

        if ($activeSession) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active session for this quiz',
                'code' => 'ACTIVE_SESSION_EXISTS',
            ], 409);
        }

        // Create new quiz session
        $session = QuizSession::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'status' => 'in_progress',
            'total_questions' => $quiz->total_questions,
            'started_at' => now(),
        ]);

        // Get the first question
        $firstQuestion = $quiz->questions()
            ->orderBy('quiz_questions.order_number')
            ->first();

        $questionData = null;
        if ($firstQuestion) {
            $questionData = [
                'id' => $firstQuestion->id,
                'question_number' => 1,
                'question_text' => $firstQuestion->question_text,
                'option_a' => $firstQuestion->option_a,
                'option_b' => $firstQuestion->option_b,
                'option_c' => $firstQuestion->option_c,
                'option_d' => $firstQuestion->option_d,
                'time_limit' => 60, // Default 60 seconds per question
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Quiz session started',
            'data' => [
                'session_id' => $session->id,
                'quiz' => [
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'time_limit' => $quiz->time_limit,
                    'total_questions' => $quiz->total_questions,
                ],
                'started_at' => $session->started_at->toISOString(),
                'expires_at' => $quiz->time_limit ? 
                    $session->started_at->addMinutes($quiz->time_limit)->toISOString() : null,
                'first_question' => $questionData,
            ],
        ], 201);
    }
}
