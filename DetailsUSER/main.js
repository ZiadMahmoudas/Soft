let logout = document.getElementById("logout");

logout.addEventListener("click", function () {
  // Clear the token from localStorage
  localStorage.removeItem("authToken");
  localStorage.removeItem("lang");
  localStorage.removeItem("theme");
  window.location.href = 'http://localhost/project/auth/signup.html';
});

// Fetch and display user profile
async function fetchUserProfile() {
  try {
    const response = await fetch("getUserProfile.php", { method: "GET" });
    const result = await response.json();

    if (result.status === "success") {
      // Update user profile fields dynamically
      document.querySelector(".coll h3").innerText = `Hello User: ${result.data.name}`;
      document.querySelector(".coll p").innerText = `Balance: $${result.data.balance}`;
    } else {
      console.error(result.message);
    }
  } catch (error) {
    console.error("Error fetching user profile:", error);
  }
}

// Save updated profile
async function saveUserProfile() {
  const updatedProfile = {
    first_name: document.getElementById("FirstName").value.trim(),
    address: document.getElementById("Address").value.trim(),
    password: document.getElementById("Password").value.trim(),
  };

  try {
    const response = await fetch("updateUserProfile.php", {
      method: "POST",
      body: JSON.stringify(updatedProfile),
    });
    const result = await response.json();

    if (result.status === "success") {
      alert("Profile updated successfully!");
      fetchUserProfile(); // Refresh profile data
    } else {
      alert("Error updating profile: " + result.message);
    }
  } catch (error) {
    console.error("Error saving user profile:", error);
  }
}

// Attach event listener to the save button
document.querySelector(".edit").addEventListener("click", saveUserProfile);

// Call fetchUserProfile on page load
fetchUserProfile();



