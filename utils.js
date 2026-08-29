/** SCROLL REVEAL ANIMATION **/
const reveals = document.querySelectorAll('.reveal');

window.addEventListener('scroll', () => {
  reveals.forEach(el => {
    const top = el.getBoundingClientRect().top;
    const windowHeight = window.innerHeight;

    if (top < windowHeight - 50) {
      el.classList.add('active');
    }
  });
});


/** AUTH & NAVBAR STATE HANDLING **/
window.onload = async function () {
    try {
        let res = await fetch("backend/check_session.php");
        let data = await res.json();

        const btn = document.getElementById("authBtn");
        const navList = document.querySelector(".nav__list");

        if (!btn || !navList) return;

        // Remove previous role buttons (avoid duplicate dashboard/MyBookings)
        document.querySelectorAll(".role-link").forEach(item => item.remove());

        if (data.loggedIn) {
            btn.innerText = `Logout (${data.name})`;
            btn.onclick = logout;

            // Add dynamic navigation link based on user role
            const li = document.createElement("li");
            li.classList.add("role-link");

            li.innerHTML = data.role === "admin"
                ? `<a href="backend/admin_dashboard.php" class="nav__link">Dashboard</a>`
                : `<a href="my_bookings.php" class="nav__link">My Bookings</a>`;

           navList.insertBefore(li, document.getElementById("authBtn").closest("a, button"));


        } else {
            btn.innerText = "Login";
            btn.onclick = openLoginPopup;
        }

        /** Auto bind search if available (Cars page only) **/
        const params = new URLSearchParams(window.location.search);
        const searchQuery = params.get('search')?.toLowerCase() || '';
        const searchInput = document.querySelector('.nav__search input');

        if (searchInput) {
            if (searchQuery) {
                searchInput.value = searchQuery;
                filterCars(searchQuery);
            }

            searchInput.addEventListener("input", (e) => {
                filterCars(e.target.value.toLowerCase());
            });
        }

    } catch (err) {
        console.error("utils.js error:", err);
    }
};


/** LOGOUT FUNCTION **/
async function logout() {
    await fetch("backend/logout.php");
    location.reload(); 
}


/** CAR FILTER (Only runs if there are cars) **/
function filterCars(query) {
    const carGrid = document.querySelector('.car-grid');
    if (!carGrid) return;

    const carCards = carGrid.querySelectorAll('.car-card');
    let found = false;

    carCards.forEach(card => {
        const name = card.querySelector('h3')?.innerText.toLowerCase() || '';
        const typeText = card.querySelector('p')?.innerText.toLowerCase() || '';

        if (name.includes(query) || typeText.includes(query)) {
            card.style.display = "block";
            found = true;
        } else {
            card.style.display = "none";
        }
    });

    // Display "No results found"
    let msg = carGrid.querySelector(".no-cars-msg");
    if (!msg) {
        msg = document.createElement("p");
        msg.classList.add("no-cars-msg");
        msg.style.textAlign = "center";
        msg.style.fontSize = "1.2rem";
        msg.style.color = "#777";
        msg.style.marginTop = "20px";
        msg.innerText = "No matching cars found.";
        carGrid.appendChild(msg);
    }

    msg.style.display = found ? "none" : "block";
}

window.openLoginPopup = function () {
    const modal = document.getElementById("loginModal");
    if (modal) modal.style.display = "flex";
};

window.closeLoginPopup = function () {
    const modal = document.getElementById("loginModal");
    if (modal) modal.style.display = "none";
};

window.switchToSignup = function () {
    closeLoginPopup();
    const modal = document.getElementById("signupModal");
    if (modal) modal.style.display = "flex";
};

window.switchToLogin = function () {
    const signup = document.getElementById("signupModal");
    if (signup) signup.style.display = "none";

    const login = document.getElementById("loginModal");
    if (login) login.style.display = "flex";
};
function searchCars(e) {
    e.preventDefault();
    let query = document.getElementById("searchInput").value.trim();

    if (query.length === 0) return false;

    window.location.href = `cars.php?search=${encodeURIComponent(query)}`;
    return false;
}
