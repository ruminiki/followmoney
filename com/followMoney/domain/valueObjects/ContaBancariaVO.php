<?php
/*
 * Created on 01/03/2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 
 include_once('EntityVO.php');
 
 class ContaBancariaVO extends EntityVO
 {
 	public $_explicitType="com.followMoney.domain.valueObjects.ContaBancariaVO";
 	public $descricao;
 	public $numero;
 	public $digito;
	public $situacao; 
	public $dataInativacao;
	
 }
 
?>
