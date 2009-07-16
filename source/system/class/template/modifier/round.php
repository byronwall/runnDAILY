<?php
class Template_Modifier_Round {
	function runtime($value, $precision) {
		return round ( $value, $precision );
	}
}