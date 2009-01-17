<?php
require("../lib/config.php");

$format = (isset($_GET["format"]))?$_GET["format"]:"html";



if($format == "ajax"){

	$uid = $_GET["uid"];
	$page_no = $_GET["page"];

	$routes = Route::getRoutesForUser($uid, 5, $page_no);

	$smarty->assign("routes", $routes);
	$smarty->assign("uid", $uid);
	$smarty->assign("page_no", $page_no+1);

	echo $smarty->fetch("routes/parts/route_list.tpl");
}
else{
	$parser = new SqlParser();
	$parser->addCondition(new SqlRangeCondition("r_distance"));
	$parser->addCondition(new SqlRangeCondition("r_creation", "FROM_UNIXTIME", "strtotime"));
	$parser->addCondition(new SqlLikeCondition("u_username"));
	$parser->addCondition(new SqlLikeCondition("r_name"));
	$parser->setData($_GET);
	
	$stmt = database::getDB()->prepare("
		SELECT *
		FROM routes, users
		WHERE 
		{$parser->getSQL()} AND
			u_uid = r_uid
	");
	if($parser->hasParams()){
		call_user_func_array(array($stmt, "bind_param"), $parser->getParamArray());
	}
	$stmt->execute();
	$stmt->store_result();

	$routes = array();
	while($row = $stmt->fetch_assoc()){
		$routes[] = Route::fromFetchAssoc($row, true, true);
	}
	$smarty->assign("routes", $routes);
	$smarty->display_master("routes/browse.tpl");
}

class SqlParser{
	private $_conditions = array();
	private $_data_set = false;
	
	public function addCondition($condition){
		$this->_conditions[] = $condition;
	}
	public function setData($arr){
		foreach($this->_conditions as $condition){
			if(!isset($arr[$condition->field]))continue;
			$condition->setData($arr[$condition->field]);
		}
		$this->_data_set = true;
	}
	public function getSQL(){
		if(!$this->_data_set) return "TRUE";
		if(!count($this->_conditions) == 0) return "TRUE";
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
		return $query;
	}
	public function getParamArray(){
		if(!$this->_data_set) return false;
		$params = array(-1);
		$codes = "";
		foreach($this->_conditions as $condition){
			if(!$condition->active)continue;
			$codes .= $condition->codes;
			$params = array_merge($params, $condition->params);
		}
		$params[0] = $codes;
		return $params;
	}
	public function hasParams(){
		foreach($this->_conditions as $condition){
			if($condition->active) return true;
		}
		return false;
	}
}
abstract class SqlCondition{
	public $field;
	public $active = false;

	public $codes;
	public $sql;
	public $params = array();

	abstract public function setData($data);

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
	}
}

class SqlRangeCondition extends SqlCondition{
	private $ph = "?";
	private $func = false;
	private $func_call;

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
		else{
			$this->sql = "{$this->field} <= {$this->ph}";
			$this->codes = "s";
			$this->params = array($data[1]);
		}
		$this->active = true;
	}
}
class SqlEqualCondition extends SqlCondition{

	function setData($data){
		$this->sql = "{$this->field} = ?";
		$this->codes = "s";
		$this->params = array($data);

		$this->active = true;
	}
}
?>