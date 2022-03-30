<?php
/* ---------- VARIABLES ----------*/
$fname = ""; //first name
$lname = ""; //last name
$em = ""; //email
$em2 = ""; //email2
$password = ""; //pass
$password2 = ""; //passw
$date = ""; //date
$error_array = []; //error_array
/* ---------- GET VALUES ----------*/
if (isset($_POST['register_button'])) {
 //First name
 $fname = strip_tags($_POST['reg_fname']);
 $fname = str_replace(" ", '', $fname);
 $fname = ucfirst(strtolower($fname));
 $_SESSION['reg_fname'] = $fname; //first name into session

 //Last name
 $lname = strip_tags($_POST['reg_lname']);
 $lname = str_replace(" ", '', $lname);
 $lname = ucfirst(strtolower($lname));
 $_SESSION['reg_lname'] = $lname; //last name into session

 //Email
 $em = strip_tags($_POST['reg_email']);
 $em = str_replace(" ", '', $em);
 $em = ucfirst(strtolower($em));
 $_SESSION['reg_em'] = $em; //email into session


 //Email 2
 $em2 = strip_tags($_POST['reg_email2']);
 $em2 = str_replace(" ", '', $em2);
 $em2 = ucfirst(strtolower($em2));
 $_SESSION['reg_em2'] = $em2; //email2 into session


 //Password
 $password = strip_tags($_POST['reg_password']);
 $_SESSION['reg_password'] = $password; //password into session
 $password2 = strip_tags($_POST['reg_password2']);
 $_SESSION['reg_password2'] = $password2; //password into session
 //Date
 $date = date('Y-m-d');

 /*----- VALIDATION ----- */
 //Email
 if ($em == $em2) {
  //check if email is in valid format
  if (filter_var($em, FILTER_VALIDATE_EMAIL)) {
   $em = filter_var($em, FILTER_VALIDATE_EMAIL);
   //check if email exists
   $e_check = mysqli_query($connect, "SELECT email FROM users WHERE email = '$em'");
   //count the rows
   $num_rows = mysqli_num_rows($e_check);
   if ($num_rows > 0) {
    array_push($error_array, "Email already in use <br>");
   };
  } else {
   array_push($error_array, "Invalid email format <br>");
  }
 } else {
  array_push($error_array, "Email's don't match <br>");
 }

 //First name
 if (strlen($fname) > 25 || strlen($fname) < 2) {
  array_push($error_array, "Your first name must be between 2 and 25 characters <br>");
 }
 //Last name
 if (strlen($lname) > 25 || strlen($lname) < 2) {
  array_push($error_array, "Your last name must be between 2 and 25 characters <br>");
 }
 //Password
 if ($password != $password2) {
  array_push($error_array, "Your password's don't match <br>");
 } else {
  if (preg_match('/[^A-Za-z0-9]/', $password)) {
   array_push($error_array, "Your password can only contain english characters or numbers <br>");
  }
 }
 if (strlen($password) > 30 || strlen($password) < 5) {
  array_push($error_array, "Your password must be between 5 and 30 characters <br>");
 }
 //Send to DB
 if (empty($error_array)) {
  //encrypt pass
  $password = md5($password);
  //generate username
  $username = strtolower($fname . "_" . $lname);
  $check_username_query = mysqli_query($connect, "SELECT username FROM users WHERE username = '$username'");
  $i = 0;
  //if username exist do while loop
  while (mysqli_num_rows($check_username_query) != 0) {
   $i++;
   $username = $username . "_" . $i;
   $check_username_query = mysqli_query($connect, "SELECT username FROM users WHERE username = '$username'");
  }
  //random pic
  $rand = rand(1, 2);
  if ($rand == 1) {
   $profile_pic = "assets/images/profile_pics/defaults/head_deep_blue.png";
  } else if ($rand == 2) {
   $profile_pic = "assets/images/profile_pics/defaults/head_emerald.png";
  }
  //send values
  $date_added = date('Y-m-d H:i:s');
  $query = mysqli_query($connect, "INSERT INTO users (first_name, last_name, username, email, password, signup_date, profile_pic, num_posts, num_likes, user_closed, friend_array) VALUES('$fname', '$lname', '$username', '$em', '$password', '$date_added', '$profile_pic', '0', '0', 'no', ',')");
  array_push($error_array, "<span style='color:#14C800'>You have been succesfully registred, go ahead and login!</span> <br>");
  //clear session vars
  $_SESSION['reg_fname'] = "";
  $_SESSION['reg_lname'] = "";
  $_SESSION['reg_em'] = "";
  $_SESSION['reg_em2'] = "";
 }
}
