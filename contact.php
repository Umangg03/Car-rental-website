<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Us - CarRental</title>
  <link rel="stylesheet" href="main.css">
  <link rel="stylesheet" href="components.css">
  <link rel="stylesheet" href="contact.css">
    <link rel="stylesheet" href="animations.css">
  <link rel="stylesheet" href="my-bookings.css">
  <link rel="stylesheet" href="cars.css">
</head>
<body>
<?php include "navbar.php"; ?>

  <section class="contact-header">
    <h1>Contact Us</h1>
    <p>We’d love to hear from you! Reach out with your queries, feedback, or support needs.</p>
  </section>

  <section class="contact-container">
    <div class="contact-info">
      <h2>Get In Touch</h2>
      <p><strong>📍 Address:</strong> 123 MG Road, Surat, India</p>
      <p><strong>📞 Phone:</strong> +91 98765 43210</p>
      <p><strong>📧 Email:</strong> support@carrental.com</p>
      <p>Our support team is available 24/7 to assist you with bookings and inquiries.</p>
    </div>

    <div class="contact-form">
  <h2>Send us a message</h2>

  <form id="contactForm">
    <input type="text" name="name" placeholder="Your Name" required>
    <input type="email" name="email" placeholder="Your Email" required>
    <textarea name="message" placeholder="Your Message..." required></textarea>
    
    <button type="submit" class="btn-primary">Send Message</button>
  </form>

  <p id="responseMsg" style="margin-top:10px;font-size:14px;"></p>
</div>

  </section>

<?php include "footer.php"; ?>

<script src="nav.js"></script>
<?php include "auth_modal.php"; ?>
<script src="utils.js"></script>
<script>
document.getElementById("contactForm").addEventListener("submit", async function(e){
    e.preventDefault();

    let formData = new FormData(this);
    let responseText = document.getElementById("responseMsg");

    let res = await fetch("backend/save_message.php", {
        method: "POST",
        body: formData
    });

    let result = await res.json();

    if(result.success){
        responseText.style.color = "green";
        responseText.innerText = result.message;
        this.reset(); 
    } else {
        responseText.style.color = "red";
        responseText.innerText = result.message;
    }
});
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
