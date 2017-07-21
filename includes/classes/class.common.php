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

class common extends Account {
	
	protected $_serverFiles = 'MUE';
	
	function __construct() {
		parent::__construct();
		
		$this->_serverFiles = config('server_files',true);
	}
	
	// TO BE REMOVED
	public function emailExists($email) {
		$this->setEmail($email);
		$accountData = $this->getAccountData();
		
		if(!is_array($accountData)) return;
		return true;
	}
	
	// TO BE REMOVED
	public function userExists($username) {
		$this->setUsername($username);
		$accountData = $this->getAccountData();
		
		if(!is_array($accountData)) return;
		return true;
	}
	
	// TO BE REMOVED
	public function validateUser($username,$password) {
		$this->setUsername($username);
		$this->setPassword($password);
		
		if(!$this->_validateAccount()) return;
		return true;
	}
	
	// TO BE REMOVED
	public function retrieveUserID($username) {
		$this->setUsername($username);
		$accountData = $this->getAccountData();
		
		if(!is_array($accountData)) return;
		return $accountData[_CLMN_MEMBID_];
	}
	
	// TO BE REMOVED
	public function retrieveUserIDbyEmail($email) {
		$this->setEmail($email);
		$accountData = $this->getAccountData();
		
		if(!is_array($accountData)) return;
		return $accountData[_CLMN_MEMBID_];
	}
	
	// TO BE REMOVED
	public function accountInformation($id) {
		$this->setUserid($id);
		$accountData = $this->getAccountData();
		
		if(!is_array($accountData)) return;
		return $accountData;
	}
	
	// TO BE REMOVED
	public function accountOnline($username) {
		$this->setUsername($username);
		
		if(!$this->isOnline()) return;
		return true;
	}
	
	// TO BE REMOVED
	public function substractCredits($userid,$amount) {
		# ONLY FOR MUE
		if(!check_value($userid)) return;
		if(!check_value($amount)) return;
		if(!Validator::UnsignedNumber($userid)) return;
		if(!Validator::UnsignedNumber($amount)) return;
		
		$userData = $this->accountInformation($userid);
		if(!is_array($userData)) return;
		
		$thisCredits = $userData[_CLMN_CREDITS_];
		if($thisCredits < $amount) return;
		
		$subtract = $this->db->query("UPDATE "._TBL_MI_." SET "._CLMN_CREDITS_." = "._CLMN_CREDITS_." - ? WHERE "._CLMN_MEMBID_." = ?", array($amount, $userid));
		if($subtract) return true;
		return;
	}
	
	// TO BE REMOVED
	public function blockAccount($userid) {
		$this->setUserid($userid);
		
		if(!$this->blockAccount()) return;
		return true;
	}
	
	// TO BE REMOVED
	public function paypal_transaction($transaction_id,$user_id,$payment_amount,$paypal_email,$order_id) {
		if(!check_value($transaction_id)) return;
		if(!check_value($user_id)) return;
		if(!check_value($payment_amount)) return;
		if(!check_value($paypal_email)) return;
		if(!check_value($order_id)) return;
		if(!Validator::UnsignedNumber($user_id)) return;
		
		$data = array(
			$transaction_id,
			$user_id,
			$payment_amount,
			$paypal_email,
			time(),
			1,
			$order_id
		);
		
		$query = "INSERT INTO WEBENGINE_PAYPAL_TRANSACTIONS (transaction_id, user_id, payment_amount, paypal_email, transaction_date, transaction_status, order_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
		$result = $this->db->query($query, $data);
		if($result) return true;
		return;
	}
	
	// TO BE REMOVED
	public function paypal_transaction_reversed_updatestatus($order_id) {
		if(check_value($order_id)) return;
		$result = $this->db->query("UPDATE WEBENGINE_PAYPAL_TRANSACTIONS SET transaction_status = ? WHERE order_id = ?", array(0, $order_id));
		if($result) return true;
		return;
	}
	
	// TO BE REMOVED
	public function retrieveAccountIPs($username) {
		if(!check_value($username)) return;
		if(!$this->userExists($username)) return;
		switch($this->_serverFiles) {
			case 'MUE':
				$result = $this->db->query_fetch("SELECT "._CLMN_LOGEX_IP_." FROM "._TBL_LOGEX_." WHERE "._CLMN_LOGEX_ACCID_." = ? GROUP BY "._CLMN_LOGEX_IP_."", array($username));
				if(is_array($result)) return $result;
				return;
			default:
				return;
		}
	}
	
	public function isIpBlocked($ip) {
		if(!Validator::Ip($ip)) return true; // automatically block ip if invalid
		$result = $this->db->query_fetch_single("SELECT * FROM WEBENGINE_BLOCKED_IP WHERE block_ip = ?", array($ip));
		if(is_array($result)) return true;
		return;
	}
	
	public function blockIpAddress($ip,$user) {
		if(!check_value($user)) return;
		if(!Validator::Ip($ip)) return;
		if($this->isIpBlocked($ip)) return;
		$result = $this->db->query("INSERT INTO WEBENGINE_BLOCKED_IP (block_ip,block_by,block_date) VALUES (?,?,?)", array($ip,$user,time()));
		if($result) return true;
	}
	
	public function retrieveBlockedIPs() {
		return $this->db->query_fetch("SELECT * FROM WEBENGINE_BLOCKED_IP ORDER BY id DESC");
	}
	
	public function unblockIpAddress($id) {
		if(!check_value($id)) return;
		$result = $this->db->query("DELETE FROM WEBENGINE_BLOCKED_IP WHERE id = ?", array($id));
		if($result) return true;
		return;
	}
	
	// TO BE REMOVED
	public function updateEmail($userid, $newemail) {
		if(!Validator::UnsignedNumber($userid)) return;
		if(!Validator::Email($newemail)) return;
		$result = $this->db->query("UPDATE "._TBL_MI_." SET "._CLMN_EMAIL_." = ? WHERE "._CLMN_MEMBID_." = ?", array($newemail, $userid));
		if($result) return true;
		return;
	}
	
}