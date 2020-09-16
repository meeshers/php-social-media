<?php
  class User {
    private $user;
    private $connection;

    public function __construct($connection, $user){
      $this->connection = $connection;
      $user_details = mysqli_query($connection, "SELECT * FROM users WHERE username='$user'");
      $this->user = mysqli_fetch_array($user_details);
    }

    public function getName() {
      $username = $this->user['username'];
      $query = mysqli_query($this->connection, "SELECT first_name, last_name FROM users WHERE username='$username'");
      $row = mysqli_fetch_array($query);
      return $row['first_name'] . " " . $row['last_name'];
    }

    public function getProfilePic() {
      $username = $this->user['username'];
      $query = mysqli_query($this->connection, "SELECT profile_pic FROM users WHERE username='$username'");
      $row = mysqli_fetch_array($query);
      return $row['profile_pic'];
    }

    public function getUsername() {
      return $this->user['username'];
    }

    public function getNumPosts(){
      $username = $this->user['username'];
      $query = mysqli_query($this->connection, "SELECT num_posts FROM users WHERE username='$username'");
      $row = mysqli_fetch_array($query);
      return $row['num_posts'];
    }

    public function isClosed(){
      $username = $this->user['username'];
      $query = mysqli_query($this->connection, "SELECT user_closed FROM users WHERE username='$username'");
      $row=mysqli_fetch_array($query);
      if($row['user_closed']=='yes'){
        return true;
      }
      else{
        return false;
      }
    }
    
    public function isFriend($username_check){
      $usernameComma = "," . $username_check . ",";

      //strstr checks if str is inside another str
      if(strstr($this->user['friend_array'], $usernameComma) || $username_check == $this->user['username']){
        return true;
      }
      else{
        return false;
      }
    }

    public function receiveRequest($user_from){
      $user_to = $this->user['username'];
      $check_request = mysqli_query($this->connection, "SELECT * FROM friend_requests WHERE user_to = '$user_to' AND user_from='$user_from'");
      if(mysqli_num_rows($check_request)> 0){
        return true;
      } else {
        return false;
      }
    }

    public function sendRequest($user_to){
      $user_from = $this->user['username'];
      $check_request = mysqli_query($this->connection, "SELECT * FROM friend_requests WHERE user_to = '$user_to' AND user_from='$user_from'");
      if(mysqli_num_rows($check_request)> 0){
        return true;
      } else {
        return false;
      }
    }

    public function removeFriend($remove_user){
      $logged_in_user = $this->user['username'];
      $query = mysqli_query($this->connection, "SELECT friend_array FROM users WHERE username='$remove_user'");
      $row = mysqli_fetch_array($query);
      $friend_array_username = $row['friend_array'];

      //update table
      $new_friend_array = str_replace($remove_user . ",", "", $this->user['friend_array']);
      $remove_friend = mysqli_query($this->connection, "UPDATE users SET friend_array='$new_friend_array' WHERE username='$logged_in_user'");

      // have to update tables for both users
      $new_friend_array = str_replace($this->user['username'] . ",", "", $friend_array_username);
      $remove_friend = mysqli_query($this->connection, "UPDATE users SET friend_array='$new_friend_array' WHERE username='$remove_user'");
    }

    public function friendRequest($user_to){
      $user_from = $this->user['username'];
      $query = mysqli_query($this->connection, "INSERT INTO friend_requests VALUES(NULL, '$user_to', '$user_from')");
    }

    public function getFriendArray() {
      $username = $this->user['username'];
      $query = mysqli_query($this->connection, "SELECT friend_array FROM users WHERE username='$username'");
      $row = mysqli_fetch_array($query);
      return $row['friend_array'];
    }
  }

?>