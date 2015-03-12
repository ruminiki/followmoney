<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("com/followMoney/dao/IGenericDao.php");
include_once("com/followMoney/dao/Database.php");
include_once("com/followMoney/dao/ContaBancariaDao.php");
include_once("com/followMoney/dao/CartaoCreditoDao.php");
include_once("com/followMoney/dao/FaturaDao.php");
include_once("com/followMoney/dao/FornecedorDao.php");
include_once("com/followMoney/domain/valueObjects/FinalidadeVO.php");
include_once("com/followMoney/domain/valueObjects/ContaBancariaVO.php");
include_once("com/followMoney/domain/valueObjects/FornecedorVO.php");
include_once("com/followMoney/domain/valueObjects/MovimentoVO.php");
include_once("com/followMoney/domain/valueObjects/MovimentoReportVO.php");
include_once("com/followMoney/domain/valueObjects/FormaPagamentoVO.php");
include_once("com/followMoney/domain/valueObjects/FaturaVO.php");
include_once("com/followMoney/domain/valueObjects/CartaoCreditoVO.php");
include_once("com/followMoney/domain/valueObjects/MovimentoProgramadoVO.php");
include_once("com/followMoney/application/util/SystemLog.php");


define("SQL_MOVIMENTO","select m.id, m.descricao, m.usuario, m.emissao, m.vencimento, m.valor, m.status, m.operacao, m.movimentoOrigem," .
		"m.parcela, m.hashParcelas, m.hashTransferencia, fl.id as idFinalidade, fl.descricao as descricaoFinalidade," .
		"c.id as idContaBancaria, c.descricao as descricaoContaBancaria, c.numero as numeroContaBancaria," .
		"c.digito as digitoContaBancaria, f.id as idFornecedor, f.descricao as descricaoFornecedor, " .
		"cr.id as idCartaoCredito, cr.descricao as descricaoCartaoCredito, cr.limite as limite, cr.dataFatura as dataFatura, cr.dataFechamento as dataFechamento, " .
		"ft.id as idFatura, ft.mesReferencia as mesReferencia, ft.valor as valorFatura, ft.valorPagamento as valorPagamentoFatura, " .
		"fp.id as idFormaPagamento, fp.descricao as descricaoFormaPagamento, fp.sigla as siglaFormaPagamento " .
		"from movimento m " .
		"left join contaBancaria c on (c.id = m.contaBancaria and c.usuario = m.usuario) " .
		"left join fornecedor f on (f.id = m.fornecedor and f.usuario = m.usuario) " .
		"left join formaPagamento fp on (fp.id = m.formaPagamento and fp.usuario = m.usuario) " .
		"left join cartaoCredito cr on (cr.id = m.cartaoCredito and cr.usuario = m.usuario) " .
		"left join fatura ft on (ft.id = m.fatura and ft.usuario = m.usuario) " .
		"inner join finalidade fl on (fl.id = m.finalidade and fl.usuario = m.usuario) "); 
		
define("SQL_FNDE","select m.id, m.descricao, m.usuario, m.emissao, m.vencimento, m.valor, m.status, m.operacao, m.movimentoOrigem," .
		"m.parcela, m.hashParcelas, m.hashTransferencia, fl.id as idFinalidade, fl.descricao as descricaoFinalidade," .
		"c.id as idContaBancaria, c.descricao as descricaoContaBancaria, c.numero as numeroContaBancaria," .
		"c.digito as digitoContaBancaria, f.id as idFornecedor, f.descricao as descricaoFornecedor, " .
		"cr.id as idCartaoCredito, cr.descricao as descricaoCartaoCredito, cr.limite as limite, cr.dataFatura as dataFatura, cr.dataFechamento as dataFechamento," .
		"ft.id as idFatura, ft.mesReferencia as mesReferencia, ft.valor as valorFatura, ft.valorPagamento as valorPagamentoFatura, " .
		"fp.id as idFormaPagamento, fp.descricao as descricaoFormaPagamento, fp.sigla as siglaFormaPagamento " .
		"from movimento m " .
		"left join contaBancaria c on (c.id = m.contaBancaria and c.usuario = m.usuario) " .
		"left join formaPagamento fp on (fp.id = m.formaPagamento and fp.usuario = m.usuario) " .
		"left join cartaoCredito cr on (cr.id = m.cartaoCredito and cr.usuario = m.usuario) " .
		"left join fatura ft on (ft.id = m.fatura and ft.usuario = m.usuario) " .
		"left join fornecedor f on (f.id = m.fornecedor and f.usuario = m.usuario) "); 
		
