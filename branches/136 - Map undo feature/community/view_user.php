<?php

require("../lib/config.php");

$uid = $_GET["uid"];

$smarty->assign("user",User::fromUid($uid));

$content = $smarty->fetch("community/view_user.tpl");
$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Community - Running Site");
$smarty->display("master.tpl");

?>