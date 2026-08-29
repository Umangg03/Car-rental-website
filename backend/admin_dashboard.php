<?php
session_start();
include __DIR__ . "/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// ── Stats ─────────────────────────────────────────────────────
$totalCars     = $conn->query("SELECT COUNT(*) AS t FROM cars")->fetch_assoc()['t'] ?? 0;
$totalUsers    = $conn->query("SELECT COUNT(*) AS t FROM users WHERE role='user'")->fetch_assoc()['t'] ?? 0;
$totalBookings = $conn->query("SELECT COUNT(*) AS t FROM bookings")->fetch_assoc()['t'] ?? 0;
$totalRevenue  = $conn->query("SELECT COALESCE(SUM(total_amount),0) AS t FROM bookings WHERE status='Confirmed'")->fetch_assoc()['t'] ?? 0;
$totalDepositsCollected = $conn->query("SELECT COALESCE(SUM(deposit_amount),0) AS t FROM bookings WHERE payment_status='Deposit Paid'")->fetch_assoc()['t'] ?? 0;
$pendingCount  = $conn->query("SELECT COUNT(*) AS t FROM bookings WHERE status='Pending'")->fetch_assoc()['t'] ?? 0;
$confirmedCount= $conn->query("SELECT COUNT(*) AS t FROM bookings WHERE status='Confirmed'")->fetch_assoc()['t'] ?? 0;
$cancelledCount= $conn->query("SELECT COUNT(*) AS t FROM bookings WHERE status='Cancelled'")->fetch_assoc()['t'] ?? 0;
$awaitingCount = $conn->query("SELECT COUNT(*) AS t FROM bookings WHERE status='Awaiting Payment'")->fetch_assoc()['t'] ?? 0;


