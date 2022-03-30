<?php
class User
{
 private $user;
 private $connect;
 public function __construct($connect, $user)
 {
  $this->connect = $connect;
  $user_details_query = mysqli_query($connect, "SELECT * FROM users WHERE username = '$user'");
  $this->user = mysqli_fetch_array($user_details_query);
 }
 public function getFirstAndLastName()
 {
  $username = $this->user['username'];
  $query = mysqli_query($this->connect, "SELECT first_name, last_name FROM users WHERE username = '$username'");
  $row = mysqli_fetch_array($query);
  return $row['first_name'] . " " . $row['last_name'];
 }
 public function getUsername()
 {
  return $this->user['username'];
 }
 public function getNumPosts()
 {
  $username = $this->user['username'];
  $query = mysqli_query($this->connect, "SELECT num_posts FROM users WHERE username = '$username'");
  $row = mysqli_fetch_array($query);
  return $row['num_posts'];
 }
 public function isClosed()
 {
  $username = $this->user['username'];
  $query = mysqli_query($this->connect, "SELECT user_closed FROM users WHERE username = '$username'");
  $row = mysqli_fetch_array($query);
  if ($row['user_closed'] == 'yes') {
   return true;
  } else {
   return false;
  }
 }
 public function isFriend($username_to_check)
 {
  $usernameComma = "," . $username_to_check . ",";
  if ((strstr($this->user['friend_array'], $usernameComma)) || $username_to_check == $this->user['username']) {
   return true;
  } else {
   return false;
  }
 }
 public function getProfilePic()
 {
  return $this->user['profile_pic'];
 }
 public function getFriendArray()
 {
  return $this->user['friend_array'];
 }
 public function didReceiveRequest($user_from)
 {
  $user_to = $this->user['username'];
  $check_request_query = mysqli_query($this->connect, "SELECT * FROM friend_requests WHERE user_to = '$user_to' AND user_from = '$user_from'");
  if (mysqli_num_rows($check_request_query) > 0) {
   return true;
  } else {
   return false;
  }
 }
 public function didSendRequest($user_to)
 {
  $user_from = $this->user['username'];
  $check_request_query = mysqli_query($this->connect, "SELECT * FROM friend_requests WHERE user_to = '$user_to' AND user_from = '$user_from'");
  if (mysqli_num_rows($check_request_query) > 0) {
   return true;
  } else {
   return false;
  }
 }
 //parametar je kojeg korisnika zelimo obrisati kao prijatelja
 public function removeFriend($user_to_remove)
 {
  //prijavljeni korisnik
  $logged_in_user = $this->user['username'];
  $query = mysqli_query($this->connect, "SELECT friend_array FROM users WHERE username = '$user_to_remove'");
  $row = mysqli_fetch_array($query);
  //niz prijatelja od korisnika kojeg zelimo obrisati
  $friend_array_username = $row['friend_array'];
  //brisemo korisnika iz niza prijatelja prijavljenog korisnika
  $new_friend_array = str_replace($user_to_remove . "," ,"", $this->user['friend_array']);
  $remove_friend = mysqli_query($this->connect, "UPDATE users SET friend_array = '$new_friend_array' WHERE username = '$logged_in_user'");
  //obrisi prijavljenog korisnika iz niza prijatelja
  $new_friend_array = str_replace($logged_in_user . ",", "", $friend_array_username);
  $remove_friend = mysqli_query($this->connect, "UPDATE users SET friend_array = '$new_friend_array' WHERE username = '$user_to_remove'");
 }
 //slanje zahtjeva za prijateljstvo
 public function sendRequest($user_to)
 {
  $user_from = $this->user['username'];
  $query = mysqli_query($this->connect, "INSERT INTO friend_requests (user_to, user_from) VALUES ('$user_to', '$user_from')");
 }
 public function getMutualFriends($user_to_check)
 {
  $mutualFriends = 0;
  //prijavljeni korisnik
  $user_array = $this->user['friend_array'];
  $user_array_expolode = explode(",", $user_array);
  //korisnik na cijem smo profilu
  $query = mysqli_query($this->connect, "SELECT friend_array FROM users WHERE username = '$user_to_check'");
  $row = mysqli_fetch_array($query);
  $user_to_check_array = $row['friend_array'];
  $user_to_check_array_expolode = explode(",", $user_to_check_array);
  //provjera
  foreach ($user_array_expolode as $i) {
   foreach ($user_to_check_array_expolode as $j) {
    if ($i == $j && $i != "") {
     $mutualFriends++;
    }
   }
  }
  return $mutualFriends;
 }
 public function getNumberOfFriendRequests()
 {
  $username = $this->getUsername();
  $query = mysqli_query($this->connect, "SELECT * FROM friend_requests WHERE user_to = '$username'");
  return mysqli_num_rows($query);
 }
}
