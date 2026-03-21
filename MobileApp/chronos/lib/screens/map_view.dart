import 'package:flutter/material.dart';
import '../services/map_service.dart';
import '../widgets/weekly_day_switcher.dart';

class MapView extends StatefulWidget {
  const MapView({super.key});

  @override
  State<MapView> createState() => _MapViewState();
}

class _MapViewState extends State<MapView> {
  List<dynamic> _classrooms = [];
  List<dynamic> _roads = [];
  List<dynamic> _entrances = [];
  Map<int, List<dynamic>> _sessionsByRoom = {};
  Set<int> _activeRoomIds = {};

  int _selectedDayIndex = 0; // 0 = Lundi, 5 = Samedi
  bool _isLoading = true;
  String? _errorMessage;

  // Grid configuration
  static const int _gridWidth = 30;
  static const int _gridHeight = 20;
  static const double _cellSize = 40;

  // Colors
  static const Color _entranceColor = Color(0xFFB0C4DE);
  static const Color _roadColor = Color(0xFFB0C4DE);
  static const Color _classroomActive = Color(0xFF10B981);
  static const Color _classroomInactive = Color(0xFF9CA3AF);
  static const Color _backgroundColor = Color(0xFFF8FAFC);
  static const Color _greyDotColor = Color(0xFFD1D5DB);

  @override
  void initState() {
    super.initState();
    _loadMapData();
  }

  Future<void> _loadMapData() async {
    try {
      setState(() => _isLoading = true);

      final layout = await MapService.getMapLayout();
      _classrooms = layout['classrooms'] ?? [];
      _roads = layout['roads'] ?? [];
      _entrances = layout['entrances'] ?? [];

      await _loadSessionsForDay(_selectedDayIndex);
      MapService.preloadAllDays();

      setState(() => _isLoading = false);
    } catch (e) {
      setState(() {
        _errorMessage = 'Échec du chargement de la carte: $e';
        _isLoading = false;
      });
    }
  }

  Future<void> _loadSessionsForDay(int dayIndex) async {
    try {
      final sessions = await MapService.getSessionsByDay(dayIndex);
      final sessionsByRoom =
          sessions['sessions_by_room'] as Map<String, dynamic>? ?? {};
      final activeRoomIds = sessions['active_room_ids'] as List<dynamic>? ?? [];

      setState(() {
        _sessionsByRoom = sessionsByRoom.map(
          (key, value) => MapEntry(int.parse(key), value as List<dynamic>),
        );
        _activeRoomIds = activeRoomIds.map((id) => id as int).toSet();
      });
    } catch (e) {
      setState(() {
        _sessionsByRoom = {};
        _activeRoomIds = {};
      });
    }
  }

