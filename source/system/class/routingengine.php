<?php
class RoutingEngine{
	private $_request_path;
	public $controller = "home";
	private $controller_full = "controller_home";
	public $action = "index";
	private $start_time;
	private $_params;
	
	/**
	 * @var Page
	 */
	public $page;
	
	private static $_instance = null;
	private static $_smarty = null;
	
	public static $controllers = array(
		"about",
		"admin",
		"community",
		"confirmation",
		"events",
		"feedback",
		"goals",
		"group",
		"guides",
		"help",
		"home",
		"log",
		"messages",
		"routes",
		"rss",
		"training",
		"user"
	);
	
	private function __construct(){
		//TODO:Add in error handlers.
		//$this->error_handler = set_error_handler(array("RoutingEngine", "errorHandler"));
		//$this->exception_handler = set_exception_handler(array("RoutingEngine", "exceptionHandler"));
	}
	
	/**
	 * @return RoutingEngine
	 */
	public static function getInstance(){
		if(is_null(self::$_instance)){
			self::$_instance = new RoutingEngine();
		}
		return self::$_instance;
	}
	
	/**
	 * Function is called to load the requested page, check permissions and set up global Smarty variables.
	 *
	 * @param string $request	p_page_name to find in database
	 * @return RoutingEngine
	 */
	public function initialize($request){
		$request = strtolower($request);
		if(substr($request, 0, 1)== "/") $request = substr($request, 1);
		if(substr($request, -1, 1)== "/") $request = substr($request, 0, -1);
		$this->_request_path = $request;
		
		$paths = explode("/", $this->_request_path);
		if(in_array($paths[0], self::$controllers)){
			$this->controller = $paths[0];
			$action = array_safe($paths, 1);
			$params = array_slice($paths, 2);
		}
		else{
			$this->controller = "home";
			$action = $paths[0];
			$params = array_slice($paths, 1);
		}
		$this->controller_full = "Controller_".$this->controller;
		if(!empty($action) && $this->_checkAction($action)){
			$this->action = $action;
			$this->_request_path = $this->controller."/{$this->action}";
		}
		else{
			$this->action = "index";
			$this->_request_path = $this->controller."/index";
		}
		
		//$this->page = Page::getPage($this->_request_path);
		$this->page = new Page();
		$this->page->page_name = $this->_request_path;
		
		//$this->requirePermission($this->page->perm_code, null, true);
		$this->_processParamters($params);
		
		$this->getSmarty()->assign("page", $this->page);
		$this->getSmarty()->assign("currentUser", User::$current_user);
		$this->getSmarty()->assign("engine", $this);
		$this->getSmarty()->assign("notifications", new Notification());
		
		
		$this->start_time = $_SERVER["TIME_START"];
		
		return $this;
	}
	/**
	 * @return bool
	 */
	public function renderRequest(){
		$controller = $this->controller_full;
		$class = new $controller();
		$action = $this->action;
		call_user_func_array(array($class, $action), $this->_params);
		
		//set default page props if not called yet
		$this->setPage("runnDAILY DEFAULT", "PV__100", true);
		
		$filename = self::getSmarty()->template_dir."/".$this->getTemplateName();		
		
		if(!file_exists($filename)){
			$handle = fopen($filename, "w");
			fwrite($handle, $this->getSmarty()->fetch("generic/default.tpl"));
			fclose($handle);
		}
		self::getSmarty()->display_master($this->getTemplateName());
		return true;
	}
	/**
	 * @return string	Formatted name of the template corresponding to the active controller/action
	 */
	public function getTemplateName(){
		if($this->controller == "home"){
			return "{$this->action}.tpl";
		}
		return "{$this->controller}/{$this->action}.tpl";
	}
	
	/**
	 * @return SmartyExt
	 */
	public static function getSmarty(){
		if(is_null(self::$_smarty)){
			self::$_smarty = new SmartyExt();
		}
		return self::$_smarty;
	}
	
