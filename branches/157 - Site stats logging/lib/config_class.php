<?php

DEFINE("SITE_ROOT", dirname(dirname(__FILE__)));
function __autoload($class){
	require_once(SITE_ROOT."/lib/class/class_".strtolower($class).".php");
}

require_once(SITE_ROOT."/lib/class/ext_mysqli.php");
require_once(SITE_ROOT."/lib/class/class_user.php");
require_once(SITE_ROOT."/lib/class/class_route.php");
require_once(SITE_ROOT."/lib/class/class_log.php");
require_once(SITE_ROOT."/lib/class/class_training.php");
require_once(SITE_ROOT."/lib/class/class_calendar.php");
require_once(SITE_ROOT."/lib/class/class_message.php");
require_once(SITE_ROOT."/lib/class/class_page.php");
require_once(SITE_ROOT."/lib/class/class_rss.php");
require_once(SITE_ROOT."/lib/class/class_sql_generator.php");
?>