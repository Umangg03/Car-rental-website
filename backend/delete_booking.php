<?php
header('Content-Type: application/json');
include "db.php"; // your DB connection

$data = json_decode(file_get_contents("php://input"), true);

if(!isset($data['id'])){
    echo json_encode(['success'=>false,'message'=>'Missing booking ID']);
    exit;
}

$id = intval($data['id']);

$sql = "DELETE FROM bookings WHERE id=$id";

if($conn->query($sql)){
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'message'=>$conn->error]);
}
