<?php
/**
 * Class helps compile templates by processing all the blocks generated
 * from the inital processing.  This really works in tandem with the compiler
 * and just lives by itself.
 *
 */
class Template_Compiler_BlockProcessor {
	private $_tags;
	private $_init = false;
	private $_stacks = array ();
	private $_first_tag = true;
	private $_compiler;
	
	function __construct(Template_Compiler $compiler) {
		$this->_compiler = $compiler;
	}
	/**
	 * Function is called in order to get the tags into here.
	 * 
	 * @param array $tags
	 * @return bool			Always returns true.
	 */
	function init($tags) {
		$this->_tags = $tags;
		
		$this->_init = true;
		$this->_first_tag = true;
		
		return true;
	}
	/**
	 * Function processes the next tag.  Goes through several steps to decided what to do.
	 * 
	 * @return string		Compiled tag with everything in valid code.
	 */
	function processNextTag() {
		if (! $this->_init) {
			throw new Exception ( "Tags must be init'ed before processing." );
		}
		//There is some seeming magic to avoid using integer indices.
		$tag = current ( $this->_tags );
		if($tag == ""){
			return;
		}
		//More magic with array processing.
		next ( $this->_tags );
		$tag_data = new Template_TagData ( $tag );
		
		//check the first character
		switch ($tag_data->getSpecialCharacter ()) {
			case "/" :
				//process end tag
				//TODO: Avoid the extra calls.
				$compiled = $this->_processEndTag ( $tag_data );
				break;
			case "*" :
				//process comment
				$compiled = "";
				break;
			default :
				//TODO: Avoid the extra calls.
				$compiled = $this->_determineNextBlockType ( $tag_data );
				break;
		}
		return $compiled;
	}
	/**
	 * Function handles an end tag.
	 * 
	 * @param string $tag		Name of the tag being closed.
	 * @return string			Compiled end tag.
	 */
	private function _processEndTag($tag) {
		//TODO: See if we can condense all these functions.
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
	/**
	 * Function is an in-between step for compiling.  Decides if the tag is
	 * new or a middle call for another.
	 * 
	 * @param string $tag		Name of the next tag.
	 * @return string			Compiled tag.
	 */
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
	/**
	 * Function handles a new tag.  This involves adding a call to the internal stack.
	 * 
	 * @param string $tag		Name of the stag
	 * @return string			Compiled new tag.
	 */
	private function _processNewBlock($tag) {
		$class = "Template_Block_{$tag->block}";
		$new_block = new $class ( $this->_compiler );
		if ($new_block->getIsBlock ()) {
			$this->_stacks [] = $new_block;
		}
		
		return $new_block->handleNewBlock ( $tag );
	}
	/**
	 * Function which finishes the compiling process.  Really just
	 * makes sure that there are no open tags left.
	 * 
	 * @return bool		Always return true or throws an exception.
	 */
	function finalize() {
		if (count ( $this->_stacks )) {
			var_dump ( $this->_stacks );
			throw new Template_Exception ( "Not all blocks are closed" );
		}
		
		return true;
	}
}
