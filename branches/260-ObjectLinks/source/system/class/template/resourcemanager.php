<?php
/**
 * Class is responsible for determining file names and paths for templates.
 *
 */
class Template_ResourceManager {
	/**
	 * @var Template
	 */
	private $_template;
	function __construct(Template $template) {
		$this->_template = $template;
	}
	/**
	 * Wrapper for file exists which throws an exception if it does not exist.
	 * @param string $filename		Filename to check
	 * @return bool					Always returns true or throws an exception.
	 */
	private function _force_exists($filename) {
		if (! file_exists ( $filename ))
			throw new Template_Exception ( "Template file does not exist." );
		
		return true;
	}
	/**
	 * Function to get a filename from a template name.
	 * 
	 * @param string $template_name		Template name.
	 * @return string					Resolved filename for the template.
	 */
	public function getTemplateFilename($template_name) {
		return $this->_template->template_dir . "/" . $template_name;
	}
	/**
	 * Function to get the filename of a compiled template.
	 * 
	 * @param string $template_name		Template name.
	 * @return string					Resolved filename for the compiled template
	 */
	public function getCompiledPath($template_name) {
		return $this->_template->compile_dir . "/" . $template_name . ".php";
	}
	/**
	 * Function to get the directory of a template.
	 * 
	 * @param string $template_name		Template name.
	 * @return string					Resolved directory name.
	 */
	public function getTemplateDir($template_name){
		//removes the ending backslash
		$regex = "/(.*(?:\/|\\\)).*\..*$/";
		$matches = array();
		preg_match($regex, $template_name, $matches);
		
		$dir = array_safe($matches, 1, "");
		
		return $this->_template->compile_dir . "/" . $dir;
	}
	/**
	 * Function gets all template files from a directory.
	 * @param string $dir		Directory to search.  Defaults to $template_dir
	 * @return array			Array of filenames
	 */
	public function getAllTemplateFiles($dir = null){
		//Default to the template dir.
		if($dir == null){
			$dir = $this->_template->template_dir;
		}
		$files = $this->_getTemplatesInDir($dir);
		return $files;		
	}
	/**
	 * Recursive function to find template files.
	 * 
	 * @param string $dir		Relative path for directory to search.
	 * @param string $prefix	Used for recursion to generate full path
	 * @return array			Array of file names.
	 */
	private function _getTemplatesInDir($dir, $prefix = ""){
		$files = array();
		$handle = opendir($dir);
		//Don't need to search in these.
		$ignore = array(".", "..", ".svn");
		while($handle){
			$file = readdir($handle);
			if($file === false) break;
			if(in_array($file, $ignore)) continue;
			
			$full_file = $dir . "/" . $file; 
			
			if(is_dir($full_file)){
				//Recurses to find templates.
				$files = array_merge($files, $this->_getTemplatesInDir($full_file, $prefix ."/". $file));
				continue;
			}
			$files[] = $prefix . "/" . $file;
		}
		return $files;
	}
}
