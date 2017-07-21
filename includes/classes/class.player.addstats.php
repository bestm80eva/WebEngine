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

class PlayerAddStats extends Player {
	
	private $_configurationFile = 'usercp.addstats';
	
	protected $_requiredZen = 0;
	protected $_maxStats = 32767;
	protected $_minAddPoints = 10;
	
	protected $_str;
	protected $_agi;
	protected $_vit;
	protected $_ene;
	protected $_cmd;
	
	function __construct() {
		parent::__construct();
		
		$cfg = loadConfigurations($this->_configurationFile);
		
		$this->_requiredZen = $cfg['addstats_price_zen'];
		$this->_maxStats = $cfg['addstats_max_stats'];
		$this->_minAddPoints = $cfg['addstats_minimum_add_points'];
	}
	
	public function setStrength($value) {
		if(!Validator::Number($value, $this->_maxStats, $this->_minAddPoints)) throw new Exception(lang('error_53', true));
		$this->_str = $value;
	}
	
	public function setAgility($value) {
		if(!Validator::Number($value, $this->_maxStats, $this->_minAddPoints)) throw new Exception(lang('error_53', true));
		$this->_agi = $value;
	}
	
	public function setVitality($value) {
		if(!Validator::Number($value, $this->_maxStats, $this->_minAddPoints)) throw new Exception(lang('error_53', true));
		$this->_vit = $value;
	}
	
	public function setEnergy($value) {
		if(!Validator::Number($value, $this->_maxStats, $this->_minAddPoints)) throw new Exception(lang('error_53', true));
		$this->_ene = $value;
	}
	
	public function setCommand($value) {
		if(!Validator::Number($value, $this->_maxStats, $this->_minAddPoints)) throw new Exception(lang('error_53', true));
		$this->_cmd = $value;
	}
	
	public function addstats() {
		if(!check($this->_player)) throw new Exception(lang('error_24', true));
		
		// get player information
		$playerInformation = $this->getPlayerInformation();
		if(!is_array($playerInformation)) throw new Exception(lang('error_67', true));
		
		// check if player belongs to account
		if(!$this->belongsToAccount()) throw new Exception(lang('error_72', true));
		
		// check points
		$str = $playerInformation[_CLMN_CHR_STAT_STR_]+$this->_str;
		$agi = $playerInformation[_CLMN_CHR_STAT_AGI_]+$this->_agi;
		$vit = $playerInformation[_CLMN_CHR_STAT_VIT_]+$this->_vit;
		$ene = $playerInformation[_CLMN_CHR_STAT_ENE_]+$this->_ene;
		$cmd = $playerInformation[_CLMN_CHR_STAT_CMD_]+$this->_cmd;
		
		if(!Validator::Number($str, $this->_maxStats, 0)) throw new Exception(lang('error_53', true));
		if(!Validator::Number($agi, $this->_maxStats, 0)) throw new Exception(lang('error_53', true));
		if(!Validator::Number($vit, $this->_maxStats, 0)) throw new Exception(lang('error_53', true));
		if(!Validator::Number($ene, $this->_maxStats, 0)) throw new Exception(lang('error_53', true));
		if(!Validator::Number($cmd, $this->_maxStats, 0)) throw new Exception(lang('error_53', true));
		
		$totalAddStats = $this->_str+$this->_agi+$this->_vit+$this->_ene+$this->_cmd;
		if($totalAddStats > $playerInformation[_CLMN_CHR_LVLUP_POINT_]) throw new Exception(lang('error_51', true));
		
		// edits begin
		
		// zen requirement
		if($this->_requiredZen >= 1) {
			if($playerInformation[_CLMN_CHR_ZEN_] < $this->_requiredZen) throw new Exception(lang('error_34', true));
			$this->_editValue(_CLMN_CHR_ZEN_, ($playerInformation[_CLMN_CHR_ZEN_]-$this->_requiredZen));
		}
		
		// set stats
		if(check($this->_str) && $this->_str>=$this->_minAddPoints) $this->_editValue(_CLMN_CHR_STAT_STR_, $str);
		if(check($this->_agi) && $this->_agi>=$this->_minAddPoints) $this->_editValue(_CLMN_CHR_STAT_AGI_, $agi);
		if(check($this->_vit) && $this->_vit>=$this->_minAddPoints) $this->_editValue(_CLMN_CHR_STAT_VIT_, $vit);
		if(check($this->_ene) && $this->_ene>=$this->_minAddPoints) $this->_editValue(_CLMN_CHR_STAT_ENE_, $ene);
		if(check($this->_cmd) && $this->_cmd>=$this->_minAddPoints) $this->_editValue(_CLMN_CHR_STAT_CMD_, $cmd);
		
		// reset level up points
		$this->_editValue(_CLMN_CHR_LVLUP_POINT_, ($playerInformation[_CLMN_CHR_LVLUP_POINT_]-$totalAddStats));
		
		// save
		if(!$this->_saveEdits()) throw new Exception(lang('error_73', true));
	}
	
}