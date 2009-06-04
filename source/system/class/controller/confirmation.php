
<?php
/**
 * 
 * @author Byron Wall
 *
 */
class Controller_Confirmation
{
	/**
	 * 
	 * @return unknown_type
	 */
	public function index()
	{
		RoutingEngine::setPage("runnDAILY Requests", "PV__300");
		$confirm_to = Confirmation::fetchForUser(User::$current_user->uid, true);
		$confirm_from= Confirmation::fetchForUser(User::$current_user->uid, false);
		
		RoutingEngine::getSmarty()->assign("confirm_to", $confirm_to);
		RoutingEngine::getSmarty()->assign("confirm_from", $confirm_from);
	}	
	/**
	 * 
	 * @return unknown_type
	 */
	public function actionProcess()
	{
		RoutingEngine::setPage("runnDAILY Requests", "PV__300");
		$cid = $_POST["cid"];
		$confirm = $_POST["confirm"];
		
		$confirmation = Confirmation::fetch($cid);
		$result = $confirmation->process($confirm);
		
		//Javascript is expecting an object with result and cid
		$output = array("cid"=>$cid, "result"=>$result);		
		RoutingEngine::returnAjax($output, true);
	}
	/**
	 * 
	 * @return unknown_type
	 */
	public function actionCreate()
	{
		RoutingEngine::setPage("runnDAILY Requests", "PV__300");
		$confirmation = new Confirmation($_POST, "");
		$confirmation->uid_from = User::$current_user->uid;
		
		$result = $confirmation->create();
		RoutingEngine::returnAjax(array("result"=>$result), true);		
	}
	public function actionCancel(){
		RoutingEngine::setPage("runnDAILY Requests", "PV__300");
		$cid = $_POST["cid"];
		
		$confirmation = Confirmation::fetch($cid);
		$result = false;
		if($confirmation->uid_from == User::$current_user->uid){
			$result = $confirmation->delete();			
		}
		//Javascript is expecting an object with result and cid
		$output = array("cid"=>$cid, "result"=>$result);		
		RoutingEngine::returnAjax($output, true);
	}
}