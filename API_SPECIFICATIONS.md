# API Specifications - Ayurvedic Exam Preparation App

## Base URL
```
Development: http://localhost:8000/api/v1
Production: https://api.ayurvedaexam.com/v1
```

## Authentication
All protected endpoints require Bearer token authentication:
```
Authorization: Bearer {jwt_token}
```

## Standard Response Format
```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": {...},
    "meta": {
        "pagination": {...},
        "timestamp": "2024-01-01T00:00:00Z"
    }
}
```

## Error Response Format
```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field_name": ["validation error message"]
    },
    "code": "ERROR_CODE"
}
```

---

## 🔐 Authentication Endpoints

### Register User
**POST** `/auth/register`

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone_number": "+1234567890"
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone_number": "+1234567890",
            "level": 1,
            "experience_points": 0,
            "daily_streak": 0,
            "avatar": null,
            "created_at": "2024-01-01T00:00:00Z"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
        "expires_at": "2024-01-08T00:00:00Z"
    }
}
```

### Login
**POST** `/auth/login`

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "level": 5,
            "experience_points": 1250,
            "daily_streak": 7,
            "avatar": "avatar_url.jpg"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
        "expires_at": "2024-01-08T00:00:00Z"
    }
}
```

### Refresh Token
**POST** `/auth/refresh`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "success": true,
    "data": {
        "token": "new_jwt_token_here",
        "expires_at": "2024-01-08T00:00:00Z"
    }
}
```

### Logout
**POST** `/auth/logout`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

---

## 👤 User Management

### Get User Profile
**GET** `/user/profile`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone_number": "+1234567890",
        "level": 5,
        "experience_points": 1250,
        "daily_streak": 7,
        "last_quiz_date": "2024-01-01",
        "avatar": "avatar_url.jpg",
        "created_at": "2024-01-01T00:00:00Z"
    }
}
```

### Update User Profile
**PUT** `/user/profile`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "name": "John Updated",
    "phone_number": "+0987654321",
    "avatar": "base64_image_data_or_file"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Profile updated successfully",
    "data": {
        "id": 1,
        "name": "John Updated",
        "email": "john@example.com",
        "phone_number": "+0987654321",
        "avatar": "new_avatar_url.jpg"
    }
}
```

### Get User Statistics
**GET** `/user/statistics`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "success": true,
    "data": {
        "total_quizzes_taken": 45,
        "average_score": 78.5,
        "best_score": 98,
        "total_achievements": 12,
        "subjects_mastered": 3,
        "current_streak": 7,
        "longest_streak": 15,
        "total_time_studied": 2340,
        "performance_trend": "improving",
        "weak_areas": [
            {
                "subject_id": 2,
                "subject_name": "Anatomy & Physiology",
                "accuracy": 65.4
            }
        ]
    }
}
```

---

## 📚 Content Management

### Get All Subjects
**GET** `/subjects`

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Ayurveda Fundamentals",
            "description": "Basic principles and foundations of Ayurveda",
            "icon": "fundamentals.png",
            "color_code": "#4CAF50",
            "topics_count": 15,
            "questions_count": 250
        }
    ]
}
```

### Get Topics by Subject
**GET** `/subjects/{subject_id}/topics`

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Introduction to Ayurveda",
            "description": "History and basic concepts",
            "questions_count": 25,
            "user_progress": {
                "attempted": 20,
                "correct": 16,
                "accuracy": 80.0
            }
        }
    ]
}
```

---

## 🧩 Quiz Management

### Get Available Quizzes
**GET** `/quizzes`

