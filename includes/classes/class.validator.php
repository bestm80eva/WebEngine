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

class Validator {
	
	private static $_usernameAlphaNumCheck = true;
	private static $_usernameMinLen = 4;
	private static $_usernameMaxLen = 10;
	private static $_passwordMinLen = 6;
	private static $_passwordMaxLen = 32;
	private static $_emailMaxLen = 50;
	
	private static function textHit($string, $exclude=""){
		if(empty($exclude)) return false;
		if(is_array($exclude)){
			foreach($exclude as $text){
				if(strstr($string, $text)) return true;
			}
		}else{
			if(strstr($string, $exclude)) return true;
		}
		return false;
	}

	private static function numberBetween($integer, $max=null, $min=0){
		if(is_numeric($min) && $integer < $min) return false;
		if(is_numeric($max) && $integer > $max) return false;
		return true;
	}

	public static function Email($string, $exclude=""){
		if(self::textHit($string, $exclude)) return false;
		return (bool)preg_match("/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i", $string);
	}

	public static function Url($string, $exclude=""){
		if(self::textHit($string, $exclude)) return false;
		return (bool)preg_match("/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i", $string); 
	}

	public static function Ip($string){
		return (bool)preg_match("/^(1?\d{1,2}|2([0-4]\d|5[0-5]))(\.(1?\d{1,2}|2([0-4]\d|5[0-5]))){3}$/", $string);
	}

	public static function Number($integer, $max=null, $min=0){
		if(preg_match("/^\-?\+?[0-9e1-9]+$/",$integer)){
			if(!self::numberBetween($integer, $max, $min)) return false;
			return true;
		}
		return false;
	}

	public static function UnsignedNumber($integer){
		return (bool)preg_match("/^\+?[0-9]+$/",$integer);
	}

	public static function Float($string){
		return (bool)($string==strval(floatval($string)))? true : false;
	}

	public static function Alpha($string){
		return (bool)preg_match("/^[a-zA-Z]+$/", $string);	
	}

	public static function AlphaNumeric($string){
		return (bool)preg_match("/^[0-9a-zA-Z]+$/", $string);	
	}

	public static function Chars($string, $allowed=array("a-z")){
		return (bool)preg_match("/^[" . implode("", $allowed) . "]+$/", $string);	
	}
	
	public static function Length($string, $max=null, $min=0){
		$length = strlen($string);
		if(!self::numberBetween($length, $max, $min)) return false;
		return true;
	}

    public static function Date($string){
        $date = date('Y', strtotime($string));
        return ($date == "1970" || $date == '') ? false : true;
    }
	
	public static function UsernameLength($string){
		if(!self::Length($string, 10, 4)) return;
		return true;
	}
	
	public static function PasswordLength($string){
		if(!self::Length($string, 32, 4)) return;
		return true;
	}
	
	public static function AccountId($string) {
		if(!self::Number($string)) return;
		if(!self::UnsignedNumber($string)) return;
		
		return true;
	}
	
	public static function AccountUsername($string) {
		$usernameCheckAlphaNumeric = check_value(config('username_alphanumeric_check',true)) ? config('username_alphanumeric_check',true) : self::$_usernameAlphaNumCheck;
		$usernameMinLen = check_value(config('username_min_length',true)) ? config('username_min_length',true) : self::$_usernameMinLen;
		$usernameMaxLen = check_value(config('username_max_length',true)) ? config('username_max_length',true) : self::$_usernameMaxLen;
		
		if($usernameCheckAlphaNumeric) if(!self::AlphaNumeric($string)) return;
		if(!self::Length($string, $usernameMaxLen, $usernameMinLen)) return;
		
		return true;
	}
	
	public static function AccountPassword($string) {
		$passwordMinLen = check_value(config('password_min_length',true)) ? config('password_min_length',true) : self::$_passwordMinLen;
		$passwordMaxLen = check_value(config('password_max_length',true)) ? config('password_max_length',true) : self::$_passwordMaxLen;
		
		if(!self::Length($string, $passwordMaxLen, $passwordMinLen)) return;
		
		return true;
	}
	
	public static function AccountEmail($string) {
		$emailMaxLen = check_value(config('email_max_length',true)) ? config('email_max_length',true) : self::$_emailMaxLen;
		
		if(!self::Length($string, $emailMaxLen, 0)) return;
		if(!self::Email($string)) return;
		
		return true;
	}
    
}