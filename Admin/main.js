
// Fetch and display users in the table
async function fetchUsers() {
  try {
    const response = await fetch("managePrices.php");
    const result = await response.json();

    if (result.status === "success") {
      const tableBody = document.getElementById("userTableBody");
      tableBody.innerHTML = ""; // Clear existing rows

      result.users.forEach(user => {
        const row = document.createElement("tr");
        row.innerHTML = `
          <td>${user.user_id}</td>
          <td>${user.user_name}</td>
          <td><input type="number" class="form-control balance-input" value="${user.balance}" data-user-id="${user.user_id}"></td>
          <td>
            <button class="btn btn-success update-btn" data-user-id="${user.user_id}">Update</button>
          </td>
        `;
        tableBody.appendChild(row);
      });

      // Add event listeners to update buttons
      document.querySelectorAll(".update-btn").forEach(button => {
        button.addEventListener("click", updateBalance);
      });
    } else {
      console.error(result.message);
    }
  } catch (error) {
    console.error("Error fetching users:", error);
  }
}

// Update user balance
async function updateBalance(event) {
  const userId = event.target.getAttribute("data-user-id");
  const input = document.querySelector(`.balance-input[data-user-id="${userId}"]`);
  const newBalance = input.value;

  const formData = new FormData();
  formData.append("user_id", userId);
  formData.append("new_balance", newBalance);

  try {
    const response = await fetch("managePrices.php", { method: "POST", body: formData });
    const result = await response.json();

    if (result.status === "success") {
      Swal.fire({
        icon: 'success',
        title: 'Update Successfully',
        text: 'You will be redirected to the login page.',
        timer: 2000,
        showConfirmButton: true
      })
      fetchUsers(); // Refresh the table
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Signup Failed',
        text: result.message
      });
    }
  } catch (error) {
    console.error("Error updating balance:", error);
  }
}

// Fetch users on page load
fetchUsers();
