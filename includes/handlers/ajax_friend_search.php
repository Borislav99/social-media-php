<?php
include "../../config/config.php";
include "../classes/User.php";
$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];
$names = explode(" ", $query);
if (strpos($query, "_") !== false) {
 //LIKE kljucna rijec, ako npr. neko ukuca Boris on ce izbaciti sve sto pocinje sa Boris
 $usersReturned = mysqli_query($connect, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
} else if (count($names) == 2) {
 $usersReturned = mysqli_query($connect, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' AND last_name LIKE '%$names[1]%' AND user_closed ='no' LIMIT 8)");
} else {
 $usersReturned = mysqli_query($connect, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' OR last_name LIKE '%$names[0]%') AND user_closed = 'no' LIMIT 8");
}
if ($query != "") {
 while ($row = mysqli_fetch_array($usersReturned)) {
  $user = new User($connect, $userLoggedIn);
  if ($row['username'] != $userLoggedIn) {
   $mutual_friends = $user->getMutualFriends($row['username']) . " friends in common";
  } else {
   $mutual_friends = "";
  }
  if ($user->isFriend($row['username'])) {
   echo "<div class='resultDisplay'>
   <a style='color:#000' href='messages.php?u=" . $row['username'] . "' >
   <div class='liveSearchProfilePic'>
   <img src='" . $row['profile_pic'] . "'>
   </div>
   <div class='liveSearchText'>
   " . $row['first_name'] . " " . $row['last_name'] . "
   <p style='margin:0'>" . $row['username'] . "</p>
   <p id='grey'>" . $mutual_friends . "</p>
   </div>
   </a>
   </div>";
  }
 }
}
