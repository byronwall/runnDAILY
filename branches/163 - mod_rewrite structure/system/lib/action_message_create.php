<?php
$msg = new Message($_POST);
if($msg->createOrUpdateMessage()){
	echo "message sent";
}
?>