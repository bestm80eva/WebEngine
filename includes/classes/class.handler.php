<?php
class Handler {
	
	private static $muonline;
	private static $memuonline;
	private static $defaultModule = 'home';
	
	private static $regexPattern = '/[^a-zA-Z0-9\/]/';
	
	public static function loadTemplate() {
		global $lang;
		try {
			if(defined('access') or access) {
				if(access == "index") {
					include(__PATH_LANGUAGES__ . config('language_default', true) . '/language.php');
					self::loadTemplateIndex();
				} elseif(access == "cron") {
					// do not load anything (for crons)
				} else {
					throw new Exception("No Access");
				}
			}
		} catch(Exception $ex) {
			die('[ERROR] '.$ex->getMessage());
		}
	}
	
	public static function loadModule($request='') {
		$dB = self::loadDB();
		
		$request = explode("/", $request);
		$request = array_filter($request);
		
		$_GET['module'] = (@check($request[0]) ? $request[0] : NULL);
		$_GET['submodule'] = '';
		
		if(count($request) > 1) {
			// Sub-Modules
			foreach($request as $reqKey => $thisReq) {
				if($reqKey != 0) {
					$parentModule = $request[$reqKey-1];
					$subModuleData = $dB->query_fetch_single("SELECT * FROM WEBENGINE_MODULES WHERE module_file = ? AND module_parent = ? AND module_status = 1", array($thisReq, $parentModule));
					if($subModuleData) {
						if($subModuleData['access'] == 2) {
							# check if logged in
							if(!isLoggedIn()) redirect('login/');
						}
						if(check($_GET['submodule'])) $_GET['submodule'] .= "/";
						$_GET['submodule'] .= $thisReq;
					} else {
						$parentModuleData = $dB->query_fetch_single("SELECT * FROM WEBENGINE_MODULES WHERE module_file = ? AND module_parent IS NULL AND module_status = 1", array($parentModule));
						if($parentModuleData['access'] == 2) {
							# check if logged in
							if(!isLoggedIn()) redirect('login/');
						}
						//$_GET['module'] = "404";
						//$_GET['submodule'] = "";
					}
				}
			}
			
			for($i = count(explode("/", $_GET['submodule']))+1; $i < count($request); $i++) {
				if(@check($request[$i])) {
					if(@check($request[$i+1])) {
						//$_GET[$request[$i]] = filter_var($request[$i+1], FILTER_SANITIZE_STRING);
						//$_GET[$request[$i]] = $request[$i+1];
						$_GET[$request[$i]] = self::cleanModuleRequest($request[$i+1]);
					} else {
						$_GET[$request[$i]] = NULL;
					}
				}
				$i++;
			}
			
		} else {
			// Top Module
			$topModuleData = $dB->query_fetch_single("SELECT * FROM WEBENGINE_MODULES WHERE module_file = ? AND module_parent IS NULL AND module_status = 1", array($_GET['module']));
			if(check($_GET['module']) && !$topModuleData) {
				$_GET['module'] = "404";
			} else {
				if($topModuleData['access'] == 2) {
					# check if logged in
					if(!isLoggedIn()) redirect('login/');
				}
			}
		}
		
		
		$module = (check(self::cleanModuleRequest($_GET['module'])) ? self::cleanModuleRequest($_GET['module']) : 'home');
		$submodule = self::cleanModuleRequest($_GET['submodule']);
		
		# SESSION
		sessionControl::lastUserLocation($module.'/'.$submodule);
		if(!isLoggedIn()) {
			sessionControl::initSessionControl($db);
		} else {
			sessionControl::initSessionControl($db, "user");
		}
		
		// Modules Path
		$modulesPath = __PATH_MODULES__;
		
		if(check($submodule)) {
			$path = $modulesPath.$module.'/'.$submodule.'.php';
			if(file_exists($path)) {
				self::loadPage($path);
			} else {
				self::loadPage($modulesPath.'404.php');
			}
		} else {
			if(file_exists($modulesPath.$module.'.php')) {
				self::loadPage($modulesPath.$module.'.php');
			} else {
				self::loadPage($modulesPath.'404.php');
			}
		}
	}
	
	private static function loadPage($path) {
		include($path);
	}
	
	private static function moduleExists($request) {
		if(file_exists(__PATH_MODULES__ . $request . '.php')) return true;
		return;
	}
	
	private static function loadTemplateIndex() {
		if(file_exists(__PATH_TEMPLATE_ROOT__.'/index.php')) {
			include(__PATH_TEMPLATE_ROOT__.'/index.php');
		} else {
			throw new Exception('Could not load template.');
		}
	}
	
	private static function cleanModuleRequest($input) {
		return preg_replace(self::$regexPattern, '', $input);
	}
	
	public static function loadDB($database="") {
		switch($database) {
			case 'Me_MuOnline':
				
				break;
			default:
				$dbc = new dB(config('SQL_DB_HOST',true), config('SQL_DB_PORT', true), config('SQL_DB_NAME', true), config('SQL_DB_USER', true), config('SQL_DB_PASS', true), config('SQL_PDO_DRIVER', true));
				return $dbc;
		}
	}
	
	public static function userIP() {
		$ip = filter_input(INPUT_SERVER, "REMOTE_ADDR", FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);
		//$ip = $_SERVER['REMOTE_ADDR'];
		if(!$ip) return "0.0.0.0";
		return $ip;
	}
}