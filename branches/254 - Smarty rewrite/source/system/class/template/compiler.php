<?php
/**
 * Class compiles template files.
 *
 */
class Template_Compiler {
	/**
	 * @var Template_ResourceManager
	 */
	private $_resource_manager;
	/**
	 * @var Template_BlockProcessor
	 */
	private $_blockProcessor;
	
	private $_compiledPieces = array ();
	private $_templateName;
	private $_saveName;
	private $_functions = array();
	
	function __construct(Template_ResourceManager $resource_manager) {
		$this->_resource_manager = $resource_manager;
	}
	/**
	 * Function starts the process of compiling a template.
	 * 
	 * @param string $template_name
	 * @return mixed		Not sure what this returns.  TBD.
	 */
	function compile($template_name) {
		//TODO: Move this check into the resource manager.
		$template_name = preg_replace("/^\/?(.*)/", "$1", $template_name);
		$this->_templateName = $template_name;
		$filename = $this->_resource_manager->getTemplateFilename ( $template_name );
		
		$contents = file_get_contents ( $filename );
		if ($contents === false) {
			//this happens when the file doesn't exist
			throw new Template_Exception ( "Cannot open file: {$template_name} to compile." );
		}
		//precompile to get includes embedded
		$pre_compiled = $this->_preProcessSource ( $contents );
		//generate all the PHP tags
		$compiled = $this->_processSource ( $pre_compiled );
		//process any functions that were added during the process
		$functions = $this->_processFunctions ();
		
		//TODO: Move this into a function that combines other elements
		$compiled = $functions . $compiled;
		
		//save to a file
		$compiled_name = $this->_writeCompiledFile ( $compiled, $template_name );
		
		return $compiled_name;
	}
	/**
	 * Function writes the compiled output to the file system.
	 * 
	 * @param string $compiled_source		Final compiled output.
	 * @param string $template_name			Name of the template.
	 * @return string						Returns the compiled file name.
	 */
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
		return $filename;
	}
	/**
	 * Function is responsible for building the function list from the pregenerated
	 * list of functions.  Function assumes a certain naming structure for the compiled
	 * functions.
	 * 
	 * @return string		Complete output of functions.
	 */
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
	/**
	 * Function rummages through the template before it is compiled.  It searches
	 * only for {{include}} blocks.  These are then embedded into the compiled output.
	 * 
	 * @param string $contents		Entire template as a string.
	 * @return string				Entire source with included templates embedded.
	 */
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
		$include_split_regex = "/{$ld}include .*?{$rd}/s";
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
	/**
	 * Function does the hardcore lifting for the template compilation process.  This
	 * function is responsible for turning a template into a compiled template.
	 * 
	 * @param string $contents		Entire source consisting only of valid blocks.
	 * @return string				Compiled output.
	 */
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
		
		//bring everything back together.
		$compiled_source = implode ( "", $_compiledPieces );
		
		return $compiled_source;
	}
	/**
	 * Function adds a function to the list of functions that need to be embedded.
	 * This does an agressive load and grabs the file contents before it returns.
	 * 
	 * @param string $function		Name of the function
	 * @param string $type			Type of function.
	 * @return string				Everything after function name in source code.
	 */
	public function addFunctionToSource($function, $type = "modifier") {
		//TODO: Consider a lazy load here.  Just store file names and load them later.
		//already been added
		if (isset ( $this->_functions [$type] [$function] )) {
			return $this->_functions [$type] [$function];
		}
		//stores the source in the functions array
		$function_source = $this->_getFunctionSource ( $function, $type );
		$this->_functions [$type] [$function] = $function_source;
		return $function_source;
	}
	/**
	 * Function responsible for grabbing the source code from another function.  This
	 * requires a certain naming structure.  Grabs everything after the function name
	 * to avoid renaming variabes inside the source.
	 * 
	 * @param string $function		Name of the function
	 * @param string $type			Type of the function.  Essentially what class is it.
	 * @return string				Source code for the function.
	 */
	private function _getFunctionSource($function, $type) {
		//TODO: Consider refactoring to remove all the hard coded things.
		$filename = CLASS_ROOT . "/template/{$type}/" . $function . ".php";
		
		if (! file_exists ( $filename )) {
			trigger_error ( "Modifier / function {$function} could not be found to compile file {$this->_templateName}." );
			return false;
		}
		$modifier_source = file_get_contents ( $filename );
		
		//Assumes the function is named runtime.  Grabs the source code.
		$func_regex = "/runtime([(].*?[)]\s*{.*?})\s*}\s*$/s";
		$matches = array ();
		$count = preg_match ( $func_regex, $modifier_source, $matches );
		
		return $matches [1];
	}
}