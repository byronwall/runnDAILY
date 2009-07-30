<?php
/**
 * Block turns into the correct structure for logic.
 *
 */
class Template_Block_If extends Template_Block {
	protected $_mids = array ("else", "elseif" );
	protected $_isBlock = true;
	protected $_block = "if";
	
	function handleNewBlock($tag) {
		return "<?php if({$tag->command}): ?>";
	}
	function handleEndBlock($tag) {
		return "<?php endif ?>";
	}
	
	function handleMiddleBlock($tag) {
		switch ($tag->block) {
			case "else" :
				$compiled = "<?php else: ?>";
				break;
			case "elseif" :
				$compiled = "<?php elseif({$tag->command}): ?>";
				break;
		}
		return $compiled;
	}
}
