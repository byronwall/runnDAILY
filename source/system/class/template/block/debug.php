<?php

class Template_Block_Debug extends Template_Block {
	protected $_block = "debug";
	
	function handleNewBlock($tag) {
		//TODO: Handle condition
		return "<?php var_dump(\$this->_vars); ?>";
	}
	function handleEndBlock($tag) {
	}
	
}
