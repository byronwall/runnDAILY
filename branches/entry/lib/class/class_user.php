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
		$this->mysqli = database::getDB();
	}

	/**
	 * This function is called.
	 *
	 */
	function validateUser(){
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

			$cookie_val = $_COOKIE["byroni_us_validation"];

			$cookie = substr($cookie_val, 0, 32);
			$userid = substr($cookie_val, 32);

			$stmt = $this->mysqli->prepare("SELECT u_username FROM users WHERE u_uid=? AND u_cookie_hash=?");
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

		}
		//without any other validation measures, the user would not be validated
	}

	function login($uname, $password, $remember = 0){
		$loggedin = false;

		$stmt = $this->mysqli->prepare("SELECT u_uid FROM users WHERE u_username=? AND u_password=MD5(?)");
		$stmt->bind_param('ss', $uname, $password) or die("error binding");
		$stmt->execute() or die("error");
		$stmt->store_result();


		echo $stmt->num_rows();
		//TODO:expand this so that all of the user details are grabbed
		echo "up here";
		$userRow = $stmt->fetch_assoc();
		var_dump($userRow);
		if(true){
			echo "in here";
			$this->username = $uname;
			$this->userID = $userRow["u_uid"];

			$_SESSION["userData"] = $this;
			//TODO: add in the session starting code

			if($remember){
				//TODO:test this code

				$this->updateUserCookie();
			}
			$loggedin = true;
		}
		else{

			$loggedin = false;
		}
		$this->validUser = $loggedin;

		$stmt->close();

		return $loggedin;
	}
	function updateUserCookie(){
		//TODO:test this code
		$cookie = md5(mktime());

		$stmt = $this->mysqli->prepare("UPDATE users SET u_cookie_hash=? WHERE u_uid=?") or die($stmt->error);
			
		$stmt->bind_param('si', $cookie, $this->userID) or die($stmt->error);

		$stmt->execute() or die($stmt->error);

		if($stmt->affected_rows == 1){

			setcookie("byroni_us_validation",$cookie.$this->userID, mktime()+3600*24*30);
		}
		$stmt->close();
	}
	function createUser($uname, $password){
		$stmt = $this->mysqli->prepare("INSERT INTO users(u_username, u_password) VALUES (?, MD5(?))");
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
		$mysqli = database::getDB();

		$result = $mysqli->query("SELECT u_username FROM users") or die("error on sql");

		$output = array();

		while($row = $result->fetch_assoc()){
			$temp = new User();
			$temp->username = $row["u_username"];
			$output[] =$temp;
		}
		return $output;
	}
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
}
?>
