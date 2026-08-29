<?php
session_start();
include("db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$bookings = mysqli_query($conn, "
    SELECT b.*, c.name AS car_name, c.type AS car_type, c.image AS car_image,
           u.name AS user_name, u.email AS user_email
    FROM bookings b
    JOIN cars c ON b.car_id = c.id
    JOIN users u ON b.user_id = u.id
    ORDER BY b.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Bookings - CarRental Admin</title>
<link rel="stylesheet" href="admin.css">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Segoe UI', sans-serif; background: #f4f6fb; }
.main { flex: 1; padding: 30px; min-height: 100vh; }

.page-header {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
}
.page-header h1 { font-size: 1.6rem; color: #111; }
.page-header p  { color: #888; font-size: 0.88rem; margin-top: 4px; }

/* Filter bar */
.filter-bar {
  display: flex; gap: 12px; flex-wrap: wrap;
  margin-bottom: 20px;
}
.filter-bar select,
.filter-bar input {
  padding: 9px 14px; border: 1.5px solid #e0e4f0;
  border-radius: 8px; font-size: 0.88rem;
  background: #fff; color: #333; outline: none;
}
.filter-bar select:focus,
.filter-bar input:focus { border-color: #0046FF; }

/* Table card */
.table-card {
  background: #fff; border-radius: 14px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.06);
  overflow: hidden;
}
.data-table { width: 100%; border-collapse: collapse; }
.data-table th {
  background: #f8f9fc; padding: 12px 16px;
  text-align: left; font-size: 0.76rem;
  font-weight: 700; color: #888;
  text-transform: uppercase; letter-spacing: 0.5px;
  border-bottom: 1.5px solid #f0f0f0;
  white-space: nowrap;
}
.data-table td {
  padding: 14px 16px; font-size: 0.87rem;
  color: #333; border-bottom: 1px solid #f5f5f5;
  vertical-align: middle;
}
.data-table tr:last-child td { border-bottom: none; }
.data-table tr:hover td { background: #fafbff; }

/* Car cell */
.car-cell { display: flex; align-items: center; gap: 10px; }
.car-cell img { width: 52px; height: 38px; object-fit: cover; border-radius: 6px; flex-shrink: 0; }
.car-cell .cn { font-weight: 600; font-size: 0.87rem; }
.car-cell .ct { font-size: 0.75rem; color: #888; }

/* Customer cell */
.cust-name  { font-weight: 600; font-size: 0.87rem; }
.cust-email { font-size: 0.75rem; color: #888; }
.cust-phone { font-size: 0.75rem; color: #666; }

/* Badges */
.badge {
  display: inline-block; padding: 3px 10px;
  border-radius: 20px; font-size: 0.72rem; font-weight: 700;
  white-space: nowrap;
}
.badge-confirmed  { background:#d1fae5; color:#065f46; }
.badge-pending    { background:#fef3c7; color:#92400e; }
.badge-awaiting-payment { background:#fee2e2; color:#991b1b; }
.badge-cancelled  { background:#f3f4f6; color:#6b7280; }
.badge-paid       { background:#d1fae5; color:#065f46; }
.badge-unpaid,
.badge-deposit-pending { background:#fee2e2; color:#991b1b; }

/* Licence link */
.lic-link {
  font-size: 0.76rem; color: #0046FF;
  text-decoration: none; display: flex;
  align-items: center; gap: 4px;
}
.lic-link:hover { text-decoration: underline; }

/* Action buttons */
.btn-approve {
  background: #0046FF; color: #fff;
  border: none; padding: 6px 14px;
  border-radius: 6px; cursor: pointer;
  font-size: 0.78rem; font-weight: 600;
  white-space: nowrap;
}
.btn-cancel-bk {
  background: #fff; color: #dc2626;
  border: 1.5px solid #dc2626;
  padding: 6px 14px; border-radius: 6px;
  cursor: pointer; font-size: 0.78rem;
  font-weight: 600; white-space: nowrap;
}
.btn-approve:hover   { background: #003ACC; }
.btn-cancel-bk:hover { background: #fee2e2; }
.action-wrap { display: flex; gap: 6px; flex-wrap: wrap; }

/* Empty state */
.empty-row td {
  text-align: center; padding: 50px !important;
  color: #aaa; font-size: 0.95rem;
}

/* Toast */
#toast {
  position: fixed; bottom: 24px; right: 24px;
  padding: 12px 24px; border-radius: 10px;
  font-size: 0.88rem; font-weight: 600; color: #fff;
  display: none; z-index: 9999;
  box-shadow: 0 4px 20px rgba(0,0,0,0.2);
}
#toast.show   { display: block; animation: su 0.3s ease; }
#toast.success { background: #065f46; }
#toast.error   { background: #991b1b; }
@keyframes su { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }

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
    <a href="manage_cars.php">🚗 Manage Cars</a>
    <a href="manage_bookings.php" class="active">📋 Manage Bookings</a>
  </nav>
</div>

<!-- Main -->
<div class="main">

  <div class="page-header">
    <div>
      <h1>📋 Manage Bookings</h1>
      <p>View all bookings, approve or cancel requests, and check uploaded documents.</p>
    </div>
  </div>

  <!-- Filters -->
  <div class="filter-bar">
    <input type="text" id="searchInput" placeholder="🔍  Search by name, car, email...">
    <select id="statusFilter" onchange="applyFilters()">
      <option value="">All Statuses</option>
      <option value="Pending">Pending</option>
      <option value="Confirmed">Confirmed</option>
      <option value="Awaiting Payment">Awaiting Payment</option>
      <option value="Cancelled">Cancelled</option>
    </select>
    <select id="payFilter" onchange="applyFilters()">
      <option value="">All Payments</option>
      <option value="Deposit Paid">Deposit Paid</option>
      <option value="Deposit Pending">Deposit Pending</option>
      <option value="Unpaid">Unpaid</option>
    </select>
  </div>

  <!-- Table -->
  <div class="table-card">
    <table class="data-table" id="bookingsTable">
      <thead>
        <tr>
          <th>#</th>
          <th>Customer</th>
          <th>Car</th>
          <th>Dates</th>
          <th>Amount</th>
          <th>Payment</th>
          <th>Licence</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="tableBody">
        <?php
        $count = 0;
        while($row = mysqli_fetch_assoc($bookings)):
          $count++;
          $days = ceil((strtotime($row['end_date']) - strtotime($row['start_date'])) / 86400);
          $statusSlug = strtolower(str_replace(' ','-',$row['status']));
          $paySlug    = strtolower(str_replace(' ','-',$row['payment_status'] ?? 'unpaid'));
        ?>
        <tr data-status="<?= htmlspecialchars($row['status']) ?>"
            data-pay="<?= htmlspecialchars($row['payment_status'] ?? '') ?>"
            data-search="<?= strtolower(htmlspecialchars($row['user_name'].$row['user_email'].$row['car_name'].$row['customer_phone'])) ?>"
            id="row-<?= $row['id'] ?>">

          <td style="color:#aaa;font-size:0.78rem">#<?= $row['id'] ?></td>

          <td>
            <div class="cust-name"><?= htmlspecialchars($row['customer_name'] ?? $row['user_name']) ?></div>
            <div class="cust-email"><?= htmlspecialchars($row['user_email']) ?></div>
            <div class="cust-phone"><?= htmlspecialchars($row['customer_phone'] ?? '—') ?></div>
          </td>

          <td>
            <div class="car-cell">
              <img src="../uploads/<?= htmlspecialchars($row['car_image'] ?? '') ?>"
                   onerror="this.src='../fallback.png'" alt="">
              <div>
                <div class="cn"><?= htmlspecialchars($row['car_name']) ?></div>
                <div class="ct"><?= htmlspecialchars($row['car_type']) ?></div>
              </div>
            </div>
          </td>

          <td style="font-size:0.82rem;white-space:nowrap">
            <?= date('d M Y', strtotime($row['start_date'])) ?><br>
            <span style="color:#aaa">→</span>
            <?= date('d M Y', strtotime($row['end_date'])) ?><br>
            <span style="color:#888;font-size:0.75rem"><?= $days ?> day<?= $days>1?'s':'' ?></span>
          </td>

          <td>
            <div style="font-weight:700;color:#111">Rs.<?= number_format($row['total_amount'] ?? 0, 0) ?></div>
            <div style="font-size:0.75rem;color:#10b981">Dep: Rs.<?= number_format($row['deposit_amount'] ?? 0, 0) ?></div>
          </td>

          <td>
            <span class="badge badge-<?= $paySlug ?>">
              <?= htmlspecialchars($row['payment_status'] ?? 'Unpaid') ?>
            </span>
          </td>

          <td>
            <?php if (!empty($row['licence_front'])): ?>
              <a class="lic-link"
                 href="../uploads/documents/<?= htmlspecialchars($row['licence_front']) ?>"
                 target="_blank">
                📄 View Licence
              </a>
            <?php else: ?>
              <span style="font-size:0.78rem;color:#ccc">Not uploaded</span>
            <?php endif; ?>
          </td>

          <td>
            <span class="badge badge-<?= $statusSlug ?>" id="status-<?= $row['id'] ?>">
              <?= htmlspecialchars($row['status']) ?>
            </span>
          </td>

          <td>
            <div class="action-wrap" id="actions-<?= $row['id'] ?>">
              <?php if (in_array($row['status'], ['Pending','Awaiting Payment'])): ?>
                <button class="btn-approve" onclick="updateBooking(<?= $row['id'] ?>,'approve')">✓ Approve</button>
                <button class="btn-cancel-bk" onclick="updateBooking(<?= $row['id'] ?>,'cancel')">✕ Cancel</button>
              <?php else: ?>
                <span style="font-size:0.78rem;color:#ccc">—</span>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endwhile; ?>
        <?php if($count === 0): ?>
        <tr class="empty-row"><td colspan="9">No bookings found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div id="toast"></div>

<script>
// ── Live search + filter ─────────────────────────────────
document.getElementById('searchInput').addEventListener('input', applyFilters);

function applyFilters() {
  const search = document.getElementById('searchInput').value.toLowerCase();
  const status = document.getElementById('statusFilter').value;
  const pay    = document.getElementById('payFilter').value;

  document.querySelectorAll('#tableBody tr[data-status]').forEach(row => {
    const matchSearch = !search || row.dataset.search.includes(search);
    const matchStatus = !status || row.dataset.status === status;
    const matchPay    = !pay    || row.dataset.pay === pay;
    row.style.display = (matchSearch && matchStatus && matchPay) ? '' : 'none';
  });
}

// ── Approve / Cancel ─────────────────────────────────────
async function updateBooking(id, action) {
  const fd = new FormData();
  fd.append('id', id);
  fd.append('action', action);

  const res  = await fetch('admin_updatebooking.php', { method:'POST', body:fd });
  const data = await res.json();

  if (data.success) {
    const newStatus = action === 'approve' ? 'Confirmed' : 'Cancelled';
    const badge = document.getElementById('status-' + id);
    if (badge) {
      badge.textContent = newStatus;
      badge.className = 'badge badge-' + newStatus.toLowerCase();
    }
    document.getElementById('actions-' + id).innerHTML =
      '<span style="font-size:0.78rem;color:#ccc">Done</span>';

    // update data attr for filter
    const row = document.getElementById('row-' + id);
    if (row) row.dataset.status = newStatus;

    showToast(action === 'approve' ? '✅ Booking approved!' : '❌ Booking cancelled.',
              action === 'approve' ? 'success' : 'error');
  } else {
    showToast('Error: ' + data.message, 'error');
  }
}

function showToast(msg, type='success') {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = 'show ' + type;
  setTimeout(() => t.className = '', 3000);
}
</script>
</body>
</html>