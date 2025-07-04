# Database Schema - Ayurvedic Exam Preparation App

## Complete Database Schema with SQL Commands

### 1. Users Table
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20) NULL,
    avatar VARCHAR(255) NULL,
    level INT UNSIGNED DEFAULT 1,
    experience_points INT UNSIGNED DEFAULT 0,
    daily_streak INT UNSIGNED DEFAULT 0,
    last_quiz_date DATE NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_level (level),
    INDEX idx_experience_points (experience_points)
);
```

### 2. Subjects Table
```sql
CREATE TABLE subjects (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    icon VARCHAR(255) NULL,
    color_code VARCHAR(7) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_name (name),
    INDEX idx_is_active (is_active)
);
```

### 3. Topics Table
```sql
CREATE TABLE topics (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    INDEX idx_subject_id (subject_id),
    INDEX idx_name (name),
    INDEX idx_is_active (is_active)
);
```

### 4. Questions Table
```sql
CREATE TABLE questions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject_id BIGINT UNSIGNED NOT NULL,
    topic_id BIGINT UNSIGNED NOT NULL,
    question_text TEXT NOT NULL,
    option_a TEXT NOT NULL,
    option_b TEXT NOT NULL,
    option_c TEXT NOT NULL,
    option_d TEXT NOT NULL,
    correct_answer ENUM('A', 'B', 'C', 'D') NOT NULL,
    explanation TEXT NULL,
    difficulty_level ENUM('basic', 'intermediate', 'advanced') DEFAULT 'basic',
    year YEAR NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE,
    INDEX idx_subject_id (subject_id),
    INDEX idx_topic_id (topic_id),
    INDEX idx_difficulty_level (difficulty_level),
    INDEX idx_year (year),
    INDEX idx_is_active (is_active)
);
```

### 5. Quizzes Table
```sql
CREATE TABLE quizzes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    type ENUM('daily', 'practice', 'mock_exam', 'custom') NOT NULL,
    time_limit INT UNSIGNED NULL COMMENT 'in minutes',
    total_questions INT UNSIGNED NOT NULL,
    passing_score INT UNSIGNED DEFAULT 50,
    is_active BOOLEAN DEFAULT TRUE,
    scheduled_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_type (type),
    INDEX idx_is_active (is_active),
    INDEX idx_scheduled_at (scheduled_at)
);
```

### 6. Quiz Questions Table (Many-to-Many)
```sql
CREATE TABLE quiz_questions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    quiz_id BIGINT UNSIGNED NOT NULL,
    question_id BIGINT UNSIGNED NOT NULL,
    order_number INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_quiz_question (quiz_id, question_id),
    INDEX idx_quiz_id (quiz_id),
    INDEX idx_question_id (question_id),
    INDEX idx_order_number (order_number)
);
```

### 7. Quiz Sessions Table
```sql
CREATE TABLE quiz_sessions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    quiz_id BIGINT UNSIGNED NOT NULL,
    status ENUM('in_progress', 'completed', 'expired') DEFAULT 'in_progress',
    score INT UNSIGNED DEFAULT 0,
    total_questions INT UNSIGNED NOT NULL,
    correct_answers INT UNSIGNED DEFAULT 0,
    time_taken INT UNSIGNED NULL COMMENT 'in seconds',
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_quiz_id (quiz_id),
    INDEX idx_status (status),
    INDEX idx_score (score),
    INDEX idx_started_at (started_at)
);
```

### 8. User Answers Table
```sql
CREATE TABLE user_answers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    quiz_session_id BIGINT UNSIGNED NOT NULL,
    question_id BIGINT UNSIGNED NOT NULL,
    selected_answer ENUM('A', 'B', 'C', 'D') NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    time_taken INT UNSIGNED NULL COMMENT 'in seconds',
    is_bookmarked BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (quiz_session_id) REFERENCES quiz_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    INDEX idx_quiz_session_id (quiz_session_id),
    INDEX idx_question_id (question_id),
    INDEX idx_is_correct (is_correct),
    INDEX idx_is_bookmarked (is_bookmarked)
);
```

### 9. Achievements Table
```sql
CREATE TABLE achievements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    icon VARCHAR(255) NULL,
    points_required INT UNSIGNED DEFAULT 0,
    badge_color VARCHAR(7) DEFAULT '#FFD700',
    category ENUM('progress', 'performance', 'consistency', 'milestone') DEFAULT 'progress',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_name (name),
    INDEX idx_category (category),
    INDEX idx_points_required (points_required),
    INDEX idx_is_active (is_active)
);
```

### 10. User Achievements Table
```sql
CREATE TABLE user_achievements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    achievement_id BIGINT UNSIGNED NOT NULL,
    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (achievement_id) REFERENCES achievements(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_achievement (user_id, achievement_id),
    INDEX idx_user_id (user_id),
    INDEX idx_achievement_id (achievement_id),
    INDEX idx_earned_at (earned_at)
);
```

### 11. Daily Quizzes Table
```sql
CREATE TABLE daily_quizzes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    quiz_date DATE UNIQUE NOT NULL,
    quiz_id BIGINT UNSIGNED NOT NULL,
    release_time TIME DEFAULT '06:00:00',
    is_released BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    INDEX idx_quiz_date (quiz_date),
    INDEX idx_quiz_id (quiz_id),
    INDEX idx_is_released (is_released)
);
```

### 12. Leaderboards Table
```sql
CREATE TABLE leaderboards (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    period ENUM('daily', 'weekly', 'monthly') NOT NULL,
    period_date DATE NOT NULL,
    score INT UNSIGNED DEFAULT 0,
    rank INT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_period (user_id, period, period_date),
    INDEX idx_user_id (user_id),
    INDEX idx_period (period),
    INDEX idx_period_date (period_date),
    INDEX idx_score (score),
    INDEX idx_rank (rank)
);
```

### 13. User Progress Table
```sql
CREATE TABLE user_progress (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    subject_id BIGINT UNSIGNED NOT NULL,
    topic_id BIGINT UNSIGNED NULL,
    total_questions_attempted INT UNSIGNED DEFAULT 0,
    correct_answers INT UNSIGNED DEFAULT 0,
    accuracy_percentage DECIMAL(5,2) DEFAULT 0.00,
    last_attempted_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_subject_topic (user_id, subject_id, topic_id),
    INDEX idx_user_id (user_id),
    INDEX idx_subject_id (subject_id),
    INDEX idx_topic_id (topic_id),
    INDEX idx_accuracy_percentage (accuracy_percentage)
);
```

### 14. Notifications Table
```sql
CREATE TABLE notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('daily_quiz', 'achievement', 'reminder', 'general') NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    data JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_type (type),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
);
```

### 15. User Bookmarks Table
```sql
CREATE TABLE user_bookmarks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    question_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_question (user_id, question_id),
    INDEX idx_user_id (user_id),
    INDEX idx_question_id (question_id)
);
```

## Seed Data Scripts

### Sample Subjects
```sql
INSERT INTO subjects (name, description, icon, color_code) VALUES
('Ayurveda Fundamentals', 'Basic principles and foundations of Ayurveda', 'fundamentals.png', '#4CAF50'),
('Anatomy & Physiology', 'Human anatomy and physiological systems', 'anatomy.png', '#2196F3'),
('Pharmacology', 'Ayurvedic medicines and their properties', 'pharmacy.png', '#FF9800'),
('Clinical Medicine', 'Diagnosis and treatment methods', 'clinical.png', '#F44336'),
('Surgery', 'Surgical procedures in Ayurveda', 'surgery.png', '#9C27B0'),
('Pediatrics', 'Ayurvedic treatment for children', 'pediatrics.png', '#00BCD4'),
('Gynecology', 'Women health and Ayurvedic treatment', 'gynecology.png', '#E91E63'),
('Psychiatry', 'Mental health in Ayurveda', 'psychiatry.png', '#795548');
```

### Sample Achievements
```sql
INSERT INTO achievements (name, description, icon, points_required, badge_color, category) VALUES
('First Steps', 'Complete your first quiz', 'first_quiz.png', 0, '#FFD700', 'progress'),
('Quick Learner', 'Complete 10 quizzes', 'quick_learner.png', 100, '#C0C0C0', 'progress'),
('Dedicated Student', 'Complete 50 quizzes', 'dedicated.png', 500, '#CD7F32', 'progress'),
('Perfect Score', 'Get 100% in any quiz', 'perfect.png', 0, '#FFD700', 'performance'),
('Speed Demon', 'Complete a quiz in under 5 minutes', 'speed.png', 0, '#FF4444', 'performance'),
('Week Warrior', 'Maintain 7-day streak', 'week_streak.png', 0, '#4CAF50', 'consistency'),
('Month Master', 'Maintain 30-day streak', 'month_streak.png', 0, '#2196F3', 'consistency'),
('Level Up', 'Reach Level 5', 'level_5.png', 500, '#9C27B0', 'milestone'),
('Expert', 'Reach Level 10', 'level_10.png', 1000, '#FF9800', 'milestone');
```

## Database Optimization

### Indexes for Performance
```sql
-- Additional composite indexes for better performance
CREATE INDEX idx_quiz_sessions_user_status ON quiz_sessions(user_id, status);
CREATE INDEX idx_user_answers_session_correct ON user_answers(quiz_session_id, is_correct);
CREATE INDEX idx_questions_subject_difficulty ON questions(subject_id, difficulty_level);
CREATE INDEX idx_leaderboards_period_score ON leaderboards(period, period_date, score DESC);
CREATE INDEX idx_user_progress_accuracy ON user_progress(user_id, accuracy_percentage DESC);
```

### Views for Common Queries
```sql
-- User Statistics View
CREATE VIEW user_statistics AS
SELECT 
    u.id,
    u.name,
    u.level,
    u.experience_points,
    u.daily_streak,
    COUNT(DISTINCT qs.id) as total_quizzes_taken,
    AVG(qs.score) as average_score,
    MAX(qs.score) as best_score,
    COUNT(DISTINCT ua.achievement_id) as total_achievements
