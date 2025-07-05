import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../providers/quiz_provider.dart';
import '../../models/question_model.dart';

class QuizScreen extends ConsumerStatefulWidget {
  final int quizId;
  
  const QuizScreen({super.key, required this.quizId});

  @override
  ConsumerState<QuizScreen> createState() => _QuizScreenState();
}

class _QuizScreenState extends ConsumerState<QuizScreen> {
  int? _selectedOption;
  bool _hasStarted = false;
  bool _showResults = false;
  Map<String, dynamic>? _currentResults;

  @override
  void initState() {
    super.initState();
    _startQuiz();
  }

  Future<void> _startQuiz() async {
    final success = await ref.read(quizNotifierProvider.notifier).startQuiz(widget.quizId);
    if (success) {
      setState(() {
        _hasStarted = true;
      });
    } else {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Failed to start quiz')),
        );
        context.go('/quizzes');
      }
    }
  }

  Future<void> _submitAnswer() async {
    if (_selectedOption == null) return;

    final currentQuestion = ref.read(currentQuestionProvider);
    if (currentQuestion == null) return;

    final success = await ref.read(quizNotifierProvider.notifier).submitAnswer(
      questionId: currentQuestion.id,
      selectedOption: _selectedOption!,
    );

    if (success) {
      setState(() {
        _selectedOption = null;
      });

      // Check if there's a next question
      final nextQuestion = ref.read(currentQuestionProvider);
      if (nextQuestion == null) {
        // Quiz is complete, submit it
        await _submitQuiz();
      }
    }
  }

  Future<void> _submitQuiz() async {
    final results = await ref.read(quizNotifierProvider.notifier).submitQuiz();
    if (results != null) {
      setState(() {
        _showResults = true;
        _currentResults = results;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final currentQuestion = ref.watch(currentQuestionProvider);
    final currentSession = ref.watch(currentSessionProvider);
    final isLoading = ref.watch(quizLoadingProvider);

    if (_showResults) {
      return _buildResultsScreen();
    }

    if (!_hasStarted || isLoading) {
      return Scaffold(
        appBar: AppBar(
          title: const Text('Loading Quiz'),
        ),
        body: const Center(
          child: CircularProgressIndicator(),
        ),
      );
    }

    if (currentQuestion == null) {
      return Scaffold(
        appBar: AppBar(
          title: const Text('Quiz Complete'),
        ),
        body: const Center(
          child: Text('Quiz completed!'),
        ),
      );
    }

    return Scaffold(
      appBar: AppBar(
        title: const Text('Quiz'),
        elevation: 0,
        actions: [
          if (currentSession != null)
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Center(
                child: Text(
                  '${currentSession.currentQuestionIndex + 1}/${currentSession.totalQuestions ?? 0}',
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ),
            ),
        ],
      ),
      body: Column(
        children: [
          // Progress Bar
          if (currentSession != null)
            Container(
              padding: const EdgeInsets.all(16),
              child: LinearProgressIndicator(
                value: (currentSession.currentQuestionIndex + 1) / (currentSession.totalQuestions ?? 1),
                backgroundColor: Colors.grey[300],
                valueColor: const AlwaysStoppedAnimation<Color>(Color(0xFF2E8B57)),
              ),
            ),
          
          // Question Content
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Question Card
                  Card(
                    elevation: 4,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: Padding(
                      padding: const EdgeInsets.all(20),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Difficulty Badge
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                            decoration: BoxDecoration(
                              color: _getDifficultyColor(currentQuestion.difficulty),
                              borderRadius: BorderRadius.circular(20),
                            ),
                            child: Text(
                              currentQuestion.difficultyDisplay,
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                          const SizedBox(height: 16),
                          
                          // Question Text
                          Text(
                            currentQuestion.questionText,
                            style: const TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                              height: 1.5,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: 24),
                  
                  // Options
                  Text(
                    'Select your answer:',
                    style: Theme.of(context).textTheme.titleMedium?.copyWith(
                      fontWeight: FontWeight.bold,
                      color: const Color(0xFF2E8B57),
                    ),
                  ),
                  const SizedBox(height: 16),
                  
                  ...currentQuestion.options.asMap().entries.map((entry) {
                    final index = entry.key;
                    final option = entry.value;
                    final isSelected = _selectedOption == index;
                    
                    return Container(
                      margin: const EdgeInsets.only(bottom: 12),
                      child: Card(
                        elevation: isSelected ? 4 : 2,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                          side: BorderSide(
                            color: isSelected ? const Color(0xFF2E8B57) : Colors.transparent,
                            width: 2,
                          ),
                        ),
                        child: InkWell(
                          borderRadius: BorderRadius.circular(12),
                          onTap: () {
                            setState(() {
                              _selectedOption = index;
                            });
                          },
                          child: Padding(
                            padding: const EdgeInsets.all(16),
                            child: Row(
                              children: [
                                Container(
                                  width: 24,
                                  height: 24,
                                  decoration: BoxDecoration(
                                    shape: BoxShape.circle,
                                    color: isSelected ? const Color(0xFF2E8B57) : Colors.transparent,
                                    border: Border.all(
                                      color: isSelected ? const Color(0xFF2E8B57) : Colors.grey,
                                      width: 2,
                                    ),
                                  ),
                                  child: isSelected
                                      ? const Icon(
                                          Icons.check,
                                          size: 16,
                                          color: Colors.white,
                                        )
                                      : null,
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Text(
                                    option,
                                    style: TextStyle(
                                      fontSize: 16,
                                      fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                                      color: isSelected ? const Color(0xFF2E8B57) : Colors.black87,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                    );
                  }).toList(),
                ],
              ),
            ),
          ),
          
          // Submit Button
          Container(
            padding: const EdgeInsets.all(16),
            child: SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: _selectedOption != null && !isLoading ? _submitAnswer : null,
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF2E8B57),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                ),
                child: isLoading
                    ? const SizedBox(
                        height: 20,
                        width: 20,
                        child: CircularProgressIndicator(
                          strokeWidth: 2,
                          valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                        ),
                      )
                    : Text(
                        currentSession != null && 
                        currentSession.currentQuestionIndex + 1 == currentSession.totalQuestions
                            ? 'Finish Quiz'
                            : 'Next Question',
                        style: const TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildResultsScreen() {
    final results = _currentResults;
    if (results == null) {
      return const Scaffold(
        body: Center(child: CircularProgressIndicator()),
      );
    }

    final score = results['score'] ?? 0;
    final totalQuestions = results['total_questions'] ?? 0;
    final percentage = totalQuestions > 0 ? (score / totalQuestions * 100).round() : 0;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Quiz Results'),
        leading: IconButton(
          icon: const Icon(Icons.close),
          onPressed: () => context.go('/dashboard'),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            // Results Card
            Card(
              elevation: 8,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(16),
              ),
              child: Container(
                width: double.infinity,
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: percentage >= 70 
                        ? [Colors.green, Colors.green.shade700]
                        : percentage >= 50 
                            ? [Colors.orange, Colors.orange.shade700]
                            : [Colors.red, Colors.red.shade700],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(16),
                ),
                padding: const EdgeInsets.all(24),
                child: Column(
                  children: [
                    Icon(
                      percentage >= 70 
                          ? Icons.celebration
                          : percentage >= 50 
                              ? Icons.thumb_up
                              : Icons.refresh,
                      size: 64,
                      color: Colors.white,
                    ),
                    const SizedBox(height: 16),
                    Text(
                      percentage >= 70 
                          ? 'Excellent!'
                          : percentage >= 50 
                              ? 'Good Job!'
                              : 'Keep Practicing!',
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 24,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      'You scored $score out of $totalQuestions',
                      style: const TextStyle(
                        color: Colors.white70,
                        fontSize: 16,
                      ),
                    ),
                    const SizedBox(height: 16),
                    Text(
                      '$percentage%',
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 48,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 24),
            
            // Action Buttons
            Column(
              children: [
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: () => context.go('/dashboard'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF2E8B57),
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                    child: const Text(
                      'Back to Dashboard',
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ),
                const SizedBox(height: 12),
                SizedBox(
                  width: double.infinity,
                  child: OutlinedButton(
                    onPressed: () => context.go('/quizzes'),
                    style: OutlinedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                    child: const Text(
                      'Take Another Quiz',
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
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
}