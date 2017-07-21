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

class PlayerRebirth extends Player {
	
	private $_configurationFile = 'usercp.reset';
	protected $_defaultLevel = 1;
	protected $_rebirthIncrement = 1;
	
	protected $_requiredLevel = 400;
	protected $_requiredZen = 0;
	
	protected $_resetStats = false;
	protected $_resetStatsValue = 25;
	protected $_resetLevelUpPoints = false;
	protected $_resetLevelUpPointsValue = 0;
	protected $_rewardLevelUpPoints = false;
	protected $_rewardLevelUpPointsAmount = 500;
	protected $_rewardCredits = false;
	protected $_rewardCreditsConfig = 0;
	protected $_rewardCreditsAmount = 0;
	
	function __construct() {
		parent::__construct();
		
		$cfg = loadConfigurations($this->_configurationFile);
		
		$this->_requiredLevel = $cfg['resets_required_level'];
		$this->_requiredZen = $cfg['resets_price_zen'];
		$this->_resetStats = $cfg['reset_stats'];
		$this->_resetStatsValue = $cfg['default_stats'];
		$this->_resetLevelUpPoints = $cfg['reset_leveluppoints'];
		$this->_resetLevelUpPointsValue = $cfg['default_leveluppoints'];
		$this->_rewardLevelUpPoints = $cfg['reward_leveluppoints'];
		$this->_rewardLevelUpPointsAmount = $cfg['reward_leveluppoints_amount'];
		$this->_rewardCredits = $cfg['resets_enable_credit_reward'] = 1 ? true : false;
		$this->_rewardCreditsConfig = $cfg['credit_config'];
		$this->_rewardCreditsAmount = $cfg['resets_credits_reward'];
		
	}
	
	public function rebirth() {
		if(!check($this->_player)) throw new Exception(lang('error_24', true));
		
		// get player information
		$playerInformation = $this->getPlayerInformation();
		if(!is_array($playerInformation)) throw new Exception(lang('error_67', true));
		
		// check if player belongs to account
		if(!$this->belongsToAccount()) throw new Exception(lang('error_32', true));
		
		// edits begin
		
		// level requirement
		if($this->_requiredLevel >= 1) {
			if($playerInformation[_CLMN_CHR_LVL_] < $this->_requiredLevel) throw new Exception(lang('error_33', true));
			$this->_editValue(_CLMN_CHR_LVL_, $this->_defaultLevel);
		}
		
		// zen requirement
		if($this->_requiredZen >= 1) {
			if($playerInformation[_CLMN_CHR_ZEN_] < $this->_requiredZen) throw new Exception(lang('error_34', true));
			$this->_editValue(_CLMN_CHR_ZEN_, ($playerInformation[_CLMN_CHR_ZEN_]-$this->_requiredZen));
		}
		
		// if stats reset is enabled, the following will be set to the reset value
		if($this->_resetStats) {
			$this->_editValue(_CLMN_CHR_STAT_STR_, $this->_resetStatsValue);
			$this->_editValue(_CLMN_CHR_STAT_AGI_, $this->_resetStatsValue);
			$this->_editValue(_CLMN_CHR_STAT_VIT_, $this->_resetStatsValue);
			$this->_editValue(_CLMN_CHR_STAT_ENE_, $this->_resetStatsValue);
			$this->_editValue(_CLMN_CHR_STAT_CMD_, $this->_resetStatsValue);
		}
		
		// reset level up points
		if($this->_resetLevelUpPoints) {
			$this->_editValue(_CLMN_CHR_LVLUP_POINT_, $this->_resetLevelUpPointsValue);
		}
		
		// reward level up points
		if($this->_rewardLevelUpPoints) {
			$this->_editValue(_CLMN_CHR_LVLUP_POINT_, ($playerInformation[_CLMN_CHR_LVLUP_POINT_]+$this->_rewardLevelUpPointsAmount));
		}
		
		// reward credit system
		if($this->_rewardCredits) {
			
			// @@@@@@@
			// @@@@@
			// @@@@
			// TODO
			// @@@@
			// @@@@@
			// @@@@@@@
			
		}
		
		// rebirth increment
		$this->_editValue(_CLMN_CHR_RSTS_, ($playerInformation[_CLMN_CHR_RSTS_]+$this->_rebirthIncrement));
		
		// rebirth
		if(!$this->_saveEdits()) throw new Exception(lang('error_68', true));
	}
	
	
}