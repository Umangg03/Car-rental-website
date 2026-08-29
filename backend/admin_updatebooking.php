<?php
include("db.php");

$id = $_POST['id'] ?? null;
$action = $_POST['action'] ?? null;

if(!$id || !$action){
    echo json_encode(['success'=>false, 'message'=>'Missing fields']);
    exit;
}

// Map action to status value
$status = ($action === 'approve') ? 'Confirmed' : 'Cancelled';

$sql = "UPDATE bookings SET status='$status' WHERE id=$id";

if($conn->query($sql) === TRUE){
    echo json_encode(['success'=>true, 'message'=>"Booking $status"]);
} else {
    echo json_encode(['success'=>false, 'message'=>'Error: '.$conn->error]);
}
?>
