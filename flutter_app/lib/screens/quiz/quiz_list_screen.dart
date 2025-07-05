import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../providers/quiz_provider.dart';
import '../../models/quiz_model.dart';

class QuizListScreen extends ConsumerStatefulWidget {
  const QuizListScreen({super.key});

  @override
  ConsumerState<QuizListScreen> createState() => _QuizListScreenState();
}

class _QuizListScreenState extends ConsumerState<QuizListScreen> {
  String _selectedDifficulty = 'all';
  int? _selectedSubjectId;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(quizNotifierProvider.notifier).loadQuizzes();
      ref.read(quizNotifierProvider.notifier).loadSubjects();
    });
  }

  void _filterQuizzes() {
    ref.read(quizNotifierProvider.notifier).loadQuizzes(
      subjectId: _selectedSubjectId,
      difficulty: _selectedDifficulty == 'all' ? null : _selectedDifficulty,
    );
  }

  @override
  Widget build(BuildContext context) {
    final quizzes = ref.watch(quizzesProvider);
    final subjects = ref.watch(subjectsProvider);
    final isLoading = ref.watch(quizLoadingProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Quizzes'),
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.filter_list),
            onPressed: _showFilterDialog,
          ),
        ],
      ),
      body: Column(
        children: [
          // Filter Bar
          Container(
            padding: const EdgeInsets.all(16),
            color: Colors.grey[50],
            child: Row(
              children: [
                // Difficulty Filter
                Expanded(
                  child: DropdownButtonFormField<String>(
                    value: _selectedDifficulty,
                    decoration: const InputDecoration(
                      labelText: 'Difficulty',
                      border: OutlineInputBorder(),
                      contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                    ),
                    items: [
                      const DropdownMenuItem(value: 'all', child: Text('All Levels')),
                      const DropdownMenuItem(value: 'easy', child: Text('Easy')),
                      const DropdownMenuItem(value: 'medium', child: Text('Medium')),
                      const DropdownMenuItem(value: 'hard', child: Text('Hard')),
                    ],
                    onChanged: (value) {
                      setState(() {
                        _selectedDifficulty = value!;
                      });
                      _filterQuizzes();
                    },
                  ),
                ),
                const SizedBox(width: 12),
                // Subject Filter
                Expanded(
                  child: DropdownButtonFormField<int?>(
                    value: _selectedSubjectId,
                    decoration: const InputDecoration(
                      labelText: 'Subject',
                      border: OutlineInputBorder(),
                      contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                    ),
                    items: [
                      const DropdownMenuItem(value: null, child: Text('All Subjects')),
                      ...subjects.map((subject) => DropdownMenuItem(
                        value: subject.id,
                        child: Text(subject.name),
                      )),
                    ],
                    onChanged: (value) {
                      setState(() {
                        _selectedSubjectId = value;
                      });
                      _filterQuizzes();
                    },
                  ),
                ),
              ],
            ),
          ),
          
          // Quiz List
          Expanded(
            child: isLoading
                ? const Center(child: CircularProgressIndicator())
                : quizzes.isEmpty
                    ? const Center(
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(
                              Icons.quiz,
                              size: 64,
                              color: Colors.grey,
                            ),
                            SizedBox(height: 16),
                            Text(
                              'No quizzes available',
                              style: TextStyle(
                                fontSize: 18,
                                color: Colors.grey,
                              ),
                            ),
                          ],
                        ),
                      )
                    : ListView.builder(
                        padding: const EdgeInsets.all(16),
                        itemCount: quizzes.length,
                        itemBuilder: (context, index) {
                          final quiz = quizzes[index];
                          return _buildQuizCard(quiz);
                        },
                      ),
          ),
        ],
      ),
    );
  }

  Widget _buildQuizCard(QuizModel quiz) {
    return Card(
      elevation: 4,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
      ),
      margin: const EdgeInsets.only(bottom: 16),
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: () => context.go('/quiz/${quiz.id}'),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: const Color(0xFF2E8B57).withOpacity(0.1),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: const Icon(
                      Icons.quiz,
                      color: Color(0xFF2E8B57),
                      size: 24,
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          quiz.title,
                          style: const TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          quiz.description,
                          style: TextStyle(
                            color: Colors.grey[600],
                            fontSize: 14,
                          ),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 16),
              
              // Stats
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  _buildStatChip(
                    Icons.help_outline,
                    '${quiz.questionsCount} Questions',
                    Colors.blue,
                  ),
                  _buildStatChip(
                    Icons.timer,
                    quiz.timeLimitDisplay,
                    Colors.orange,
                  ),
                  _buildStatChip(
                    Icons.trending_up,
                    quiz.difficultyDisplay,
                    _getDifficultyColor(quiz.difficulty),
                  ),
                ],
              ),
              const SizedBox(height: 16),
              
              // Start Button
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: () => context.go('/quiz/${quiz.id}'),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF2E8B57),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 12),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                  child: const Text(
                    'Start Quiz',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildStatChip(IconData icon, String label, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(20),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 16, color: color),
          const SizedBox(width: 4),
          Text(
            label,
            style: TextStyle(
              color: color,
              fontSize: 12,
              fontWeight: FontWeight.w500,
            ),
          ),
        ],
      ),
    );
  }

  Color _getDifficultyColor(String difficulty) {
    switch (difficulty.toLowerCase()) {
      case 'easy':
        return Colors.green;
      case 'medium':
        return Colors.orange;
      case 'hard':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  void _showFilterDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Filter Quizzes'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Text('Select your preferred difficulty and subject to filter quizzes.'),
            const SizedBox(height: 16),
            Row(
              children: [
                const Icon(Icons.info_outline, color: Colors.blue),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(
                    'Filters help you find quizzes that match your skill level and interests.',
                    style: TextStyle(
                      color: Colors.grey[600],
                      fontSize: 14,
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(),
            child: const Text('Close'),
          ),
        ],
      ),
    );
  }
}