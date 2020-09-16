<?php
  require 'config/config.php';
  include("includes/classes/User.php");
  include("includes/classes/Post.php");

  if(isset($_SESSION['username'])) {
    $userLoggedIn = $_SESSION['username'];
    $user_detail = mysqli_query($connection,"SELECT * FROM users WHERE username='$userLoggedIn'");
    $user = mysqli_fetch_array($user_detail);
  }
  else {
    header("Location: register.php"); // sends user back to register page if not logged in
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- font -->
  <link href="https://fonts.googleapis.com/css2?family=Kufam:wght@400;700&display=swap" rel="stylesheet">
  <!-- bootstrap -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
  <!-- fontawesome -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  <!-- header css -->
  <link rel="stylesheet" href="assets/css/styles.css">
  <!-- js -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js" integrity="sha512-8vfyGnaOX2EeMypNMptU+MwwK206Jk1I/tMQV4NkhOz+W8glENoMhGyU6n/6VgQUhQcJH8NqQgHhMtZjJJBv3A==" crossorigin="anonymous"></script>
  <script src="assets/javascript/demo.js"></script>
  

  <!-- <script
  src="https://code.jquery.com/jquery-3.5.1.min.js"
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  crossorigin="anonymous"></script> -->
  <title>NotFacebook</title>
</head>
<body>

  <div class="navigation">
    <div class="logo">
      <a href="index.php"><i class="fa fa-snowflake-o" aria-hidden="true"></i>
        NotFacebook</a>
    </div>
    <div class="nav">
      <a href="<?php echo $userLoggedIn; ?>">
        <?php 
          echo $user['first_name'];
        ?>
      </a>
      <a href="#"><i class="fa fa-home" aria-hidden="true"></i></a>
      <a href="#"><i class="fa fa-envelope" aria-hidden="true"></i></a>
      <a href="#"><i class="fa fa-bell-o" aria-hidden="true"></i></a>
      <a href="request.php"><i class="fa fa-user-circle-o" aria-hidden="true"></i></a>
      <a href="#"><i class="fa fa-cog" aria-hidden="true"></i></a>
      <a href="includes/handlers/logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i></a>
    </div>
  </div>

  <div class="wrapper">