// ── Recent bookings ────────────────────────────────────────────
$recentBookings = $conn->query("
    SELECT b.id, b.start_date, b.end_date, b.status,
           b.total_amount, b.deposit_amount, b.payment_status,
           b.customer_name, b.customer_phone,
           c.name AS car_name, u.name AS user_name, u.email AS user_email
    FROM bookings b
    JOIN cars c ON b.car_id = c.id
    JOIN users u ON b.user_id = u.id
    ORDER BY b.id DESC
    LIMIT 8
");

// ── Recent messages ────────────────────────────────────────────
$messages = $conn->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 5");

// ── Recently added cars ────────────────────────────────────────
$recentCars = $conn->query("SELECT name, type, price, image FROM cars ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - CarRental</title>
<link rel="stylesheet" href="admin.css">
<style>
/* ── Base ── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Segoe UI', sans-serif; background: #f4f6fb; }

/* ── Main area ── */
.main { flex: 1; padding: 30px; min-height: 100vh; overflow-x: hidden; }

.topbar {
  background: #fff; padding: 16px 24px;
  border-radius: 12px; margin-bottom: 24px;
  display: flex; justify-content: space-between; align-items: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
.topbar .welcome { font-size: 1rem; color: #555; }
.topbar .welcome b { color: #111; }
.topbar .date-str { font-size: 0.82rem; color: #aaa; }

h1 { font-size: 1.6rem; color: #111; margin-bottom: 4px; }
.subtitle { color: #888; font-size: 0.88rem; margin-bottom: 24px; }

/* ── Stat cards ── */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 14px;
  margin-bottom: 28px;
}
.stat-card {
  background: #fff;
  border-radius: 14px;
  padding: 16px 14px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.06);
  display: flex; align-items: center; gap: 12px;
  transition: transform 0.2s;
  min-width: 0;
}
.stat-card:hover { transform: translateY(-3px); }
.stat-icon {
  width: 44px; height: 44px;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.3rem; flex-shrink: 0;
}
.ic-blue   { background: #e8f0fe; }
.ic-green  { background: #d1fae5; }
.ic-yellow { background: #fef3c7; }
.ic-red    { background: #fee2e2; }
.ic-purple { background: #ede9fe; }
.stat-info { min-width: 0; overflow: hidden; }
.stat-info .num  { font-size: 1.1rem; font-weight: 800; color: #111; line-height: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.stat-info .lbl  { font-size: 0.72rem; color: #888; margin-top: 4px; }



/* ── Tables ── */
.section-card {
  background: #fff;
  border-radius: 14px;
  padding: 24px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.06);
  margin-bottom: 24px;
}
.section-card h3 {
  font-size: 1rem; color: #222;
  margin-bottom: 16px;
  display: flex; justify-content: space-between; align-items: center;
}
.section-card h3 a {
  font-size: 0.8rem; color: #0046FF;
  text-decoration: none; font-weight: 600;
}

.data-table { width: 100%; border-collapse: collapse; }
.data-table th {
  background: #f8f9fc; padding: 10px 14px;
  text-align: left; font-size: 0.78rem;
  font-weight: 700; color: #888;
  text-transform: uppercase; letter-spacing: 0.5px;
  border-bottom: 1.5px solid #f0f0f0;
}
.data-table td {
  padding: 12px 14px;
  font-size: 0.88rem; color: #333;
  border-bottom: 1px solid #f5f5f5;
  vertical-align: middle;
}
.data-table tr:last-child td { border-bottom: none; }
.data-table tr:hover td { background: #fafbff; }

/* Status badges */
.badge {
  display: inline-block; padding: 3px 10px;
  border-radius: 20px; font-size: 0.72rem; font-weight: 700;
}
.badge-confirmed  { background:#d1fae5; color:#065f46; }
.badge-pending    { background:#fef3c7; color:#92400e; }
.badge-awaiting   { background:#fee2e2; color:#991b1b; }
.badge-cancelled  { background:#f3f4f6; color:#6b7280; }
.badge-paid       { background:#d1fae5; color:#065f46; }
.badge-unpaid     { background:#fee2e2; color:#991b1b; }

/* Action buttons in table */
.btn-approve {
  background: #0046FF; color: #fff;
  border: none; padding: 5px 12px;
  border-radius: 6px; cursor: pointer;
  font-size: 0.78rem; font-weight: 600;
  margin-right: 4px;
}
.btn-cancel-bk {
  background: #fff; color: #dc2626;
  border: 1.5px solid #dc2626;
  padding: 5px 12px; border-radius: 6px;
  cursor: pointer; font-size: 0.78rem; font-weight: 600;
}
.btn-approve:hover  { background: #003ACC; }
.btn-cancel-bk:hover { background: #fee2e2; }

/* Recent cars row */
.car-row { display: flex; align-items: center; gap: 12px; }
.car-row img { width: 54px; height: 40px; object-fit: cover; border-radius: 6px; }
.car-row .car-name { font-weight: 600; font-size: 0.88rem; color: #222; }
.car-row .car-type { font-size: 0.76rem; color: #888; }

/* Messages */
.msg-item {
  padding: 12px 0; border-bottom: 1px solid #f5f5f5;
  display: flex; gap: 14px; align-items: flex-start;
}
.msg-item:last-child { border-bottom: none; }
.msg-avatar {
  width: 38px; height: 38px; border-radius: 50%;
  background: #e8f0fe; display: flex; align-items: center;
  justify-content: center; font-size: 1rem; flex-shrink: 0;
}
.msg-name { font-weight: 600; font-size: 0.88rem; color: #222; }
.msg-email { font-size: 0.76rem; color: #888; }
.msg-text { font-size: 0.82rem; color: #555; margin-top: 4px; line-height: 1.4; }
.msg-date { font-size: 0.72rem; color: #bbb; white-space: nowrap; }

/* Toast */
#toast {
  position: fixed; bottom: 24px; right: 24px;
  background: #111; color: #fff;
  padding: 12px 24px; border-radius: 10px;
  font-size: 0.88rem; font-weight: 600;
  display: none; z-index: 9999;
  box-shadow: 0 4px 20px rgba(0,0,0,0.2);
}
#toast.show { display: block; animation: slideUp 0.3s ease; }
#toast.success { background: #065f46; }
#toast.error   { background: #991b1b; }
@keyframes slideUp { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

@media(max-width:1100px) { .stats-grid { grid-template-columns: repeat(3, 1fr) !important; } }
@media(max-width:700px) { .stats-grid { grid-template-columns: repeat(2, 1fr) !important; } }
@media(max-width:900px) {
  .main { flex: 1; padding: 16px; }
  .charts-row { grid-template-columns: 1fr; }
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
    <a href="admin_dashboard.php" class="active">📊 Dashboard</a>
    <a href="admin_add_car.php">➕ Add Car</a>
    <a href="manage_cars.php">🚗 Manage Cars</a>
    <a href="manage_bookings.php">📋 Manage Bookings</a>
  </nav>
</div>

<!-- Main -->
<div class="main">

  <!-- Topbar -->
  <div class="topbar">
    <div class="welcome">Welcome back, <b><?= htmlspecialchars($_SESSION['user_name']) ?></b> 👋</div>
    <div class="date-str" id="dateStr"></div>
  </div>

  <h1>Dashboard</h1>
  <p class="subtitle">Full overview of your car rental business.</p>

  <!-- ── Stat Cards ── -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon ic-blue">🚗</div>
      <div class="stat-info">
        <div class="num"><?= $totalCars ?></div>
        <div class="lbl">Total Cars</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon ic-purple">👥</div>
      <div class="stat-info">
        <div class="num"><?= $totalUsers ?></div>
        <div class="lbl">Registered Users</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon ic-yellow">📋</div>
      <div class="stat-info">
        <div class="num"><?= $totalBookings ?></div>
        <div class="lbl">Total Bookings</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon ic-green">💰</div>
      <div class="stat-info">
        <div class="num">Rs.<?= number_format($totalRevenue, 0) ?></div>
        <div class="lbl">Total Revenue (Confirmed)</div><div style="font-size:0.72rem;color:#10b981;margin-top:3px">Deposits in hand: Rs.<?= number_format($totalDepositsCollected, 0) ?></div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon ic-red">⏳</div>
      <div class="stat-info">
        <div class="num"><?= $pendingCount ?></div>
        <div class="lbl">Pending Bookings</div>
      </div>
    </div>
  </div>
  <!-- ── Recent Bookings ── -->
  <div class="section-card">
    <h3>Recent Bookings <a href="manage_bookings.php">View All →</a></h3>
    <table class="data-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Customer</th>
          <th>Car</th>
          <th>Dates</th>
          <th>Total</th>
          <th>Payment</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($bk = $recentBookings->fetch_assoc()): ?>
        <tr id="bkrow-<?= $bk['id'] ?>">
          <td style="color:#aaa;font-size:0.78rem">#<?= $bk['id'] ?></td>
          <td>
            <div style="font-weight:600;font-size:0.88rem"><?= htmlspecialchars($bk['customer_name']) ?></div>
            <div style="font-size:0.76rem;color:#888"><?= htmlspecialchars($bk['user_email']) ?></div>
          </td>
          <td><?= htmlspecialchars($bk['car_name']) ?></td>
          <td style="font-size:0.82rem">
            <?= date('d M', strtotime($bk['start_date'])) ?> →
            <?= date('d M Y', strtotime($bk['end_date'])) ?>
          </td>
          <td>Rs.<?= number_format($bk['total_amount'], 0) ?></td>
          <td>
            <span class="badge <?= $bk['payment_status'] === 'Deposit Paid' ? 'badge-paid' : 'badge-unpaid' ?>">
              <?= htmlspecialchars($bk['payment_status'] ?? 'Unpaid') ?>
            </span>
          </td>
          <td>
            <span class="badge badge-<?= strtolower(str_replace(' ','-',$bk['status'])) ?>" id="status-<?= $bk['id'] ?>">
              <?= htmlspecialchars($bk['status']) ?>
            </span>
          </td>
          <td>
            <?php if ($bk['status'] === 'Pending' || $bk['status'] === 'Awaiting Payment'): ?>
              <button class="btn-approve" onclick="updateBooking(<?= $bk['id'] ?>,'approve')">Approve</button>
              <button class="btn-cancel-bk" onclick="updateBooking(<?= $bk['id'] ?>,'cancel')">Cancel</button>
            <?php else: ?>
              <span style="font-size:0.78rem;color:#bbb">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- ── Bottom row: Recent Cars + Messages ── -->
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">

    <!-- Recently Added Cars -->
    <div class="section-card" style="margin-bottom:0">
      <h3>Recently Added Cars <a href="manage_cars.php">View All →</a></h3>
      <table class="data-table">
        <thead>
          <tr><th>Car</th><th>Type</th><th>Price/Day</th></tr>
        </thead>
        <tbody>
          <?php while ($car = $recentCars->fetch_assoc()): ?>
          <tr>
            <td>
              <div class="car-row">
                <img src="../uploads/<?= htmlspecialchars($car['image']) ?>" onerror="this.src='../fallback.png'">
                <div>
                  <div class="car-name"><?= htmlspecialchars($car['name']) ?></div>
                </div>
              </div>
            </td>
            <td><?= htmlspecialchars($car['type']) ?></td>
            <td>Rs.<?= number_format($car['price'], 0) ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <!-- Contact Messages -->
    <div class="section-card" style="margin-bottom:0">
      <h3>Recent Messages</h3>
      <?php while ($msg = $messages->fetch_assoc()): ?>
      <div class="msg-item">
        <div class="msg-avatar">💬</div>
        <div style="flex:1">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <div class="msg-name"><?= htmlspecialchars($msg['name']) ?></div>
            <div class="msg-date"><?= date('d M', strtotime($msg['created_at'])) ?></div>
          </div>
          <div class="msg-email"><?= htmlspecialchars($msg['email']) ?></div>
          <div class="msg-text"><?= htmlspecialchars(substr($msg['message'], 0, 80)) . (strlen($msg['message']) > 80 ? '...' : '') ?></div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>

  </div>

</div><!-- /main -->

<!-- Toast -->
<div id="toast"></div>

<script>
// ── Date in topbar ───────────────────────────────────────
document.getElementById('dateStr').innerText =
  new Date().toLocaleDateString('en-IN', { weekday:'long', day:'2-digit', month:'long', year:'numeric' });


// ── Approve / Cancel booking ─────────────────────────────
async function updateBooking(id, action) {
  const formData = new FormData();
  formData.append('id', id);
  formData.append('action', action);

  const res  = await fetch('admin_updatebooking.php', { method: 'POST', body: formData });
  const data = await res.json();

  if (data.success) {
    const newStatus = action === 'approve' ? 'Confirmed' : 'Cancelled';
    const badge = document.getElementById('status-' + id);
    if (badge) {
      badge.textContent = newStatus;
      badge.className = 'badge badge-' + newStatus.toLowerCase();
    }
    // Hide action buttons for this row
    const row = document.getElementById('bkrow-' + id);
    if (row) row.querySelector('td:last-child').innerHTML = '<span style="font-size:0.78rem;color:#bbb">Done</span>';
    showToast(newStatus === 'Confirmed' ? 'Booking approved!' : 'Booking cancelled.', newStatus === 'Confirmed' ? 'success' : 'error');
  } else {
    showToast('Error: ' + data.message, 'error');
  }
}

// ── Toast ────────────────────────────────────────────────
function showToast(msg, type='success') {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = 'show ' + type;
  setTimeout(() => t.className = '', 3000);
}
</script>

</body>
</html>