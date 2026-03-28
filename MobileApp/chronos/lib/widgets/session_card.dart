import 'package:flutter/material.dart';
import '../models/session.dart';

class SessionCard extends StatelessWidget {
  final Session session;
  final bool compact;

  const SessionCard({
    super.key,
    required this.session,
    this.compact = false,
  });

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: EdgeInsets.only(bottom: compact ? 8 : 12),
      elevation: 0,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
        side: BorderSide(color: const Color(0xFFE5E7EB)),
      ),
      child: Container(
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(12),
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              Colors.white,
              const Color(0xFFF9FAFB),
            ],
          ),
        ),
        child: Padding(
          padding: EdgeInsets.all(compact ? 12 : 16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Time chip
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: const Color(0xFF4F46E5).withOpacity(0.1),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Text(
                  session.formattedTime,
                  style: const TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                    color: Color(0xFF4F46E5),
                  ),
                ),
              ),
              SizedBox(height: compact ? 8 : 12),
              
              // Course name
              Text(
                session.course,
                style: TextStyle(
                  fontSize: compact ? 14 : 16,
                  fontWeight: FontWeight.w600,
                  color: const Color(0xFF1F2937),
                ),
              ),
              SizedBox(height: compact ? 4 : 8),
              
              // Professor
              Row(
                children: [
                  Icon(
                    Icons.person_outline,
                    size: compact ? 14 : 16,
                    color: const Color(0xFF6B7280),
                  ),
                  const SizedBox(width: 6),
                  Text(
                    session.professor,
                    style: TextStyle(
                      fontSize: compact ? 12 : 14,
                      color: const Color(0xFF6B7280),
                    ),
                  ),
                ],
              ),
              SizedBox(height: compact ? 2 : 4),
              
              // Room
              Row(
                children: [
                  Icon(
                    Icons.room_outlined,
                    size: compact ? 14 : 16,
                    color: const Color(0xFF6B7280),
                  ),
                  const SizedBox(width: 6),
                  Text(
                    'Salle: ${session.room}',
                    style: TextStyle(
                      fontSize: compact ? 12 : 14,
                      color: const Color(0xFF6B7280),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
