<?php
$con = mysqli_connect("localhost", "root", "", "social");
if ($con) {
 echo "connected";
} else {
 echo "failed";
}
