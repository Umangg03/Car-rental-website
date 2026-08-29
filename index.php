<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="data:,">
  <title>Car Rental - Home</title>
  <link rel="stylesheet" href="main.css">
  <link rel="stylesheet" href="components.css">
  <link rel="stylesheet" href="animations.css">
  <link rel="stylesheet" href="my-bookings.css">
  <link rel="stylesheet" href="cars.css">
</head>
<body>
<?php include "navbar.php"; ?>


<!-- Hero Section -->
<section class="top reveal">
  <!-- Luxury Car Rental Header -->
  <header class="luxury-header">
    <h1 >Luxury Cars On Rent</h1>
  </header>

  <!-- Main Car Section -->
  <div class="car-image-container reveal">
    <div class="car-image">
      <img src="mcar.png" alt="Main Car" class="main-car-img">
    </div>
  </div>  

  <!-- Featured Cars -->
  <a href="cars.php">
  <section class="featured reveal">
    <h2>Featured Cars</h2>
    <div class="car-grid">
      <!-- Repeat for each car card -->
      <div class="car-card f">
        <img src="./uploads/car1.png" alt="Hyundai Creta">
        <h2>BMW M3</h2>
        <p>🚙 Sedan | 🗓️ 2024 | 👥 4 Seats</p>
        <p>⛽ Petrol | ⚙️ Automatic</p>
        <p class="price">₹1,500/day</p>
        <span class="badge">Available Now</span>
      </div>
      <div class="car-card f">
    <img src="./uploads/car3.png" alt="Kia Seltos">
    <h2>Audi A1</h2>
    <p>🚙 Hatchback | 🗓️ 2023 | 👥 4 Seats</p>
    <p>⛽ Petrol | ⚙️ Manual</p>
    <p class="price">₹1,400/day</p>
    <span class="badge">Available Now</span>
  </div>

  <div class="car-card f">
    <img src="./uploads/car2.png" alt="Baleno">
    <h2>Audi A3</h2>
    <p>🚗 Sedan | 🗓️ 2022 | 👥 4 Seats</p>
    <p>⛽ Petrol | ⚙️ Automatic</p>
    <p class="price">₹1900/day</p>
    <span class="badge">Available Now</span>
  </div>

  <div class="car-card f">
    <img src="./uploads/car8.png" alt="XUV700">
    <h2>Mercedes-benz G63 AMG</h2>
    <p>🚙 SUV | 🗓️ 2024 | 👥 5 Seats</p>
    <p>⛽ Diesel | ⚙️ Automatic</p>
    <p class="price">₹3,000/day</p>
    <span class="badge">Available Now</span>
  </div>

  <div class="car-card f">
    <img src="./uploads/car11.png" alt="Honda City">
    <h2>Mercedes-benz E220 </h2>
    <p>🚗 Sedan | 🗓️ 2023 | 👥 4 Seats</p>
    <p>⛽ Petrol | ⚙️ Automatic</p>
    <p class="price">₹2,200/day</p>
    <span class="badge">Available Now</span>
  </div>

  <div class="car-card f">
    <img src="./uploads/car6.png" alt="Nexon EV">
    <h2>BMW M4 Competition</h2>
    <p>⛽🚗 Sedan | 🗓️ 2024 | 👥 4 Seats</p>
    <p>Petrol | ⚙️ Automatic</p>
    <p class="price">₹2,600/day</p>
    <span class="badge">Available Now</span>
  </div>
</div>

    </div>

  </section>
</section>
  </a>
<!-- Banner -->
<div class="banner-container reveal">
  <div class="banner-content">
    <div class="text-content">
      <h1>Do You Own a Luxury Car?</h1>
      <p>Monetize your vehicle effortlessly by listing it on CarRental.</p>
      <p>We take care of insurance, driver verification and secure payments — so you can earn passive income, stress-free.</p>
      <a href="#" class="list-btn">List your car</a>
    </div>
    <div class="image-content">
      <img src="bancar.png" alt="White luxury sports car">
    </div>
  </div>
