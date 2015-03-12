<?php
/*
 * Created on 01/03/2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 
 include_once('EntityVO.php');
 
 class AcompanhamentoVO extends EntityVO
 {
	public $_explicitType = "com.milkManager.domain.valueObjects.AcompanhamentoVO";
 	public $fotografia;
	public $observacao;
	public $animal;
	public $procedimento;
	public $dataProcedimento;
	public $hash;
 }
 
?>
