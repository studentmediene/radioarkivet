<?php
/********************************
 * @AUTHOR: Christian Wallervand*
 ********************************/
class GeneralUtil {
	const NEEDLE = "\\";
	/*****************************************
	 * Use to remove time from broadcast title*
	 *****************************************/
	static function removeTime($string){
		//Pattern look for time e.g 17:00:00
		$pattern = '/[0-9]{2}:[0-9]{2}:[0-9]{2}/';
		$replacement = '';
		//remove the time from $string
		return preg_replace($pattern, $replacement, $string);
	}
	
	
	/******************************************
	 * Use to get the last part of a path/url *
	 * E.g xxx/yyy/zzz.php will return zzz.php*
	 ******************************************/
	static function pathTail($string) {
		$index = strripos($string, GeneralUtil::NEEDLE) + 1;
		return substr($string, $index);
	}
	
	static function encode($string) {
		return utf8_encode($string);
	}
}