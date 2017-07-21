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

class PlayerClearPk extends Player {
	
	private $_configurationFile = 'usercp.clearpk';
	
	protected $_defaultPkLevel = 3;
	protected $_requiredZen = 0;
	
	function __construct() {
		parent::__construct();
		
		$cfg = loadConfigurations($this->_configurationFile);
		
		$this->_requiredZen = $cfg['clearpk_price_zen'];
		$this->_defaultPkLevel = $cfg['default_pk_level'];
	}
	
	public function clearpk() {
		if(!check($this->_player)) throw new Exception(lang('error_24', true));
		
		// get player information
		$playerInformation = $this->getPlayerInformation();
		if(!is_array($playerInformation)) throw new Exception(lang('error_67', true));
		
		// check if player belongs to account
		if(!$this->belongsToAccount()) throw new Exception(lang('error_36', true));
		
		// edits begin
		
		// zen requirement
		if($this->_requiredZen >= 1) {
			if($playerInformation[_CLMN_CHR_ZEN_] < $this->_requiredZen) throw new Exception(lang('error_34', true));
			$this->_editValue(_CLMN_CHR_ZEN_, ($playerInformation[_CLMN_CHR_ZEN_]-$this->_requiredZen));
		}
		
		// pk level
		$this->_editValue(_CLMN_CHR_PK_LEVEL_, $this->_defaultPkLevel);
		$this->_editValue(_CLMN_CHR_PK_TIME_, 0);
		
		// clear pk
		if(!$this->_saveEdits()) throw new Exception(lang('error_70', true));
	}
	
}