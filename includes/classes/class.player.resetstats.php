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

class PlayerResetStats extends Player {
	
	private $_configurationFile = 'usercp.resetstats';
	
	protected $_requiredZen = 0;
	protected $_defaultStats = 25;
	protected $_characterCmd;
	
	function __construct() {
		parent::__construct();
		
		$cfg = loadConfigurations($this->_configurationFile);
		
		$this->_requiredZen = $cfg['resetstats_price_zen'];
		$this->_defaultStats = $cfg['resetstats_new_stats'];
		$this->_characterCmd = custom('character_cmd');
	}
	
	public function resetstats() {
		if(!check($this->_player)) throw new Exception(lang('error_24', true));
		
		// get player information
		$playerInformation = $this->getPlayerInformation();
		if(!is_array($playerInformation)) throw new Exception(lang('error_67', true));
		
		// check if player belongs to account
		if(!$this->belongsToAccount()) throw new Exception(lang('error_35', true));
		
		// edits begin
		
		// zen requirement
		if($this->_requiredZen >= 1) {
			if($playerInformation[_CLMN_CHR_ZEN_] < $this->_requiredZen) throw new Exception(lang('error_34', true));
			$this->_editValue(_CLMN_CHR_ZEN_, ($playerInformation[_CLMN_CHR_ZEN_]-$this->_requiredZen));
		}
		
		// reset stats
		$this->_editValue(_CLMN_CHR_STAT_STR_, $this->_defaultStats);
		$this->_editValue(_CLMN_CHR_STAT_AGI_, $this->_defaultStats);
		$this->_editValue(_CLMN_CHR_STAT_VIT_, $this->_defaultStats);
		$this->_editValue(_CLMN_CHR_STAT_ENE_, $this->_defaultStats);
		$this->_editValue(_CLMN_CHR_STAT_CMD_, $this->_defaultStats);
		
		// add level up points
		$totalStats = $playerInformation[_CLMN_CHR_STAT_STR_]+$playerInformation[_CLMN_CHR_STAT_AGI_]+$playerInformation[_CLMN_CHR_STAT_VIT_]+$playerInformation[_CLMN_CHR_STAT_ENE_]+$playerInformation[_CLMN_CHR_STAT_CMD_];
		$totalDefault = in_array($playerInformation[_CLMN_CHR_CLASS_], $this->_characterCmd) ? $this->_defaultStats*5 : $this->_defaultStats*4;
		$this->_editValue(_CLMN_CHR_LVLUP_POINT_, ($totalStats-$totalDefault));
		
		// save
		if(!$this->_saveEdits()) throw new Exception(lang('error_71', true));
	}
	
}