<?php
//TODO: delete this file;
class Module extends Object{
	public $content;
	public $id;
	public $title;
	public $size;
	
	public $name;
	public $code;
	public $desc;
	public $loc;
	
	public static $hash;
	
	function __construct($arr = null, $arr_pre = "m_"){
		parent::__construct($arr, $arr_pre);
	}
	
	/**
	 * @param int $id
	 * @param string $title
	 * @param int $size
	 * @param string $content
	 * @return Module
	 */
	public static function fromVariables($id = "", $title = "Module", $size = null, $content = ""){
		$module = new Module();
		
		$module->content = $content;
		$module->id = $id;
		$module->title = $title;
		$module->size = $size;
		
		return $module;
	}
	public function createModule(){
		$stmt = Database::getDB()->prepare("
			INSERT INTO modules
			SET
				m_name = ?
		");
		$stmt->bind_param("s", $this->name);
		$stmt->execute();
		$stmt->close();
		
		return true;
	}
	/**
	 * @return array, Module
	 */
	public static function getAllModules($page = null){
		$page_cond = (is_null($page))?"true":"m_loc = \"{$page}\"";
		
		$result = Database::getDB()->query("
			SELECT *
			FROM modules
			WHERE {$page_cond}
		");
		$modules = array();
		while($row = $result->fetch_assoc()){
			$mod = new Module($row);
			$modules[$mod->code] = $mod;
		}
		$result->close();
		
		return $modules;
	}
	public function update(){
		$stmt = Database::getDB()->prepare("
			UPDATE modules
			SET
				m_title = ?,
				m_desc = ?,
				m_loc = ?
			WHERE
				m_code = ?
		");
		$stmt->bind_param("sssi", $this->title, $this->desc, $this->loc, $this->code);
		$stmt->execute();
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		return $rows == 1;
		
	}
}
?>