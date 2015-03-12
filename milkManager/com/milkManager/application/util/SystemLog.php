<?php
/*
 * Created on 27/03/2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class SystemLog{
 	
 	public static function writeLog($log){
 		$file = fopen('/var/www/followMoney/milkManager/system_log.log', 'a');
		fwrite($file, "\n\n" . date_format(date_create(),"j F, Y, g:i:s a") . "--------------------\n\n");
 		fwrite($file, $log);
 		fclose($file);
 	}
 	
 }
 
?>
