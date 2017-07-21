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

class AccountRegister extends Account {
	
	private $_verificationEnabled = true;
	private $_welcomeEmailEnabled = true;
	private $_verificationUrl = 'verification/email/';
	private $_verificationTimeLimit = 86400;
	
	function __construct() {
		parent::__construct();
		
		$registerCfg = loadConfigurations('register');
		if(!is_array($registerCfg)) throw new Exception('Register configs missing.');
		
		$this->_verificationEnabled = $registerCfg['verify_email'] ? true : false;
		$this->_welcomeEmailEnabled = $registerCfg['send_welcome_email'] ? true : false;
		$this->_verificationTimeLimit = check_value($registerCfg['verification_timelimit']) ? $registerCfg['verification_timelimit']*3600 : 86400;
		
	}
	
	/**
	 * registerAccount
	 * new account registration process
	 */
	public function registerAccount() {
		if(!check_value($this->_username)) throw new Exception('The username is required to create your account.');
		if(!check_value($this->_password)) throw new Exception('The password is required to create your account.');
		if(!check_value($this->_email)) throw new Exception('The email address is required to create your account.');
		
		if($this->usernameExists($this->_username)) throw new Exception('The username you entered is already in use, click here to recover access to your account.');
		if($this->emailExists($this->_email)) throw new Exception('The email address you entered is already in use, click here to recover access to your account.');
		
		// email verification
		if($this->_verificationEnabled) {
			$saveRegistration = $this->_saveRegistration();
			if(!$saveRegistration) throw new Exception('There was an error creating your account, please contact support.');
			if(!$this->_sendVerificationEmail()) throw new Exception('There was an error sending the verification email, please contact support.');
			return;
		}
		
		// regular registration
		$createAccount = $this->_createAccount();
		if(!$createAccount) throw new Exception('There was an error creating your account, please contact support.');
		
		// welcome email
		if($this->_welcomeEmailEnabled) $this->_sendWelcomeEmail();
	}
	
	/**
	 * verifyEmail
	 * verifies a saved registration and creates the account
	 */
	public function verifyEmail() {
		if(!check_value($this->_username)) throw new Exception('The username is required to verify your email.');
		if(!check_value($this->_verificationKey)) throw new Exception('The verification key is missing.');
		
		// get saved registration data
		$registrationData = $this->_getVerificationAccountData();
		if(!is_array($registrationData)) throw new Exception(lang('error_21',true));
		
		// check key
		if($registrationData['registration_key'] != $this->_verificationKey) {
			throw new Exception('The verification key provided is not valid.');
		}
		
		// check date
		if(time() > (strtotime($registrationData['registration_date'])+$this->_verificationTimeLimit)) {
			$this->_deleteSavedRegistration();
			throw new Exception('verification time limit expired, please register again.');
		}
		
		// create account
		$this->setPassword($registrationData['registration_password']);
		$this->setEmail($registrationData['registration_email']);
		$createAccount = $this->_createAccount();
		if(!$createAccount) throw new Exception('There was an error creating your account, please contact support.');
		
		// delete saved registration data
		$this->_deleteSavedRegistration();
		
		// welcome email
		if($this->_welcomeEmailEnabled) $this->_sendWelcomeEmail();
	}
	
	/**
	 * _saveRegistration
	 * saves the account registration data
	 */
	private function _saveRegistration() {
		if($this->_usernamePendingVerification()) throw new Exception('The username you entered is pending activation, if this is your account please make sure to check your inbox for the activation email.');
		if($this->_emailPendingVerification()) throw new Exception('The email address you entered is pending activation, if this is your account please make sure to check your inbox for the activation email.');
		
		$this->_verificationKey = $this->_generateVerificationKey();
		
		$data = array(
			'account' => $this->_username,
			'password' => $this->_password,
			'email' => $this->_email,
			'key' => $this->_verificationKey
		);
		
		$query = "INSERT INTO WEBENGINE_REGISTER_ACCOUNT (registration_account,registration_password,registration_email,registration_date,registration_key) VALUES (:account, :password, :email, CURRENT_TIMESTAMP, :key)";
		
		$result = $this->db->query($query, $data);
		if(!$result) return;
		
		return true;
	}
	
	/**
	 * _usernamePendingVerification
	 * checks if the username is pending verification
	 */
	private function _usernamePendingVerification() {
		if(!check_value($this->_username)) return;
		$result = $this->db->query_fetch_single("SELECT * FROM WEBENGINE_REGISTER_ACCOUNT WHERE registration_account = ?", array($this->_username));
		if(!is_array($result)) return;
		return true;
	}
	
	/**
	 * _emailPendingVerification
	 * checks if the email is pending verification
	 */
	private function _emailPendingVerification() {
		if(!check_value($this->_email)) return;
		$result = $this->db->query_fetch_single("SELECT * FROM WEBENGINE_REGISTER_ACCOUNT WHERE registration_email = ?", array($this->_email));
		if(!is_array($result)) return;
		return true;
	}
	
	/**
	 * _buildVerificationLink
	 * builds the verification link url
	 */
	private function _buildVerificationLink() {
		if(!check_value($this->_username)) return;
		if(!check_value($this->_verificationKey)) return;
		$verificationLink = __BASE_URL__ . $this->_verificationUrl;
		$verificationLink .= 'user/';
		$verificationLink .= $this->_username;
		$verificationLink .= '/key/';
		$verificationLink .= $this->_verificationKey;
		return $verificationLink;
	}
	
	/**
	 * _sendVerificationEmail
	 * sends a verification email to the player
	 */
	private function _sendVerificationEmail() {
		try {
			$email = new Email();
			$email->setTemplate('WELCOME_EMAIL_VERIFICATION');
			$email->addVariable('{USERNAME}', $this->_username);
			$email->addVariable('{LINK}', $this->_buildVerificationLink());
			$email->addAddress($this->_email);
			$email->send();
			return true;
		} catch (Exception $ex) {
			# TODO logs system
			return;
		}
	}
	
	/**
	 * _getVerificationAccountData
	 * returns saved registration data
	 */
	private function _getVerificationAccountData() {
		if(!check_value($this->_username)) return;
		$result = $this->db->query_fetch_single("SELECT * FROM WEBENGINE_REGISTER_ACCOUNT WHERE registration_account = ?", array($this->_username));
		if(!is_array($result)) return;
		return $result;
	}
	
	/**
	 * _deleteSavedRegistration
	 * deletes saved registration data
	 */
	private function _deleteSavedRegistration() {
		if(!check_value($this->_username)) return;
		$result = $this->db->query("DELETE FROM WEBENGINE_REGISTER_ACCOUNT WHERE registration_account = ?", array($this->_username));
		if(!$result) return;
		return true;
	}
	
	/**
	 * _sendWelcomeEmail
	 * sends welcome email to the player
	 */
	private function _sendWelcomeEmail() {
		if(!check_value($this->_username)) return;
		if(!check_value($this->_email)) return;
		
		try {
			$email = new Email();
			$email->setTemplate('WELCOME_EMAIL');
			$email->addVariable('{USERNAME}', $this->_username);
			$email->addAddress($this->_email);
			$email->send();
			return true;
		} catch (Exception $ex) {
			# TODO logs system
			return;
		}
	}
	
}