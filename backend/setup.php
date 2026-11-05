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

$tables[] = "CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL DEFAULT 'user'
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
  description TEXT,
  image VARCHAR(255),
  status VARCHAR(20) NOT NULL DEFAULT 'Available',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
  total_amount DECIMAL(10,2) DEFAULT 0,
  deposit_amount DECIMAL(10,2) DEFAULT 0,
  payment_status VARCHAR(30) NOT NULL DEFAULT 'Unpaid',
  mock_transaction_id VARCHAR(100),
  licence_front VARCHAR(255),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (car_id) REFERENCES cars(id)
)";

$tables[] = "CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL
)";

$tables[] = "CREATE TABLE IF NOT EXISTS messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

foreach ($tables as $sql) {
  if ($conn->query($sql) === TRUE) {
    echo "Table created successfully.<br>";
  } else {
    echo "Error creating table: " . $conn->error . "<br>";
  }
}

// Add columns required by newer versions when tables already existed.
function ensureColumn(mysqli $conn, string $table, string $column, string $definition): void
{
  $table = $conn->real_escape_string($table);
  $column = $conn->real_escape_string($column);
  $exists = $conn->query(
    "SELECT 1 FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$table' AND COLUMN_NAME = '$column'"
  );

  if (!$exists) {
    echo "Error checking $table.$column: " . $conn->error . "<br>";
    return;
  }

  if ($exists->num_rows === 0) {
    if ($conn->query("ALTER TABLE `$table` ADD COLUMN `$column` $definition")) {
      echo "Added missing column $table.$column.<br>";
    } else {
      echo "Error adding $table.$column: " . $conn->error . "<br>";
    }
  }
}

ensureColumn($conn, 'users', 'role', "VARCHAR(20) NOT NULL DEFAULT 'user'");
ensureColumn($conn, 'cars', 'image', "VARCHAR(255)");
ensureColumn($conn, 'cars', 'status', "VARCHAR(20) NOT NULL DEFAULT 'Available'");
ensureColumn($conn, 'cars', 'created_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
ensureColumn($conn, 'bookings', 'total_amount', "DECIMAL(10,2) DEFAULT 0");
ensureColumn($conn, 'bookings', 'deposit_amount', "DECIMAL(10,2) DEFAULT 0");
ensureColumn($conn, 'bookings', 'payment_status', "VARCHAR(30) NOT NULL DEFAULT 'Unpaid'");
ensureColumn($conn, 'bookings', 'mock_transaction_id', "VARCHAR(100)");
ensureColumn($conn, 'bookings', 'licence_front', "VARCHAR(255)");

$conn->query("UPDATE users SET role = 'user' WHERE role IS NULL OR role = ''");

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