</div>
 <!-- Why Choose Us -->
  <section class="why-choose reveal">
    <h2>Why Choose CarRental?</h2>
    <div class="why-grid">
      <div class="why-item">
        <h3>🚗 Wide Selection</h3>
        <p>Choose from economy cars to luxury SUVs at affordable prices.</p>
      </div>
      <div class="why-item">
        <h3>💳 Easy Booking</h3>
        <p>Seamless booking process with instant confirmation.</p>
      </div>
      <div class="why-item">
        <h3>🛡️ Safe & Reliable</h3>
        <p>All cars are fully insured and maintained regularly.</p>
      </div>
      <div class="why-item">
        <h3>⭐ Trusted Service</h3>
        <p>Rated highly by thousands of happy customers.</p>
      </div>
    </div>
  </section>

  <!-- Customer Feedback -->
  <section class="testimonials reveal">
    <h2>What Our Customers Say</h2>
    <div class="testimonial-grid">
      <div class="testimonial">
        <p>"The best rental experience I’ve had! The car was clean and the service was excellent."</p>
        <h4>- Rahul S.</h4>
      </div>
      <div class="testimonial">
        <p>"Affordable rates and a wide variety of options. Highly recommend!"</p>
        <h4>- Priya K.</h4>
      </div>
      <div class="testimonial">
        <p>"Booking was super easy, and the car was ready on time. Will rent again."</p>
        <h4>- Aman P.</h4>
      </div>
    </div>
  </section>

  <!-- Subscription Section -->
<section class="subscription-section reveal ">
  <div class="subscription-box">
    <h2>Never Miss a Deal!</h2  >
    <p>Subscribe to get the latest offers, new arrivals, and exclusive discounts.</p>
    <form id="subscription-form">
      <input type="email" placeholder="Enter your email id" required>
      <button type="submit">Subscribe</button>
    </form>
  </div>
</section>


  <!-- Our Partners -->
  <section class="partners reveal">
    <h2>Our Trusted Partners</h2>
    <div class="partners-logos">
      <img src="logo1.png" alt="Partner 1">
      <img src="logo2.png" alt="Partner 2">
      <img src="logo3.png" alt="Partner 3">
      <img src="logo4.png" alt="Partner 4">
    </div>
  </section>

<!-- Login Popup -->
<div id="loginModal" class="login-modal">
  <div class="login-modal-content">
    <span class="login-close" onclick="closeLoginPopup()">&times;</span>
    <h2>Login</h2>
    <form id="loginForm" method="POST">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" class="btn-primary">Login</button>
    </form>
    <p id="loginMessage" style="color:red; margin-top:10px;"></p>
    <p>Don’t have an account? 
      <a href="#" onclick="switchToSignup()">Sign up</a>
    </p>
  </div>
</div>

<!-- Signup Popup -->
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
    <p id="signupMessage" style="color:red; margin-top:20px;"></p>
    <p>Already have an account? 
      <a href="#" onclick="switchToLogin()">Login</a>
    </p>
  </div>
</div>

<script>
function openLoginPopup() {
  document.getElementById("loginModal").style.display = "flex";
}
function closeLoginPopup() {
  document.getElementById("loginModal").style.display = "none";
}
function openSignupPopup() {
  document.getElementById("signupModal").style.display = "flex";
}
function closeSignupPopup() {
  document.getElementById("signupModal").style.display = "none";
}
function switchToSignup() {
  closeLoginPopup();
  openSignupPopup();
}
function switchToLogin() {
  closeSignupPopup();
  openLoginPopup();
}
window.onclick = function(e) {
  if (e.target.classList.contains("login-modal")) {
    e.target.style.display = "none";
  }
};

// ✅ Handle Login
document.getElementById("loginForm").addEventListener("submit", async function(e) {
  e.preventDefault();
  let formData = new FormData(this);

  let res = await fetch("backend/login.php", { method: "POST", body: formData });
  let data = await res.json();

  document.getElementById("loginMessage").innerText = data.message;

  if (data.success) {
    closeLoginPopup();
    setTimeout(() => {
      location.reload();
    }, 500);
  }
});

// ✅ Handle Signup
document.getElementById("signupForm").addEventListener("submit", async function(e) {
  e.preventDefault();
  let formData = new FormData(this);
  let res = await fetch("backend/signup.php", { method: "POST", body: formData });
  let data = await res.text();

  if (data.includes("successful")) {
    document.getElementById("signupMessage").style.color = "green";
    document.getElementById("signupMessage").innerText = "Signup successful! Please login.";
    setTimeout(() => {
      switchToLogin();
    }, 1500);
  } else {
    document.getElementById("signupMessage").innerText = data;
  }
});

// ✅ Keep User Logged In (Session Check)
window.onload = async function() {
  let res = await fetch("backend/check_session.php");
  let data = await res.json();
  console.log("Session:", data);
};


</script>


<?php include "footer.php"; ?>

<script src="nav.js"></script>
<script src="utils.js"></script>

</body>
</html>