**Query Parameters:**
- `type` (optional): `daily|practice|mock_exam|custom`
- `subject_id` (optional): Filter by subject
- `difficulty` (optional): `basic|intermediate|advanced`
- `page` (optional): Page number for pagination

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Ayurveda Fundamentals - Practice Quiz",
            "description": "Test your knowledge of basic Ayurvedic principles",
            "type": "practice",
            "time_limit": 30,
            "total_questions": 20,
            "passing_score": 70,
            "difficulty_distribution": {
                "basic": 8,
                "intermediate": 10,
                "advanced": 2
            },
            "subjects": [
                {
                    "id": 1,
                    "name": "Ayurveda Fundamentals",
                    "questions_count": 20
                }
            ]
        }
    ],
    "meta": {
        "pagination": {
            "current_page": 1,
            "total_pages": 5,
            "per_page": 10,
            "total_items": 50
        }
    }
}
```

### Start Quiz
**POST** `/quizzes/{quiz_id}/start`

**Headers:** `Authorization: Bearer {token}`

**Response (201):**
```json
{
    "success": true,
    "message": "Quiz session started",
    "data": {
        "session_id": 123,
        "quiz": {
            "id": 1,
            "title": "Daily Quiz - January 1, 2024",
            "time_limit": 10,
            "total_questions": 10
        },
        "started_at": "2024-01-01T10:00:00Z",
        "expires_at": "2024-01-01T10:10:00Z",
        "first_question": {
            "id": 1,
            "question_number": 1,
            "question_text": "What are the three doshas in Ayurveda?",
            "option_a": "Vata, Pitta, Kapha",
            "option_b": "Vata, Pitta, Ojas",
            "option_c": "Pitta, Kapha, Tejas",
            "option_d": "Vata, Kapha, Prana",
            "time_limit": 60
        }
    }
}
```

### Get Current Quiz Session
**GET** `/quiz-sessions/{session_id}`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "success": true,
    "data": {
        "session_id": 123,
        "status": "in_progress",
        "current_question_number": 3,
        "total_questions": 10,
        "time_remaining": 450,
        "score": 20,
        "current_question": {
            "id": 3,
            "question_number": 3,
            "question_text": "Which dosha is responsible for digestion?",
            "option_a": "Vata",
            "option_b": "Pitta",
            "option_c": "Kapha",
            "option_d": "All of the above",
            "time_limit": 60
        },
        "answered_questions": [1, 2],
        "bookmarked_questions": [2]
    }
}
```

### Submit Answer
**PUT** `/quiz-sessions/{session_id}/answer`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "question_id": 1,
    "selected_answer": "A",
    "time_taken": 45,
    "is_bookmarked": false
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Answer submitted successfully",
    "data": {
        "is_correct": true,
        "correct_answer": "A",
        "explanation": "The three fundamental doshas in Ayurveda are Vata, Pitta, and Kapha.",
        "points_earned": 10,
        "current_score": 30,
        "next_question": {
            "id": 2,
            "question_number": 2,
            "question_text": "Next question text...",
            "option_a": "Option A",
            "option_b": "Option B",
            "option_c": "Option C",
            "option_d": "Option D"
        }
    }
}
```

### Submit Quiz
**POST** `/quiz-sessions/{session_id}/submit`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "success": true,
    "message": "Quiz submitted successfully",
    "data": {
        "session_id": 123,
        "final_score": 80,
        "total_questions": 10,
        "correct_answers": 8,
        "time_taken": 520,
        "percentage": 80.0,
        "passed": true,
        "experience_gained": 50,
        "new_level": 5,
        "achievements_unlocked": [
            {
                "id": 3,
                "name": "Perfect Score",
                "description": "Score 100% in any quiz"
            }
        ],
        "detailed_results": "quiz_results_endpoint"
    }
}
```

