<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
/* ── Navbar ── */
.header {
  position: fixed; top: 0; left: 0; width: 100%;
  z-index: 1000;
  background: rgba(255,255,255,0.92);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border-bottom: 1px solid rgba(0,0,0,0.07);
  box-shadow: 0 2px 20px rgba(0,0,0,0.06);
  transition: transform 0.3s ease;
}
.header.hidden { transform: translateY(-100%); }

.nav-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 5%;
  height: 68px;
  gap: 20px;
}

/* Logo */
.logo__text {
  display: flex; align-items: center;
  text-decoration: none; flex-shrink: 0;
}
.logo__text img {
  height: 38px; width: auto;
  transition: opacity 0.2s;
}
.logo__text:hover img { opacity: 0.85; }

/* Nav right group */
.nav-right {
  display: flex; align-items: center;
  gap: 6px; flex: 1; justify-content: flex-end;
}

/* Nav list */
.nav__list {
  display: flex; gap: 2px;
  list-style: none; margin: 0; padding: 0;
}
.nav__link {
  position: relative;
  display: inline-block;
  padding: 8px 14px;
  text-decoration: none;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.88rem;
  font-weight: 500;
  color: #555;
  border-radius: 8px;
  transition: color 0.2s, background 0.2s;
  white-space: nowrap;
}
.nav__link:hover {
  color: #0046FF;
  background: #f0f4ff;
}
.nav__link.active {
  color: #0046FF;
  font-weight: 600;
  background: #f0f4ff;
}
/* Active underline dot */
.nav__link.active::after {
  content: '';
  position: absolute;
  bottom: 2px; left: 50%;
  transform: translateX(-50%);
  width: 4px; height: 4px;
  background: #0046FF;
  border-radius: 50%;
}



/* Auth button */
#authBtn {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 9px 20px;
  background: #0046FF; color: #fff;
  border: none; border-radius: 100px;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.85rem; font-weight: 600;
  cursor: pointer; text-decoration: none;
  white-space: nowrap; flex-shrink: 0;
  transition: background 0.2s, transform 0.15s;
}
#authBtn:hover { background: #003ACC; transform: translateY(-1px); }

/* Mobile toggle */
.nav__toggle {
  display: none;
  border: none; background: transparent;
  cursor: pointer; padding: 6px;
  border-radius: 8px;
  transition: background 0.2s;
}
.nav__toggle:hover { background: #f0f4ff; }
.nav__toggle span {
  display: block; width: 22px; height: 2px;
  background: #333; border-radius: 2px;
  margin: 5px 0;
  transition: transform 0.3s, opacity 0.3s;
}

/* Backdrop */
#nav-backdrop {
  display: none; position: fixed; inset: 0;
  background: rgba(0,0,0,0.3);
  z-index: 999; backdrop-filter: blur(2px);
}

/* ── Mobile ── */
@media(max-width: 860px) {
  .nav__toggle { display: block; }

  #site-nav {
    position: fixed;
    top: 68px; right: 0;
    width: 240px; height: calc(100vh - 68px);
    background: #fff;
    border-left: 1px solid #eef0f8;
    box-shadow: -4px 0 30px rgba(0,0,0,0.1);
    transform: translateX(100%);
    transition: transform 0.3s ease;
    z-index: 1000; padding: 16px;
    overflow-y: auto;
  }
  #site-nav.is-open { transform: translateX(0); }

  .nav__list { flex-direction: column; gap: 4px; }
  .nav__link { display: block; padding: 11px 16px; }

}
</style>

<header class="header" id="mainHeader">
  <div class="nav-container">

    <!-- Logo -->
    <a href="index.php" class="logo__text">
      <img src="carlogo1.png" alt="CarRental Logo">
    </a>

    <!-- Nav right -->
    <div class="nav-right">

      <!-- Desktop nav -->
      <nav id="site-nav">
        <ul class="nav__list">
          <li><a href="index.php"  class="nav__link <?= $currentPage==='index.php' ?'active':'' ?>">Home</a></li>
          <li><a href="cars.php"   class="nav__link <?= $currentPage==='cars.php'  ?'active':'' ?>">Cars</a></li>
          <li><a href="about.php"  class="nav__link <?= $currentPage==='about.php' ?'active':'' ?>">About</a></li>
          <li><a href="contact.php"class="nav__link <?= $currentPage==='contact.php'?'active':'' ?>">Contact</a></li>
          <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
            <li><a href="backend/admin_dashboard.php" class="nav__link">Dashboard</a></li>
          <?php elseif(isset($_SESSION['role']) && $_SESSION['role']==='user'): ?>
            <li><a href="my-bookings.php" class="nav__link <?= $currentPage==='my-bookings.php'?'active':'' ?>">My Bookings</a></li>
          <?php endif; ?>
        </ul>
      </nav>

      <!-- Auth -->
      <?php if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']): ?>
        <a id="authBtn" onclick="logout()">👤 <?= htmlspecialchars($_SESSION['user_name']) ?></a>
      <?php else: ?>
        <a id="authBtn" onclick="openLoginPopup()">Login</a>
      <?php endif; ?>

      <!-- Hamburger -->
      <button class="nav__toggle" id="nav-toggle" aria-label="Toggle menu">
        <span></span><span></span><span></span>
      </button>
    </div>

  </div>
  <div id="nav-backdrop"></div>
</header>