<?php
  //Declare variables
  $fname='';
  $lname='';
  $email='';
  $email2='';
  $password='';
  $password2='';
  $date='';
  $error_array= array(); //holds error messages

  //form handling
  if(isset($_POST['reg_button'])){
    //register form values
    //first name
    $fname = strip_tags($_POST['reg_fname']); //remove html tags
    $fname = str_replace(' ', '', $fname); //remove spaces
    $fname = ucfirst(strtolower($fname)); // convert to lowercase then capitalize first letter
    $_SESSION['reg_fname'] = $fname; //Store first name into session variable

    //last name
    $lname = strip_tags($_POST['reg_lname']); //remove html tags
    $lname = str_replace(' ', '', $lname); //remove spaces
    $lname = ucfirst(strtolower($lname)); // convert to lowercase then capitalize first letter
    $_SESSION['reg_lname'] = $lname;

    //email
    $email = strip_tags($_POST['reg_email']); //remove html tags
    $email = str_replace(' ', '', $email); //remove spaces
    $email = ucfirst(strtolower($email)); // convert to lowercase then capitalize first letter
    $_SESSION['reg_email'] = $email;

    //email 2
    $email2 = strip_tags($_POST['reg_email2']); //remove html tags
    $email2 = str_replace(' ', '', $email2); //remove spaces
    $email2 = ucfirst(strtolower($email2)); // convert to lowercase then capitalize first letter
    $_SESSION['reg_email2'] = $email2;

    //password
    $password = strip_tags($_POST['reg_password']); //remove html tags
    $password2 = strip_tags($_POST['reg_password2']);

    //date
    $date = date("Y-m-d"); //current date

    if($email == $email2){
      if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        //check if email exists
        $email_check = mysqli_query($connection,"SELECT email FROM users WHERE email='$email'");
        //check rows returned
        $num_rows=mysqli_num_rows($email_check);
        if($num_rows > 0){
          array_push($error_array,"Email already in use<br>" ) ;
        }
      } else {
        array_push($error_array, "Invalid format<br>");
      }
    } else {
      array_push($error_array, "Emails don't match<br>");
    }

    if(strlen($fname) >25 || strlen($fname) < 2) {
      array_push($error_array, "Your first name must be between 2 and 25 characters<br>");
    }

    if(strlen($lname) >25 || strlen($lname) < 2) {
      array_push($error_array, "Your last name name must be between 2 and 25 characters<br>");
    }

    if($password != $password2){
      array_push($error_array,"Your passwords do not match!<br>");
    } else {
      if(preg_match('/[^A-Za-z0-9]/', $password)) {
        array_push($error_array,"Your password can only contain english characters or numbers<br>");
      }
    }

    if(strlen($password)>30 || strlen($password)< 5){
      array_push($error_array,"Your password must be between 5 and 30 characters<br>");
    }

    if(empty($error_array)){
      $password = md5($password); //encrypts the password

      // generate username by concatenation first name and last name
      $username= strtolower($fname . "_" . $lname);
      $check_username = mysqli_query($connection,"SELECT username FROM users WHERE username='$username'"); //check if username already exists in db

      $i = 0;
      //if username exists add number to username
      while(mysqli_num_rows($check_username) != 0) {
        $i++;
        $username = $username . $i;
        $check_username = mysqli_query($connection, "SELECT username FROM users WHERE username='$username'");
      }

      //random profile pic assigned
      $random = rand(1, 2); //create random number between one and two
      if($random = 1){
        $profile_pic="assets/images/profile_pics/defaults/blue.jpg";
      }
      else if($random=2){
        $profile_pic="assets/images/profile_pics/defaults/gray.jpg";
      }

      $query = mysqli_query($connection, "INSERT INTO users VALUES(NULL, '$fname', '$lname', '$username', '$email', '$password', '$date', '$profile_pic', '0', '0', 'no', ',')");

      array_push($error_array, "<span style='color: #14C800;'> You're all set! Please log in. </span><br>");

      //Clear session variables
      $_SESSION['reg_fname'] = '';
      $_SESSION['reg_lname'] = '';
      $_SESSION['reg_email'] = '';
      $_SESSION['reg_email2'] = '';
    }
  }
?>