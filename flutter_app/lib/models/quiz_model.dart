class QuizModel {
  final int id;
  final String title;
  final String description;
  final int subjectId;
  final String difficulty;
  final int timeLimit;
  final int questionsCount;
  final String? type;
  final bool isActive;
  final DateTime createdAt;
  final DateTime updatedAt;

  QuizModel({
    required this.id,
    required this.title,
    required this.description,
    required this.subjectId,
    required this.difficulty,
    required this.timeLimit,
    required this.questionsCount,
    this.type,
    required this.isActive,
    required this.createdAt,
    required this.updatedAt,
  });

  factory QuizModel.fromJson(Map<String, dynamic> json) {
    return QuizModel(
      id: json['id'],
      title: json['title'],
      description: json['description'],
      subjectId: json['subject_id'],
      difficulty: json['difficulty'],
      timeLimit: json['time_limit'],
      questionsCount: json['questions_count'] ?? 0,
      type: json['type'],
      isActive: json['is_active'] == 1 || json['is_active'] == true,
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'title': title,
      'description': description,
      'subject_id': subjectId,
      'difficulty': difficulty,
      'time_limit': timeLimit,
      'questions_count': questionsCount,
      'type': type,
      'is_active': isActive,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }

  String get difficultyDisplay {
    switch (difficulty.toLowerCase()) {
      case 'easy':
        return 'Easy';
      case 'medium':
        return 'Medium';
      case 'hard':
        return 'Hard';
      default:
        return difficulty;
    }
  }

  String get timeLimitDisplay {
    if (timeLimit < 60) {
      return '$timeLimit min';
    } else {
      final hours = timeLimit ~/ 60;
      final minutes = timeLimit % 60;
      return minutes > 0 ? '${hours}h ${minutes}m' : '${hours}h';
    }
  }
}