### Get Quiz Results
**GET** `/quiz-sessions/{session_id}/results`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "success": true,
    "data": {
        "session_summary": {
            "quiz_title": "Daily Quiz - January 1, 2024",
            "final_score": 80,
            "percentage": 80.0,
            "time_taken": 520,
            "rank": 15
        },
        "question_analysis": [
            {
                "question_id": 1,
                "question_text": "What are the three doshas?",
                "selected_answer": "A",
                "correct_answer": "A",
                "is_correct": true,
                "time_taken": 45,
                "explanation": "Detailed explanation..."
            }
        ],
        "subject_wise_performance": [
            {
                "subject_id": 1,
                "subject_name": "Ayurveda Fundamentals",
                "questions_attempted": 5,
                "correct_answers": 4,
                "accuracy": 80.0
            }
        ],
        "recommendations": [
            "Focus more on Anatomy & Physiology",
            "Practice more intermediate level questions"
        ]
    }
}
```

---

## 📅 Daily Quiz

### Get Today's Daily Quiz
**GET** `/daily-quiz/today`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "success": true,
    "data": {
        "available": true,
        "quiz": {
            "id": 150,
            "title": "Daily Quiz - January 1, 2024",
            "total_questions": 10,
            "time_limit": 10,
            "released_at": "2024-01-01T06:00:00Z"
        },
        "user_status": {
            "attempted": false,
            "can_attempt": true
        },
        "streak_info": {
            "current_streak": 6,
            "will_break_streak": false
        }
    }
}
```

### Get Daily Quiz History
**GET** `/daily-quiz/history`

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `page` (optional): Page number
- `limit` (optional): Items per page (default: 10)

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "date": "2024-01-01",
            "quiz_title": "Daily Quiz - January 1, 2024",
            "attempted": true,
            "score": 80,
            "percentage": 80.0,
            "rank": 25,
            "experience_gained": 50
        }
    ],
    "meta": {
        "streak_stats": {
            "current_streak": 7,
            "longest_streak": 15,
            "total_days_attempted": 45
        }
    }
}
```

---

## 🏆 Gamification

### Get User Achievements
**GET** `/user/achievements`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "success": true,
    "data": {
        "earned_achievements": [
            {
                "id": 1,
                "name": "First Steps",
                "description": "Complete your first quiz",
                "icon": "first_quiz.png",
                "badge_color": "#FFD700",
                "category": "progress",
                "earned_at": "2024-01-01T10:00:00Z"
            }
        ],
        "available_achievements": [
            {
                "id": 2,
                "name": "Quick Learner",
                "description": "Complete 10 quizzes",
                "icon": "quick_learner.png",
                "badge_color": "#C0C0C0",
                "category": "progress",
                "progress": {
                    "current": 7,
                    "required": 10,
                    "percentage": 70
                }
            }
        ],
        "total_earned": 5,
        "total_available": 15
    }
}
```

### Get Leaderboard
**GET** `/leaderboard/{period}`

**Path Parameters:**
- `period`: `daily|weekly|monthly`

**Query Parameters:**
- `page` (optional): Page number
- `limit` (optional): Items per page (default: 50)

**Response (200):**
```json
{
    "success": true,
    "data": {
        "period": "weekly",
        "period_date": "2024-01-01",
        "user_rank": 15,
        "leaders": [
            {
                "rank": 1,
                "user": {
                    "id": 25,
                    "name": "Top Student",
                    "avatar": "avatar_url.jpg",
                    "level": 8
                },
                "score": 2450,
                "quizzes_completed": 12
            }
        ]
    }
}
```

