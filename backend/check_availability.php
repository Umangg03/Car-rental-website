<?php
// backend/check_availability.php
// Returns availability status for a car on given dates

session_start();
include 'db.php';
header('Content-Type: application/json');

$car_id    = intval($_GET['car_id'] ?? 0);
$start     = $_GET['start'] ?? '';
$end       = $_GET['end'] ?? '';
$today     = date('Y-m-d');

// Basic validation
if (!$car_id || !$start || !$end) {
    echo json_encode(['available' => false, 'message' => 'Missing fields']);
    exit;
}

// Past date guard
if ($start < $today) {
    echo json_encode(['available' => false, 'message' => 'Pickup date cannot be in the past.']);
    exit;
}

if ($end <= $start) {
    echo json_encode(['available' => false, 'message' => 'Return date must be after pickup date.']);
    exit;
}

// Check for overlapping bookings (Pending or Confirmed only)
$stmt = $conn->prepare("
    SELECT id, end_date 
    FROM bookings 
    WHERE car_id = ?
      AND status IN ('Pending', 'Confirmed')
      AND start_date < ?
      AND end_date > ?
    LIMIT 1
");
$stmt->bind_param("iss", $car_id, $end, $start);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $conflict = $res->fetch_assoc();
    echo json_encode([
        'available'      => false,
        'message'        => 'Car is already booked for these dates.',
        'available_from' => $conflict['end_date']
    ]);
} else {
    echo json_encode(['available' => true, 'message' => 'Car is available!']);
}

$stmt->close();
$conn->close();
?>