<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quiz_session_id',
        'question_id',
        'selected_answer',
        'is_correct',
        'time_taken',
        'is_bookmarked',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_correct' => 'boolean',
        'is_bookmarked' => 'boolean',
        'time_taken' => 'integer',
    ];

    /**
     * Get the quiz session that owns the answer.
     */
    public function quizSession()
    {
        return $this->belongsTo(QuizSession::class);
    }

    /**
     * Get the question for the answer.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the user through the quiz session.
     */
    public function user()
    {
        return $this->hasOneThrough(User::class, QuizSession::class, 'id', 'id', 'quiz_session_id', 'user_id');
    }

    /**
     * Scope a query to only include correct answers.
     */
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    /**
     * Scope a query to only include incorrect answers.
     */
    public function scopeIncorrect($query)
    {
        return $query->where('is_correct', false);
    }

    /**
     * Scope a query to only include bookmarked answers.
     */
    public function scopeBookmarked($query)
    {
        return $query->where('is_bookmarked', true);
    }
}
