<?php
//require_once(CLASS_ROOT."\hash_module.php");

class Module extends Object{
	public $content;
	public $id;
	public $title;
	public $size;
	public $name;
	
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
}
?>