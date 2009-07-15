<?php

class Template_Block_Foreach extends Template_Block {
	protected $_mids = array ("foreachelse" );
	protected $_isBlock = true;
	protected $_block = "foreach";
	protected $_params = array ("from", "item" );
	
	private $_from;
	private $_hadElse = false;
	
	/* (non-PHPdoc)
	 * @see source/system/class/template/Template_Block#handleNewBlock($data)
	 */
	function handleNewBlock($tag) {
		//TODO: Handle condition
		foreach ( $this->_params as $param ) {
			${$param} = $tag->params [$param];
		}
		$this->_from = $from;
		
		return "<?php if(count({$from})): foreach({$from} as \$this->_vars['{$item}']): ?>";
	}
	/**
	 * 
	 */
	function handleEndBlock($tag) {
		if ($this->_hadElse) {
			return "<?php endif; ?>";
		} else {
			return "<?php endforeach; endif; ?>";
		}
	}
	
	/**
	 * @param $block
	 */
	function handleMiddleBlock($tag) {
		switch ($tag->block) {
			case "foreachelse" :
				$this->_hadElse = true;
				$compiled = "<?php endforeach; else: ?>";
				break;
		}
		return $compiled;
	}
}
