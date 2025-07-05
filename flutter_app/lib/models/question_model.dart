class QuestionModel {
  final int id;
  final String questionText;
  final List<String> options;
  final int correctOption;
  final String? explanation;
  final String difficulty;
  final int topicId;
  final DateTime createdAt;
  final DateTime updatedAt;

  QuestionModel({
    required this.id,
    required this.questionText,
    required this.options,
    required this.correctOption,
    this.explanation,
    required this.difficulty,
    required this.topicId,
    required this.createdAt,
    required this.updatedAt,
  });

  factory QuestionModel.fromJson(Map<String, dynamic> json) {
    return QuestionModel(
      id: json['id'],
      questionText: json['question_text'],
      options: List<String>.from(json['options']),
      correctOption: json['correct_option'],
      explanation: json['explanation'],
      difficulty: json['difficulty'],
      topicId: json['topic_id'],
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'question_text': questionText,
      'options': options,
      'correct_option': correctOption,
      'explanation': explanation,
      'difficulty': difficulty,
      'topic_id': topicId,
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

  bool isCorrect(int selectedOption) {
    return selectedOption == correctOption;
  }
}