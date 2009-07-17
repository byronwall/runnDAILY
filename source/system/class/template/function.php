<?php
abstract class Template_Function extends Template_Block {
	protected $_function;
	protected $_isBlock = false;
	protected $_compiler;
	
	function __construct(Template_Compiler $compiler){
		$this->_compiler = $compiler;
	}	
	function getBlockName() {
		return $this->_function;
	}
	function handleNewBlock($tag){
		$this->_compiler->addFunctionToSource($this->_function, "block");
		
		$params = var_export($tag->params, true);
		
		return "<?php echo template_block_{$this->_function}({$params}, \$this); ?>";
	}
	abstract function runtime($params, $template);
}