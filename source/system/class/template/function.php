<?php
/**
 * Abstract class for template functions.  This extends the block class.
 *
 */
abstract class Template_Function extends Template_Block {
	/**
	 * Name of the function.  Should match class name.
	 * @var string
	 */
	protected $_function;
	/**
	 * Indicates that there should not be an end tag.
	 * @var bool
	 */
	protected $_isBlock = false;
	
	/* (non-PHPdoc)
	 * @see source/system/class/template/Template_Block#getBlockName()
	 * 
	 * Returns the function name instead of the block name.
	 */
	final function getBlockName() {
		//returns the function name instead of the block name
		return $this->_function;
	}
	/* (non-PHPdoc)
	 * @see source/system/class/template/Template_Block#handleNewBlock($data)
	 * 
	 * Adds the function to the compiler stack.  Then simply echoes a call to the function.
	 */
	final function handleNewBlock($tag) {
		//adds the function to the compiler and returns the code to execute it at runtime
		$this->_compiler->addFunctionToSource ( strtolower ( $this->_function ), "block" );
		
		//export is used so that the array is valid PHP code.
		$params = var_export ( $tag->params, true );
		
		return "<?php echo template_block_{$this->_function}({$params}, \$this); ?>";
	}
	/**
	 * This is the important part.  This function will be copied into the compiled source.
	 * If you look above you can see that its output is simply echoed into the final output.
	 * 
	 * @param Array $params				Array of parameters passed from the template source.
	 * @param Template $template		Reference to the runtime template instance.
	 * @return string					Output for the final result.  Presumably valid HTML
	 */
	abstract function runtime($params, $template);
}