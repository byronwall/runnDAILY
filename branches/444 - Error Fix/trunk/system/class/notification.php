<?php
/**
 * Class is used to handle inter-site messages.
 * Notifications are created using the static add function.
 * These will show up on the next time the page is loaded.
 *
 * @author byron wall
 *
 */
class Notification{
	
	public $message;
	
	
	/**
	 * This is stored using three value logic.
	 * 0 = non persisting, clear on next refresh
	 * 1 = non persisting but just created, becomes 0 on render
	 * 2 = persisting
	 *
	 * @var int
	 */
	public $persist = 1;
	public $id;
	
	private static $notifications;
	private static $next_id = 1;
	
	public function __construct($message = null, $persist = false){
		$this->id = self::$next_id++;
		$this->message = $message;
		$this->persist = ($persist)?2:1;
	}
	
	/**
	 * Returns all of the current notifications.
	 * Intended to be called by Smarty only.
	 *
	 * @return array
	 */
	public function getNotifications(){
		return self::_unserialize();
	}
	
	
	
	/**
	 * @param string $message
	 * @param bool $persist Whether or not the message hangs around.
	 * @return bool
	 */
	public static function add($message, $persist = false){
		if(!isset($message)) return false;
		
		self::_unserialize();
		$notify = new Notification($message, $persist);
		self::$notifications[$notify->id] = $notify;
		self::_serialize();
		
		return true;
	}
	/**
	 * This removes a notification.  Intended for persisting ones.
	 * This should really only be called by an action page since the id is
	 * largely unknown and ambiguous.
	 *
	 * @param int $id
	 * @return bool
	 */
	public static function remove($id){
		if(!isset($id)) return false;
		
		self::_unserialize();
		unset(self::$notifications[$id]);
		self::_serialize();
		
		return true;
	}
	
	/**
	 * This function saves the notifications to the session data.
	 *
	 * @return bool
	 */
	private static function _serialize(){
		$_SESSION["Notifications"] = self::$notifications;
		
		return true;
	}
	/**
	 * This function restores saved notifications.  It also handles whether or
	 * not to clear not persisting notifications.
	 *
	 * @return array
	 */
	public static function _unserialize(){
		if(!isset(self::$notifications)){
			if(isset($_SESSION["Notifications"])){
				self::$notifications = $_SESSION["Notifications"];
				foreach(self::$notifications as $notification){
					switch ($notification->persist){
						case 0:
							unset(self::$notifications[$notification->id]);
							break;
						case 1:
							$notification->persist = 0;
							break;
					}
					if($notification->id >= self::$next_id ){
						self::$next_id = $notification->id +1;
					}
				}
			}
			else{
				self::$notifications = array();
			}
		}
		$_SESSION["Notifications"] = self::$notifications;
		
		return self::$notifications;
	}
}
?>