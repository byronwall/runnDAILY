<?php
/**
 * This class is used to maintain the backend of the user system.
 * Provides a means to create users, and authenticate users.
 *
 */
class User extends Object{

	public $isAuthenticated = false;

	public $uid;
	public $location_lng;
	public $location_lat;
	public $username;
	public $password;
	public $email;
	public $page_perm = "PV__400";
	public $cookie_hash;
	public $date_access;
	public $msg_new;
	public $join;
	public $img_src;
	
	public $permissions;
	public $settings = array();
	
	public $friends;
	
	/**
	 * @var User
	 */
	public static $current_user;
	
	function __construct($arr = null, $arr_pre = "u_"){
		parent::__construct($arr, $arr_pre);
		$this->date_access = strtotime($this->date_access);
		$this->join = strtotime($this->join);
	}
	function removeFriend($f_uid){
		$stmt = Database::getDB()->prepare("
			DELETE FROM users_friends
			WHERE
				f_uid_1 = ? AND
				f_uid_2 = ?
		");
		$stmt->bind_param("ii", $this->uid, $f_uid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$rows = $stmt->affected_rows;
		
		if($rows == 1){
			unset($this->friends[$f_uid]);
			return true;
		}
		return false;
	}
	/**
	 * Internal function to add a given permission to the array.
	 * This is used mainly to handle hierarchy issues.
	 *
	 * @param string $perm_code
	 * @param int $gid
	 * @return bool
	 */
	private function _addPermission($perm_code, $gid = null){
		if(!isset($perm_code)) return false;
		
		$code = explode("__", $perm_code);
		if(isset($gid)){
			$this->permissions[$gid][$code[0]] = $code[1];
		}
		else{
			$this->permissions["site"][$code[0]] = $code[1];
		}
		return true;
	}
	/**
	 * Gets the permissions for the current user.
	 * This searches the page perm code first.
	 * Then it search for permission groups.
	 * Finally it gets things from the user_roles table.
	 * It does not handle do_not_allow yet.
	 *
	 * @return array
	 */
	function getPermissions(){
		$this->permissions = array();
		
		$this->_addPermission($this->page_perm);
		
		//get the groups table first
		$stmt = Database::getDB()->prepare("
			SELECT pr_code, g_gid, pgr_allow
			FROM users_permission_groups
			LEFT JOIN permission_groups_roles USING(pg_id)
			WHERE
				u_uid = ?
		");
		$stmt->bind_param("i", User::$current_user->uid);
		$stmt->execute();
		$stmt->store_result();
		
		while($row = $stmt->fetch_assoc()){
			$this->_addPermission($row["pr_code"], array_safe($row, "g_gid"));
		}
		$stmt->close();
		
		//get the user individual ones
		$stmt = Database::getDB()->prepare("
			SELECT pr_code, g_gid, ur_allow
			FROM users_roles
			WHERE
				u_uid = ?
		");
		$stmt->bind_param("i", User::$current_user->uid);
		$stmt->execute();
		$stmt->store_result();
		
		while($row = $stmt->fetch_assoc()){
			$this->_addPermission($row["pr_code"], array_safe($row, "g_gid"));
		}
		$stmt->close();
		
		return $this->permissions;
	}

	/**
	 * Looks through the cookies to see if a user can be authenticated.
	 * Returns the correct user or a default one.
	 *
	 * @return User
	 */
	public static function cookieLogin(){
		if(isset($_COOKIE[COOKIE_NAME])){
			$cookie = substr($_COOKIE[COOKIE_NAME], 0, 32);
			$uid = substr($_COOKIE[COOKIE_NAME], 32);

			$stmt = Database::getDB()->prepare("
				SELECT *
				FROM users
				WHERE
					u_uid = ? AND
					u_cookie_hash = ?
			");
			$stmt->bind_param("is", $uid, $cookie);
			$stmt->execute();
			$stmt->store_result();
			
			$rows = $stmt->num_rows;
			$row = $stmt->fetch_assoc();
			$stmt->close();

			if($rows == 1){
				$valid_user = new User($row);
				$valid_user->isAuthenticated = true;
				$valid_user->updateAccessTime();
				Log::insertItem($valid_user->uid, 203, null, null, null, null);
				return $valid_user;
			}
		}
		return new User();
	}

	/**
	 * Attempts to log in the user with the given credentials
	 *
	 * @param string $uname
	 * @param string $password
	 * @param boolean $remember
	 * @return boolean indicating whether or not the user is logged in
	 */
	public static function login($uname, $password, $remember = 0){
		$stmt = Database::getDB()->prepare("
			SELECT *
			FROM users
			WHERE
				u_username = ? AND
				u_password = MD5(?)
		");
		$stmt->bind_param('ss', $uname, $password);
		$stmt->execute();
		$stmt->store_result();

		$row = $stmt->fetch_assoc();
		$stmt->close();

		if($row){
			$valid_user = new User($row);
			return User::loginSystem($valid_user, $remember);
		}
		return false;
	}

	public static function loginSystem($user, $remember = false){
		$_SESSION["userData"] = $user;

		if($remember){
			$user->updateUserCookie();
		}
		$user->isAuthenticated = true;
		$user->updateAccessTime();
		Log::insertItem($user->uid, 201, null, null, null, null);
			
		return $user;

	}
	/**
	 * Internal function that is called when the cookie needs to be updated for the user.
	 * This will update the database entry and also the user's cookies.
	 *
	 */
	private function updateUserCookie(){
		setcookie(COOKIE_NAME,$this->cookie_hash.$this->uid, mktime()+3600*24*30,"/");
		
		return true;
	}
	
	/**
	 * @return bool	Whether or not the user was created.
	 */
	public function create(){
		$stmt = Database::getDB()->prepare("
			INSERT INTO users
			SET
				u_username = ?,
				u_password = ?,
				u_cookie_hash = ?,
				u_email = ?,
				u_location_lat = ?,
				u_location_lng = ?,
				u_join = NOW()
		");
		$stmt->bind_param("ssssdd",
			$this->username,$this->password,$this->cookie_hash,
			$this->email, $this->location_lat, $this->location_lng
		);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		if($stmt->error){
			$stmt->close();
			return false;
		}
		
		$id = $stmt->insert_id;
		$stmt->close();
		
		if($id){
			$this->uid = $id;
			foreach($this->settings as $key=>$value){
				$this->saveSetting($key);
			}
		}
		return true;
	}
	function saveAllSettings(){
		foreach($this->settings as $key=>$value){
			$this->saveSetting($key);
		}
		
		return true;
	}
	function saveSetting($key){
		if(!isset($key) || !isset($this->settings[$key])) return false;
		
		$stmt = Database::getDB()->prepare("
			REPLACE INTO users_metadata
			SET
				u_uid = ?,
				um_key = ?,
				um_value = ?
		");
		$stmt->bind_param("iss", $this->uid, $key, $this->settings[$key]);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		return $rows == 1;
	}

	public static function sendActivationEmail($uid){
		return false;
	}

	/*
	 * This function would be used to update user preferences from some sort of profile page.
	 * */
	function updateUserDetails(){
		$stmt = Database::getDB()->prepare("
			UPDATE users
			SET
				u_location_lat = ?,
				u_location_lng = ?,
				u_email = ?,
				u_password = ?
			WHERE
				u_uid = ?
		");
		$stmt->bind_param("ddssi", $this->location_lat, $this->location_lng, $this->email, $this->password, $this->uid);
		$stmt->execute();

		$isSuccess = $stmt->affected_rows == 1;

		$stmt->close();
		return $isSuccess;
	}

	/**
	 * Updates the database to reflect user activity.
	 *
	 */
	function updateAccessTime(){
		$stmt = Database::getDB()->prepare("UPDATE users SET u_date_access = NOW() WHERE u_uid = ?");
		$stmt->bind_param("i", $this->uid);
		$stmt->execute();
		$stmt->close();
	}

	/**
	 * Logs the current user out of the system.  This is done by destroying the session
	 * and removing the cookie.
	 *
	 */
	public static function logout(){
		Log::insertItem(User::$current_user->uid, 202, null, null, null, null);
		session_unset();
		session_destroy();
		setcookie(COOKIE_NAME, "", mktime()-3600, "/");
		return true;
	}

	/**
	 * This function is used to get an array containing a list of all the site's users.
	 * This is really going to become deprecated but for now it grabs all of the users.
	 *
	 * @return Array of User types
	 */
	public static function getListOfUsers(){
		$result = Database::getDB()->query("
			SELECT users.u_uid, u_username, u_join
			FROM users
			ORDER BY u_join DESC
			LIMIT 50
		") or die("error on sql");

		$user_list = array();

		while($row = $result->fetch_assoc()){
			$user_list[] = $row;
		}
		
		return $user_list;
	}
	/**
	 * This function is used to populate a User with details given only their username.
	 *
	 * @param string $username
	 * @return User
	 */
	public static function fromUsername($username){
		$stmt = Database::getDB()->prepare("
			SELECT * FROM users WHERE u_username=?
		") or die($stmt->error);

		$stmt->bind_param("s",$username);
		$stmt->execute();
		$stmt->store_result();

		$row = $stmt->fetch_assoc();
		
		$user = new User($row);
		$stmt->close();
		
		return $user;
	}

	/**
	 * Creates a new user from a given user id.
	 *
	 * @param int $uid: the user id of the desired user
	 * @return User: object representing the new user
	 */
	public static function fromUid($uid){
		$stmt = Database::getDB()->prepare("SELECT * FROM users WHERE u_uid=?") or die($stmt->error);
		$stmt->bind_param("i",$uid);
		$stmt->execute();
		$stmt->store_result();

		$row = $stmt->fetch_assoc();

		$user = new User($row);

		$stmt->close();

		return $user;
	}

	public function checkPermissions($min_perm, $redirect = true){
		if(isset($this->type) && $this->type > $min_perm){
			if(!$redirect) return false;
			$_SESSION["login_redirect"] = $_SERVER["REQUEST_URI"];
			Page::redirect("/login");
		}
		return true;
	}
	public static function activateUser($uid, $activation_hash){
		$stmt = Database::getDB()->prepare("
			UPDATE users
			SET u_type = 300
			WHERE u_uid = ? AND u_cookie_hash = ?
		");
		$stmt->bind_param("is", $uid, $activation_hash) or die($stmt->error);
		$stmt->execute();
		$stmt->store_result();

		$rows = $stmt->affected_rows;
		$stmt->close();

		if($rows == 1){
			return true;
		}
		return false;
	}
	public function refreshSettings(){
		$stmt = Database::getDB()->prepare("
			SELECT um_key as hash, um_value as value
			FROM users_metadata
			WHERE
				u_uid = ?
		");
		$stmt->bind_param("i", $this->uid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		while($row = $stmt->fetch_assoc()){
			$this->settings[$row["hash"]] = $row["value"];
		}
		
		$stmt->close();

		return;
	}
	public function refreshDetails($row = null){
		if(!isset($row)){
			$stmt = Database::getDB()->prepare("
				SELECT u_msg_new
				FROM users
				WHERE u_uid = ?
			");
			$stmt->bind_param("i", $this->uid) or die($stmt->error);
			$stmt->execute();
			$stmt->store_result();
			
			$row = $stmt->fetch_assoc();
			$stmt->close();
		}
		$this->__construct($row);
	}
	public static function getUserExists($username){
		$stmt = Database::getDB()->prepare("
			SELECT u_uid
			FROM users
			WHERE
				u_username = ?
		");
		$stmt->bind_param("s", $username);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$rows = $stmt->num_rows;
		$stmt->close();
		
		return $rows == 1;
	}

	/**
	 * Function is used to create a friend for the current user.
	 *
	 * @param int $friend_uid: uid of the person to be added as a friend
	 * @return int: int giving the number of rows changed (success/fail)
	 */
	public function addFriend($friend_uid){
		if($this->uid == $friend_uid) return false;
		
		$stmt = Database::getDB()->prepare("INSERT INTO users_friends(f_uid_1, f_uid_2, f_date_start) VALUES(?,?, NOW())");
		$stmt->bind_param("ii", $this->uid, $friend_uid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$affected_rows = $stmt->affected_rows;
		$stmt->close();
		
		if($affected_rows == 1){
			$this->friends[$friend_uid] = true;
			Log::insertItem(User::$current_user->uid, 400, $friend_uid, null, null, null);
			return $affected_rows;
		}
		
		return false;
	}
	/**
	 * Function returns an array of the user's friends.
	 *
	 * @return array: an array of User objects
	 */
	public function getFriends(){
		$stmt = Database::getDB()->prepare("
			SELECT users.u_uid, u_username
			FROM users_friends
			INNER JOIN users ON users.u_uid = users_friends.f_uid_2
			WHERE users_friends.f_uid_1 = ?
			LIMIT 50
		");
		$stmt->bind_param("i", User::$current_user->uid);
		$stmt->execute();
		$stmt->store_result();
		
		$users = array();
		
		while($row = $stmt->fetch_assoc()){
			$users[] = $row;
		}
		
		return $users;
	}
	public function updateUserInDB(){
		$stmt = Database::getDB()->prepare("
			UPDATE users
			SET
				u_type = ?
			WHERE
				u_uid = ?
		");
		$stmt->bind_param("ii", $this->type, $this->uid);
		$stmt->execute();
		$stmt->store_result();
		
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		return $rows == 1;
	}
	
	public function updateImage($filename){
		$this->img_src = $filename;
		$stmt = Database::getDB()->prepare("
			UPDATE users
			SET
				u_img_src = ?
			WHERE
				u_uid = ?
		");
		$stmt->bind_param("si", $this->img_src, $this->uid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		if($rows == 1){
			return true;
		}
	}	
	
	public function deleteUser(){
		//require admin privs
		User::$current_user->checkPermissions(100);
		
		$stmt = Database::getDB()->prepare("
			DELETE FROM users
			WHERE
				u_uid = ?
		");
		$stmt->bind_param("i", $this->uid);
		$stmt->execute();
		$stmt->store_result();
		
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		return $rows == 1;
	}
	public function checkFriendsWith($f_uid){
		if(isset(User::$current_user->friends[$f_uid])){
			return User::$current_user->friends[$f_uid];
		}

		$stmt = Database::getDB()->prepare("
			SELECT *
			FROM users_friends
			WHERE
				f_uid_1 = ? AND
				f_uid_2 = ?
		");
		
		$stmt->bind_param("ii", User::$current_user->uid, $f_uid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		if($rows == 1){
			User::$current_user->friends[$f_uid] = 1;
			return true;
		}
		
		return false;
	}
	
	public static function searchForUser($search_string){
		$query_string = "%" . $search_string . "%";
		$stmt = Database::getDB()->prepare("
			SELECT u_uid, u_username
			FROM users
			WHERE u_username LIKE ?
			UNION
			SELECT u_uid, u_username
			FROM users
			WHERE u_email LIKE ?
			ORDER BY u_username
			LIMIT 40
		");
		
		$stmt->bind_param("ss", $query_string, $query_string);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$users = array();
		
		while($row = $stmt->fetch_assoc()){
			$users[] = $row;
		}
		
		$stmt->close();
		return $users;
	}
}
?>
