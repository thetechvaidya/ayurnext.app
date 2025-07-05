import 'question_model.dart';

class QuizSessionModel {
  final int id;
  final int quizId;
  final int userId;
  final String status;
  final int currentQuestionIndex;
  final int? score;
  final int? totalQuestions;
  final DateTime? startedAt;
  final DateTime? completedAt;
  final DateTime createdAt;
  final DateTime updatedAt;
  final QuestionModel? currentQuestion;
  final Map<String, dynamic>? quizData;

  QuizSessionModel({
    required this.id,
    required this.quizId,
    required this.userId,
    required this.status,
    required this.currentQuestionIndex,
    this.score,
    this.totalQuestions,
    this.startedAt,
    this.completedAt,
    required this.createdAt,
    required this.updatedAt,
    this.currentQuestion,
    this.quizData,
  });

  factory QuizSessionModel.fromJson(Map<String, dynamic> json) {
    return QuizSessionModel(
      id: json['id'] ?? json['session_id'],
      quizId: json['quiz_id'],
      userId: json['user_id'],
      status: json['status'],
      currentQuestionIndex: json['current_question_index'] ?? 0,
      score: json['score'],
      totalQuestions: json['total_questions'],
      startedAt: json['started_at'] != null
          ? DateTime.parse(json['started_at'])
          : null,
      completedAt: json['completed_at'] != null
          ? DateTime.parse(json['completed_at'])
          : null,
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
      currentQuestion: json['current_question'] != null
          ? QuestionModel.fromJson(json['current_question'])
          : null,
      quizData: json['quiz'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'quiz_id': quizId,
      'user_id': userId,
      'status': status,
      'current_question_index': currentQuestionIndex,
      'score': score,
      'total_questions': totalQuestions,
      'started_at': startedAt?.toIso8601String(),
      'completed_at': completedAt?.toIso8601String(),
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
      'current_question': currentQuestion?.toJson(),
      'quiz': quizData,
    };
  }

  bool get isActive => status == 'active';
  bool get isCompleted => status == 'completed';
  bool get isAbandoned => status == 'abandoned';

  double get progressPercentage {
    if (totalQuestions == null || totalQuestions == 0) return 0.0;
    return (currentQuestionIndex / totalQuestions!) * 100;
  }

  String get statusDisplay {
    switch (status.toLowerCase()) {
      case 'active':
        return 'In Progress';
      case 'completed':
        return 'Completed';
      case 'abandoned':
        return 'Abandoned';
      default:
        return status;
    }
  }
}