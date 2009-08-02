<?php
/**
 * Function is used to cycle through values.  Direct port of Smarty function.
 * @author Byron Wall
 *
 */
class Template_Block_Cycle extends Template_Function {
	protected $_function = "cycle";
	
	function runtime($params, $smarty) {
		static $cycle_vars = array ();
		
		$name = (empty ( $params ['name'] )) ? 'default' : $params ['name'];
		$print = (isset ( $params ['print'] )) ? ( bool ) $params ['print'] : true;
		$advance = (isset ( $params ['advance'] )) ? ( bool ) $params ['advance'] : true;
		$reset = (isset ( $params ['reset'] )) ? ( bool ) $params ['reset'] : false;
		
		if (! in_array ( 'values', array_keys ( $params ) )) {
			if (! isset ( $cycle_vars [$name] ['values'] )) {
				$smarty->trigger_error ( "cycle: missing 'values' parameter" );
				return;
			}
		} else {
			if (isset ( $cycle_vars [$name] ['values'] ) && $cycle_vars [$name] ['values'] != $params ['values']) {
				$cycle_vars [$name] ['index'] = 0;
			}
			$cycle_vars [$name] ['values'] = $params ['values'];
		}
		
		$cycle_vars [$name] ['delimiter'] = (isset ( $params ['delimiter'] )) ? $params ['delimiter'] : ',';
		
		if (is_array ( $cycle_vars [$name] ['values'] )) {
			$cycle_array = $cycle_vars [$name] ['values'];
		} else {
			$cycle_array = explode ( $cycle_vars [$name] ['delimiter'], $cycle_vars [$name] ['values'] );
		}
		
		if (! isset ( $cycle_vars [$name] ['index'] ) || $reset) {
			$cycle_vars [$name] ['index'] = 0;
		}
		
		if (isset ( $params ['assign'] )) {
			$print = false;
			$smarty->assign ( $params ['assign'], $cycle_array [$cycle_vars [$name] ['index']] );
		}
		
		if ($print) {
			$retval = $cycle_array [$cycle_vars [$name] ['index']];
		} else {
			$retval = null;
		}
		
		if ($advance) {
			if ($cycle_vars [$name] ['index'] >= count ( $cycle_array ) - 1) {
				$cycle_vars [$name] ['index'] = 0;
			} else {
				$cycle_vars [$name] ['index'] ++;
			}
		}
		
		return $retval;
	}

}