class MovimentoDao implements IGenericDao{
		
	public static function save($movimento){
	
 		$finalidade     = ($movimento->finalidade != null && $movimento->finalidade->idEntity > 0) ? ($movimento->finalidade->idEntity) : 'NULL';
 		$fornecedor     = ($movimento->fornecedor != null && $movimento->fornecedor->idEntity > 0) ? ($movimento->fornecedor->idEntity) : 'NULL';
 		$contaBancaria  = ($movimento->contaBancaria != null && $movimento->contaBancaria->idEntity > 0) ? ($movimento->contaBancaria->idEntity) : 'NULL';
		$formaPagamento = ($movimento->formaPagamento != null && $movimento->formaPagamento->idEntity > 0) ? ($movimento->formaPagamento->idEntity) : 'NULL';
		$cartaoCredito  = ($movimento->cartaoCredito != null && $movimento->cartaoCredito->idEntity > 0) ? ($movimento->cartaoCredito->idEntity) : 'NULL';
 		$fatura         = ($movimento->fatura != null && $movimento->fatura->idEntity > 0) ? ($movimento->fatura->idEntity) : 'NULL';
		
		$sql = "insert into movimento (descricao, " .
				"usuario, emissao, vencimento, " .
				"valor, status, operacao, hashParcelas, hashTransferencia, movimentoOrigem, parcela, fatura, " .
				"finalidade, contaBancaria, fornecedor, formaPagamento, cartaoCredito) " .
				"values ('$movimento->descricao', " .
						"'$movimento->idUsuario', " .
						"'" . $movimento->emissao->toString('yyyyMMdd') . "'," .
						"'" . $movimento->vencimento->toString('yyyyMMdd'). "'," .
						"'$movimento->valor'," .
						"'$movimento->status'," .
						"'$movimento->operacao'," .
						"'$movimento->hashParcelas'," .
						"'$movimento->hashTransferencia'," .
						"'$movimento->movimentoOrigem'," .	
						"'$movimento->parcela'," .
						"$fatura," .
						$finalidade . "," . $contaBancaria . "," . $fornecedor . "," . $formaPagamento . "," . $cartaoCredito . ")";
		
		SystemLog::writeLog("MovimentoDao.save() " . $sql);
		
		try{
			$id = Database::insert($sql);
			if ( $id > 0 ){
				$movimento->idEntity = $id;
				//se o movimento for pago com cartão de crédito tem que adicionar na fatura atual
				if ( $cartaoCredito != 'NULL' ){
					FaturaDao::adicionarMovimentoFatura($movimento);
				}
				return $movimento;
			}	
		}catch(Exception $e){
			//throw new Exception('N�o foi poss�vel salvar o movimento selecionado.');
			throw $e;
		}
		
	}
	
	public static function update($movimento){
 		$finalidade     = ($movimento->finalidade != null && $movimento->finalidade->idEntity > 0) ? ($movimento->finalidade->idEntity) : 'NULL';
 		$fornecedor     = ($movimento->fornecedor != null && $movimento->fornecedor->idEntity > 0) ? ($movimento->fornecedor->idEntity) : 'NULL';
 		$contaBancaria  = ($movimento->contaBancaria != null && $movimento->contaBancaria->idEntity > 0) ? ($movimento->contaBancaria->idEntity) : 'NULL';
		$formaPagamento = ($movimento->formaPagamento != null && $movimento->formaPagamento->idEntity > 0) ? ($movimento->formaPagamento->idEntity) : 'NULL';
		$cartaoCredito  = ($movimento->cartaoCredito != null && $movimento->cartaoCredito->idEntity > 0) ? ($movimento->cartaoCredito->idEntity) : 'NULL';
		$fatura         = ($movimento->fatura != null && $movimento->fatura->idEntity > 0) ? ($movimento->fatura->idEntity) : 'NULL';
		
		$sql = "update movimento set descricao='$movimento->descricao', " .
						"usuario='$movimento->idUsuario', " .
						"emissao='" . $movimento->emissao->toString('yyyyMMdd'). "'," .
						"vencimento='" . $movimento->vencimento->toString('yyyyMMdd'). "'," .
						"valor=$movimento->valor," .
						"status='$movimento->status'," .
						"operacao='$movimento->operacao'," .
						"hashParcelas='$movimento->hashParcelas'," .
						"hashTransferencia='$movimento->hashTransferencia'," .
						"finalidade=$finalidade," .
						"contaBancaria=$contaBancaria," .
						"formaPagamento=$formaPagamento," .
						"cartaoCredito=$cartaoCredito," .
						"fornecedor=$fornecedor," .
						"movimentoOrigem=$movimento->movimentoOrigem," .
						"parcela='$movimento->parcela'," .
						"fatura=$fatura " .
				"where id = $movimento->idEntity and usuario ='$movimento->idUsuario'";
		
		try{

			if ( MovimentoDao::isFatura($movimento) ){
				throw new Exception("O movimento não pode ser alterado pois se trata do pagamento de fatura de cartão de crédito. Caso deseje, cancele o pagamento da fatura para que o movimento seja removido.");
			}

			if ( MovimentoDao::isMovimentoOfClosedFatura($movimento) ){
				throw new Exception('O movimento selecionado está relacionado a uma fatura FECHADA. É necessário primeiro reabrir a fatura para alterar o movimento.');
			}

			Database::update($sql);
			return $movimento;	
		}catch(Exception $e){
			throw $e;
		}
	}
	
