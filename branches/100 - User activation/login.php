<?php
require("lib/config.php");

/*
 * This is the page for logging into the site.
 */

if(!isset($_SESSION["login_redirect"])){
	$_SESSION["login_redirect"] =  $_SERVER["HTTP_REFERER"];
}

$content = $smarty->fetch("login.tpl");
$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Login .::. Running Site");
$smarty->display("master.tpl");
?>