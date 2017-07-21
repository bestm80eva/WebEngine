<?php
/**
 * WebEngine
 * http://muengine.net/
 * 
 * @version 2.0.0
 * @author Lautaro Angelico <http://lautaroangelico.com/>
 * @copyright (c) 2013-2017 Lautaro Angelico, All Rights Reserved
 * 
 * Licensed under the MIT license
 * http://opensource.org/licenses/MIT
 */

// module configs
$cfg = loadConfigurations('downloads');
if(!is_array($cfg)) throw new Exception(lang('error_66',true));

// module status
if(!$cfg['active']) throw new Exception(lang('error_47',true));

$downloadsCACHE = LoadCacheData('downloads.cache');
if(is_array($downloadsCACHE)) {
	foreach($downloadsCACHE as $key => $tempDownloadsData) {
		if($key > 0) {
			switch($tempDownloadsData[5]) {
				case 1:
					$downloadCLIENTS[] = $tempDownloadsData;
				break;
				case 2:
					$downloadPATCHES[] = $tempDownloadsData;
				break;
				case 3:
					$downloadTOOLS[] = $tempDownloadsData;
				break;
			}
		}
	}
}

if($cfg['show_client_downloads']) {
	if(is_array($downloadCLIENTS)) {
		echo '<div class="panel panel-addstats">';
			echo '<div class="panel-body">';
				echo '<div class="panel-title">'.lang('downloads_txt_6',true).'</div>';
				echo '<table class="table">';
				foreach($downloadCLIENTS as $download) {
					echo '<tr>';
						echo '<td style="width: 40%">'.$download[1].'</td>';
						echo '<td style="width: 20%" class="text-center">'.$download[4].'</td>';
						echo '<td style="width: 20%"class="text-center">'.round($download[3], 2).' '.lang('downloads_txt_4',true).'</td>';
						echo '<td style="width: 20%"class="text-center"><a href="'.$download[2].'" class="btn btn-primary btn-xs" target="_blank">'.lang('downloads_txt_5',true).'</a></td>';
					echo '</tr>';
				}
				echo '</table>';
			echo '</div>';
		echo '</div>';
	}
}

if($cfg['show_patch_downloads']) {
	if(is_array($downloadPATCHES)) {
		echo '<div class="panel panel-addstats">';
			echo '<div class="panel-body">';
				echo '<div class="panel-title">'.lang('downloads_txt_7',true).'</div>';
				echo '<table class="table">';
				foreach($downloadPATCHES as $download) {
					echo '<tr>';
						echo '<td style="width: 40%">'.$download[1].'</td>';
						echo '<td style="width: 20%" class="text-center">'.$download[4].'</td>';
						echo '<td style="width: 20%"class="text-center">'.round($download[3], 2).' '.lang('downloads_txt_4',true).'</td>';
						echo '<td style="width: 20%"class="text-center"><a href="'.$download[2].'" class="btn btn-primary btn-xs" target="_blank">'.lang('downloads_txt_5',true).'</a></td>';
					echo '</tr>';
				}
				echo '</table>';
			echo '</div>';
		echo '</div>';
	}
}

if($cfg['show_tool_downloads']) {
	if(is_array($downloadTOOLS)) {
		echo '<div class="panel panel-addstats">';
			echo '<div class="panel-body">';
				echo '<div class="panel-title">'.lang('downloads_txt_8',true).'</div>';
				echo '<table class="table">';
				foreach($downloadTOOLS as $download) {
					echo '<tr>';
						echo '<td style="width: 40%">'.$download[1].'</td>';
						echo '<td style="width: 20%" class="text-center">'.$download[4].'</td>';
						echo '<td style="width: 20%"class="text-center">'.round($download[3], 2).' '.lang('downloads_txt_4',true).'</td>';
						echo '<td style="width: 20%"class="text-center"><a href="'.$download[2].'" class="btn btn-primary btn-xs" target="_blank">'.lang('downloads_txt_5',true).'</a></td>';
					echo '</tr>';
				}
				echo '</table>';
			echo '</div>';
		echo '</div>';
	}
}