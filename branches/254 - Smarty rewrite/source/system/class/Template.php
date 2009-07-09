<?php
class Template {
	public $require_compile = true;
	public $compile_dir;
	public $template_dir;
	
	private $_compiler;
	private $_resource_manager;
	
	function __construct() {
		//safe defaults
		$this->compile_dir = SYSTEM_ROOT . "/tpl_compiled";
		$this->template_dir = SYSTEM_ROOT . "/tpl";
		
		$this->_resource_manager = new Template_ResourceManager ( $this );
		$this->_compiler = new Template_Compiler ( $this->_resource_manager );
	}
	function assign($name, $value) {
		throw new Template_Exception ( "Not supported yet." );
	}
	function display($template_name) {
		$output = $this->fetch ( $template_name );
		
		echo $output;
	}
	function fetch($template_name) {
		if ($this->require_compile) {
			$this->_compile ( $template_name );
		}
		$output = $this->_processCompiledTemplate ( $template_name );
		
		return $output;
	}
	private function _compile($template_name) {
		$this->_compiler->compile ( $template_name );
	}
	private function _processCompiledTemplate($template_name) {
		$filename = $this->_resource_manager->getCompiledPath ( $template_name );
		
		include_once ($filename);
		
		return true;
	}
}
class Template_Compiler {
	/**
	 * @var Template_ResourceManager
	 */
	private $_resource_manager;
	private $_blockProcessor;
	
	private $_compiledPieces = array ();
	
	function __construct(Template_ResourceManager $resource_manager) {
		$this->_resource_manager = $resource_manager;
		$this->_blockProcessor = new Template_CompilerBlockProcessor ( );
	}
	function compile($template_name) {
		$filename = $this->_resource_manager->getTemplateFilename ( $template_name );
		
		$contents = file_get_contents ( $filename );
		
		$this->_processSource ( $contents );
		
		$bytes = $this->_writeCompiledFile ( $template_name );
		if ($bytes === false)
			throw new Template_Exception ( "Error compiling source" );
		return $bytes;
	}
	private function _writeCompiledFile($template_name) {
		$filename = $this->_resource_manager->getCompiledPath ( $template_name );
		
		$compiled_source = implode ( "\n", $this->_compiledPieces );
		
		return file_put_contents ( $filename, $compiled_source );
	}
	private function _processSource($contents) {
		$ldl = "{{";
		$rdl = "}}";
		
		$ld = preg_quote ( $ldl, "/" );
		$rd = preg_quote ( $rdl, "/" );
		
		$matches = array ();
		
		preg_match_all ( "/{$ld}\s*(.*?)\s*{$rd}/s", $contents, $matches );
		
		//this just has the tag and not the delims
		$tags = $matches [1];
		$this->_blockProcessor->init ( $tags );
		
		//only grab text by splitting at tags
		$text_blocks = preg_split ( "/{$ld}.*?{$rd}/s", $contents );
		
		foreach ( $text_blocks as $text ) {
			$this->_compiledPieces [] = $text;
			$this->_compiledPieces [] = $this->_blockProcessor->processNextTag ();
		}
		
		$this->_blockProcessor->finalize ();
		
		return true;
	}
}
class Template_Block_If extends Template_Block {
	protected $_mids = array ("else", "elseif" );
	protected $_isBlock = true;
	protected $_block = "if";
	
	function handleNewBlock() {
		//TODO: Handle condition
		return "<?php if(true): ?>";
	}
	/**
	 * 
	 */
	function handleEndBlock() {
		return "<?php endif ?>";
	}
	
	/**
	 * @param $block
	 */
	function handleMiddleBlock($block) {
		switch($block){
			case "else":
				$compiled = "<?php else: ?>";
				break;
			case "elseif":
				//TODO: Really handle condition
				$compiled = "<?php elseif(true): ?>";
				break;
		}
		return $compiled;
	}
}
class Template_CompilerBlockProcessor {
	private $_tags;
	private $_init = false;
	private $_stacks = array ();
	
	function __construct() {
	
	}
	function init($tags) {
		$this->_tags = $tags;
		
		$this->_init = true;
	}
	function processNextTag() {
		if (! $this->_init)
			throw new Exception ( "Tags must be init'ed before processing." );
		
		$tag = next ( $this->_tags );
		$tag_data = new Template_TagData($tag);
		
		$inputs = explode ( " ", $tag );
		$block = $inputs [0];
		$params = array_slice ( $inputs, 1 );
		//check the first character
		switch (substr ( $block, 0, 1 )) {
			case "/" :
				//process end tag
				$compiled = $this->_processEndTag ( substr($block, 1) );
				break;
			case "*" :
				$compiled = "";
				//process comment
				break;
			case "$":
				//
				$compiled = $block;
				break;
			default :
				$compiled = $this->_determineNextBlockType ( $block );
				break;
		}
		return $compiled;
	}
	private function _processEndTag($block) {
		$expected = array_pop ( $this->_stacks );
		
		if ($expected->getBlockName () != $block)
			throw new Template_Exception ( "Tag mismatch" );
		
		return $expected->handleEndBlock ();
	}
	private function _determineNextBlockType($block) {
		//TODO: Clean this up to a single exit point.
		if (count ( $this->_stacks )) {
			$open_block = end ( $this->_stacks );
			
			if ($open_block->checkValidMidBlock ( $block )) {
				return $open_block->handleMiddleBlock ( $block );
			}
		}
		return $this->_processNewBlock ( $block );
	}
	private function _processNewBlock($block) {
		$class = "Template_Block_{$block}";
		
		$allowed = array("if");
		
		if(!in_array($block, $allowed)) return false;
		
		$new_block = new $class ( );
		if ($new_block->getIsBlock ()) {
			$this->_stacks [] = $new_block;
		}
		
		return $new_block->handleNewBlock();
	}
	function finalize() {
		if (count ( $this->_stacks )) {
			throw new Template_Exception ( "Not all blocks are closed" );
		}
	}
}

abstract class Template_Block {
	protected $_block;
	protected $_isBlock = false;
	protected $_mids = array ();
	
	function checkValidMidBlock($block) {
		return in_array ( $block, $this->_mids );
	}
	function getBlockName() {
		return $this->_block;
	}
	function getIsBlock() {
		return $this->_isBlock;
	}
	abstract function handleEndBlock();
	abstract function handleNewBlock();
	function handleMiddleBlock($block){
		throw new Template_Exception("{$block} has valid middle tags but no handler.");
	}

}
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
}
class Template_Exception extends Exception {
}