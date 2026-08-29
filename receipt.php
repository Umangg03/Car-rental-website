<?php
// receipt.php — goes in ROOT folder
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

include 'backend/db.php';

$booking_id = intval($_GET['booking_id'] ?? 0);
$txn_id     = htmlspecialchars($_GET['txn'] ?? '');

if (!$booking_id) {
    header("Location: my-bookings.php");
    exit;
}

// Fetch booking + car details
$stmt = $conn->prepare("
    SELECT b.*, c.name AS car_name, c.type AS car_type,
           c.transmission, c.fuel, c.seats, c.location
    FROM bookings b
    JOIN cars c ON b.car_id = c.id
    WHERE b.id = ? AND b.user_id = ?
");
$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
$stmt->execute();
$b = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$b) {
    header("Location: my-bookings.php");
    exit;
}

$days = (int) ceil((strtotime($b['end_date']) - strtotime($b['start_date'])) / 86400);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Receipt #<?= str_pad($booking_id, 6, '0', STR_PAD_LEFT) ?> - CarRental</title>
<style>

/* ── Screen styles ─────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: 'Segoe UI', Arial, sans-serif;
  background: #f0f4ff;
  padding: 30px 16px;
  min-height: 100vh;
}

.actions-bar {
  display: flex; gap: 12px;
  justify-content: flex-end;
  margin-bottom: 20px;
  max-width: 680px;
  margin-left: auto; margin-right: auto;
}

.btn-print {
  padding: 11px 26px;
  background: #0046FF; color: #fff;
  border: none; border-radius: 8px;
  font-size: 0.95rem; font-weight: 700;
  cursor: pointer; display: flex;
  align-items: center; gap: 8px;
}
.btn-print:hover { background: #003ACC; }

.btn-back {
  padding: 11px 26px;
  background: #fff; color: #0046FF;
  border: 2px solid #0046FF;
  border-radius: 8px; font-size: 0.95rem;
  font-weight: 700; cursor: pointer;
  text-decoration: none;
  display: flex; align-items: center; gap: 8px;
}

/* ── Receipt card ──────────────────────────────────── */
.receipt {
  max-width: 680px;
  margin: 0 auto;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 30px rgba(0,0,0,0.10);
  overflow: hidden;
}

