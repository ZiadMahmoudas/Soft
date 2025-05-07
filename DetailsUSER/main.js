let logout = document.getElementById("logout");

logout.addEventListener("click", async function () {
  // Clear the token from localStorage
  localStorage.removeItem("authToken");

  // Optionally notify the backend about the logout
  await fetch('http://localhost/project/auth/signup.php', {
    method: 'POST',
    body: new URLSearchParams({ action: 'logout' }),
  });

  // Redirect to the login page
  window.location.href = 'http://localhost/project/auth/signup.html';
});



