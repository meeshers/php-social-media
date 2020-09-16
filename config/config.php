<?php
  ob_start(); // Turns on output buffering
  session_start();

  $timezone = date_default_timezone_set("America/Chicago");

  $connection = mysqli_connect('localhost', 'root', '', 'social');

  // if there is an error connecting to the db, display error
  if(mysqli_connect_errno()){
    echo "Failed to connect: " . mysqli_connect_errno();
  }

  //$query = mysqli_query($connection, "INSERT INTO test VALUES(NULL, 'test name')");
?>