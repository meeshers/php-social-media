//ajax call for submitting profile post
$(document).ready(function(){
  //button for profile post
  $('#submit_prof_post').click(function(){
    $.ajax({
      type: "POST",
      url: "includes/handlers/ajax_profile_post.php",
      data: $('form.profile_post').serialize(),
      success: function(msg) {
        $("#post_form").modal('hide');
        location.reload();
      },
      error: function(){
        alert('Failure');
      }
    });
  });
});