	public static function remove($movimento){

		$sql    = "delete from movimento where id = $movimento->idEntity and usuario = '$movimento->idUsuario'";
		
		try{
			if ( MovimentoDao::isFatura($movimento) ){
				throw new Exception("O movimento não pode ser excluído pois se trata do pagamento de fatura de cartão de crédito. Você deve cancelar pagamento da fatura para que o movimento seja removido.");	
			}

			if ( MovimentoDao::isMovimentoOfClosedFatura($movimento) ){
				throw new Exception('O movimento selecionado está relacionado a uma fatura FECHADA. É necessário primeiro reabrir a fatura para excluir o movimento.');
			}
			//NO CASO DA FATURA ESTAR ABERTA OU NÃO EXISTIR DELETE CASCADE MOVIMENTOS_FATURA
			Database::remove($sql);	
		}catch(Exception $e){
			throw $e;
		}
    }	

    //===============FATURAS=========================
    private static function isFatura($movimento){
    	$mesReferencia = ($movimento->fatura != null && $movimento->fatura->mesReferencia != '') ? ($movimento->fatura->mesReferencia) : 'NULL';

		//O MOVIMENTO É UMA FATURA	
		if ( isset($mesReferencia) && $mesReferencia != 'NULL' ){
			return true;
		}
		return false;
    }


    private static function isMovimentoOfClosedFatura($movimento){
		$sql = "select f.status from movimentosFatura mf inner join fatura f on (f.id = mf. fatura) where mf.movimento = $movimento->idEntity";
		$result = Database::executeQuery($sql);	

		//O MOVIMENTO ESTÁ ASSOCIADO A UMA FATURA FECHADA
		if($row = mysqli_fetch_array($result)){
			if ( $row['status'] == 'FECHADA' ){
				return true;
			}
		}
		return false;
    }

	//===============PARCELAS========================
	
	public static function findParcelas($hash, $usuario){
		
		$sql = SQL_MOVIMENTO . " where m.usuario = '$usuario' and m.hashParcelas = '$hash' order by m.vencimento, m.descricao ";
		$result = Database::executeQuery($sql);
		return MovimentoDao::resultToArray($result);
	}
	
	public static function removeParcelas($movimento, $usuario){
		$sql = "delete from movimento where hashParcelas = '$movimento->hashParcelas' and usuario = '$movimento->idUsuario'";
		try{
			Database::remove($sql);
			return $movimento;	
		}catch(Exception $e){
			throw new Exception('Não foi possivel remover o movimento selecionado.');
		}
	}
	
	public static function removeProximasParcelas($movimento, $usuario){
		$sql = "delete from movimento where hashParcelas = '$movimento->hashParcelas' and movimento.vencimento >= '" . $movimento->vencimento->toString('yyyyMMdd'). "' and usuario = '$movimento->idUsuario'";
		try{
			Database::remove($sql);
			return $movimento;	
		}catch(Exception $e){
			throw new Exception('Não foi possivel remover o movimento selecionado.');
		}
	}
	
	//==================TRANSFERENCIA================
	
