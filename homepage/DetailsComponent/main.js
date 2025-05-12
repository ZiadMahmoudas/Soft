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
    const source = document.getElementById("source").value;
    const destination = document.getElementById("destination").value;
    const ticketClass = document.getElementById("class").value;
    const ticketType = document.getElementById("ticket_type").value;
    const price = document.getElementById("priceInput").value;

    if (!source || !destination || !ticketClass || !ticketType || !price) {
        Swal.fire({
            title: "Error",
            text: "Please fill in all fields before submitting.",
            icon: "error",
        });
        return;
    }

    const formData = new FormData();
    formData.append("Source", source);
    formData.append("Destination", destination);
    formData.append("Class", ticketClass);
    formData.append("Ticket_type", ticketType);
    formData.append("Price", price);

    try {
        const response = await fetch("bookTicket.php", { method: "POST", body: formData });
        const result = await response.json();

        if (result.status === "success") {
            Swal.fire({
                title: "Ticket Booked!",
                text: "Your ticket has been successfully booked.",
                icon: "success",
                confirmButtonText: "Print Ticket",
                showCancelButton: true,
                cancelButtonText: "Close",
            }).then((action) => {
                if (action.isConfirmed) {                    
                    const ticketContent = `
                        Ticket Details:
                        -----------------------------------------
                        Source: ${result.ticket.Source}
                        Destination: ${result.ticket.Destination}
                        Class: ${result.ticket.Class}
                        Ticket Type: ${result.ticket.Ticket_type}
                        Price: $${result.ticket.Price}
                        -----------------------------------------
                        Thank you for booking with Train Station!
                    `;
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF();
                    doc.text(ticketContent, 10, 10);
                    doc.save("ticket.pdf");
                }
            });

            // Update user balance
            document.getElementById("userBalance").innerText = `Balance: $${result.balance}`;
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
        // Fetch User ID from the server
        const userIdResponse = await fetch("getUserId.php");
        const userIdResult = await userIdResponse.json();

        if (userIdResult.status !== "success") {
            console.error("Error fetching User ID:", userIdResult.message);
            return;
        }

        const User_id = userIdResult.User_id;

        // Fetch Balance using User ID
        const response = await fetch("getBalance.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ User_id }),
        });
        const result = await response.json();

        if (result.status === "success") {
            const balance = result.balance;
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
