<?php
// backend/get_bookings.php
session_start();
require 'db.php';
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) { echo json_encode([]); exit; }

$stmt = $conn->prepare("
    SELECT b.id, b.car_id, b.start_date, b.end_date, b.status,
           b.customer_name, b.customer_email, b.customer_phone, b.customer_address,
           b.total_amount, b.deposit_amount, b.payment_status, b.mock_transaction_id,
           c.name AS car_name, c.image AS car_image
    FROM bookings b
    JOIN cars c ON b.car_id = c.id
    WHERE b.user_id = ?
    ORDER BY b.id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

$bookings = [];
while ($row = $res->fetch_assoc()) {
    $bookings[] = $row;
}

echo json_encode($bookings);
$stmt->close();
$conn->close();
?>