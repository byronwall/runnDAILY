<?php
if(!isset($_POST["action"])) exit("No action");

switch($_POST["action"]){
	case "create":
		$message = new Message($_POST);

		if($message->msg){
			$message->uid_to = 0;
			$message->uid_from = User::$current_user->uid;

			exit(json_encode($message->createOrUpdateMessage()));
		}
		break;
	case "delete":
		$message = new Message($_POST);		
		exit(json_encode($message->deleteMessage()));
		break;
}

exit("EOF");

?>