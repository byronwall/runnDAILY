<?php
class Template_Modifier_String_Format {
	function runtime($string, $format) {
		return sprintf ( $format, $string );
	}
}