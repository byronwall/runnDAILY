<?php

require_once 'source\system\class\template\tagdata.php';
require_once 'source\system\config.php';

require_once 'PHPUnit\Framework\TestCase.php';

/**
 * Template_TagData test case.
 */
class Template_TagDataTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Tests Template_TagData->__construct()
	 */
	public function testNonSpecial() {
		$tag = 'if $some_var';
		$tagData = new Template_TagData ( $tag );
		
		$this->assertEquals ( "", $tagData->getSpecialCharacter () );
	}
	public function testSpecial() {
		$tag = '/if $some_var';
		$tagData = new Template_TagData ( $tag );
		
		$this->assertEquals ( "/", $tagData->getSpecialCharacter () );
	}
	public function testBlock() {
		$tag = 'if $engine->requirePermission("PV__300")';
		$tagData = new Template_TagData ( $tag );
		
		$this->assertEquals ( "if", $tagData->block );
	}
	public function testCommand() {
		$tag = 'if $some_var';
		$tagData = new Template_TagData ( $tag );
		
		$this->assertEquals ( '$some_var', $tagData->command );
	}
	public function testTagWithParams() {
		$block = 'foreach';
		$command = '';
		$_params = array ();
		$_params ["from"] = '$sources';
		$_params ["item"] = 'source';
		
		$params = implode_assoc ( $_params );
		
		$tag = "{$block} {$command} {$params}";
		$tagData = new Template_TagData ( $tag );
		
		$this->assertEquals ( "", $tagData->getSpecialCharacter () );
		$this->assertEquals ( $block, $tagData->block );
		$this->assertEquals ( $command, $tagData->command );
		$this->assertEquals ( $_params, $tagData->params );
	}
	public function testTagWithParamsAndSpaces() {
		$block = 'foreach';
		$command = '';
		$_params = array ();
		$_params ["from "] = ' $sources ';
		$_params [" item "] = ' source ';
		
		$params = implode_assoc ( $_params );
		
		$tag = "{$block} {$command} {$params}";
		
		$tagData = new Template_TagData ( $tag );
		
		$this->assertEquals ( "", $tagData->getSpecialCharacter () );
		$this->assertEquals ( $block, $tagData->block );
		$this->assertEquals ( $command, $tagData->command, $tag );
		
		$params_expected = array ("from" => '$sources', "item" => "source" );
		
		$this->assertEquals ( $params_expected, $tagData->params );
	}
	public function testComplexCommand() {
		$block = 'if';
		$command = '$currentUser->uid != $user->uid';
		$_params = null;
		
		$tag = "{$block} {$command}";
		
		$tagData = new Template_TagData ( $tag );
		
		$this->assertEquals ( "", $tagData->getSpecialCharacter () );
		$this->assertEquals ( $block, $tagData->block );
		$this->assertEquals ( $command, $tagData->command, $tag );
		$this->assertEquals ( $_params, $tagData->params );
	}

}
function implode_assoc($array, $inner_glue = "=", $outer_glue = " ") {
	$pieces = array ();
	foreach ( $array as $key => $value ) {
		$pieces [] = $key . $inner_glue . $value;
	}
	return implode ( $outer_glue, $pieces );
}

