<?php
function smarty_modifier_time_format($seconds){
	return date("H:i:s", mktime(0,0,$seconds, 1, 1, 2009));
}
?>