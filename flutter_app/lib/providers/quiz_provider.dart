import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../models/quiz_model.dart';
import '../models/subject_model.dart';
import '../models/quiz_session_model.dart';
import '../models/question_model.dart';
import '../services/api_service.dart';

class QuizState {
  final List<QuizModel> quizzes;
  final List<SubjectModel> subjects;
  final QuizSessionModel? currentSession;
  final QuestionModel? currentQuestion;
  final bool isLoading;
  final String? error;

  QuizState({
    required this.quizzes,
    required this.subjects,
    this.currentSession,
    this.currentQuestion,
    required this.isLoading,
    this.error,
  });

  QuizState copyWith({
    List<QuizModel>? quizzes,
    List<SubjectModel>? subjects,
    QuizSessionModel? currentSession,
    QuestionModel? currentQuestion,
    bool? isLoading,
    String? error,
  }) {
    return QuizState(
      quizzes: quizzes ?? this.quizzes,
      subjects: subjects ?? this.subjects,
      currentSession: currentSession ?? this.currentSession,
      currentQuestion: currentQuestion ?? this.currentQuestion,
      isLoading: isLoading ?? this.isLoading,
      error: error ?? this.error,
    );
  }
}

class QuizNotifier extends StateNotifier<QuizState> {
  final ApiService _apiService;

  QuizNotifier(this._apiService) : super(QuizState(
    quizzes: [],
    subjects: [],
    isLoading: false,
  ));

  Future<void> loadSubjects() async {
    state = state.copyWith(isLoading: true, error: null);
    
    try {
      final subjects = await _apiService.getSubjects();
      state = state.copyWith(
        subjects: subjects,
        isLoading: false,
      );
    } catch (e) {
      state = state.copyWith(
        isLoading: false,
        error: e.toString(),
      );
    }
  }

  Future<void> loadQuizzes({
    int? subjectId,
    String? difficulty,
    int? limit,
  }) async {
    state = state.copyWith(isLoading: true, error: null);
    
    try {
      final quizzes = await _apiService.getQuizzes(
        subjectId: subjectId,
        difficulty: difficulty,
        limit: limit,
      );
      state = state.copyWith(
        quizzes: quizzes,
        isLoading: false,
      );
    } catch (e) {
      state = state.copyWith(
        isLoading: false,
        error: e.toString(),
      );
    }
  }

  Future<bool> startQuiz(int quizId) async {
    state = state.copyWith(isLoading: true, error: null);
    
    try {
      final session = await _apiService.startQuiz(quizId);
      state = state.copyWith(
        currentSession: session,
        currentQuestion: session.currentQuestion,
        isLoading: false,
      );
      return true;
    } catch (e) {
      state = state.copyWith(
        isLoading: false,
        error: e.toString(),
      );
      return false;
    }
  }

  Future<bool> submitAnswer({
    required int questionId,
    required int selectedOption,
  }) async {
    if (state.currentSession == null) return false;
    
    state = state.copyWith(isLoading: true, error: null);
    
    try {
      final response = await _apiService.submitAnswer(
        sessionId: state.currentSession!.id,
        questionId: questionId,
        selectedOption: selectedOption,
      );
      
      // Update current question if there's a next question
      if (response['next_question'] != null) {
        final nextQuestion = QuestionModel.fromJson(response['next_question']);
        state = state.copyWith(
          currentQuestion: nextQuestion,
          isLoading: false,
        );
      } else {
        // No next question, quiz is complete
        state = state.copyWith(
          currentQuestion: null,
          isLoading: false,
        );
      }
      
      return true;
    } catch (e) {
      state = state.copyWith(
        isLoading: false,
        error: e.toString(),
      );
      return false;
    }
  }

  Future<Map<String, dynamic>?> submitQuiz() async {
    if (state.currentSession == null) return null;
    
    state = state.copyWith(isLoading: true, error: null);
    
    try {
      final results = await _apiService.submitQuiz(state.currentSession!.id);
      
      // Clear current session after submission
      state = state.copyWith(
        currentSession: null,
        currentQuestion: null,
        isLoading: false,
      );
      
      return results;
    } catch (e) {
      state = state.copyWith(
        isLoading: false,
        error: e.toString(),
      );
      return null;
    }
  }

  Future<Map<String, dynamic>?> getQuizResults(int sessionId) async {
    state = state.copyWith(isLoading: true, error: null);
    
    try {
      final results = await _apiService.getQuizResults(sessionId);
      state = state.copyWith(isLoading: false);
      return results;
    } catch (e) {
      state = state.copyWith(
        isLoading: false,
        error: e.toString(),
      );
      return null;
    }
  }

  void clearError() {
    state = state.copyWith(error: null);
  }

  void clearCurrentSession() {
    state = state.copyWith(
      currentSession: null,
      currentQuestion: null,
    );
  }
}

// Providers
final quizNotifierProvider = StateNotifierProvider<QuizNotifier, QuizState>((ref) {
  return QuizNotifier(ref.watch(apiServiceProvider));
});

final quizzesProvider = Provider<List<QuizModel>>((ref) {
  return ref.watch(quizNotifierProvider).quizzes;
});

final subjectsProvider = Provider<List<SubjectModel>>((ref) {
  return ref.watch(quizNotifierProvider).subjects;
});

final currentSessionProvider = Provider<QuizSessionModel?>((ref) {
  return ref.watch(quizNotifierProvider).currentSession;
});

final currentQuestionProvider = Provider<QuestionModel?>((ref) {
  return ref.watch(quizNotifierProvider).currentQuestion;
});

final quizLoadingProvider = Provider<bool>((ref) {
  return ref.watch(quizNotifierProvider).isLoading;
});

final quizErrorProvider = Provider<String?>((ref) {
  return ref.watch(quizNotifierProvider).error;
});

// We need to import the apiServiceProvider
final apiServiceProvider = Provider((ref) => ApiService.instance);