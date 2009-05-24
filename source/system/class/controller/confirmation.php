
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
		$results = Confirmation::fetchForUser(User::$current_user->uid);
		
		var_dump($results);
		RoutingEngine::getSmarty()->assign("confirmations", $results);
	}	
	/**
	 * 
	 * @return unknown_type
	 */
	public function actionProcess()
	{
		
	}
	/**
	 * 
	 * @return unknown_type
	 */
	public function actionCreate()
	{
		
	}
}