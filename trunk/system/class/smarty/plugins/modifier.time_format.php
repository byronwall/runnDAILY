<?php
function smarty_modifier_time_format($seconds, $familiar = true){
	if($familiar){
		$formatted = date("H:i:s", mktime(0,0,$seconds, 1, 1, 2009));
		$familiar = explode(":", $formatted);
		$output = "";
		$unit = array('hour','min','sec');
		
		for ($i = 0; $i < count($familiar); $i++){
			if($familiar[$i] != 0){
				$familiar[$i] += 0;
				$output .= $familiar[$i];
				$output .= " " . $unit[$i];
				if($familiar[$i] != 1){
					$output .= "s";
				}
				if ($i < 2){
					$output .= " ";
				}
			}
		}
		
		return $output;
	}
	else{
		return date("H:i:s", mktime(0,0,$seconds, 1, 1, 2009));
	}
}
?>