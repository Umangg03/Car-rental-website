<?php
include "backend/db.php";

$name = "Umang";
$email = "umangchaudhari670@gmail.com";
$password = "umang@123";  // <-- your chosen password

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashedPassword')";

if ($conn->query($sql) === TRUE) {
    echo "✅ Admin user created successfully!";
} else {
    echo "❌ Error: " . $conn->error;
}

$conn->close();
?>
    