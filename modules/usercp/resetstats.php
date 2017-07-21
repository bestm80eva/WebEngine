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
$cfg = loadConfigurations('usercp.resetstats');
if(!is_array($cfg)) throw new Exception(lang('error_66',true));

// module status
if(!$cfg['active']) throw new Exception(lang('error_47',true));

// player information
$Player = new PlayerResetStats();
$Player->setUsername($_SESSION['username']);
$accountPlayers = $Player->getAccountPlayerList();
if(!is_array($accountPlayers)) throw new Exception(lang('error_46',true));

// form submit
if(check($_GET['player'])) {
	try {
		$PlayerResetStats = new PlayerResetStats();
		$PlayerResetStats->setUsername($_SESSION['username']);
		$PlayerResetStats->setPlayer($_GET['player']);
		$PlayerResetStats->resetstats();
		
		message('success', lang('success_9',true));
	} catch(Exception $ex) {
		message('error', $ex->getMessage());
	}
}

// form
echo '<table class="table general-table-ui">';
	echo '<tr>';
		echo '<td></td>';
		echo '<td>'.lang('resetstats_txt_1',true).'</td>';
		echo '<td>'.lang('resetstats_txt_2',true).'</td>';
		echo '<td>'.lang('resetstats_txt_3',true).'</td>';
		echo '<td>'.lang('resetstats_txt_4',true).'</td>';
		echo '<td>'.lang('resetstats_txt_5',true).'</td>';
		echo '<td>'.lang('resetstats_txt_6',true).'</td>';
		echo '<td>'.lang('resetstats_txt_7',true).'</td>';
		echo '<td></td>';
	echo '</tr>';
	
	foreach($accountPlayers as $playerName) {
		$Player->setPlayer($playerName);
		$playerInformation = $Player->getPlayerInformation();
		
		echo '<tr>';
			echo '<td>'.returnPlayerAvatar($playerInformation[_CLMN_CHR_CLASS_]).'</td>';
			echo '<td>'.$playerInformation[_CLMN_CHR_NAME_].'</td>';
			echo '<td>'.$playerInformation[_CLMN_CHR_LVL_].'</td>';
			echo '<td>'.number_format($playerInformation[_CLMN_CHR_STAT_STR_]).'</td>';
			echo '<td>'.number_format($playerInformation[_CLMN_CHR_STAT_AGI_]).'</td>';
			echo '<td>'.number_format($playerInformation[_CLMN_CHR_STAT_VIT_]).'</td>';
			echo '<td>'.number_format($playerInformation[_CLMN_CHR_STAT_ENE_]).'</td>';
			echo '<td>'.number_format($playerInformation[_CLMN_CHR_STAT_CMD_]).'</td>';
			echo '<td><a href="'.__BASE_URL__.'usercp/resetstats/player/'.$playerInformation[_CLMN_CHR_NAME_].'" class="btn btn-primary">'.lang('resetstats_txt_8',true).'</a></td>';
		echo '</tr>';
	}
echo '</table>';

// requirements
echo '<div class="module-requirements text-center">';
	if($cfg['resetstats_enable_zen_requirement']) echo '<p>'.langf('resetstats_txt_9', array(number_format($cfg['resetstats_price_zen']))).'</p>';
echo '</div>';