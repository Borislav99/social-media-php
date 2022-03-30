<?php
include "includes/header.php";
include "includes/form_handlers/settings_handler.php";
?>
<div class="main_column column">
 <h4>Account settings</h4>
 <?php
 echo "<img id='small_profile_pic' src=" . $user['profile_pic'] . ">";
 ?>
 <br>
 <!-- Nova slika -->
 <a href="upload.php">Upload new profile picture</a>
 <br>
 <br>
 <br>
 <!-- Nova slika -->

 <!-- FORMA ZA MOFIFIKACIJU -->
 <?php
 $user_data_query = mysqli_query($connect, "SELECT first_name, last_name, email FROM users WHERE username = '$userLoggedIn'");
 $row = mysqli_fetch_array($user_data_query);
 $first_name = $row['first_name'];
 $last_name = $row['last_name'];
 $email = $row['email'];
 ?>
 Modify your profile
 <form action="settings.php" method="POST">
  First name: <input id="settings_input" type='text' name="first_name" value="<?php echo $first_name; ?>">
  <br>
  Last name: <input id="settings_input" type='text' name="last_name" value="<?php echo $last_name; ?>">
  <br>
  Email: <input type='email' id="settings_input" name="email" value="<?php echo $email; ?>">
  <br>
  <?php echo $message; ?>
  <input type="submit" name="update_details" id="save_details" value="Update Details" class="info settings_submit">
 </form>

 <!-- FORMA ZA LOZINKU -->
 <h4>Change password</h4>
 <form action="settings.php" method="POST">
  Old Password: <input id="settings_input" type='password' name="old_password">
  <br>
  New password: <input id="settings_input" type='password' name="new_password_1">
  <br>
  New password again: <input id="settings_input" type='password' name="new_password_2">
  <br>
  <?php echo $password_message; ?>
  <br>
  <input type="submit" name="update_password" value="Update Password" class="info settings_submit">
 </form>
 <!-- FORMA ZA LOZINKU -->
 <br>
 <!-- ZATVORI ACCOUNT -->
 <form action="settings.php" method="POST">
  <input type="submit" name="close_account" value="Close Account" class="danger settings_submit">
 </form>
 <!-- ZATVORI ACCOUNT -->

 <!-- FORMA ZA MOFIFIKACIJU -->
</div>