<?php
$servername = "localhost";
$username   = "root";   // default WAMP MySQL user
$password   = "";       // default WAMP has empty password
$db         = "carrental"; // ✅ use your actual db name

// Create connection
$conn = new mysqli($servername, $username, $password, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
