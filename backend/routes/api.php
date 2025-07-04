<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\SubjectController;
use App\Http\Controllers\API\QuizController;
use App\Http\Controllers\API\QuizSessionController;

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
    // Public routes
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });
    
    // Public subject information (can be accessed without authentication)
    Route::get('subjects', [SubjectController::class, 'index']);
    Route::get('subjects/{subject}', [SubjectController::class, 'show']);
    
    // Protected routes
    Route::middleware(['auth:sanctum'])->group(function () {
        // Authentication
        Route::prefix('auth')->group(function () {
            Route::get('user', [AuthController::class, 'user']);
            Route::put('user', [AuthController::class, 'updateProfile']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::post('logout', [AuthController::class, 'logout']);
        });
        
        // Subjects (with user progress)
        Route::get('subjects/{subject}/topics', [SubjectController::class, 'topics']);
        
        // Quizzes
        Route::get('quizzes', [QuizController::class, 'index']);
        Route::get('quizzes/{quiz}', [QuizController::class, 'show']);
        Route::post('quizzes/{quiz}/start', [QuizController::class, 'start']);
        
        // Quiz Sessions
        Route::get('quiz-sessions/{session}', [QuizSessionController::class, 'show']);
        Route::post('quiz-sessions/{session}/answer', [QuizSessionController::class, 'submitAnswer']);
        Route::post('quiz-sessions/{session}/submit', [QuizSessionController::class, 'submit']);
        Route::get('quiz-sessions/{session}/results', [QuizSessionController::class, 'results']);
        
        // User specific routes
        Route::prefix('user')->group(function () {
            // Placeholder routes for future features
            Route::get('dashboard', function () {
                return response()->json(['message' => 'Dashboard endpoint - Coming soon']);
            });
            Route::get('progress', function () {
                return response()->json(['message' => 'Progress endpoint - Coming soon']);
            });
            Route::get('achievements', function () {
                return response()->json(['message' => 'Achievements endpoint - Coming soon']);
            });
            Route::get('leaderboard', function () {
                return response()->json(['message' => 'Leaderboard endpoint - Coming soon']);
            });
            Route::get('bookmarks', function () {
                return response()->json(['message' => 'Bookmarks endpoint - Coming soon']);
            });
            Route::get('history', function () {
                return response()->json(['message' => 'Quiz history endpoint - Coming soon']);
            });
        });
        
        // Analytics routes (placeholder)
        Route::prefix('analytics')->group(function () {
            Route::get('performance', function () {
                return response()->json(['message' => 'Performance analytics - Coming soon']);
            });
            Route::get('streak', function () {
                return response()->json(['message' => 'Streak analytics - Coming soon']);
            });
        });
    });
});