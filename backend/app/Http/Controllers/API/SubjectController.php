<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Get all active subjects.
     */
    public function index()
    {
        $subjects = Subject::active()
            ->withCount(['topics', 'questions'])
            ->orderBy('name')
            ->get()
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'description' => $subject->description,
                    'icon' => $subject->icon,
                    'color_code' => $subject->color_code,
                    'topics_count' => $subject->topics_count,
                    'questions_count' => $subject->questions_count,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $subjects,
        ]);
    }

    /**
     * Get a specific subject with details.
     */
    public function show(Subject $subject)
    {
        $subject->load(['topics' => function($query) {
            $query->active()->withCount('questions');
        }]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $subject->id,
                'name' => $subject->name,
                'description' => $subject->description,
                'icon' => $subject->icon,
                'color_code' => $subject->color_code,
                'topics' => $subject->topics->map(function ($topic) {
                    return [
                        'id' => $topic->id,
                        'name' => $topic->name,
                        'description' => $topic->description,
                        'questions_count' => $topic->questions_count,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Get topics for a specific subject.
     */
    public function topics(Request $request, Subject $subject)
    {
        $user = $request->user();
        
        $topics = $subject->topics()
            ->active()
            ->withCount('questions')
            ->get()
            ->map(function ($topic) use ($user) {
                $userProgress = null;
                
                if ($user) {
                    $progress = $user->progress()
                        ->where('subject_id', $topic->subject_id)
                        ->where('topic_id', $topic->id)
                        ->first();
                    
                    if ($progress) {
                        $userProgress = [
                            'attempted' => $progress->total_questions_attempted,
                            'correct' => $progress->correct_answers,
                            'accuracy' => round($progress->accuracy_percentage, 1),
                        ];
                    }
                }

                return [
                    'id' => $topic->id,
                    'name' => $topic->name,
                    'description' => $topic->description,
                    'questions_count' => $topic->questions_count,
                    'user_progress' => $userProgress,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $topics,
        ]);
    }
}
