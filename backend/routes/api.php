<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');
    });

    // User management routes
    Route::middleware('auth:sanctum')->prefix('user')->group(function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
        // Route::get('statistics', [UserController::class, 'statistics']);
        // Route::get('achievements', [UserController::class, 'achievements']);
        // Route::get('leaderboard-position', [UserController::class, 'leaderboardPosition']);
    });

    // Content management routes (public)
    // Route::get('subjects', [SubjectController::class, 'index']);
    // Route::get('subjects/{subject}/topics', [TopicController::class, 'index']);

    // Quiz management routes
    Route::middleware('auth:sanctum')->group(function () {
        // Route::get('quizzes', [QuizController::class, 'index']);
        // Route::get('quizzes/{quiz}', [QuizController::class, 'show']);
        // Route::post('quizzes/{quiz}/start', [QuizController::class, 'start']);
        // Route::get('quiz-sessions/{session}', [QuizSessionController::class, 'show']);
        // Route::put('quiz-sessions/{session}/answer', [QuizSessionController::class, 'submitAnswer']);
        // Route::post('quiz-sessions/{session}/submit', [QuizSessionController::class, 'submit']);
        // Route::get('quiz-sessions/{session}/results', [QuizSessionController::class, 'results']);
    });

    // Daily quiz routes
    Route::middleware('auth:sanctum')->prefix('daily-quiz')->group(function () {
        // Route::get('today', [DailyQuizController::class, 'today']);
        // Route::get('history', [DailyQuizController::class, 'history']);
        // Route::post('share-result', [DailyQuizController::class, 'shareResult']);
    });

    // Questions & Content routes
    Route::middleware('auth:sanctum')->group(function () {
        // Route::get('questions/bookmarked', [QuestionController::class, 'bookmarked']);
        // Route::post('questions/{question}/bookmark', [QuestionController::class, 'bookmark']);
        // Route::delete('questions/{question}/bookmark', [QuestionController::class, 'removeBookmark']);
    });

    // Gamification routes
    Route::middleware('auth:sanctum')->group(function () {
        // Route::get('achievements', [AchievementController::class, 'index']);
        // Route::get('leaderboard/{period}', [LeaderboardController::class, 'show']);
        // Route::get('user/level-progress', [UserController::class, 'levelProgress']);
    });

    // Analytics routes
    Route::middleware('auth:sanctum')->prefix('analytics')->group(function () {
        // Route::get('performance', [AnalyticsController::class, 'performance']);
        // Route::get('weak-areas', [AnalyticsController::class, 'weakAreas']);
        // Route::get('progress-chart', [AnalyticsController::class, 'progressChart']);
        // Route::get('time-management', [AnalyticsController::class, 'timeManagement']);
    });
});