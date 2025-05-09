let logout = document.getElementById("logout");

logout.addEventListener("click", function () {
  localStorage.removeItem("lang");
  localStorage.removeItem("theme");
  window.location.href = 'http://localhost/Soft/auth/signup.html';
});

// Handle login
const User_name = document.querySelector("#User_name");
const Balance = document.querySelector("#Balance");


// Fetch User_id from the server if not found in the DOM
async function getUserIdFromServer() {
  try {
    const response = await fetch("getUserId.php", { method: "GET" });
    const result = await response.json();
    if (result.status === "success") {
      return result.User_id;
    } 
  } catch (error) {
    console.error("Error fetching User ID from server:", error);
    return null;
  }
}


// Fetch and display user profile
async function fetchUserProfile() {
  const User_id = await getUserIdFromServer();
  if (!User_id) {
    alert("User ID is missing. Please check the page setup.");
    return;
  }

  try {
    const response = await fetch("getUserProfile.php", {
      method: "POST",
      body: JSON.stringify({ User_id }),
    });
    const result = await response.json();

    if (result.status === "success") {
      const user = result.data;
      document.querySelector(".coll h3").innerText = `HELLO USER: ${user.name}`;
      document.querySelector(".coll p").innerText = `BALANCE: $${user.balance}`;
    } else {
      alert("ERROR FETCHING PROFILE: " + result.message);
    }
  } catch (error) {
    console.error("ERROR FETCHING USER PROFILE:", error);
  } 
}

// Save updated profile
async function saveUserProfile() {
  const User_id = await getUserIdFromServer();
  if (!User_id) {
    alert("User ID is missing. Please check the page setup.");
    return;
  }

  const updatedProfile = {
    User_id,
    Address: document.getElementById("Address").value.trim(),
    name: document.getElementById("name").value.trim(),
    Password: document.getElementById("Password").value.trim(),
  };

  try {
    const response = await fetch("updateUserProfile.php", {
      method: "POST",
      body: JSON.stringify(updatedProfile),
    });
    const result = await response.json();

    if (result.status === "success") {
      Swal.fire({
        icon: 'success',
        title: 'Successful',
        text: 'Good Response',
        timer: 2000,
        showConfirmButton: true
      })
      fetchUserProfile(); 
      window.location.href = 'http://localhost/Soft/auth/signup.html';
    } else {
      Swal.fire({
        icon: 'error',
        title: `enter Data:`,
        text: `${result.message}`,
        timer: 2000,
        showConfirmButton: true
      })
    }
  } catch (error) {
    console.error("ERROR SAVING USER PROFILE:", error);
  } 
}

// Attach event listener to the save button
document.querySelector(".edit").addEventListener("click", saveUserProfile);

// Call fetchUserProfile on page load
fetchUserProfile();



