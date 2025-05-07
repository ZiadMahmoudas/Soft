import { loadTheme, initThemeListeners } from "../../componentJS/theme-mode.js";
import { nav } from "../../componentJS/toolsFornavbar.js";
nav();

const params = new URLSearchParams(window.location.search);
const title = params.get("title");
const image = params.get("image");

document.getElementById("title").innerText = title;
const imagePath = "../../componentImages/img/" + image;
document.getElementById("image").src = imagePath;

logout.addEventListener("click", function () {
  localStorage.removeItem("lang");
  localStorage.removeItem("theme");
  window.location.href = 'http://localhost/project/auth/signup.html';
});

// Fetch dynamic price
async function fetchPrice() {
  const source = document.getElementById("source").value;
  const destination = document.getElementById("destination").value;
  const ticketClass = document.getElementById("class").value;
  const ticketType = document.getElementById("ticket_type").value;

  if (source && destination && ticketClass && ticketType) {
    const formData = new FormData();
    formData.append("source", source);
    formData.append("destination", destination);
    formData.append("class", ticketClass);
    formData.append("ticket_type", ticketType);

    try {
      const response = await fetch("calculatePrice.php", { method: "POST", body: formData });
      const result = await response.json();
      document.getElementById("price").innerText = `Price: $${result.price}`;
      document.getElementById("priceInput").value = result.price; // Store price for submission
    } catch (error) {
      console.error("Error fetching price:", error);
    }
  }
}

// Add event listeners to update price dynamically
document.getElementById("source").addEventListener("change", fetchPrice);
document.getElementById("destination").addEventListener("change", fetchPrice);
document.getElementById("class").addEventListener("change", fetchPrice);
document.getElementById("ticket_type").addEventListener("change", fetchPrice);

// Submit ticket booking
document.getElementById("ticketForm").addEventListener("submit", async function (e) {
  e.preventDefault();

  const source = document.getElementById("source").value;
  const destination = document.getElementById("destination").value;
  const ticketClass = document.getElementById("class").value;
  const ticketType = document.getElementById("ticket_type").value;
  const price = document.getElementById("priceInput").value;

  const formData = new FormData();
  formData.append("source", source);
  formData.append("destination", destination);
  formData.append("class", ticketClass);
  formData.append("ticket_type", ticketType);
  formData.append("price", price);

  try {
    const response = await fetch("bookTicket.php", { method: "POST", body: formData });
    const result = await response.json();

    if (result.status === "success") {
      Swal.fire({
        title: "Ticket Booked Successfully!",
        html: `
          <p><strong>Source:</strong> ${result.ticket.source}</p>
          <p><strong>Destination:</strong> ${result.ticket.destination}</p>
          <p><strong>Class:</strong> ${result.ticket.class}</p>
          <p><strong>Ticket Type:</strong> ${result.ticket.ticket_type}</p>
          <p><strong>Price:</strong> $${result.ticket.price}</p>
          <p><strong>Purchase Time:</strong> ${result.ticket.purchase_time}</p>
        `,
        icon: "success",
        showCancelButton: true,
        confirmButtonText: "Go to Profile",
        cancelButtonText: "Download Ticket"
      }).then((action) => {
        if (action.isConfirmed) {
          window.location.href = "http://localhost/project/DetailsUSER/profile.php";
        }
      });
    } else {
      Swal.fire({
        title: "Error",
        text: result.message,
        icon: "error"
      });
    }
  } catch (error) {
    console.error("Error booking ticket:", error);
  }
});