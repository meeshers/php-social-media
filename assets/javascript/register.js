$(document).ready(function(){
  //on click sign up, hide login and show register form
  $("#signup").click(function(){
    $(".first").slideUp("medium", function(){
      $(".second").slideDown("medium");
    })
  });

  $("#sign-in").click(function(){
    $(".second").slideUp("medium", function(){
      $(".first").slideDown("medium");
    })
  });
});