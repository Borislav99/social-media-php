<!DOCTYPE html>
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
?>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
  <style type="text/css">
    * {
      font-size: 12px;
      font-family: Arial, Helvetica, sans-serif;

    }
  </style>
  <script>
    function toggle() {
      var element = document.getElementById("comment_section");
      if (element.style.display == "block") {
        element.style.display = "none";
      } else {
        element.style.display = "block";
      }
    }
  </script>
  <?php
  //get id of post
  if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
  }
  $user_query = mysqli_query($connect, "SELECT added_by, user_to FROM posts WHERE id = '$post_id'");
  $row = mysqli_fetch_array($user_query);
  $posted_to = $row['added_by'];
  //kome je postavljeno
  $user_to = $row['user_to'];
  if (isset($_POST['postComment' . $post_id])) {
    $post_body = $_POST['post_body'];
    $post_body = mysqli_real_escape_string($connect, $post_body);
    $date_time_now = date("Y-m-d H:i:s");
    $insert_post = mysqli_query($connect, "INSERT INTO comments (post_body, posted_by, posted_to, date_added, removed, post_id) VALUES ('$post_body', '$userLoggedIn', '$posted_to', '$date_time_now', 'no', '$post_id')");

    //ako stavljamo na tudji profil
    if ($posted_to != $userLoggedIn) {
      $notification = new Notification($connect, $userLoggedIn);
      $notification->insertNotification($post_id, $posted_to, 'comment');
    }
    //ako je profile post, ali ne postavljamo na nas profile 
    if ($user_to != 'none' && $user_to != $userLoggedIn) {
      $notification = new Notification($connect, $userLoggedIn);
      $notification->insertNotification($post_id, $user_to, 'profile_comment');
    }
    //ako je neko komentarisao objavu, kad pristigne novi komentar obavijesti ih
    $get_commentors = mysqli_query($connect, "SELECT * FROM comments WHERE post_id='$post_id'");
    $notified_users = [];
    while ($row = mysqli_fetch_array($get_commentors)) {
      //ako osoba koja je u petlji nije osoba koja je napravila originalnu objavu necemo im dati notifikaciju i ako je osoba u petlji ona koja nije vlasnik objave necemo joj poslati notifikaciju
      //ne saljemo sebi notifikaciju kad komentarisemo
      if ($row['posted_by'] != $posted_to && $row['posted_by'] != $user_to && $row['posted_by'] != $userLoggedIn && !in_array($row['posted_by'], $notified_users)) {
        $notification = new Notification($connect, $userLoggedIn);
        $notification->insertNotification($post_id, $row['posted_by'], 'comment_non_owner');
        array_push($notified_users, $row['posted_by']);
      }
    }


    echo "<p>Comment Posted!</p>";
  }
  ?>
  <form id="comment_form" name="postComment<?php echo $post_id; ?>" action="comment_frame.php?post_id=<?php echo $post_id; ?>" method="POST">
    <textarea name="post_body" id=""></textarea>
    <input name="postComment<?php echo $post_id; ?>" type="submit" value="Submit">
  </form>
  <?php
  $get_comments = mysqli_query($connect, "SELECT * FROM comments WHERE post_id = '$post_id' ORDER BY id ASC");
  $count = mysqli_num_rows($get_comments);
  if ($count != 0) {
    while ($comment = mysqli_fetch_array($get_comments)) {
      //get vars
      $comment_body = $comment['post_body'];
      $posted_to = $comment['posted_to'];
      $posted_by = $comment['posted_by'];
      $date_added = $comment['date_added'];
      $removed = $comment['removed'];
      //get vars
      //dobijanje vremenea
      $date_time_now = date("Y-m-d H:i:s");
      //vrijeme objave
      $start_date = new DateTime($date_added);
      //trenutno vrijeme
      $end_date = new DateTime($date_time_now);
      //razlika medju vremenima
      $interval = $start_date->diff($end_date);
      if ($interval->y >= 1) {
        if ($interval == 1) {
          $time_message = " year old";
        } else {
          $time_message = $interval->y . " years ago";
        }
      } else if ($interval->m >= 1) {
        if ($interval->d == 0) {
          $days = " ago";
        } else if ($interval->d == 1) {
          $days = $interval->d . " day ago";
        } else {
          $days = $interval->d . " days ago";
        }
        if ($interval->m == 1) {
          $time_message = $interval->m . " month" . $days;
        } else {
          $time_message = $interval->m . " months" . $days;
        }
      } else if ($interval->d >= 1) {
        if ($interval->d == 1) {
          $time_message = "Yesterday";
        } else {
          $time_message = $interval->d . " days ago";
        }
      } else if ($interval->h >= 1) {
        if ($interval->h == 1) {
          $time_message = " hour ago";
        } else {
          $time_message = $interval->h . " hours ago";
        }
      } else if ($interval->i >= 1) {
        if ($interval->i == 1) {
          $time_message = " minute ago";
        } else {
          $time_message = $interval->i . " minutes ago";
        }
      } else if ($interval->s < 30) {
        $time_message = " now";
      }
      //kraj dobijanje vremena
      $user_obj = new User($connect, $posted_by);
  ?>
      <div class="comment_section" id="comment_section">
        <a target="_parent" href="<?php echo $posted_by; ?>">
          <img src="<?php echo $user_obj->getProfilePic(); ?>" title="<?php echo $posted_by ?>" style="float:left" height="30" alt="">
        </a>
        <a target="_parent" href="<?php echo $posted_by; ?>">
          <?php
          echo $user_obj->getFirstAndLastName();
          ?></a>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $time_message . "<br>" . $comment_body ?>
        <hr>
      </div>
  <?php
    }
  } else {
    echo "<center><br><br>No Comments to show!</center>";
  }
  ?>

</body>

</html>