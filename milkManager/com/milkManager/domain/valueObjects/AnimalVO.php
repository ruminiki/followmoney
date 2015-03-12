<?php
/*
 * Created on 01/03/2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 
 include_once('EntityVO.php');
 
 class AnimalVO extends EntityVO
 {
	public $_explicitType = "com.milkManager.domain.valueObjects.AnimalVO";
 	public $numero;
	public $nome;
	public $sexo;
	public $origem;
	public $raca;
	public $dataNascimento;
	public $localizacao;
	public $situacao;
	public $nascimento;
		
 }
 
?>
