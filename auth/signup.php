<?php
require_once '../users.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'login') {
        $name = $_POST['name'];
        $password = $_POST['password'];

        // Check if the user is an admin
        $adminResult = (new Admin())->login($name, $password);
        if ($adminResult) {
            echo json_encode(['status' => 'success', 'isAdmin' => true, 'message' => 'Admin login successful']);
            exit;
        }

        // Check if the user is a regular user
        $userResult = (new User())->login($name, $password);
        if ($userResult) {
            echo json_encode(['status' => 'success', 'isAdmin' => false, 'message' => 'User login successful']);
            exit;
        }

        // If neither, return an error
        echo json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
        exit;
    } elseif ($action === 'signup') {
        $name = $_POST['name'];
        $address = $_POST['address'];
        $password = $_POST['password'];

        try {
            (new User())->signup($name, $address, $password);
            echo json_encode(['status' => 'success', 'message' => 'Signup successful']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Signup failed: ' . $e->getMessage()]);
        }
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignUp</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>
    <link rel="stylesheet" href="style/css.css">
    <link rel="stylesheet" href="../componentCSS/StaticStyles/staticCss.css">
</head>
<body>
<div class="container-fluid full-screen">
    <div class="row">
        <div class="col-xs-12 col-12 col-md-5 col-lg-4 authfy-panel-left">
            <div class="brand-logo text-center">
              <img src="../componentImages/img/brand-logo.png" width="150" alt="brand-logo">
            </div>
            <div class="authfy-login">
              <div class="authfy-panel panel-login text-center active">
                <div class="authfy-heading">
                  <h3 class="auth-title">Login to your account</h3>
                  <p>Don’t have an account? <button class="lnk-toggler" data-panel=".panel-signup">Sign Up Free!</button></p>
                </div>
                <div class="row social-buttons">
                  <div class="col-xs-4 col-4">
                    <a href="#" class="btn btn-lg  btn-facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                  </div>
                  <div class="col-xs-4 col-4">
                    <a href="#" class="btn btn-lg  btn-twitter">
                        <i class="bi bi-twitter-x"></i>
                    </a>
                  </div>
                  <div class="col-xs-4 col-4">
                    <a href="#" class="btn btn-lg  btn-google">
                        <i class="bi bi-google"></i>
                    </a>
                  </div>
                </div>
                <div class="row loginOr">
                  <div class="col-xs-12 col-sm-12">
                    <span class="spanOr">or</span>
                  </div>
                </div>
                <div class="row ">
                  <div class="col-xs-12 col-sm-12 ">
                    <form id="loging">
                      <div class="form-group">
                        <input type="text" class="form-control" name="name" id="name1" placeholder="Username" >
                      </div>
                      <p class="text-danger" id="errorBox"></p>
                      <div class="form-group">
                        <input type="password" class="form-control" name="password" id="pass" placeholder="Password" >
                        <i class="bi bi-eye-slash-fill eye" id="eye"></i>
                      </div>
                      <p class="text-danger" id="errorBoxs"></p>
                      <button id="LoginForm" type="button"  class="btn btn-lg btn-primary">Login</button>
                    </form>
                  </div>
                </div>
              </div>
              <div class="authfy-panel panel-signup text-center">
                <div class="row">
                  <div class="col-xs-12 col-sm-12">
                    <div class="authfy-heading">
                      <h3 class="auth-title">Sign up for free!</h3>
                    </div>
                    <form  id="formSign">
                      <div class="form-group">
                        <input type="text" class="form-control" name="name" id="name" placeholder="Full name" required>
                      </div>
                      <p class="text-danger text-start" id="errorname">Enter Your Name</p>
                      <div class="form-group">
                        <input type="text" class="form-control" name="address" id="addressSignup" placeholder="Address" required>
                      </div>
                      <p class="text-danger text-start" id="erroraddress">Enter Your address</p>
                      <div class="form-group">
                        <input type="password" class="form-control" name="password" id="pass1" placeholder="Password" required>
                        <i class="bi bi-eye-slash-fill eye" id="eye1"></i>
                      </div>
                      <p class="text-danger text-start" id="errorpass">Enter Your password</p>
                      <button type="button" id="signupForm" class="btn btn-lg btn-primary btn-block">Sign Up</button>
                    </form>
                    <a class="lnk-toggler" data-panel=".panel-login" href="#">Already have an account?</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
    <div class="col-md-7 col-lg-8 authfy-panel-right hidden-xs hidden-sm">
        <div class="hero-heading">
          <div class="headline">
            <h3>Welcome to Authfy Account</h3>
            <p>We're happy to hear your song. Start your journey easily and book your ticket now!</p>
          </div>
        </div>
      </div>
    </div>
</div>
</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="vendor/main.js"></script>

</html>