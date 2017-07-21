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

// module configs
$cfg = loadConfigurations('usercp.reset');
if(!is_array($cfg)) throw new Exception(lang('error_66',true));

// module status
if(!$cfg['active']) throw new Exception(lang('error_47',true));

// player information
$Player = new PlayerRebirth();
$Player->setUsername($_SESSION['username']);
$accountPlayers = $Player->getAccountPlayerList();
if(!is_array($accountPlayers)) throw new Exception(lang('error_46',true));

// form submit
if(check($_GET['player'])) {
	try {
		$playerRebirth = new PlayerRebirth();
		$playerRebirth->setUsername($_SESSION['username']);
		$playerRebirth->setPlayer($_GET['player']);
		$playerRebirth->rebirth();
		
		message('success', lang('success_8',true));
		if($cfg['resets_enable_credit_reward']) message('success', langf('resetcharacter_txt_8', array($cfg['resets_credits_reward'])));
	} catch(Exception $ex) {
		message('error', $ex->getMessage());
	}
}

// form
echo '<table class="table general-table-ui">';
	echo '<tr>';
		echo '<td></td>';
		echo '<td>'.lang('resetcharacter_txt_1',true).'</td>';
		echo '<td>'.lang('resetcharacter_txt_2',true).'</td>';
		echo '<td>'.lang('resetcharacter_txt_3',true).'</td>';
		echo '<td>'.lang('resetcharacter_txt_4',true).'</td>';
		echo '<td></td>';
	echo '</tr>';
	
	foreach($accountPlayers as $playerName) {
		$Player->setPlayer($playerName);
		$playerInformation = $Player->getPlayerInformation();
		
		echo '<tr>';
			echo '<td>'.returnPlayerAvatar($playerInformation[_CLMN_CHR_CLASS_]).'</td>';
			echo '<td>'.$playerInformation[_CLMN_CHR_NAME_].'</td>';
			echo '<td>'.$playerInformation[_CLMN_CHR_LVL_].'</td>';
			echo '<td>'.number_format($playerInformation[_CLMN_CHR_ZEN_]).'</td>';
			echo '<td>'.number_format($playerInformation[_CLMN_CHR_RSTS_]).'</td>';
			echo '<td><a href="'.__BASE_URL__.'usercp/reset/player/'.$playerInformation[_CLMN_CHR_NAME_].'" class="btn btn-primary">'.lang('resetcharacter_txt_5',true).'</a></td>';
		echo '</tr>';
	}
echo '</table>';

// requirements
echo '<div class="module-requirements text-center">';
	echo '<p>'.langf('resetcharacter_txt_6', array($cfg['resets_required_level'])).'</p>';
	if($cfg['resets_enable_zen_requirement']) echo '<p>'.langf('resetcharacter_txt_7', array(number_format($cfg['resets_price_zen']))).'</p>';
echo '</div>';