### Get Level Progress
**GET** `/user/level-progress`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "success": true,
    "data": {
        "current_level": 5,
        "current_xp": 1250,
        "next_level_xp": 1500,
        "xp_to_next_level": 250,
        "progress_percentage": 83.33,
        "level_benefits": [
            "Unlock advanced practice quizzes",
            "Access to detailed analytics"
        ],
        "recent_xp_activities": [
            {
                "activity": "Completed daily quiz",
                "xp_gained": 50,
                "timestamp": "2024-01-01T10:00:00Z"
            }
        ]
    }
}
```

---

## 📊 Analytics

### Get Performance Analytics
**GET** `/analytics/performance`

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `period` (optional): `week|month|3months|year` (default: month)
- `subject_id` (optional): Filter by subject

**Response (200):**
```json
{
    "success": true,
    "data": {
        "period": "month",
        "overall_stats": {
            "total_quizzes": 25,
            "average_score": 78.5,
            "improvement_trend": "+5.2%",
            "total_time_studied": 1250
        },
        "score_trend": [
            {
                "date": "2024-01-01",
                "average_score": 75.0
            },
            {
                "date": "2024-01-07",
                "average_score": 82.0
            }
        ],
        "subject_performance": [
            {
                "subject_id": 1,
                "subject_name": "Ayurveda Fundamentals",
                "accuracy": 85.5,
                "quizzes_taken": 8,
                "trend": "improving"
            }
        ]
    }
}
```

### Get Weak Areas Analysis
**GET** `/analytics/weak-areas`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "subject_id": 2,
            "subject_name": "Anatomy & Physiology",
            "topic_id": 5,
            "topic_name": "Nervous System",
            "accuracy": 45.0,
            "questions_attempted": 20,
            "severity": "high",
            "recommendations": [
                "Practice more basic level questions",
                "Review fundamental concepts"
            ]
        }
    ]
}
```

---

## 🔖 Bookmarks

### Get Bookmarked Questions
**GET** `/questions/bookmarked`

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `subject_id` (optional): Filter by subject
- `page` (optional): Page number

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "question_text": "What are the three doshas?",
            "subject_name": "Ayurveda Fundamentals",
            "topic_name": "Basic Principles",
            "difficulty_level": "basic",
            "bookmarked_at": "2024-01-01T10:00:00Z",
            "last_attempted": "2024-01-01T09:00:00Z",
            "user_answer": "A",
            "is_correct": true
        }
    ]
}
```

### Toggle Bookmark
**POST** `/questions/{question_id}/bookmark`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "success": true,
    "message": "Question bookmarked successfully",
    "data": {
        "is_bookmarked": true
    }
}
```

---

## 📱 Push Notifications

### Update FCM Token
**POST** `/user/fcm-token`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "fcm_token": "firebase_cloud_messaging_token",
    "device_type": "android"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "FCM token updated successfully"
}
```

---

## 📤 Social Sharing

### Share Quiz Result
**POST** `/daily-quiz/share-result`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "session_id": 123,
    "platform": "twitter",
    "include_score": true,
    "include_streak": true
}
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "share_text": "Just scored 80% on today's Ayurveda quiz! 🌿 My streak is now 7 days! #AyurvedaExam",
        "share_image_url": "https://api.example.com/share-images/session-123.png",
        "deep_link": "ayurvedaexam://quiz-result/123"
    }
}
```

---

## Error Codes

| Code | Description |
|------|-------------|
| `VALIDATION_ERROR` | Request validation failed |
| `AUTHENTICATION_REQUIRED` | User not authenticated |
| `AUTHORIZATION_FAILED` | User not authorized for this action |
| `RESOURCE_NOT_FOUND` | Requested resource not found |
| `QUIZ_SESSION_EXPIRED` | Quiz session has expired |
| `QUIZ_ALREADY_COMPLETED` | Quiz already completed by user |
| `DAILY_QUIZ_NOT_AVAILABLE` | Daily quiz not yet released |
| `RATE_LIMIT_EXCEEDED` | Too many requests |
| `SERVER_ERROR` | Internal server error |

## Rate Limiting

| Endpoint Category | Limit |
|------------------|-------|
| Authentication | 5 requests per minute |
| Quiz Operations | 30 requests per minute |
| General API | 100 requests per minute |
| Analytics | 20 requests per minute |

## Pagination

All paginated endpoints support these query parameters:
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 10, max: 100)

Pagination response format:
```json
{
    "meta": {
        "pagination": {
            "current_page": 1,
            "total_pages": 10,
            "per_page": 10,
            "total_items": 100,
            "has_next_page": true,
            "has_previous_page": false
        }
    }
}
```