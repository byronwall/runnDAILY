<?php

/**
 * Block is used to include a file at runtime instead of compile time.
 * This should only be used in the case of an uknown template name.
 *
 */
class Template_Block_IncludeRuntime extends Template_Block {
	protected $_block = "includeruntime";
	
	function handleNewBlock($tag) {
		return "<?php \$this->_include({$tag->params['file']}); ?>";
	}
}
