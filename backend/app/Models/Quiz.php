<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'type',
        'time_limit',
        'total_questions',
        'passing_score',
        'is_active',
        'scheduled_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'scheduled_at' => 'datetime',
        'time_limit' => 'integer',
        'total_questions' => 'integer',
        'passing_score' => 'integer',
    ];

    /**
     * Get the questions for the quiz.
     */
    public function questions()
    {
        return $this->belongsToMany(Question::class, 'quiz_questions')
                    ->withPivot('order_number')
                    ->orderBy('quiz_questions.order_number')
                    ->withTimestamps();
    }

    /**
     * Get the quiz sessions for the quiz.
     */
    public function quizSessions()
    {
        return $this->hasMany(QuizSession::class);
    }

    /**
     * Get the daily quiz record for this quiz.
     */
    public function dailyQuiz()
    {
        return $this->hasOne(DailyQuiz::class);
    }

    /**
     * Scope a query to only include active quizzes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by quiz type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to get scheduled quizzes.
     */
    public function scopeScheduled($query)
    {
        return $query->whereNotNull('scheduled_at');
    }

    /**
     * Check if the quiz is a daily quiz.
     */
    public function isDailyQuiz()
    {
        return $this->type === 'daily';
    }

    /**
     * Check if the quiz is a practice quiz.
     */
    public function isPracticeQuiz()
    {
        return $this->type === 'practice';
    }

    /**
     * Check if the quiz is a mock exam.
     */
    public function isMockExam()
    {
        return $this->type === 'mock_exam';
    }
}
