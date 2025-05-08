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
        row.setAttribute("data-user-id", user.user_id || ""); // تأكد من تعيين القيمة
        row.setAttribute("data-user-name", user.user_name?.toLowerCase() || ""); // تأكد من تعيين القيمة
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

// Filter users based on search input
document.getElementById("searchUserInput").addEventListener("input", function () {
  const searchValue = this.value.toLowerCase();
  const rows = document.querySelectorAll("#userTableBody tr");

  rows.forEach(row => {
    const userId = row.getAttribute("data-user-id"); // استخدم getAttribute لجلب القيمة النصيةة
    const userName = row.getAttribute("data-user-name"); // استخدم getAttribute لجلب القيمة النصيةة

    if (userId.includes(searchValue) || userName.includes(searchValue)) {
      row.style.display = ""; // عرض الصف إذا تطابق
    } else {
      row.style.display = "none"; // إخفاء الصف إذا لم يتطابق
    }
  });
});

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
        text: 'Balance updated successfully.',
        timer: 2000,
        showConfirmButton: true
      });
      fetchUsers(); // Refresh the table
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Update Failed',
        text: result.message
      });
    }
  } catch (error) {
    console.error("Error updating balance:", error);
  }
}

// Fetch and display stations in the table
async function fetchStations() {
  try {
    const response = await fetch("manageStations.php");
    const result = await response.json();

    if (result.status === "success") {
      const tableBody = document.getElementById("stationTableBody");
      tableBody.innerHTML = ""; // Clear existing rows

      result.stations.forEach(station => {
        const row = document.createElement("tr");
        row.innerHTML = `
          <td>${station.station_id}</td>
          <td>${station.station_name}</td>
          <td>${station.city}</td>
        `;
        tableBody.appendChild(row);
      });
    } else {
      console.error("Error fetching stations:", result.message);
    }
  } catch (error) {
    console.error("Error fetching stations:", error);
  }
}

// Add a new station
document.getElementById("addStationForm").addEventListener("click", async function (e) {
  e.preventDefault();

  const stationName = document.getElementById("stationName").value.trim();
  const city = document.getElementById("city").value.trim();

  if (stationName && city) {
    const formData = new FormData();
    formData.append("station_name", stationName);
    formData.append("city", city);

    try {
      const response = await fetch("manageStations.php", { method: "POST", body: formData });
      const result = await response.json(); // تأكد من تحليل JSON فقط

      if (result.status === "success") {
        Swal.fire({
          icon: 'success',
          title: 'Add Station successfully',
          text: 'Balance updated successfully.',
          timer: 2000,
          showConfirmButton: true
        });
        fetchStations(); // Refresh the table
        document.getElementById("addStationForm").reset(); // Clear the form
      } 
    } catch (error) {
      console.error("Error adding station:", error);
    }
  }
});

// Search for a user by ID
document.getElementById("searchUserInput").addEventListener("keyup", async function (e) {
  e.preventDefault();

  const userId = document.getElementById("searchUserInput").value.trim();

  if (userId) {
    try {
      const response = await fetch(`managePrices.php?user_id=${userId}`);
      const result = await response.json();

      if (result.status === "success") {
        const user = result.user;
        const tableBody = document.getElementById("userTableBody");
        tableBody.innerHTML = ""; // Clear existing rows

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

        // Add event listener to the update button
        document.querySelector(".update-btn").addEventListener("click", updateBalance);
      } 
    } catch (error) {
      console.error("Error searching for user:", error);
    }
  }
});


fetchUsers();


fetchStations();
