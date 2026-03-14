import '../models/session.dart';
import 'api_service.dart';

class TimetableService {
  /// Get student's timetable grouped by day
  static Future<Map<String, List<Session>>> getStudentTimetable() async {
    final data = await ApiService.get('student/timetable.php');
    
    final Map<String, dynamic> scheduleData = data['schedule'] ?? {};
    final Map<String, List<Session>> schedule = {};
    
    final days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
    
    for (final day in days) {
      final daySessions = scheduleData[day] as List<dynamic>? ?? [];
      schedule[day] = daySessions
          .map((session) => Session.fromJson(session as Map<String, dynamic>))
          .toList();
    }
    
    return schedule;
  }

  /// Get professor's timetable grouped by day
  static Future<Map<String, List<ProfSession>>> getProfessorTimetable() async {
    final data = await ApiService.get('prof/timetable.php');
    
    final Map<String, dynamic> scheduleData = data['schedule'] ?? {};
    final Map<String, List<ProfSession>> schedule = {};
    
    final days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
    
    for (final day in days) {
      final daySessions = scheduleData[day] as List<dynamic>? ?? [];
      schedule[day] = daySessions
          .map((session) => ProfSession.fromJson(session as Map<String, dynamic>))
          .toList();
    }
    
    return schedule;
  }

  /// Get all timetables for security (all classes)
  static Future<Map<String, List<SecuritySession>>> getSecurityTimetable() async {
    final data = await ApiService.get('security/timetable.php');
    
    final Map<String, dynamic> scheduleData = data['schedule'] ?? {};
    final Map<String, List<SecuritySession>> schedule = {};
    
    final days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
    
    for (final day in days) {
      final daySessions = scheduleData[day] as List<dynamic>? ?? [];
      schedule[day] = daySessions
          .map((session) => SecuritySession.fromJson(session as Map<String, dynamic>))
          .toList();
    }
    
    return schedule;
  }

  /// Get current day name in French
  static String getCurrentDay() {
    final now = DateTime.now();
    final days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
    return days[now.weekday - 1];
  }
}

/// Session model for professors (includes class name instead of professor name)
class ProfSession {
  final int id;
  final String course;
  final String className;
  final String room;
  final String startTime;
  final String endTime;

  ProfSession({
    required this.id,
    required this.course,
    required this.className,
    required this.room,
    required this.startTime,
    required this.endTime,
  });

  factory ProfSession.fromJson(Map<String, dynamic> json) {
    return ProfSession(
      id: json['id'] ?? 0,
      course: json['course'] ?? 'Unknown Course',
      className: json['class'] ?? 'Unknown Class',
      room: json['room'] ?? 'N/A',
      startTime: json['start_time'] ?? '00:00',
      endTime: json['end_time'] ?? '00:00',
    );
  }

  String get formattedTime => '$startTime - $endTime';
}

/// Session model for security (includes all info: class, professor, room)
class SecuritySession {
  final int id;
  final String course;
  final String className;
  final int classId;
  final String professor;
  final String room;
  final int roomId;
  final String startTime;
  final String endTime;

  SecuritySession({
    required this.id,
    required this.course,
    required this.className,
    required this.classId,
    required this.professor,
    required this.room,
    required this.roomId,
    required this.startTime,
    required this.endTime,
  });

  factory SecuritySession.fromJson(Map<String, dynamic> json) {
    return SecuritySession(
      id: json['id'] ?? 0,
      course: json['course'] ?? 'Unknown Course',
      className: json['class'] ?? 'Unknown Class',
      classId: json['class_id'] ?? 0,
      professor: json['professor'] ?? 'Unknown Professor',
      room: json['room'] ?? 'N/A',
      roomId: json['room_id'] ?? 0,
      startTime: json['start_time'] ?? '00:00',
      endTime: json['end_time'] ?? '00:00',
    );
  }

  String get formattedTime => '$startTime - $endTime';
}
