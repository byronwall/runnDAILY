<?php
/**
 * This class is used to maintain the backend of the user system.
 * Provides a means to create users, and authenticate users.
 *
 */
class User{

	var $isAuthenticated = false;
	var $username;
	var $passwordHash;
	var $userID;
	var $location_lat;
	var $location_lng;

	var $u_email;
	var $type = 400;
	var $cookie_hash;
	public $date_access;

	var $routes = array();

	private $mysqli;

	/**
	 * Function is called to determine if the user is currently logged in.
	 * This check is first done using the session variables.  If those are
	 * not successful then the cookies are checked.
	 *
	 */
	public static function cookieLogin(){
		if(isset($_COOKIE["byroni_us_validation"])){
			$cookie_val = $_COOKIE["byroni_us_validation"];
			$cookie = substr($cookie_val, 0, 32);
			$userid = substr($cookie_val, 32);

			$stmt = database::getDB()->prepare("SELECT * FROM users WHERE u_uid=? AND u_cookie_hash=?");
			$stmt->bind_param("is", $userid, $cookie);

			$stmt->execute();
			$stmt->store_result();

			if($row = $stmt->fetch_assoc()){
				$valid_user = User::fromFetchAssoc($row);
				$valid_user->isAuthenticated = true;
				$stmt->close();
				$valid_user->updateAccessTime();
				Log::insertItem($valid_user->userID, 203, null, null, null, null);
				return $valid_user;
			}
			$stmt->close();
		}
		return false;
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
		$stmt = database::getDB()->prepare("SELECT * FROM users WHERE u_username=? AND u_password=MD5(?)");
		$stmt->bind_param('ss', $uname, $password);
		$stmt->execute();
		$stmt->store_result();

		$row = $stmt->fetch_assoc();
		$stmt->close();

		if($row){
			$valid_user = User::fromFetchAssoc($row);
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
		Log::insertItem($user->userID, 201, null, null, null, null);
			
		return $user;

	}
	/**
	 * Internal function that is called when the cookie needs to be updated for the user.
	 * This will update the database entry and also the user's cookies.
	 *
	 */
	private function updateUserCookie(){
		setcookie("byroni_us_validation",$this->cookie_hash.$this->userID, mktime()+3600*24*30,"/");
		
		return true;
	}
	/**
	 * Creates a new user.
	 *
	 * @param string $uname
	 * @param string $password
	 * @return boolean indicating whether or not the creation and subsequent login were successful.
	 */
	public static function createUser($uname, $password){
		$stmt = database::getDB()->prepare("
			INSERT INTO users(u_username, u_password, u_cookie_hash) VALUES (?, MD5(?), MD5(NOW()))
		");
		$stmt->bind_param("ss", $uname, $password);
		$stmt->execute();

		$rows = $stmt->affected_rows;
		$uid = $stmt->insert_id;

		$stmt->close();

		if($rows == 1){
			User::sendActivationEmail($uid);
			User::loginSystem(User::fromUid($uid));
			return true;
		}
		return false;
	}

	public static function sendActivationEmail($uid){
		return false;
	}

	/*
	 * This function would be used to update user preferences from some sort of profile page.
	 * */
	function updateUserDetails(){
		$stmt = database::getDB()->prepare("UPDATE users SET u_location_lat = ?, u_location_lng = ?, u_email = ? WHERE u_uid = ?");
		$stmt->bind_param("ddsi", $this->location_lat, $this->location_lng, $this->u_email, $this->userID);
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
		$stmt = database::getDB()->prepare("UPDATE users SET u_date_access = NOW() WHERE u_uid = ?");
		$stmt->bind_param("i", $this->userID);
		$stmt->execute();
		$stmt->close();
	}

	/**
	 * Logs the current user out of the system.  This is done by destroying the session
	 * and removing the cookie.
	 *
	 */
	public static function logout(){
		Log::insertItem($_SESSION["userData"]->userID, 202, null, null, null, null);
		session_destroy();
		setcookie("byroni_us_validation", "", mktime()-3600, "/");
		return true;
	}

	/**
	 * This function is used to get an array containing a list of all the site's users.
	 * This is really going to become deprecated but for now it grabs all of the users.
	 *
	 * @return Array of User types
	 */
	public static function getListOfUsers(){
		$result = database::getDB()->query("SELECT * FROM users") or die("error on sql");

		$user_list = array();

		while($row = $result->fetch_assoc()){
			$user_list[] = User::fromFetchAssoc($row);
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
		$stmt = database::getDB()->prepare("
			SELECT * FROM users WHERE u_username=?
		") or die($stmt->error);

		$stmt->bind_param("s",$username);
		$stmt->execute();
		$stmt->store_result();

		$row = $stmt->fetch_assoc();
		
		$user = User::fromFetchAssoc($row);
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
		$stmt = database::getDB()->prepare("SELECT * FROM users WHERE u_uid=?") or die($stmt->error);
		$stmt->bind_param("i",$uid);
		$stmt->execute();
		$stmt->store_result();

		$row = $stmt->fetch_assoc();

		$user = User::fromFetchAssoc($row);

		$stmt->close();

		return $user;
	}

	/**
	 * Function used as a uniform means of returning a new user from a datbase
	 * query.  Updates the current user in place.
	 *
	 * @param array $row: array of results from a database query
	 */
	private function loadInfoFromFetchAssoc($row){
		$this->username = $row["u_username"];
		$this->userID = $row["u_uid"];
		$this->location_lat = $row["u_location_lat"];
		$this->location_lng = $row["u_location_lng"];
		$this->u_email = $row["u_email"];
		$this->msg_new = $row["u_msg_new"];
		$this->type = $row["u_type"];
		$this->cookie_hash = $row["u_cookie_hash"];
		$this->date_access = $row["u_date_access"];

	}

	/**
	 * Wrapper for the instance funciton of the similar name.  Is used to
	 * return a new User from a database query.
	 *
	 * @param array $row: array representing the database query
	 * @return User: the new user from the database query
	 */
	public static function fromFetchAssoc($row){
		$user = new User();
		$user->loadInfoFromFetchAssoc($row);
		return $user;
	}
	public function checkPermissions($min_perm, $redirect = true){
		if($this->type > $min_perm){
			if(!$redirect) return false;
			$_SESSION["login_redirect"] = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
			header("location: http://" . $_SERVER["SERVER_NAME"] ."/login.php");
			exit;
		}
		return true;
	}
	public static function activateUser($uid, $activation_hash){
		$stmt = database::getDB()->prepare("
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
	public function refreshDetails(){
		$stmt = database::getDB()->prepare("
			SELECT * FROM users WHERE u_uid = ?
		");
		$stmt->bind_param("i", $this->userID);
		$stmt->execute();
		$stmt->store_result();
		
		$row = $stmt->fetch_assoc();
		$this->loadInfoFromFetchAssoc($row);
		$stmt->close();
		
	}
	public static function getUserExists($username){
		$stmt = database::getDB()->prepare("
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
		if($this->userID == $friend_uid) return 0;
		
		$stmt = database::getDB()->prepare("INSERT INTO users_friends(f_uid_1, f_uid_2, f_date_start) VALUES(?,?, NOW())");
		$stmt->bind_param("ii", $this->userID, $friend_uid);
		$stmt->execute();
		$stmt->store_result();
		
		$affected_rows = $stmt->affected_rows;
		$stmt->close();
		
		return $affected_rows;
	}
	/**
	 * Function returns an array of the user's friends.
	 *
	 * @return array: an array of User objects
	 */
	public function getFriends(){
		$stmt = database::getDB()->prepare("
			SELECT users.* FROM users_friends
			INNER JOIN users
			ON users.u_uid = users_friends.f_uid_2
			WHERE users_friends.f_uid_1 = ?
		");
		$stmt->bind_param("i", $this->userID);
		$stmt->execute();
		$stmt->store_result();
		
		$users = array();
		
		while($row = $stmt->fetch_assoc()){
			$users[] = User::fromFetchAssoc($row);
		}
		
		return $users;
	}
}
?>
