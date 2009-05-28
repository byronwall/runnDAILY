<?php
class DateRange{
	var $start;
	var $end;
	
	function getWeekRange($start = "today", $offset = 0){
		$this->start = strtotime($start);
		$week = date("W", $this->start) + $offset;
		
		$i = 0;
		
		while(date("W", strtotime("-$i day")) >= $week){
			$i++;
		}
		
		$i--;
		
		$this->start = strtotime("-$i day", $this->start);
		$this->end = strtotime("+6 day", $this->start);
	}
}
?>