<?php
session_start();
include("db.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

if(isset($_POST['submit'])) {
    $brand        = $_POST['brand'] ?? '';
    $model        = $_POST['model'];
    $year         = $_POST['year'];
    $price        = $_POST['price'];
    $category     = $_POST['category'];
    $transmission = $_POST['transmission'];
    $fuel         = $_POST['fuel'];
    $seats        = $_POST['seats'];
    $location     = $_POST['location'];
    $description  = $_POST['description'];
    $name         = trim($brand . " " . $model);
    $type         = $category;
    $uploadDir    = '../uploads/';

    if(!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName   = time() . "_" . basename($_FILES['carImage']['name']);
    $targetFile = $uploadDir . $fileName;

    if(move_uploaded_file($_FILES['carImage']['tmp_name'], $targetFile)) {
        $stmt = $conn->prepare(
            "INSERT INTO cars
             (name, type, transmission, year, price, seats, fuel, location, description, image)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            echo "Database Error: " . $conn->error;
            exit();
        }

        $stmt->bind_param(
            "sssidissss",
            $name,
            $type,
            $transmission,
            $year,
            $price,
            $seats,
            $fuel,
            $location,
            $description,
            $fileName
        );

        if($stmt->execute()) {
            echo "<script>alert('Car added successfully'); window.location='manage_cars.php';</script>";
            exit();
        } else {
            echo "Database Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "<script>alert('Image Upload failed. Check permissions.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Car - CarRental Admin</title>
<link rel="stylesheet" href="admin.css">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'DM Sans', sans-serif; background: #f4f6fb; }
.main { flex: 1; padding: 30px; min-height: 100vh; }

.page-header { margin-bottom: 28px; }
.page-header h1 {
  font-family: 'Syne', sans-serif;
  font-size: 1.8rem; color: #0d0d0d;
  margin-bottom: 4px;
}
.page-header p { color: #888; font-size: 0.88rem; }

/* ── Form card ── */
.add-car-card {
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.07);
  overflow: hidden;
  max-width: 820px;
}

/* Card header strip */
.card-header {
  background: linear-gradient(135deg, #0046FF, #0035CC);
  padding: 24px 32px;
  display: flex; align-items: center; gap: 14px;
}
.card-header .icon {
  width: 48px; height: 48px; background: rgba(255,255,255,0.15);
  border-radius: 12px; display: flex; align-items: center;
  justify-content: center; font-size: 1.5rem;
}
.card-header h2 {
  font-family: 'Syne', sans-serif;
  color: #fff; font-size: 1.2rem;
}
.card-header p { color: rgba(255,255,255,0.75); font-size: 0.82rem; margin-top: 2px; }

/* Form body */
.card-body { padding: 32px; }

/* Section divider */
.form-section {
  margin-bottom: 28px;
}
.form-section-title {
  font-size: 0.72rem; font-weight: 700;
  text-transform: uppercase; letter-spacing: 1.2px;
  color: #bbb; margin-bottom: 16px;
  padding-bottom: 8px;
  border-bottom: 1.5px solid #f0f0f0;
}

/* Grid row */
.form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
.form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

/* Field */
.field { display: flex; flex-direction: column; gap: 6px; }
.field label {
  font-size: 0.8rem; font-weight: 600; color: #444;
}
.field input,
.field select,
.field textarea {
  padding: 11px 14px;
  border: 1.5px solid #e8ecf5;
  border-radius: 10px;
  font-size: 0.92rem;
  font-family: 'DM Sans', sans-serif;
  color: #222;
  background: #fafbff;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.field input:focus,
.field select:focus,
.field textarea:focus {
  border-color: #0046FF;
  box-shadow: 0 0 0 3px rgba(0,70,255,0.08);
  background: #fff;
}
.field textarea { resize: vertical; min-height: 90px; }

/* File upload */
.file-upload-wrap {
  border: 2px dashed #d0d8f0;
  border-radius: 12px;
  padding: 24px;
  text-align: center;
  background: #fafbff;
  cursor: pointer;
  transition: border-color 0.2s, background 0.2s;
  position: relative;
}
.file-upload-wrap:hover {
  border-color: #0046FF;
  background: #f0f4ff;
}
.file-upload-wrap input[type="file"] {
  position: absolute; inset: 0;
  opacity: 0; cursor: pointer; width: 100%; height: 100%;
}
.file-upload-icon { font-size: 2rem; margin-bottom: 8px; }
.file-upload-wrap p { font-size: 0.85rem; color: #888; }
.file-upload-wrap strong { color: #0046FF; }
#fileName { font-size: 0.8rem; color: #0046FF; margin-top: 6px; font-weight: 600; }

/* Submit button */
.submit-btn {
  width: 100%; padding: 15px;
  background: linear-gradient(135deg, #0046FF, #0035CC);
  color: #fff; border: none; border-radius: 12px;
  font-family: 'Syne', sans-serif;
  font-size: 1rem; font-weight: 700;
  cursor: pointer; margin-top: 8px;
  transition: opacity 0.2s, transform 0.15s;
  display: flex; align-items: center; justify-content: center; gap: 10px;
}
.submit-btn:hover { opacity: 0.92; transform: translateY(-1px); }
.submit-btn:active { transform: translateY(0); }

@media(max-width:700px) {
  .form-row-3, .form-row-2 { grid-template-columns: 1fr; }
  .card-body { padding: 20px; }
}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <div class="topbar-logo" onclick="window.location='../index.php'" style="cursor:pointer">
    <img src="../carlogo1.png" alt="CarRental Logo">
  </div>
  <div class="profile">
    <img src="admin.png" alt="Admin">
    <h3><?= htmlspecialchars($_SESSION['user_name']) ?></h3>
    <span style="font-size:0.75rem;color:#888">Administrator</span>
  </div>
  <nav>
    <a href="admin_dashboard.php">📊 Dashboard</a>
    <a href="admin_add_car.php" class="active">➕ Add Car</a>
    <a href="manage_cars.php">🚗 Manage Cars</a>
    <a href="manage_bookings.php">📋 Manage Bookings</a>
  </nav>
</div>

<!-- Main -->
<div class="main">
  <div class="page-header">
    <h1>➕ Add New Car</h1>
    <p>Fill in the details below to list a new car on the platform.</p>
  </div>

  <div class="add-car-card">
    <div class="card-header">
      <div class="icon">🚗</div>
      <div>
        <h2>Car Listing Form</h2>
        <p>All fields marked are required for listing</p>
      </div>
    </div>

    <div class="card-body">
      <form method="POST" enctype="multipart/form-data">

        <!-- Basic Info -->
        <div class="form-section">
          <div class="form-section-title">Basic Information</div>
          <div class="form-row-2" style="margin-bottom:16px">
            <div class="field">
              <label>Brand *</label>
              <input type="text" name="brand" placeholder="e.g. BMW" required>
            </div>
            <div class="field">
              <label>Model *</label>
              <input type="text" name="model" placeholder="e.g. X5" required>
            </div>
          </div>
          <div class="field">
            <label>Description</label>
            <textarea name="description" placeholder="Brief description of the car..."></textarea>
          </div>
        </div>

        <!-- Specs -->
        <div class="form-section">
          <div class="form-section-title">Specifications</div>
          <div class="form-row-3" style="margin-bottom:16px">
            <div class="field">
              <label>Year *</label>
              <input type="number" name="year" placeholder="2024" min="2000" max="2030" required>
            </div>
            <div class="field">
              <label>Daily Price (Rs.) *</label>
              <input type="number" name="price" placeholder="1500" required>
            </div>
            <div class="field">
              <label>Seating Capacity *</label>
              <input type="number" name="seats" placeholder="5" min="1" max="10" required>
            </div>
          </div>
          <div class="form-row-3">
            <div class="field">
              <label>Category *</label>
              <select name="category" required>
                <option value="">Select type</option>
                <option>SUV</option>
                <option>Sedan</option>
                <option>Hatchback</option>
                <option>Luxury</option>
                <option>Sports</option>
              </select>
            </div>
            <div class="field">
              <label>Transmission *</label>
              <select name="transmission" required>
                <option value="">Select</option>
                <option>Automatic</option>
                <option>Manual</option>
              </select>
            </div>
            <div class="field">
              <label>Fuel Type *</label>
              <select name="fuel" required>
                <option value="">Select</option>
                <option>Petrol</option>
                <option>Diesel</option>
                <option>Hybrid</option>
                <option>Electric</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Location & Image -->
        <div class="form-section">
          <div class="form-section-title">Location & Image</div>
          <div class="field" style="margin-bottom:16px">
            <label>Pickup Location *</label>
            <input type="text" name="location" placeholder="e.g. Surat, Gujarat" required>
          </div>
          <div class="field">
            <label>Car Image *</label>
            <div class="file-upload-wrap">
              <input type="file" name="carImage" accept="image/*" required
                     onchange="document.getElementById('fileName').innerText = this.files[0]?.name || ''">
              <div class="file-upload-icon">📷</div>
              <p><strong>Click to upload</strong> or drag & drop</p>
              <p>PNG, JPG, WEBP supported</p>
              <div id="fileName"></div>
            </div>
          </div>
        </div>

        <button type="submit" name="submit" class="submit-btn">
          🚗 List This Car
        </button>
      </form>
    </div>
  </div>
</div>
</body>
</html>