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

class AccountPassword extends Account {
	
	private $_changePasswordVerificationEnabled = true;
	private $_changePasswordVerificationTimeLimit = 3600;
	private $_changePasswordVerificationUrl = 'verification/password/';
	private $_changePasswordSendNewPasswordEmail = true;
	
	private $_recoveryRandomPasswordLength = 10;
	
	function __construct() {
		parent::__construct();
		
		$mypasswordCfg = loadConfigurations('usercp.mypassword');
		if(!is_array($mypasswordCfg)) throw new Exception('Mypassword configs missing.');
		
		$this->_changePasswordVerificationEnabled = $mypasswordCfg['change_password_email_verification'] ? true : false;
		$this->_changePasswordVerificationTimeLimit = check_value($mypasswordCfg['change_password_request_timeout']) ? $mypasswordCfg['change_password_request_timeout']*3600 : 3600;
		$this->_changePasswordSendNewPasswordEmail = $mypasswordCfg['send_new_password_email'] ? true : false;
		
		$forgotpasswordCfg = loadConfigurations('forgotpassword');
		if(!is_array($forgotpasswordCfg)) throw new Exception('Forgotpassword configs missing.');
		
	}
	
	/**
	 * changePassword
	 * password change process
	 */
	public function changePassword() {
		if(!check_value($this->_userid)) throw new Exception(lang('error_4',true));
		if(!check_value($this->_username)) throw new Exception(lang('error_4',true));
		if(!check_value($this->_password)) throw new Exception(lang('error_4',true));
		if(!check_value($this->_newPassword)) throw new Exception(lang('error_4',true));
		
		if(!Validator::AccountPassword($this->_password)) throw new Exception(lang('error_7',true));
		if(!Validator::AccountPassword($this->_newPassword)) throw new Exception(lang('error_7',true));
		
		$accountData = $this->getAccountData();
		if(!is_array($accountData)) throw new Exception(lang('error_12',true));
		$this->setEmail($accountData[_CLMN_EMAIL_]);
		
		if(!$this->_validateAccount()) throw new Exception(lang('error_13',true));
		if($this->isOnline()) throw new Exception(lang('error_14',true));
		
		// password email verification
		if($this->_changePasswordVerificationEnabled) {
			if(is_array($this->_getPasswordChangeRequestData())) throw new Exception(lang('error_19',true));
			$changePasswordRequest = $this->_createPasswordChangeRequest();
			if(!$changePasswordRequest) throw new Exception(lang('error_21',true));
			if(!$this->_sendChangePasswordVerificationEmail()) throw new Exception(lang('error_20',true));
			return;
		}
		
		// update password
		$this->_updatePassword();
		
		// send new password email
		if($this->_changePasswordSendNewPasswordEmail) $this->_sendNewPasswordEmail();
	}
	
	/**
	 * recoverPassword
	 * password recovery process
	 */
	public function recoverPassword() {
		if(!check_value($this->_email)) throw new Exception(lang('error_4',true));
		
		$accountData = $this->getAccountData();
		if(!is_array($accountData)) throw new Exception(lang('error_12',true));
		$this->setUserid($accountData[_CLMN_MEMBID_]);
		$this->setUsername($accountData[_CLMN_USERNM_]);
		$this->setNewPassword($this->_generateRandomPassword($this->_recoveryRandomPasswordLength));
		
		// password email verification
		if(is_array($this->_getPasswordChangeRequestData())) throw new Exception(lang('error_19',true));
		$changePasswordRequest = $this->_createPasswordChangeRequest();
		if(!$changePasswordRequest) throw new Exception(lang('error_21',true));
		if(!$this->_sendPasswordRecoveryVerificationEmail()) throw new Exception(lang('error_20',true));
	}
	
	/**
	 * verifyPassword
	 * verifies the password change request data
	 */
	public function verifyPassword() {
		if(!check_value($this->_username)) throw new Exception(lang('error_21',true));
		if(!check_value($this->_verificationKey)) throw new Exception(lang('error_27',true));
		
		$accountData = $this->getAccountData();
		if(!is_array($accountData)) throw new Exception(lang('error_12',true));
		$this->setUserid($accountData[_CLMN_MEMBID_]);
		$this->setEmail($accountData[_CLMN_EMAIL_]);
		
		// get saved request data
		$requestData = $this->_getPasswordChangeRequestData();
		if(!is_array($requestData)) throw new Exception(lang('error_21',true));
		$this->setNewPassword($requestData['request_password']);
		
		// check key
		if($requestData['request_key'] != $this->_verificationKey) {
			throw new Exception(lang('error_27',true));
		}
		
		// check date
		if(time() > (strtotime($requestData['request_date'])+$this->_changePasswordVerificationTimeLimit)) {
			$this->_deletePasswordChangeRequest();
			throw new Exception(lang('error_26',true));
		}
		
		// update password
		$this->_updatePassword();
		$this->_deletePasswordChangeRequest();
		
		// send new password email
		if($this->_changePasswordSendNewPasswordEmail) $this->_sendNewPasswordEmail();
	}
	
