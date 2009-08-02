<?php
/**
 * Block represents foreach and foreachelse.  These are used to iterate an array.
 *
 */
class Template_Block_Foreach extends Template_Block {
	protected $_mids = array ("foreachelse" );
	protected $_isBlock = true;
	protected $_block = "foreach";
	protected $_params = array ("from", "item" );
	
	private $_hadElse = false;
	
	function handleNewBlock($tag) {
		//TODO: Maybe this should handle a non-array case.
		$from = $tag->params["from"];
		$item = $tag->params["item"];
		
		//Does a count to handle the foreachelse case.
		return "<?php if(count({$from})): foreach({$from} as \$this->_vars['{$item}']): ?>";
	}
	function handleEndBlock($tag) {
		//finish the if created above.
		if ($this->_hadElse) {
			return "<?php endif; ?>";
		} else {
			return "<?php endforeach; endif; ?>";
		}
	}
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
