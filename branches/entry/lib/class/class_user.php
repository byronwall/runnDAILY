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

	var $routes = array();

	private $mysqli;

	function __construct(){
		$this->mysqli = database::getDB();
	}

	/**
	 * Function is called to determine if the user is currently logged in.
	 * This check is first done using the session variables.  If those are
	 * not successful then the cookies are checked.
	 *
	 */
	function validateUser(){
		if(isset($_SESSION["userData"])){
			//the user data already exists, so the user is valid.
		}
		elseif(isset($_COOKIE["byroni_us_validation"])){

			$cookie_val = $_COOKIE["byroni_us_validation"];

			$cookie = substr($cookie_val, 0, 32);
			$userid = substr($cookie_val, 32);

			$stmt = $this->mysqli->prepare("SELECT * FROM users WHERE u_uid=? AND u_cookie_hash=?");
			$stmt->bind_param("is", $userid, $cookie);

			$stmt->execute();
			$stmt->store_result();

			if($row = $stmt->fetch_assoc()){

				$this->loadInfoFromFetchAssoc($row);
				$this->isAuthenticated = true;

				$stmt->close();

				$this->updateAccessTime();

				$_SESSION["userData"] = $this;
			}
		}
	}

	/**
	 * Attempts to log in the user with the given credentials
	 *
	 * @param string $uname
	 * @param string $password
	 * @param boolean $remember
	 * @return boolean indicating whether or not the user is logged in
	 */
	function login($uname, $password, $remember = 0){
		$loggedin = false;

		$stmt = $this->mysqli->prepare("SELECT * FROM users WHERE u_username=? AND u_password=MD5(?)");
		$stmt->bind_param('ss', $uname, $password) or die("error binding");
		$stmt->execute() or die("error");
		$stmt->store_result();

		if($row = $stmt->fetch_assoc()){
			$this->loadInfoFromFetchAssoc($row);

			$_SESSION["userData"] = $this;

			if($remember){
				$this->updateUserCookie();
			}
			$loggedin = true;
		}

		$this->isAuthenticated = $loggedin;

		$stmt->close();

		$this->updateAccessTime();

		return $loggedin;
	}
	/**
	 * Internal function that is called when the cookie needs to be updated for the user.
	 * This will update the database entry and also the user's cookies.
	 *
	 */
	private function updateUserCookie(){
		$cookie = md5(mktime());

		$stmt = $this->mysqli->prepare("UPDATE users SET u_cookie_hash=? WHERE u_uid=?") or die($stmt->error);
			
		$stmt->bind_param("si", $cookie, $this->userID) or die($stmt->error);

		$stmt->execute() or die($stmt->error);

		if($stmt->affected_rows == 1){
			setcookie("byroni_us_validation",$cookie.$this->userID, mktime()+3600*24*30,"/");
		}
		$stmt->close();
	}
	/**
	 * Creates a new user.
	 *
	 * @param string $uname
	 * @param string $password
	 * @return boolean indicating whether or not the creation and subsequent login were successful.
	 */
	function createUser($uname, $password){
		$stmt = $this->mysqli->prepare("INSERT INTO users(u_username, u_password) VALUES (?, MD5(?))");
		$stmt->bind_param("ss", $uname, $password);
		$stmt->execute();
		$stmt->close();

		return $this->login($uname, $password);

	}

	/*
	 * This function would be used to update user preferences from some sort of profile page.
	 * */
	function updateUserDetails(){
		$stmt = $this->mysqli->prepare("UPDATE users SET u_location_lat = ?, u_location_lng = ? WHERE u_uid = ?");
		$stmt->bind_param("ddi", $this->location_lat, $this->location_lng, $this->userID);
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
		$stmt = $this->mysqli->prepare("UPDATE users SET u_date_access = NOW() WHERE u_uid = ?");
		$stmt->bind_param("i", $this->userID);
		$stmt->execute();
		$stmt->close();
	}

	/**
	 * Function loads a collection of routes into memory for the current user.
	 * This call is designed to be used to see a sampling of a user's routes.
	 *
	 * @param int $count: number of results returned
	 */

	function loadRoutes($count = 5){
		$this->routes = array();
		$stmt = $this->mysqli->prepare("SELECT * FROM routes WHERE r_uid = ? LIMIT ?");

		$stmt->bind_param("ii", $this->userID, $count) or die($stmt->error);

		$stmt->execute() or die($stmt->error);

		$stmt->store_result() or die($stmt->error);

		while($row = $stmt->fetch_assoc()){
			$this->routes[] = Route::fromFetchAssoc($row, true);
		}

		$stmt->close();
	}

	/**
	 * Logs the current user out of the system.  This is done by destroying the session
	 * and removing the cookie.
	 *
	 */
	function logout(){
		session_destroy();
		setcookie("byroni_us_validation", "", mktime()-3600, "/");
	}

	/**
	 * This function is used to get an array containing a list of all the site's users.
	 * This is really going to become deprecated but for now it grabs all of the users.
	 *
	 * @return Array of User types
	 */
	public static function getListOfUsers(){
		$mysqli = database::getDB();

		$result = $mysqli->query("SELECT * FROM users") or die("error on sql");

		$output = array();

		while($row = $result->fetch_assoc()){
			$output[] = User::fromFetchAssoc($row);
		}
		return $output;
	}
	/**
	 * This function is used to populate a User with details given only their username.
	 *
	 * @param string $username
	 * @return User
	 */
	public static function fromUsername($username){
		$mysqli = database::getDB();

		$stmt = $mysqli->prepare("SELECT u_username, u_uid FROM users WHERE u_username=?") or die($stmt->error);

		$stmt->bind_param("s",$username);
		$stmt->execute();
		$stmt->store_result();

		$row = $stmt->fetch_assoc();

		$user = new User();
		$user->username = $row["u_username"];
		$user->userID = $row["u_uid"];

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
		$mysqli = database::getDB();

		$stmt = $mysqli->prepare("SELECT u_username, u_uid FROM users WHERE u_uid=?") or die($stmt->error);

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
}
?>
