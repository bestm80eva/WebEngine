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

class AccountEmail extends Account {
	
	function __construct() {
		parent::__construct();
		
	}
	
	public function changeEmailAddress($accountId, $newEmail, $ipAddress) {
		return;
		if(!check_value($accountId)) throw new Exception(lang('error_21',true));
		if(!check_value($newEmail)) throw new Exception(lang('error_21',true));
		if(!check_value($ipAddress)) throw new Exception(lang('error_21',true));
		if(!Validator::Ip($ipAddress)) throw new Exception(lang('error_21',true));
		if(!Validator::Email($newEmail)) throw new Exception(lang('error_21',true));
		
		# check if email already in use
		if($this->emailExists($newEmail)) throw new Exception(lang('error_11',true));
		
		# account info
		$accountInfo = $this->accountInformation($accountId);
		if(!is_array($accountInfo)) throw new Exception(lang('error_21',true));
		
		$myemailCfg = loadConfigurations('usercp.myemail');
		if($myemailCfg['require_verification']) {
			# requires verification
			$userName = $accountInfo[_CLMN_USERNM_];
			$userEmail = $accountInfo[_CLMN_EMAIL_];
			$requestDate = strtotime(date("m/d/Y 23:59"));
			$key = md5(md5($userName).md5($userEmail).md5($requestDate).md5($newEmail));
			$verificationLink = __BASE_URL__.'verifyemail/?op='.Encode_id(3).'&uid='.Encode_id($accountId).'&email='.$newEmail.'&key='.$key;
			
			# send verification email
			$sendEmail = $this->changeEmailVerificationMail($userName, $userEmail, $newEmail, $verificationLink, $ipAddress);
			if(!$sendEmail) throw new Exception(lang('error_21',true));
		} else {
			# no verification required
			if(!$this->updateEmail($accountId, $newEmail)) throw new Exception(lang('error_21',true));
		}
	}
	
	public function changeEmailVerificationProcess($encodedId, $newEmail, $encryptedKey) {
		return;
		$userId = Decode_id($encodedId);
		if(!Validator::UnsignedNumber($userId)) throw new Exception(lang('error_21',true));
		if(!Validator::Email($newEmail)) throw new Exception(lang('error_21',true));
		
		# check if email already in use
		if($this->emailExists($newEmail)) throw new Exception(lang('error_11',true));
		
		# account info
		$accountInfo = $this->accountInformation($userId);
		if(!is_array($accountInfo)) throw new Exception(lang('error_21',true));
		
		# check key
		$requestDate = strtotime(date("m/d/Y 23:59"));
		$key = md5(md5($accountInfo[_CLMN_USERNM_]).md5($accountInfo[_CLMN_EMAIL_]).md5($requestDate).md5($newEmail));
		if($key != $encryptedKey) throw new Exception(lang('error_21',true));
		
		# change email
		if(!$this->updateEmail($userId, $newEmail)) throw new Exception(lang('error_21',true));
	}
	
	private function changeEmailVerificationMail($userName, $emailAddress, $newEmail, $verificationLink, $ipAddress) {
		return;
		try {
			$email = new Email();
			$email->setTemplate('CHANGE_EMAIL_VERIFICATION');
			$email->addVariable('{USERNAME}', $userName);
			$email->addVariable('{IP_ADDRESS}', $ipAddress);
			$email->addVariable('{NEW_EMAIL}', $newEmail);
			$email->addVariable('{LINK}', $verificationLink);
			$email->addAddress($emailAddress);
			$email->send();
			
			return true;
		} catch (Exception $ex) {
			return;
		}
	}
	
	private function generateAccountRecoveryLink($userid,$email,$recovery_code) {
		return;
		if(!check_value($userid)) return;
		if(!check_value($recovery_code)) return;
		
		$build_url = __BASE_URL__;
		$build_url .= 'forgotpassword/';
		$build_url .= '?ui=';
		$build_url .= Encode($userid);
		$build_url .= '&ue=';
		$build_url .= Encode($email);
		$build_url .= '&key=';
		$build_url .= $recovery_code;
		return $build_url;
	}
	
}