<?php
/**
 * Block is created implicitly in order to echo out variables.  This
 * is the only block that supports modifiers.
 *
 */
class Template_Block_Echo extends Template_Block {
	protected $_block = "echo";
	
	function handleNewBlock($tag) {
		//TODO: Consider moving modifiers to their own system.
		$command = $tag->command;
		
		//looks for the | (pipe) in order to determine modifier.
		$mod_regex = "/^(.*)[|](.*?)(?::(.*))?$/";
		$count = preg_match ( $mod_regex, $tag->command, $matches );
		
		if ($count) {
			$var = array_safe ( $matches, 1 );
			$mod = array_safe ( $matches, 2 );
			$param = array_safe ( $matches, 3 );
			
			if (substr ( $mod, 0, 1 ) == "@") {
				$mod = substr ( $mod, 1 );
				$command = $mod . "(" . $var . ")";
				$allowed = false;
			} else {
				$allowed = $this->_compiler->addFunctionToSource ( $mod, "modifier" );
				
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
		}
		
		return "<?php echo $command; ?>";
	}
}
