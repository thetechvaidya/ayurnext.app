# Ayurvedic Exam Preparation Flutter App

A beautiful, modern Flutter mobile application for Ayurvedic exam preparation that connects to a Laravel backend API.

## 🌟 Features

### 📱 **Complete Mobile Experience**
- **Beautiful UI/UX** with modern Material Design
- **Responsive Design** that works on all screen sizes
- **Dark/Light Theme** support (auto-adapts to system)
- **Smooth Animations** and transitions

### 🔐 **Authentication System**
- **User Registration** with validation
- **Secure Login** with JWT tokens
- **Password Validation** and confirmation
- **Auto-logout** on token expiration
- **Session Management**

### 🎮 **Quiz Engine**
- **Interactive Quiz Interface** with progress tracking
- **Real-time Answer Submission** 
- **Question Navigation** with progress bar
- **Multiple Choice Questions** with visual selection
- **Immediate Results** with detailed scoring
- **Performance Analytics**

### 📊 **Gamification Features**
- **XP (Experience Points)** system
- **Level Progression** with rewards
- **Daily Streaks** tracking
- **Achievement System** (coming soon)
- **Leaderboards** (coming soon)

### 📚 **Content Management**
- **Subject Organization** with 8 Ayurvedic subjects
- **Topic Categorization** with 40+ topics
- **Difficulty Levels** (Easy, Medium, Hard)
- **Question Filtering** by subject and difficulty
- **Search Functionality**

### 🎯 **User Dashboard**
- **Personal Stats** overview
- **Progress Tracking** 
- **Quick Actions** for common tasks
- **Recent Activity** display
- **Performance Metrics**

## 🚀 Getting Started

### Prerequisites
- Flutter SDK (3.0.0 or higher)
- Dart SDK
- Android Studio / VS Code
- Laravel Backend API running

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/thetechvaidya/ayurnext.app.git
   cd ayurnext.app/flutter_app
   ```

2. **Install dependencies**
   ```bash
   flutter pub get
   ```

3. **Configure API Base URL**
   Update the API base URL in `lib/services/api_service.dart`:
   ```dart
   static const String baseUrl = 'http://your-api-url.com/api/v1';
   ```

4. **Run the app**
   ```bash
   flutter run
   ```

## 📱 App Architecture

### 🏗️ **Project Structure**
```
lib/
├── main.dart                 # App entry point
├── models/                   # Data models
│   ├── user_model.dart
│   ├── quiz_model.dart
│   ├── question_model.dart
│   ├── subject_model.dart
│   └── quiz_session_model.dart
├── providers/                # State management
│   ├── auth_provider.dart
│   └── quiz_provider.dart
├── services/                 # API services
│   └── api_service.dart
└── screens/                  # UI screens
    ├── auth/                 # Authentication screens
    │   ├── login_screen.dart
    │   └── register_screen.dart
    ├── home/                 # Main app screens
    │   └── dashboard_screen.dart
    ├── quiz/                 # Quiz-related screens
    │   ├── quiz_list_screen.dart
    │   └── quiz_screen.dart
    └── profile/              # User profile
        └── profile_screen.dart
```

### 🔧 **State Management**
- **Riverpod** for reactive state management
- **Provider pattern** for dependency injection
- **Immutable state objects** for predictable updates
- **Error handling** with user-friendly messages

### 🌐 **API Integration**
- **RESTful API** communication with Laravel backend
- **JWT Authentication** for secure access
- **Dio HTTP client** with interceptors
- **Error handling** and retry logic
- **Offline support** (coming soon)

## 🎨 Design System

### 🎯 **Theme**
- **Primary Color**: Forest Green (#2E8B57)
- **Secondary Color**: Sea Green (#228B22)
- **Background**: Light Grey (#F5F5F5)
- **Typography**: Poppins font family

### 🎭 **Components**
- **Custom Cards** with elevation and rounded corners
- **Gradient Backgrounds** for enhanced visual appeal
- **Icon Integration** with meaningful representations
- **Consistent Spacing** and padding
- **Responsive Layouts** for different screen sizes

## 📊 API Endpoints Used

### 🔐 Authentication
- `POST /auth/register` - User registration
- `POST /auth/login` - User login
- `GET /auth/user` - Get current user
- `POST /auth/logout` - User logout

### 📚 Content
- `GET /subjects` - Get all subjects
- `GET /subjects/{id}` - Get subject details
- `GET /quizzes` - Get quizzes with filters
- `GET /quizzes/{id}` - Get quiz details

### 🎮 Quiz Engine
- `POST /quizzes/{id}/start` - Start a quiz session
- `POST /quiz-sessions/{id}/answer` - Submit answer
- `POST /quiz-sessions/{id}/submit` - Complete quiz
- `GET /quiz-sessions/{id}/results` - Get results

## 🔧 Configuration

### 📱 **Android Setup**
1. Update `android/app/build.gradle` with proper configuration
2. Set minimum SDK version to 21
3. Configure signing keys for release builds

### 🍎 **iOS Setup**
1. Update `ios/Runner/Info.plist` with app information
2. Configure deployment target to iOS 11.0+
3. Set up provisioning profiles for App Store

### 🌐 **Network Configuration**
- Update API base URL in `api_service.dart`
- Configure timeout settings
- Set up SSL certificate pinning (recommended)

## 🧪 Testing

### Unit Tests
```bash
flutter test
```

### Integration Tests
```bash
flutter test integration_test/
```

### Widget Tests
```bash
flutter test test/widget_test.dart
```

## 📦 Build & Deployment

### 🤖 **Android Release**
```bash
flutter build apk --release
# or
flutter build appbundle --release
```

### 🍎 **iOS Release**
```bash
flutter build ios --release
```

## 🔮 Upcoming Features

- [ ] **Offline Mode** - Take quizzes without internet
- [ ] **Push Notifications** - Daily reminders and achievements
- [ ] **Social Features** - Share progress with friends
- [ ] **Advanced Analytics** - Detailed performance insights
- [ ] **Custom Study Plans** - Personalized learning paths
- [ ] **Video Lessons** - Integrated learning content
- [ ] **Voice Recognition** - Practice pronunciation
- [ ] **AR Features** - Interactive 3D models

## 🐛 Troubleshooting

### Common Issues

1. **API Connection Failed**
   - Check internet connection
   - Verify API base URL
   - Ensure Laravel backend is running

2. **Build Failures**
   - Run `flutter clean && flutter pub get`
   - Check Flutter and Dart SDK versions
   - Verify all dependencies are compatible

3. **Performance Issues**
   - Enable release mode for testing
   - Check memory usage in dev tools
   - Optimize image assets

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👥 Team

- **Development**: The Tech Vaidya Team
- **Design**: Modern Material Design Principles
- **Backend**: Laravel 10 REST API

## 📞 Support

For support and questions:
- Create an issue on GitHub
- Contact: support@ayurnext.app
- Documentation: [docs.ayurnext.app](https://docs.ayurnext.app)

---

**Made with ❤️ for Ayurvedic Education**