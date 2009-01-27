<?php
if(!isset($_POST["f_uid"])){
	echo 0;
	exit;
}

$friend_uid = $_POST["f_uid"];

if(!isset($_SESSION["userData"])){
	echo 0;
	exit;
}
$current_user = $_SESSION["userData"];

echo $current_user->addFriend($friend_uid);
?>