<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
require_once(CLASS_ROOT."\hash_module.php");
function smarty_function_modules($params, &$smarty)
{
	$mod_con = new module_controller($params, RoutingEngine::getSmarty());
	$mod_names = array_safe($params, "list");
	$mod_list = explode(",", $mod_names);

	$modules = array();
	$unplaced_modules = array();
	$counts = array("3"=>0, "4"=>0,"5"=>0);
	
	foreach($mod_list as $mod_code){
		$mod_name = Module::$hash[$mod_code];
		if(!method_exists($mod_con, $mod_name))continue;
		$module = $mod_con->{$mod_name}();
		if($module->size){
			$modules[$module->size][] = $module; 
			$counts[$module->size]++;
		}
		else{
			$unplaced_modules[] = $module;
		}
	}
	
	foreach($unplaced_modules as $module){
		asort($counts);
		current($counts);
		$modules[key($counts)][] = $module;
		$counts[key($counts)]++;
	}
	RoutingEngine::getSmarty()->assign("modules", $modules);
	
	return RoutingEngine::getSmarty()->fetch("modules/module_master.tpl");
}
?>
