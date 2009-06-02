<?php
class Page extends Object{
	public $page_name;
	public $title = "runnDAILY";
	public $common;
	public $perm_code = "PV__100";
	
	private static $_smarty = null;
	
	function __construct($arr = null, $arr_pre = "p_"){
		parent::__construct($arr, $arr_pre);
	}
	
	/**
	 * @param string $script_name
	 * @return Page
	 */
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
		
		return new Page($row);
	}
	public static function redirect($page = "/"){
		if(substr($page, 0,1)!= "/"){
			$page = "/".$page;
		}
		header("location: http://{$_SERVER["SERVER_NAME"]}{$page}");
		exit;
	}
	/**
	 * @return array[int]Page
	 */
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
				p_perm_code = ?,
				p_title = ?,
				p_tab = ?,
				p_common = ?
			WHERE
				p_page_name = ?
		");
		$stmt->bind_param("sssss", $this->perm_code, $this->title, $this->tab, $this->common,$this->page_name);
		$stmt->execute() or die("error:{$stmt->error}");
		$stmt->store_result();
		
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		if($rows == 1){
			return true;
		}
		return false;
	}
	/**
	 * @return string	Formatted name of the template with correct extension
	 */
	public function getTemplateName(){
		return preg_replace("/\/(.*)(\.php)?/", "$1.tpl", $this->page_name);
	}
	public function createPage(){
		$stmt = Database::getDB()->prepare("
			INSERT INTO permissions
			SET
				p_page_name = ?,
				p_perm_code = ?
		");
		$stmt->bind_param("ss", $this->page_name, $this->perm_code);
		$stmt->execute();
		
		$stmt->close();
		
		return true;
	}
	
}
?>