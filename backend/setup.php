<?php
$servername = "localhost";
$username = "root";   // default WAMP user
$password = "";       // default WAMP password is empty
$dbname = "carrental";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Create Database
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
  echo "Database created successfully or already exists.<br>";
} else {
  echo "Error creating database: " . $conn->error;
}

// Select Database
$conn->select_db($dbname);

// Create Tables
$tables = [];

// Users Table
$tables[] = "CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
)";

$tables[] = "CREATE TABLE IF NOT EXISTS cars (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  type VARCHAR(50),
  transmission VARCHAR(50),
  year INT,
  price DECIMAL(10,2),
  seats INT,
  fuel VARCHAR(50),
  location VARCHAR(100),
  description TEXT
)";

// Bookings Table
$tables[] = "CREATE TABLE IF NOT EXISTS bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  car_id INT,
  car_name VARCHAR(100),
  booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  start_date DATE,
  end_date DATE,
  status VARCHAR(20) DEFAULT 'Pending',
  customer_name VARCHAR(100),
  customer_email VARCHAR(100),
  customer_phone VARCHAR(20),
  customer_address TEXT,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (car_id) REFERENCES cars(id)
)";

$tables[] ="CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL
);
";

foreach ($tables as $sql) {
  if ($conn->query($sql) === TRUE) {
    echo "Table created successfully.<br>";
  } else {
    echo "Error creating table: " . $conn->error . "<br>";
  }
}

// // SQL to insert sample cars
// $sql = "
// INSERT INTO cars (name, type, transmission, year, price, seats, fuel, location, description)
// VALUES
// ('Audi Q7','SUV','Automatic',2023,3500,5,'Diesel','Chicago','Full-size luxury SUV with advanced tech');
// ";

// if ($conn->query($sql) === TRUE) {
//     echo "Sample cars inserted successfully!";
// } else {
//     echo "Error: " . $conn->error;
// }



$conn->close();
?>

