import { loadTheme, initThemeListeners } from "../../componentJS/theme-mode.js";
import { nav } from "../../componentJS/toolsFornavbar.js";
nav();

const params = new URLSearchParams(window.location.search);
const title = params.get("title");
const image = params.get("image");

document.getElementById("title").innerText = title;
const imagePath = "../../componentImages/img/" + image;
document.getElementById("image").src = imagePath;
const logout = document.querySelector("#logout")
logout.addEventListener("click", function () {
  localStorage.removeItem("lang");
  localStorage.removeItem("theme");
  window.location.href = 'http://localhost/Soft/auth/signup.html';
});

// Fetch dynamic price
async function fetchPrice() {
  const source = document.getElementById("source").value;
  const destination = document.getElementById("destination").value;
  const ticketClass = document.getElementById("class").value;
  const ticketType = document.getElementById("ticket_type").value;

  if (source && destination && ticketClass && ticketType) {
    const formData = new FormData();
    formData.append("Source", source);
    formData.append("Destination", destination);
    formData.append("Class", ticketClass);
    formData.append("Ticket_type", ticketType);

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
document.getElementById("ticketForm").addEventListener("click", async function (e) {
  // Collect form data
  const source = document.getElementById("source").value;
  const destination = document.getElementById("destination").value;
  const ticketClass = document.getElementById("class").value;
  const ticketType = document.getElementById("ticket_type").value;
  const price = document.getElementById("priceInput").value;

  // Validate form data
  if (!source || !destination || !ticketClass || !ticketType || !price) {
    Swal.fire({
      title: "Error",
      text: "Please fill in all fields before submitting.",
      icon: "error",
    });
    return;
  }

  // Prepare data for submission
  const formData = new FormData();
  formData.append("Source", source);
  formData.append("Destination", destination);
  formData.append("Class", ticketClass);
  formData.append("Ticket_type", ticketType);
  formData.append("Price", price);

  try {
    // Send data to the server
    const response = await fetch("bookTicket.php", { method: "POST", body: formData });
    const result = await response.json();

    // Handle server response
    if (result.status === "success") {
      Swal.fire({
        title: "Success",
        text: result.message,
        icon: "success",
        confirmButtonText: "OK",
      }).then(() => {
        // Optionally redirect or perform another action
        console.log("Ticket details:", result.ticket);
      });
    } else {
      Swal.fire({
        title: "Error",
        text: result.message,
        icon: "error",
      });
    }
  } catch (error) {
    console.error("Error booking ticket:", error);
    Swal.fire({
      title: "Error",
      text: "An unexpected error occurred. Please try again later.",
      icon: "error",
    });
  }
});

// Fetch and display user balance
async function fetchUserBalance() {
    try {
        const response = await fetch("../../DetailsUSER/getUserProfile.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ User_id: 1 }), // استبدل بـ User_id الديناميكي إذا كان متاحًا
        });
        const result = await response.json();

        if (result.status === "success") {
            const balance = result.data.balance;
            document.getElementById("userBalance").innerText = `Balance: $${balance}`;
        } else {
            console.error("Error fetching balance:", result.message);
        }
    } catch (error) {
        console.error("Error fetching balance:", error);
    }
}

// Call fetchUserBalance on page load
document.addEventListener("DOMContentLoaded", () => {
    fetchUserBalance();
});