<?php
include "../../config/config.php";
include "../classes/User.php";
include "../classes/Notification.php";
//broj notifikacija za ocitavanje
$limit = 6;
$notification = new Notification($connect, $_REQUEST['userLoggedIn']);
echo $notification->getNotifications($_REQUEST, $limit);
