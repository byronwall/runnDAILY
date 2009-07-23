<?php
class Template_Modifier_String_Format extends Template_Modifier {
	function runtime($string, $format) {
		return sprintf ( $format, $string );
	}
}