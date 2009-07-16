<?php
class Template {
	public $require_compile = true;
	public $compile_dir;
	public $template_dir;
	public $master;
	
	private $_compiler;
	private $_resource_manager;
	private $_vars = array ();
	
	function __construct() {
		//safe defaults
		$this->compile_dir = SYSTEM_ROOT . "/tpl_compiled";
		$this->template_dir = SYSTEM_ROOT . "/tpl";
		$this->master = "master.tpl";
		
		$this->_resource_manager = new Template_ResourceManager ( $this );
	}
	function assign($name, $value) {
		$this->_vars [$name] = $value;
		
		return true;
	}
	function display($template_name) {
		$output = $this->fetch ( $template_name, $master );
		
		echo $output;
	}
	function fetch($template_name) {
		if ($this->require_compile) {
			$this->_compile ( $template_name );
		}
		$output = $this->_processCompiledTemplate ( $template_name );
		
		return $output;
	}
	private function _processCompiledTemplate($template_name) {
		$filename = $this->_resource_manager->getCompiledPath ( $template_name );
		
		ob_start ();
		include_once ($filename);
		$_template_output = ob_get_contents ();
		ob_end_clean ();
		
		return $_template_output;
	}
	public function display_master($tpl) {
		if ($this->require_compile) {
			$this->_compile ( $this->master, $tpl );
		}
		$output = $this->_processCompiledTemplate ( $tpl );
		echo $output;
	}
	private function _compile($tpl, $save = null) {
		$this->_compiler = new Template_Compiler ( $this->_resource_manager );
		
		return $this->_compiler->compile ( $tpl, $save );
	}
}
class Template_Exception extends Exception {
}