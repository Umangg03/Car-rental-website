<?php
session_start();
include("db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$cars = mysqli_query($conn, "SELECT * FROM cars ORDER BY id DESC");
if (!$cars) die("Query failed: " . mysqli_error($conn));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Cars - CarRental Admin</title>
<link rel="stylesheet" href="admin.css">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Segoe UI', sans-serif; background: #f4f6fb; }
.main { flex: 1; padding: 30px; min-height: 100vh; }

.page-header {
  display: flex; justify-content: space-between;
  align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
}
.page-header h1 { font-size: 1.6rem; color: #111; }
.page-header p  { color: #888; font-size: 0.88rem; margin-top: 4px; }
.btn-add {
  padding: 10px 22px; background: #0046FF; color: #fff;
  border: none; border-radius: 8px; font-size: 0.9rem;
  font-weight: 700; cursor: pointer; text-decoration: none;
  display: inline-flex; align-items: center; gap: 8px;
}
.btn-add:hover { background: #003ACC; }

/* Filter bar */
.filter-bar { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
.filter-bar input,
.filter-bar select {
  padding: 9px 14px; border: 1.5px solid #e0e4f0;
  border-radius: 8px; font-size: 0.88rem;
  background: #fff; color: #333; outline: none;
}
.filter-bar input:focus,
.filter-bar select:focus { border-color: #0046FF; }

/* Grid view */
.cars-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
  margin-bottom: 24px;
}
.car-card-admin {
  background: #fff; border-radius: 14px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.06);
  overflow: hidden; transition: transform 0.2s;
}
.car-card-admin:hover { transform: translateY(-3px); }
.car-card-admin img {
  width: 100%; height: 170px; object-fit: cover;
}
.car-card-body { padding: 16px; }
.car-card-body h3 { font-size: 1rem; color: #111; margin-bottom: 4px; }
.car-meta { display: flex; flex-wrap: wrap; gap: 6px; margin: 10px 0; }
.meta-tag {
  background: #f0f4ff; color: #0046FF;
  padding: 3px 10px; border-radius: 20px;
  font-size: 0.72rem; font-weight: 600;
}
.meta-tag.green { background: #d1fae5; color: #065f46; }
.meta-tag.yellow { background: #fef3c7; color: #92400e; }
.car-price {
  font-size: 1.2rem; font-weight: 800; color: #0046FF; margin: 8px 0;
}
.car-price span { font-size: 0.78rem; font-weight: 400; color: #888; }
.car-desc {
  font-size: 0.78rem; color: #888; margin-bottom: 12px;
  line-height: 1.4;
  display: -webkit-box; -webkit-line-clamp: 2;
  -webkit-box-orient: vertical; overflow: hidden;
}
.car-actions { display: flex; gap: 8px; }
.btn-edit {
  flex: 1; padding: 9px; background: #0046FF; color: #fff;
  border: none; border-radius: 8px; font-size: 0.82rem;
  font-weight: 600; cursor: pointer; text-align: center;
  text-decoration: none; display: block;
}
.btn-del {
  flex: 1; padding: 9px; background: #fff; color: #dc2626;
  border: 1.5px solid #dc2626; border-radius: 8px;
  font-size: 0.82rem; font-weight: 600; cursor: pointer;
  text-align: center; text-decoration: none; display: block;
}
.btn-edit:hover { background: #003ACC; }
.btn-del:hover  { background: #fee2e2; }

/* Status badge on card */
.avail-badge {
  display: inline-block; padding: 3px 10px;
  border-radius: 20px; font-size: 0.72rem; font-weight: 700;
  margin-bottom: 6px;
}
.avail-available { background:#d1fae5; color:#065f46; }
.avail-booked    { background:#fee2e2; color:#991b1b; }

/* Empty */
.empty-state {
  text-align: center; padding: 60px; color: #aaa;
  grid-column: 1/-1;
}

@media(max-width:900px){ .main{ margin-left:0; padding:16px; } }
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
    <a href="admin_add_car.php">➕ Add Car</a>
    <a href="manage_cars.php" class="active">🚗 Manage Cars</a>
    <a href="manage_bookings.php">📋 Manage Bookings</a>
  </nav>
</div>

<!-- Main -->
<div class="main">

  <div class="page-header">
    <div>
      <h1>🚗 Manage Cars</h1>
      <p>View, edit or remove cars listed on your platform.</p>
    </div>
    <a href="admin_add_car.php" class="btn-add">➕ Add New Car</a>
  </div>

  <!-- Filters -->
  <div class="filter-bar">
    <input type="text" id="searchInput" placeholder="🔍  Search by name, type, location...">
    <select id="typeFilter" onchange="applyFilters()">
      <option value="">All Types</option>
      <option value="SUV">SUV</option>
      <option value="Sedan">Sedan</option>
      <option value="Hatchback">Hatchback</option>
      <option value="Luxury">Luxury</option>
      <option value="Sports">Sports</option>
    </select>
    <select id="fuelFilter" onchange="applyFilters()">
      <option value="">All Fuels</option>
      <option value="Petrol">Petrol</option>
      <option value="Diesel">Diesel</option>
      <option value="Electric">Electric</option>
      <option value="Hybrid">Hybrid</option>
    </select>
  </div>

  <!-- Cars Grid -->
  <div class="cars-grid" id="carsGrid">
    <?php while($row = mysqli_fetch_assoc($cars)): ?>
    <div class="car-card-admin"
         data-name="<?= strtolower(htmlspecialchars($row['name'])) ?>"
         data-type="<?= htmlspecialchars($row['type']) ?>"
         data-fuel="<?= htmlspecialchars($row['fuel']) ?>">

      <img src="../uploads/<?= htmlspecialchars($row['image']) ?>"
           alt="<?= htmlspecialchars($row['name']) ?>"
           onerror="this.src='../fallback.png'">

      <div class="car-card-body">
        <span class="avail-badge avail-<?= strtolower($row['status'] ?? 'available') ?>">
          <?= ($row['status'] ?? 'Available') === 'Available' ? '🟢 Available' : '🔴 '.$row['status'] ?>
        </span>
        <h3><?= htmlspecialchars($row['name']) ?></h3>
        <div class="car-meta">
          <span class="meta-tag"><?= htmlspecialchars($row['type']) ?></span>
          <span class="meta-tag"><?= htmlspecialchars($row['year']) ?></span>
          <span class="meta-tag"><?= htmlspecialchars($row['fuel']) ?></span>
          <span class="meta-tag <?= $row['transmission']==='Automatic'?'green':'yellow' ?>">
            <?= htmlspecialchars($row['transmission']) ?>
          </span>
          <span class="meta-tag">👥 <?= htmlspecialchars($row['seats']) ?></span>
          <span class="meta-tag">📍 <?= htmlspecialchars($row['location']) ?></span>
        </div>
        <div class="car-price">
          Rs.<?= number_format($row['price'], 0) ?> <span>/ day</span>
        </div>
        <div class="car-desc"><?= htmlspecialchars($row['description'] ?? '') ?></div>
        <div class="car-actions">
          <a href="edit_car.php?id=<?= $row['id'] ?>" class="btn-edit">✏️ Edit</a>
          <a href="delete_car.php?id=<?= $row['id'] ?>"
             class="btn-del"
             onclick="return confirm('Delete <?= addslashes($row['name']) ?>?')">🗑️ Delete</a>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>

</div>

<script>
document.getElementById('searchInput').addEventListener('input', applyFilters);

function applyFilters() {
  const search = document.getElementById('searchInput').value.toLowerCase();
  const type   = document.getElementById('typeFilter').value;
  const fuel   = document.getElementById('fuelFilter').value;

  let visible = 0;
  document.querySelectorAll('.car-card-admin').forEach(card => {
    const matchSearch = !search || card.dataset.name.includes(search);
    const matchType   = !type   || card.dataset.type === type;
    const matchFuel   = !fuel   || card.dataset.fuel === fuel;
    const show = matchSearch && matchType && matchFuel;
    card.style.display = show ? '' : 'none';
    if (show) visible++;
  });

  // Show empty state if no results
  let empty = document.getElementById('emptyState');
  if (!empty) {
    empty = document.createElement('div');
    empty.id = 'emptyState';
    empty.className = 'empty-state';
    empty.innerHTML = '<p style="font-size:2rem">🔍</p><p style="margin-top:8px">No cars match your filters.</p>';
    document.getElementById('carsGrid').appendChild(empty);
  }
  empty.style.display = visible === 0 ? 'block' : 'none';
}
</script>
</body>
</html>