	public static function removeTransferencia($movimento, $usuario){
		$sql = "delete from movimento where hashTransferencia = '$movimento->hashTransferencia' and usuario = '$movimento->idUsuario'";
		try{
			Database::remove($sql);
			return $movimento;	
		}catch(Exception $e){
			throw new Exception('N�o � possivel remover o movimento selecionado.');
		}
	}
	
	//===============================================
	public static function findAll($usuario){
		
		$sql = SQL_MOVIMENTO . " where m.usuario = '$usuario' and m.hashTransferencia = '' and m.fatura is NULL order by m.vencimento, m.descricao ";
		$result = Database::executeQuery($sql);
		return MovimentoDao::resultToArray($result);
	}
	
	public static function listByRange($dtInicial,$dtFinal,$usuario){
		
		$sql = SQL_MOVIMENTO . " where m.usuario = '$usuario' " .
					   "and m.vencimento between '" . $dtInicial->toString('yyyyMMdd') . "' and '" . $dtFinal->toString('yyyyMMdd'). "' " . 
					   "and m.hashTransferencia = '' and m.fatura is NULL order by m.vencimento, m.emissao";
		$result = Database::executeQuery($sql);
		return MovimentoDao::resultToArray($result);
	}
	
	public static function findByDescricao($param,$dtInicial,$dtFinal,$usuario){
		
		$sql = "";		
		if ( $param === "" || strpos($param, 'where') === 0 ){
			$sql = SQL_MOVIMENTO . $param .
				" and m.usuario = $usuario and m.hashTransferencia = '' and m.fatura is NULL order by m.vencimento, m.emissao ";
				
		}else{
			$sql = SQL_MOVIMENTO . " where m.usuario = '$usuario' and m.descricao like '%$param%' " .
				"and m.vencimento between '" . $dtInicial->toString('yyyyMMdd') . "' and '" . $dtFinal->toString('yyyyMMdd'). "' " . 
				" and m.hashTransferencia = '' and m.fatura is NULL order by m.vencimento, m.emissao ";
		}
		
		SystemLog::writeLog("MovimentoDao.findByDescricao()): " . $sql);
		
		$result = Database::executeQuery($sql);
		return MovimentoDao::resultToArray($result);
	}
	
	public static function findByDescricaoFinalidade($descricao,$dtInicial,$dtFinal,$usuario){
		
		$sql = SQL_MOVIMENTO . " where m.usuario = '$usuario' and fl.descricao like '%$descricao%' " .
				"and m.vencimento between '" . $dtInicial->toString('yyyyMMdd') . "' and '" . $dtFinal->toString('yyyyMMdd'). "' " . 
				" and m.hashTransferencia = '' and m.fatura is NULL order by m.vencimento, m.emissao ";
		$result = Database::executeQuery($sql);
		return MovimentoDao::resultToArray($result);
	}
	
	public static function findByDescricaoContaBancaria($descricao,$dtInicial,$dtFinal,$usuario){
		
		$sql = SQL_MOVIMENTO . " where m.usuario = '$usuario' and c.descricao like '%$descricao%' " .
				"and m.vencimento between '" . $dtInicial->toString('yyyyMMdd') . "' and '" . $dtFinal->toString('yyyyMMdd'). "' " . 
				" order by m.vencimento, m.emissao ";
		$result = Database::executeQuery($sql);
		return MovimentoDao::resultToArray($result);
	}
	
	public static function findById($id, $usuario){
		
		$sql = SQL_MOVIMENTO . " where m.usuario = '$usuario' and m.id = '$id' " .
				" and m.hashTransferencia = '' and m.fatura is NULL order by m.descricao ";
		$result = Database::executeQuery($sql);
		if($row = mysqli_fetch_array($result)){
			return MovimentoDao::rowToObject($row);
		}
		return null;
	}
	
	//================FINALIDADE=========================
	public static function findByFinalidade($finalidade, $usuario){
		$sql = SQL_MOVIMENTO . " where m.usuario = '$usuario' and m.finalidade ='$finalidade->idEntity' " .
				" and m.hashTransferencia = '' and m.fatura is NULL ";
		$result = Database::executeQuery($sql);
		$list = array();
		while($row = mysqli_fetch_array($result)){
			array_push($list, MovimentoDao::rowToObject($row));			 
		}
		return MovimentoDao::findSubItensByFinalidade($finalidade->idEntity, $usuario, $list);
	}
	
