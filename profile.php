<?php
  include("includes/header.php");

  if(isset($_GET['profile_username'])){
    $username = $_GET['profile_username'];
    $user_details = mysqli_query($connection, "SELECT * FROM users WHERE username='$username'");
    $user_array = mysqli_fetch_array($user_details);

    $num_friends = (substr_count($user_array['friend_array'], ","))-1;
  }

  if(isset($_POST['remove_friend'])){
    $user = new User($connection, $userLoggedIn);
    $user->removeFriend($username);
  }

  if(isset($_POST['add_friend'])){
    $user = new User($connection, $userLoggedIn);
    $user->friendRequest($username);
  }

  if(isset($_POST['respond_request'])){
    header("Location: requests.php");
  }
  //session_destroy();
?>

  <style>
    .wrapper {
      margin-left: 0px;
      padding-left: 0px;
    }
  </style>

  <div class="profile_left">
    <img src="<?php echo $user_array['profile_pic']; ?>" alt="">
    <div class="profile_info">
      <p>
        <?php echo "Posts: " . $user_array['num_posts']; ?>
      </p>
      <p>
        <?php echo "Likes: " . $user_array['num_likes']; ?>
      </p>
      <p>
        <?php echo "Friends: " . $num_friends; ?>
      </p>
    </div>

    <form action="<?php echo $username; ?>" method="POST">
      <!-- check if user is closed -->
      <?php 
      $profile_user = new User($connection, $username);
      if($profile_user->isClosed()){
        header("Location: user_closed.php");
      }

      $logged_in_user = new User($connection, $userLoggedIn);

      //if not on same profile
      if($userLoggedIn != $username){
        if($logged_in_user->isFriend($username)){
          echo '<input type="submit" name="remove_friend" class="danger" value="Remove Friend"><br>';
        } else if($logged_in_user->receiveRequest($username)) {
          echo '<input type="submit" name="respond_request" class="warning" value="Respond to Request"><br>';
        } else if($logged_in_user->sendRequest($username)) {
          echo '<input type="submit" name="" class="default" value="Request Sent!"><br>';
        } else {
          echo '<input type="submit" name="add_friend" class="success" value="Add Friend"><br>';
        }
      }

      ?>
    </form>
    <!-- Button trigger modal -->
    <input type="submit" class="deep_blue" data-toggle="modal" data-target="#post_form" value="Post something">
  </div>

  <div class="profile-main-column column">
    <div class="posts-area"></div>
    
  </div>

  <!-- Modal -->
  <div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="postModalLabel">Post something!</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <p>This will appear on the user's profile page and newfeed.</p>
          <form class="profile_post" action="" method="POST">
            <div class="form-group">
              <textarea name="post-body" id="form-control"></textarea>
              <input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>">
              <input type="hidden" name="user_to" value="<?php echo $username; ?>">
            </div>
          </form>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" name="post_button" id="submit_prof_post">Post!</button>
        </div>
      </div>
    </div>
  </div>
  
  <script>
    $(function(){
      let userLoggedIn = '<?php echo $userLoggedIn; ?>';
      let inProgress=false;
      let profileUsername='<?php echo $username; ?>';

      loadPosts(); //loads first posts

      $(window).scroll(function() {
        let bottomElement = $('.status-post').last();
        let noPosts = $('.posts-area').find('.noPosts').val();

        if(isElementInView(bottomElement[0]) && noPosts =='false'){
          loadPosts();
        }
      });

      function loadPosts() {
        if(inProgress){
          return;
        }
        inProgress=true;
        $('#loading').show();
        let page = $('.posts-area').find('.nextPage').val() || 1;

        $.ajax({
          url: "includes/handlers/ajax_load_profile.php",
          type: 'POST',
          data: 'page=' + page + '&userLoggedIn=' + userLoggedIn + "&profileUsername=" + profileUsername,
          cache: false,

          success: function(res){
            $('.posts-area').find('.nextPage').remove();
            $('.posts-area').find('.noPosts').remove();
            $('.posts-area').find('.noPostsText').remove();

            $('#loading').hide();
            $('.posts-area').append(res);

            inProgress=false;
          }
        });
      }

      function isElementInView(el){
        let rect = el.getBoundingClientRect();

        return(
          rect.top >= 0 &&
          rect.left >= 0 &&
          rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
          rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
      }
    });
  </script>

<!-- closing tag for wrapper div in header -->
</div>

</body>
</html>