FROM users u
LEFT JOIN quiz_sessions qs ON u.id = qs.user_id AND qs.status = 'completed'
LEFT JOIN user_achievements ua ON u.id = ua.user_id
GROUP BY u.id;

-- Leaderboard View
CREATE VIEW current_leaderboard AS
SELECT 
    u.name,
    u.avatar,
    u.level,
    u.experience_points,
    RANK() OVER (ORDER BY u.experience_points DESC) as rank
FROM users u
WHERE u.experience_points > 0
ORDER BY u.experience_points DESC
LIMIT 100;
```

## Database Maintenance

### Regular Cleanup Jobs
```sql
-- Clean up expired quiz sessions (older than 24 hours)
DELETE FROM quiz_sessions 
WHERE status = 'in_progress' 
AND created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR);

-- Archive old leaderboard entries (older than 1 year)
CREATE TABLE leaderboards_archive AS 
SELECT * FROM leaderboards 
WHERE period_date < DATE_SUB(CURDATE(), INTERVAL 1 YEAR);

DELETE FROM leaderboards 
WHERE period_date < DATE_SUB(CURDATE(), INTERVAL 1 YEAR);
```

### Backup Strategy
```sql
-- Daily backup script
mysqldump --single-transaction --routines --triggers ayurveda_exam_db > backup_$(date +%Y%m%d).sql

-- Weekly full backup with compression
mysqldump --single-transaction --routines --triggers ayurveda_exam_db | gzip > weekly_backup_$(date +%Y%m%d).sql.gz
```

This database schema provides a robust foundation for the Ayurvedic exam preparation app with proper indexing, relationships, and optimization for scalability.