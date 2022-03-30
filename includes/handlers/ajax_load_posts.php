<?php 
//konekciju sa bazom
include "../../config/config.php";
//klasa korisnika zbog pristupa
include "../classes/User.php";
//klasa objava zbog pristupa
include "../classes/Post.php";
//broj objava po pozivu
$limit = 10;
//objasniti poslije
$posts = new Post($connect, $_REQUEST['userLoggedIn']);
//ucitaj objave
$posts->loadPostsFriends($_REQUEST, 10);