/* Header */
.r-header {
  background: linear-gradient(135deg, #0046FF, #0035CC);
  color: #fff;
  padding: 32px 36px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  flex-wrap: wrap;
  gap: 16px;
}
.r-header-left h1 { font-size: 1.5rem; margin-bottom: 4px; }
.r-header-left p  { font-size: 0.85rem; opacity: 0.82; }
.confirmed-badge {
  background: #d1fae5; color: #065f46;
  padding: 6px 18px; border-radius: 20px;
  font-size: 0.82rem; font-weight: 800;
  white-space: nowrap;
}

/* TXN strip */
.txn-strip {
  background: #f0f4ff;
  padding: 14px 36px;
  display: flex; align-items: center; gap: 16px;
  border-bottom: 1px solid #e8ecf8;
}
.txn-strip .lbl { font-size: 0.75rem; color: #888; margin-bottom: 3px; }
.txn-strip .val { font-size: 0.95rem; font-weight: 700; color: #0046FF; font-family: monospace; }

/* Body */
.r-body { padding: 32px 36px; }

/* Section */
.r-section { margin-bottom: 28px; }
.r-section-title {
  font-size: 0.72rem; font-weight: 800;
  text-transform: uppercase; letter-spacing: 1.2px;
  color: #bbb; margin-bottom: 14px;
  padding-bottom: 6px;
  border-bottom: 1.5px solid #f0f0f0;
}

/* Info grid */
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.info-item .lbl { font-size: 0.76rem; color: #999; margin-bottom: 3px; }
.info-item .val { font-size: 0.97rem; font-weight: 600; color: #1a1a1a; }

/* Price table */
.price-table { width: 100%; border-collapse: collapse; }
.price-table td { padding: 9px 0; font-size: 0.93rem; color: #555; }
.price-table td:last-child { text-align: right; font-weight: 600; color: #1a1a1a; }
.price-table .sep td { border-top: 1.5px dashed #e8e8e8; padding-top: 12px; }
.price-table .total-row td { font-size: 1.05rem; font-weight: 700; color: #0046FF; background: #f0f4ff; padding: 10px 8px; }
.price-table .deposit-row td { color: #065f46; font-weight: 700; background: #f0fdf4; padding: 9px 8px; }
.price-table .balance-row td { color: #dc2626; }
.price-table .total-row td:first-child,
.price-table .deposit-row td:first-child { border-radius: 4px 0 0 4px; padding-left: 10px; }
.price-table .total-row td:last-child,
.price-table .deposit-row td:last-child  { border-radius: 0 4px 4px 0; padding-right: 10px; }

/* Footer */
.r-footer {
  background: #f8f9fc;
  border-top: 1px solid #eef0f8;
  padding: 16px 36px;
  text-align: center;
  font-size: 0.8rem;
  color: #aaa;
}
.r-footer strong { color: #0046FF; }

/* ── Print styles ──────────────────────────────────── */
@media print {
  body {
    background: white !important;
    padding: 0 !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  .actions-bar { display: none !important; }

  .receipt {
    box-shadow: none !important;
    border-radius: 0 !important;
    max-width: 100% !important;
  }

  .r-header {
    background: #0046FF !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  .price-table .total-row td  { background: #e8f0ff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  .price-table .deposit-row td { background: #e8fdf0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

  .txn-strip { background: #f0f4ff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

  @page {
    margin: 10mm;
    size: A4;
  }
}
</style>
</head>
<body>

<!-- Action Buttons (hidden on print) -->
<div class="actions-bar">
  <button class="btn-print" onclick="printReceipt()">
    ⬇️ Download / Print PDF
  </button>
  <a href="my-bookings.php" class="btn-back">
    ← My Bookings
  </a>
</div>

<!-- Receipt -->
<div class="receipt" id="receipt">

  <!-- Header -->
  <div class="r-header">
    <div class="r-header-left">
      <h1>🚗 Booking Confirmed!</h1>
      <p>Receipt #<?= str_pad($booking_id, 6, '0', STR_PAD_LEFT) ?> &nbsp;&middot;&nbsp; <?= date('d M Y, h:i A') ?></p>
    </div>
    <span class="confirmed-badge">✓ CONFIRMED</span>
  </div>

  <!-- TXN strip -->
  <div class="txn-strip">
    <div style="font-size:1.4rem">💳</div>
    <div>
      <div class="lbl">Transaction ID</div>
      <div class="val"><?= $txn_id ?: ($b['mock_transaction_id'] ?? 'N/A') ?></div>
    </div>
  </div>

  <!-- Body -->
  <div class="r-body">

    <!-- Car Details -->
    <div class="r-section">
      <div class="r-section-title">Car Details</div>
      <div class="info-grid">
        <div class="info-item">
          <div class="lbl">Car Name</div>
          <div class="val"><?= htmlspecialchars($b['car_name']) ?></div>
        </div>
        <div class="info-item">
          <div class="lbl">Type</div>
          <div class="val"><?= htmlspecialchars($b['car_type']) ?></div>
        </div>
        <div class="info-item">
          <div class="lbl">Fuel</div>
          <div class="val"><?= htmlspecialchars($b['fuel']) ?></div>
        </div>
        <div class="info-item">
          <div class="lbl">Transmission</div>
          <div class="val"><?= htmlspecialchars($b['transmission']) ?></div>
        </div>
      </div>
    </div>

    <!-- Booking Details -->
    <div class="r-section">
      <div class="r-section-title">Booking Details</div>
      <div class="info-grid">
        <div class="info-item">
          <div class="lbl">Pickup Date</div>
          <div class="val"><?= date('d M Y', strtotime($b['start_date'])) ?></div>
        </div>
        <div class="info-item">
          <div class="lbl">Return Date</div>
          <div class="val"><?= date('d M Y', strtotime($b['end_date'])) ?></div>
        </div>
        <div class="info-item">
          <div class="lbl">Duration</div>
          <div class="val"><?= $days ?> Day<?= $days > 1 ? 's' : '' ?></div>
        </div>
        <div class="info-item">
          <div class="lbl">Pickup Location</div>
          <div class="val"><?= htmlspecialchars($b['location']) ?></div>
        </div>
      </div>
    </div>

    <!-- Customer Details -->
    <div class="r-section">
      <div class="r-section-title">Customer Details</div>
      <div class="info-grid">
        <div class="info-item">
          <div class="lbl">Full Name</div>
          <div class="val"><?= htmlspecialchars($b['customer_name']) ?></div>
        </div>
        <div class="info-item">
          <div class="lbl">Phone</div>
          <div class="val"><?= htmlspecialchars($b['customer_phone']) ?></div>
        </div>
        <div class="info-item" style="grid-column:1/-1">
          <div class="lbl">Email</div>
          <div class="val"><?= htmlspecialchars($b['customer_email']) ?></div>
        </div>
        <div class="info-item" style="grid-column:1/-1">
          <div class="lbl">Delivery Address</div>
          <div class="val"><?= htmlspecialchars($b['customer_address']) ?></div>
        </div>
      </div>
    </div>

    <!-- Payment Summary -->
    <div class="r-section">
      <div class="r-section-title">Payment Summary</div>
      <table class="price-table">
        <tr>
          <td>Daily Rate</td>
          <td>Rs. <?= number_format($b['total_amount'] / $days, 0) ?> / day</td>
        </tr>
        <tr>
          <td>Duration</td>
          <td><?= $days ?> day<?= $days > 1 ? 's' : '' ?></td>
        </tr>
        <tr class="sep"><td colspan="2" style="padding:0;border:none"></td></tr>
        <tr class="total-row">
          <td>Total Rental Amount</td>
          <td>Rs. <?= number_format($b['total_amount'], 0) ?></td>
        </tr>
        <tr class="deposit-row">
          <td>&#10003; Deposit Paid (20%)</td>
          <td>Rs. <?= number_format($b['deposit_amount'], 0) ?></td>
        </tr>
        <tr class="balance-row">
          <td>Balance Due at Pickup</td>
          <td>Rs. <?= number_format($b['total_amount'] - $b['deposit_amount'], 0) ?></td>
        </tr>
      </table>
    </div>

  </div><!-- /r-body -->

  <div class="r-footer">
    Thank you for choosing <strong>CarRental</strong> &nbsp;&middot;&nbsp;
    support@carrental.com &nbsp;&middot;&nbsp; +91 98765 43210
  </div>

</div><!-- /receipt -->

<script>
function printReceipt() {
  window.print();
}
</script>

</body>
</html>