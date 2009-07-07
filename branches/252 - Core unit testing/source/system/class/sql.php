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
	
	/**
	 * @param $fields
	 * @return SQL
	 */
	function select($fields){
		$sql = "SELECT {$fields}";
		$this->_addSQL($sql);
		
		$this->_has_select = true;

		return $this;
	}
	/**
	 * @param $table
	 * @return SQL
	 */
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
	/**
	 * @param $field
	 * @param $value
	 * @return SQL
	 */
	function where_eq($field, $value){
		return $this->_where_op("=", $field, $value);
	}
	/**
	 * @param $field
	 * @param $value
	 * @return SQL
	 */
	function where_lt($field, $value){
		return $this->_where_op("<", $field, $value);
	}
	/**
	 * @param $field
	 * @param $value
	 * @return SQL
	 */
	function where_gt($field, $value){
		return $this->_where_op(">", $field, $value);
	}
	/**
	 * @param $field
	 * @param $value
	 * @return SQL
	 */
	function where_lt_eq($field, $value){
		return $this->_where_op("<=", $field, $value);
	}
	/**
	 * @param $field
	 * @param $value
	 * @return SQL
	 */
	function where_gt_eq($field, $value){
		return $this->_where_op(">=", $field, $value);
	}
	/**
	 * @param $field
	 * @param $lower
	 * @param $upper
	 * @return SQL
	 */
	function where_between($field, $lower, $upper){
		$val_1 = min($lower, $upper);
		$val_2 = max($lower, $upper);
		
		$sql = "WHERE {$field} BETWEEN ? AND ?";
		$this->_addSQL($sql);
		$this->_bindValue($val_1);
		$this->_bindValue($val_2);
		
		return $this;
	}
	/**
	 * @param $field
	 * @param $values
	 * @return SQL
	 */
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
	/**
	 * @param $field
	 * @param $value
	 * @return SQL
	 */
	function where_like($field, $value){
		$sql = "WHERE {$field} LIKE ?";
		$this->_addSQL($sql);
		$this->_bindValue("%{$value}%");
		
		return $this;
	}
	/**
	 * @param $sql
	 * @param $params
	 * @return SQL
	 */
	function where($sql, $params = null){
		$this->_addSQL("WHERE " . $sql);
		
		$param_count = func_num_args();
		for($i = 1; $i<$param_count; $i++){
			$arg = func_get_arg($i);
			$this->_bindValue($arg);
		}
		
		return $this;
	}
	/**
	 * @param $table
	 * @param $left_field
	 * @param $right_field
	 * @param $nest_result
	 * @return SQL
	 */
	function leftjoin($table, $left_field, $right_field = null, $nest_result = false){
		if(isset($right_field)){
			$sql = "LEFT JOIN {$table} ON {$left_field} = {$right_field}";
		}
		else{
			$sql = "LEFT JOIN {$table} USING( {$left_field} )";
		}
		
		$this->_addSQL($sql);
		if($nest_result) $this->_left_joins[] = $table;
		
		return $this;
	}
	
	/**
	 * @param $count
	 * @return SQL
	 */
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
	/**
	 * @param $primary
	 * @param $as_object
	 * @return mixed
	 */
	function fetch($primary, $as_object = false){
		return $this->where_eq($this->_primary, $primary)->execute($as_object, false);
	}
	/**
	 * @return SQL
	 */
	function debug(){
		$sql = $this->_get_full_sql();
		$type = $this->_sql_all_bind_types();
		
		$sections = explode("?", $sql);
		$sql_w_values = "";
		for($i = 0; $i < count($sections)-1; $i++){
			$sql_w_values .= $sections[$i] . $this->_bind_vals[$i];
		}
		
		//immediately return known information
		var_dump($sql, $sql_w_values, $type);
		//set a flag so the results are dumped as well (handled in execute)
		$this->_has_debug = true;
		
		return $this;
	}
	
	/**
	 * @param $as_object
	 * @param $arr_on_single
	 * @param $arr_index
	 * @return mixed
	 */
	function execute($as_object = false, $arr_on_single = false, $arr_index = null){
		//set some defaults if needed
		if(!$this->_has_select) $this->select("*");
		if(!$this->_has_limit) $this->limit(100);
		
		$sql = $this->_get_full_sql();
		if(count($this->_bind_vals)){
		
		$stmt = Database::getDB()->prepare($sql);
		$types = $this->_sql_all_bind_types();
		
		call_user_func_array(array($stmt, "bind_param"), array_merge(array($types), $this->_bind_vals));
		$stmt->execute() or RoutingEngine::throwException($stmt->error);
		$stmt->store_result();
		}
		else{
			$stmt = Database::getDB()->query($sql);
		}
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
		$return = (!$arr_on_single && count($results) == 1) ? $results[0] : $results; 
		
		if($this->_has_debug) var_dump($return);
		return $return;
	}
	/**
	 * @param $params
	 * @return SQL
	 */
	function wrap_value($params){
		$sql = end($this->_sql_stack);
		
		$sql_parts = explode("?", $sql);
		
		$pos = 0;
		$sql_out = "";
		foreach(func_get_args() as $sql_arg){
			$sql_out .= $sql_parts[$pos++] . $sql_arg . "(?)";
		}
		$remain = count($sql_parts) - $pos - 1;
		if($remain){
			$sql_out .= implode("?", array_slice($sql_parts, $pos));
		}
		array_splice($this->_sql_stack, -1, 1, $sql_out);
		
		return $this;
	}
	
	/**
	 * @param $field
	 * @param $desc
	 * @return SQL
	 */
	function orderby($field, $desc = true){
		$dir = ($desc)?"DESC": "ASC";
		$sql = "ORDER BY {$field} {$dir}";

		$this->_addSQL($sql);
		
		return $this;
	}
	
	private function _bindValue($value){
		$this->_bind_vals[] = $value;
	}
	private $_sql_stack = array();
	private function _addSQL($sql){
		$this->_sql_stack[] = $sql;
	}
	private function _sql_type($sql){
		$op = explode(" ", $sql);
		switch($op[0]){
			case "SELECT":
				return 0;
			case "FROM": return 1;
			case "LEFT": return 2;
			case "WHERE": return 3;
			case "ORDER":return 4;
			case "LIMIT": return 5;
		}
		throw new Exception("The type is not defined");
	}
	private function _sql_vaue($sql){
		$op = explode(" ", $sql, 2);
		switch($op[0]){
			case "SELECT": 
			case "FROM": 
			case "WHERE": 
			case "LIMIT": 
				return $op[1];
			case "ORDER":
			case "LEFT":
				$with_by = explode(" ", $op[1], 2);
				return $with_by[1];
		}
		throw new Exception("The call was not parsed");
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
	private function _process_sql(){
		$this->_statements = array();
		
		foreach($this->_sql_stack as $sql){
			$type = $this->_sql_type($sql);
			$this->_statements[$type][] = $this->_sql_vaue($sql);
		}
		
		return true;
	}
	private function _get_full_sql(){
		$this->_process_sql();		
		
		$sql = array();
		
		//full statement with index corresponding to above
		$keys = array("SELECT", "FROM", "LEFT JOIN", "WHERE", "ORDER BY", "LIMIT");
		
		//how to join elements together
		$glue = array(", ",", ",null," AND ", ", ", ", ");
		
		//they may not be in order yet
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