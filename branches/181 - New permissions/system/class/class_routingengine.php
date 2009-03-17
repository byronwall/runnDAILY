<?php
class RoutingEngine{
	private $_request_path;
	public $controller = "home";
	private $controller_full = "home_controller";
	public $action = "index";
	private $start_time;
	
	/**
	 * @var Page
	 */
	public $page;
	
	private static $_instance = null;
	private static $_smarty = null;
	
	public static $controllers = array();
	
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
	 * @param string $request
	 * @return RoutingEngine
	 */
	public function initialize($request){
		
		$request = preg_replace("/(\/)?(.*)/", "$2", $request);
		$this->_request_path = $request;
		
		$paths = explode("/", $this->_request_path);
		if(in_array($paths[0]."_controller", self::$controllers)){
			$this->controller = $paths[0];
			$this->controller_full = $paths[0]."_controller";
			if(!empty($paths[1]) && $this->_checkAction($paths[1])){
				$this->action = $paths[1];
			}
			else{
				$this->action = "index";
				$this->_request_path = $paths[0]."/index";
				//Page::redirect("/{$this->controller}/index");
			}
		}
		else{
			$this->controller = "home";
			$this->controller_full = "home_controller";
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
	 * @return Smarty_Ext
	 */
	public static function getSmarty(){
		if(is_null(self::$_smarty)){
			self::$_smarty = new Smarty_Ext();
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
	public function requirePermission($perm_code, $gid = null, $redir = false){
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
			Page::redirect("/home/login");
		}
		
		return $allow;
	}
}
?>