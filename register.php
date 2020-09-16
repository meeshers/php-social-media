<?php
  require 'config/config.php';
  require 'includes/forms/register_form.php';
  require 'includes/forms/login_form.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/css/register.css">
  <link href="https://fonts.googleapis.com/css2?family=Kufam:wght@400;700&display=swap" rel="stylesheet">
  <script
  src="https://code.jquery.com/jquery-3.5.1.min.js"
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  crossorigin="anonymous"></script>
  <script src="assets/javascript/register.js"></script>
  <title>Register</title>
</head>
<body>
  <?php
    if(isset($_POST['reg_button'])) {
      echo '
        <script>
          $(document).ready(function(){
            $(".first").hide();
            $(".second").show();
          });
        </script>
      ';
    }
  ?>

  <div class="wrapper">
    <div class="login">
      <div class="login-header">
        <h1>NotFacebook</h1>
        Login or sign up below!
      </div>
    <!-- LOGIN FORM -->
    <div class="first">
      <form action="register.php" method="POST">
        <input type="email" name="login_email" placeholder="Email Address"
        value= "<?php 
            if(isset($_SESSION['login_email'])){
              echo $_SESSION['login_email'];
            }
          ?>"
        required>
        <br>
        <input type="password" name="login_password" placeholder="Password" required>
        <br>

        <?php
          if(in_array("Email or password was incorrect!<br>", $error_array)) {
            echo "Email or password was incorrect!<br>";
          }
        ?>
        <input type="submit" name="login_button" value="Login">
        <br>
        <a href="#" id="signup" class="signup">Need an account? Register here!</a>
      </form>
      </div>
    <br>
    
  <!-- REGISTER FORM -->
  <div class="second">
      <form action="register.php" method="POST">
        <input type="text" name="reg_fname" placeholder="First Name"
          value= "<?php 
            if(isset($_SESSION['reg_fname'])){
              echo $_SESSION['reg_fname'];
            }
          ?>"
        required>
        <br>
        <?php 
          if(in_array("Your first name must be between 2 and 25 characters<br>", $error_array)){
            echo "Your first name must be between 2 and 25 characters<br>";
          }    
        ?>

        <input type="text" name="reg_lname" placeholder="Last Name" 
        value= "<?php 
            if(isset($_SESSION['reg_lname'])){
              echo $_SESSION['reg_lname'];
            }
          ?>"
        required>
        <br>
        <?php 
          if(in_array("Your last name name must be between 2 and 25 characters<br>", $error_array)){
            echo "Your last name name must be between 2 and 25 characters<br>";
          }    
        ?>

        <input type="email" name="reg_email" placeholder="Email" 
        value= "<?php 
            if(isset($_SESSION['reg_email'])){
              echo $_SESSION['reg_email'];
            }
          ?>"
        required>
        <br>
        <input type="email" name="reg_email2" placeholder="Confirm Email" 
        value= "<?php 
            if(isset($_SESSION['reg_email2'])){
              echo $_SESSION['reg_email2'];
            }
          ?>"
        required>
        <br>
        <?php 
          if(in_array("Email already in use<br>", $error_array)){
            echo "Email already in use<br>";
          }    
          else if(in_array("Invalid format<br>", $error_array)){
            echo "Invalid format<br>";
          }    
          else if(in_array("Emails don't match<br>", $error_array)){
            echo "Emails don't match<br>";
          }    
        ?>

        <input type="password" name="reg_password" placeholder="Password" required>
        <br>
        <input type="password" name="reg_password2" placeholder="Confirm Password" required>
        <br>
        <?php 
          if(in_array("Your passwords do not match!<br>", $error_array)){
            echo "Your passwords do not match!<br>";
          }    
          else if(in_array("Your password can only contain english characters or numbers<br>", $error_array)){
            echo "Your password can only contain english characters or numbers<br>";
          }    
          else if(in_array("Your password must be between 5 and 30 characters<br>", $error_array)){
            echo "Your password must be between 5 and 30 characters<br>";
          }    
        ?>

        <input type="submit" name="reg_button" value="Register">
        <br>
        <?php 
          if(in_array("<span style='color: #14C800;'> You're all set! Please log in. </span><br>", $error_array)){
            echo "<span style='color: #14C800;'> You're all set! Please log in. </span><br>";
          }    
        ?>

        <a href="#" id="sign-in" class="sign-in">Already have an account? Sign in here!</a>
      </form>
      </div>
  </div>
</body>
</html>