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

if(isLoggedIn()) redirect();
echo '<div class="page-title"><span>'.lang('module_titles_txt_20',true).'</span></div>';

try {
	
	if(!check_value($_GET['user'], $_GET['key'])) redirect();
	
	$Registration = new AccountRegister();
	$Registration->setUsername($_GET['user']);
	$Registration->setVerificationKey($_GET['key']);
	$Registration->verifyEmail();
	
	message('success', lang('success_1',true));
	redirect(2,'login', 3);
	
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}