<?php
  if(isset($_POST['login_button'])) {
    $email = filter_var($_POST['login_email'], FILTER_SANITIZE_EMAIL); //sanitize email
    $_SESSION['login_email'] = $email; //store email into session variable
    $password = md5($_POST['login_password']); //get password to match

    $check_db_query = mysqli_query($connection, "SELECT * FROM users WHERE email='$email' AND password='$password'");
    $check_login_query = mysqli_num_rows($check_db_query);

    if($check_login_query == 1){
      $row = mysqli_fetch_array($check_db_query);
      $username = $row['username'];

      $user_closed_q = mysqli_query($connection,"SELECT * FROM users WHERE email='$email' AND user_closed='yes'");
      if(mysqli_num_rows($user_closed_q) == 1) {
        $reopen_account = mysqli_query($connection,"UPDATE users SET user_closed ='no' WHERE email='$email'");
      }

      $_SESSION['username'] = $username;
      header("Location: index.php"); //redirects to index php
      exit();
    }
    else {
      array_push($error_array, "Email or password was incorrect!<br>");
    }

  }
?>