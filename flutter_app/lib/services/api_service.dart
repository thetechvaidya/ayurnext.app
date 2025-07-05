import 'dart:convert';
import 'dart:io';
import 'package:dio/dio.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user_model.dart';
import '../models/quiz_model.dart';
import '../models/subject_model.dart';
import '../models/quiz_session_model.dart';

class ApiService {
  static const String baseUrl = 'http://localhost:8000/api/v1';
  
  late Dio _dio;
  static ApiService? _instance;
  
  ApiService._internal() {
    _dio = Dio(BaseOptions(
      baseUrl: baseUrl,
      connectTimeout: const Duration(seconds: 30),
      receiveTimeout: const Duration(seconds: 30),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    ));
    
    // Add interceptors
    _dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          // Add auth token to requests
          final token = await getAuthToken();
          if (token != null) {
            options.headers['Authorization'] = 'Bearer $token';
          }
          handler.next(options);
        },
        onError: (error, handler) {
          print('API Error: ${error.response?.data}');
          handler.next(error);
        },
      ),
    );
  }
  
  static ApiService get instance {
    _instance ??= ApiService._internal();
    return _instance!;
  }
  
  // Authentication methods
  Future<String?> getAuthToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('auth_token');
  }
  
  Future<void> setAuthToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('auth_token', token);
  }
  
  Future<void> clearAuthToken() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
  }
  
  // Auth API calls
  Future<Map<String, dynamic>> register({
    required String name,
    required String email,
    required String password,
    required String passwordConfirmation,
  }) async {
    try {
      final response = await _dio.post('/auth/register', data: {
        'name': name,
        'email': email,
        'password': password,
        'password_confirmation': passwordConfirmation,
      });
      
      if (response.data['token'] != null) {
        await setAuthToken(response.data['token']);
      }
      
      return response.data;
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }
  
  Future<Map<String, dynamic>> login({
    required String email,
    required String password,
  }) async {
    try {
      final response = await _dio.post('/auth/login', data: {
        'email': email,
        'password': password,
      });
      
      if (response.data['token'] != null) {
        await setAuthToken(response.data['token']);
      }
      
      return response.data;
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }
  
  Future<void> logout() async {
    try {
      await _dio.post('/auth/logout');
      await clearAuthToken();
    } on DioException catch (e) {
      await clearAuthToken(); // Clear token even if logout fails
      throw _handleError(e);
    }
  }
  
  Future<UserModel> getCurrentUser() async {
    try {
      final response = await _dio.get('/auth/user');
      return UserModel.fromJson(response.data['user']);
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }
  
  // Subject API calls
  Future<List<SubjectModel>> getSubjects() async {
    try {
      final response = await _dio.get('/subjects');
      return (response.data['subjects'] as List)
          .map((json) => SubjectModel.fromJson(json))
          .toList();
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }
  
  Future<SubjectModel> getSubject(int id) async {
    try {
      final response = await _dio.get('/subjects/$id');
      return SubjectModel.fromJson(response.data['subject']);
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }
  
  // Quiz API calls
  Future<List<QuizModel>> getQuizzes({
    int? subjectId,
    String? difficulty,
    int? limit,
  }) async {
    try {
      final queryParams = <String, dynamic>{};
      if (subjectId != null) queryParams['subject_id'] = subjectId;
      if (difficulty != null) queryParams['difficulty'] = difficulty;
      if (limit != null) queryParams['limit'] = limit;
      
      final response = await _dio.get('/quizzes', queryParameters: queryParams);
      return (response.data['quizzes'] as List)
          .map((json) => QuizModel.fromJson(json))
          .toList();
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }
  
  Future<QuizModel> getQuiz(int id) async {
    try {
      final response = await _dio.get('/quizzes/$id');
      return QuizModel.fromJson(response.data['quiz']);
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }
  
  Future<QuizSessionModel> startQuiz(int quizId) async {
    try {
      final response = await _dio.post('/quizzes/$quizId/start');
      return QuizSessionModel.fromJson(response.data);
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }
  
  // Quiz Session API calls
  Future<Map<String, dynamic>> submitAnswer({
    required int sessionId,
    required int questionId,
    required int selectedOption,
  }) async {
    try {
      final response = await _dio.post('/quiz-sessions/$sessionId/answer', data: {
        'question_id': questionId,
        'selected_option': selectedOption,
      });
      return response.data;
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }
  
  Future<Map<String, dynamic>> submitQuiz(int sessionId) async {
    try {
      final response = await _dio.post('/quiz-sessions/$sessionId/submit');
      return response.data;
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }
  
  Future<Map<String, dynamic>> getQuizResults(int sessionId) async {
    try {
      final response = await _dio.get('/quiz-sessions/$sessionId/results');
      return response.data;
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }
  
  // Error handling
  String _handleError(DioException e) {
    switch (e.type) {
      case DioExceptionType.connectionTimeout:
      case DioExceptionType.receiveTimeout:
        return 'Connection timeout. Please check your internet connection.';
      case DioExceptionType.badResponse:
        if (e.response?.statusCode == 401) {
          clearAuthToken();
          return 'Session expired. Please login again.';
        }
        return e.response?.data['message'] ?? 'An error occurred.';
      case DioExceptionType.cancel:
        return 'Request cancelled.';
      default:
        return 'Network error. Please try again.';
    }
  }
}