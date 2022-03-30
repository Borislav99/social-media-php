<?php
include 'config/config.php';
include 'includes/form_handlers/register_handler.php';
include 'includes/form_handlers/login_handler.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Register</title>
     <link rel="stylesheet" href="assets/css/register_style.css">
     <script src="assets/js/register.js"></script>
</head>
<?php
if (isset($_POST['register_button'])) {
     echo "<script>
     document.addEventListener('DOMContentLoaded', function() {
     let login_form = document.querySelector('.first');
     let register_form = document.querySelector('.second');
     register_form.classList.remove('second');
     login_form.classList.add('second');
})
     </script>
     ";
}
?>
<div class="wrapper">

     <body>
          <!---------- LOGIN ---------->
          <div class="login_box">
               <div class="login_header">
                    <h1>Apeiron Book</h1>
                    Login or sign up bellow!
               </div>
               <div class="first">
                    <form action="register.php" method="post">
                         <input type="email" name="log_email" id="" placeholder="Email Adress" value="<?php if (isset($_SESSION['email'])) {
                                                                                                              echo $_SESSION['log_email'];
                                                                                                         } ?>">
                         <br>
                         <input type="password" name="log_password" id="" placeholder="Password">
                         <br>
                         <input type="submit" name="log_button" value="Login">
                         <br>
                         <?php
                         if (in_array("Email or password was incorrect <br>", $error_array)) {
                              echo "Email or password was incorrect <br>";
                         }
                         ?>
                         <a href="#" id="signup" class="signup">Need an account, register here!</a>
                    </form>
               </div>
               <br>
               <!---------- LOGIN ---------->
               <!---------- REGISTER ---------->
               <div class="second">
                    <form action="register.php" method="post">
                         <! ----- First name ----->
                              <input type="text" name="reg_fname" placeholder="First Name" required id="" value="<?php if (isset($_SESSION['reg_fname'])) {
                                                                                                                        echo $_SESSION['reg_fname'];
                                                                                                                   } ?>">
                              <br>
                              <?php
                              //first name error
                              if (in_array("Your first name must be between 2 and 25 characters <br>", $error_array)) {
                                   echo "Your first name must be between 2 and 25 characters <br>";
                              }
                              ?>
                              <! ----- First name ----->

                                   <! ----- Last name ----->
                                        <input type="text" name="reg_lname" placeholder="Last Name" required id="" value="<?php if (isset($_SESSION['reg_lname'])) {
                                                                                                                                  echo $_SESSION['reg_lname'];
                                                                                                                             } ?>">
                                        <br>
                                        <?php
                                        //last name error
                                        if (in_array("Your last name must be between 2 and 25 characters <br>", $error_array)) {
                                             echo "Your last name must be between 2 and 25 characters <br>";
                                        }
                                        ?>
                                        <! ----- Last name ----->

                                             <! ----- Email ----->

                                                  <input type="email" name="reg_email" placeholder="Email" required id="" value="
  <?php if (isset($_SESSION['reg_em'])) {
          echo $_SESSION['reg_em'];
     } ?>">
                                                  <br>
                                                  <input type="email" name="reg_email2" placeholder="Confirm email" required id="" value="
  <?php if (isset($_SESSION['reg_em2'])) {
          echo $_SESSION['reg_em2'];
     } ?>">
                                                  <br>
                                                  <?php
                                                  //email error
                                                  if (in_array("Email already in use <br>", $error_array)) {
                                                       echo "Email already in use <br>";
                                                  } else if (in_array("Invalid email format <br>", $error_array)) {
                                                       echo "Invalid email format <br>";
                                                  } else if (in_array("Email's don't match <br>", $error_array)) {
                                                       echo "Email's don't match <br>";
                                                  };
                                                  ?>
                                                  <! ----- Email ----->

                                                       <! ----- Password ----->

                                                            <input type="password" name="reg_password" placeholder="Password" required id="">
                                                            <br>
                                                            <input type="password" name="reg_password2" placeholder="Confirm Password" required id="">
                                                            <br>
                                                            <?php
                                                            //password error
                                                            if (in_array("Your password's don't match <br>", $error_array)) {
                                                                 echo "Your password's don't match <br>";
                                                            } else if (in_array("Your password can only contain english characters or numbers <br>", $error_array)) {
                                                                 echo "Your password can only contain english characters or numbers <br>";
                                                            } else if (in_array("Your password must be between 5 and 30 characters <br>", $error_array)) {
                                                                 echo "Your password must be between 5 and 30 characters <br>";
                                                            };
                                                            ?>
                                                            <! ----- Password ----->
                                                                 <input type="submit" name="register_button" value="Register">
                                                                 <br>
                                                                 <?php
                                                                 if (in_array("<span style='color:#14C800'>You have been succesfully registred, go ahead and login!</span> <br>", $error_array)) {
                                                                      echo "<span style='color:#14C800'>You have been succesfully registred, go ahead and login!</span> <br>";
                                                                 }
                                                                 ?>
                                                                 <a href="#" id="signin" class="signin">Already have an account, sign in here!</a>

                    </form>
               </div>
          </div>
          <!---------- REGISTER ---------->
     </body>
</div>

</html>