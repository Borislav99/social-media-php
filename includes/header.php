<?php
require_once('config/config.php');
include "classes/User.php";
include "classes/Post.php";
include "classes/Message.php";
include "classes/Notification.php";
if (isset($_SESSION['log_username'])) {
  $userLoggedIn = $_SESSION['log_username'];
  $user_details_query = mysqli_query($connect, "SELECT * FROM users WHERE username = '$userLoggedIn'");
  $user = mysqli_fetch_assoc($user_details_query);
} else {
  header("Location: register.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=`device-width`, initial-scale=1.0">
  <link rel="shortcut icon" href="favicon.png" type="image/x-icon">
  <!-- BOOTSTRAP -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="assets/js/bootbox.min.js"></script>
  <script src="assets/js/jquery.Jcrop.js"></script>
  <script src="assets/js/jcrop_bits.js"></script>
  <link rel="stylesheet" href="assets/css/jquery.Jcrop.css" type="text/css" />
  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="https://kit.fontawesome.com/e2ef9baa8a.js" crossorigin="anonymous"></script>
  <script src="assets/js/social_media.js"></script>

  <title>Social Media</title>
</head>

<body>
  <div class="top_bar">
    <div class="logo">
      <a href="index.php">Apeiron Book</a>
    </div>
    <!-- SEARCH -->
    <div class="search">
      <form name='search_form' action="search.php" method="get">
        <input type="text" name="q" placeholder="Search..." autocomplete="off" id="search_text_input" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')">
        <!-- submit btn -->
        <div class="button_holder">
          <img src="assets/images/icons/magnifying_glass.png" alt="">
        </div>
        <!-- submit btn -->
      </form>
      <div class="search_results">

      </div>
      <div class="search_results_footer_empty">

      </div>
    </div>
    <!-- SEARCH -->
    <nav>
      <?php
      echo "<a href='#'>" . $user['first_name'] . "</a>";
      ?>
      <?php
      //poruke
      $messages = new Message($connect, $userLoggedIn);
      $num_messages = $messages->getUnreadNumber();
      //notifikacije
      $notifications = new Notification($connect, $userLoggedIn);
      $num_notifications = $notifications->getUnreadNumber();
      //zahtjevi za prijateljstvo
      $user_obj = new User($connect, $userLoggedIn);
      $num_requests = $user_obj->getNumberOfFriendRequests();
      ?>
      <a href="index.php"><i class="fa fa-home fa-lg"></i></a>
      <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'message')">
        <i class="fa fa-envelope fa-lg"></i>
        <?php if ($num_messages > 0) {
          echo "<span class='notification_badge' id='unread_message'>" . $num_messages . "</span>";
        } ?>
      </a>
      <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')"><i class="fa fa-bell fa-lg">
          <?php if ($num_notifications > 0) {
            echo "<span class='notification_badge' id='unread_notification'>" . $num_notifications . "</span>";
          } ?>
        </i></a>
      <a href="requests.php"><i class="fa fa-users fa-lg">
          <?php if ($num_requests > 0) {
            echo "<span class='notification_badge' id='unread_notification'>" . $num_requests . "</span>";
          } ?>
        </i></a>
      <a href="settings.php"><i class="fa fa-cog fa-lg"></i></a>
      <a href="includes/handlers/logout.php"><i class="fa fa-sign-out fa-lg"></i></a>
    </nav>
    <!-- DROPDOWN -->
    <div class="dropdown_data_window" style="height: 0px; border:none;">
      <input type="hidden" name="" id="dropdown_data_type" value="">

    </div>
    <!-- DROPDOWN -->
  </div>
  <div class="wrapper">