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
$cfg = loadConfigurations('usercp.clearpk');
if(!is_array($cfg)) throw new Exception(lang('error_66',true));

// module status
if(!$cfg['active']) throw new Exception(lang('error_47',true));

// player information
$Player = new PlayerClearPk();
$Player->setUsername($_SESSION['username']);
$accountPlayers = $Player->getAccountPlayerList();
if(!is_array($accountPlayers)) throw new Exception(lang('error_46',true));

// form submit
if(check($_GET['player'])) {
	try {
		$PlayerClearPk = new PlayerClearPk();
		$PlayerClearPk->setUsername($_SESSION['username']);
		$PlayerClearPk->setPlayer($_GET['player']);
		$PlayerClearPk->clearpk();
		
		message('success', lang('success_10',true));
	} catch(Exception $ex) {
		message('error', $ex->getMessage());
	}
}

// form
echo '<table class="table general-table-ui">';
	echo '<tr>';
		echo '<td></td>';
		echo '<td>'.lang('clearpk_txt_1',true).'</td>';
		echo '<td>'.lang('clearpk_txt_2',true).'</td>';
		echo '<td>'.lang('clearpk_txt_6',true).'</td>';
		echo '<td>'.lang('clearpk_txt_3',true).'</td>';
		echo '<td></td>';
	echo '</tr>';
	
	foreach($accountPlayers as $playerName) {
		$Player->setPlayer($playerName);
		$playerInformation = $Player->getPlayerInformation();
		
		echo '<tr>';
			echo '<td>'.returnPlayerAvatar($playerInformation[_CLMN_CHR_CLASS_]).'</td>';
			echo '<td>'.$playerInformation[_CLMN_CHR_NAME_].'</td>';
			echo '<td>'.number_format($playerInformation[_CLMN_CHR_ZEN_]).'</td>';
			echo '<td>'.returnPkLevel($playerInformation[_CLMN_CHR_PK_LEVEL_]).'</td>';
			echo '<td>'.number_format($playerInformation[_CLMN_CHR_PK_KILLS_]).'</td>';
			echo '<td><a href="'.__BASE_URL__.'usercp/clearpk/player/'.$playerInformation[_CLMN_CHR_NAME_].'" class="btn btn-primary">'.lang('clearpk_txt_4',true).'</a></td>';
		echo '</tr>';
	}
echo '</table>';

// requirements
echo '<div class="module-requirements text-center">';
	if($cfg['clearpk_enable_zen_requirement']) echo '<p>'.langf('clearpk_txt_5', array(number_format($cfg['clearpk_price_zen']))).'</p>';
echo '</div>';