<?php
/**
 * WebEngine
 * http://muengine.net/
 * 
 * @version 2.0.0
 * @author Lautaro Angelico <http://lautaroangelico.com/>
 * @copyright (c) 2013-2017 Lautaro Angelico, All Rights Reserved
 * 
 * Licensed under the MIT license
 * http://opensource.org/licenses/MIT
 */

class Account {
	
	protected $_userid;
	protected $_username;
	protected $_password;
	protected $_newPassword;
	protected $_email;
	protected $_serial = '111111111111';
	
	protected $_accountData;
	protected $_verificationKey;
	
	function __construct() {
		global $dB, $dB2;
		
		$this->db = (config('SQL_USE_2_DB',true) ? $dB2 : $dB);
		$this->_md5Enabled = config('SQL_ENABLE_MD5', true);
	}
	
	/**
	 * setVerificationKey
	 * sets the verification key value
	 */
	public function setVerificationKey($value) {
		if(!check_value($value)) throw new Exception('');
		if(!Validator::Number($value)) throw new Exception('');
		if(!Validator::UnsignedNumber($value)) throw new Exception('');
		
		$this->_verificationKey = $value;
	}
	
	/**
	 * setUserid
	 * sets the user id
	 */
	public function setUserid($value) {
		if(!check_value($value)) throw new Exception('The user id you entered is not valid.');
		if(!Validator::AccountId($value)) throw new Exception('The user id you entered is not valid.');
		
		$this->_userid = $value;
	}
	
	/**
	 * setUsername
	 * sets the username
	 */
	public function setUsername($value) {
		if(!check_value($value)) throw new Exception('The username you entered is not valid.');
		if(!Validator::AccountUsername($value)) throw new Exception('The username you entered is not valid.');
		
		$this->_username = $value;
	}
	
	/**
	 * setPassword
	 * sets the password
	 */
	public function setPassword($value) {
		if(!check_value($value)) throw new Exception('The password you entered is not valid.');
		if(!Validator::AccountPassword($value)) throw new Exception('The password you entered is not valid.');
		
		$this->_password = $value;
	}
	
	/**
	 * setNewPassword
	 * sets the new password
	 */
	public function setNewPassword($value) {
		if(!check_value($value)) throw new Exception('The new password you entered is not valid.');
		if(!Validator::AccountPassword($value)) throw new Exception('The new password you entered is not valid.');
		
		$this->_newPassword = $value;
	}
	
	/**
	 * setEmail
	 * sets the email
	 */
	public function setEmail($value) {
		if(!check_value($value)) throw new Exception('The email address you entered is not valid.');
		if(!Validator::AccountEmail($value)) throw new Exception('The email address you entered is not valid.');
		
		$this->_email = $value;
	}
	
	/**
	 * usernameExists
	 * checks if the username is in use
	 */
	public function usernameExists() {
		if(!check_value($this->_username)) return;
		$result = $this->db->query_fetch_single("SELECT "._CLMN_USERNM_." FROM "._TBL_MI_." WHERE "._CLMN_USERNM_." = ?", array($this->_username));
		if(!is_array($result)) return;
		return true;
	}
	
	/**
	 * emailExists
	 * checks if the email address is in use
	 */
	public function emailExists() {
		if(!check_value($this->_email)) return;
		$result = $this->db->query_fetch_single("SELECT "._CLMN_EMAIL_." FROM "._TBL_MI_." WHERE "._CLMN_EMAIL_." = ?", array($this->_email));
		if(!is_array($result)) return;
		return true;
	}
	
	/**
	 * getAccountData
	 * returns the account data
	 */
	public function getAccountData() {
		$this->_loadAccountData();
		return $this->_accountData;
	}
	
	/**
	 * blockAccount
	 * bans an account depending on the identificator set
	 */
	public function blockAccount() {
		if(check_value($this->_userid)) {
			$result = $this->db->query("UPDATE "._TBL_MI_." SET "._CLMN_BLOCCODE_." = ? WHERE "._CLMN_MEMBID_." = ?", array(1, $this->_userid));
			if(!$result) return;
			return true;
		}
		
		if(check_value($this->_username)) {
			$result = $this->db->query("UPDATE "._TBL_MI_." SET "._CLMN_BLOCCODE_." = ? WHERE "._CLMN_USERNM_." = ?", array(1, $this->_username));
			if(!$result) return;
			return true;
		}
		
		if(check_value($this->_email)) {
			$result = $this->db->query("UPDATE "._TBL_MI_." SET "._CLMN_BLOCCODE_." = ? WHERE "._CLMN_EMAIL_." = ?", array(1, $this->_email));
			if(!$result) return;
			return true;
		}
		
		return;
	}
	
