<?php
class Post
{
  private $user_obj;
  private $connect;
  public function __construct($connect, $user)
  {
    $this->connect = $connect;
    $this->user_obj = new User($connect, $user);
  }
  public function submitPost($body, $user_to)
  {
    //brise HTML tagove
    $body = strip_tags($body);
    //nema specijalnih karaktera
    $body = mysqli_real_escape_string($this->connect, $body);
    //omoguci <br>
    $body = str_replace("\r\n", "\n", $body);
    $body = nl2br($body);
    //omoguci <br>
    //obrisi prazan prostor
    $check_empty = preg_replace('/\s+/', "", $body);
    if ($check_empty != "") {
      //current date and time
      $date_added = date('Y-m-d H:i:s');
      //get username
      $added_by = $this->user_obj->getUsername();
      //if user is not on his profile user_to is none
      if ($user_to == $added_by) {
        $user_to = "none";
      }
      //insert post to database
      $query = mysqli_query($this->connect, "INSERT INTO posts (body, added_by, user_to, date_added, user_closed, deleted, likes) VALUES ('$body', '$added_by', '$user_to', '$date_added', 'no', 'no', 0)");
      //return id of the post which is inserted rn
      $returned_id = mysqli_insert_id($this->connect);

      //insert notification - notify user if someone is submitted to his page
      if ($user_to != 'none') {
        $notification = new Notification($this->connect, $this->user_obj->getUsername());
        //returned_id je id objave koja se objavljuje dobijana iz varijable returned_id
        $notification->insertNotification($returned_id, $user_to, 'profile_post');
      }
      //update post count for user
      $num_posts = $this->user_obj->getNumPosts();
      $num_posts++;
      $update_query = mysqli_query($this->connect, "UPDATE users SET num_posts = '$num_posts' WHERE username = '$added_by'");
    }
  }
  //ucitava objave od prijatelja koji su nam ih poslali
  public function loadPostsFriends()
  {
    $str = "";
    $data = mysqli_query($this->connect, "SELECT * FROM posts WHERE deleted = 'no' ORDER BY id DESC");
    while ($row = mysqli_fetch_array($data)) {
      $id = $row['id'];
      $body = $row['body'];
      $added_by = $row['added_by'];
      $date_time = $row['date_added'];
      if ($row['user_to'] == 'none') {
        $user_to = "";
      } else {
        $user_to_obj = new User($this->connect, $row['user_to']);
        $user_to_name = $user_to_obj->getFirstAndLastName();
        $user_to = "to <a href='" . $row['user_to'] . "'>" . $user_to_name . "</a>";
      }
      //da li je racun zatvoren, ukoliko jeste ne zelimo prikazivati njegove objave
      //korisnik koji je napravio objavu
      $added_by_obj = new User($this->connect, $added_by);
      if ($added_by_obj->isClosed()) {
        continue;
      }
      $user_logged_obj = new User($this->connect, $this->user_obj->getUsername());
      if ($_SESSION['log_username'] == $added_by) {
        $delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
      } else {
        $delete_button = "";
      }
      if ($user_logged_obj->isFriend($added_by)) {

        $user_details_query = mysqli_query($this->connect, "SELECT first_name, last_name, profile_pic FROM users WHERE username = '$added_by'");
        $user_row = mysqli_fetch_array($user_details_query);
        $first_name = $user_row['first_name'];
        $last_name = $user_row['last_name'];
        $profile_pic = $user_row['profile_pic'];
        //SHOW COMMENT SECTION

?>
        <script>
          function toggle<?php echo $id; ?>() {
            var target = event.target;
            if (target !== "a") {
              var element = document.getElementById("toggleComment<?php echo $id; ?>");
              if (element.style.display == "block") {
                element.style.display = "none";
              } else {
                element.style.display = "block";
              }
            }
          }
        </script>
      <?php

        //SHOW COMMENT SECTION
        //dobijanje vremenea
        $date_time_now = date("Y-m-d H:i:s");
        //vrijeme objave
        $start_date = new DateTime($date_time);
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

        $comments_check = mysqli_query($this->connect, "SELECT * FROM comments WHERE post_id = '$id'");
        $comments_check_num = mysqli_num_rows($comments_check);

        $str = "<div class='status_post' onClick='javascript:toggle$id()'>

          <div class='post_profile_pic'>
            <img src='$profile_pic' width='50'>
          </div>

          <div class='posted_by' style='color:#ACACAC'>
          <a href='$added_by'>$first_name $last_name</a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
          $delete_button
          </div>

          <div id=post_body>
          $body
          <br>
          <br>
          <br>
          </div>

          <div class='newsfeedPostOptions'>
          Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
          <iframe src='like.php?post_id=$id' id='like_iframe' frameborder='0' scrolling='no'></iframe>
          </div>

          <div class='post_comment' id='toggleComment$id' style='display:none'>
          <iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
          </div>

          </div>
          <hr>
  ";
        echo $str;
      }
      ?>
      <script>
        $(document).ready(function() {
          $('#post<?php echo $id ?>').on('click', function() {
            bootbox.confirm("Are you sure you want to delete this post?", function(result) {
              $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {
                result: result
              });
              if (result) {
                location.reload();
              }
            });
          })
        });
      </script>
    <?php
    }
  }
  public function loadProfilePosts($req)
  {
    $str = "";
    //dolazi iz REQUESTA
    $profileUser = $_GET['profile_username'];
    $data = mysqli_query($this->connect, "SELECT * FROM posts WHERE deleted = 'no' AND ((added_by = '$profileUser' AND user_to = 'none') OR user_to = '$profileUser') ORDER BY id DESC");
    while ($row = mysqli_fetch_array($data)) {
      $id = $row['id'];
      $body = $row['body'];
      $added_by = $row['added_by'];
      $date_time = $row['date_added'];
      $user_logged_obj = new User($this->connect, $this->user_obj->getUsername());
      if ($_SESSION['log_username'] == $added_by) {
        $delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
      } else {
        $delete_button = "";
      }
      $user_details_query = mysqli_query($this->connect, "SELECT first_name, last_name, profile_pic FROM users WHERE username = '$added_by'");
      $user_row = mysqli_fetch_array($user_details_query);
      $first_name = $user_row['first_name'];
      $last_name = $user_row['last_name'];
      $profile_pic = $user_row['profile_pic'];
      //SHOW COMMENT SECTION

    ?>
      <script>
        function toggle<?php echo $id; ?>() {
          var target = event.target;
          if (target !== "a") {
            var element = document.getElementById("toggleComment<?php echo $id; ?>");
            if (element.style.display == "block") {
              element.style.display = "none";
            } else {
              element.style.display = "block";
            }
          }
        }
      </script>
      <?php

      //SHOW COMMENT SECTION
      //dobijanje vremenea
      $date_time_now = date("Y-m-d H:i:s");
      //vrijeme objave
      $start_date = new DateTime($date_time);
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

      $comments_check = mysqli_query($this->connect, "SELECT * FROM comments WHERE post_id = '$id'");
      $comments_check_num = mysqli_num_rows($comments_check);

      $str = "<div class='status_post' onClick='javascript:toggle$id()'>

          <div class='post_profile_pic'>
            <img src='$profile_pic' width='50'>
          </div>

          <div class='posted_by' style='color:#ACACAC'>
          <a href='$added_by'>$first_name $last_name</a>&nbsp;&nbsp;&nbsp;&nbsp;$time_message
          $delete_button
          </div>

          <div id=post_body>
          $body
          <br>
          <br>
          <br>
          </div>

          <div class='newsfeedPostOptions'>
          Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
          <iframe src='like.php?post_id=$id' id='like_iframe' frameborder='0' scrolling='no'></iframe>
          </div>

          <div class='post_comment' id='toggleComment$id' style='display:none'>
          <iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
          </div>

          </div>
          <hr>
  ";
      echo $str;
      ?>
      <script>
        $(document).ready(function() {
          $('#post<?php echo $id ?>').on('click', function() {
            bootbox.confirm("Are you sure you want to delete this post?", function(result) {
              $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {
                result: result
              });
              if (result) {
                location.reload();
              }
            });
          })
        });
      </script>
    <?php
    }
  }
  public function getSinglePost($post_id)
  {
    $userLoggedIn = $this->user_obj->getUsername();
    $opened_query = mysqli_query($this->connect, "UPDATE notifications SET opened = 'yes' WHERE user_to = '$userLoggedIn' AND link LIKE '%=$post_id'");
    $str = "";
    $data = mysqli_query($this->connect, "SELECT * FROM posts WHERE deleted = 'no' AND id = '$post_id'");
    $row = mysqli_fetch_array($data);
    $id = $row['id'];
    $body = $row['body'];
    $added_by = $row['added_by'];
    $date_time = $row['date_added'];
    if ($row['user_to'] == 'none') {
      $user_to = "";
    } else {
      $user_to_obj = new User($this->connect, $row['user_to']);
      $user_to_name = $user_to_obj->getFirstAndLastName();
      $user_to = "to <a href='" . $row['user_to'] . "'>" . $user_to_name . "</a>";
    }
    //da li je racun zatvoren, ukoliko jeste ne zelimo prikazivati njegove objave
    //korisnik koji je napravio objavu
    $added_by_obj = new User($this->connect, $added_by);
    if ($added_by_obj->isClosed()) {
      return;
    }
    $user_logged_obj = new User($this->connect, $this->user_obj->getUsername());
    if ($_SESSION['log_username'] == $added_by) {
      $delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
    } else {
      $delete_button = "";
    }
    if ($user_logged_obj->isFriend($added_by)) {

      $user_details_query = mysqli_query($this->connect, "SELECT first_name, last_name, profile_pic FROM users WHERE username = '$added_by'");
      $user_row = mysqli_fetch_array($user_details_query);
      $first_name = $user_row['first_name'];
      $last_name = $user_row['last_name'];
      $profile_pic = $user_row['profile_pic'];
      //SHOW COMMENT SECTION

    ?>
      <script>
        function toggle<?php echo $id; ?>() {
          var target = event.target;
          if (target !== "a") {
            var element = document.getElementById("toggleComment<?php echo $id; ?>");
            if (element.style.display == "block") {
              element.style.display = "none";
            } else {
              element.style.display = "block";
            }
          }
        }
      </script>
      <?php

      //SHOW COMMENT SECTION
      //dobijanje vremenea
      $date_time_now = date("Y-m-d H:i:s");
      //vrijeme objave
      $start_date = new DateTime($date_time);
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

      $comments_check = mysqli_query($this->connect, "SELECT * FROM comments WHERE post_id = '$id'");
      $comments_check_num = mysqli_num_rows($comments_check);

      $str = "<div class='status_post' onClick='javascript:toggle$id()'>

          <div class='post_profile_pic'>
            <img src='$profile_pic' width='50'>
          </div>

          <div class='posted_by' style='color:#ACACAC'>
          <a href='$added_by'>$first_name $last_name</a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
          $delete_button
          </div>

          <div id=post_body>
          $body
          <br>
          <br>
          <br>
          </div>

          <div class='newsfeedPostOptions'>
          Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
          <iframe src='like.php?post_id=$id' id='like_iframe' frameborder='0' scrolling='no'></iframe>
          </div>

          <div class='post_comment' id='toggleComment$id' style='display:none'>
          <iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
          </div>

          </div>
          <hr>
  ";
      echo $str;

      ?>
      <script>
        $(document).ready(function() {
          $('#post<?php echo $id ?>').on('click', function() {
            bootbox.confirm("Are you sure you want to delete this post?", function(result) {
              $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {
                result: result
              });
              if (result) {
                location.reload();
              }
            });
          })
        });
      </script>
<?php
    } else {
      echo "You are not friends with this user";
      return;
    }
  }
}