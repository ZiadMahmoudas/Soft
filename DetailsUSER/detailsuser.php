<?php
require_once '../users.php';
require_once '../logout.php';
include_once "./detailsuser.php";
//   header("location: ../auth/signup.php");
  $adminResult = (new Admin())->logout();
  $userResult = (new User())->logout();
?>