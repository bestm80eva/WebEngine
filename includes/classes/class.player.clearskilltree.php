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

class PlayerClearSkillTree extends Player {
	
	private $_configurationFile = 'usercp.clearskilltree';
	
	protected $_defaltMasterLevel = 0;
	protected $_defaltMasterLevelExp = 0;
	protected $_defaltMasterLevelNextExp = 0;
	protected $_defaltMasterLevelPoint= 0;
	
	protected $_requiredZen = 0;
	protected $_requiredMastelLevel = 0;
	
	function __construct() {
		parent::__construct();
		
		$cfg = loadConfigurations($this->_configurationFile);
		
		$this->_requiredZen = $cfg['clearst_price_zen'];
		$this->_requiredMastelLevel = $cfg['clearst_required_level'];
	}
	
	public function clearskilltree() {
		if(!check($this->_player)) throw new Exception(lang('error_24', true));
		
		// get player information
		$playerInformation = $this->getPlayerInformation();
		if(!is_array($playerInformation)) throw new Exception(lang('error_67', true));
		
		// get player master level information
		$playerMLInformation = $this->getPlayerMasterLevelInformation();
		if(!is_array($playerMLInformation)) throw new Exception(lang('error_67', true));
		
		// check if player belongs to account
		if(!$this->belongsToAccount()) throw new Exception(lang('error_36', true));
		
		// edits begin
		
		$this->_setEditTable(_TBL_MASTERLVL_);
		$this->_setEditNameColumn(_CLMN_ML_NAME_);
		
		// zen requirement
		if($this->_requiredZen >= 1) {
			if($playerInformation[_CLMN_CHR_ZEN_] < $this->_requiredZen) throw new Exception(lang('error_34', true));
			$this->_editValue(_CLMN_CHR_ZEN_, ($playerInformation[_CLMN_CHR_ZEN_]-$this->_requiredZen));
		}
		
		// master level requirement
		if($this->_requiredMastelLevel >= 1) {
			if($playerMLInformation[_CLMN_ML_LVL_] < $this->_requiredMastelLevel) throw new Exception(lang('error_39', true));
		}
		
		// master level
		$this->_editValue(_CLMN_ML_LVL_, $this->_defaltMasterLevel);
		$this->_editValue(_CLMN_ML_EXP_, $this->_defaltMasterLevelExp);
		$this->_editValue(_CLMN_ML_NEXP_, $this->_defaltMasterLevelNextExp);
		$this->_editValue(_CLMN_ML_POINT_, $this->_defaltMasterLevelPoint);
		
		// clear skill tree
		if(!$this->_clearMagicList()) throw new Exception(lang('error_74', true));
		
		// save
		if(!$this->_saveEdits()) throw new Exception(lang('error_74', true));
	}
	
	private function _clearMagicList() {
		if(!check($this->_player)) return;
		
		$result = $this->db->query("UPDATE "._TBL_CHR_." SET "._CLMN_CHR_MAGIC_L_." = null WHERE "._CLMN_CHR_NAME_." = ?", array($this->_player));
		if(!$result) return;
		return true;
	}
	
}