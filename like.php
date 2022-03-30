<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8">
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <link rel="stylesheet" href="assets/css/style.css">
 <title>Document</title>
</head>

<body>
 <style>
  * {
   font-family: Arial, Helvetica, sans-serif;
  }

  body {
   background-color: #fff;
  }

  form {
   position: absolute;
   top: 0;
  }
 </style>
 <?php
 include "includes/classes/User.php";
 include "includes/classes/Post.php";
 include "includes/classes/Notification.php";
 require_once('config/config.php');
 if (isset($_SESSION['log_username'])) {
  $userLoggedIn = $_SESSION['log_username'];
  $user_details_query = mysqli_query($connect, "SELECT * FROM users WHERE username = '$userLoggedIn'");
  $user = mysqli_fetch_assoc($user_details_query);
 } else {
  header("Location: register.php");
 }
 //get id of post
 if (isset($_GET['post_id'])) {
  $post_id = $_GET['post_id'];
 }
 $get_likes = mysqli_query($connect, "SELECT likes, added_by FROM posts WHERE id = '$post_id'");
 $row = mysqli_fetch_array($get_likes);
 $total_likes = $row['likes'];
 //osoba koja je objavila
 $user_liked = $row['added_by'];
 $user_details_query = mysqli_query($connect, "SELECT * FROM users WHERE username = '$user_liked'");
 $row = mysqli_fetch_array($user_details_query);
 $total_user_likes = $row['num_likes'];
 //like button
 if (isset($_POST['like_button'])) {
  $total_likes = $total_likes + 1;
  //azuriranje tabele objava
  $query = mysqli_query($connect, "UPDATE posts SET likes = '$total_likes' WHERE id = '$post_id'");
  //azuriranje tabele korisnika
  $total_user_likes++;
  $user_likes = mysqli_query($connect, "UPDATE users SET num_likes = '$total_user_likes' WHERE username = '$user_liked'");
  //unesi u tabeli
  $insert_user = mysqli_query($connect, "INSERT INTO likes (username, post_id) VALUES ('$userLoggedIn', '$post_id')");
  //insert notification
  if ($user_liked != $userLoggedIn) {
   $notification = new Notification($connect, $userLoggedIn);
   //returned_id je id objave koja se objavljuje dobijana iz varijable returned_id
   $notification->insertNotification($post_id, $user_liked, 'like');
  }
 }
 //unlike button
 if (isset($_POST['unlike_button'])) {
  $total_likes--;
  //azuriranje tabele objava
  $query = mysqli_query($connect, "UPDATE posts SET likes = '$total_likes' WHERE id = '$post_id'");
  //azuriranje tabele korisnika
  $total_user_likes--;
  $user_likes = mysqli_query($connect, "UPDATE users SET num_likes = '$total_user_likes' WHERE username = '$user_liked'");
  //unesi u tabeli
  $insert_user = mysqli_query($connect, "DELETE FROM likes WHERE username = '$userLoggedIn' AND post_id = '$post_id'");
 }
 //check for previous likes
 $check_query = mysqli_query($connect, "SELECT * FROM likes WHERE username = '$userLoggedIn' AND post_id = '$post_id'");
 $num_rows = mysqli_num_rows($check_query);
 if ($num_rows > 0) {
  echo "
  <form action='like.php?post_id=$post_id' method='post'>
  <input type='submit' class='comment_like' name='unlike_button' value='Unlike'></input>
  <div class='like_value'>
  $total_likes Likes
  </div>
  </form>
  ";
 } else {
  echo "
  <form action='like.php?post_id=$post_id' method='post'>
  <input type='submit' class='comment_like' name='like_button' value='Like'></input>
  <div class='like_value'>
  $total_likes Likes
  </div>
  </form>
  ";
 }
 ?>
</body>

</html>