	private static function findSubItensByFinalidade($finalidade, $usuario, $array){
		$sqlSubItem = " select f.id as finalidade from finalidade f where f.finalidadeSuperior ='$finalidade' ";
		$result = Database::executeQuery($sqlSubItem);
		$subItens = array();
		//busca os movimentos de todas finalidades do primeiro sub nivel
		while($row = mysqli_fetch_array($result)){
			$array = MovimentoDao::findByItem($row['finalidade'], $usuario, $array);	 
			array_push($subItens, $row['finalidade']);
		}
		//para cada sub item busca os seus subitens tamb�m
		for ( $i = 0; $i < count($subItens); $i++ ) {
			return MovimentoDao::findSubItensByFinalidade($subItens[$i], $usuario, $array);
		}
		return $array;
	}
	
	public static function findByItem($finalidade, $usuario, $array){
		$sql = SQL_FNDE . " inner join finalidade fl on ( fl.id = m.finalidade and fl.id = $finalidade ) where m.usuario = '$usuario' and m.hashTransferencia = '' ";
		$result = Database::executeQuery($sql);
		while($row = mysqli_fetch_array($result)){
			array_push($array, MovimentoDao::rowToObject($row));			 
		}
		return $array;
	}
	
	public static function findMovimentosByFinalidadeAndRange($finalidade, $dataInicial, $dataFinal, $usuario){
		
		$sql = SQL_MOVIMENTO . " where m.usuario = '$usuario' " .
				       "and m.vencimento between '" . $dataInicial->toString('yyyyMMdd') . "' and '" . $dataFinal->toString('yyyyMMdd') . "' " . 
				       "and m.hashTransferencia = '' and m.fatura is NULL and m.finalidade = " . $finalidade->idEntity . "";
					   
		SystemLog::writeLog("MovimentoDao.findMovimentosByFinalidadeAndRange(): " . $sql);
					   
		$result = Database::executeQuery($sql);
		$list = array();
		while($row = mysqli_fetch_array($result)){
			array_push($list, MovimentoDao::rowToObject($row));			 
		}
		return MovimentoDao::findSubItensByFinalidadeAndRange($finalidade->idEntity, $dataInicial, $dataFinal, $usuario, $list);
	}
	
	private static function findSubItensByFinalidadeAndRange($finalidade,  $dataInicial, $dataFinal, $usuario, $list){
		$sqlSubItem = "select f.id as finalidade from finalidade f where f.finalidadeSuperior ='$finalidade' ";
		$result = Database::executeQuery($sqlSubItem);
		$subItens = array();
		//busca os movimentos de todas finalidades do primeiro sub nivel
		while($row = mysqli_fetch_array($result)){
			$list = MovimentoDao::findByItemAndRange($row['finalidade'], $dataInicial, $dataFinal, $usuario, $list);	 
			array_push($subItens, $row['finalidade']);
		}
		//para cada sub item busca os seus subitens tamb�m
		for ( $i = 0; $i < count($subItens); $i++ ) {
			return MovimentoDao::findSubItensByFinalidadeAndRange($subItens[$i], $dataInicial, $dataFinal, $usuario, $list);
		}
		return $list;
	}
	
	public static function findByItemAndRange($finalidade, $dataInicial, $dataFinal, $usuario, $array){
		$sql = SQL_FNDE . "inner join finalidade fl on ( fl.id = m.finalidade and fl.id = $finalidade ) where m.usuario = '$usuario' and m.hashTransferencia = '' " .
			           	  "and m.vencimento between '" . $dataInicial->toString('yyyyMMdd') . "' and '" . $dataFinal->toString('yyyyMMdd') . "' " . 
				      	  "and m.hashTransferencia = '' and m.fatura is NULL";
		$result = Database::executeQuery($sql);
		while($row = mysqli_fetch_array($result)){
			array_push($array, MovimentoDao::rowToObject($row));			 
		}
		return $array;
	}
	
	//===============CONTA BANCARIA============================
	public static function findByContaBancaria($contaBancaria, $usuario){
		
		$sql = SQL_MOVIMENTO . " where m.usuario = '$usuario' and m.contaBancaria ='$contaBancaria->idEntity' order by m.vencimento, m.id ";
		$result = Database::executeQuery($sql);
		return MovimentoDao::resultToArray($result);
	}

