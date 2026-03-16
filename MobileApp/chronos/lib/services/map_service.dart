import 'package:flutter/material.dart';
import 'api_service.dart';

class MapService {
  /// Cache for map layout data (rarely changes)
  static Map<String, dynamic>? _layoutCache;
  static DateTime? _layoutCacheTime;
  static const Duration _layoutCacheDuration = Duration(minutes: 30);

  /// Cache for sessions by date
  static final Map<String, Map<String, dynamic>> _sessionsCache = {};
  static final Map<String, DateTime> _sessionsCacheTime = {};
  static const Duration _sessionsCacheDuration = Duration(minutes: 5);

  /// Fetch map layout with caching
  static Future<Map<String, dynamic>> getMapLayout() async {
    final now = DateTime.now();

    // Return cached layout if valid
    if (_layoutCache != null &&
        _layoutCacheTime != null &&
        now.difference(_layoutCacheTime!) < _layoutCacheDuration) {
      return _layoutCache!;
    }

    final data = await ApiService.get('map/layout.php');
    _layoutCache = data;
    _layoutCacheTime = now;
    return data;
  }

  /// Fetch sessions for a specific date with caching
  static Future<Map<String, dynamic>> getSessionsByDate(DateTime date) async {
    final dateKey = _formatDateKey(date);
    final now = DateTime.now();

    // Return cached sessions if valid
    if (_sessionsCache.containsKey(dateKey) &&
        _sessionsCacheTime.containsKey(dateKey) &&
        now.difference(_sessionsCacheTime[dateKey]!) < _sessionsCacheDuration) {
      return _sessionsCache[dateKey]!;
    }

    final dateStr = '${date.year}-${date.month.toString().padLeft(2, '0')}-${date.day.toString().padLeft(2, '0')}';
    final data = await ApiService.get('map/sessions_by_date.php?date=$dateStr');

    _sessionsCache[dateKey] = data;
    _sessionsCacheTime[dateKey] = now;
    return data;
  }

  /// Clear all caches
  static void clearCache() {
    _layoutCache = null;
    _layoutCacheTime = null;
    _sessionsCache.clear();
    _sessionsCacheTime.clear();
  }

  /// Preload sessions for a range of dates (optimization)
  static Future<void> preloadSessions(List<DateTime> dates) async {
    for (final date in dates) {
      try {
        await getSessionsByDate(date);
      } catch (e) {
        // Ignore errors during preload
      }
    }
  }

  static String _formatDateKey(DateTime date) {
    return '${date.year}-${date.month.toString().padLeft(2, '0')}-${date.day.toString().padLeft(2, '0')}';
  }
}
