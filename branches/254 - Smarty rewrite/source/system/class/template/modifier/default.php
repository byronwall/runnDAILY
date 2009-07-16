<?php
class Template_Modifier_Default {
	function runtime($string, $default = '') {
		if (! isset ( $string ) || $string === '')
			return $default;
		else
			return $string;
	}
}