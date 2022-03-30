<?php
include "includes/header.php";
$message_obj = new Message($connect, $userLoggedIn);
//cuvamo korisnicko ime korisnika kojem zelimo poslati poruku
if (isset($_GET['u'])) {
 $user_to = $_GET['u'];
} else {
 $user_to = $message_obj->getMostRecentUser();
 if ($user_to == false) {
  $user_to = 'new';
 }
}
//ako korisnik pokusava da posalje novu poruku napravi objekat od korisnika kome se salje poruka
if ($user_to != "new") {
 $user_to_obj = new User($connect, $user_to);
 if (isset($_POST['post_message'])) {
  //da li ima teksta
  if (isset($_POST['message_body'])) {
   $body = mysqli_real_escape_string($connect, $_POST['message_body']);
   $date = date('Y-m-d H:i:s');
   $message_obj->sendMessage($user_to, $body, $date);
  }
 }
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
<div class="main_column column" id="main_column">
 <?php
 if ($user_to !== 'new') {
  echo "<h4>You and <a href='$user_to'>" . $user_to_obj->getFirstAndLastName() . "</a></h4>";
 ?>
  <div class="loaded_messages" id='scroll_messages'>
   <?php echo $message_obj->getMessages($user_to); ?>
  </div>
 <?php
 } else {
  echo "<h4>New Message</h4>";
 }
 ?>
 <div class="message_post">
  <form action="" method="POST">
   <!-- Razlicito zavisno od toga da li salje novu poruku ili prica sa nekim -->
   <?php
   if ($user_to == 'new') {
    echo "Select the friend you would like to message<br><br>";
   ?>
    To: <input type='text' name='q' placeholder='Name' autocomplete='off' id='search_text_input' onkeyup='getUsers(this.value, "<?php echo $userLoggedIn; ?>")'>;
   <?php
    echo "<div class='results'></div>";
   } else {
    echo "<textarea name='message_body' id='message_textarea' placeholder='Write your message...'></textarea>";
    echo "<input type='submit' name='post_message' class='info' id='message_submit' value='Send'></input>";
   }
   ?>
  </form>
 </div>
 <script>
  let div = document.querySelector("#scroll_messages");
  if (div != null) {
   div.scrollTop = div.scrollHeight;

  }
 </script>
</div>
<div class="user_details column" id="conversations">
 <h4>Conversations</h4>
 <div class="loaded_conversations">
  <?php echo $message_obj->getConvos();
  ?>
 </div>
 <br>
 <a href="messages.php?u=new">New messages</a>
</div>