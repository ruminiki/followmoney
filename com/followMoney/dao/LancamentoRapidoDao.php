<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("com/followMoney/dao/Database.php");
include_once("com/followMoney/dao/MovimentoDao.php");
include_once("com/followMoney/application/util/SystemLog.php");
include_once("com/followMoney/application/util/DateUtil.php");

class LancamentoRapidoDao {

	public static function lancar($movimento){
		return MovimentoDao::save($movimento);
	}

}

?>
