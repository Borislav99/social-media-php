<?php
class Notification
{
  private $user_obj;
  private $connect;
  public function __construct($connect, $user)
  {
    $this->connect = $connect;
    $this->user_obj = new User($connect, $user);
  }
  public function getUnreadNumber()
  {
    $userLoggedIn = $this->user_obj->getUsername();
    $query = mysqli_query($this->connect, "SELECT * FROM notifications WHERE viewed = 'no' AND user_to = '$userLoggedIn'");
    return mysqli_num_rows($query);
  }
  public function getNotifications($data, $limit)
  {
    $page = $data['page'];
    $userLoggedIn = $this->user_obj->getUsername();
    $return_string = "";
    //ako je prva stranica
    if ($page == 1) {
      $start = 0;
    } else {
      //odakle da pocne ukoliko se ne nalazi na prvoj stranici
      $start = ($page - 1) * $limit;
    }
    //korisnik je pregledao poruku upit, stavi sve poruke na pregledane ukoliko otvori popup
    $set_viewed_query = mysqli_query($this->connect, "UPDATE notifications SET viewed='yes' WHERE user_to = '$userLoggedIn'");

    $query = mysqli_query($this->connect, "SELECT * FROM notifications WHERE user_to = '$userLoggedIn' ORDER BY id DESC");
    if (mysqli_num_rows($query) == 0) {
      echo "You have no notifications";
      return;
    }
    //broj poruka pregledanih
    $num_iterations = 0;
    //broj poruka poslatih
    $count = 1;
    while ($row = mysqli_fetch_array($query)) {
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
      //od koga je not
      $user_from = $row['user_from'];
      $user_data_query = mysqli_query($this->connect, "SELECT * FROM users WHERE username='$user_from'");
      $user_data = mysqli_fetch_array($user_data_query);
      //dobijanje vremenea
      $date_time_now = date("Y-m-d H:i:s");
      //vrijeme objave
      $start_date = new DateTime($row['datetime']);
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
      $opened = $row['opened'];
      if (isset($row['opened']) && $row['opened'] == 'no') {
        $style = 'background-color:#DDEDFF';
      } else {
        $style = '';
      }
      $return_string .= "
   <a href='" . $row['link'] . "'>
   <div class='resultDisplay resultDisplayNotification' style='" . $style . "'>
<div class='notificationsProfilePic'>
<img src='" . $user_data['profile_pic'] . "'>
<p class='timestamp_smaller' id='grey'>
" . $time_message . "
</p> " . $row['message'] . "
</div>
   </div>
  </a>";
    }
    //ako su objave ucitane
    if ($count > $limit) {
      $return_string .= "<input type='hidden' class='nextPageDropdownData' value='." . ($page + 1) . "'><input type='hidden' class='noMoreDropdownData' value='false'>";
    }
    //ako nismo
    else {
      $return_string .= "<input type='hidden' class='nextPageDropdownData' value='." . ($page + 1) . "'><input type='hidden' class='noMoreDropdownData' value='true'><p style='text-align:center;'>No more Notifications to load</p>";
    }
    return $return_string;
  }
  public function insertNotification($post_id, $user_to, $type)
  {
    $userLoggedIn = $this->user_obj->getUsername();
    $userLoggedInName = $this->user_obj->getFirstAndLastName();
    $date_time = date("Y-m_d H:i:s");
    switch ($type) {
      case 'comment':
        $message = $userLoggedInName . " commented on your post";
        break;
      case 'like':
        $message = $userLoggedInName . " liked your post";
        break;
      case 'profile_post':
        $message = $userLoggedInName . " posted on your profile";
        break;
      case 'comment_non_owner':
        $message = $userLoggedInName . " commented on a post you commented on";
        break;
      case 'profile_comment':
        $message = $userLoggedInName . " commented on your profile post";
        break;
    }
    //
    $link = 'post.php?id=' . $post_id;
    $insert_query = mysqli_query($this->connect, "INSERT INTO notifications (user_to, user_from, message, link, datetime, opened, viewed) VALUES ('$user_to', '$userLoggedIn', '$message', '$link', '$date_time', 'no', 'no')");
  }
}
