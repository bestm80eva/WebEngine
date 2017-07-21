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

define('access', 'api');
include('../includes/webengine.php');

$cs = cs_CalculateTimeLeft();
$timeLeft = (check_value($cs) ? $cs : 0);

echo json_encode(
	array(
		'TimeLeft' => $timeLeft
	)
);
