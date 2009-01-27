<?php

class SqlRangeCondition extends SqlCondition{
	private $ph = "?";
	private $func = false;
	private $func_call;
	private $_param_orginal = array();

	function __construct($field, $mysql_func = "", $php_func = ""){
		parent::__construct($field);
		if(!empty($mysql_func)){
			$this->ph = "$mysql_func(?)";
		}
		if(!empty($php_func)){
			$this->func = true;
			$this->func_call = $php_func;
		}
	}
	function setData($arr){
		$this->_param_orginal = $arr;
		$data = ($this->func)? array_map($this->func_call, $arr): $arr;


		if(isset($data[0]) && $data[0]!=""){
			if(isset($data[1]) && $data[1] !=""){
				$this->sql = "{$this->field} BETWEEN {$this->ph} AND {$this->ph}";
				$this->codes = "ss";
				$this->params = array($data[0], $data[1]);
			}
			else{
				$this->sql =  "{$this->field} >= {$this->ph}";
				$this->codes = "s";
				$this->params = array($data[0]);
			}
		}
		elseif(isset($data[1]) && $data[1] !=""){
			$this->sql = "{$this->field} <= {$this->ph}";
			$this->codes = "s";
			$this->params = array($data[1]);
		}
		else{
			return false;
		}
		$this->active = true;
		return true;
	}
	function getQueryString(){
		if(isset($this->params[0], $this->params[1])) return "{$this->field}[0]={$this->_param_orginal[0]}&{$this->field}[1]={$this->_param_orginal[1]}";
		if(isset($this->params[0])) return "{$this->field}[0]={$this->_param_orginal[0]}";
		if(isset($this->params[1])) return "{$this->field}[1]={$this->_param_orginal[1]}";
	}
}
?>