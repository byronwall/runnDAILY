<?php
require("config.php");

$msg = Message::fromFetchAssoc($_POST);
if($msg->createOrUpdateMessage()){
	echo "message sent";
}


?>