<?php
include "../../config/config.php";
include "../classes/User.php";
include "../classes/Message.php";
//broj poruka za ocitavanje
$limit = 6;
$message = new Message($connect, $_REQUEST['userLoggedIn']);
echo $message->getConvosDropdown($_REQUEST, $limit);
