<?php
class Page extends Object{
	public $page_name;
	public $min_permission = 100;
	public $tab = "home";
	public $title = "Runn Daily";
	public $common;
	
	private static $_smarty = null;
	
	function __construct($arr = null, $arr_pre = "p_"){
		parent::__construct($arr, $arr_pre);
	}
	
	/**
	 * @return Smarty_Ext
	 */
	public static function getSmarty(){
		if(is_null(self::$_smarty)){
			self::$_smarty = new Smarty_Ext();
		}
		return self::$_smarty;
	}
	
	public static function getPage($script_name){
		$stmt = Database::getDB()->prepare("
			SELECT *
			FROM permissions
			WHERE
				p_page_name = ?
		");
		$stmt->bind_param("s", $script_name);
		$stmt->execute();
		$stmt->store_result();
		
		$row = $stmt->fetch_assoc();
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		if($rows != 1){
			$add_stmt = Database::getDB()->prepare("
				INSERT INTO permissions(p_page_name) VALUES(?)
			");
			$add_stmt->bind_param("s", $script_name);
			$add_stmt->execute();
			$add_stmt->close();
			
			return new Page();
		}
		return new Page($row);
	}
	public static function redirect($page = "/"){
		header("location: http://{$_SERVER["SERVER_NAME"]}{$page}");
		exit;
	}
	public static function getAllPages(){
		$stmt = Database::getDB()->prepare("
			SELECT *
			FROM permissions
		");
		$stmt->execute();
		$stmt->store_result();
		
		$pages = array();
		
		while($row = $stmt->fetch_assoc()){
			$pages[] = new Page($row);
		}
		$stmt->close();
		return $pages;
	}
	public function updatePage(){
		$stmt = Database::getDB()->prepare("
			UPDATE permissions
			SET
				p_min_permission = ?,
				p_title = ?,
				p_new_flag = FALSE,
				p_tab = ?,
				p_common = ?
			WHERE
				p_page_name = ?
		");
		$stmt->bind_param("issss", $this->min_permission, $this->title, $this->tab, $this->common,$this->page_name);
		$stmt->execute() or die("error:{$stmt->error}");
		$stmt->store_result();
		
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		if($rows == 1){
			return true;
		}
		return false;
	}
	public function getTemplateName(){
		return preg_replace("/\/(.*)\.php/", "$1.tpl", $this->page_name);
	}
	public static function getControllerExists($controller){
		return file_exists(SYSTEM_ROOT."/controllers/{$controller}_controller.php");
	}
	
}
?>