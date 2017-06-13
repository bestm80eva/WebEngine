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

if(isLoggedIn()) redirect();

echo '<div class="page-title"><span>'.lang('module_titles_txt_15',true).'</span></div>';

try {
	$cfg = loadConfigurations('forgotpassword');
	
	if(!$cfg['active']) throw new Exception(lang('error_47',true));	
	if(check_value($_POST['webenginePasswordRecovery_submit'])) {
		try {
			
			$Recovery = new AccountPassword();
			$Recovery->setEmail($_POST['webengineEmail_current']);
			$Recovery->recoverPassword();
			
			message('success', lang('success_6',true));
		} catch (Exception $ex) {
			message('error', $ex->getMessage());
		}
	}
	
	echo '<div class="col-xs-8 col-xs-offset-2" style="margin-top:30px;">';
		echo '<form class="form-horizontal" action="" method="post">';
			echo '<div class="form-group">';
				echo '<label for="webengineEmail" class="col-sm-4 control-label">'.lang('forgotpass_txt_1',true).'</label>';
				echo '<div class="col-sm-8">';
					echo '<input type="text" class="form-control" id="webengineEmail" name="webengineEmail_current">';
				echo '</div>';
			echo '</div>';
			echo '<div class="form-group">';
				echo '<div class="col-sm-offset-4 col-sm-8">';
					echo '<button type="submit" name="webenginePasswordRecovery_submit" value="submit" class="btn btn-primary">'.lang('forgotpass_txt_2',true).'</button>';
				echo '</div>';
			echo '</div>';
		echo '</form>';
	echo '</div>';
	
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}