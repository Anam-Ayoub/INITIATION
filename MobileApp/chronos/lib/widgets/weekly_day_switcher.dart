import 'package:flutter/material.dart';

class WeeklyDaySwitcher extends StatelessWidget {
  final int selectedDayIndex; // 0 = Lundi, 5 = Samedi
  final Function(int) onDayChanged;

  const WeeklyDaySwitcher({
    super.key,
    required this.selectedDayIndex,
    required this.onDayChanged,
  });

  static const List<String> _days = [
    'Lundi',
    'Mardi',
    'Mercredi',
    'Jeudi',
    'Vendredi',
    'Samedi',
  ];

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: const Color(0xFFE5E7EB)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 4,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          IconButton(
            onPressed: selectedDayIndex > 0
                ? () => onDayChanged(selectedDayIndex - 1)
                : null,
            icon: const Icon(Icons.chevron_left, color: Color(0xFF4F46E5)),
            padding: EdgeInsets.zero,
            constraints: const BoxConstraints(minWidth: 40, minHeight: 40),
          ),
          Text(
            _days[selectedDayIndex],
            style: const TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.w600,
              color: Color(0xFF1F2937),
            ),
          ),
          IconButton(
            onPressed: selectedDayIndex < 5
                ? () => onDayChanged(selectedDayIndex + 1)
                : null,
            icon: const Icon(Icons.chevron_right, color: Color(0xFF4F46E5)),
            padding: EdgeInsets.zero,
            constraints: const BoxConstraints(minWidth: 40, minHeight: 40),
          ),
        ],
      ),
    );
  }
}
