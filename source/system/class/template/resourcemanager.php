<?php
class Template_ResourceManager {
	/**
	 * @var Template
	 */
	private $_template;
	function __construct(Template $template) {
		$this->_template = $template;
	}
	function getTemplateHandle($template_name) {
		$filename = $this->getTemplateFilename ( $template_name );
		$this->_force_exists ( $filename );
		
		return fopen ( $filename );
	}
	private function _force_exists($filename) {
		if (! file_exists ( $filename ))
			throw new Template_Exception ( "Template file does not exist." );
		
		return true;
	}
	public function getTemplateFilename($template_name) {
		return $this->_template->template_dir . "/" . $template_name;
	}
	public function getCompiledPath($template_name) {
		return $this->_template->compile_dir . "/" . $template_name . ".php";
	}
	public function getTemplateDir($template_name){
		$regex = "/(.*(?:\/|\\\)).*\..*$/";
		$matches = array();
		preg_match($regex, $template_name, $matches);
		
		$dir = array_safe($matches, 1, "");
		
		return $this->_template->compile_dir . "/" . $dir;
	}
}
