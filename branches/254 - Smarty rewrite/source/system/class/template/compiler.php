<?php
class Template_Compiler {
	/**
	 * @var Template_ResourceManager
	 */
	private $_resource_manager;
	private $_blockProcessor;
	
	private $_compiledPieces = array ();
	private $_saveName;
	private $_functions = array();
	
	function __construct(Template_ResourceManager $resource_manager) {
		$this->_resource_manager = $resource_manager;
	}
	function compile($template_name, $save_name = null) {
		//load the template
		$filename = $this->_resource_manager->getTemplateFilename ( $template_name );
		if ($save_name != null) {
			$this->_saveName = $save_name;
		}
		$contents = file_get_contents ( $filename );
		if ($contents === false) {
			throw new Template_Exception ( "Cannot open file: {$template_name} to compile." );
		}
		//precompile to get includes embedded
		$pre_compiled = $this->_preProcessSource ( $contents );
		//generate all the PHP tags
		$compiled = $this->_processSource ( $pre_compiled );
		//
		$functions = $this->_processFunctions ();
		
		$compiled = $functions . $compiled;
		
		$template_name = (is_null ( $save_name )) ? $template_name : $save_name;
		
		//save to a file
		$bytes = $this->_writeCompiledFile ( $compiled, $template_name );
		
		return $compiled;
	}
	private function _writeCompiledFile($compiled_source, $template_name) {
		//create directory if needed
		$dir = $this->_resource_manager->getTemplateDir ( $template_name );
		if (! file_exists ( $dir )) {
			mkdir ( $dir, 0777, true );
		}
		
		//create file with compiled template
		$filename = $this->_resource_manager->getCompiledPath ( $template_name );
		$bytes = file_put_contents ( $filename, $compiled_source );
		if ($bytes === false) {
			throw new Template_Exception ( "Error writing compiled file: {$template_name}" );
		}
		return $bytes;
	}
	private function _processFunctions() {
		$output = "<?php\r\n";
		foreach ( $this->_functions as $type => $functions ) {
			foreach ( $functions as $function => $source ) {
				if ($source === false) {
					continue;
				}
				$output .= "function template_{$type}_{$function}{$source}\r\n";
			}
		}
		$output .= "?>";
		
		return $output;
	}
	private function _preProcessSource($contents) {
		$ldl = "{{";
		$rdl = "}}";
		
		$ld = preg_quote ( $ldl, "/" );
		$rd = preg_quote ( $rdl, "/" );
		
		//this will only match the include blocks
		$include_regex = "/{$ld}include file=[\"']?(.*?)[\"']?{$rd}/s";
		
		$matches = array ();
		$count = preg_match_all ( $include_regex, $contents, $matches );
		//return input since there are no includes.
		if (! $count) {
			return $contents;
		}
		
		//section reads all of the include template into an array
		//it recursively precompiles other templates too.
		$includes = array ();
		foreach ( $matches [1] as $file ) {
			if ($file == "*master*") {
				$file = $this->_saveName;
			}
			$filename = $this->_resource_manager->getTemplateFilename ( $file );
			$_contents = file_get_contents ( $filename );
			
			$includes [] = $this->_preProcessSource ( $_contents );
		}
		$include_split_regex = "/{$ld}include.*?{$rd}/s";
		$text_blocks = preg_split ( $include_split_regex, $contents );
		
		//reset the precompiled entries and weave thing together
		//next and current simply move through arrays without using an integer index
		$precompiled_source = "";
		reset ( $includes );
		foreach ( $text_blocks as $text ) {
			$precompiled_source .= $text;
			$precompiled_source .= current ( $includes );
			next ( $includes );
		}
		return $precompiled_source;
	}
	private function _processSource($contents) {
		$ldl = "{{";
		$rdl = "}}";
		
		$ld = preg_quote ( $ldl, "/" );
		$rd = preg_quote ( $rdl, "/" );
		
		$matches = array ();
		
		//this will match all tags and extra the non-delimter parts
		preg_match_all ( "/{$ld}\s*(.*?)\s*{$rd}/s", $contents, $matches );
		
		$blockProcessor = new Template_Compiler_BlockProcessor ( $this );
		
		//this just has the tag and not the delims
		$tags = $matches [1];
		$blockProcessor->init ( $tags );
		
		//only grab text by splitting at tags
		$text_blocks = preg_split ( "/{$ld}.*?{$rd}/s", $contents );
		
		foreach ( $text_blocks as $text ) {
			$_compiledPieces [] = $text;
			$_compiledPieces [] = $blockProcessor->processNextTag ();
		}
		
		//make sure there are no lingering issues
		$blockProcessor->finalize ();
		
		$compiled_source = implode ( "", $_compiledPieces );
		
		return $compiled_source;
	}
	public function addFunctionToSource($function, $type = "modifier") {
		//already been added
		if (isset ( $this->_functions [$type] [$function] )) {
			return $this->_functions [$type] [$function];
		}
		$function_source = $this->_getFunctionSource ( $function, $type );
		$this->_functions [$type] [$function] = $function_source;
		return $function_source;
	}
	private function _getFunctionSource($function, $type) {
		$filename = CLASS_ROOT . "/template/{$type}/" . $function . ".php";
		
		if (! file_exists ( $filename )) {
			trigger_error ( "Modifier / function {$function} could not be found to compile." );
			return false;
		}
		$modifier_source = file_get_contents ( $filename );
		
		$func_regex = "/runtime([(].*?[)]\s*{.*?})\s*}\s*$/s";
		$matches = array ();
		$count = preg_match ( $func_regex, $modifier_source, $matches );
		
		return $matches [1];
	}
}