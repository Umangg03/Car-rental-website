<?php
session_start();
include "../backend/db.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim(mysqli_real_escape_string($conn, $_POST['name']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $message = trim(mysqli_real_escape_string($conn, $_POST['message']));

    if(empty($name) || empty($email) || empty($message)) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit;
    }

    $query = "INSERT INTO messages (name, email, message) VALUES ('$name', '$email', '$message')";

    if(mysqli_query($conn, $query)){
        echo json_encode(["success" => true, "message" => "Message saved successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Database error. Try again."]);
    }
}
?>
