<?php
/**
 * Template class is the entry point for the template features.  It behaves identically
 * to Smarty in terms of syntax.
 *
 */
class Template {
	/**
	 * Indicates whether or not templates should be compiled.
	 * @var bool
	 */
	public $require_compile = true;
	/**
	 * If compiling a template, whether or not to short circuit by checking modification date
	 * @var bool
	 */
	public $require_dates_compile = true;
	/**
	 * Directory to place compiled templates.
	 * @var string
	 */
	public $compile_dir;
	/**
	 * Directory where template files are located.
	 * @var string
	 */
	public $template_dir;
	/**
	 * Template name of a master template.
	 * @var string
	 */
	public $master;
	
	private $_compiler;
	private $_resource_manager;
	private $_vars = array ();
	
	function __construct() {
		$this->compile_dir = SYSTEM_ROOT . "/tpl_compiled";
		$this->template_dir = SYSTEM_ROOT . "/tpl";
		$this->master = "master.tpl";
		
		//TODO: not sure how I feel about this reference.  tried to separate things.
		$this->_resource_manager = new Template_ResourceManager ( $this );
	}
	/**
	 * Function assigns a variable to current template.
	 * 
	 * @param strign $name		Name used to retrieve variable in templates
	 * @param mixed $value		Value to be recalled inside the template
	 * @return bool				This function always returns true
	 */
	function assign($name, $value) {
		$this->_vars [$name] = $value;
		return true;
	}
	/**
	 * Function is used to display a given template.
	 * 
	 * @param string $template_name		Name of the template relative to the template directory.
	 * @return bool						This functions always returns true.
	 */
	function display($template_name) {
		//fetch will output the results.
		$output = $this->fetch ( $template_name, true );
		
		return true;
	}
	/**
	 * Function will load the given template and return the results.
	 * 
	 * @param string $template_name		Name of the template relative to the template directory.
	 * @param bool $display				Whether or not to display the results.
	 * @return string					Returns results of the template parsing
	 */
	function fetch($template_name, $display = false) {
		$this->compile ( $template_name );
		$filename = $this->_resource_manager->getCompiledPath ( $template_name );
		
		//the errors are changed to avoid issues with array indices not set in $_var
		$error = error_reporting ( E_ERROR | E_WARNING | E_PARSE );
		if ($display) {
			//supposedly it is faster to avoid output buffering if not needed.
			include_once ($filename);
			error_reporting ( $error );
			return true;
		}
		ob_start ();
		include_once ($filename);
		$_template_output = ob_get_contents ();
		ob_end_clean ();
		
		error_reporting ( $error );
		return $_template_output;
	}
	/**
	 * Displays a template in the context of a master template file.  This is fairly specific
	 * to our implementation of templating.
	 * 
	 * @param string $tpl		Name of the template relative to the template directory.
	 * @return bool				Always returns true.
	 */
	public function display_master($tpl) {
		$this->assign("tpl", $tpl);
		$this->display($this->master);
		
		return true;
	}
	/**
	 * Function is a simple wrapper around the compiling class.  This function attempts to 
	 * short circuit the compiling process is possible.
	 * 
	 * @param $tpl			Name of the template relative to the template directory.
	 * @param $save			Name to save file as.  Only used in conjunction with master templates.
	 * @return mixed		I don't know.  Nothing important.
	 */
	public function compile($tpl, $save = null) {
		//quick check to make sure we need to compile
		if (! $this->require_compile) {
			return true;
		}
		$template_name = $this->_resource_manager->getTemplateFilename ( $tpl );
		$compiled_name = $this->_resource_manager->getCompiledPath ( $tpl );
		$compile_file = ! file_exists ( $compiled_name );
		
		//longer check based on modification times to avoid compiling
		if ($compile_file || $this->require_dates_compile || filemtime ( $template_name ) > filemtime ( $compiled_name )) {
			$this->_compiler = new Template_Compiler ( $this->_resource_manager );
			
			return $this->_compiler->compile ( $tpl, $save );
		}
		return $compiled_name;
	}
	/**
	 * Compiles are templates in the template directory.  Used to avoid checking if compiled
	 * files exist in a production environment.
	 * 
	 * @return bool		Always returns true.
	 */
	public static function compileAllTemplates() {
		$template = new Template ( );
		$resource = new Template_ResourceManager ( $template );
		
		$files = $resource->getAllTemplateFiles ();
		foreach ( $files as $file ) {
			echo $file . "<bR>";
			$compiler = new Template_Compiler ( $resource );
			$compiler->compile ( $file );
		}
		return true;
	}
	/**
	 * This includes a file in place.  This is only intended to be used for the include_runtime
	 * block.  This is the only way to include files with variable template names.  Do not call
	 * this function directly unless creating a new block.
	 * 
	 * @param string $tpl		Name of the template relative to the template directory.
	 * @return bool				Always returns true.
	 */
	private function _include($tpl){
		$this->compile ( $tpl );
		$filename = $this->_resource_manager->getCompiledPath($tpl);
		
		//this avoids the hoopla of the other include calls because this function is only called from inside another template
		include_once ($filename);
		
		return true;
	}
}
/**
 * Generic exception to show the issue was inside the template system.
 * 
 */
class Template_Exception extends Exception {
}