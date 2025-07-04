<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject_id',
        'topic_id',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'explanation',
        'difficulty_level',
        'year',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'year' => 'integer',
    ];

    /**
     * Get the subject that owns the question.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the topic that owns the question.
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Get the quizzes that contain this question.
     */
    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class, 'quiz_questions')
                    ->withPivot('order_number')
                    ->withTimestamps();
    }

    /**
     * Get the user answers for this question.
     */
    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class);
    }

    /**
     * Get the users who bookmarked this question.
     */
    public function bookmarkedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_bookmarks')
                    ->withTimestamps();
    }

    /**
     * Scope a query to only include active questions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by difficulty level.
     */
    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty_level', $difficulty);
    }

    /**
     * Scope a query to filter by year.
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Check if the given answer is correct.
     */
    public function isCorrectAnswer($answer)
    {
        return strtoupper($answer) === strtoupper($this->correct_answer);
    }
}
