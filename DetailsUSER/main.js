let logout = document.getElementById("logout");
console.log(logout);
console.log(555);
logout.addEventListener("click", async function () {

    await fetch('', {
      method: 'POST', 
      credentials: 'include' 
    });

    window.location.href = 'http://localhost/project/LoginANDSign/signup.php';
  });