	public static function findMovimentosByCBAndRange($contaBancaria, $dataInicial, $dataFinal, $usuario){
		
		$sql = SQL_MOVIMENTO . " where m.usuario = '$usuario' " .
				"and m.vencimento between '" . $dataInicial->toString('yyyyMMdd') . "' and '" . $dataFinal->toString('yyyyMMdd') . "' " . 
				"and m.contaBancaria = " . $contaBancaria->idEntity . " order by m.vencimento, m.emissao ";
		$result = Database::executeQuery($sql);
		return MovimentoDao::resultToArray($result);
	}
	
	//===============CARTAO DE CREDITO============================
	public static function findMovimentosByCartaoCreditoAndRange($cartaoCredito, $dataInicial, $dataFinal, $usuario){
		
		$sql = SQL_MOVIMENTO . " where m.usuario = '$usuario' and m.hashTransferencia = '' and m.fatura is NULL " .
				"and m.vencimento between '" . $dataInicial->toString('yyyyMMdd') . "' and '" . $dataFinal->toString('yyyyMMdd') . "' " . 
				"and m.cartaoCredito = " . $cartaoCredito->idEntity . " order by m.vencimento, m.emissao ";
		$result = Database::executeQuery($sql);
		return MovimentoDao::resultToArray($result);
	}
	
	public static function findMovimentosByFaturaCartao($fatura){
		$sql =  SQL_MOVIMENTO . " inner join movimentosFatura mf on (mf.movimento = m.id and mf.fatura = $fatura->idEntity) where m.usuario = '$fatura->idUsuario' order by m.vencimento, m.emissao ";
		
		SystemLog::writeLog("MovimentoDao.findMovimentosByFaturaCartao(): " . $sql);
		
		$result = Database::executeQuery($sql);
		return MovimentoDao::resultToArray($result);
	}
	
	//===============FORMA PAGAMENTO============================
	public static function findMovimentosByFormaPagamentoAndRange($formaPagamento, $dataInicial, $dataFinal, $usuario){
		
		$sql = SQL_MOVIMENTO . " where m.usuario = '$usuario' " .
				"and m.vencimento between '" . $dataInicial->toString('yyyyMMdd') . "' and '" . $dataFinal->toString('yyyyMMdd') . "' " . 
				"and m.formaPagamento = " . $formaPagamento->idEntity . " order by m.vencimento, m.emissao ";
		$result = Database::executeQuery($sql);
		return MovimentoDao::resultToArray($result);
	}
	
	
	//==================GRAFICOS====================================
	
	public static function resumeByYear($year, $usuario){
		$list = array();
		for ($i = 1; $i <= 12; $i++){
			if ( $i < 10 ){
				array_push($list, MovimentoDao::getSaldoByMonth('0'.$i, $year, $usuario));
			}else{
				array_push($list, MovimentoDao::getSaldoByMonth($i, $year, $usuario));
			}		
		}
		return $list;
	}
	
	public static function getSaldoByMonth($month, $year, $usuario){
		$sql = "select * from movimento where usuario = '$usuario' " .
				" and hashTransferencia = '' and fatura is NULL and substring(vencimento, 1,6) like '%" . $year . $month . "' order by descricao ";
				
		SystemLog::writeLog("MovimentoDao.getSaldoByMonth(): " . $sql);		
				
		$result = Database::executeQuery($sql);
		$totalCreditos = 0;
		$totalDebitos = 0;
		while($row = mysqli_fetch_array($result)){
			if ( $row['operacao'] == 'DEBITO'){
				$totalDebitos += $row['valor'];				
			}else{
				$totalCreditos += $row['valor'];
			}
		}
		$object = new MovimentoReportVO();
		$object->credito = $totalCreditos;
		$object->debito = $totalDebitos;
		$object->mes = $month;
		return $object;
	}
	
	//==============SALDO ANTERIOR=============================
	public static function calculaSaldoAnteriorCB($contaBancaria, $data, $usuario){
		
		$sql = "select operacao, valor  from movimento where usuario = '$usuario' " .
				"and vencimento < '" . $data->toString('yyyyMMdd') . "' and contaBancaria = " . $contaBancaria->idEntity;
		$result = Database::executeQuery($sql);
		$saldo = 0;
		while($row = mysqli_fetch_array($result)){
			if ( $row['operacao'] == 'DEBITO'){
				$saldo -= $row['valor'];				
			}else{
				$saldo += $row['valor'];
			}
		}
		return $saldo;
	}
	
