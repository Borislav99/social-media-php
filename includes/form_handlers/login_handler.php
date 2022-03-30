<?php
if (isset($_POST['log_button'])) {
 //sanitaze
 $email = filter_var($_POST['log_email'], FILTER_SANITIZE_EMAIL);
 //store in session in case of wrong email
 $_SESSION['log_email'] = $email;
 //md5
 $password = md5($_POST['log_password']);
 //
 $check_database_query = mysqli_query($connect, "SELECT * FROM users WHERE email = '$email' AND password = '$password'");
 $check_login_query = mysqli_num_rows($check_database_query);
 if ($check_login_query == 1) {
  $row = mysqli_fetch_array($check_database_query);
  $username = $row['username'];
  $_SESSION['log_username'] = $username;
  $email = $row['email'];
  $_SESSION['log_email'] = $email;
  header("Location: index.php");
  //reopen an account
  $user_closed_query = mysqli_query($connect, "SELECT * FROM users WHERE email = '$email' AND user_closed = 'yes'");
  if (mysqli_num_rows($user_closed_query) == 1) {
   $reopen_account = mysqli_query($connect, "UPDATE users SET user_closed = 'no' WHERE email = '$email'");
  }
  exit();
 } else {
  array_push($error_array, "Email or password was incorrect <br>");
 }
}
