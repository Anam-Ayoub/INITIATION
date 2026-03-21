import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user.dart';
import 'api_service.dart';
import 'map_service.dart';

class AuthService {
  static const String _tokenKey = 'auth_token';
  static const String _userKey = 'user_data';

  /// Login user and store token
  static Future<User> login(String email, String password) async {
    final data = await ApiService.post(
      'auth/login.php',
      body: {'email': email, 'password': password},
      requireAuth: false,
    );

    final token = data['token'] as String;
    final user = User.fromJson(data['user'] as Map<String, dynamic>);

    // Store token and user data
    await _saveAuthData(token, user);
    ApiService.setAuthToken(token);

    return user;
  }

  /// Logout user and clear stored data
  static Future<void> logout() async {
    try {
      await ApiService.post('auth/logout.php');
    } catch (e) {
      // Ignore API errors on logout
    }

    // Clear stored data
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_tokenKey);
    await prefs.remove(_userKey);
    ApiService.clearAuthToken();
    MapService.clearCache();
  }

  /// Check if user is authenticated
  static Future<bool> isAuthenticated() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString(_tokenKey);
    
    if (token != null) {
      ApiService.setAuthToken(token);
      return true;
    }
    
    return false;
  }

  /// Get stored user data
  static Future<User?> getUser() async {
    final prefs = await SharedPreferences.getInstance();
    final userJson = prefs.getString(_userKey);
    
    if (userJson != null) {
      return User.fromJson(jsonDecode(userJson));
    }
    
    return null;
  }

  /// Save auth data to storage
  static Future<void> _saveAuthData(String token, User user) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_tokenKey, token);
    await prefs.setString(_userKey, jsonEncode(user.toJson()));
  }

  /// Refresh user data from server
  static Future<User?> refreshUser() async {
    if (!await isAuthenticated()) {
      return null;
    }

    try {
      final data = await ApiService.get('student/profile.php');
      final user = User.fromJson(data);
      
      // Update stored user data
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString(_userKey, jsonEncode(user.toJson()));
      
      return user;
    } catch (e) {
      return null;
    }
  }
}
