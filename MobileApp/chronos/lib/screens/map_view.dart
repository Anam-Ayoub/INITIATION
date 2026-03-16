import 'package:flutter/material.dart';
import '../services/map_service.dart';
import '../widgets/date_switcher.dart';
import 'dart:math' as math;

class MapView extends StatefulWidget {
  const MapView({super.key});

  @override
  State<MapView> createState() => _MapViewState();
}

class _MapViewState extends State<MapView> {
  Map<String, dynamic>? _mapLayout;
  List<dynamic> _classrooms = [];
  List<dynamic> _roads = [];
  List<dynamic> _entrances = [];
  Map<int, List<dynamic>> _sessionsByRoom = {};
  Set<int> _activeRoomIds = {};

  DateTime _selectedDate = DateTime.now();
  bool _isLoading = true;
  String? _errorMessage;

  // Grid configuration
  static const int _gridWidth = 30;
  static const int _gridHeight = 20;
  static const double _cellSize = 40;
  static const double _roadWidth = 30;

  // Colors
  static const Color _entranceColor = Color(0xFFB0C4DE); // Desaturated light blue
  static const Color _roadColor = Color(0xFFB0C4DE);     // Desaturated light blue
  static const Color _classroomActive = Color(0xFF10B981);  // Green when session scheduled
  static const Color _classroomInactive = Color(0xFF9CA3AF); // Grey when no session
  static const Color _backgroundColor = Color(0xFFF8FAFC);

  @override
  void initState() {
    super.initState();
    _loadMapData();
  }

  Future<void> _loadMapData() async {
    try {
      setState(() => _isLoading = true);

      // Load map layout
      final layout = await MapService.getMapLayout();
      _mapLayout = layout;
      _classrooms = layout['classrooms'] ?? [];
      _roads = layout['roads'] ?? [];
      _entrances = layout['entrances'] ?? [];

      // Load sessions for selected date
      await _loadSessionsForDate(_selectedDate);

      // Preload nearby dates for smooth navigation
      _preloadNearbyDates();

      setState(() => _isLoading = false);
    } catch (e) {
      setState(() {
        _errorMessage = 'Failed to load map: $e';
        _isLoading = false;
      });
    }
  }

  Future<void> _loadSessionsForDate(DateTime date) async {
    try {
      final sessions = await MapService.getSessionsByDate(date);
      final sessionsByRoom = sessions['sessions_by_room'] as Map<String, dynamic>? ?? {};
      final activeRoomIds = sessions['active_room_ids'] as List<dynamic>? ?? [];

      setState(() {
        _sessionsByRoom = sessionsByRoom.map(
          (key, value) => MapEntry(int.parse(key), value as List<dynamic>),
        );
        _activeRoomIds = activeRoomIds.map((id) => id as int).toSet();
      });
    } catch (e) {
      // Silently fail - rooms will show as inactive
      setState(() {
        _sessionsByRoom = {};
        _activeRoomIds = {};
      });
    }
  }

  void _preloadNearbyDates() {
    // Preload yesterday and tomorrow for smooth navigation
    final dates = [
      _selectedDate.subtract(const Duration(days: 1)),
      _selectedDate.add(const Duration(days: 1)),
    ];
    MapService.preloadSessions(dates);
  }

