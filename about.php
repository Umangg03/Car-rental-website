<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>About Us - CarRental</title>
<link rel="stylesheet" href="main.css">
  <link rel="stylesheet" href="components.css">
  <link rel="stylesheet" href="animations.css">
  <link rel="stylesheet" href="my-bookings.css">
  <link rel="stylesheet" href="cars.css">
  <link rel="stylesheet" href="about.css">
</head>
<body>
<?php include "navbar.php"; ?>


  <!-- About Section -->
  <section class="about-header">
    <h1>About CarRental</h1>
    <p>Premium car rental service built for comfort, performance, and trust.</p>
  </section>

  <section class="about-content">
    <div class="about-container">
      <div class="about-text">
        <h2>Who We Are</h2>
        <p>
          CarRental is a trusted platform offering a wide range of vehicles — from economy hatchbacks to premium luxury SUVs.
          Our mission is to make renting cars as easy as booking a cab. With transparent pricing, insured vehicles, and verified drivers, 
          we ensure a safe and reliable experience every time.
        </p>
        <h2>Our Mission</h2>
        <p>
          We aim to revolutionize car rental services in India by focusing on technology, trust, and transparency. 
          Whether you’re traveling for business, leisure, or adventure — we have the perfect car for you.
        </p>
      </div>

      <div class="about-image">
        <img src="bancar.png" alt="Luxury Car">
      </div>
    </div>
  </section>

  <section class="about-features">
    <h2>Why Choose Us?</h2>
    <div class="features-grid">
      <div class="feature-box">
        <h3>🚘 Wide Selection</h3>
        <p>Choose from economy, SUV, and luxury cars at competitive prices.</p>
      </div>
      <div class="feature-box">
        <h3>🔒 Secure Booking</h3>
        <p>Safe, transparent, and fast booking with online confirmation.</p>
      </div>
      <div class="feature-box">
        <h3>⭐ Customer Support</h3>
        <p>24/7 customer support to help you before and after booking.</p>
      </div>
    </div>
  </section>

<?php include "footer.php"; ?>


<script src="nav.js"></script>
<?php include "auth_modal.php"; ?>
<script src="utils.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("loginForm");
    if (!loginForm) return; // modal not loaded = do nothing

    loginForm.addEventListener("submit", async function(e) {
        e.preventDefault(); // VERY IMPORTANT

        let formData = new FormData(loginForm);
        let res = await fetch("backend/login.php", { method: "POST", body: formData });

        let data;
        try { data = await res.json(); } catch(err) {
            alert("Login failed — server error.");
            return;
        }

        document.getElementById("loginMessage").innerText = data.message;

        if (data.success) {
            closeLoginPopup();
            setTimeout(() => location.reload(), 300);
        }
    });
});
</script>

</body>
</html>
