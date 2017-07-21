<?php
/**
 * WebEngine CMS
 * https://webenginecms.org/
 * 
 * @version 2.0.0
 * @author Lautaro Angelico <http://lautaroangelico.com/>
 * @copyright (c) 2013-2017 Lautaro Angelico, All Rights Reserved
 * 
 * Licensed under the MIT license
 * http://opensource.org/licenses/MIT
 */

class sessionControl {
	
	private static $db;
	
	/**
	 * initSessionControl
	 * initiates the session control system
	 * 
	 * @param string $type
	 */
	public static function initSessionControl($type="") {
		global $dB, $dB2;
		
		self::$db = (config('SQL_USE_2_DB',true) ? $dB2 : $dB);
		switch($type) {
			case "user":
				$sessionData = self::sessionInfo(session_id(),"sessionid");
				if(!is_array($sessionData)) {
					self::logout();
				} else {
					if($sessionData['ip_address'] != $_SERVER['REMOTE_ADDR']) {
						self::logout();
					}
					self::isSessionIDLE($sessionData['last_activity']);
					self::updateSession("user");
				}
				break;
			default:
				if($_SESSION['guest']) {
					$sessionData = self::sessionInfo(session_id(),"sessionid");
					if(!is_array($sessionData)) {
						self::newGuestSession();
					} else {
						self::updateSession();
					}
				} else {
					self::newGuestSession();
				}
		}
	}
	
	/**
	 * newSession
	 * deletes the guest session data, regenerates the session id and creates a new user session
	 * 
	 * @param int $userid
	 * @param string $username
	 */
	public static function newSession($userid, $username) {
		self::deleteSession(session_id());
		$_SESSION['guest'] = false;
		$_SESSION['failed_logins'] = 0;
		
		session_regenerate_id();
		$_SESSION['valid'] = true;
		$_SESSION['userid'] = $userid;
		$_SESSION['username'] = $username;
		
		self::deleteMultipleSessions($userid);
		$data = array($userid, session_id(), $_SESSION['last_location'], $_SERVER['REMOTE_ADDR']);
		
		try {
			self::$db->query("INSERT INTO WEBENGINE_SESSION_CONTROL (userid,session_id,last_location,ip_address,last_activity) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)", $data);
		} catch(Exception $ex) {
			// Log system: session control error log
		}
	}
	
	/**
	 * isLoggedIn
	 * checks if user session data exists
	 * 
	 * @return boolean
	 */
	public static function isLoggedIn() {
		if(check_value($_SESSION['valid'], $_SESSION['userid'], $_SESSION['username'])) {
			return true;
		}
		return false;
	}
	
	/**
	 * logout
	 * deletes the session data from the database and destroys the session data
	 */
	public static function logout() {
		self::deleteSession(session_id());
		session_unset($_SESSION['valid']);
		session_unset($_SESSION['userid']);
		session_unset($_SESSION['username']);
		session_destroy();
		redirect();
	}
	
	/**
	 * sessionInfo
	 * returns the session information from the database
	 * 
	 * @param string $identifier
	 * @param string $data
	 * @return array
	 */
	private static function sessionInfo($data,$identifier="") {
		if(!check_value($data)) return;
		switch($identifier) {
			case "sessionid":
				$query = "SELECT * FROM WEBENGINE_SESSION_CONTROL WHERE session_id = ?";
				break;
			default:
				$query = "SELECT * FROM WEBENGINE_SESSION_CONTROL WHERE userid = ?";
		}
		try {
			return self::$db->query_fetch_single($query, array($data));
		} catch (Exception $ex) {
			// Log system: session control error log
		}
	}
	
	/**
	 * newGuestSession
	 * creates a new guest session in the database
	 * 
	 * @return boolean
	 */
	private static function newGuestSession() {
		$_SESSION['guest'] = true;
		try {
			$data = array(session_id(), $_SESSION['last_location'], $_SERVER['REMOTE_ADDR']);
			self::$db->query("INSERT INTO WEBENGINE_SESSION_CONTROL (session_id,last_location,ip_address,last_activity) VALUES (?, ?, ?, CURRENT_TIMESTAMP)", $data);
			return true;
		} catch(Exception $ex) {
			// Log system: session control error log
		}
	}
	
	/**
	 * lastUserLocation
	 * updates the last user location
	 * 
	 * @param string $location
	 */
	public static function lastUserLocation($location) {
		$_SESSION['last_location'] = (check_value($location) ? $location : "/");
	}
	
	/**
	 * updateSession
	 * updates the session information in the database
	 * 
	 * @param string $type
	 * @return boolean
	 */
	private static function updateSession($type="") {
		switch($type) {
			case "user":
				$data = array($_SESSION['last_location'], session_id());
				$query = "UPDATE WEBENGINE_SESSION_CONTROL SET last_location = ?, last_activity = CURRENT_TIMESTAMP WHERE session_id = ?";
				break;
			default:
				$data = array($_SESSION['last_location'], $_SERVER['REMOTE_ADDR'], session_id());
				$query = "UPDATE WEBENGINE_SESSION_CONTROL SET last_location = ?, ip_address = ?, last_activity = CURRENT_TIMESTAMP WHERE session_id = ?";
		}
		try {
			self::$db->query($query, $data);
			return true;
		} catch (Exception $ex) {
			// Log system: session control error log
		}
	}
	
	/**
	 * deleteSession
	 * deletes the session information from the database
	 * 
	 * @param string $sessionid
	 * @return boolean
	 */
	private static function deleteSession($sessionid) {
		if(!check_value($sessionid)) return;
		try {
			self::$db->query("DELETE FROM WEBENGINE_SESSION_CONTROL WHERE session_id = ?", array($sessionid));
			return true;
		} catch (Exception $ex) {
			// Log system: session control error log
		}
	}
	
	/**
	 * deleteMultipleSessions
	 * deletes the session information of a specific user id from the database
	 * 
	 * @param int $userid
	 * @return boolean
	 */
	private static function deleteMultipleSessions($userid) {
		if(!check_value($userid)) return;
		try {
			self::$db->query("DELETE FROM WEBENGINE_SESSION_CONTROL WHERE userid = ?", array($userid));
			return true;
		} catch (Exception $ex) {
			// Log system: session control error log
		}
	}
	
	/**
	 * isSessionIDLE
	 * checks if a session is idle for over 5 minutes and logouts the user
	 * 
	 * @param datetime $last_action
	 */
	private static function isSessionIDLE($last_action) {
		$loginCfg = loadConfigurations('login');
		$loginSessionTimeout = check_value($loginCfg['session_timeout']) ? $loginCfg['session_timeout'] : 300;
		
		$lastAction = strtotime($last_action);
		$idleTime = time() - $lastAction;
		if($idleTime >= $loginSessionTimeout) {
			self::logout();
		}
	}
}