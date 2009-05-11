<?php
function smarty_modifier_date_familiar($datetime){
		$date_secs = strtotime($datetime);
		$days_since = idate("z") - idate("z", $date_secs);
		
		if ($days_since < 7){
			if ($days_since == 0){
				return ("This " . Log::evaluateTime($datetime));
			}elseif ($days_since == 1){
				return ("Yesterday " . Log::evaluateTime($datetime));
			}else{
				return (date("l", $date_secs) . " " . Log::evaluateTime($datetime));
			}
		}
		
		$weeks_since = idate("W") - idate("W", $date_secs);
		$months_since = idate("m") - idate("m", $date_secs);
		
		if ($months_since == 0){
			switch($weeks_since){
				case 1:
					return ("Last week");
					break;
				case 2:
					return ("Two weeks ago");
					break;
				case 3:
					return ("Three weeks ago");
					break;
				case 4:
					return ("Four weeks ago");
					break;
			}
		}elseif ($months_since == 1){
			return ("Last month");
		}elseif ($months_since > 1){
			return (date("F Y", $date_secs));
		}
}
?>