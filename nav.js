// nav.js (SAFE VERSION)
const nav = document.getElementById("site-nav");
const navToggle = document.getElementById("nav-toggle");
const backdrop = document.getElementById("nav-backdrop");

// Only run if elements exist
if (nav && navToggle && backdrop) {

  navToggle.addEventListener("click", () => {
    nav.classList.toggle("is-open");
    backdrop.style.display = nav.classList.contains("is-open") ? "block" : "none";
    document.body.classList.toggle("nav-open", nav.classList.contains("is-open"));
  });

  backdrop.addEventListener("click", () => {
    nav.classList.remove("is-open");
    backdrop.style.display = "none";
    document.body.classList.remove("nav-open");
  });
}

// Search function stays the same
function searchCars() {
  const query = document.getElementById("searchInput")?.value?.toLowerCase();
  if (query) {
    alert("Searching for: " + query);
  }
}

const searchInput = document.getElementById("searchInput");

if (searchInput) {
  
  searchInput.addEventListener("input", () => {
    const query = searchInput.value.toLowerCase().trim();
    filterCars(query);
  });

  // Auto search if URL contains `?search=`
  const params = new URLSearchParams(window.location.search);
  if (params.get('search')) {
    searchInput.value = params.get('search');
    filterCars(params.get('search').toLowerCase());
  }
}

