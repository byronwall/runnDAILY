<?php
abstract class SqlCondition{
	public $field;
	public $active = false;

	public $codes;
	public $sql;
	public $params = array();

	abstract public function setData($data);
	
	function getQueryString(){
		return "{$this->field}={$this->params[0]}";
	}

	public function __construct($field){
		$this->field = $field;
	}
}
?>