<?php
class SmartyExt extends Smarty{
	
	function __construct(){
		$this->template_dir = SYSTEM_ROOT."/tpl";
		$this->compile_dir = CLASS_ROOT."/smarty/templates_c";
		$this->cache_dir = CLASS_ROOT."/smarty/cache";
		$this->config_dir = CLASS_ROOT."/smarty/configs";
		$this->left_delimiter = "{{";
		$this->right_delimiter = "}}";
	}
	
	public function display_master($tpl){
		$this->assign("page_content", $this->fetch($tpl));
		$this->display("master.tpl");
	}
	public function display_error(){
		$this->display("error/index.tpl");
	}
}
?>