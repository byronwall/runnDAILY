<?php

class SqlParser{
	private $_conditions = array();
	private $_data_set = false;
	private $_active_conditions = 0;
	public $count;
	public $page;
	public $is_limited = false;

	function __construct($should_limit = false, $count = 10, $page = 0){
		if($should_limit){
			$this->count = $count;
			$this->page = $page;
			$this->is_limited = true;
		}
	}

	public function addCondition($condition){
		$this->_conditions[] = $condition;
	}
	public function setData($arr){
		foreach($this->_conditions as $condition){
			if(!isset($arr[$condition->field]) || $arr[$condition->field]=="")continue;
			if($condition->setData($arr[$condition->field])){
				$this->_active_conditions++;
			}
		}
		if(isset($arr["page"])){
			$this->is_limited = true;
			$this->page = $arr["page"];
			$this->count = (isset($arr["count"]))?$arr["count"]: 10;
		}
		
		$this->_data_set = true;
	}
	public function getSQL(){
		if(!$this->_data_set) return " TRUE ";
		
		if($this->_active_conditions == 0){
			$query = " TRUE ";
		}
		else{
			$init = true;
			$query = "";

			foreach($this->_conditions as $condition){
				if(!$condition->active)continue;
				if(!$init){
					$query .= " AND ";
				}
				$query .= $condition->sql;
				$init=false;
			}
		}
		if($this->is_limited){
			$query .= " LIMIT ?,? ";
		}

		return $query;
	}
	public function getParamArray(){
		if(!$this->_data_set) return false;
		$params = array("codes");
		$codes = "";
		foreach($this->_conditions as $condition){
			if(!$condition->active)continue;
			$codes .= $condition->codes;
			$params = array_merge($params, $condition->params);
		}

		if($this->is_limited){
			$codes .= "ii";
			$params[] = $this->page * $this->count;
			$params[] = $this->count;
		}

		$params[0] = $codes;
		return $params;
	}
	public function hasParams(){
		if($this->is_limited) return true;

		foreach($this->_conditions as $condition){
			if($condition->active) return true;
		}
		return false;
	}
	public function getQueryString($should_page = false, $should_incr_page = false){
		$query = "";
		$sep = "";
		foreach($this->_conditions as $condition){
			if(!$condition->active) continue;
			if($sep == ""){
				$sep = "&";
			}
			else{
				$query .= $sep;
			}
			$query .= $condition->getQueryString();
		}
		if($this->is_limited && $should_page){
			$page = ($should_incr_page)?$this->page+1:$this->page;
			$query .= "{$sep}page={$page}&count={$this->count}";
		}
		return $query;
	}
	public function bindParamToStmt($stmt){
		if($this->hasParams()){
			call_user_func_array(array($stmt, "bind_param"), $this->getParamArray());
		}
		return true;
	}
}
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
class SqlLikeCondition extends SqlCondition{

	function setData($data){
		$this->sql = "{$this->field} LIKE ?";
		$this->codes = "s";
		$this->params = array("%{$data}%");

		$this->active = true;
		return true;
	}
}
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
class SqlEqualCondition extends SqlCondition{

	function setData($data){
		$this->sql = "{$this->field} = ?";
		$this->codes = "s";
		$this->params = array($data);

		$this->active = true;
		return true;
	}
}
?>