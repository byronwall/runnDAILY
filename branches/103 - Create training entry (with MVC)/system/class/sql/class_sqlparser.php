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
?>