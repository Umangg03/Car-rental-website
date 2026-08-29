<?php
// payment.php  — goes in ROOT folder
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$booking_id     = intval($_GET['booking_id']     ?? 0);
$deposit_amount = floatval($_GET['deposit']      ?? 0);
$total_amount   = floatval($_GET['total']        ?? 0);
$car_name       = htmlspecialchars($_GET['car']  ?? 'Car');
$days           = intval($_GET['days']           ?? 1);

if (!$booking_id || !$deposit_amount) {
    header("Location: cars.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment - CarRental</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'Segoe UI', sans-serif;
    background: #f0f4ff;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }

  /* ── Main card ── */
  .pay-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 40px rgba(0,0,0,0.12);
    width: 100%;
    max-width: 420px;
    overflow: hidden;
  }

  /* ── Header ── */
  .pay-header {
    background: linear-gradient(135deg, #0046FF, #003ACC);
    color: #fff;
    padding: 24px 28px;
  }
  .pay-header .brand {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 18px;
  }
  .pay-header .brand span {
    font-size: 1.1rem;
    font-weight: 700;
    letter-spacing: 0.5px;
  }
  .pay-header .brand .lock-icon {
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    width: 32px; height: 32px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px;
  }
  .booking-summary p { font-size: 0.85rem; opacity: 0.85; margin-bottom: 4px; }
  .deposit-amt {
    font-size: 2.2rem;
    font-weight: 700;
    margin-top: 6px;
  }
  .deposit-amt small { font-size: 0.9rem; font-weight: 400; opacity: 0.8; }

  /* ── Tabs ── */
  .pay-tabs {
    display: flex;
    border-bottom: 2px solid #f0f0f0;
    padding: 0 28px;
  }
  .pay-tab {
    padding: 14px 16px;
    font-size: 0.88rem;
    font-weight: 600;
    color: #888;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    transition: all 0.2s;
  }
  .pay-tab.active { color: #0046FF; border-bottom-color: #0046FF; }

  /* ── Body ── */
  .pay-body { padding: 24px 28px 28px; }

  /* Card tab */
  .tab-panel { display: none; }
  .tab-panel.active { display: block; }

  .form-group { margin-bottom: 16px; }
  .form-group label {
    display: block; font-size: 0.8rem;
    font-weight: 600; color: #555; margin-bottom: 6px;
  }
  .form-group input {
    width: 100%; padding: 12px 14px;
    border: 1.5px solid #dde1f0;
    border-radius: 8px; font-size: 0.95rem;
    color: #333; transition: border 0.2s;
    outline: none;
  }
  .form-group input:focus { border-color: #0046FF; }

  .form-row { display: flex; gap: 14px; }
  .form-row .form-group { flex: 1; }

  /* Card number styling */
  .card-icons { position: relative; }
  .card-icons input { padding-right: 80px; letter-spacing: 2px; }
  .card-brand-icons {
    position: absolute; right: 12px; top: 50%;
    transform: translateY(-50%);
    display: flex; gap: 6px; align-items: center;
  }
  .card-brand-icons img { height: 22px; border-radius: 3px; }

  /* UPI tab */
  .upi-input-wrap {
    display: flex; gap: 10px; align-items: flex-end;
  }
  .upi-input-wrap .form-group { flex: 1; margin: 0; }
  .upi-verify-btn {
    padding: 12px 18px; background: #f0f4ff;
    color: #0046FF; border: 1.5px solid #0046FF;
    border-radius: 8px; font-weight: 600; cursor: pointer;
    white-space: nowrap; font-size: 0.88rem;
  }
  .upi-apps {
    display: flex; gap: 14px; margin-top: 20px;
    justify-content: center;
  }
  .upi-app {
    display: flex; flex-direction: column;
    align-items: center; gap: 6px;
    padding: 12px 16px; border: 1.5px solid #eee;
    border-radius: 10px; cursor: pointer;
    font-size: 0.75rem; color: #555; font-weight: 600;
    transition: all 0.2s;
  }
  .upi-app:hover { border-color: #0046FF; background: #f0f4ff; color: #0046FF; }
  .upi-app span { font-size: 1.6rem; }

  /* Pay button */
  .pay-btn {
    width: 100%; padding: 15px;
    background: linear-gradient(135deg, #0046FF, #003ACC);
    color: #fff; border: none; border-radius: 10px;
    font-size: 1rem; font-weight: 700;
    cursor: pointer; margin-top: 20px;
    transition: opacity 0.2s; position: relative;
    overflow: hidden;
  }
  .pay-btn:hover { opacity: 0.92; }
  .pay-btn:disabled { opacity: 0.7; cursor: not-allowed; }

  .secure-note {
    text-align: center; font-size: 0.75rem;
    color: #aaa; margin-top: 12px;
  }
  .secure-note span { color: #4CAF50; }

  /* ── Loading overlay ── */
  .loading-overlay {
    display: none; position: fixed;
    inset: 0; background: rgba(255,255,255,0.95);
    z-index: 999; flex-direction: column;
    align-items: center; justify-content: center; gap: 20px;
  }
  .loading-overlay.show { display: flex; }
  .spinner {
    width: 52px; height: 52px;
    border: 4px solid #e0e8ff;
    border-top-color: #0046FF;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
  }
  @keyframes spin { to { transform: rotate(360deg); } }
  .loading-overlay p { font-size: 1rem; color: #333; font-weight: 600; }

  /* ── Success overlay ── */
  .success-overlay {
    display: none; position: fixed;
    inset: 0; background: rgba(255,255,255,0.97);
    z-index: 1000; flex-direction: column;
    align-items: center; justify-content: center; gap: 16px;
    text-align: center; padding: 30px;
  }
  .success-overlay.show { display: flex; }
  .success-circle {
    width: 90px; height: 90px;
    background: #d1fae5; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 2.8rem;
    animation: popIn 0.4s cubic-bezier(0.175,0.885,0.32,1.275);
  }
  @keyframes popIn { from{transform:scale(0)} to{transform:scale(1)} }
  .success-overlay h2 { font-size: 1.6rem; color: #065f46; }
  .success-overlay p  { font-size: 0.95rem; color: #555; max-width: 300px; }
  .txn-id { background: #f0f4ff; padding: 8px 20px; border-radius: 8px; font-size: 0.82rem; color: #0046FF; font-weight: 600; font-family: monospace; }
  .success-btn {
    padding: 13px 32px;
    background: #0046FF; color: #fff;
    border: none; border-radius: 10px;
    font-size: 0.95rem; font-weight: 700;
    cursor: pointer; text-decoration: none;
    display: inline-block; margin-top: 6px;
  }
  .success-btn.outline {
    background: #fff; color: #0046FF;
    border: 2px solid #0046FF; margin-right: 8px;
  }
</style>
</head>
<body>

<!-- ── Payment Card ── -->
<div class="pay-card" id="payCard">

  <!-- Header -->
  <div class="pay-header">
    <div class="brand">
      <div class="lock-icon">🔒</div>
      <span>CarRental · Secure Payment</span>
    </div>
    <div class="booking-summary">
      <p><?= $car_name ?> · <?= $days ?> day<?= $days > 1 ? 's' : '' ?></p>
      <p>Total Rental: ₹<?= number_format($total_amount, 0) ?></p>
    </div>
    <div class="deposit-amt">
      ₹<?= number_format($deposit_amount, 0) ?>
      <small>/ deposit (20%)</small>
    </div>
  </div>

  <!-- Tabs -->
  <div class="pay-tabs">
    <div class="pay-tab active" onclick="switchTab('card')">💳 Card</div>
    <div class="pay-tab" onclick="switchTab('upi')">📱 UPI</div>
  </div>

  <!-- Body -->
  <div class="pay-body">

    <!-- Card Panel -->
    <div class="tab-panel active" id="panel-card">
      <div class="form-group card-icons">
        <label>Card Number</label>
        <input type="text" id="cardNumber" placeholder="4111 1111 1111 1111"
               maxlength="19" oninput="formatCardNumber(this)">
        <div class="card-brand-icons">
          <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Visa.svg" alt="Visa">
          <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="MC">
        </div>
      </div>
      <div class="form-group">
        <label>Cardholder Name</label>
        <input type="text" id="cardName" placeholder="Name on card">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Expiry Date</label>
          <input type="text" id="cardExpiry" placeholder="MM / YY"
                 maxlength="7" oninput="formatExpiry(this)">
        </div>
        <div class="form-group">
          <label>CVV</label>
          <input type="password" id="cardCvv" placeholder="•••" maxlength="3">
        </div>
      </div>
      <button class="pay-btn" id="payBtn" onclick="processPayment()">
        Pay ₹<?= number_format($deposit_amount, 0) ?> Now
      </button>
    </div>

    <!-- UPI Panel -->
    <div class="tab-panel" id="panel-upi">
      <div class="form-group">
        <label>UPI ID</label>
        <div class="upi-input-wrap">
          <div class="form-group">
            <input type="text" id="upiId" placeholder="yourname@upi">
          </div>
          <button class="upi-verify-btn" onclick="processPayment()">Verify & Pay</button>
        </div>
      </div>
      <div class="upi-apps">
        <div class="upi-app" onclick="processPayment()"><span>🟣</span>PhonePe</div>
        <div class="upi-app" onclick="processPayment()"><span>🔵</span>GPay</div>
        <div class="upi-app" onclick="processPayment()"><span>🟡</span>Paytm</div>
      </div>
      <button class="pay-btn" style="margin-top:24px" onclick="processPayment()">
        Pay ₹<?= number_format($deposit_amount, 0) ?> Now
      </button>
    </div>

    <p class="secure-note"><span>🔒</span> 256-bit SSL encrypted · Safe & Secure</p>
  </div>
</div>

<!-- Loading -->
<div class="loading-overlay" id="loadingOverlay">
  <div class="spinner"></div>
  <p>Processing your payment...</p>
  <small style="color:#aaa;font-size:0.8rem">Please do not close this window</small>
</div>

<!-- Success -->
<div class="success-overlay" id="successOverlay">
  <div class="success-circle">✅</div>
  <h2>Payment Successful!</h2>
  <p>Your deposit has been paid and booking is confirmed.</p>
  <div class="txn-id" id="txnIdDisplay">TXN...</div>
  <div style="margin-top:10px; display:flex; flex-wrap:wrap; gap:10px; justify-content:center;">
    <a id="receiptBtn" href="#" class="success-btn outline">📄 Download Receipt</a>
    <a href="my-bookings.php" class="success-btn">My Bookings →</a>
  </div>
</div>

<script>
const bookingId     = <?= $booking_id ?>;
const depositAmount = <?= $deposit_amount ?>;
const totalAmount   = <?= $total_amount ?>;
const carName       = "<?= addslashes($car_name) ?>";
const days          = <?= $days ?>;

// ── Tab switching ──────────────────────────────────────────
function switchTab(tab) {
  document.querySelectorAll('.pay-tab').forEach((t,i) => {
    t.classList.toggle('active', (i===0 && tab==='card') || (i===1 && tab==='upi'));
  });
  document.getElementById('panel-card').classList.toggle('active', tab==='card');
  document.getElementById('panel-upi').classList.toggle('active', tab==='upi');
}

// ── Card number formatter ──────────────────────────────────
function formatCardNumber(input) {
  let v = input.value.replace(/\D/g,'').substring(0,16);
  input.value = v.match(/.{1,4}/g)?.join(' ') || v;
}

// ── Expiry formatter ───────────────────────────────────────
function formatExpiry(input) {
  let v = input.value.replace(/\D/g,'');
  if (v.length >= 2) v = v.substring(0,2) + ' / ' + v.substring(2,4);
  input.value = v;
}

// ── Process payment ────────────────────────────────────────
async function processPayment() {
  // Show loading
  document.getElementById('loadingOverlay').classList.add('show');

  // Simulate 2.5 second payment processing
  await new Promise(r => setTimeout(r, 2500));

  try {
    const formData = new FormData();
    formData.append('booking_id', bookingId);

    const res  = await fetch('backend/confirm_booking.php', { method: 'POST', body: formData });
    const data = await res.json();

    document.getElementById('loadingOverlay').classList.remove('show');

    if (data.success) {
      // Show success screen
      document.getElementById('txnIdDisplay').innerText = data.transaction_id;
      document.getElementById('receiptBtn').href =
        `receipt.php?booking_id=${bookingId}&txn=${data.transaction_id}`;
      document.getElementById('successOverlay').classList.add('show');
    } else {
      alert('Payment failed: ' + data.message);
    }
  } catch(err) {
    document.getElementById('loadingOverlay').classList.remove('show');
    alert('Server error. Please try again.');
  }
}
</script>
</body>
</html>