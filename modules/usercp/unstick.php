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
$cfg = loadConfigurations('usercp.unstick');
if(!is_array($cfg)) throw new Exception(lang('error_66',true));

// module status
if(!$cfg['active']) throw new Exception(lang('error_47',true));

// player information
$Player = new PlayerUnstick();
$Player->setUsername($_SESSION['username']);
$accountPlayers = $Player->getAccountPlayerList();
if(!is_array($accountPlayers)) throw new Exception(lang('error_46',true));

// form submit
if(check($_GET['player'])) {
	try {
		$playerUnstick = new PlayerUnstick();
		$playerUnstick->setUsername($_SESSION['username']);
		$playerUnstick->setPlayer($_GET['player']);
		$playerUnstick->unstick();
		
		message('success', lang('success_11',true));
	} catch(Exception $ex) {
		message('error', $ex->getMessage());
	}
}

// form
echo '<table class="table general-table-ui">';
	echo '<tr>';
		echo '<td></td>';
		echo '<td>'.lang('unstickcharacter_txt_1',true).'</td>';
		echo '<td>'.lang('unstickcharacter_txt_2',true).'</td>';
		echo '<td>'.lang('unstickcharacter_txt_5',true).'</td>';
		echo '<td></td>';
	echo '</tr>';
	
	foreach($accountPlayers as $playerName) {
		$Player->setPlayer($playerName);
		$playerInformation = $Player->getPlayerInformation();
		
		echo '<tr>';
			echo '<td>'.returnPlayerAvatar($playerInformation[_CLMN_CHR_CLASS_]).'</td>';
			echo '<td>'.$playerInformation[_CLMN_CHR_NAME_].'</td>';
			echo '<td>'.number_format($playerInformation[_CLMN_CHR_ZEN_]).'</td>';
			echo '<td>'.returnMapName($playerInformation[_CLMN_CHR_MAP_]).'</td>';
			echo '<td><a href="'.__BASE_URL__.'usercp/unstick/player/'.$playerInformation[_CLMN_CHR_NAME_].'" class="btn btn-primary">'.lang('unstickcharacter_txt_3',true).'</a></td>';
		echo '</tr>';
	}
echo '</table>';

// requirements
echo '<div class="module-requirements text-center">';
	if($cfg['unstick_enable_zen_requirement']) echo '<p>'.langf('unstickcharacter_txt_4', array(number_format($cfg['unstick_price_zen']))).'</p>';
echo '</div>';