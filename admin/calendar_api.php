<?php
session_start();
header('Content-Type: application/json');
include("db/config.php");

if (!isset($_SESSION['adminId'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    // Fetch all events (FullCalendar sends start/end params)
    case 'fetch':
        $start = $_GET['start'] ?? '';
        $end   = $_GET['end'] ?? '';

        if ($start && $end) {
            $stmt = $db->prepare("SELECT * FROM calendar_events WHERE start_date >= ? AND start_date <= ? ORDER BY start_date");
            $stmt->bind_param("ss", $start, $end);
        } else {
            $stmt = $db->prepare("SELECT * FROM calendar_events ORDER BY start_date");
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $events = [];
        while ($row = $result->fetch_assoc()) {
            $event = [
                'id'     => (int)$row['id'],
                'title'  => $row['title'],
                'start'  => $row['start_date'],
                'allDay' => (bool)$row['all_day'],
                'color'  => $row['color'],
                'extendedProps' => [
                    'type'        => $row['event_type'],
                    'description' => $row['description']
                ]
            ];
            if ($row['end_date'] && $row['end_date'] !== '0000-00-00') {
                $endDate = date('Y-m-d', strtotime($row['end_date'] . ' +1 day'));
                $event['end'] = $endDate;
            }
            $events[] = $event;
        }
        $stmt->close();
        echo json_encode($events);
        break;

    // Add new event
    case 'add':
        $title   = trim($_POST['title'] ?? '');
        $start   = $_POST['start_date'] ?? '';
        $end     = $_POST['end_date'] ?? null;
        $allDay  = isset($_POST['all_day']) ? (int)$_POST['all_day'] : 1;
        $color   = trim($_POST['color'] ?? '#007bff');
        $type    = trim($_POST['event_type'] ?? 'event');
        $desc    = trim($_POST['description'] ?? '');

        if (!$title || !$start) {
            http_response_code(400);
            echo json_encode(['error' => 'Title and start date are required']);
            exit();
        }

        $adminId = base64_decode($_SESSION['adminId']);
        $end = $end ?: null;

        $stmt = $db->prepare("INSERT INTO calendar_events (title, start_date, end_date, all_day, color, event_type, description, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['error' => 'Prepare failed: ' . $db->error]);
            exit();
        }
        $stmt->bind_param("sssisssi", $title, $start, $end, $allDay, $color, $type, $desc, $adminId);
        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode(['error' => 'Execute failed: ' . $stmt->error]);
            exit();
        }
        $id = $stmt->insert_id;
        $stmt->close();

        echo json_encode(['success' => true, 'id' => $id]);
        break;

    // Delete event
    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid event ID']);
            exit();
        }
        $stmt = $db->prepare("DELETE FROM calendar_events WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?>
