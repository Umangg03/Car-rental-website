<style>
/* ── Footer ── */
.site-footer {
  background: #0a0e1a;
  color: #fff;
  padding: 60px 8% 0;
  font-family: 'DM Sans', sans-serif;
}

.footer-top {
  display: grid;
  grid-template-columns: 1.8fr 1fr 1fr 1fr;
  gap: 40px;
  padding-bottom: 48px;
  border-bottom: 1px solid rgba(255,255,255,0.08);
}

/* Brand column */
.footer-brand .brand-name {
  font-family: 'Syne', sans-serif;
  font-size: 1.4rem; font-weight: 800;
  color: #fff; margin-bottom: 12px;
  display: flex; align-items: center; gap: 8px;
}
.footer-brand p {
  color: rgba(255,255,255,0.5);
  font-size: 0.88rem; line-height: 1.7;
  max-width: 260px; margin-bottom: 20px;
}
.footer-socials { display: flex; gap: 10px; }
.social-btn {
  width: 38px; height: 38px;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  text-decoration: none; font-size: 1rem;
  transition: background 0.2s, border-color 0.2s, transform 0.2s;
}
.social-btn:hover {
  background: #0046FF;
  border-color: #0046FF;
  transform: translateY(-2px);
}

/* Link columns */
.footer-col h4 {
  font-family: 'Syne', sans-serif;
  font-size: 0.82rem; font-weight: 700;
  text-transform: uppercase; letter-spacing: 1.2px;
  color: rgba(255,255,255,0.4);
  margin-bottom: 18px;
}
.footer-col a {
  display: block;
  color: rgba(255,255,255,0.65);
  text-decoration: none;
  font-size: 0.88rem;
  margin-bottom: 10px;
  transition: color 0.2s, padding-left 0.2s;
}
.footer-col a:hover { color: #fff; padding-left: 4px; }

/* Contact info */
.contact-item {
  display: flex; align-items: flex-start; gap: 10px;
  color: rgba(255,255,255,0.65);
  font-size: 0.88rem; margin-bottom: 12px;
}
.contact-item .ci { font-size: 1rem; flex-shrink: 0; margin-top: 1px; }

/* Bottom bar */
.footer-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 0;
  gap: 16px;
  flex-wrap: wrap;
}
.footer-bottom p {
  color: rgba(255,255,255,0.3);
  font-size: 0.8rem;
}
.footer-bottom-links { display: flex; gap: 20px; }
.footer-bottom-links a {
  color: rgba(255,255,255,0.3);
  text-decoration: none; font-size: 0.8rem;
  transition: color 0.2s;
}
.footer-bottom-links a:hover { color: #fff; }

@media(max-width: 768px) {
  .footer-top {
    grid-template-columns: 1fr 1fr;
    gap: 32px;
  }
  .footer-brand { grid-column: 1 / -1; }
}
@media(max-width: 480px) {
  .footer-top { grid-template-columns: 1fr; }
}
</style>

<footer class="site-footer">
  <div class="footer-top">

    <!-- Brand -->
    <div class="footer-brand">
      <div class="brand-name">🚗 CarRental</div>
      <p>Your trusted premium car rental service in Gujarat. Drive in comfort, style, and confidence.</p>
      <div class="footer-socials">
        <a href="#" class="social-btn" title="Facebook">📘</a>
        <a href="https://www.instagram.com/umangg.03?igsh=c3FneXEwdW50eTN0" class="social-btn" title="Instagram" target="_blank">📸</a>
        <a href="#" class="social-btn" title="Twitter">🐦</a>
      </div>
    </div>

    <!-- Quick Links -->
    <div class="footer-col">
      <h4>Quick Links</h4>
      <a href="index.php">Home</a>
      <a href="cars.php">Browse Cars</a>
      <a href="about.php">About Us</a>
      <a href="contact.php">Contact</a>
      <a href="my-bookings.php">My Bookings</a>
    </div>

    <!-- Car Types -->
    <div class="footer-col">
      <h4>Car Types</h4>
      <a href="cars.php">Luxury Cars</a>
      <a href="cars.php">SUVs</a>
      <a href="cars.php">Sedans</a>
      <a href="cars.php">Hatchbacks</a>
      <a href="cars.php">Sports Cars</a>
    </div>

    <!-- Contact -->
    <div class="footer-col">
      <h4>Contact Us</h4>
      <div class="contact-item">
        <span class="ci">📍</span>
        <span>123 MG Road, Surat, Gujarat, India</span>
      </div>
      <div class="contact-item">
        <span class="ci">📞</span>
        <span>+91 98765 43210</span>
      </div>
      <div class="contact-item">
        <span class="ci">📧</span>
        <span>support@carrental.com</span>
      </div>
      <div class="contact-item">
        <span class="ci">⏰</span>
        <span>24/7 Customer Support</span>
      </div>
    </div>

  </div>

  <!-- Bottom bar -->
  <div class="footer-bottom">
    <p>© 2025 CarRental. All rights reserved. Made with ❤️ in Surat.</p>
    <div class="footer-bottom-links">
      <a href="#">Privacy Policy</a>
      <a href="#">Terms of Service</a>
      <a href="#">Refund Policy</a>
    </div>
  </div>
</footer>