  void _onDateChanged(DateTime newDate) {
    setState(() => _selectedDate = newDate);
    _loadSessionsForDate(newDate);
    _preloadNearbyDates();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: _backgroundColor,
      body: Column(
        children: [
          // Date Switcher at top
          SafeArea(
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: DateSwitcher(
                selectedDate: _selectedDate,
                onDateChanged: _onDateChanged,
              ),
            ),
          ),

          // Legend
          _buildLegend(),

          // Map Area
          Expanded(
            child: _isLoading
                ? const Center(child: CircularProgressIndicator())
                : _errorMessage != null
                    ? _buildErrorView()
                    : _buildMap(),
          ),
        ],
      ),
    );
  }

  Widget _buildLegend() {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: const Color(0xFFE5E7EB)),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          _legendItem('Entrée', _entranceColor),
          const SizedBox(width: 24),
          _legendItem('Route', _roadColor),
          const SizedBox(width: 24),
          _legendItem('Salle occupée', _classroomActive),
          const SizedBox(width: 24),
          _legendItem('Salle libre', _classroomInactive),
        ],
      ),
    );
  }

  Widget _legendItem(String label, Color color) {
    return Row(
      children: [
        Container(
          width: 16,
          height: 16,
          decoration: BoxDecoration(
            color: color,
            borderRadius: BorderRadius.circular(4),
          ),
        ),
        const SizedBox(width: 6),
        Text(
          label,
          style: const TextStyle(
            fontSize: 12,
            color: Color(0xFF6B7280),
          ),
        ),
      ],
    );
  }

  Widget _buildErrorView() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.error_outline, size: 64, color: Colors.red.shade300),
          const SizedBox(height: 16),
          Text(
            _errorMessage!,
            textAlign: TextAlign.center,
            style: TextStyle(color: Colors.red.shade600),
          ),
          const SizedBox(height: 16),
          ElevatedButton(
            onPressed: _loadMapData,
            child: const Text('Réessayer'),
          ),
        ],
      ),
    );
  }

  Widget _buildMap() {
    if (_classrooms.isEmpty && _roads.isEmpty && _entrances.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.map_outlined, size: 64, color: Colors.grey.shade300),
            const SizedBox(height: 16),
            Text(
              'Aucune carte disponible',
              style: TextStyle(
                fontSize: 16,
                color: Colors.grey.shade600,
              ),
            ),
          ],
        ),
      );
    }

    return InteractiveViewer(
      boundaryMargin: const EdgeInsets.all(20),
      minScale: 0.5,
      maxScale: 3,
      child: Center(
        child: SingleChildScrollView(
          scrollDirection: Axis.horizontal,
          child: SingleChildScrollView(
            scrollDirection: Axis.vertical,
            child: Padding(
              padding: const EdgeInsets.all(20),
              child: CustomPaint(
                size: const Size(
                  _gridWidth * _cellSize,
                  _gridHeight * _cellSize,
                ),
                painter: _MapGridPainter(
                  classrooms: _classrooms,
                  roads: _roads,
                  entrances: _entrances,
                  activeRoomIds: _activeRoomIds,
                  sessionsByRoom: _sessionsByRoom,
                  selectedDate: _selectedDate,
                  entranceColor: _entranceColor,
                  roadColor: _roadColor,
                  classroomActive: _classroomActive,
                  classroomInactive: _classroomInactive,
                  onClassroomTap: _onClassroomTap,
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }

  void _onClassroomTap(dynamic classroom) {
    final roomId = classroom['room_id'] as int;
    final roomName = classroom['name'] as String;
    final sessions = _sessionsByRoom[roomId] ?? [];

    showModalBottomSheet(
      context: context,
      builder: (context) => _ClassroomDetailsSheet(
        roomName: roomName,
        sessions: sessions,
        selectedDate: _selectedDate,
      ),
    );
  }
}

class _MapGridPainter extends CustomPainter {
  final List<dynamic> classrooms;
  final List<dynamic> roads;
  final List<dynamic> entrances;
  final Set<int> activeRoomIds;
  final Map<int, List<dynamic>> sessionsByRoom;
  final DateTime selectedDate;
  final Color entranceColor;
  final Color roadColor;
  final Color classroomActive;
  final Color classroomInactive;
  final Function(dynamic) onClassroomTap;

  _MapGridPainter({
    required this.classrooms,
    required this.roads,
    required this.entrances,
    required this.activeRoomIds,
    required this.sessionsByRoom,
    required this.selectedDate,
    required this.entranceColor,
    required this.roadColor,
    required this.classroomActive,
    required this.classroomInactive,
    required this.onClassroomTap,
  });

  @override
  void paint(Canvas canvas, Size size) {
    // Draw background
    final bgPaint = Paint()..color = const Color(0xFFF8FAFC);
    canvas.drawRect(
      Rect.fromLTWH(0, 0, size.width, size.height),
      bgPaint,
    );

    // Draw grid lines
    _drawGridLines(canvas, size);

    // Draw roads
    for (final road in roads) {
      _drawRoad(canvas, road);
    }

    // Draw entrances
    for (final entrance in entrances) {
      _drawEntrance(canvas, entrance);
    }

    // Draw classrooms
    for (final classroom in classrooms) {
      _drawClassroom(canvas, classroom);
    }
  }

  void _drawGridLines(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = const Color(0xFFE5E7EB)
      ..strokeWidth = 0.5;

    // Vertical lines
    for (int i = 0; i <= 30; i++) {
      final x = i * 40.0;
      canvas.drawLine(Offset(x, 0), Offset(x, size.height), paint);
    }

    // Horizontal lines
    for (int i = 0; i <= 20; i++) {
      final y = i * 40.0;
      canvas.drawLine(Offset(0, y), Offset(size.width, y), paint);
    }
  }

  void _drawRoad(Canvas canvas, dynamic road) {
    final row = road['row'] as int;
    final col = road['col'] as int;

    final paint = Paint()
      ..color = roadColor
      ..style = PaintingStyle.fill;

    final rect = Rect.fromLTWH(
      col * 40 + 5,
      row * 40 + 5,
      30,
      30,
    );

    canvas.drawRect(rect, paint);
  }

  void _drawEntrance(Canvas canvas, dynamic entrance) {
    final row = entrance['row'] as int;
    final col = entrance['col'] as int;
    final name = entrance['name'] as String? ?? 'Entrée';

    final paint = Paint()
      ..color = entranceColor
      ..style = PaintingStyle.fill;

    final rect = Rect.fromLTWH(
      col * 40 + 2,
      row * 40 + 2,
      36,
      36,
    );

    // Draw rounded rect for entrance
    final rrect = RRect.fromRectAndRadius(rect, const Radius.circular(6));
    canvas.drawRRect(rrect, paint);

    // Draw border
    final borderPaint = Paint()
      ..color = const Color(0xFF64748B)
      ..style = PaintingStyle.stroke
      ..strokeWidth = 2;
    canvas.drawRRect(rrect, borderPaint);

    // Draw label
    final textPainter = TextPainter(
      text: TextSpan(
        text: 'E',
        style: const TextStyle(
          color: Color(0xFF475569),
          fontSize: 14,
          fontWeight: FontWeight.bold,
        ),
      ),
      textDirection: TextDirection.ltr,
    );
    textPainter.layout();
    textPainter.paint(
      canvas,
      Offset(
        col * 40 + 20 - textPainter.width / 2,
        row * 40 + 20 - textPainter.height / 2,
      ),
    );
  }

  void _drawClassroom(Canvas canvas, dynamic classroom) {
    final row = classroom['row'] as int;
    final col = classroom['col'] as int;
    final roomId = classroom['room_id'] as int;
    final name = classroom['name'] as String;

    // Determine color based on whether room has sessions
    final hasSessions = activeRoomIds.contains(roomId);
    final color = hasSessions ? classroomActive : classroomInactive;

    final paint = Paint()
      ..color = color
      ..style = PaintingStyle.fill;

    final rect = Rect.fromLTWH(
      col * 40 + 2,
      row * 40 + 2,
      36,
      36,
    );

    // Draw rounded rect
    final rrect = RRect.fromRectAndRadius(rect, const Radius.circular(6));
    canvas.drawRRect(rrect, paint);

    // Draw border
    final borderPaint = Paint()
      ..color = hasSessions ? const Color(0xFF059669) : const Color(0xFF6B7280)
      ..style = PaintingStyle.stroke
      ..strokeWidth = 2;
    canvas.drawRRect(rrect, borderPaint);

    // Draw room name
    final textPainter = TextPainter(
      text: TextSpan(
        text: name,
        style: const TextStyle(
          color: Colors.white,
          fontSize: 10,
          fontWeight: FontWeight.w600,
        ),
      ),
      textDirection: TextDirection.ltr,
      textAlign: TextAlign.center,
    );
    textPainter.layout(maxWidth: 32);
    textPainter.paint(
      canvas,
      Offset(
        col * 40 + 20 - textPainter.width / 2,
        row * 40 + 20 - textPainter.height / 2,
      ),
    );
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => true;

  @override
  bool hitTest(Offset position) => true;
}

class _ClassroomDetailsSheet extends StatelessWidget {
  final String roomName;
  final List<dynamic> sessions;
  final DateTime selectedDate;

  const _ClassroomDetailsSheet({
    required this.roomName,
    required this.sessions,
    required this.selectedDate,
  });

  @override
  Widget build(BuildContext context) {
    final dayNames = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
    final dayName = dayNames[selectedDate.weekday % 7];

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Handle bar
          Center(
            child: Container(
              width: 40,
              height: 4,
              decoration: BoxDecoration(
                color: Colors.grey.shade300,
                borderRadius: BorderRadius.circular(2),
              ),
            ),
          ),
          const SizedBox(height: 16),

          // Header
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: const Color(0xFF4F46E5).withOpacity(0.1),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: const Icon(
                  Icons.meeting_room,
                  color: Color(0xFF4F46E5),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      roomName,
                      style: const TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                    Text(
                      '$dayName - ${sessions.length} cours',
                      style: TextStyle(
                        fontSize: 14,
                        color: Colors.grey.shade600,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),

          const SizedBox(height: 20),

          // Sessions list
          if (sessions.isEmpty)
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.grey.shade50,
                borderRadius: BorderRadius.circular(12),
              ),
              child: Center(
                child: Column(
                  children: [
                    Icon(
                      Icons.event_available,
                      size: 48,
                      color: Colors.grey.shade400,
                    ),
                    const SizedBox(height: 8),
                    Text(
                      'Aucun cours dans cette salle',
                      style: TextStyle(
                        color: Colors.grey.shade600,
                      ),
                    ),
                  ],
                ),
              ),
            )
          else
            ListView.separated(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              itemCount: sessions.length,
              separatorBuilder: (_, __) => const SizedBox(height: 8),
              itemBuilder: (context, index) {
                final session = sessions[index];
                return _buildSessionCard(session);
              },
            ),

          const SizedBox(height: 20),
        ],
      ),
    );
  }

  Widget _buildSessionCard(dynamic session) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFFF8FAFC),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: const Color(0xFFE5E7EB)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                  color: const Color(0xFF4F46E5).withOpacity(0.1),
                  borderRadius: BorderRadius.circular(6),
                ),
                child: Text(
                  '${session['start_time']} - ${session['end_time']}',
                  style: const TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                    color: Color(0xFF4F46E5),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Text(
            session['course'],
            style: const TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.w600,
            ),
          ),
          const SizedBox(height: 4),
          Row(
            children: [
              Icon(Icons.person_outline, size: 14, color: Colors.grey.shade600),
              const SizedBox(width: 4),
              Text(
                session['professor'],
                style: TextStyle(
                  fontSize: 13,
                  color: Colors.grey.shade600,
                ),
              ),
              const SizedBox(width: 16),
              Icon(Icons.class_, size: 14, color: Colors.grey.shade600),
              const SizedBox(width: 4),
              Text(
                session['class'],
                style: TextStyle(
                  fontSize: 13,
                  color: Colors.grey.shade600,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
