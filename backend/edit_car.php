<?php
session_start();
include("db.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../index.php");
    exit();
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if(!$id) {
    header("Location: manage_cars.php");
    exit();
}

$car_stmt = $conn->prepare("SELECT * FROM cars WHERE id = ?");
$car_stmt->bind_param("i", $id);
$car_stmt->execute();
$car = $car_stmt->get_result()->fetch_assoc();
$car_stmt->close();

if (!$car) {
    header("Location: manage_cars.php");
    exit();
}

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
        $image = time() . "_" . basename($_FILES['carImage']['name']);
        move_uploaded_file($_FILES['carImage']['tmp_name'], '../uploads/' . $image);
    }

    $stmt = $conn->prepare(
        "UPDATE cars SET
         name = ?, type = ?, transmission = ?, year = ?, price = ?, seats = ?,
         fuel = ?, location = ?, description = ?, image = ?
         WHERE id = ?"
    );
    $stmt->bind_param(
        "sssidissssi",
        $name,
        $type,
        $transmission,
        $year,
        $price,
        $seats,
        $fuel,
        $location,
        $description,
        $image,
        $id
    );

    if($stmt->execute()){
        echo "<script>alert('Car updated'); window.location='manage_cars.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
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