	/**
	 * _createPasswordChangeRequest
	 * creates a new password change request
	 */
	private function _createPasswordChangeRequest() {
		if(!check_value($this->_userid)) return;
		if(!check_value($this->_newPassword)) return;
		
		$this->_verificationKey = $this->_generateVerificationKey();
		
		$data = array(
			'userid' => $this->_userid,
			'newpassword' => $this->_newPassword,
			'key' => $this->_verificationKey
		);
		
		$query = "INSERT INTO WEBENGINE_PASSCHANGE_REQUEST (request_userid,request_password,request_key,request_date) VALUES (:userid, :newpassword, :key, CURRENT_TIMESTAMP)";
		
		$result = $this->db->query($query, $data);
		if(!$result) return;
		
		return true;
	}
	
	/**
	 * _sendNewPasswordEmail
	 * sends an email with the players new password
	 */
	private function _sendNewPasswordEmail() {
		if(!check_value($this->_username)) return;
		if(!check_value($this->_newPassword)) return;
		if(!check_value($this->_email)) return;
		try {
			$email = new Email();
			$email->setTemplate('CHANGE_PASSWORD');
			$email->addVariable('{USERNAME}', $this->_username);
			$email->addVariable('{NEW_PASSWORD}', $this->_newPassword);
			$email->addAddress($this->_email);
			$email->send();
			return true;
		} catch (Exception $ex) {
			# TODO logs system
			return false;
		}
	}
	
	/**
	 * _buildPasswordVerificationLink
	 * builds the password verification link url
	 */
	private function _buildPasswordVerificationLink() {
		if(!check_value($this->_username)) return;
		if(!check_value($this->_verificationKey)) return;
		$verificationLink = __BASE_URL__ . $this->_changePasswordVerificationUrl;
		$verificationLink .= 'user/';
		$verificationLink .= $this->_username;
		$verificationLink .= '/key/';
		$verificationLink .= $this->_verificationKey;
		return $verificationLink;
	}
	
	/**
	 * _sendChangePasswordVerificationEmail
	 * sends the password verification email to the user
	 */
	private function _sendChangePasswordVerificationEmail() {
		if(!check_value($this->_username)) return;
		if(!check_value($this->_email)) return;
		try {
			$expirationTime = sec_to_hms($this->_changePasswordVerificationTimeLimit);
			
			$email = new Email();
			$email->setTemplate('CHANGE_PASSWORD_EMAIL_VERIFICATION');
			$email->addVariable('{USERNAME}', $this->_username);
			$email->addVariable('{DATE}', date("Y-m-d H:i A"));
			$email->addVariable('{IP_ADDRESS}', $_SERVER['REMOTE_ADDR']);
			$email->addVariable('{LINK}', $this->_buildPasswordVerificationLink());
			$email->addVariable('{EXPIRATION_TIME}', $expirationTime[0]);
			$email->addAddress($this->_email);
			$email->send();
			return true;
		} catch (Exception $ex) {
			# TODO logs system
			return;
		}
	}
	
	/**
	 * _sendPasswordRecoveryVerificationEmail
	 * sends the password recovery verification email to the user
	 */
	private function _sendPasswordRecoveryVerificationEmail() {
		if(!check_value($this->_username)) return;
		if(!check_value($this->_email)) return;
		try {
			
			$email = new Email();
			$email->setTemplate('PASSWORD_RECOVERY_REQUEST');
			$email->addVariable('{USERNAME}', $this->_username);
			$email->addVariable('{DATE}', date("Y-m-d H:i A"));
			$email->addVariable('{IP_ADDRESS}', $_SERVER['REMOTE_ADDR']);
			$email->addVariable('{LINK}', $this->_buildPasswordVerificationLink());
			$email->addAddress($this->_email);
			$email->send();
			return true;
		} catch (Exception $ex) {
			# TODO logs system
			return;
		}
	}
	
	/**
	 * _getPasswordChangeRequestData
	 * returns the password request data
	 */
	private function _getPasswordChangeRequestData() {
		if(!check_value($this->_userid)) return;
		$result = $this->db->query_fetch_single("SELECT * FROM WEBENGINE_PASSCHANGE_REQUEST WHERE request_userid = ?", array($this->_userid));
		if(!is_array($result)) return;
		return $result;
	}
	
	/**
	 * _deletePasswordChangeRequest
	 * deletes the password request from the database
	 */
	private function _deletePasswordChangeRequest() {
		if(!check_value($this->_userid)) return;
		$result = $this->db->query("DELETE FROM WEBENGINE_PASSCHANGE_REQUEST WHERE request_userid = ?", array($this->_userid));
		if(!$result) return;
		return true;
	}
	
	/**
	 * _generateRandomPassword
	 * creates a random password
	 * 
	 * credits: http://www.catchstudio.com/labs/password-generator/
	 */
	private function _generateRandomPassword($length=10, $alpha=true, $numeric=true, $special=false) {
		if(!Validator::UnsignedNumber($length)) return;
		
		$chars = '';
		$paswd = '';
		
		$characters = array(
			'alpha' => 'abcdefghijklmnopqrstuvwxyz',
			'numeric' => '1234567890',
			'special' => '#[]@+=$<>*!.-_,%{}'
		);
		
		if($alpha) $chars .= $characters['alpha'] . strtoupper($characters['alpha']);
		if($numeric) $chars .= $characters['numeric'];
		if($special) $chars .= $characters['special'];
		
		$len = strlen($chars);
		for($i=0; $i<$length; $i++) {
			$paswd .= substr($chars, mt_rand(0, $len-1), 1);
		}
		
		return str_shuffle($paswd);
	}
	
}