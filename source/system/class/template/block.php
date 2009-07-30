<?php
abstract class Template_Block {
	/**
	 * Name of the block
	 * @var string
	 */
	protected $_block;
	
	/**
	 * Indicates if this is actually a block (or function if false)
	 * @var bool
	 */
	protected $_isBlock = false;
	/**
	 * Array of valid middle tag names.
	 * @var Array
	 */
	protected $_mids = array ();
	/**
	 * Reference to the current template compiler.
	 * @var Template_Compiler
	 */
	protected $_compiler;
	
	/**
	 * Constructor takes a reference to a compiler.  This allows all blocks access to a compiler.
	 * 
	 * @param Template_Compiler $compiler
	 * @return Template_Block
	 */
	function __construct(Template_Compiler $compiler) {
		$this->_compiler = $compiler;
	}
	
	/**
	 * Used in the compilation process to determine if a given block belongs to an already
	 * created block.
	 * 
	 * @param string $tag		Name of the tag to check.
	 * @return bool				Whether or not the tag belongs.
	 */
	function checkValidMidBlock($tag) {
		return in_array ( $tag->block, $this->_mids );
	}
	/**
	 * Simple function to get the block name.  Defaults to the _block variable, but can be overridden
	 * to return something different.
	 * 
	 * @return string		Name of the block
	 */
	function getBlockName() {
		return $this->_block;
	}
	/**
	 * Determines whether or not the block is actually a block.  If true then the compiler will
	 * expect to find an ending tag or potential middle tags.
	 * 
	 * @return bool			Whether or not the tag is identified as a block.
	 */
	function getIsBlock() {
		return $this->_isBlock;
	}
	/**
	 * Stub function which should be overridden for any blocks that intend to handle an end tag.
	 * 
	 * @param Template_TagData $data	Data from the compiler.
	 * @return string					Should return valid PHP code if overridden.
	 */
	function handleEndBlock($data) {
		throw new Template_Exception ( "{$block} should not have an end block." );
	}
	/**
	 * @param Template_TagData $data	Data from the compiler.
	 * @return unknown_type
	 */
	abstract function handleNewBlock($data);
	/**
	 * @param Template_TagData $data	Data from the compiler.
	 * @return string					Valid PHP code if overridden.
	 */
	function handleMiddleBlock($data) {
		throw new Template_Exception ( "{$block} has valid middle tags but no handler." );
	}
}