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

class AccountLogin extends Account {
	
	private $_maxFailedLogins = 5;
	private $_failedLoginTimeout = 900;
	private $_ipAddress;
	private $_failedLoginCount = 0;
	private $_unlockTimestamp;
	
	function __construct() {
		parent::__construct();
		
		$loginCfg = loadConfigurations('login');
		if(!is_array($loginCfg)) throw new Exception('Login configs missing.');
		
		// set configs
		$this->_maxFailedLogins = check_value($loginCfg['max_login_attempts']) ? $loginCfg['max_login_attempts'] : 5;
		$this->_failedLoginTimeout = check_value($loginCfg['failed_login_timeout']) ? $loginCfg['failed_login_timeout']*60 : 900;
		
		// check ip address
		if(!Validator::Ip($_SERVER['REMOTE_ADDR'])) throw new Exception(lang('error_65',true));
		$this->_ipAddress = $_SERVER['REMOTE_ADDR'];
	}
	
	public function login() {
		if(!check_value($this->_username)) throw new Exception(lang('error_4',true));
		if(!check_value($this->_password)) throw new Exception(lang('error_4',true));
		
		// check failed logins
		$this->_getFailedLogins();
		if($this->_failedLoginCount >= $this->_maxFailedLogins) {
			$timeout = time()+$this->_failedLoginTimeout;
			if($this->_unlockTimestamp > $timeout) throw new Exception(lang('error_3',true));
			$this->_removeFailedLogins();
		}
		
		// check credentials
		if(!$this->_validateAccount()) {
			$this->_addFailedLogin();
			throw new Exception(langf('login_txt_5', array($this->checkFailedLogins($_SERVER['REMOTE_ADDR']), mconfig('max_login_attempts'), mconfig('max_login_attempts'))));
		}
		
		// account data
		$this->_loadAccountData();
		
		// initiate session
		sessionControl::newSession($this->_accountData[_CLMN_MEMBID_], $this->_accountData[_CLMN_USERNM_]);
		
	}
	
	private function _getFailedLogins() {
		if(!check_value($this->_ipAddress)) return;
		$result = $this->db->query_fetch_single("SELECT * FROM WEBENGINE_FLA WHERE ip_address = ? ORDER BY id DESC", array($this->_ipAddress));
		if(!is_array($result)) return;
		$this->_failedLoginCount = $result['failed_attempts'];
		if(check_value($result['unlock_timestamp'])) $this->_unlockTimestamp = $result['unlock_timestamp'];
	}
	
	private function _addFailedLogin() {
		if(!check_value($this->_ipAddress)) return;
		if($this->_failedLoginCount >= 1) {
			
			if(($this->_failedLoginCount+1) >= $this->_maxFailedLogins) {
				$timeout = time()+$this->_failedLoginTimeout;
				$this->db->query("UPDATE WEBENGINE_FLA SET username = ?, failed_attempts = failed_attempts + 1, timestamp = ?, unlock_timestamp = ? WHERE ip_address = ?", array($this->_username, time(), $timeout, $this->_ipAddress));
			} else {
				$this->db->query("UPDATE WEBENGINE_FLA SET username = ?, failed_attempts = failed_attempts + 1, timestamp = ? WHERE ip_address = ?", array($this->_username, time(), $this->_ipAddress));
			}
		} else {
			$data = array(
				$this->_username,
				$this->_ipAddress,
				0,
				1,
				time()
			);
			$this->db->query("INSERT INTO WEBENGINE_FLA (username, ip_address, unlock_timestamp, failed_attempts, timestamp) VALUES (?, ?, ?, ?, ?)", $data);
		}
	}
	
	private function _removeFailedLogins() {
		if(!check_value($this->_ipAddress)) return;
		$this->db->query("DELETE FROM WEBENGINE_FLA WHERE ip_address = ?", array($this->_ipAddress));
	}
	
}