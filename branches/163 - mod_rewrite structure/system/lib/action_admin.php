<?php
if(!isset($_POST["action"])){
	Page::redirect("/admin/");
}

switch($_POST["action"]){
	case "update_stats":
		if(Stats::insertStats()){
			exit("success");
		}
		echo "DID NOT WORK";
		break;
}

die("some other failure");

?>