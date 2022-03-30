<?php
include "includes/header.php";
if (isset($_POST['post'])) {
 $post = new Post($connect, $userLoggedIn);
 $post->submitPost($_POST['post_text'], 'none');
 header("Location: index.php");
}
?>
<div class="user_details column">
 <a href="#">
  <img src="<?php echo $user['profile_pic']; ?>" alt="">
 </a>
 <div class="user_details_left_right">
  <a href="#">
   <?php
   $usr = new User($connect, $user['username']);
   echo $usr->getFirstAndLastName($usr);
   ?>
  </a>
  <br>
  <?php
  echo "Posts: " . $user['num_posts'] . "<br>";
  echo "Likes: " . $user['num_likes'];
  ?>
 </div>
</div>

<div class="main_column column">
 <form class="post_form" action="index.php" method="post">
  <textarea name="post_text" id="post_text" cols="30" rows="10" placeholder="Got something to say?"></textarea>
  <input type="submit" name="post" id="post_button" value="Post">
  <hr>
 </form>

 <?php
 $posts_obj = new Post($connect, $userLoggedIn);
 $posts_obj->loadPostsFriends($_REQUEST);
 ?>
</div>
</body>
</div>

</html>