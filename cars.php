<?php
$searchQuery = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

include('backend/db.php');


$today = date('Y-m-d');

$cars = $conn->query("SELECT * FROM cars ORDER BY id DESC");


$availability_map = [];
$avail_result = $conn->query("
    SELECT car_id, MAX(end_date) AS booked_until
    FROM bookings
    WHERE status IN ('Pending','Confirmed')
      AND end_date >= '$today'
    GROUP BY car_id
");
while ($row = $avail_result->fetch_assoc()) {
    $availability_map[$row['car_id']] = $row['booked_until'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cars - CarRental</title>
<link rel="stylesheet" href="main.css">
<link rel="stylesheet" href="components.css">
<link rel="stylesheet" href="cars.css">
<link rel="stylesheet" href="animations.css">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body { font-family: 'DM Sans', sans-serif; }

/* ── Page Hero ── */
.cars-hero {
  background: transparent;
  padding: 60px 8% 50px;
  text-align: center;
  margin-top: 68px;
  position: relative;
  overflow: hidden;
}
.cars-hero::before {
  content: '';
  position: absolute;
  width: 500px; height: 500px;
  border-radius: 50%;
  display:none;
  top: -200px; right: -100px;
  pointer-events: none;
}
.cars-hero h1 {
  font-family: 'Syne', sans-serif;
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 800; color: #111;
  margin-bottom: 6px;
}
.cars-hero p {
  color: #888;
  font-size: 0.95rem; margin-bottom: 28px;
}

/* ── Search bar ── */
.cars-search-wrap {
  display: flex;
  justify-content: center;
  position: relative; z-index: 1;
}
.cars-search-inner {
  display: flex; align-items: center;
  background: #fff;
  border-radius: 100px;
  width: 100%; max-width: 520px;
  box-shadow: 0 8px 40px rgba(0,0,0,0.25);
  overflow: hidden;
}
.search-icon {
  padding: 0 16px; font-size: 1rem;
  color: #888; flex-shrink: 0;
}
.cars-search-inner input {
  flex: 1; border: none; outline: none;
  padding: 15px 8px;
  font-size: 0.93rem;
  font-family: 'DM Sans', sans-serif;
  color: #003ACC; background: transparent;
}
.cars-search-inner input::placeholder { color: #bbb; }
.cars-search-inner button {
  border: none; background: transparent;
  padding: 0 18px; font-size: 1rem;
  color: #ccc; cursor: pointer;
  border-left: 1px solid #f0f0f0;
  height: 50px;
  transition: color 0.2s, background 0.2s;
}
.cars-search-inner button:hover { color: #333; background: #f0f4ff; }

/* ── Car listings section ── */
.car-listings {
  padding: 40px 5% 60px;
  background: #f4f6fb;
}

/* ── Car card ── */
.car-card {
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 2px 16px rgba(0,0,0,0.07);
  text-align: center;
  transition: transform 0.25s, box-shadow 0.25s;
  padding-bottom: 20px;
}
.car-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 40px rgba(0,0,0,0.13);
}
.car-card img {
  width: 100%; height: 180px; object-fit: cover;
}
.car-card h3 {
  margin: 14px 12px 4px;
  font-family: 'Syne', sans-serif;
  font-size: 1.05rem; font-weight: 700; color: #111;
}
.car-card > p {
  color: #888; font-size: 0.85rem; margin-bottom: 10px;
}

/* ── Availability badge ── */
.avail-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.74rem; font-weight: 700;
  margin: 10px 0 4px;
}
.avail-badge.available { background: #d1fae5; color: #065f46; }
.avail-badge.booked    { background: #fee2e2; color: #991b1b; font-size: 0.68rem; }

/* ── View Details button ── */
.btn-view {
  display: inline-block;
  margin-top: 10px;
  padding: 10px 24px;
  background: #0046FF; color: #fff;
  border: none; border-radius: 100px;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.85rem; font-weight: 600;
  cursor: pointer;
  transition: background 0.2s, transform 0.15s;
}
.btn-view:hover { background: #003ACC; transform: translateY(-1px); }

/* ── Car grid ── */
.car-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 22px;
  padding: 10px 0 0;
  justify-content: center;
  align-items: flex-start;
}
.car-card { width: 280px; flex-shrink: 0; }

/* ── Detail Modal ── */
.detail-modal {
  display: none; position: fixed; top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.6);
  justify-content: center; align-items: flex-start;
  z-index: 2000; overflow-y: auto; padding: 30px 20px;
}
.detail-modal-content {
  background: #fff; padding: 40px; border-radius: 16px;
  max-width: 950px; width: 100%;
  box-shadow: 0 10px 40px rgba(0,0,0,0.2);
  position: relative; display: flex; flex-direction: column; gap: 30px;
  animation: fadeIn 0.3s ease-out; margin: auto;
}
@keyframes fadeIn { from{opacity:0;transform:scale(0.95)} to{opacity:1;transform:scale(1)} }
.detail-close { position:absolute; top:15px; right:25px; font-size:36px; color:#888; cursor:pointer; }
.detail-close:hover { color:#333; }
.detail-top { display:flex; flex-wrap:wrap; gap:40px; }
.detail-left { flex:2; min-width:280px; }
.detail-left img { width:100%; max-height:350px; object-fit:cover; border-radius:10px; margin-bottom:15px; }
#carName { font-size:2rem; font-weight:700; margin-bottom:4px; font-family:'Syne',sans-serif; }
#carType { font-size:0.95rem; color:#6c757d; margin-bottom:15px; }
.detail-features { display:flex; flex-wrap:wrap; gap:10px; margin-bottom:20px; }
.feature { background:#f8f9fa; padding:8px 16px; border-radius:8px; font-size:0.88rem; color:#495057; display:flex; align-items:center; gap:6px; }
.detail-right { flex:1; min-width:260px; background:#f8f9fa; padding:25px; border-radius:12px; }
.price-summary { background:#e8f0fe; border-radius:8px; padding:15px; margin:12px 0; font-size:0.9rem; }
.price-summary .row { display:flex; justify-content:space-between; margin-bottom:6px; }
.price-summary .total-row { font-weight:700; font-size:1rem; border-top:1px solid #c0cfe8; padding-top:8px; margin-top:8px; }
.price-summary .deposit-row { color:#0046FF; font-weight:600; }
.avail-status-box { padding:10px 14px; border-radius:8px; font-size:0.88rem; font-weight:600; margin-bottom:12px; display:none; }
.avail-status-box.ok  { background:#d1fae5; color:#065f46; }
.avail-status-box.err { background:#fee2e2; color:#991b1b; }
.detail-right label { font-size:0.88rem; font-weight:600; color:#343a40; margin-top:14px; display:block; }
.detail-right input, .detail-right textarea { width:100%; padding:10px; margin-top:4px; border:1px solid #dee2e6; border-radius:6px; font-size:0.95rem; box-sizing:border-box; }
.detail-right input:focus, .detail-right textarea:focus { outline:none; border-color:#0046FF; }
.detail-btn-book { background:#0046FF; color:#fff; padding:14px; border:none; border-radius:8px; cursor:pointer; margin-top:20px; width:100%; font-size:1rem; font-weight:600; transition:0.3s; }
.detail-btn-book:hover { background:#003ACC; }
.detail-btn-book:disabled { background:#9ab3ff; cursor:not-allowed; }
.note { font-size:0.78rem; color:#6c757d; margin-top:10px; text-align:center; }
.doc-hint { font-size:0.78rem; color:#888; margin-top:3px; }
@media(max-width:768px){ .detail-modal-content{padding:20px;} .detail-top{flex-direction:column;} }
</style>
</head>
<body>
<?php include "navbar.php"; ?>


<!-- ── Hero + Search ── -->
<div class="cars-hero">
  <h1>Available Cars</h1>
  <p>Browse our full fleet and book your perfect ride</p>
  <div class="cars-search-wrap">
    <div class="cars-search-inner">
      <div class="search-icon">🔍</div>
      <input type="text" id="searchInput"
             placeholder="Search by car name, type, fuel..."
             oninput="filterCars(this.value.toLowerCase())">
      <button onclick="document.getElementById('searchInput').value=''; filterCars('');">✕</button>
    </div>
  </div>
</div>

<!-- ── Car Listings ── -->
<section class="car-listings reveal">
  <div class="car-grid">
    <?php if($cars->num_rows > 0): ?>
      <?php while($car = $cars->fetch_assoc()):
        $cid          = $car['id'];
        $booked_until = $availability_map[$cid] ?? null;
        $is_booked    = ($booked_until !== null);
      ?>
        <div class="car-card">
          <img src="uploads/<?= htmlspecialchars($car['image']) ?>"
               alt="<?= htmlspecialchars($car['name']) ?>"
               onerror="this.src='fallback.png'">

          <!-- Availability badge -->
          <?php if ($is_booked): ?>
            <span class="avail-badge booked">
              🔴 Booked · Available <?= date('d M Y', strtotime($booked_until)) ?>
            </span>
          <?php else: ?>
            <span class="avail-badge available">🟢 Available Now</span>
          <?php endif; ?>

          <h3><?= htmlspecialchars($car['name']) ?></h3>
          <p><?= htmlspecialchars($car['type']) ?> - ₹<?= htmlspecialchars($car['price']) ?>/day</p>

          <button class="btn-view"
            onclick="openDetailPopup(
              '<?= addslashes($car['name']) ?>',
              '<?= addslashes($car['image']) ?>',
              '<?= addslashes($car['type']) ?>',
              '<?= addslashes($car['transmission']) ?>',
              '<?= addslashes($car['year']) ?>',
              <?= $car['price'] ?>,
              '<?= addslashes($car['description']) ?>',
              '<?= addslashes($car['seats']) ?>',
              '<?= addslashes($car['fuel']) ?>',
              '<?= addslashes($car['location']) ?>',
              <?= $cid ?>
            )">View Details
          </button>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No cars available at the moment.</p>
    <?php endif; ?>
  </div>
</section>

<!-- ── Car Detail & Booking Modal ─────────────────────────── -->
<div id="detailModal" class="detail-modal">
  <div class="detail-modal-content">
    <span class="detail-close" onclick="closeDetailPopup()">&times;</span>

    <div class="detail-top">
      <!-- Left: Car info -->
      <div class="detail-left">
        <img id="carImage" src="" alt="Car Image">
        <h2 id="carName">Car Name</h2>
        <p id="carType">Type • Year</p>
        <div class="detail-features">
          <div class="feature">🚗 <span id="carSeats">4 Seats</span></div>
          <div class="feature">⚡ <span id="carFuel">Fuel</span></div>
          <div class="feature">⚙️ <span id="carTrans">Transmission</span></div>
          <div class="feature">📍 <span id="carLocation">Location</span></div>
        </div>
        <h3>Description</h3>
        <p id="carDesc">Description here.</p>
      </div>

      <!-- Right: Booking form -->
      <div class="detail-right">

        <!-- Availability status feedback -->
        <div id="availStatus" class="avail-status-box"></div>

        <label>Pickup Date</label>
        <input type="date" id="pickupDate" onchange="onDatesChange()">

        <label>Return Date</label>
        <input type="date" id="returnDate" onchange="onDatesChange()">

        <!-- Price Summary (hidden until dates picked) -->
        <div id="priceSummary" class="price-summary" style="display:none;">
          <div class="row"><span>Daily Rate</span><span id="summaryRate">₹0</span></div>
          <div class="row"><span>Days</span><span id="summaryDays">0</span></div>
          <div class="row total-row"><span>Total Amount</span><span id="summaryTotal">₹0</span></div>
          <div class="row deposit-row"><span>💰 Deposit (20%)</span><span id="summaryDeposit">₹0</span></div>
        </div>

        <label>Full Name</label>
        <input type="text" id="custName" placeholder="Enter your name">

        <label>Email</label>
        <input type="email" id="custEmail" placeholder="Enter your email">

        <label>Contact Number</label>
        <input type="tel" id="custPhone" placeholder="Enter your phone">

        <label>Delivery Address</label>
        <textarea id="custNotes" rows="2" placeholder="Delivery location"></textarea>

        <label>Driving Licence (Front) <span style="color:red">*</span></label>
        <input type="file" id="licenceFront" accept="image/jpeg,image/png,image/jpg,application/pdf">
        <p class="doc-hint">JPG, PNG or PDF · Max 5MB</p>

        <button class="detail-btn-book" id="bookBtn">Book Now</button>
        <p class="note">20% deposit required to confirm booking</p>
      </div>
    </div>
  </div>
</div>

<!-- Login / Signup modals (keep as-is) -->
<div id="loginModal" class="login-modal">
  <div class="login-modal-content">
    <span class="login-close" onclick="closeLoginPopup()">&times;</span>
    <h2>Login</h2>
    <form id="loginForm">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" class="btn-primary">Login</button>
    </form>
    <p id="loginMessage" style="color:red;margin-top:10px;"></p>
    <p>Don't have an account? <a href="#" onclick="switchToSignup()">Sign up</a></p>
  </div>
</div>

<div id="signupModal" class="login-modal">
  <div class="login-modal-content">
    <span class="login-close" onclick="closeSignupPopup()">&times;</span>
    <h2>Sign Up</h2>
    <form id="signupForm" method="POST">
      <input type="text" name="name" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" class="btn-primary">Sign Up</button>
    </form>
    <p id="signupMessage" style="color:red;margin-top:10px;"></p>
    <p>Already have an account? <a href="#" onclick="switchToLogin()">Login</a></p>
  </div>
</div>

<?php include "footer.php"; ?>

<script>
// ── Global state 
let selectedCarId   = null;
let selectedCarPrice = 0;
let availabilityOK  = false;

// Set today as minimum date for both pickers
const todayStr = new Date().toISOString().split('T')[0];
document.getElementById('pickupDate').min  = todayStr;
document.getElementById('returnDate').min  = todayStr;

// ── Open modal 
function openDetailPopup(name,img,type,trans,year,price,desc,seats,fuel,location,carId){
  selectedCarId    = carId;
  selectedCarPrice = parseFloat(price);
  availabilityOK   = false;

  document.getElementById("carName").innerText    = name;
  document.getElementById("carImage").src         = "uploads/" + img;
  document.getElementById("carType").innerText    = type + " • " + year;
  document.getElementById("carTrans").innerText   = trans;
  document.getElementById("carDesc").innerText    = desc;
  document.getElementById("carSeats").innerText   = seats + " Seats";
  document.getElementById("carFuel").innerText    = fuel;
  document.getElementById("carLocation").innerText = location;

  // Reset form
  document.getElementById("pickupDate").value  = '';
  document.getElementById("returnDate").value  = '';
  document.getElementById("custName").value    = '';
  document.getElementById("custEmail").value   = '';
  document.getElementById("custPhone").value   = '';
  document.getElementById("custNotes").value   = '';
  document.getElementById("licenceFront").value = '';
  document.getElementById("priceSummary").style.display = 'none';
  hideAvailStatus();

  document.getElementById("detailModal").style.display = "flex";
}

function closeDetailPopup(){
  document.getElementById("detailModal").style.display = "none";
}

// ── Date change handler 
function onDatesChange(){
  const pickup = document.getElementById("pickupDate").value;
  const ret    = document.getElementById("returnDate").value;

  // Ensure return min = day after pickup
  if (pickup) {
    const nextDay = new Date(pickup);
    nextDay.setDate(nextDay.getDate() + 1);
    document.getElementById("returnDate").min = nextDay.toISOString().split('T')[0];
  }

  if (!pickup || !ret) { hidePriceSummary(); hideAvailStatus(); return; }

  if (ret <= pickup) {
    showAvailStatus('err', '⚠️ Return date must be after pickup date.');
    hidePriceSummary();
    availabilityOK = false;
    return;
  }

  // Check availability via AJAX
  checkAvailability(pickup, ret);
}

function checkAvailability(start, end){
  showAvailStatus('ok', '⏳ Checking availability...');
  availabilityOK = false;

  fetch(`backend/check_availability.php?car_id=${selectedCarId}&start=${start}&end=${end}`)
    .then(r => r.json())
    .then(data => {
      if(data.available){
        showAvailStatus('ok', '✅ Car is available for these dates!');
        availabilityOK = true;
        updatePriceSummary(start, end);
      } else {
        let msg = '🔴 ' + data.message;
        if(data.available_from){
          msg += ' Available from ' + formatDate(data.available_from) + '.';
        }
        showAvailStatus('err', msg);
        hidePriceSummary();
        availabilityOK = false;
      }
    })
    .catch(() => {
      showAvailStatus('err', 'Could not check availability. Try again.');
    });
}

// ── Price summary 
function updatePriceSummary(start, end){
  const days    = Math.round((new Date(end) - new Date(start)) / 86400000);
  const total   = days * selectedCarPrice;
  const deposit = Math.round(total * 0.20);

  document.getElementById("summaryRate").innerText    = '₹' + selectedCarPrice.toLocaleString('en-IN');
  document.getElementById("summaryDays").innerText    = days + (days === 1 ? ' day' : ' days');
  document.getElementById("summaryTotal").innerText   = '₹' + total.toLocaleString('en-IN');
  document.getElementById("summaryDeposit").innerText = '₹' + deposit.toLocaleString('en-IN');
  document.getElementById("priceSummary").style.display = 'block';
}

function hidePriceSummary(){
  document.getElementById("priceSummary").style.display = 'none';
}

// ── Availability status helpers 
function showAvailStatus(type, msg){
  const box = document.getElementById("availStatus");
  box.className = 'avail-status-box ' + type;
  box.innerText = msg;
  box.style.display = 'block';
}
function hideAvailStatus(){
  document.getElementById("availStatus").style.display = 'none';
}

// ── Format date nicely
function formatDate(dateStr){
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-IN', {day:'2-digit', month:'short', year:'numeric'});
}

// ── Book Now
document.getElementById("bookBtn").onclick = async function(e){
  e.preventDefault();

  if(!availabilityOK){
    alert("Please select valid dates and ensure the car is available.");
    return;
  }

  const licenceFile = document.getElementById("licenceFront").files[0];
  if(!licenceFile){
    alert("Please upload your Driving Licence (front).");
    return;
  }
  if(licenceFile.size > 5 * 1024 * 1024){
    alert("Licence file must be under 5MB.");
    return;
  }

  const pickup  = document.getElementById("pickupDate").value;
  const ret     = document.getElementById("returnDate").value;
  const name    = document.getElementById("custName").value.trim();
  const email   = document.getElementById("custEmail").value.trim();
  const phone   = document.getElementById("custPhone").value.trim();
  const address = document.getElementById("custNotes").value.trim();

  if(!name || !email || !phone || !address){
    alert("Please fill all required fields.");
    return;
  }

  this.disabled = true;
  this.innerText = 'Processing...';

  const formData = new FormData();
  formData.append("car_id",           selectedCarId);
  formData.append("start_date",       pickup);
  formData.append("end_date",         ret);
  formData.append("customer_name",    name);
  formData.append("customer_email",   email);
  formData.append("customer_phone",   phone);
  formData.append("customer_address", address);
  formData.append("licence_front",    licenceFile);

  try {
    const res  = await fetch("backend/book_car.php", {method:"POST", body:formData});
    const data = await res.json();

    if(data.success){
      closeDetailPopup();
      const params = new URLSearchParams({
        booking_id : data.booking_id,
        deposit    : data.deposit_amount,
        total      : data.total_amount,
        car        : data.car_name,
        days       : data.days
      });
      window.location.href = 'payment.php?' + params.toString();
    } else {
      alert("❌ " + data.message);
      this.disabled = false;
      this.innerText = 'Book Now';
    }
  } catch(err) {
    alert("Server error. Please try again.");
    this.disabled = false;
    this.innerText = 'Book Now';
  }
};

// ── Car search filter
const searchQuery = "<?php echo addslashes($searchQuery); ?>";
window.addEventListener('DOMContentLoaded', () => {
  if(searchQuery){ filterCars(searchQuery); }
});

function filterCars(query){
  const carGrid = document.querySelector('.car-grid');
  const carCards = carGrid.querySelectorAll('.car-card');
  let anyVisible = false;
  carCards.forEach(card => {
    const name = card.querySelector('h3')?.innerText.toLowerCase() || '';
    const type = card.querySelector('p')?.innerText.toLowerCase() || '';
    const show = name.includes(query) || type.includes(query);
    card.style.display = show ? 'block' : 'none';
    if(show) anyVisible = true;
  });
  let msg = carGrid.querySelector('.no-cars-msg');
  if(!msg){
    msg = document.createElement('p');
    msg.classList.add('no-cars-msg');
    msg.style = 'text-align:center;font-size:1.2rem;color:#555;';
    msg.innerText = 'No matching cars found.';
    carGrid.appendChild(msg);
  }
  msg.style.display = anyVisible ? 'none' : 'block';
}

// ── Login form 
document.getElementById("loginForm").addEventListener("submit", async function(e){
  e.preventDefault();
  let formData = new FormData(this);
  let res  = await fetch("backend/login.php", {method:"POST", body:formData});
  let data = await res.json();
  document.getElementById("loginMessage").innerText = data.message;
  if(data.success){ closeLoginPopup(); window.location.href = "cars.php"; }
});
</script>

<script src="nav.js"></script>
<script src="utils.js"></script>
</body>
</html>