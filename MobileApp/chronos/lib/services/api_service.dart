import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;

class ApiService {
  // Base URL for the CHRONOS API
  static const String baseUrl = 'https://chronos.alwaysdata.net/TIMETABLE_APP/api';
  
  static String? _authToken;
  static VoidCallback? _onUnauthorized;

  /// Set callback to be called when 401 Unauthorized is received
  static void setOnUnauthorized(VoidCallback callback) {
    _onUnauthorized = callback;
  }

  /// Set the authentication token
  static void setAuthToken(String token) {
    _authToken = token;
  }

  /// Clear the authentication token
  static void clearAuthToken() {
    _authToken = null;
  }

  /// Get default headers with optional auth token
  static Map<String, String> _getHeaders({bool requireAuth = true}) {
    final headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };

    if (requireAuth && _authToken != null) {
      headers['Authorization'] = 'Bearer $_authToken';
    }

    return headers;
  }

  /// Parse API response
  static dynamic _handleResponse(http.Response response) {
    // Check for unauthorized status first
    if (response.statusCode == 401) {
      if (_onUnauthorized != null) {
        _onUnauthorized!();
      }
      throw ApiException('Session expirée. Veuillez vous reconnecter.', isUnauthorized: true);
    }

    final body = jsonDecode(response.body);
    
    if (response.statusCode >= 200 && response.statusCode < 300) {
      if (body['success'] == true) {
        return body['data'];
      } else {
        throw ApiException(body['message'] ?? 'La requête a échoué');
      }
    } else {
      throw ApiException(body['message'] ?? 'Erreur serveur: ${response.statusCode}');
    }
  }

  /// POST request
  static Future<dynamic> post(String endpoint, {Map<String, dynamic>? body, bool requireAuth = false}) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/$endpoint'),
        headers: _getHeaders(requireAuth: requireAuth),
        body: body != null ? jsonEncode(body) : null,
      );
      return _handleResponse(response);
    } catch (e) {
      if (e is ApiException) rethrow;
      throw ApiException('Erreur réseau: Vérifiez votre connexion internet');
    }
  }

  /// GET request
  static Future<dynamic> get(String endpoint, {bool requireAuth = true}) async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/$endpoint'),
        headers: _getHeaders(requireAuth: requireAuth),
      );
      return _handleResponse(response);
    } catch (e) {
      if (e is ApiException) rethrow;
      throw ApiException('Erreur réseau: Vérifiez votre connexion internet');
    }
  }
}

class ApiException implements Exception {
  final String message;
  final bool isUnauthorized;
  ApiException(this.message, {this.isUnauthorized = false});

  @override
  String toString() => message;
}
