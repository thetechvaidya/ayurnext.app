import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../models/user_model.dart';
import '../services/api_service.dart';

class AuthState {
  final UserModel? user;
  final bool isLoggedIn;
  final bool isLoading;
  final String? error;

  AuthState({
    this.user,
    required this.isLoggedIn,
    required this.isLoading,
    this.error,
  });

  AuthState copyWith({
    UserModel? user,
    bool? isLoggedIn,
    bool? isLoading,
    String? error,
  }) {
    return AuthState(
      user: user ?? this.user,
      isLoggedIn: isLoggedIn ?? this.isLoggedIn,
      isLoading: isLoading ?? this.isLoading,
      error: error ?? this.error,
    );
  }
}

class AuthNotifier extends StateNotifier<AuthState> {
  final ApiService _apiService;

  AuthNotifier(this._apiService) : super(AuthState(
    isLoggedIn: false,
    isLoading: false,
  )) {
    _checkAuthStatus();
  }

  Future<void> _checkAuthStatus() async {
    state = state.copyWith(isLoading: true);
    
    try {
      final token = await _apiService.getAuthToken();
      if (token != null) {
        final user = await _apiService.getCurrentUser();
        state = state.copyWith(
          user: user,
          isLoggedIn: true,
          isLoading: false,
          error: null,
        );
      } else {
        state = state.copyWith(
          isLoggedIn: false,
          isLoading: false,
        );
      }
    } catch (e) {
      state = state.copyWith(
        isLoggedIn: false,
        isLoading: false,
        error: e.toString(),
      );
    }
  }

  Future<bool> login(String email, String password) async {
    state = state.copyWith(isLoading: true, error: null);
    
    try {
      final response = await _apiService.login(
        email: email,
        password: password,
      );
      
      final user = UserModel.fromJson(response['user']);
      
      state = state.copyWith(
        user: user,
        isLoggedIn: true,
        isLoading: false,
        error: null,
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

  Future<bool> register({
    required String name,
    required String email,
    required String password,
    required String passwordConfirmation,
  }) async {
    state = state.copyWith(isLoading: true, error: null);
    
    try {
      final response = await _apiService.register(
        name: name,
        email: email,
        password: password,
        passwordConfirmation: passwordConfirmation,
      );
      
      final user = UserModel.fromJson(response['user']);
      
      state = state.copyWith(
        user: user,
        isLoggedIn: true,
        isLoading: false,
        error: null,
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

  Future<void> logout() async {
    state = state.copyWith(isLoading: true);
    
    try {
      await _apiService.logout();
    } catch (e) {
      // Continue with logout even if API call fails
      print('Logout error: $e');
    }
    
    state = AuthState(
      isLoggedIn: false,
      isLoading: false,
    );
  }

  void clearError() {
    state = state.copyWith(error: null);
  }
}

// Providers
final apiServiceProvider = Provider((ref) => ApiService.instance);

final authNotifierProvider = StateNotifierProvider<AuthNotifier, AuthState>((ref) {
  return AuthNotifier(ref.watch(apiServiceProvider));
});

final currentUserProvider = Provider<UserModel?>((ref) {
  return ref.watch(authNotifierProvider).user;
});

final isLoggedInProvider = Provider<bool>((ref) {
  return ref.watch(authNotifierProvider).isLoggedIn;
});

final isLoadingProvider = Provider<bool>((ref) {
  return ref.watch(authNotifierProvider).isLoading;
});

final authErrorProvider = Provider<String?>((ref) {
  return ref.watch(authNotifierProvider).error;
});