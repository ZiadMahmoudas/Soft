let logout = document.getElementById("logout");

logout.addEventListener("click", function () {
  localStorage.removeItem("userData");
  window.location.href = 'http://localhost/project/auth/signup.html';
});

// Handle login
async function handleLogin() {
  const name = document.getElementById("username").value.trim();
  const password = document.getElementById("password").value.trim();

  try {
    const response = await fetch("signup.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ action: "login", name, password }),
    });

    const result = await response.json();

    if (result.status === "success") {
      // تخزين بيانات المستخدم في localStorage
      localStorage.setItem("userData", JSON.stringify(result.data));
      alert("Login successful!");
      window.location.href = "detailsuser.html"; // الانتقال إلى صفحة التفاصيل
    } else {
      alert("Login failed: " + result.message);
    }
  } catch (error) {
    console.error("Error during login:", error);
  }
}

// Fetch and display user profile
function fetchUserProfile() {


  // تحديث اسم المستخدم والرصيد في الصفحة
  document.querySelector(".coll h3").innerText = `Hello User: ${userData.user_name || "Unknown"}`;
  document.querySelector(".coll p").innerText = `Balance: $${userData.balance || 0}`;
}

// Save updated profile
async function saveUserProfile() {
  const userData = JSON.parse(localStorage.getItem("userData"));
  if (!userData || !userData.user_id) {
    alert("User not logged in!");
    return;
  }

  const updatedProfile = {
    user_id: userData.user_id, // تمرير user_id
    address: document.getElementById("Address").value.trim(),
    password: document.getElementById("Password").value.trim(),
  };

  try {
    const response = await fetch("updateUserProfile.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
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



