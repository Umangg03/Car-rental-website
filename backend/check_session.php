<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        "loggedIn" => true,
        "id" => $_SESSION['user_id'],
        "name" => $_SESSION['user_name'],
        "role" => $_SESSION['role'] // ✅ important
    ]);
} else {
    echo json_encode([
        "loggedIn" => false
    ]);
}
?>
