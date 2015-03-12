<?php
/*
 * Created on 01/03/2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 include_once('EntityVO.php');
 
 class FaturaVO extends EntityVO
 {
 	public $_explicitType="com.followMoney.domain.valueObjects.FaturaVO";
	public $emissao;
	public $vencimento;
	public $valor;
	public $valorPagamento;
	public $mesReferencia;
	public $contaBancaria;
	public $formaPagamento;
	public $cartaoCredito;
	public $status;
	public $saldo;
	
 }
 
?>
