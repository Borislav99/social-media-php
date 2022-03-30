<?php
include "includes/header.php";
?>
<div class="main_column column" id="main_column">
  <h4>Friend Requests</h4>
  <?php
  $query = mysqli_query($connect, "SELECT * FROM friend_requests WHERE user_to = '$userLoggedIn'");
  //ako nema zahtjeva
  if (mysqli_num_rows($query) == 0) {
    echo "No friend request at this time";
  } else {
    while ($row = mysqli_fetch_array($query)) {
      //od kojeg korisnika je primio zahtjev
      $user_from = $row['user_from'];
      $user_from_obj = new User($connect, $user_from);
      echo $user_from_obj->getFirstAndLastName() . " sent you a friend request";
      //zatim hocemo da dobijemo niz unutar koga se nalazi prijatelji osobe koja nam je poslala zahtjev za prijateljstvo
      $user_from_friend_array = $user_from_obj->getFriendArray();

      if (isset($_POST['accept_request' . $user_from])) {
        //upit za dodavanje prijatelja prijavljenom korisniku
        $add_friend_query = mysqli_query($connect, "UPDATE users SET friend_array=CONCAT(friend_array,'$user_from,') WHERE username = '$userLoggedIn'");
        //upit za dodavanje prijavljenog korisnika kao prijatelja osobi koja je poslala zahtjev za prijateljstvo
        $add_friend_query = mysqli_query($connect, "UPDATE users SET friend_array=CONCAT(friend_array,'$userLoggedIn,') WHERE username = '$user_from'");
        //izbrisi podatke iz tabele friend_requests
        $delete_query = mysqli_query($connect, "DELETE FROM friend_requests WHERE user_to = '$userLoggedIn' AND user_from = '$user_from'");
        //uradi echo
        echo "You are now friends";
        header("Location: requests.php");
      }
      if (isset($_POST['ignore_request' . $user_from])) {
        //izbrisi podatke iz tabele friend_requests
        $delete_query = mysqli_query($connect, "DELETE FROM friend_requests WHERE user_to = '$userLoggedIn' AND user_from = '$user_from'");
        //uradi echo
        echo "You've ignored friend request from " . $user_from;
        header("Location: requests.php");
      }
  ?>
      <form action="requests.php" method="post">
        <input id="accept_button" type="submit" value="Accept" name="accept_request<?php echo $user_from; ?>">
        <input id="ignore_button" type="submit" value="Ignore" name="ignore_request<?php echo $user_from; ?>">
      </form>

  <?php
    }
  }
  ?>
</div>