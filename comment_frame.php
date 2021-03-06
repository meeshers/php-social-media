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
  <link rel="stylesheet" href="assets/css/styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Kufam:wght@400;700&display=swap" rel="stylesheet">
  <title>Comments</title>
</head>
<body>

  <script>
  //toggle the comment section
    function toggle() {
      let element = document.getElementById('comment-section');
      if(element.style.display == 'block'){
        element.style.display = 'none';
      }
      else{
        element.style.display = 'block';
      }
    }
  </script>

  <?php 
    //get id of the post
    if(isset($_GET['post_id'])){
      $post_id = $_GET['post_id'];
    }

    $user_query = mysqli_query($connection, "SELECT added_by, user_to FROM posts WHERE id='$post_id'");
    $row = mysqli_fetch_array($user_query);
    $posted_to = $row['added_by'];

    if(isset($_POST['postComment' . $post_id])){
      $post_body = $_POST['post_body'];
      $post_body = mysqli_escape_string($connection, $post_body);
      $date_time_now = date("Y-m-d H:i:s");
      $insert_post = mysqli_query($connection, "INSERT INTO comments VALUES(NULL, '$post_body', '$userLoggedIn','$posted_to','$date_time_now', 'no', '$post_id')");
      echo "<p>Comment posted!</p>";
    }
  ?>

  <form action="comment_frame.php?post_id=<?php echo $post_id ?>" id="comment-form" name="postComment<?php echo $post_id ?>" method="POST">
    <textarea name="post_body"></textarea>
    <input type="submit" name="postComment<?php echo $post_id; ?>" value="Post">
  </form>

  <!-- Load comments -->
  <?php 
    $get_comments = mysqli_query($connection, "SELECT * FROM comments WHERE post_id='$post_id' ORDER BY id ASC"); //order by ascending order
    $count=mysqli_num_rows($get_comments);

    if($count != 0){
      while($comment = mysqli_fetch_array($get_comments)){
        $comment_body = $comment['post_body'];
        $posted_to = $comment['posted_to'];
        $posted_by = $comment['posted_by'];
        $date_added = $comment['date_added'];
        $removed = $comment['removed'];

        //timeframe
        $date_time_now = date('Y-m-d H:i:s');
        $start_date = new DateTime($date_added); //time of post
        $end_date = new DateTime($date_time_now); //current time
        $interval = $start_date->diff($end_date); //difference between dates
        if($interval->y >=1) {
          if($interval==1){
            $time_message = $interval->y . " year ago"; //this will say 1 year ago
          }
          else {
            $time_message = $interval->y . " years ago"; //years ago
          }
        }
        else if($interval->m >=1){
          if($interval->d == 0){
            $days = "ago";
          }
          else if($interval->d ==1){
            $days = $interval->d . " day ago";
          }
          else {
            $days = $interval->d . "days ago";
          }

          if($interval->m == 1) {
            $time_message = $interval->m ." month" . $days;
          }
          else {
            $time_message = $interval->m . " months" . $days;
          }
        }
        else if($interval->d >=1){
          if($interval->d == 1){
            $time_message = "Yesterday";
          }
          else {
            $time_message = $interval->d . " days ago";
          }
        }
        else if($interval->h >=1){
          if($interval->h == 1){
            $time_message = $interval->h . " hour ago";
          }
          else {
            $time_message = $interval->h . " hours ago";
          }
        }
        else if($interval->i >=1){
          if($interval->i == 1){
            $time_message = $interval->i . " minute ago";
          }
          else {
            $time_message = $interval->i . " minutes ago";
          }
        }
        else if($interval->s >=1){
          if($interval->s < 1){
            $time_message = "Just now";
          }
          else {
            $time_message = $interval->s . " seconds ago";
          }
        } //end timframe

        $user_obj = new User($connection, $posted_by);
        
        ?>

        <div class="comment-section">
          <a href="<?php echo $posted_by; ?>" target="_parent">
            <img src="<?php echo $user_obj->getProfilePic(); ?>" title="<?php echo $posted_by; ?>"
              style="float:left;" height="30">
          </a>
          <a href="<?php echo $posted_by; ?>" target="_parent">
            <b><?php echo $user_obj->getName(); ?></b>
          </a>
          &nbsp;
          <?php
            echo $time_message . "<br>" . $comment_body;
          ?>
          <hr>
        </div>

        <?php
      }// end while
    }// end if
    else {
      echo "<center><br>No comments to show!</center>";
    }
  ?>

</body>
</html>