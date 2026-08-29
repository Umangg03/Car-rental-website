<?php
session_start();
include("db.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$id = $_GET['id'] ?? null;
if($id){
    $conn->query("DELETE FROM cars WHERE id=$id");
}
header("Location: manage_cars.php");
