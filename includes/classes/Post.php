<?php

  class Post {
    private $user_obj;
    private $connection;

    public function __construct($connection, $user){
      $this->connection = $connection;
      $this->user_obj = new User($connection, $user);
    }

    public function submitPost($body, $user_to) {
      $body = strip_tags($body); //remove html tags
      $body = mysqli_real_escape_string($this->connection, $body); //strip quotes, etc to insert into db

      $body = str_replace('\r\n', '\n', $body); //search for any line breaks
      $body = nl2br($body); //new line to line break built in function

      $check_empty = preg_replace('/\s+/', '', $body); //Deletes all spaces

      if($check_empty != "") {
        //Current date and time
        $date_added = date("Y-m-d H:i:s");
        //get username
        $added_by = $this->user_obj->getUsername();
        //if user is not on own profile, user_to is none
        if($user_to == $added_by) {
          $user_to = "none";
        }

        //insert post
        $query = mysqli_query($this->connection, "INSERT INTO posts VALUES(NULL,'$body', '$added_by', '$user_to', '$date_added', 'no', 'no', '0')");
        $returned_id = mysqli_insert_id($this->connection); //returns id of post that was submitted

        //insert notification

        //update post count for user
        $num_posts = $this->user_obj->getNumPosts();
        $num_posts++;
        $update_query = mysqli_query($this->connection, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");
      }
    }

    public function loadPostFriends($data, $limit){
      $page = $data['page'];
      $userLoggedIn = $this->user_obj->getUsername();

      if($page == 1){
        $start = 0; //start at the very first one
      }
      else {
        $start = ($page -1) * $limit; //will start at nth post depending on page
      }

      $str = ""; //string to return
      $data_query = mysqli_query($this->connection, "SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC"); //order by id in descending order

      if(mysqli_num_rows($data_query)>0){
        $num_iteration = 0; //number of results checked
        $count = 1; //how many results loaded  

        while($row = mysqli_fetch_array($data_query)){
          $id = $row['id'];
          $body = $row['body'];
          $added_by = $row['added_by'];
          $date_time = $row['date_added'];

          //prepare user_to string so it can be included if not posted to a user
          if($row['user_to']== 'none') {
            $user_to = "";
          }
          else {
            $user_to_obj = new User($this->connection, $row['user_to']);
            $user_to_name = $user_to_obj->getName();
            $user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";
          }

          //Check if user who posted has a closed account
          $added_by_obj = new User($this->connection, $added_by);
          if($added_by_obj->isClosed()){
            continue;
          }

          $user_logged_obj = new User($this->connection, $userLoggedIn);
          if($user_logged_obj->isFriend($added_by)){
            //number of row to start at
            if($num_iteration++ < $start){
              continue;
            }

            //once ten posts have been loaded, break
            if($count > $limit){
              break;
            }
            else {
              $count++;
            }

            if($userLoggedIn == $added_by){
              $delete_button="<button class='delete_button btn-danger' id='post$id'><i class='fa fa-times'></i></button>";
            }
            else {
              $delete_button="";
            }
            
            $user_details = mysqli_query($this->connection, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
            $user_row=mysqli_fetch_array($user_details);
            $first_name = $user_row['first_name'];
            $last_name = $user_row['last_name'];
            $profile_pic = $user_row['profile_pic'];
            
            ?>

              <script>
              function toggle<?php echo $id; ?>() {
                let target = $(event.target);
                if(!target.is("a")){
                  let element = document.getElementById('toggleComment<?php echo $id; ?>');
                  if(element.style.display == 'block'){
                    element.style.display = 'none';
                  }
                  else{
                    element.style.display = 'block';
                  }
                }
              }
              </script>

            <?php 

              $comments_check = mysqli_query($this->connection, "SELECT * FROM comments WHERE post_id='$id'");
              $comments_num = mysqli_num_rows($comments_check);

            //timeframe
            $date_time_now = date('Y-m-d H:i:s');
            $start_date = new DateTime($date_time); //time of post
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
            }
            $str .= "<div class='status-post' onClick='javascript:toggle$id()'>
                      <div class='post-profile'>
                        <img src='$profile_pic' width='50'>
                      </div>
                      <div class='posted-by' style='color:#ACACAC;'>
                        <a href='$added_by'>$first_name $last_name</a> $user_to &nbsp; $time_message
                        $delete_button
                      </div>
                      <div id='post-body'>
                        $body
                        <br>
                        <br>
                      </div>
                      <div class='newsfeed-options'>
                        Comments($comments_num)&nbsp; 
                        <iframe src='like.php?post_id=$id' scrolling='no'></iframe>
                      </div>

                    </div>

                    <div class='postComment' id='toggleComment$id' style='display:none;'>
                      <iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
                    </div>
                    <hr>";
           }

        ?>
          <script>
            $(document).ready(function(){
              $('#post<?php echo $id; ?>').on('click', function(){
                bootbox.confirm("Are you sure you want to delete this post?", function(result){
                  $.post("includes/forms/delete_post.php?post_id=<?php echo $id; ?>", {result: result});

                  if(result){
                    location.reload();
                  }

                });
              });
            })
          </script>
        <?php
          } //end while
        if($count > $limit){
          $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
                  <input type='hidden' class='noPosts' value='false'>";
        }
        else {
          $str .= "<input type='hidden' class='noPosts' value='true'>
                  <p style='text-align:center;'>No more posts to show!</p>";
        }

      } //endif
      echo $str;
    }

    public function loadProfilePosts($data, $limit){
      $page = $data['page'];
      $profileUser = $data['profileUsername'];
      $userLoggedIn = $this->user_obj->getUsername();

      if($page == 1){
        $start = 0; //start at the very first one
      }
      else {
        $start = ($page -1) * $limit; //will start at nth post depending on page
      }

      $str = ""; //string to return
      $data_query = mysqli_query($this->connection, "SELECT * FROM posts WHERE deleted='no' AND ((added_by='$profileUser' AND user_to='none') OR user_to='$profileUser') ORDER BY id DESC"); //order by id in descending order

      if(mysqli_num_rows($data_query)>0){
        $num_iteration = 0; //number of results checked
        $count = 1; //how many results loaded  

        while($row = mysqli_fetch_array($data_query)){
          $id = $row['id'];
          $body = $row['body'];
          $added_by = $row['added_by'];
          $date_time = $row['date_added'];

          $user_logged_obj = new User($this->connection, $userLoggedIn);
          
            //number of row to start at
            if($num_iteration++ < $start){
              continue;
            }

            //once ten posts have been loaded, break
            if($count > $limit){
              break;
            }
            else {
              $count++;
            }

            if($userLoggedIn == $added_by){
              $delete_button="<button class='delete_button btn-danger' id='post$id'><i class='fa fa-times'></i></button>";
            }
            else {
              $delete_button="";
            }
            
            $user_details = mysqli_query($this->connection, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
            $user_row=mysqli_fetch_array($user_details);
            $first_name = $user_row['first_name'];
            $last_name = $user_row['last_name'];
            $profile_pic = $user_row['profile_pic'];
            
            ?>

              <script>
              function toggle<?php echo $id; ?>() {
                let target = $(event.target);
                if(!target.is("a")){
                  let element = document.getElementById('toggleComment<?php echo $id; ?>');
                  if(element.style.display == 'block'){
                    element.style.display = 'none';
                  }
                  else{
                    element.style.display = 'block';
                  }
                }
              }
              </script>

            <?php 

              $comments_check = mysqli_query($this->connection, "SELECT * FROM comments WHERE post_id='$id'");
              $comments_num = mysqli_num_rows($comments_check);

            //timeframe
            $date_time_now = date('Y-m-d H:i:s');
            $start_date = new DateTime($date_time); //time of post
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
            }
            $str .= "<div class='status-post' onClick='javascript:toggle$id()'>
                      <div class='post-profile'>
                        <img src='$profile_pic' width='50'>
                      </div>
                      <div class='posted-by' style='color:#ACACAC;'>
                        <a href='$added_by'>$first_name $last_name</a> &nbsp; $time_message
                        $delete_button
                      </div>
                      <div id='post-body'>
                        $body
                        <br>
                        <br>
                      </div>
                      <div class='newsfeed-options'>
                        Comments($comments_num)&nbsp; 
                        <iframe src='like.php?post_id=$id' scrolling='no'></iframe>
                      </div>

                    </div>

                    <div class='postComment' id='toggleComment$id' style='display:none;'>
                      <iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
                    </div>
                    <hr>";
           

        ?>
          <script>
            $(document).ready(function(){
              $('#post<?php echo $id; ?>').on('click', function(){
                bootbox.confirm("Are you sure you want to delete this post?", function(result){
                  $.post("includes/forms/delete_post.php?post_id=<?php echo $id; ?>", {result: result});

                  if(result){
                    location.reload();
                  }

                });
              });
            })
          </script>
        <?php
          } //end while
        if($count > $limit){
          $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
                  <input type='hidden' class='noPosts' value='false'>";
        }
        else {
          $str .= "<input type='hidden' class='noPosts' value='true'>
                  <p style='text-align:center;'>No more posts to show!</p>";
        }

      } //endif
      echo $str;
    }
  }

?>