class SubjectModel {
  final int id;
  final String name;
  final String description;
  final String? icon;
  final int questionsCount;
  final DateTime createdAt;
  final DateTime updatedAt;

  SubjectModel({
    required this.id,
    required this.name,
    required this.description,
    this.icon,
    required this.questionsCount,
    required this.createdAt,
    required this.updatedAt,
  });

  factory SubjectModel.fromJson(Map<String, dynamic> json) {
    return SubjectModel(
      id: json['id'],
      name: json['name'],
      description: json['description'],
      icon: json['icon'],
      questionsCount: json['questions_count'] ?? 0,
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'description': description,
      'icon': icon,
      'questions_count': questionsCount,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }
}