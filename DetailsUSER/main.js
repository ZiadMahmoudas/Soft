let logout = document.getElementById("logout");

logout.addEventListener("click", function () {
  // Clear the token from localStorage
  localStorage.removeItem("authToken");
  localStorage.removeItem("lang");
  localStorage.removeItem("theme");
  window.location.href = 'http://localhost/project/auth/signup.html';
});

// Fetch and display user balance
async function fetchBalance() {
  const userId = 1; // Replace with dynamic user_id (e.g., from session or localStorage)

  const formData = new FormData();
  formData.append("user_id", userId);

  try {
    const response = await fetch("getBalance.php", { method: "POST", body: formData });
    const result = await response.json();

    if (result.status === "success") {
      document.getElementById("balance").innerText = `Balance: $${result.balance}`;
    } else {
      console.error(result.message);
    }
  } catch (error) {
    console.error("Error fetching balance:", error);
  }
}

// Call fetchBalance on page load
fetchBalance();



