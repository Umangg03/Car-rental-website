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
    <p id="loginMessage" style="color:red;margin-top:10px;"></p>
    <p>Don’t have an account? <a href="#" onclick="switchToSignup()">Sign up</a></p>
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
    <p id="signupMessage" style="color:red;margin-top:10px;"></p>
    <p>Already have an account? <a href="#" onclick="switchToLogin()">Login</a></p>
  </div>
</div>
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
