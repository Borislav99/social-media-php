<?php
include "includes/header.php";
//get id of the post
if (isset($_GET['id'])) {
 $id = $_GET['id'];
} else {
 $id = 0;
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
<div id="main_column" class="main_column column">
 <div class="posts_area">
  <?php
  $post = new Post($connect, $userLoggedIn);
  $post->getSinglePost($id);
  ?>
 </div>
</div>