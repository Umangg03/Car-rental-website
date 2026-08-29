<?php
session_start();
include("db.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../index.php");
    exit();
}

$id = $_GET['id'] ?? null;
if(!$id) header("Location: manage_cars.php");

$car = $conn->query("SELECT * FROM cars WHERE id=$id")->fetch_assoc();

if(isset($_POST['update'])) {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $transmission = $_POST['transmission'];
    $year = $_POST['year'];
    $price = $_POST['price'];
    $seats = $_POST['seats'];
    $fuel = $_POST['fuel'];
    $location = $_POST['location'];
    $description = $_POST['description'];

    $image = $car['image'];
    if(!empty($_FILES['carImage']['name'])){
        $image = '../uploads/' . basename($_FILES['carImage']['name']);
        move_uploaded_file($_FILES['carImage']['tmp_name'], $image);
    }

    $sql = "UPDATE cars SET
        name='$name', type='$type', transmission='$transmission', year='$year',
        price='$price', seats='$seats', fuel='$fuel', location='$location',
        description='$description', image='$image'
        WHERE id=$id";

    if($conn->query($sql)){
        echo "<script>alert('Car updated'); window.location='manage_cars.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<link rel="stylesheet" href="admin.css">
<!-- Wrap the form in a styled container -->
<div class="edit-car-container">
  <h2>Edit Car</h2>
  <form method="POST" enctype="multipart/form-data" class="edit-car-form">
    <label>Car Name</label>
    <input type="text" name="name" value="<?= $car['name'] ?>" required>

    <label>Type</label>
    <input type="text" name="type" value="<?= $car['type'] ?>" required>

    <label>Year</label>
    <input type="number" name="year" value="<?= $car['year'] ?>" required>

    <label>Price ($)</label>
    <input type="number" name="price" value="<?= $car['price'] ?>" required>

    <label>Seats</label>
    <input type="number" name="seats" value="<?= $car['seats'] ?>" required>

    <label>Fuel</label>
    <input type="text" name="fuel" value="<?= $car['fuel'] ?>" required>

    <label>Transmission</label>
    <input type="text" name="transmission" value="<?= $car['transmission'] ?>" required>

    <label>Location</label>
    <input type="text" name="location" value="<?= $car['location'] ?>" required>

    <label>Description</label>
    <textarea name="description"><?= $car['description'] ?></textarea>

    <label>Change Image</label>
    <input type="file" name="carImage">

    <button type="submit" name="update" class="detail-btn-book">Update Car</button>
  </form>
</div>

