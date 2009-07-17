<?php
class Template_Compiler_BlockProcessor {
	private $_tags;
	private $_init = false;
	private $_stacks = array ();
	private $_first_tag = true;
	private $_compiler;
	
	function __construct(Template_Compiler $compiler) {
		$this->_compiler = $compiler;
	}
	function init($tags) {
		$this->_tags = $tags;
		
		$this->_init = true;
		$this->_first_tag = true;
	}
	function processNextTag() {
		if (! $this->_init) {
			throw new Exception ( "Tags must be init'ed before processing." );
		}
		
		$tag = current ( $this->_tags );
		if($tag == ""){
			return;
		}
		next ( $this->_tags );
		$tag_data = new Template_TagData ( $tag );
		
		//check the first character
		switch ($tag_data->getSpecialCharacter ()) {
			case "/" :
				//process end tag
				$compiled = $this->_processEndTag ( $tag_data );
				break;
			case "*" :
				$compiled = "";
				//process comment
				break;
			default :
				$compiled = $this->_determineNextBlockType ( $tag_data );
				break;
		}
		return $compiled;
	}
	private function _processEndTag($tag) {
		$expected = array_pop ( $this->_stacks );
		
		if ($expected->getBlockName () != $tag->block) {
			$error = "
				Tag mismatch.
				Expected: {$expected}.
				Got: {$tag->block}.
			";
			throw new Template_Exception ( $error );
		}
		
		return $expected->handleEndBlock ( $tag );
	}
	private function _determineNextBlockType($tag) {
		//TODO: Clean this up to a single exit point.
		if (count ( $this->_stacks )) {
			$open_block = end ( $this->_stacks );
			
			if ($open_block->checkValidMidBlock ( $tag )) {
				return $open_block->handleMiddleBlock ( $tag );
			}
		}
		return $this->_processNewBlock ( $tag );
	}
	private function _processNewBlock($tag) {
		$class = "Template_Block_{$tag->block}";
		$new_block = new $class ( $this->_compiler );
		if ($new_block->getIsBlock ()) {
			$this->_stacks [] = $new_block;
		}
		
		return $new_block->handleNewBlock ( $tag );
	}
	function finalize() {
		if (count ( $this->_stacks )) {
			var_dump ( $this->_stacks );
			throw new Template_Exception ( "Not all blocks are closed" );
		}
	}
}
