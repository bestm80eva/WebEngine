<?php
/**
 * WebEngine
 * http://muengine.net/
 * 
 * @version 1.1.0
 * @author Lautaro Angelico <http://lautaroangelico.com/>
 * @copyright (c) 2013-2017 Lautaro Angelico, All Rights Reserved
 * 
 * Licensed under the MIT license
 * http://opensource.org/licenses/MIT
 */

echo '<div class="page-title"><span>'.lang('module_titles_txt_20',true).'</span></div>';

try {
	
	if(!check_value($_GET['user'], $_GET['key'])) redirect();
	
	$AccountPassword = new AccountPassword();
	$AccountPassword->setUsername($_GET['user']);
	$AccountPassword->setVerificationKey($_GET['key']);
	$AccountPassword->verifyPassword();

	message('success', lang('success_2',true));
	redirect(2,'usercp', 3);
	
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}