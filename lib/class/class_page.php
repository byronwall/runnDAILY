<?php
class Page extends Object{
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
	public static function redirect($page){
		header("location: http://{$_SERVER["SERVER_NAME"]}{$page}");
		exit;
	}
	public static function getAllPages(){
		$stmt = database::getDB()->prepare("
			SELECT *
			FROM permissions
		");
		$stmt->execute();
		$stmt->store_result();
		
		$pages = array();
		
		while($row = $stmt->fetch_assoc()){
			$pages[] = new Page($row, "p_");
		}
		$stmt->close();
		return $pages;
	}
	public function updatePage(){
		$stmt = database::getDB()->prepare("
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
	
}
?>