<?php
class log_controller{
	public function browse(){
		$format = (isset($_GET["format"]))?$_GET["format"]:"html";
		
		if($format == "ajax"){
		
			$uid = $_GET["uid"];
			$page_no = $_GET["page"];
		
			$logs = Log::getAllActivityForUserPaged($uid, 5, $page_no);
		
			RoutingEngine::getSmarty()->assign("logs", $logs);
			RoutingEngine::getSmarty()->assign("uid", $uid);
			RoutingEngine::getSmarty()->assign("page_no", $page_no+1);
			
			exit(RoutingEngine::getSmarty()->fetch("log/log_list.tpl"));
		}
	}
}
?>