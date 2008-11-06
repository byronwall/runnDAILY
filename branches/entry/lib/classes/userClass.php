<?php
/**
 * This class is used to maintain the backend of the user system.
 * Provides a means to create users, and authenticate users.
 *
 */
class User{

	var $validUser = false;
	var $username;
	var $passwordHash;
	var $userID;
	var $mysqli;

	//TODO:add in the other fields for user preferences

	function __construct(){
		global $SETTINGS;
		$this->mysqli = $SETTINGS["dbconn"];
	}

	/**
	 * This function is called.
	 *
	 */
	function validateUser(){
		global $DEBUG;
		//the first thing to check is a session variable for the current user
		//if the session exists then simply return a valid user

		//TODO:implement the session part of the validate user code
		session_start();
		if(isset($_SESSION["userData"])){
			//the user data already exists, so the user is valid.
		}
		//the next thing to check is the cookies
		//if the cookies exist for a user then grab the user details and return a valid user
		//TODO:test the cookie part of the validate user function
		elseif(isset($_COOKIE["byroni_us_validation"])){
			$DEBUG[]="trying to validate by cookies";
			$cookie_val = $_COOKIE["byroni_us_validation"];

			$cookie = substr($cookie_val, 0, 32);
			$userid = substr($cookie_val, 32);

			$stmt = $this->mysqli->prepare("SELECT username FROM users WHERE uid=? AND cookie=?");
			$stmt->bind_param('is', $userid, $cookie);

			$stmt->execute();
			$stmt->bind_result($username);

			if($stmt->fetch()){
				$this->username = $username;
				$this->userID  = $userid;
				$this->validUser = true;
				$_SESSION["userData"] = $this;
			}
			else{
				die("there was an error where the cookie exists with the client but not the server.");
			}
		}
		else{
			$DEBUG[] = "there was no validation available";
		}
		//without any other validation measures, the user would not be validated
	}

	function login($uname, $password, $remember = 0){
		$loggedin = false;

		$stmt = $this->mysqli->prepare("SELECT uid, cookie FROM users WHERE username=? AND password=MD5(?)");
		$stmt->bind_param('ss', $uname, $password);
		$stmt->execute();

		//TODO:expand this so that all of the user details are grabbed
		$stmt->bind_result($id, $cookie);

		if($stmt->fetch()){
			$this->username = $uname;
			$this->userID = $id;
			$stmt->close();
			$_SESSION["userData"] = $this;
			//TODO: add in the session starting code
			if(!isset($cookie)){
				$DEBUG[]= 'cookie was not found in the DB';

				if($remember){
					//TODO:test this code

					$this->updateUserCookie();
				}
			}
			else{
				$DEBUG[]=  'cookie already exists on the DB.';
				$DEBUG[]=  $cookie;
			}
			$loggedin = true;
		}
		else{
			$DEBUG[]=  'nothing was found or there was an error';
			$loggedin = false;
		}
		$this->validUser = $loggedin;
		return $loggedin;
	}
	function updateUserCookie(){
		//TODO:test this code
		$cookie = md5(mktime());

		$stmt = $this->mysqli->stmt_init();
		$stmt->prepare("UPDATE users SET remember_me=1, cookie=? WHERE uid=?") or die($stmt->error);
			
		$stmt->bind_param('si', $cookie, $this->userID) or die($stmt->error);

		$stmt->execute() or die($stmt->error);

		if($stmt->affected_rows == 1){
			$DEBUG[]=  "cookie was changed";
			setcookie("byroni_us_validation",$cookie.$this->userID, mktime()+3600*24*30);
		}
		else{
			$DEBUG[]=  "there was an error changing the cookie on the server";
		}
		$stmt->close();
	}
	function createUser($uname, $password){
		$stmt = $this->mysqli->stmt_init();

		//TODO:test this change to the create user function
		$stmt->prepare("INSERT INTO users(username, password) VALUES (?, MD5(?))");
		$stmt->bind_param('ss', $uname, $password);
		$stmt->execute();
		$stmt->close();

		return $this->login($uname, $password);

	}
	//TODO:implement a way to change the user details
	/*
	 * This function would be used to update user preferences from some sort of profile page.
	 * */
	function updateUserDetails(){
		die("not implemented");
	}
	function logout(){
		session_destroy();
		setcookie("byroni_us_validation", "", mktime()-3600);
	}

	public static function getListOfUsers(){
		global $SETTINGS;

		$mysqli = $SETTINGS["dbconn"];

		$result = $mysqli->query("SELECT username FROM users") or die("error on sql");

		$output = array();

		while($row = $result->fetch_assoc()){
			$temp = new User();
			$temp->username = $row["username"];
			$output[] =$temp;
		}
		return $output;
	}
	public static function fromUsername($username){
		global $SETTINGS;

		$mysqli = $SETTINGS["dbconn"];

		$stmt = $mysqli->prepare("SELECT username, uid FROM users WHERE username=?") or die($stmt->error);

		$stmt->bind_param("s",$username);
		$stmt->execute();
		$stmt->store_result();

		$row = $stmt->fetch_assoc();

		$user = new User();
		$user->username = $row["username"];
		$user->userID = $row["uid"];

		$stmt->close();

		return $user;
	}
}
?>
