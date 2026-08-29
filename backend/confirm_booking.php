<?php
// backend/confirm_booking.php
// Called after mock payment success — marks booking as Confirmed + Paid
session_start();
require 'db.php';
header('Content-Type: application/json');

$user_id    = $_SESSION['user_id'] ?? null;
$booking_id = intval($_POST['booking_id'] ?? 0);

if (!$user_id || !$booking_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

// Verify booking belongs to this user
$check = $conn->prepare("SELECT id FROM bookings WHERE id = ? AND user_id = ?");
$check->bind_param("ii", $booking_id, $user_id);
$check->execute();
if ($check->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Booking not found.']);
    $check->close(); exit;
}
$check->close();

// Generate a mock transaction ID
$mock_txn_id = 'TXN' . strtoupper(uniqid());

// Update booking to Confirmed + Paid
$stmt = $conn->prepare("
    UPDATE bookings
    SET status = 'Confirmed',
        payment_status = 'Deposit Paid',
        mock_transaction_id = ?
    WHERE id = ? AND user_id = ?
");
$stmt->bind_param("sii", $mock_txn_id, $booking_id, $user_id);

if ($stmt->execute()) {
    echo json_encode([
        'success'        => true,
        'transaction_id' => $mock_txn_id,
        'booking_id'     => $booking_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Could not confirm booking: ' . $stmt->error]);
}
$stmt->close();
$conn->close();
?>