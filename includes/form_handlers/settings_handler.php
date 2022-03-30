<?php
//ako pritisne submit dugme za korisnicke detalje
if (isset($_POST['update_details'])) {
 $first_name = $_POST['first_name'];
 $last_name = $_POST['last_name'];
 $email = $_POST['email'];
 // provjera email-a, odnosno da li je email vec koristen
 $email_check = mysqli_query($connect, "SELECT * FROM users WHERE email = '$email'");
 $row = mysqli_fetch_array($email_check);
 if (isset($row['username'])) {
  $matched_user = $row['username'];
 } else {
  $matched_user = "";
 }
 if ($matched_user == "" || $matched_user == $userLoggedIn) {
  $query = mysqli_query($connect, "UPDATE users SET first_name = '$first_name', last_name = '$last_name', email = '$email' WHERE username = '$userLoggedIn'");
  $message = "Details updated!<br><br>";
 } else {
  $message = "Email already in use<br><br>";
 }
} else {
 $message = "";
}
//ako pritisne dugme sifre
if (isset($_POST['update_password'])) {
 $old_password = strip_tags($_POST['old_password']);
 $new_password_1 = strip_tags($_POST['new_password_1']);
 $new_password_2 = strip_tags($_POST['new_password_2']);
 $password_query = mysqli_query($connect, "SELECT password FROM users WHERE username = '$userLoggedIn'");
 $row = mysqli_fetch_array($password_query);
 //lozinka sa baze
 $db_password = $row['password'];
 //uporedi dve lozinke
 if (md5($old_password) == $db_password) {
  //ako se podudaraju
  if ($new_password_1 == $new_password_2) {
   //ako jeste
   if (strlen($new_password_1) <= 4) {
    $password_message = "Sorry your password must be greater than 4 characters";
   } else {
    $new_password_md5 = md5($new_password_1);
    $password_query = mysqli_query($connect, "UPDATE users SET password = '$new_password_md5' WHERE username = '$userLoggedIn'");
    $password_message = "Password has been changed";
   }
  } else {
   $password_message = "Your two new password's need to match<br><br>";
  }
 } else {
  $password_message = "Your old password doesn't match";
 }
} else {
 $password_message = "";
}
//ako pritisne dugme za zatvaranje profile
if (isset($_POST['close_account'])) {
 header("Location: close_account.php");
}
