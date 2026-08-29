<?php
header('Content-Type: application/json');
include "db.php"; // your DB connection

$data = json_decode(file_get_contents("php://input"), true);

if(!isset($data['id'], $data['start'], $data['end'], $data['name'], $data['email'], $data['phone'], $data['address'])) {
    echo json_encode(['success'=>false,'message'=>'Missing fields']);
    exit;
}

$id = intval($data['id']);
$start = $conn->real_escape_string($data['start']);
$end = $conn->real_escape_string($data['end']);
$name = $conn->real_escape_string($data['name']);
$email = $conn->real_escape_string($data['email']);
$phone = $conn->real_escape_string($data['phone']);
$address = $conn->real_escape_string($data['address']);

$sql = "UPDATE bookings SET start_date='$start', end_date='$end', customer_name='$name', customer_email='$email', customer_phone='$phone', customer_address='$address' WHERE id=$id";

if($conn->query($sql)){
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'message'=>$conn->error]);
}
