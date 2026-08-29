<?php
// backend/book_car.php  (UPDATED - now returns booking_id for payment redirect)
session_start();
require 'db.php';
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Please login to book a car.']);
    exit;
}

$car_id           = intval($_POST['car_id']           ?? 0);
$start_date       = $_POST['start_date']              ?? '';
$end_date         = $_POST['end_date']                ?? '';
$customer_name    = trim($_POST['customer_name']      ?? '');
$customer_email   = trim($_POST['customer_email']     ?? '');
$customer_phone   = trim($_POST['customer_phone']     ?? '');
$customer_address = trim($_POST['customer_address']   ?? '');
$today            = date('Y-m-d');

if (!$car_id || !$start_date || !$end_date ||
    !$customer_name || !$customer_email ||
    !$customer_phone || !$customer_address) {
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields.']);
    exit;
}

if ($start_date < $today) {
    echo json_encode(['success' => false, 'message' => 'Pickup date cannot be in the past.']);
    exit;
}
if ($end_date <= $start_date) {
    echo json_encode(['success' => false, 'message' => 'Return date must be after pickup date.']);
    exit;
}

// Availability check
$avail = $conn->prepare("
    SELECT id, end_date FROM bookings
    WHERE car_id = ?
      AND status IN ('Pending','Confirmed')
      AND start_date < ?
      AND end_date   > ?
    LIMIT 1
");
$avail->bind_param("iss", $car_id, $end_date, $start_date);
$avail->execute();
$avail_res = $avail->get_result();
if ($avail_res->num_rows > 0) {
    $conflict = $avail_res->fetch_assoc();
    echo json_encode([
        'success' => false,
        'message' => 'Car already booked for these dates. Available from '
                     . date('d M Y', strtotime($conflict['end_date'])) . '.'
    ]);
    $avail->close(); exit;
}
$avail->close();

// Get car price
$car_stmt = $conn->prepare("SELECT price, name FROM cars WHERE id = ?");
$car_stmt->bind_param("i", $car_id);
$car_stmt->execute();
$car_row = $car_stmt->get_result()->fetch_assoc();
$car_stmt->close();
if (!$car_row) {
    echo json_encode(['success' => false, 'message' => 'Car not found.']);
    exit;
}

$days           = (int) ceil((strtotime($end_date) - strtotime($start_date)) / 86400);
$total_amount   = $days * floatval($car_row['price']);
$deposit_amount = round($total_amount * 0.20, 2);

// Licence upload
if (!isset($_FILES['licence_front']) || $_FILES['licence_front']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Driving licence (front) is required.']);
    exit;
}
$allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
$file_mime     = mime_content_type($_FILES['licence_front']['tmp_name']);
if (!in_array($file_mime, $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Licence must be JPG, PNG or PDF.']);
    exit;
}
if ($_FILES['licence_front']['size'] > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'Licence file must be under 5MB.']);
    exit;
}
$upload_dir = __DIR__ . '/../uploads/documents/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
$ext           = pathinfo($_FILES['licence_front']['name'], PATHINFO_EXTENSION);
$licence_front = 'licence_' . $user_id . '_' . time() . '.' . $ext;
if (!move_uploaded_file($_FILES['licence_front']['tmp_name'], $upload_dir . $licence_front)) {
    echo json_encode(['success' => false, 'message' => 'Failed to upload licence.']);
    exit;
}

// Insert booking with status=Awaiting Payment
$sql = "INSERT INTO bookings 
        (user_id, car_id, start_date, end_date, status,
         customer_name, customer_email, customer_phone, customer_address,
         total_amount, deposit_amount, licence_front, payment_status)
        VALUES (?, ?, ?, ?, 'Awaiting Payment', ?, ?, ?, ?, ?, ?, ?, 'Unpaid')";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'DB error: ' . $conn->error]);
    exit;
}
$stmt->bind_param(
    "iissssssdds",
    $user_id, $car_id,
    $start_date, $end_date,
    $customer_name, $customer_email, $customer_phone, $customer_address,
    $total_amount, $deposit_amount,
    $licence_front
);

if ($stmt->execute()) {
    $booking_id = $conn->insert_id;
    echo json_encode([
        'success'        => true,
        'booking_id'     => $booking_id,
        'car_name'       => $car_row['name'],
        'days'           => $days,
        'total_amount'   => $total_amount,
        'deposit_amount' => $deposit_amount
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Booking failed: ' . $stmt->error]);
}
$stmt->close();
$conn->close();
?>