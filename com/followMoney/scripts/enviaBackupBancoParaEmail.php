<?php
/*
 * Created on 27/02/2015
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

set_include_path('/var/www/followMoney');
include_once("com/followMoney/application/util/Email.php");

$title     = $argv[1];
$file_name = $argv[2];

Email::sendAlertMail($title, 'followmoneybr@gmail.com', 'Backup', $file_name);

?>
