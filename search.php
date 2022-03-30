<?php

use function PHPSTORM_META\type;

include "includes/header.php";
if (isset($_GET['q'])) {
 $query = $_GET['q'];
} else {
 $query = "";
}
if (isset($_GET['type'])) {
 $type = $_GET['type'];
} else {
 $type = "name";
}
?>
<div class="main_column column" id="main_column">
 <?php
 if (empty($query)) {
  echo "You must enter something in the search box";
 } else {
  /* --- QUERIES --- */
  if ($type == 'username') {
   $usersReturnedQuery = mysqli_query($connect, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no'");
  } else {
   $names = explode(" ", $query);
   //ako imaju tri rijeci pretpostavicemo da traze ime, srednje ime i prezime
   if (count($names) == 3) {
    $usersReturnedQuery = mysqli_query($connect, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[2]%') AND user_closed = 'no'");
   }
   //dvije rijeci
   if (count($names) == 2) {
    $usersReturnedQuery = mysqli_query($connect, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed = 'no'");
   }

   //ako ima jednu rijec pretrazi imena i prezimena
   else {
    $usersReturnedQuery = mysqli_query($connect, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed = 'no' LIMIT 5");
   }
  }
  //da li su rezultati pronadjeni
  if (mysqli_num_rows($usersReturnedQuery) == 0) {
   echo "There is no-one with " . $type . " like " . $query;
  } else {
   echo mysqli_num_rows($usersReturnedQuery) . " results found. <br> <br>";
  }
  //u svakom slucaju cemo ponuditi mogucnost da zamijene tip, recimo da probaju trazi za drugaciji type
  echo "<p id='grey'>Try searching for:</p>";
  echo "<a href='search.php?q=" . $query . "&type=name'>Names</a>";
  echo "<br>";
  echo "<a href='search.php?q=" . $query . "&type=username'>Usernames</a>";
  while ($row = mysqli_fetch_array($usersReturnedQuery)) {
   $user_obj = new User($connect, $user['username']);
   $button = "";
   $mutual_friends = "";
   //ako nismo sebe nasli
   if ($user['username'] !== $row['username']) {
    //napravi dugme zavisno od toga da li smo prijatelji ili ne
    //ako smo prijatelji
    if ($user_obj->isFriend($row['username'])) {
     $button = "<input type='submit' class='danger' value='Remove Friend' name='" . $row['username'] . "'></input>";
    }
    //ako smo primili zahtjev za prijateljstvo
    else if ($user_obj->didReceiveRequest($row['username'])) {
     $button = "<input type='submit' class='warning' value='Respond to request' name='" . $row['username'] . "'></input>";
    }
    //ako smo mi poslali zahtjev
    else if ($user_obj->didSendRequest($row['username'])) {
     $button = "<input class='default' type='submit' value='Request sent'></input>";
    }
    //ako nismo prijatelji dacemo mogucnost da dodamo prijatelja
    else {
     $button = "<input type='submit' class='success' value='Add Friend' name='" . $row['username'] . "'></input>";
    }
    //zajednicki prijatelji
    $mutual_friends = $user_obj->getMutualFriends($row['username']) . " friends in common";
    //forma za sve dugmadi
    if (isset($_POST[$row['username']])) {
     //ako su prijatelji
     if ($user_obj->isFriend($row['username'])) {
      $user_obj->removeFriend($row['username']);
      header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
     }
     //ako su primili zahtjev za prijateljstvo
     else if ($user_obj->didSendRequest($row['username'])) {
      header("Location: requests.php");
     }
     //ako smo mi poslali zahtjev
     else if ($user_obj->didSendRequest($row['username'])) {
     }
     //dodaj prijatelja
     else {
      $user_obj->sendRequest($row['username']);
      header("Location:  http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
     }
    }
   }
   //div sa informacijama
   echo "
   <div class='search_result'>

    <div class='searchPageFriendButtons'>
     <form action='' method='post'>
     " . $button . "
     <br>
     </form>
    </div>

    <div class='result_profile_pic'>
     <a href='" . $row['username'] . "'><img style='height:100px' src='" . $row['profile_pic'] . "'></a>
    </div>

     <a href='" . $row['username'] . "'>" . $row['first_name'] . " " . $row['last_name'] . "
     <p id='grey'>" . $row['username'] . "</p>
     </a>
     <br>
     " . $mutual_friends . "
     <br>
   </div>
   <hr>
   ";
  }
 }
 ?>
</div>