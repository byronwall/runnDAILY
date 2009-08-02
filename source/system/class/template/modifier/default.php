<?php
class Template_Modifier_Default extends Template_Modifier {
	function runtime($string, $default = '') {
		if (! isset ( $string ) || $string === '')
			return $default;
		else
			return $string;
	}
}