<?php
class Smarty_Ext extends Smarty{
	
	function __construct(){
		$this->template_dir = SITE_ROOT."/tpl";
		$this->compile_dir = SITE_ROOT."/_smarty/templates_c";
		$this->cache_dir = SITE_ROOT."/_smarty/cache";
		$this->config_dir = SITE_ROOT."/_smarty/configs";
		$this->left_delimiter = "{{";
		$this->right_delimiter = "}}";
	}
	
	public function display_master($tpl){
		$this->assign("page_content", $this->fetch($tpl));
		$this->display("master.tpl");
	}
}
?>