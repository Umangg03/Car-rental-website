<?php
session_start();

if(!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true){
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Bookings - Car Rental</title>
  <link rel="stylesheet" href="main.css">
  <link rel="stylesheet" href="Components.css">
  <link rel="stylesheet" href="my-bookings.css">
  <link rel="stylesheet" href="animations.css">
  <style>
    body { padding-top: 72px; background: #f5f7ff; }

    .bookings-section { padding: 40px 5%; }
    .bookings-section h1 { font-size: 2rem; color: #111; margin-bottom: 6px; }
    .bookings-section > p  { color: #777; margin-bottom: 30px; }

    #bookings-list {
      display: flex; flex-wrap: wrap;
      gap: 24px; justify-content: flex-start;
    }

    /* ── Booking Card ── */
    .booking-card {
      background: #fff;
      border: 1px solid #e8ecf8;
      border-radius: 16px;
      width: 320px;
      overflow: hidden;
      box-shadow: 0 4px 16px rgba(0,0,0,0.06);
      transition: transform 0.2s;
    }
    .booking-card:hover { transform: translateY(-4px); }

    .booking-card img {
      width: 100%; height: 180px;
      object-fit: cover;
    }

    .card-body { padding: 18px; }

    .card-body h3 { font-size: 1.1rem; color: #111; margin-bottom: 6px; }

    .dates-row {
      font-size: 0.85rem; color: #555;
      background: #f0f4ff; border-radius: 8px;
      padding: 8px 12px; margin: 10px 0;
      display: flex; align-items: center; gap: 6px;
    }

    /* Status badge */
    .status-badge {
      display: inline-block; padding: 3px 12px;
      border-radius: 20px; font-size: 0.76rem; font-weight: 700;
      margin-bottom: 10px;
    }
    .status-Confirmed        { background:#d1fae5; color:#065f46; }
    .status-Pending          { background:#fef3c7; color:#92400e; }
    .status-Awaiting-Payment { background:#fee2e2; color:#991b1b; }
    .status-Cancelled        { background:#f3f4f6; color:#6b7280; }
    .status-Completed        { background:#e0e7ff; color:#3730a3; }

    /* Payment info */
    .pay-info {
      font-size: 0.83rem; color: #555;
      border-top: 1px dashed #eee; padding-top: 10px; margin-top: 8px;
    }
    .pay-info .row { display:flex; justify-content:space-between; margin-bottom:4px; }
    .pay-info .row span:last-child { font-weight: 600; color: #222; }
    .pay-info .deposit-paid { color: #065f46; font-weight: 700; }
    .pay-info .deposit-pending { color: #991b1b; }

    /* Buttons */
    .button-group {
      display: flex; flex-wrap: wrap;
      gap: 8px; margin-top: 14px;
    }
    .btn-sm {
      flex: 1; padding: 9px 8px;
      border: none; border-radius: 8px;
      font-size: 0.82rem; font-weight: 600;
      cursor: pointer; text-align: center;
      text-decoration: none; display: inline-block;
    }
    .btn-update  { background: #0046FF; color: #fff; }
    .btn-receipt { background: #f0f4ff; color: #0046FF; border: 1.5px solid #0046FF; }
    .btn-delete  { background: #fff; color: #e05050; border: 1.5px solid #e05050; }
    .btn-sm:hover { opacity: 0.85; }

    /* Update Modal */
    .detail-modal {
      display: none; position: fixed; inset: 0;
      background: rgba(0,0,0,0.5);
      justify-content: center; align-items: center; z-index: 5000;
    }
    .detail-modal-content {
      background: #fff; padding: 30px;
      width: 420px; max-width: 95vw;
      border-radius: 14px; position: relative;
    }
    .detail-close {
      cursor: pointer; position: absolute;
      right: 18px; top: 12px; font-size: 24px; color: #888;
    }
    .detail-modal-content label {
      display: block; font-size: 0.82rem;
      font-weight: 600; color: #555; margin: 12px 0 4px;
    }
    .detail-modal-content input,
    .detail-modal-content textarea {
      width: 100%; padding: 10px 12px;
      border: 1.5px solid #dde1f0; border-radius: 8px;
      font-size: 0.93rem; color: #333; outline: none;
    }
    .detail-modal-content input:focus,
    .detail-modal-content textarea:focus { border-color: #0046FF; }
    .btn-update-submit {
      width: 100%; margin-top: 18px; padding: 12px;
      background: #0046FF; color: #fff; border: none;
      border-radius: 8px; font-size: 1rem;
      font-weight: 600; cursor: pointer;
    }

    /* Empty state */
    .empty-state {
      text-align: center; padding: 60px 20px; width: 100%;
    }
    .empty-state .icon { font-size: 3rem; margin-bottom: 16px; }
    .empty-state h3 { color: #333; margin-bottom: 8px; }
    .empty-state p  { color: #888; font-size: 0.9rem; }
    .empty-state a  {
      display: inline-block; margin-top: 16px;
      padding: 12px 28px; background: #0046FF;
      color: #fff; border-radius: 8px;
      text-decoration: none; font-weight: 600;
    }
  </style>
</head>
<body>

<?php include "navbar.php"; ?>

<section class="bookings-section">
  <h1>My Bookings</h1>
  <p>Here are your current and past bookings.</p>
  <div id="bookings-list"><p style="color:#888">Loading...</p></div>
</section>

<!-- Update Modal -->
<div id="updateModal" class="detail-modal">
  <div class="detail-modal-content">
    <span class="detail-close" onclick="closeUpdatePopup()">&times;</span>
    <h2 style="margin-bottom:4px">Update Booking</h2>
    <form id="updateForm">
      <input type="hidden" id="updateBookingId">
      <label>Start Date</label>
      <input type="date" id="updateStartDate" required>
      <label>End Date</label>
      <input type="date" id="updateEndDate" required>
      <label>Full Name</label>
      <input type="text" id="updateName" required>
      <label>Email</label>
      <input type="email" id="updateEmail" required>
      <label>Phone</label>
      <input type="tel" id="updatePhone" required>
      <label>Address</label>
      <textarea id="updateAddress" rows="3" required></textarea>
      <button type="submit" class="btn-update-submit">Update Booking</button>
    </form>
  </div>
</div>

<script>
async function loadBookings(){
  const res      = await fetch("backend/get_bookings.php");
  const bookings = await res.json();
  const container = document.getElementById("bookings-list");
  container.innerHTML = "";

  if(bookings.length === 0){
    container.innerHTML = `
      <div class="empty-state">
        <div class="icon">🚗</div>
        <h3>No bookings yet</h3>
        <p>You haven't made any bookings. Start by browsing our cars!</p>
        <a href="cars.php">Browse Cars</a>
      </div>`;
    return;
  }

  bookings.forEach(b => {
    // Status badge class (handle spaces)
    const statusClass = 'status-' + b.status.replace(/\s+/g,'-');

    // Payment row
    const totalAmt   = b.total_amount   ? '₹' + Number(b.total_amount).toLocaleString('en-IN')   : '—';
    const depositAmt = b.deposit_amount ? '₹' + Number(b.deposit_amount).toLocaleString('en-IN') : '—';
    const isPaid     = b.payment_status === 'Deposit Paid';
    const payClass   = isPaid ? 'deposit-paid' : 'deposit-pending';
    const payLabel   = b.payment_status || 'Unpaid';

    // Receipt button — only if confirmed & has txn id
    const receiptBtn = (b.status === 'Confirmed' && b.mock_transaction_id)
      ? `<a class="btn-sm btn-receipt"
           href="receipt.php?booking_id=${b.id}&txn=${b.mock_transaction_id}">
           📄 Receipt
         </a>`
      : '';

    // Pay now button — if still awaiting payment
    const payNowBtn = (b.status === 'Awaiting Payment')
      ? `<a class="btn-sm btn-update"
           href="payment.php?booking_id=${b.id}&deposit=${b.deposit_amount}&total=${b.total_amount}&car=${encodeURIComponent(b.car_name)}&days=${daysBetween(b.start_date,b.end_date)}">
           💳 Pay Now
         </a>`
      : '';

    container.innerHTML += `
      <div class="booking-card">
        <img src="uploads/${b.car_image ? b.car_image.replace('../uploads/','') : 'fallback.png'}"
             alt="${b.car_name}" onerror="this.src='fallback.png'">
        <div class="card-body">
          <h3>${b.car_name}</h3>
          <span class="status-badge ${statusClass}">${b.status}</span>

          <div class="dates-row">
            📅 ${formatDate(b.start_date)} → ${formatDate(b.end_date)}
          </div>

          <div class="pay-info">
            <div class="row"><span>Total Amount</span><span>${totalAmt}</span></div>
            <div class="row"><span>Deposit (20%)</span><span>${depositAmt}</span></div>
            <div class="row">
              <span>Payment</span>
              <span class="${payClass}">${payLabel}</span>
            </div>
          </div>

          <div class="button-group">
            ${payNowBtn}
            ${receiptBtn}
            <button class="btn-sm btn-update"
              onclick="openUpdatePopup(${b.id},'${b.start_date}','${b.end_date}',
              '${escJs(b.customer_name)}','${escJs(b.customer_email)}',
              '${escJs(b.customer_phone)}','${escJs(b.customer_address)}')">
              ✏️ Update
            </button>
            <button class="btn-sm btn-delete" onclick="deleteBooking(${b.id})">
              ✕ Cancel
            </button>
          </div>
        </div>
      </div>`;
  });
}

// Helpers
function escJs(str){ return str ? str.replace(/'/g,"\\'") : ''; }

function formatDate(d){
  if(!d) return '—';
  const dt = new Date(d);
  return dt.toLocaleDateString('en-IN',{day:'2-digit',month:'short',year:'numeric'});
}

function daysBetween(start, end){
  return Math.ceil((new Date(end) - new Date(start)) / 86400000);
}

async function deleteBooking(id){
  if(!confirm("Cancel this booking?")) return;
  await fetch("backend/delete_booking.php",{
    method:"POST",
    headers:{"Content-Type":"application/json"},
    body: JSON.stringify({id})
  });
  loadBookings();
}

function openUpdatePopup(id,start,end,name,email,phone,address){
  document.getElementById("updateBookingId").value  = id;
  document.getElementById("updateStartDate").value  = start;
  document.getElementById("updateEndDate").value    = end;
  document.getElementById("updateName").value       = name;
  document.getElementById("updateEmail").value      = email;
  document.getElementById("updatePhone").value      = phone;
  document.getElementById("updateAddress").value    = address;
  document.getElementById("updateModal").style.display = "flex";
}

function closeUpdatePopup(){
  document.getElementById("updateModal").style.display = "none";
}

document.getElementById("updateForm").addEventListener("submit", async function(e){
  e.preventDefault();
  await fetch("backend/update_booking.php",{
    method:"POST",
    headers:{"Content-Type":"application/json"},
    body: JSON.stringify({
      id      : document.getElementById("updateBookingId").value,
      start   : document.getElementById("updateStartDate").value,
      end     : document.getElementById("updateEndDate").value,
      name    : document.getElementById("updateName").value,
      email   : document.getElementById("updateEmail").value,
      phone   : document.getElementById("updatePhone").value,
      address : document.getElementById("updateAddress").value
    })
  });
  alert("Updated successfully!");
  closeUpdatePopup();
  loadBookings();
});

window.onload = loadBookings;
</script>

<?php include "footer.php"; ?>
</body>
</html>