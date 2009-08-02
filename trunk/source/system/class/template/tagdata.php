<?php
class Template_TagData {
	public $block;
	public $command;
	public $params;
	
	private $_special = "";
	
	/**
	 * Constructor is the entry point to generate tag parts.
	 * 
	 * @param string $tag			The stuff inside {{here}}.
	 * @return Template_TagData		Returns itself.
	 */
	function __construct($tag) {
		//check for echo only
		$_echo = "/^((?:[$].*)|(?:\w::.*))/";
		$echo = preg_match ( $_echo, $tag, $_echo_match );
		if ($echo) {
			$this->_special = '$';
			$_nonBlock = $_echo_match [1];
			$this->block = "echo";
		} else {
			//parses out the tag parts.
			$_specialRegex = "/^(\W)?\s*(\S+)(?:\s+(.*?))?\s*$/s";
			$_matches = array ();
			$_isSpecial = preg_match ( $_specialRegex, $tag, $_matches );
			
			$this->_special = array_safe ( $_matches, 1, "" );
			$this->block = array_safe ( $_matches, 2, "" );
			
			$_nonBlock = array_safe ( $_matches, 3, "" );
		}
		
		$this->_parseCommandAndParams ( $_nonBlock );
		return $this;
	}
	/**
	 * Getter to return the special character.
	 * 
	 * @return string		Special character.
	 */
	function getSpecialCharacter() {
		return $this->_special;
	}
	/**
	 * Function reads through the part that is not a block name and
	 * determines any command or parameters.
	 * 
	 * @param string $nonBlock		The stuff inside {{here}} that is not the block name.
	 * @return bool					Always returns true.
	 */
	private function _parseCommandAndParams($nonBlock) {
		//split non-block into commands and params array
		$split_regex = "/(\w+)\s*=\s*[\"']?([\w$.\/(){}\->]+)[\"']?/";
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
		
		return true;
	}
	/**
	 * Function rewrites any template variables into actual variables for runtime.
	 * 
	 * @param string $command		The sections like {{if this}} or {{foreach from=this}}
	 * @return string				Entire {{if thing}} again with rewritten variables
	 */
	private function _parseVar($command) {
		//TODO: Consider refactoring to avoid hard coded variable names (prefixes).
		
		//this one turns Dot.syntax into Array[syntax]
		$dot_regex = "/([$]?\w+)[.](\w+)/";
		$command = preg_replace ( $dot_regex, "\$1['$2']", $command );
		//this one looks for $variables-> and rewrites them
		//matches $vars->likeThis, $varsAlone, Static::vars
		$regex = "/[$](.*?)(?=->|\s+|\Z|\||\[.*?\])/";
		$replaced = preg_replace ( $regex, '\$this->_vars["$1"]', $command );
		
		return $replaced;
	}
}