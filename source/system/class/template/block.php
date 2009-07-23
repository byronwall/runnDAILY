<?php
abstract class Template_Block {
	protected $_block;
	protected $_isBlock = false;
	protected $_mids = array ();
	protected $_compiler;
	
	function __construct(Template_Compiler $compiler){
		$this->_compiler = $compiler;
	}
	
	function checkValidMidBlock($tag) {
		return in_array ( $tag->block, $this->_mids );
	}
	function getBlockName() {
		return $this->_block;
	}
	function getIsBlock() {
		return $this->_isBlock;
	}
	function handleEndBlock($data){
	}
	/**
	 * @param Template_TagData $data
	 * @return unknown_type
	 */
	abstract function handleNewBlock($data);
	function handleMiddleBlock($data){
		throw new Template_Exception("{$block} has valid middle tags but no handler.");
	}
}