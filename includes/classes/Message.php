<?php
class Message
{
 private $user_obj;
 private $connect;
 public function __construct($connect, $user)
 {
  $this->connect = $connect;
  $this->user_obj = new User($connect, $user);
 }
 public function getMostRecentUser()
 {
  $userLoggedIn = $this->user_obj->getUsername();
  //most recent one
  $query = mysqli_query($this->connect, "SELECT user_to, user_from FROM messages WHERE user_to = '$userLoggedIn' OR user_from = '$userLoggedIn' ORDER BY id DESC LIMIT 1");
  if (mysqli_num_rows($query) == 0) {
   return false;
  } else {
   $row = mysqli_fetch_array($query);
   $user_to = $row['user_to'];
   $user_from = $row['user_from'];
   if ($user_to !== $userLoggedIn) {
    return $user_to;
   } else {
    return $user_from;
   }
  }
 }
 public function sendMessage($user_to, $body, $date)
 {
  if ($body != "") {
   $userLoggedIn = $this->user_obj->getUsername();
   $query = mysqli_query($this->connect, "INSERT INTO messages (user_to, user_from, body, date, opened, viewed, deleted) VALUES ('$user_to', '$userLoggedIn', '$body', '$date', 'no', 'no', 'no')");
  }
 }
 public function getMessages($otherUser)
 {
  $userLoggedIn = $this->user_obj->getUsername();
  $data = "";
  $query = mysqli_query($this->connect, "UPDATE messages SET opened = 'yes' WHERE user_to = '$userLoggedIn' AND user_from = '$otherUser'");
  $get_messages_query = mysqli_query($this->connect, "SELECT * FROM messages WHERE (user_to = '$userLoggedIn' AND user_from = '$otherUser') OR (user_from = '$userLoggedIn' AND user_to = '$otherUser')");
  while ($row = mysqli_fetch_array($get_messages_query)) {
   $user_to = $row['user_to'];
   $user_from = $row['user_from'];
   $body = $row['body'];
   if ($user_to == $userLoggedIn) {
    $div_top = "<div class='message' id='green'>";
   } else {
    $div_top = "<div class='message' id='blue'>";
   }
   $data = $data . $div_top . $body . "</div><br><br>";
  }
  return $data;
 }
 public function getConvos()
 {
  $userLoggedIn = $this->user_obj->getUsername();
  $return_string = "";
  $convos = [];
  $query = mysqli_query($this->connect, "SELECT user_to, user_from  FROM messages WHERE user_to = '$userLoggedIn' OR user_from = '$userLoggedIn' ORDER BY id DESC");
  while ($row = mysqli_fetch_array($query)) {
   if ($row['user_to'] !== $userLoggedIn) {
    if (!in_array($row['user_to'], $convos)) {
     array_push($convos, $row['user_to']);
    }
   } else {
    if (!in_array($row['user_from'], $convos)) {
     array_push($convos, $row['user_from']);
    }
   }
  }
  foreach ($convos as $username) {
   $user_found_obj = new User($this->connect, $username);
   $latest_message_details = $this->getLatestMessage($userLoggedIn, $username);

   if (strlen($latest_message_details[1]) >= 12) {
    $dots = "...";
   } else {
    $dots = "";
   }
   //ako je duze od 12 karaktera obrisi od 12og
   $split = str_split($latest_message_details[1], 12);
   $split = $split[0] . $dots;
   $return_string .= "
   <a href='messages.php?u=" . $username . "'>
   <div class='user_found_messages'>
   <img src='" . $user_found_obj->getProfilePic() . "' style='border-radius:5px; margin-right:5px'>" . $user_found_obj->getFirstAndLastName() .
    " <span class='time_stamp_smaller' id='grey'>" . $latest_message_details[2]  . "</span>
   <p id='grey'>" . $latest_message_details[0] . $split . "</p>
   </div></a>";
  }
  return $return_string;
 }
 public function getLatestMessage($userLoggedIn, $username)
 {
  $details_array = [];
  $query = mysqli_query($this->connect, "SELECT body, user_to, date FROM messages WHERE (user_to = '$userLoggedIn' AND user_from = '$username') OR (user_to = '$username' AND user_from = '$userLoggedIn') ORDER BY id DESC LIMIT 1");
  $row = mysqli_fetch_array($query);
  //ko je rekao
  if ($row['user_to'] == $userLoggedIn) {
   $sent_by = "They said: ";
  } else {
   $sent_by = "You said: ";
  };
  //dobijanje vremenea
  $date_time_now = date("Y-m-d H:i:s");
  //vrijeme objave
  $start_date = new DateTime($row['date']);
  //trenutno vrijeme
  $end_date = new DateTime($date_time_now);
  //razlika medju vremenima
  $interval = $start_date->diff($end_date);
  if ($interval->y >= 1) {
   if ($interval == 1) {
    $time_message = " year old";
   } else {
    $time_message = $interval->y . " years ago";
   }
  } else if ($interval->m >= 1) {
   if ($interval->d == 0) {
    $days = " ago";
   } else if ($interval->d == 1) {
    $days = $interval->d . " day ago";
   } else {
    $days = $interval->d . " days ago";
   }
   if ($interval->m == 1) {
    $time_message = $interval->m . " month" . $days;
   } else {
    $time_message = $interval->m . " months" . $days;
   }
  } else if ($interval->d >= 1) {
   if ($interval->d == 1) {
    $time_message = "Yesterday";
   } else {
    $time_message = $interval->d . " days ago";
   }
  } else if ($interval->h >= 1) {
   if ($interval->h == 1) {
    $time_message = " hour ago";
   } else {
    $time_message = $interval->h . " hours ago";
   }
  } else if ($interval->i >= 1) {
   if ($interval->i == 1) {
    $time_message = " minute ago";
   } else {
    $time_message = $interval->i . " minutes ago";
   }
  } else if ($interval->s < 60) {
   $time_message = " now";
  }
  array_push($details_array, $sent_by);
  array_push($details_array, $row['body']);
  array_push($details_array, $time_message);
  return $details_array;
 }
 //dva parametra, jedan su podaci drugi su limit koliko ih zelimo prikazati u dropdown-u
 public function getConvosDropdown($data, $limit)
 {
  //broj stranice na kojoj se nalazimo preko ajaxa
  $page = $data['page'];
  $userLoggedIn = $this->user_obj->getUsername();
  $return_string = "";
  $convos = [];
  //ako je prva stranica
  if ($page == 1) {
   $start = 0;
  } else {
   //odakle da pocne ukoliko se ne nalazi na prvoj stranici
   $start = ($page - 1) * $limit;
  }
  //korisnik je pregledao poruku upit, stavi sve poruke na pregledane ukoliko otvori popup
  $set_viewed_query = mysqli_query($this->connect, "UPDATE messages SET viewed='yes' WHERE user_to = '$userLoggedIn'");

  $query = mysqli_query($this->connect, "SELECT user_to, user_from  FROM messages WHERE user_to = '$userLoggedIn' OR user_from = '$userLoggedIn' ORDER BY id DESC");
  while ($row = mysqli_fetch_array($query)) {
   if ($row['user_to'] !== $userLoggedIn) {
    if (!in_array($row['user_to'], $convos)) {
     array_push($convos, $row['user_to']);
    }
   } else {
    if (!in_array($row['user_from'], $convos)) {
     array_push($convos, $row['user_from']);
    }
   }
  }
  //broj poruka pregledanih
  $num_iterations = 0;
  //broj poruka poslatih
  $count = 1;
  foreach ($convos as $username) {
   //ako je broj pregledanih poruka manji od broja poruka na kojoj se sad nalazimo
   if ($num_iterations++ < $start) {
    continue;
   }
   //ako smo dosli do limita
   if ($count > $limit) {
    //izadji iz petlje
    break;
   } else {
    $count++;
   }
   //upit da se provjeri da li je otvoreno ili ne
   //uzimamo kolonu opened prilikom prolaska kroz svaku petlju
   $is_unread_query = mysqli_query($this->connect, "SELECT opened FROM messages WHERE user_to = '$userLoggedIn' AND user_from = '$username' ORDER BY id DESC");
   $row = mysqli_fetch_array($is_unread_query);
   //postavljamo stil zavisno od toga da li je opened ili ne
   if (isset($row['opened']) && $row['opened'] == 'no') {
    $style = 'background-color:#DDEDFF';
   } else {
    $style = '';
   }

   $user_found_obj = new User($this->connect, $username);
   $latest_message_details = $this->getLatestMessage($userLoggedIn, $username);

   if (strlen($latest_message_details[1]) >= 12) {
    $dots = "...";
   } else {
    $dots = "";
   }
   //ako je duze od 12 karaktera obrisi od 12og
   $split = str_split($latest_message_details[1], 12);
   $split = $split[0] . $dots;
   $return_string .= "
   <a href='messages.php?u=" . $username . "'>
   <div class='user_found_messages' style='" . $style . "'>
   <img src='" . $user_found_obj->getProfilePic() . "' style='border-radius:5px; margin-right:5px'>" . $user_found_obj->getFirstAndLastName() .
    " <span class='time_stamp_smaller' id='grey'>" . $latest_message_details[2]  . "</span>
   <p id='grey'>" . $latest_message_details[0] . $split . "</p>
   </div></a>";
  }
  //ako su objave ucitane
  if ($count > $limit) {
   $return_string .= "<input type='hidden' class='nextPageDropdownData' value='." . ($page + 1) . "'><input type='hidden' class='noMoreDropdownData' value='false'>";
  }
  //ako nismo
  else {
   $return_string .= "<input type='hidden' class='nextPageDropdownData' value='." . ($page + 1) . "'><input type='hidden' class='noMoreDropdownData' value='true'><p style='text-align:center;'>No more Messages to load</p>";
  }
  return $return_string;
 }
 //broj neprocitanih poruka
 public function getUnreadNumber()
 {
  $userLoggedIn = $this->user_obj->getUsername();
  $query = mysqli_query($this->connect, "SELECT * FROM messages WHERE viewed = 'no' AND user_to = '$userLoggedIn'");
  return mysqli_num_rows($query);
 }
}
