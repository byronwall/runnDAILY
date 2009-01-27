<?php
if(!isset($_POST["action"])){
	Page::redirect("/training/");
}

$t_item = new TrainingLog($_POST);

switch ($_POST["action"]){
	case "edit":
		if($t_item->updateItem() ){
			Page::redirect("/training/view.php?tid={$t_item->tid}");
		}
		Page::redirect("/training/manage.php?tid={$t_item->tid}");
		break;
	case "delete":
		if($t_item->deleteItemSecure()){
			Page::redirect("/training/");
		}
		Page::redirect("/training/manage.php?tid={$t_item->tid}");
		break;
	case "save":
		if($t_item->createItem()){
			Page::redirect("/training/view.php?tid={$t_item->tid}");
		}
		Page::redirect("/training/");
		break;
}

?>