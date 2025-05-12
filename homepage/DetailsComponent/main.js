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

const source = document.getElementById("source");
const destination = document.getElementById("destination");
const ticketClass = document.getElementById("class");
const ticketType = document.getElementById("ticket_type");
const price =  document.getElementById("price");
const  payment= document.getElementById("newPrice");
// Fetch dynamic price
async function fetchPrice() {
    const src = source.value;
    const dest = destination.value;
    const cls = ticketClass.value;
    const type = ticketType.value;
  if (src && dest && cls && type) {
    const formData = new FormData();
    formData.append("Source", src);
    formData.append("Destination", dest);
    formData.append("Class", cls);
    formData.append("Ticket_type", type);

      const response = await fetch("calculatePrice.php", { method: "POST", body: formData });
      const result = await response.json();
      price.innerText = `Price: $${result.price}`;
      payment.value = result.price;
    } 
  }


source.addEventListener("change", fetchPrice);
destination.addEventListener("change", fetchPrice);
ticketClass.addEventListener("change", fetchPrice);
ticketType.addEventListener("change", fetchPrice);

// Submit ticket booking
document.getElementById("ticketForm").addEventListener("click", async function (e) {
 
    const src = source.value;
    const dest = destination.value;
    const cls = ticketClass.value;
    const type = ticketType.value;
    const Price = payment.value;
    if (!src || !dest || !cls || !type || !Price) {
        Swal.fire({
            title: "Error",
            text: "Please fill in all fields before submitting.",
            icon: "error",
        });
        return;
    }

    const formData = new FormData();
    formData.append("Source", src);
    formData.append("Destination", dest);
    formData.append("Class", cls);
    formData.append("Ticket_type", type);
    formData.append("Price", Price);


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
            }).then((e) => {
                if (e.isConfirmed) {           
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
                    document.getElementById("userBalance").innerText = `Balance: $${result.balance}`;
                }
            });
        } else {
            Swal.fire({
                title: "Error",
                text: result.message,
                icon: "error",
            });
        }
    
});



// Fetch and display user balance
async function fetchUserBalance() {
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
}

    fetchUserBalance();
