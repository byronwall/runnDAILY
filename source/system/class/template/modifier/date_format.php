<?php
/**
 * Modifier is used to format input into a good looking date.
 *
 */
class Template_Modifier_Date_Format extends Template_Modifier {
	function runtime($variable, $param = "F j, Y") {
		if (! is_int ( $variable )) {
			$variable = strtotime ( $variable );
		}
		return date ( $param, $variable );
	}
}