<?php
class SQL{
	private $_bind_vals = array();
	private $_statements = array();
	
	private $_table;
	private $_primary;
	private $_class;
	private $_has_select = false;
	private $_has_limit = false;
	private $_has_debug = false;
	
	private $_tables_classes = array(
		"users" => "user"
	);
	private $_left_joins = array();
	
	function __construct($table = null, $class = null, $primary = "id"){
		$this->_table = $table;
		$this->_primary = $primary;
		$this->_class = $class;
		
		if(isset($table)) $this->_addSQL("FROM {$table}");
	}
	
	function select($fields, $params = null){
		$sql = "SELECT {$fields}";
		$this->_addSQL($sql);
		
		$this->_has_select = true;

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
	function where_in($field, $values){
		if(!is_array($values)) return $this;
		
		$marks = str_repeat("?,", count($values));
		$marks = substr($marks, 0, -1);
		
		$sql = "WHERE {$field} IN({$marks})";
		$this->_addSQL($sql);
		foreach($values as $value){
			$this->_bindValue($value);
		}
		
		return $this;
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
	function leftjoin($table, $left_field, $right_field = null){
		if(isset($right_field)){
			$sql = "LEFT JOIN {$table} ON {$left_field} = {$right_field}";
		}
		else{
			$sql = "LEFT JOIN {$table} USING( {$left_field} )";
		}
		
		$this->_addSQL($sql);
		$this->_left_joins[] = $table;
		
		return $this;
	}
	
	function limit($count = 15){
		if($this->_has_limit) throw new Exception("Only one limit allowed");
		$sql = "LIMIT 0,?";
		$this->_addSQL($sql);
		$this->_bindValue($count);
		
		$this->_has_limit = true;
		
		return $this;
	}
	function page(){
		
	}
	function fetch($primary, $as_object = false){
		return $this->where_eq($this->_primary, $primary)->execute($as_object, false);
	}
	function debug(){
		$sql = $this->_get_full_sql();
		$type = $this->_sql_all_bind_types();
		
		$sections = explode("?", $sql);
		$sql_w_values = "";
		for($i = 0; $i < count($sections)-1; $i++){
			$sql_w_values .= $sections[$i] . $this->_bind_vals[$i];
		}
		
		var_dump($sql, $sql_w_values, $type);
		$this->_has_debug = true;
		
		return $this;
	}
	
	function execute($as_object = false, $arr_on_single = false, $arr_index = null){
		//set some defaults if needed
		if(!$this->_has_select) $this->select("*");
		if(!$this->_has_limit) $this->limit(100);
		
		$sql = $this->_get_full_sql();
		$stmt = Database::getDB()->prepare($sql);
		$types = $this->_sql_all_bind_types();
		
		call_user_func_array(array($stmt, "bind_param"), array_merge(array($types), $this->_bind_vals));
		$stmt->execute() or RoutingEngine::throwException($stmt->error);
		$stmt->store_result();
		
		$results = array();
		while($row = $stmt->fetch_assoc()){
			if($as_object){
				$obj = new $this->_class($row);
				if(count($this->_left_joins)){
					foreach($this->_left_joins as $table){
						$type = $this->_tables_classes[$table]; 
						$obj->{$type} = new $type($row); 
					}
				}
				$results[] = $obj; 
			}
			else{
				if(isset($arr_index)) $results[$row[$arr_index]] = $row;
				else $results[] = $row;
			}
		}
		$stmt->close();
		if($this->_has_debug) var_dump($results);
		
		return ($arr_on_single || count($results) > 1) ? $results : $results[0];
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
			case "LEFT": return 2;
			case "WHERE": return 3;
			case "ORDER":return 4;
			case "LIMIT": return 5;
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
			case "LEFT":
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
		$keys = array("SELECT", "FROM", "LEFT JOIN", "WHERE", "ORDER BY", "LIMIT");
		$glue = array(", ",", ",null," AND ", ", ", ", ");
		
		ksort($this->_statements);
		
		foreach($this->_statements as $key=>$sql_type){
			//left join
			if($key == 2){
				foreach($sql_type as $join){
					$sql[] = "LEFT JOIN " . $join;
				}
			}
			else{
				$sql[] = $keys[$key] . "\t" . implode($glue[$key], $sql_type);				
			}
		}
		$full_sql = implode("\n", $sql);
		
		return $full_sql;
	}
}
?>