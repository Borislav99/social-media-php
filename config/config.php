<?php
ob_start();
session_start();
$connect = mysqli_connect("127.0.0.2:3307", "root", "", "social");;
$timezone = date_default_timezone_set("Europe/Sarajevo");
