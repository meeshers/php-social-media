<?php
  include("includes/header.php");

  if(isset($_POST['post'])){
    $post = new Post($connection, $userLoggedIn);
    $post->submitPost($_POST['post-text'], 'none');
    header("Location: index.php"); //when post is submitted, it will refresh
  }
  //session_destroy();
?>

  <div class="user-details column">
    <a href="<?php echo $userLoggedIn; ?>"><img src="<?php echo $user['profile_pic']; ?>" alt=""></a>

    <div class="user-details-left-right">
      <a href="<?php echo $userLoggedIn; ?>">
      <?php
        echo $user['first_name'] . " " . $user['last_name'] . "<br>";
      ?>
      </a>
      <?php echo "Posts: " . $user['num_posts'] . "<br>";
      echo "Likes " . $user['num_likes'];
      ?>
    </div>
  </div>

  <div class="main-column column">
    <form class="post-form" action="index.php" method="POST">
      <textarea name="post-text" id="post-text" placeholder="What's on your mind?"></textarea>
      <input type="submit" name="post" id="post-button" value="Post">
    </form>
    <hr>

    <div class="posts-area"></div>
    <img id="loading" src="assets/images/loading.gif" alt="loading">

  </div>

  <script>
    $(function(){
      let userLoggedIn = '<?php echo $userLoggedIn; ?>';
      let inProgress=false;

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
          url: "includes/handlers/ajax_load_posts.php",
          type: 'POST',
          data: 'page=' + page + '&userLoggedIn=' + userLoggedIn,
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