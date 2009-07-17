<?php
class Template_TagData {
	public $block;
	public $command;
	public $params;
	
	private $_special = "";
	
	function __construct($tag) {
		//TODO: Process the tag.
		//check for special
		

		//check for echo only
		$_echo = "/^((?:[$].*)|(?:\w::.*))/";
		$echo = preg_match ( $_echo, $tag, $_echo_match );
		if ($echo) {
			$this->_special = '$';
			$_nonBlock = $_echo_match [1];
			$this->block = "echo";
		} else {
			$_specialRegex = "/^(\W)?\s*(\S+)(?:\s+(.*?))?\s*$/s";
			$_matches = array ();
			$_isSpecial = preg_match ( $_specialRegex, $tag, $_matches );
			
			$this->_special = array_safe ( $_matches, 1, "" );
			$this->block = array_safe ( $_matches, 2, "" );
			
			$_nonBlock = array_safe ( $_matches, 3, "" );
		}
		
		$this->_parseCommandAndParams ( $_nonBlock );
		
	//check the first character
	}
	function getSpecialCharacter() {
		return $this->_special;
	}
	private function _parseCommandAndParams($nonBlock) {
		//split non-block into commands and params array
		$split_regex = "/(\w+)\s*=\s*[\"']?([\w$.\/(){}]+)[\"']?/";
		$_matches = array ();
		$_count = preg_match_all ( $split_regex, $nonBlock, $_matches );
		
		if ($_count) {
			$_params = array ();
			for($i = 0; $i < $_count; $i ++) {
				$key = $_matches [1] [$i];
				$value = $_matches [2] [$i];
				$_params [$key] = $this->_parseVar ( $value );
			}
			$this->params = $_params;
		} else {
			$this->command = $this->_parseVar ( $nonBlock );
		}
	}
	private function _parseVar($command) {
		$dot_regex = "/([$]?\w+)[.](\w+)/";
		$command = preg_replace($dot_regex, "\$1['$2']", $command);
		
		$regex = "/[$](.*?)(?=->|\s+|\Z|\||\[.*?\])/";
		$replaced = preg_replace ( $regex, '\$this->_vars["$1"]', $command );
		
		return $replaced;
	}
}
class Template_VariableManager {

}