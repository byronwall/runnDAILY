<?php
$site_root = dirname(dirname(__FILE__));
$google_id = ABQIAAAAYZcibhuwr8GMgCWYwqU-RxQzNv4mzrEKtvvUg4SKGFnPU6pUNBTkQL_qSiLmJQ3qE-zNxRFJgRZM8g

require_once($site_root."/lib/class/ext_mysqli.php");
require_once($site_root."/lib/class/class_user.php");
require_once($site_root."/lib/class/class_route.php");
require_once($site_root."/lib/class/config_special.php")

require_once($site_root."/_smarty/Smarty.class.php");

/*SET UP TEMPLATING ENGINE*/
$smarty = new Smarty();

$smarty->template_dir = $site_root."/tpl";
$smarty->compile_dir = $site_root."/_smarty/templates_c";
$smarty->cache_dir = $site_root."/_smarty/cache";
$smarty->config_dir = $site_root."/_smarty/configs";
$smarty->left_delimiter = "{{";
$smarty->right_delimiter = "}}";

/*VALIDATE THE USER EVERYTIME*/
session_start();

$user = new User();
$user->validateUser();

if(isset($_SESSION["userData"])){
	$user = $_SESSION["userData"];
}

/*GENERATE THE TEMPLATE THINGS FOR EVERY PAGE*/
$smarty->assign("currentUser", $user);

?>
