<?php
class Template_Modifier_Date_Format {
	function runtime($variable, $param = "F j, Y") {
		if (! is_int ( $variable )) {
			$variable = strtotime ( $variable );
		}
		return date ( $param, $variable );
	}
}