<?php
class Calendar{
	var $month;
	var $year;

	var $days = array();

	public function __construct($month, $year){
		$this->month = $month;
		$this->year = $year;

		for($i = 1;$i<=$this->getDaysInMonth();$i++){
			$this->days[$i] = null;
		}
	}

	public function getDaysInMonth(){
		return date("j", mktime(0,0,0,$this->month+1, 0, $this->year));
	}
	public function getFirstDayOfMonth(){
		return date("w", mktime(0,0,0,$this->month, 1, $this->year));
	}
	public function getLastDayOfMonth(){
		return date("w", mktime(0,0,0,$this->month+1, 0, $this->year));
	}
	public function getDayHeaders(){
		$headers = array("sun","mon","tue","wed","thu","fri","sat");
		return $headers;
	}
	public function getFirstDayOnCalendar(){
		return mktime(0,0,0,$this->month, 1-$this->getFirstDayOfMonth(), $this->year);
	}
	public function getLastDayOnCalendar(){
		return mktime(0,0,0,$this->month+1, 6 - $this->getLastDayOfMonth(), $this->year);
	}
	public function getLastMonthDays(){
		$first_day = $this->getFirstDayOfMonth();
		$first_old_date = date("j", mktime(0,0,0,$this->month, 1-$first_day, $this->year));

		$old_days = array();

		for($i = 0; $i< $first_day; $i++){
			$old_days[] = $first_old_date + $i;
		}

		return $old_days;

	}
	public function getNextMonthDays(){
		$last_day = $this->getLastDayOfMonth();

		$next_days = array();

		for($i=$last_day;$i<6;$i++){
			$next_days[] = $i - $last_day + 1;

		}
		return $next_days;
	}
	public function addItemToDay($timestamp, $item){
		$day = date("j", $timestamp);

		if(isset($this->days[$day])){
			$this->days[$day][] = $item;
		}
		else{
			$this->days[$day] = array($item);				
		}
		return true;
	}
}
?>