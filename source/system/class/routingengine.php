<?php
class RoutingEngine{
	private $_request_path;
	public $controller = "home";
	private $controller_full = "controller_home";
	public $action = "index";
	private $start_time;
	
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
		"group",
		"help",
		"home",
		"log",
		"message",
		"routes",
		"rss",
		"training",
		"user"
	);
	
	private function __construct(){
		
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
		$request = preg_replace("/(\/)?(.*)/", "$2", $request);
		$this->_request_path = $request;
		
		$paths = explode("/", $this->_request_path);
		if(in_array($paths[0], self::$controllers)){
			$this->controller = $paths[0];
			$this->controller_full = "Controller_".$paths[0];
			if(!empty($paths[1]) && $this->_checkAction($paths[1])){
				$this->action = $paths[1];
			}
			else{
				$this->action = "index";
				$this->_request_path = $paths[0]."/index";
			}
		}
		else{
			$this->controller = "home";
			$this->controller_full = "Controller_Home";
			if(!empty($paths[0]) && $this->_checkAction($paths[0])){
				$this->action = $paths[0];
				$this->_request_path = "home/{$this->action}";
			}
			else{
				$this->action = "index";
				$this->_request_path = "home/index";
			}
		}
		
		$this->page = Page::getPage($this->_request_path);
		
		$this->requirePermission($this->page->perm_code, null, true);
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
		$class->{$action}();
		
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
	
	/**
	 * Function to get the current page name.  Used mainly for google tracking.
	 *
	 * @return string	Name of the current page
	 */
	public function getPageName(){
		return $this->_request_path;
	}
}
?>