<?php
DEFINE("SITE_ROOT", dirname(dirname(__FILE__)));

/*we can use this to avoid all the stuff below
function __autoload($class_name) {
    require_once $class_name . '.php';
}
*/

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
require_once(SITE_ROOT."/lib/class/modules.php");

function array_safe($arr, $index, $default = null){
	return (isset($arr[$index]))?$arr[$index]:$default;
}
function classFromArray($class, $arr, $arr_pre = ""){
	$ref = new $class();
	if (is_array($arr))
	{
		foreach(array_keys(get_class_vars($class)) as $k){
			if (isset($arr[$arr_pre.$k]))
			{
				$ref->$k = $arr[$arr_pre.$k];
			}
		}
	}
	return $ref;
}
?>