	/**
	 * isOnline
	 * checks if the account is online
	 */
	public function isOnline() {
		if(check_value($this->_username)) {
			$result = $this->db->query_fetch_single("SELECT "._CLMN_CONNSTAT_." FROM "._TBL_MS_." WHERE "._CLMN_USERNM_." = ? AND "._CLMN_CONNSTAT_." = ?", array($this->_username, 1));
			if(!is_array($result)) return;
			return true;
		}
		
		$accountData = $this->getAccountData();
		if(is_array($accountData)) {
			$result = $this->db->query_fetch_single("SELECT "._CLMN_CONNSTAT_." FROM "._TBL_MS_." WHERE "._CLMN_USERNM_." = ? AND "._CLMN_CONNSTAT_." = ?", array($accountData[_CLMN_USERNM_], 1));
			if(!is_array($result)) return;
			return true;
		}
		
		return;
	}
	
	/**
	 * _loadAccountData
	 * loads the account data depending on the identificator set
	 */
	protected function _loadAccountData() {
		if(check_value($this->_userid)) {
			$result = $this->db->query_fetch_single("SELECT * FROM "._TBL_MI_." WHERE "._CLMN_MEMBID_." = ?", array($this->_userid));
			if(!is_array($result)) return;
			$this->_accountData = $result;
			return;
		}
		
		if(check_value($this->_username)) {
			$result = $this->db->query_fetch_single("SELECT * FROM "._TBL_MI_." WHERE "._CLMN_USERNM_." = ?", array($this->_username));
			if(!is_array($result)) return;
			$this->_accountData = $result;
			return;
		}
		
		if(check_value($this->_email)) {
			$result = $this->db->query_fetch_single("SELECT * FROM "._TBL_MI_." WHERE "._CLMN_EMAIL_." = ?", array($this->_email));
			if(!is_array($result)) return;
			$this->_accountData = $result;
			return;
		}
		
		return;
	}
	
	/**
	 * _createAccount
	 * creates a new account in the database
	 */
	protected function _createAccount() {
		if(!check_value($this->_username)) return;
		if(!check_value($this->_password)) return;
		if(!check_value($this->_email)) return;
		
		$data = array(
			'username' => $this->_username,
			'password' => $this->_password,
			'name' => $this->_username,
			'serial' => $this->_serial,
			'email' => $this->_email
		);
		
		if($this->_md5Enabled) {
			$query = "INSERT INTO "._TBL_MI_." ("._CLMN_USERNM_.", "._CLMN_PASSWD_.", "._CLMN_MEMBNAME_.", "._CLMN_SNONUMBER_.", "._CLMN_EMAIL_.", "._CLMN_BLOCCODE_.", "._CLMN_CTLCODE_.") VALUES (:username, [dbo].[fn_md5](:password, :username), :name, :serial, :email, 0, 0)";
		} else {
			$query = "INSERT INTO "._TBL_MI_." ("._CLMN_USERNM_.", "._CLMN_PASSWD_.", "._CLMN_MEMBNAME_.", "._CLMN_SNONUMBER_.", "._CLMN_EMAIL_.", "._CLMN_BLOCCODE_.", "._CLMN_CTLCODE_.") VALUES (:username, :password, :name, :serial, :email, 0, 0)";
		}
		
		$result = $this->db->query($query, $data);
		if(!$result) return;
		
		return true;
	}
	
	/**
	 * _validateAccount
	 * checks if the username and password are correct
	 */
	protected function _validateAccount() {
		if(!check_value($this->_username)) return;
		if(!check_value($this->_password)) return;
		$data = array(
			'username' => $this->_username,
			'password' => $this->_password
		);
		if($this->_md5Enabled) {
			$query = "SELECT * FROM "._TBL_MI_." WHERE "._CLMN_USERNM_." = :username AND "._CLMN_PASSWD_." = [dbo].[fn_md5](:password, :username)";
		} else {
			$query = "SELECT * FROM "._TBL_MI_." WHERE "._CLMN_USERNM_." = :username AND "._CLMN_PASSWD_." = :password";
		}
		
		$result = $this->db->query_fetch_single($query, $data);
		if(!is_array($result)) return;
		
		return true;
	}
	
	/**
	 * _generateVerificationKey
	 * generates a 6-digit random number
	 */
	protected function _generateVerificationKey() {
		return mt_rand(111111,999999);
	}
	
	/**
	 * _updatePassword
	 * changes the account password
	 */
	protected function _updatePassword() {
		if(!check_value($this->_userid)) return;
		if(!check_value($this->_username)) return;
		if(!check_value($this->_newPassword)) return;
		if($this->_md5Enabled) {
			$data = array(
				'userid' => $this->_userid,
				'username' => $this->_username,
				'newpassword' => $this->_newPassword
			);
			$query = "UPDATE "._TBL_MI_." SET "._CLMN_PASSWD_." = [dbo].[fn_md5](:newpassword, :username) WHERE "._CLMN_MEMBID_." = :userid";
		} else {
			$data = array(
				'userid' => $this->_userid,
				'newpassword' => $this->_newPassword
			);
			$query = "UPDATE "._TBL_MI_." SET "._CLMN_PASSWD_." = :newpassword WHERE "._CLMN_MEMBID_." = :userid";
		}
		
		$result = $this->db->query($query, $data);
		if(!$result) return;
		
		return true;
	}
	
}