	/**
	 * @return float
	 */
	public function getPageTime(){
		return microtime(true) - $this->start_time;
	}
	/**
	 * @param string $action
	 * @return bool
	 */
	private function _checkAction($action){
		return method_exists($this->controller_full, $action);
	}
	/**
	 * Function checks whether or not a user has the requested permission.
	 *
	 * @param string $perm_code	Permission code
	 * @param int $gid			Group id if applicable
	 * @param bool $redir		Whether or not to redirect on a failed permission
	 * @return bool				Whether or not the user has the required permission
	 */
	public function requirePermission($perm_code = "PV__300", $gid = null, $redir = false){
		if(!isset($perm_code)) $allow = false;
		else{
			$code = explode("__", $perm_code);
			$group = (isset($gid))?$gid:"site";
			
			if(isset(User::$current_user->permissions[$group][$code[0]])){
				$allow = User::$current_user->permissions[$group][$code[0]] <= $code[1];
			}
			else{
				$allow = false;
			}
		}
		
		if(!$allow && $redir){
			$_SESSION["login_redirect"] = $this->_request_path;
			Notification::add("You do not have the authentication level to view that page.");
			Notification::add("Once you login, you will be returned to:". $this->_request_path);
			Page::redirect("/home/login");
		}
		
		return $allow;
	}
	/**
	 * Function saves the current user to the session data.
	 * Called at the end of every page to persist and changes.
	 *
	 * @return bool
	 */
	public function persistUserData(){
		$_SESSION["userData"] = User::$current_user;
		
		return true;
	}
	/**
	 * Function is called to set-up the current user before all other calls.
	 * For an authentic user, settings and details are updated.
	 *
	 * @return bool
	 */
	public function authenticateUser(){
		if(isset($_SESSION["userData"]) && $_SESSION["userData"]->uid){
			User::$current_user = $_SESSION["userData"];
			//User::$current_user->refreshDetails();
			//User::$current_user->refreshSettings();
		}
		else{
			$_SESSION["userData"] = User::cookieLogin();
			User::$current_user = $_SESSION["userData"];
			User::$current_user->getFriends();
		}
		User::$current_user->initialize();
		
		return true;
	}
	/**
	 * @param mixed $output
	 * @param bool $json Wheter or not to json_encode the output
	 * @return bool
	 */
	public static function returnAjax($output, $json = true){
		if($json){
			echo json_encode($output);
			exit;
		}
		echo $output;
		exit;
	}
	
	public static function returnAjaxForm($result, $data){
		$output = array("data"=>$data, "result"=>$result);		
		RoutingEngine::returnAjax($output, true);
	}
	
	/**
	 * Function to get the current page name.  Used mainly for google tracking.
	 *
	 * @return string	Name of the current page
	 */
	public function getPageName(){
		return $this->_request_path;
	}

	/**
	 * Internal function is called to initially grab any parameters from the URL.
	 * 
	 * @param array $params	Array containing the parameter data.
	 * @return void
	 */
	private function _processParamters($params){
		$this->_params = $params;
		foreach($this->_params as $k=>$v){
			$_GET[$k] = $v;
		}
	}
	/**
	 * Function is called to assign names to the parameters passed in the URL.
	 * These parameters are then accessible through $_GET.
	 * 
	 * @param string $param	List of parameters to be named 
	 * @return void
	 */
	public function registerParams($param){
		$names = func_get_args();
		foreach($names as $index=>$name){
			if(!isset($this->_params[$index])) continue;
			$this->_params[$name] = $this->_params[$index];
			$_GET[$name] = $this->_params[$index];
		}
	}
	
	private static $_isPageSet = false;
	public static function setPage($title = "runnDAILY", $perm = "PV__100", $default = false){
		if($default && self::$_isPageSet) return;
		self::getInstance()->requirePermission($perm, null, true);		
		self::getInstance()->page->title = $title;
		
		self::$_isPageSet = true;
	}
	public function getCommonName(){
		return $this->controller ."_".$this->action;
	}
	public static function throwException($comment){
		throw new SiteException($comment);
	}
	public static function errorHandler($errno, $errstr, $errfile, $errline) {
	    switch ($errno) {
	    	case E_NOTICE:
	        case E_USER_NOTICE:
	            $errors = "Notice";
	            break;
	        case E_WARNING:
	        case E_USER_WARNING:
	            $errors = "Warning";
	            break;
	        case E_ERROR:
	        case E_USER_ERROR:
	            $errors = "Fatal Error";
	            break;
	        default:
	            $errors = "Unknown";
	            break;
        }
        echo "$errno,$errstr, $errfile, $errline <br>";
        //return true;
        if(!self::getInstance()->requirePermission("PV__100")){
	        self::getSmarty()->display_error();
		    die;
        }
        return true;
	}
	public static function exceptionHandler($exception){
		if(!self::getInstance()->requirePermission("PV__100")){
	        self::getSmarty()->display_error();
		    die;
        }
        var_dump($exception);
	}
}
?>