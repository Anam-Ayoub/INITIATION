class Session {
  final int id;
  final String course;
  final String professor;
  final String room;
  final String startTime;
  final String endTime;

  Session({
    required this.id,
    required this.course,
    required this.professor,
    required this.room,
    required this.startTime,
    required this.endTime,
  });

  factory Session.fromJson(Map<String, dynamic> json) {
    return Session(
      id: json['id'] ?? 0,
      course: json['course'] ?? 'Cours inconnu',
      professor: json['professor'] ?? 'Professeur inconnu',
      room: json['room'] ?? 'S/O',
      startTime: json['start_time'] ?? '00:00',
      endTime: json['end_time'] ?? '00:00',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'course': course,
      'professor': professor,
      'room': room,
      'start_time': startTime,
      'end_time': endTime,
    };
  }

  /// Get formatted time range (e.g., "08:30 - 10:30")
  String get formattedTime => '$startTime - $endTime';
}
