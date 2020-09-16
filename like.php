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

    //get id of the post
    if(isset($_GET['post_id'])){
      $post_id = $_GET['post_id'];
    }

    $get_likes = mysqli_query($connection, "SELECT likes, added_by FROM posts WHERE id='$post_id'");
    $row=mysqli_fetch_array($get_likes);
    $total_likes=$row['likes'];
    $user_liked = $row['added_by'];

    $user_details = mysqli_query($connection, "SELECT * FROM users WHERE username='$user_liked'");
    $row=mysqli_fetch_array($user_details);
    $total_user_likes = $row['num_likes'];

    //like button
    if(isset($_POST['like-button'])){
      $total_likes++;
      $query = mysqli_query($connection, "UPDATE posts SET likes='$total_likes' WHERE id='$post_id'");
      $total_user_likes++;
      $user_likes=mysqli_query($connection, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$user_liked'");
      $insert_user = mysqli_query($connection,"INSERT INTO likes VALUES(NULL,'$userLoggedIn', '$post_id')");

      //insert notification
    }

    //unlike button
    if(isset($_POST['unlike-button'])){
      $total_likes--;
      $query = mysqli_query($connection, "UPDATE posts SET likes='$total_likes' WHERE id='$post_id'");
      $total_user_likes--;
      $user_likes=mysqli_query($connection, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$user_liked'");
      $insert_user = mysqli_query($connection,"DELETE FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");
    }

    //check for previous likes
    $check_query = mysqli_query($connection, "SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");
    $num_rows = mysqli_num_rows($check_query);

    if($num_rows > 0) {
      echo '<form action="like.php?post_id=' . $post_id . '" method="POST">
          <input type="submit" class="comment-like" name="unlike-button" value="Unlike">
          <div class="like-value">
            ' . $total_likes .' Likes
          </div>
          </form>';
    }
    else {
      echo '<form action="like.php?post_id=' . $post_id . '" method="POST">
      <input type="submit" class="comment-like" name="like-button" value="Like">
      <div class="like-value">
        ' . $total_likes .' Likes
      </div>
      </form>';
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/css/styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Kufam:wght@400;700&display=swap" rel="stylesheet">
  <title>Likes</title>
</head>
<body>
  <style>
    form {
      position: absolute;
      top: 0;
    }
  </style>
</body>
</html>