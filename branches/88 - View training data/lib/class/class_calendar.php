<?php
define("CAL_MONTH", 1);
define("CAL_WEEK", 2);

class Calendar{
	var $month;
	var $year;
	var $offset;
	var $cal_type = CAL_MONTH;
	var $timestamp;

	var $days = array();

	public function __construct($timestamp, $cal_type = CAL_MONTH){
		$this->timestamp = $timestamp;
		$this->month = date("n", $timestamp);
		$this->year = date("Y", $timestamp);
		$this->offset = $this->getFirstDayOfMonth();
		$this->cal_type = $cal_type;

		$offset_front = $this->getFirstDayOfMonth();
		$offset_back = $this->getLastDayOfMonth();

		if($this->cal_type == CAL_MONTH){
			$j = 1;
			for($i = 1 - $offset_front;$i<=$this->getDaysInMonth() + 6 - $offset_back;$i++){
				$new_day = mktime(0,0,0,$this->month, $i, $this->year);
				$this->days[$this->hashTime($new_day)] = new CalendarDay($new_day, $j++);
			}
		}
		else if($this->cal_type == CAL_WEEK){
			$day_offset = date("w", $timestamp);
			$day = date("j", $timestamp);
			for($i = 0;$i<7;$i++){
				$new_day = mktime(0,0,0,$this->month, $day - $day_offset + $i, $this->year);
				$this->days[$this->hashTime($new_day)] = new CalendarDay($new_day);
			}
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
		if($this->cal_type == CAL_MONTH){
			return mktime(0,0,0,$this->month, 1-$this->getFirstDayOfMonth(), $this->year);
		}
		else if($this->cal_type == CAL_WEEK){
			$day_offset = date("w", $this->timestamp);
			$day = date("j", $this->timestamp);
			return mktime(0,0,0,$this->month, $day - $day_offset, $this->year);
		}
	}
	public function getLastDayOnCalendar(){
		if($this->cal_type == CAL_MONTH){
			return mktime(0,0,0,$this->month+1, 6 - $this->getLastDayOfMonth(), $this->year);
		}
		else if($this->cal_type == CAL_WEEK){
			$day_offset = date("w", $this->timestamp);
			$day = date("j", $this->timestamp);
			return mktime(0,0,0,$this->month, $day - $day_offset +6, $this->year);
		}
	}
	public function addItemToDay($timestamp, $item){
		$hash = $this->hashTime($timestamp);
		$this->days[$hash]->items[] = $item;

		return true;
	}
	private function hashTime($timestamp){
		return date("md", $timestamp);
	}
}
class CalendarDay{
	var $timestamp;
	var $items = array();
	var $day_num;
	var $month_current = true;

	function __construct($timestamp, $day_num = 0){
		$this->timestamp = $timestamp;
		$this->day_num = $day_num;
	}
}
?>