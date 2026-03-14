class User {
  final int id;
  final String fullName;
  final String email;
  final String type;
  final int? classId;
  final String? className;

  User({
    required this.id,
    required this.fullName,
    required this.email,
    required this.type,
    this.classId,
    this.className,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'] ?? 0,
      fullName: json['full_name'] ?? '',
      email: json['email'] ?? '',
      type: json['type'] ?? 'student',
      classId: json['class_id'],
      className: json['class_name'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'full_name': fullName,
      'email': email,
      'type': type,
      'class_id': classId,
      'class_name': className,
    };
  }
}
