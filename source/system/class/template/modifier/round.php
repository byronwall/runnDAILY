<?php
class Template_Modifier_Round extends Template_Modifier {
	function runtime($value, $precision) {
		return round ( $value, $precision );
	}
}