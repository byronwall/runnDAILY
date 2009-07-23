<?php
/**
 * Abstract class for template functions.  This extends the block class.
 *
 */
abstract class Template_Function extends Template_Block {
	protected $_function;
	protected $_isBlock = false;
	protected $_compiler;
	
	final function __construct(Template_Compiler $compiler){
		$this->_compiler = $compiler;
	}	
	final function getBlockName() {
		//returns the function name instead of the block name
		return $this->_function;
	}
	final function handleNewBlock($tag){
		//adds the function to the compiler and returns the code to execute it at runtime
		$this->_compiler->addFunctionToSource(strtolower($this->_function), "block");
		
		//export is used so that the array is valid PHP code.
		$params = var_export($tag->params, true);
		
		return "<?php echo template_block_{$this->_function}({$params}, \$this); ?>";
	}
	abstract function runtime($params, $template);
}