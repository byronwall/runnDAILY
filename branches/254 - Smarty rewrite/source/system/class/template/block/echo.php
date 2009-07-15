<?php

class Template_Block_Echo extends Template_Block {
	protected $_block = "echo";
	
	function handleNewBlock($tag) {
		//TODO: Handle condition
		$mod_regex = "/^(.*)[|].*$/";
		$command = preg_replace ( $mod_regex, "$1", $tag->command );
		
		return "<?php echo $command; ?>";
	}
}
