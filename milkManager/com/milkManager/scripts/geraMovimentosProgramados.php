<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

set_include_path('/var/www:/var/www/ZendFramework/library'); 
//set_include_path('/var/www/ZendFramework/library'); 

include_once("com/followMoney/dao/Database.php");
include_once("com/followMoney/domain/valueObjects/MovimentoVO.php");
include_once("com/followMoney/domain/valueObjects/FinalidadeVO.php");
include_once("com/followMoney/domain/valueObjects/ContaBancariaVO.php");
include_once("com/followMoney/domain/valueObjects/CartaoCreditoVO.php");
include_once("com/followMoney/domain/valueObjects/FornecedorVO.php");
include_once("com/followMoney/domain/valueObjects/FormaPagamentoVO.php");
include_once("com/followMoney/domain/valueObjects/FaturaVO.php");
include_once("com/followMoney/dao/MovimentoDao.php");
require_once("ZendFramework/library/Zend/Date.php");

define("SQL_PROGRAMADOS","select mp.movimento, substr(mp.ultimaGeracao, 7,2) as dia, substr(mp.ultimaGeracao, 5,2) as mes, mp.periodicidade, " .
	"m.emissao, m.vencimento, m.valor, m.status, m.operacao, m.finalidade, m.contaBancaria, m.usuario, m.descricao, m.fornecedor, m.cartaoCredito, " .
	"m.formaPagamento, m.fatura from movimentosProgramados mp inner join movimento m on (m.id = mp.movimento) " .
	"where concat(substr(mp.ultimaGeracao, 1,4), substr(mp.ultimaGeracao, 5,2)) < DATE_FORMAT(CURRENT_DATE(),'%Y%m')"); 

SystemLog::writeLog("geraMovimentosProgramados.findMovimentos(): " . SQL_PROGRAMADOS);

$result = Database::executeQuery(SQL_PROGRAMADOS);

while($row = mysql_fetch_array($result)){

	$movimento = new MovimentoVO();

	$movimento->descricao          = $row['descricao'];
	$movimento->idUsuario          = $row['usuario'];
	$movimento->emissao            = new Zend_Date();
	$datearray                     = array('year' => date('Y'), 'month' => date('m'), 'day' => $row['dia']);
	$movimento->vencimento         = new Zend_Date($datearray);

	$finalidade                    = new FinalidadeVO();
	$finalidade->idEntity          = $row['finalidade'];
	$movimento->finalidade         = $finalidade;

	$contaBancaria                 = new ContaBancariaVO();
	$contaBancaria->idEntity       = $row['contaBancaria'];
	$movimento->contaBancaria      = $contaBancaria;

	$fornecedor                    = new FornecedorVO();
	$fornecedor->idEntity          = $row['fornecedor'];
	$movimento->fornecedor         = $fornecedor;

	$movimento->valor              = $row['valor'];
	$movimento->status             = $row['status'];
	$movimento->operacao           = $row['operacao'];

	$formaPagamento                = new FormaPagamentoVO();
	$formaPagamento->idEntity      = $row['formaPagamento'];
	$movimento->formaPagamento     = $formaPagamento;

	$cartaoCredito                 = new CartaoCreditoVO();
	$cartaoCredito->idEntity       = $row['cartaoCredito'];
	$movimento->cartaoCredito      = $cartaoCredito;

	$movimento->movimentoOrigem    = $row['movimento'];

	$id = MovimentoDao::save($movimento);
	SystemLog::writeLog("geraMovimentosProgramados.insertMovimentoProgramado(): MOVIMENTO GERADO: " . $id->idEntity);
	$sql = "update movimentosProgramados set ultimaGeracao = '" . $movimento->vencimento->toString('YYYYMMdd') . "' where movimento = " . $row['movimento'];
	SystemLog::writeLog("geraMovimentosProgramados.atualizaMovimentoProgramado(): " . $sql);
	Database::update($sql);
}

?>