	public static function calculaSaldoAnterior($data, $usuario){
		
		$sql = "select operacao, valor  from movimento where usuario = '$usuario' " .
				" and hashTransferencia = '' and fatura is NULL and vencimento < '" . $data->toString('yyyyMMdd') . "'";
		
		SystemLog::writeLog("MovimentoDao.calculaSaldoAnterior(): " . $sql);				
				
		$result = Database::executeQuery($sql);
		$saldo = 0;
		while($row = mysqli_fetch_array($result)){
			if ( $row['operacao'] == 'DEBITO'){
				$saldo -= $row['valor'];				
			}else{
				$saldo += $row['valor'];
			}
		}
		return $saldo;
	}
	
	//===============CONVERTE RETORNO DO BANCO EM LISTA DE OBJTOS======================
	private static function resultToArray($result){
		$list = array();
		while($row = mysqli_fetch_array($result)){
			array_push($list, MovimentoDao::rowToObject($row));			 
		}
		return $list;
	}
	
	private static function rowToObject($row){
		$movimento = new MovimentoVO();
		$movimento->idEntity		   = $row['id'];
		$movimento->descricao 	 	   = $row['descricao'];
		$movimento->idUsuario		   = $row['usuario'];
		$emissao = new DateTime();
		$emissao->setDate(substr($row['emissao'],0,4), substr($row['emissao'],4,2), substr($row['emissao'],6,2)); 
		$movimento->emissao   		   = $emissao;
		$vencimento = new DateTime();
		$vencimento->setDate(substr($row['vencimento'],0,4), substr($row['vencimento'],4,2), substr($row['vencimento'],6,2));
		$movimento->vencimento		   = $vencimento;
		$finalidade                    = new FinalidadeVO();
		$finalidade->idEntity          = $row['idFinalidade'];
		$finalidade->descricao         = $row['descricaoFinalidade'];
		$finalidade->idUsuario		   = $row['usuario'];
		$movimento->finalidade		   = $finalidade;
		
		$contaBancaria                 = new ContaBancariaVO();
		$contaBancaria->idEntity       = $row['idContaBancaria'];
		$contaBancaria->descricao      = $row['descricaoContaBancaria'];
		$contaBancaria->numero         = $row['numeroContaBancaria'];
		$contaBancaria->digito         = $row['digitoContaBancaria'];
		$contaBancaria->idUsuario      = $row['usuario'];
		$movimento->contaBancaria      = $contaBancaria;
		
		$fornecedor                    = new FornecedorVO();
		$fornecedor->idEntity          = $row['idFornecedor'];
		$fornecedor->descricao         = $row['descricaoFornecedor'];
		$movimento->fornecedor         = $fornecedor;
		
		$movimento->movimentoOrigem    = $row['movimentoOrigem'];
		$movimento->parcela     	   = $row['parcela'];
		$movimento->valor		  	   = $row['valor'];
		$movimento->hashParcelas	   = $row['hashParcelas'];
		$movimento->hashTransferencia  = $row['hashTransferencia'];
		$movimento->status 		 	   = $row['status'];
		$movimento->operacao	 	   = $row['operacao'];
		
		$formaPagamento                = new FormaPagamentoVO();
		$formaPagamento->idEntity      = $row['idFormaPagamento'];
		$formaPagamento->descricao     = $row['descricaoFormaPagamento'];
		$formaPagamento->sigla         = $row['siglaFormaPagamento'];
		$formaPagamento->idUsuario	   = $row['usuario'];
		$movimento->formaPagamento     = $formaPagamento;
		
		$cartaoCredito                 = new CartaoCreditoVO();
		$cartaoCredito->idEntity       = $row['idCartaoCredito'];
		$cartaoCredito->descricao      = $row['descricaoCartaoCredito'];
		$cartaoCredito->limite         = $row['limite'];
		$cartaoCredito->dataFatura     = $row['dataFatura'];
		$cartaoCredito->dataFechamento = $row['dataFechamento'];
		$cartaoCredito->idUsuario	   = $row['usuario'];
		$movimento->cartaoCredito      = $cartaoCredito;
		
		$fatura                 	   = new FaturaVO();
		$fatura->idEntity              = $row['idFatura'];
		$fatura->mesReferencia         = $row['mesReferencia'];
		$fatura->valor                 = $row['valorFatura'];
		$fatura->valorPagamento        = $row['valorPagamentoFatura'];
		$fatura->idUsuario	           = $row['usuario'];
		$movimento->fatura             = $fatura;

		return $movimento;
	}
	
}

?>
