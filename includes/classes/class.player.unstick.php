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

class PlayerUnstick extends Player {
	
	private $_configurationFile = 'usercp.unstick';
	
	protected $_requiredZen = 0;
	protected $_map = 0;
	protected $_coordx = 125;
	protected $_coordy = 125;
	
	function __construct() {
		parent::__construct();
		
		$cfg = loadConfigurations($this->_configurationFile);
		
		$this->_requiredZen = $cfg['unstick_price_zen'];
		$this->_map = $cfg['unstick_map'];
		$this->_coordx = $cfg['unstick_coord_x'];
		$this->_coordy = $cfg['unstick_coord_y'];
	}
	
	public function unstick() {
		if(!check($this->_player)) throw new Exception(lang('error_24', true));
		
		// get player information
		$playerInformation = $this->getPlayerInformation();
		if(!is_array($playerInformation)) throw new Exception(lang('error_67', true));
		
		// check if player belongs to account
		if(!$this->belongsToAccount()) throw new Exception(lang('error_37', true));
		
		// edits begin

		// zen requirement
		if($this->_requiredZen >= 1) {
			if($playerInformation[_CLMN_CHR_ZEN_] < $this->_requiredZen) throw new Exception(lang('error_34', true));
			$this->_editValue(_CLMN_CHR_ZEN_, ($playerInformation[_CLMN_CHR_ZEN_]-$this->_requiredZen));
		}
		
		// location reset
		$this->_editValue(_CLMN_CHR_MAP_, $this->_map);
		$this->_editValue(_CLMN_CHR_MAP_X_, $this->_coordx);
		$this->_editValue(_CLMN_CHR_MAP_Y_, $this->_coordy);
		
		// unstick
		if(!$this->_saveEdits()) throw new Exception(lang('error_69', true));
	}
	
}