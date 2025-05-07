const regAddress = /^[a-zA-Z\s,]{3,}$/i;
const regPassword = /^\w{4,}$/i;
const regName = /^[a-zA-Z ]{2,}$/i;

let btnSignUp = document.getElementById("signupForm");
let namesignup = document.getElementById("name");
let passSignup = document.getElementById("pass1");
let addressSignup = document.getElementById("addressSignup");

let btnLoginIN = document.getElementById("LoginForm");
let namelogin = document.getElementById("name1");
let passLogin = document.getElementById("pass");

const errorname = document.getElementById("errorname");
const erroraddress = document.getElementById("erroraddress");
const errorpass = document.getElementById("errorpass");
const errorBox = document.getElementById("errorBox");
const errorBoxs = document.getElementById("errorBoxs");

let btneye = document.getElementById("eye");
let btneye1 = document.getElementById("eye1");
let formSign = document.getElementById("formSign");
let formlogin = document.getElementById("loging");
// Toggle password visibility for signup
function toggleEye(icon, input) {
  const show = input.type === "password";
  input.type = show ? "text" : "password";
  icon.classList.toggle("bi-eye-fill", show);
  icon.classList.toggle("bi-eye-slash-fill", !show);
}

btneye.addEventListener("click", () => toggleEye(btneye, passLogin));
btneye1.addEventListener("click", () => toggleEye(btneye1, passSignup));

// Slide for forms
document.querySelectorAll(".lnk-toggler").forEach(btn => {
  btn.addEventListener("click", function () {
    const panelSelector = btn.getAttribute("data-panel");
    document.querySelectorAll(".authfy-panel").forEach(panel => panel.classList.remove("active"));
    document.querySelector(panelSelector).classList.add("active");
  });
});

// Hide error messages initially
errorname.style.display = "none";
erroraddress.style.display = "none";
errorpass.style.display = "none";
errorBox.style.display = "none";
errorBoxs.style.display = "none";

// Validation for signup fields
namesignup.addEventListener("input", () => {
  if (regName.test(namesignup.value.trim())) {
    errorname.style.display = "none";
  } else {
    errorname.style.display = "block";
    errorname.textContent = "Name must be at least 2 characters.";
  }
});

addressSignup.addEventListener("input", () => {
  if (regAddress.test(addressSignup.value.trim())) {
    erroraddress.style.display = "none";
  } else {
    erroraddress.style.display = "block";
    erroraddress.textContent = "Address must be at least 3 characters.";
  }
});

passSignup.addEventListener("input", () => {
  if (regPassword.test(passSignup.value.trim())) {
    errorpass.style.display = "none";
  } else {
    errorpass.style.display = "block";
    errorpass.textContent = "Password must be at least 4 characters.";
  }
});

// Validation for login fields
namelogin.addEventListener("input", () => {
  if (regName.test(namelogin.value.trim())) {
    errorBox.style.display = "none";
  } else {
    errorBox.style.display = "block";
    errorBox.textContent = "Username is required.";
  }
});

passLogin.addEventListener("input", () => {
  if (regPassword.test(passLogin.value.trim())) {
    errorBoxs.style.display = "none";
  } else {
    errorBoxs.style.display = "block";
    errorBoxs.textContent = "Password is required.";
  }
});

// Signup button click handler
btnSignUp.addEventListener("click", async function (e) {
  e.preventDefault();

  const fields = {
    name: namesignup.value.trim(),
    address: addressSignup.value.trim(),
    password: passSignup.value.trim(),
  };

  let isValid = true;

  if (!regName.test(fields.name)) {
    errorname.style.display = "block";
    isValid = false;
  }

  if (!regAddress.test(fields.address)) {
    erroraddress.style.display = "block";
    isValid = false;
  }

  if (!regPassword.test(fields.password)) {
    errorpass.style.display = "block";
    isValid = false;
  }

  if (isValid) {
    const formData = new FormData();
    formData.append("action", "signup");
    formData.append("name", fields.name);
    formData.append("address", fields.address);
    formData.append("password", fields.password);

    try {
      const response = await fetch('', { method: 'POST', body: formData });
      const result = await response.json();

      if (result.status === "success") {
        Swal.fire({
          icon: 'success',
          title: 'Signup Successful',
          text: 'You will be redirected to the login page.',
          timer: 2000,
          showConfirmButton: true
        })
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Signup Failed',
          text: result.message
        });
      }
    } catch (error) {
      console.error("Error during signup:", error);
    }
  }
  formSign.reset();
});


// Login button click handler
btnLoginIN.addEventListener("click", async function (e) {
  e.preventDefault();

  const fields = {
    name: namelogin.value.trim(),
    password: passLogin.value.trim(),
  };

  let isValid = true;

  if (fields.name === "") {
    errorBox.style.display = "block";
    isValid = false;
  }

  if (fields.password === "") {
    errorBoxs.style.display = "block";
    isValid = false;
  }

  if (isValid) {
    const formData = new FormData();
    formData.append("action", "login");
    formData.append("name", fields.name);
    formData.append("password", fields.password);


      console.log("Sending login data:", fields); 
      const response = await fetch('', { method: 'POST', body: formData });
      const result = await response.json();
      console.log("Login response:", result); 
     
      formlogin.reset();
      if (result.status === "success") {
        Swal.fire({
          icon: 'success',
          title: 'Login Successful',
          text: 'Redirecting...',
          timer: 2000,
          showConfirmButton: true
        }).then(() => {
          if (result.isAdmin) {
            window.location.href = "../../Admin/admin.html";
          } else {
           
            window.location.href = "../../DetailsUSER/main.js";
          }
        });
      }
    
      
      else {
        Swal.fire({
          icon: 'error',
          title: 'Login Failed',
          text: result.message
        });
      
    }
  }
});

