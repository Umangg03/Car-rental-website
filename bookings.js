// bookings.js
document.addEventListener("DOMContentLoaded", () => {
  const bookingsList = document.getElementById("bookings-list");
  const bookings = JSON.parse(localStorage.getItem("bookings")) || [];

  if (bookings.length === 0) {
    bookingsList.innerHTML = "<p>No bookings yet.</p>";
  } else {
    bookingsList.innerHTML = "";
    bookings.forEach((booking, index) => {
      const div = document.createElement("div");
      div.classList.add("booking-card");
      div.innerHTML = `
        <h3>${booking.car}</h3>
        <p><strong>Pickup:</strong> ${booking.pickup}</p>
        <p><strong>Return:</strong> ${booking.return}</p>
        <button onclick="cancelBooking(${index})">Cancel</button>
      `;
      bookingsList.appendChild(div);
    });
  }
});

function cancelBooking(index) {
  let bookings = JSON.parse(localStorage.getItem("bookings")) || [];
  bookings.splice(index, 1);
  localStorage.setItem("bookings", JSON.stringify(bookings));
  location.reload();
}
