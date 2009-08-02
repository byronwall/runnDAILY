<?php
/**
 * Shell class for modifiers to extend.
 *
 */
abstract class Template_Modifer {
	/**
	 * Function source is copied into compiled template.  Output is echoed into final output.
	 * 
	 * @param mixed $variable		Any variable that is passed from the echo block
	 * @param mixed $param			Single parameter passed from the template.	
	 * @return string				Output to be echoed into final output.
	 */
	abstract function runtime($variable, $param);
}