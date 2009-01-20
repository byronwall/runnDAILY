<?php
class Page{
	var $page_name;
	var $min_permission = 100;
	var $tab = "home";
	var $title = "Runn Daily";
	public $common;
	
	public static function fromFetchAssoc($row){
		$page = new Page();
		
		$page->min_permission = isset($row["p_min_permission"])? $row["p_min_permission"] : null;
		$page->page_name = isset($row["p_page_name"])? $row["p_page_name"] : null;
		$page->tab = isset($row["p_tab"])? $row["p_tab"] : null;
		$page->title = isset($row["p_title"])? $row["p_title"] : null;
		$page->common = isset($row["p_common"])? $row["p_common"] : null;
		
		return $page;
	}
	public static function getPage($script_name){
		$stmt = database::getDB()->prepare("
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
			$add_stmt = database::getDB()->prepare("
				INSERT INTO permissions(p_page_name) VALUES(?)
			");
			$add_stmt->bind_param("s", $script_name);
			$add_stmt->execute();
			$add_stmt->close();
			
			return new Page();
		}
		return Page::fromFetchAssoc($row);
	}
	public $modules = array();
	public function addModule($module){
		$this->modules[] = call_user_func("Module::draw_{$module}");
	}
	
}
?>