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

class Player {
	
	protected $_username;
	protected $_playerid;
	protected $_player;
	
	protected $_playerInformation;
	protected $_playerMasterLevelInformation;
	
	protected $_playerEditTable;
	protected $_playerEditNameColumn;
	protected $_playerEditList = array();
	
	
	
	function __construct() {
		global $dB;
		
		// character database
		$this->db = $dB;
		
		// default edit table and name column
		$this->_playerEditTable = _TBL_CHR_;
		$this->_playerEditNameColumn = _CLMN_CHR_NAME_;
	}
	
	/**
	 * setUsername
	 * 
	 */
	public function setUsername($value) {
		if(!check_value($value)) throw new Exception('The username you entered is not valid.');
		if(!Validator::AccountUsername($value)) throw new Exception('The username you entered is not valid.');
		
		$this->_username = $value;
	}
	
	/**
	 * setPlayer
	 * 
	 */
	public function setPlayer($value) {
		
		$this->_player = $value;
	}
	
	/**
	 * getAccountPlayerList
	 * 
	 */
	public function getAccountPlayerList() {
		if(!check($this->_username)) throw new Exception('Account username not set, cannot load account characters.');
		
		$result = $this->db->query_fetch_single("SELECT "._CLMN_GAMEID_1_.","._CLMN_GAMEID_2_.","._CLMN_GAMEID_3_.","._CLMN_GAMEID_4_.","._CLMN_GAMEID_5_." FROM "._TBL_AC_." WHERE "._CLMN_AC_ID_." = ?", array($this->_username));
		if(!is_array($result)) return;
		return $result;
	}
	
	/**
	 * getAccountPlayerIDC
	 * 
	 */
	public function getAccountPlayerIDC() {
		if(!check($this->_username)) throw new Exception('Account username not set, cannot load account characters.');
		
		$result = $this->db->query_fetch_single("SELECT "._CLMN_GAMEIDC_." FROM "._TBL_AC_." WHERE "._CLMN_AC_ID_." = ?", array($this->_username));
		if(!is_array($result)) return;
		return $result[_CLMN_GAMEIDC_];
	}
	
	/**
	 * belongsToAccount
	 * 
	 */
	public function belongsToAccount() {
		if(!check($this->_username)) throw new Exception('Account username not set.');
		if(!check($this->_player)) throw new Exception(lang('error_24', true));
		
		$accountCharacters = $this->getAccountPlayerList();
		if(!in_array($this->_player, $accountCharacters)) return;
		return true;
	}
	
	/**
	 * getPlayerInformation
	 * 
	 */
	public function getPlayerInformation() {
		if(!check($this->_player)) throw new Exception(lang('error_24', true));
		
		$this->_loadPlayerInformation();
		if(!is_array($this->_playerInformation)) return;
		return $this->_playerInformation;
	}
	
	/**
	 * getPlayerMasterLevelInformation
	 * 
	 */
	public function getPlayerMasterLevelInformation() {
		if(!check($this->_player)) throw new Exception(lang('error_24', true));
		
		$this->_loadMasterLevelInformation();
		if(!is_array($this->_playerMasterLevelInformation)) return;
		return $this->_playerMasterLevelInformation;
	}
	
	/**
	 * _setEditTable
	 * 
	 */
	protected function _setEditTable($table) {
		if(!check($table)) throw new Exception('Edit table is not valid.');
		
		$this->_playerEditTable = $table;
	}
	
	/**
	 * _setEditNameColumn
	 * 
	 */
	protected function _setEditNameColumn($column) {
		if(!check($column)) throw new Exception('Edit table is not valid.');
		
		$this->_playerEditNameColumn = $column;
	}
	
	/**
	 * _editValue
	 * 
	 */
	protected function _editValue($column, $value) {
		if(!check($column, $value)) throw new Exception('Edit value is not valid.');
		
		$this->_playerEditList[$column] = $value;
	}
	
	/**
	 * _saveEdits
	 * 
	 */
	protected function _saveEdits() {
		if(!check($this->_player)) throw new Exception(lang('error_24', true));
		if(!is_array($this->_playerEditList)) throw new Exception('No edits have been set.');
		if(count($this->_playerEditList) < 1) throw new Exception('No edits have been set.');
		
		$columnList = array();
		$valueList = array();
		
		foreach($this->_playerEditList as $column => $value) {
			$columnList[] = $column;
			$valueList[] = $value;
		}
		
		$valueList[] = $this->_player;
		
		$queryColumns = implode(' = ?, ', $columnList) . ' = ?';
		$query = "UPDATE ".$this->_playerEditTable." SET ".$queryColumns." WHERE ".$this->_playerEditNameColumn." = ?";
		
		$result = $this->db->query($query, $valueList);
		if($result) return true;
		return;
	}
	
	/**
	 * _loadPlayerInformation
	 * 
	 */
	protected function _loadPlayerInformation() {
		if(!check($this->_player)) throw new Exception(lang('error_24', true));
		
		$result = $this->db->query_fetch_single("SELECT * FROM "._TBL_CHR_." WHERE "._CLMN_CHR_NAME_." = ?", array($this->_player));
		if(!is_array($result)) return;
		$this->_playerInformation = $result;
	}
	
	/**
	 * _loadMasterLevelInformation
	 * 
	 */
	protected function _loadMasterLevelInformation() {
		if(!check($this->_player)) throw new Exception(lang('error_24', true));
		
		$result = $this->db->query_fetch_single("SELECT * FROM "._TBL_MASTERLVL_." WHERE "._CLMN_ML_NAME_." = ?", array($this->_player));
		if(!is_array($result)) return;
		$this->_playerMasterLevelInformation = $result;
	}
	
	
	
}