<?php
include "../../config/config.php";
include "../../includes/classes/User.php";
$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];
$names = explode(" ", $query);
/* --- QUERIES --- */
//ako query ima " _ " pretpostavi da se radi o korisnickom imenu
if (strpos($query, "_") !== false) {
 $usersReturnedQuery = mysqli_query($connect, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 5");
}
//ako korisnik ukuca samo dvije rijeci, pretpostavi da misli na prvu rijec imena i prvu rijec prezimena
else if (count($names) == 2) {
 $usersReturnedQuery = mysqli_query($connect, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed = 'no' LIMIT 5");
}
//ako ima jednu rijec pretrazi imena i prezimena
else {
 $usersReturnedQuery = mysqli_query($connect, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed = 'no' LIMIT 5");
}
/* --- QUERIES --- */
if ($query != "") {
 while ($row = mysqli_fetch_array($usersReturnedQuery)) {
  $user = new User($connect, $userLoggedIn);
  if ($row['username'] != $userLoggedIn) {
   $mutual_friends = $user->getMutualFriends($row['username']) . " friends in common";
  } else {
   $mutual_friends = "";
  }
  echo "
  <div class='resultDisplay'>
   <a style='color:#1485BD' href='" . $row['username'] . "'>
    <div class='liveSearchProfilePic'>
     <img src='" . $row['profile_pic'] . "'>
    </div>
    <div class='liveSearchText'>
    " . $row['first_name'] . " " . $row['last_name'] . "
    <p>" . $row['username'] . "</p>
    <p id='grey'>" . $mutual_friends . "</p>
    </div>
   </a>
  </div>
  ";
 }
}
