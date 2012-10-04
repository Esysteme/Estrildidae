<?php



$login = new controller("user", "block_newsletter", "");
$login->get_controller();
$login->display();

/*
$login = new controller("species", "block_video", "");
$login->get_controller();
$login->display();
*/

$login = new controller("species", "block_last_photo", "");
$login->get_controller();
$login->display();



$login = new controller("user", "block_last_online", "");
$login->get_controller();
$login->display();


$login = new controller("user", "block_last_registered", "");
$login->get_controller();
$login->display();
