<?php

class Template_Block_Echo extends Template_Block {
	protected $_block = "echo";
	
	function handleNewBlock($tag) {
		//TODO: Handle condition
		$command = $tag->command;
		
		$mod_regex = "/^(.*)[|](.*?)(?::(.*))?$/";
		$count = preg_match ( $mod_regex, $tag->command, $matches );
		
		if ($count) {
			$var = array_safe ( $matches, 1 );
			$mod = array_safe ( $matches, 2 );
			$param = array_safe ( $matches, 3 );
			
			if (substr ( $mod, 0, 1 ) == "@") {
				$mod = substr ( $mod, 1 );
				$allowed = true;
			} else {
				$allowed = $this->_compiler->addFunctionToSource ( $mod, "modifier" );
			}
			
			
			if ($allowed) {
				if ($param) {
					$command = "template_modifier_" . $mod . "({$var}, {$param})";
				
				} else {
					$command = "template_modifier_" . $mod . "({$var})";
				}
			} else {
				$command = $var;
			}
		}
		
		return "<?php echo $command; ?>";
	}
}
