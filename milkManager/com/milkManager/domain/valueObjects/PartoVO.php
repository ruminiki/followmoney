<?php
/*
 * Created on 01/03/2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 
 include_once('EntityVO.php');
 
 class PartoVO extends EntityVO
 {
	public $_explicitType = "com.milkManager.domain.valueObjects.PartoVO";
 	public $observacao;
	public $dataParto;
	public $horaParto;
	public $enseminacao;
	public $nascidos;
	
 }
 
?>