  void _onDayChanged(int newDayIndex) {
    setState(() => _selectedDayIndex = newDayIndex);
    _loadSessionsForDay(newDayIndex);
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
              child: WeeklyDaySwitcher(
                selectedDayIndex: _selectedDayIndex,
                onDayChanged: _onDayChanged,
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
          _legendItemWithDot('Hors carte', _greyDotColor),
          const SizedBox(width: 16),
          _legendItemWithDot('Route/Entrée', _roadColor),
          const SizedBox(width: 16),
          _legendItemWithDot('Salle occupée', _classroomActive),
          const SizedBox(width: 16),
          _legendItemWithDot('Salle libre', _classroomInactive),
        ],
      ),
    );
  }

  Widget _legendItemWithDot(String label, Color color) {
    return Row(
      children: [
        CircleAvatar(radius: 6, backgroundColor: color),
        const SizedBox(width: 6),
        Text(
          label,
          style: const TextStyle(fontSize: 11, color: Color(0xFF6B7280)),
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
              style: TextStyle(fontSize: 16, color: Colors.grey.shade600),
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
                  selectedDayIndex: _selectedDayIndex,
                  entranceColor: _entranceColor,
                  roadColor: _roadColor,
                  classroomActive: _classroomActive,
                  classroomInactive: _classroomInactive,
                  greyDotColor: _greyDotColor,
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
        selectedDay: MapService.days[_selectedDayIndex],
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
  final int selectedDayIndex;
  final Color entranceColor;
  final Color roadColor;
  final Color classroomActive;
  final Color classroomInactive;
  final Color greyDotColor;
  final Function(dynamic) onClassroomTap;

  _MapGridPainter({
    required this.classrooms,
    required this.roads,
    required this.entrances,
    required this.activeRoomIds,
    required this.sessionsByRoom,
    required this.selectedDayIndex,
    required this.entranceColor,
    required this.roadColor,
    required this.classroomActive,
    required this.classroomInactive,
    required this.greyDotColor,
    required this.onClassroomTap,
  });

  @override
  void paint(Canvas canvas, Size size) {
    // Draw grey dots for background/empty areas
    _drawBackgroundDots(canvas, size);

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

  void _drawBackgroundDots(Canvas canvas, Size size) {
    final dotPaint = Paint()
      ..color = greyDotColor
      ..style = PaintingStyle.fill;

    // Draw dots in a grid pattern for empty background
    for (int row = 0; row < 20; row++) {
      for (int col = 0; col < 30; col++) {
        final x = col * 40 + 20;
        final y = row * 40 + 20;
        canvas.drawCircle(Offset(x.toDouble(), y.toDouble()), 2, dotPaint);
      }
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

    final rect = Rect.fromLTWH(col * 40 + 5, row * 40 + 5, 30, 30);

    canvas.drawRect(rect, paint);
  }

  void _drawEntrance(Canvas canvas, dynamic entrance) {
    final row = entrance['row'] as int;
    final col = entrance['col'] as int;

    final paint = Paint()
      ..color = entranceColor
      ..style = PaintingStyle.fill;

    final rect = Rect.fromLTWH(col * 40 + 2, row * 40 + 2, 36, 36);

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
    final sessions = sessionsByRoom[roomId] ?? [];
    final hasSessions = sessions.isNotEmpty;

    final color = hasSessions ? classroomActive : classroomInactive;

    final paint = Paint()
      ..color = color
      ..style = PaintingStyle.fill;

    final rect = Rect.fromLTWH(col * 40 + 2, row * 40 + 2, 36, 36);
    final rrect = RRect.fromRectAndRadius(rect, const Radius.circular(6));
    canvas.drawRRect(rrect, paint);

    // Draw border
    final borderPaint = Paint()
      ..color = hasSessions ? const Color(0xFF059669) : const Color(0xFF6B7280)
      ..style = PaintingStyle.stroke
      ..strokeWidth = 2;
    canvas.drawRRect(rrect, borderPaint);

    // If has sessions, draw subject name above and start time inside
    if (hasSessions && sessions.isNotEmpty) {
      final session = sessions.first;
      final courseName = session['course'] as String? ?? 'Cours';
      final startTime = session['start_time'] as String? ?? '--:--';

      // Draw subject name above the cell
      final subjectTextPainter = TextPainter(
        text: TextSpan(
          text: courseName.length > 12
              ? '${courseName.substring(0, 12)}...'
              : courseName,
          style: const TextStyle(
            color: Color(0xFF1F2937),
            fontSize: 9,
            fontWeight: FontWeight.w600,
          ),
        ),
        textDirection: TextDirection.ltr,
        textAlign: TextAlign.center,
      );
      subjectTextPainter.layout(maxWidth: 40);
      subjectTextPainter.paint(
        canvas,
        Offset(
          col * 40 + 20 - subjectTextPainter.width / 2,
          row * 40 - subjectTextPainter.height - 2,
        ),
      );

      // Draw start time inside the cell
      final timeTextPainter = TextPainter(
        text: TextSpan(
          text: startTime,
          style: const TextStyle(
            color: Colors.white,
            fontSize: 10,
            fontWeight: FontWeight.w700,
          ),
        ),
        textDirection: TextDirection.ltr,
        textAlign: TextAlign.center,
      );
      timeTextPainter.layout(maxWidth: 32);
      timeTextPainter.paint(
        canvas,
        Offset(
          col * 40 + 20 - timeTextPainter.width / 2,
          row * 40 + 20 - timeTextPainter.height / 2,
        ),
      );
    } else {
      // Draw room name for inactive classrooms
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
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => true;

  @override
  bool hitTest(Offset position) => true;
}

class _ClassroomDetailsSheet extends StatelessWidget {
  final String roomName;
  final List<dynamic> sessions;
  final String selectedDay;

  const _ClassroomDetailsSheet({
    required this.roomName,
    required this.sessions,
    required this.selectedDay,
  });

  @override
  Widget build(BuildContext context) {
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
                child: const Icon(Icons.meeting_room, color: Color(0xFF4F46E5)),
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
                      '$selectedDay - ${sessions.length} cours',
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
                      style: TextStyle(color: Colors.grey.shade600),
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
            style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w600),
          ),
          const SizedBox(height: 4),
          Row(
            children: [
              Icon(Icons.person_outline, size: 14, color: Colors.grey.shade600),
              const SizedBox(width: 4),
              Text(
                session['professor'],
                style: TextStyle(fontSize: 13, color: Colors.grey.shade600),
              ),
              const SizedBox(width: 16),
              Icon(Icons.class_, size: 14, color: Colors.grey.shade600),
              const SizedBox(width: 4),
              Text(
                session['class'],
                style: TextStyle(fontSize: 13, color: Colors.grey.shade600),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
