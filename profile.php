<?php
include "includes/header.php";
$message_obj = new Message($connect, $userLoggedIn);
if (isset($_GET['profile_username'])) {
 $username = $_GET['profile_username'];
 $user_details_query = mysqli_query($connect, "SELECT * FROM users WHERE username = '$username'");
 $user_array = mysqli_fetch_array($user_details_query);
 //broj prijatelja
 $num_friends = substr_count(($user_array['friend_array']), ",") - 1;
}
if (isset($_POST['remove_friend'])) {
 $user = new User($connect, $_SESSION['log_username']);
 $user->removeFriend($username);
}
if (isset($_POST['add_friend'])) {
 $user = new User($connect, $_SESSION['log_username']);
 $user->sendRequest($username);
}
if (isset($_POST['respond_request'])) {
 header("Location: requests.php");
}

if (isset($_POST['post_message'])) {
 if (isset($_POST['message_body'])) {
  $body = mysqli_real_escape_string($connect, $_POST['message_body']);
  $date = date("Y-m-d H:i:s");
  $message_obj->sendMessage($username, $body, $date);
 }
 /*
 $link = '#profileTabs a[href="#messages_div"]';
 echo "<script>
 $(fuction() {
  $('" . $link . "').tab('show');
 });
 </script>";
 */
}

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
  <!-- broj objava -->
  <p><?php echo "Posts: " . $user_array['num_posts']; ?></p>
  <!-- broj lajkova -->
  <p><?php echo "Likes: " . $user_array['num_likes']; ?></p>
  <!-- broj prijatelja -->
  <p><?php echo "Friends: " . $num_friends; ?></p>
 </div>
 <!-- forma -->
 <form action="<?php echo $username; ?>" method="post">
  <?php
  //ako je akaunt zatvoren
  $profile_user_obj = new User($connect, $username);
  if ($profile_user_obj->isClosed()) {
   header("Location : user_closed.php");
  } else {
   $logged_in_user_obj = new User($connect, $userLoggedIn);
   // da li je korisnik na svom profilu, ako nije prikazi dugme za dodavanje prijatelja
   if ($userLoggedIn != $username) {
    //ako su vec prijatelji prikazujemo da obrise prijatelja
    if ($logged_in_user_obj->isFriend($username)) {
     echo "<input type='submit' name='remove_friend' class='danger' value='Remove friend'></input> <br>";
    }
    //ukoliko je korisnik primio zahtjev za prijateljstvo od nekoga
    else if ($logged_in_user_obj->didReceiveRequest($username)) {
     echo "<input type='submit' name='respond_request' class='warning' value='Respond to request'></input> <br>";
    }
    //ukoliko je korisnik poslao zahtjev za prijateljstvo nekome
    else if ($logged_in_user_obj->didSendRequest($username)) {
     echo "<input type='submit' name='respond_request' class='default' value='Request Sent'></input> <br>";
    } else {
     echo "<input type='submit' name='add_friend' class='success' value='Add friend'></input> <br>";
    }
   }
  }
  ?>
 </form>
 <input type="submit" value="Post Something" class="deep_blue" data-toggle='modal' data-target='#post_form'>
 <?php
 if ($userLoggedIn !== $_GET['profile_username']) {
 ?>
  <div class="profile_info_bottom"><?php echo $logged_in_user_obj->getMutualFriends($_GET['profile_username']) . ' Mutual Friends' ?></div>
 <?php
 }
 ?>
 <!-- forma -->
</div>

<div class="profile_main_column column">
 <!-- TABS -->
 <ul class="nav nav-tabs" role="tablist" id="profileTabs">
  <li role="presentation" class="active"><a href="#newsfeed_div" aria-controls="newsfeed_div" role="tab" data-toggle="tab">Newsfeed</a></li>
  <li role="presentation"><a href="#messages_div" aria-controls="messages_div" role="tab" data-toggle="tab">Messages</a></li>
 </ul>
 <!-- TABS -->
 <!-- ???? -->
 <div class="tab-content">
  <div role="tabpanel" id="newsfeed_div" class="tab-pane fade in active">
   <?php
   $posts_obj = new Post($connect, $userLoggedIn);
   $posts_obj->loadProfilePosts($_REQUEST);
   ?>
  </div>


  <div role="tabpanel" id="messages_div" class="tab-pane fade ">
   <?php
   echo "<h4>You and <a href='" . $username . "'>" . $profile_user_obj->getFirstAndLastName() . "</a></h4><br><hr>";
   ?>
   <div class="loaded_messages" id='scroll_messages'>
    <?php echo $message_obj->getMessages($username); ?>
   </div>
   <div class="message_post">
    <form action="" method="POST">
     <!-- Razlicito zavisno od toga da li salje novu poruku ili prica sa nekim -->
     <textarea name='message_body' id='message_textarea' placeholder='Write your message...'></textarea>
     <input type='submit' name='post_message' class='info' id='message_submit' value='Send'></input>
    </form>
   </div>
   <script>
    let div = document.querySelector("#scroll_messages");
    if (div != null) {
     div.scrollTop = div.scrollHeight;

    }
   </script>
  </div>
 </div>
 <!-- ???? -->
 <br>
</div>
<!-- Button trigger modal -->

<!-- Modal -->
<div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
 <div class="modal-dialog" role="document">
  <div class="modal-content">

   <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Post something</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
     <span aria-hidden="true">&times;</span>
    </button>
   </div>

   <div class="modal-body">
    <p>This will appear on the user's profile page and also their newsfeed for your frineds to see!</p>
    <form action="" class="profile_post" method="POST">
     <div class="form-group">
      <textarea name="post_body" class="form_control" id="" cols="30" rows="10"></textarea>
      <input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>">
      <input type="hidden" name="user_to" value="<?php echo $username; ?>">
     </div>
    </form>
   </div>

   <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary" name="post_button" id='submit_profile_post'>Post</button>
   </div>
  </div>
 </div>
</div>

</body>

</div>

</html>