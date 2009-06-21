<?php
class SQL{
	private $_bind_vals = array();
	private $_statements = array();
	
	private $_table;
	private $_primary;
	
	function __construct($table, $primary = "id"){
		$this->_table = $table;
		$this->_primary = $primary;
	}
	
	function select($fields, $params = null){
		$sql = "SELECT {$fields}";
		$this->_addSQL($sql);

		return $this;
	}
	function from($table){
		$sql = "FROM {$table}";
		$this->_addSQL($sql);
		
		return $this;
	}
	private function _where_op($op, $field, $value){
		$sql = "WHERE {$field} {$op} ?";
		$this->_addSQL($sql);
		$this->_bindValue($value);
		
		return $this;
	}
	function where_eq($field, $value){
		return $this->_where_op("=", $field, $value);
	}
	function where_lt($field, $value){
		return $this->_where_op("<", $field, $value);
	}
	function where_gt($field, $value){
		return $this->_where_op(">", $field, $value);
	}
	function where_lt_eq($field, $value){
		return $this->_where_op("<=", $field, $value);
	}
	function where_gt_eq($field, $value){
		return $this->_where_op(">=", $field, $value);
	}
	function where_between($field, $lower, $upper){
		$val_1 = min($lower, $upper);
		$val_2 = max($lower, $upper);
		
		$sql = "WHERE {$field} BETWEEN ? AND ?";
		$this->_addSQL($sql);
		$this->_bindValue($val_1);
		$this->_bindValue($val_2);
		
		return $this;
	}
	function where_in(){
		
	}
	function where($sql, $params = null){
		$this->_addSQL("WHERE " . $sql);
		
		$param_count = func_num_args();
		for($i = 1; $i<$param_count; $i++){
			$arg = func_get_arg($i);
			$this->_bindValue($arg);
		}
		
		return $this;
	}
	function limit($count){
		$sql = "LIMIT 0,?";
		$this->_addSQL($sql);
		$this->_bindValue($count);
		
		return $this;
	}
	function page(){
		
	}
	function execute(){
		$sql = $this->_get_full_sql();
		$stmt = Database::getDB()->prepare($sql);
		$types = $this->_sql_all_bind_types();
		
		var_dump($sql, $types);
		
		call_user_func_array(array($stmt, "bind_param"), array_merge(array($types), $this->_bind_vals));
		$stmt->execute() or RoutingEngine::throwException($stmt->error);
		$stmt->store_result();
		
		$results = array();
		while($row = $stmt->fetch_assoc()){
			$results[] = $row;
		}
		$stmt->close();
		return $results;
	}
	function orderby($field, $desc = true){
		$dir = ($desc)?"DESC": "ASC";
		$sql = "ORDER BY {$field} {$dir}";

		$this->_addSQL($sql);
		
		return $this;
	}
	
	private function _bindValue($value){
		$this->_bind_vals[] = $value;
	}
	private function _addSQL($sql){
		$type = $this->_sql_type($sql);
		$this->_statements[$type][] = $this->_sql_vaue($sql);
	}
	private function _sql_type($sql){
		$op = explode(" ", $sql);
		switch($op[0]){
			case "SELECT": return 0;
			case "FROM": return 1;
			case "WHERE": return 2;
			case "ORDER":return 3;
			case "LIMIT": return 4;
		}
	}
	private function _sql_vaue($sql){
		$op = explode(" ", $sql, 2);
		switch($op[0]){
			case "SELECT": 
			case "FROM": 
			case "WHERE": 
			case "LIMIT": 
				return $op[1];
				break;
			case "ORDER":
				$with_by = explode(" ", $op[1], 2);
				return $with_by[1];
		}
	}
	private function _sql_bind_type($value){
		if(is_int($value)) return "i";
		if(is_numeric($value)) return "d";
		return "s";
	}
	private function _sql_all_bind_types(){
		$output = "";
		foreach($this->_bind_vals as $value){
			$output .= $this->_sql_bind_type($value);
		}
		return $output;
	}
	private function _get_full_sql(){
		$sql = array();
		$keys = array("SELECT", "FROM", "WHERE", "ORDER BY", "LIMIT");
		$glue = array(", ",", "," AND ", ", ", ", ");
		
		ksort($this->_statements);
		
		foreach($this->_statements as $key=>$sql_type){
			$sql[] = $keys[$key] . "\t" . implode($glue[$key], $sql_type);
		}
		$full_sql = implode("\n", $sql);
		
		return $full_sql;
	}
}
?>