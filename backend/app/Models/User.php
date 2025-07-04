<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'avatar',
        'level',
        'experience_points',
        'daily_streak',
        'last_quiz_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array
     */
    protected $attributes = [
        'level' => 1,
        'experience_points' => 0,
        'daily_streak' => 0,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_quiz_date' => 'date',
            'level' => 'integer',
            'experience_points' => 'integer',
            'daily_streak' => 'integer',
        ];
    }

    /**
     * Get the quiz sessions for the user.
     */
    public function quizSessions()
    {
        return $this->hasMany(QuizSession::class);
    }

    /**
     * Get the user's achievements.
     */
    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
                    ->withPivot('earned_at')
                    ->withTimestamps();
    }

    /**
     * Get the user's progress records.
     */
    public function progress()
    {
        return $this->hasMany(UserProgress::class);
    }

    /**
     * Get the user's bookmarked questions.
     */
    public function bookmarkedQuestions()
    {
        return $this->belongsToMany(Question::class, 'user_bookmarks')
                    ->withTimestamps();
    }

    /**
     * Get the user's notifications.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the user's leaderboard entries.
     */
    public function leaderboardEntries()
    {
        return $this->hasMany(Leaderboard::class);
    }

    /**
     * Get the user's answers.
     */
    public function answers()
    {
        return $this->hasManyThrough(UserAnswer::class, QuizSession::class);
    }
}
