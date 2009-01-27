<?php
if(!isset($_POST["action"])){
	Page::redirect("/admin/");
}

switch($_POST["action"]){
	case "update":
		$p_page = new Page($_POST, "p_");
		if($p_page->updatePage()){
			exit("success");
		}
		exit("DID NOT UPDATE");
		break;
}

?>