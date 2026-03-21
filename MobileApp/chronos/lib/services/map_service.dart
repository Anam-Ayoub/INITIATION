import 'api_service.dart';

class MapService {
  /// Cache for map layout data (rarely changes)
  static Map<String, dynamic>? _layoutCache;
  static DateTime? _layoutCacheTime;
  static const Duration _layoutCacheDuration = Duration(minutes: 30);

  /// Cache for sessions by day (0-5 for Lundi-Samedi)
  static final Map<int, Map<String, dynamic>> _sessionsCache = {};
  static final Map<int, DateTime> _sessionsCacheTime = {};
  static const Duration _sessionsCacheDuration = Duration(minutes: 5);

  static const List<String> days = [
    'Lundi',
    'Mardi',
    'Mercredi',
    'Jeudi',
    'Vendredi',
    'Samedi',
  ];

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

  /// Fetch sessions for a specific day index (0-5 for Lundi-Samedi)
  static Future<Map<String, dynamic>> getSessionsByDay(int dayIndex) async {
    if (dayIndex < 0 || dayIndex > 5) {
      throw ArgumentError('Day index must be between 0 and 5');
    }

    final now = DateTime.now();

    // Return cached sessions if valid
    if (_sessionsCache.containsKey(dayIndex) &&
        _sessionsCacheTime.containsKey(dayIndex) &&
        now.difference(_sessionsCacheTime[dayIndex]!) <
            _sessionsCacheDuration) {
      return _sessionsCache[dayIndex]!;
    }

    final dayName = days[dayIndex];
    final data = await ApiService.get('map/sessions_by_day.php?day=$dayName');

    _sessionsCache[dayIndex] = data;
    _sessionsCacheTime[dayIndex] = now;
    return data;
  }

  /// Clear all caches
  static void clearCache() {
    _layoutCache = null;
    _layoutCacheTime = null;
    _sessionsCache.clear();
    _sessionsCacheTime.clear();
  }

  /// Preload sessions for all days (optimization)
  static Future<void> preloadAllDays() async {
    for (int i = 0; i < 6; i++) {
      try {
        await getSessionsByDay(i);
      } catch (e) {
        // Ignore errors during preload
      }
    }
  }
}
