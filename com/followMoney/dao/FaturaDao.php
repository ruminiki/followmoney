<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("com/followMoney/dao/Database.php");
include_once("com/followMoney/domain/valueObjects/FaturaVO.php");
include_once("com/followMoney/domain/valueObjects/MovimentoVO.php");
include_once("com/followMoney/domain/valueObjects/FormaPagamentoVO.php");
include_once("com/followMoney/domain/valueObjects/CartaoCreditoVO.php");
include_once("com/followMoney/domain/valueObjects/ContaBancariaVO.php");
include_once("com/followMoney/application/util/SystemLog.php");
include_once("com/followMoney/application/util/DateUtil.php");
include_once("com/followMoney/dao/CartaoCreditoDao.php");
include_once("com/followMoney/dao/MovimentoDao.php");


define("SQL_FATURA","select f.id, f.emissao, f.vencimento, f.emissao, f.valor, f.valorPagamento, f.usuario as usuario, f.mesReferencia as mesReferencia, f.status as status, " .
		"c.id as idContaBancaria, c.descricao as descricaoContaBancaria, c.numero as numeroContaBancaria, c.digito as digitoContaBancaria, " .
		"cr.id as idCartaoCredito, cr.descricao as descricaoCartaoCredito, cr.limite as limite, cr.dataFatura as dataFatura, cr.dataFechamento as dataFechamento, " .
		"fp.id as idFormaPagamento, fp.descricao as descricaoFormaPagamento, fp.sigla as siglaFormaPagamento " .
		"from fatura f " .
		"left join contaBancaria c on (c.id = f.contaBancaria and c.usuario = f.usuario) " .
		"left join formaPagamento fp on (fp.id = f.formaPagamento and fp.usuario = f.usuario) " .
		"inner join cartaoCredito cr on (cr.id = f.cartaoCredito and cr.usuario = f.usuario)"); 

class FaturaDao {

	//ao salvar um movimento pago com cartao de credito, adiciona-o na fatura atual
	public static function adicionarMovimentoFatura($movimento){
	
		$cartaoCredito  = $movimento->cartaoCredito;
		
		$mes  = substr($movimento->vencimento->toString('yyyyMMdd'),4,2);
		$year = substr($movimento->vencimento->toString('yyyyMMdd'),0,4);
		
		$mesString = DateUtil::getRepresentacaoMesString($mes);
		
		SystemLog::writeLog("FaturaDao.adicionarMovimentoFatura(): " . $year . $mes . $mesString);
			
		//verifica se existe fatura para o período do lançamento
		$sql = "select id, status from fatura where mesReferencia = '" . $mesString . "/" . $year . "' and cartaoCredito = " . $cartaoCredito->idEntity;
		
		SystemLog::writeLog("FaturaDao.adicionarMovimentoFatura(): " . $sql);		
		
		$result = Database::executeQuery($sql);
		$list = array();
		if($row = mysqli_fetch_array($result)){
			
			if ( $row['status'] == 'FECHADA' ){
				throw new Exception('A fatura do cartão para o período selecionado já está fechada. É necessário cancelar o pagamento para reabrir a fatura e poder fazer novos lançamentos.');
			}
			
			$sql = "insert into movimentosFatura (fatura, movimento) values ( " . $row['id'] . ", $movimento->idEntity)";
			SystemLog::writeLog("FaturaDao.adicionarMovimentoFatura() " . $sql);
			Database::insert($sql);	
			
		}else{
			
			$dataFechamento = CartaoCreditoDao::getDataFechamento($cartaoCredito);
			
			if ( $mes == '01' ){
				$emissao = (intval($year) - 1).DateUtil::getMesAnterior($mes).$dataFechamento;				
			}else{
				$emissao = $year.DateUtil::getMesAnterior($mes).$dataFechamento;				
			}
			
			//$dataFatura = CartaoCreditoDao::getDataFatura($cartaoCredito);

			$sql = "insert into fatura ( emissao, vencimento, mesReferencia, usuario, cartaoCredito ) " .
					"values (" .
					"'" . $emissao . "'," .
					"'" . $movimento->vencimento->toString('yyyyMMdd') . "'," .
					"'$mesString/$year'," . 
					"$movimento->idUsuario," . 
					"$cartaoCredito->idEntity)";

			SystemLog::writeLog("FaturaDao.adicionarMovimentoFatura() " . $sql);	

			try{
				$id = Database::insert($sql);
				
				$sql = "insert into movimentosFatura (fatura, movimento) values ( $id, $movimento->idEntity)";
				
				Database::insert($sql);
				
			}catch(Exception $e){
				//throw new Exception('N?o foi poss?vel salvar o movimento selecionado.');
				throw $e;
			}
		}
		
	}
	
	
	//sempre que ocorre a consulta de fatura o sistema gera uma caso ela não exista
	public static function findFaturaByCartao($cartaoCredito, $mesReferencia){
		$valorFatura = 0;
		$saldo = 0;
	
		//calcula o valor da fatura
		$sql = "select sum(m.valor) as valor " .
			   "from movimento m " .
			   "inner join movimentosFatura mf on (mf.movimento = m.id) " .
	           "inner join fatura f on (mf.fatura = f.id) " .
               "where f.cartaoCredito = $cartaoCredito->idEntity and f.mesReferencia = '$mesReferencia' and m.operacao = 'CREDITO' "; 
		
		SystemLog::writeLog("FaturaDao.findFaturaByCartao() " . $sql);
		
		$result = Database::executeQuery($sql);
		
		if($row = mysqli_fetch_array($result)){
			$valorFatura = $row['valor'] != NULL ? $row['valor'] : 0;
			$valorFatura *= -1;
		}

		$sql = "select sum(m.valor) as valor " .
			   "from movimento m " .
			   "inner join movimentosFatura mf on (mf.movimento = m.id) " .
	           "inner join fatura f on (mf.fatura = f.id) " .
               "where f.cartaoCredito = $cartaoCredito->idEntity and f.mesReferencia = '$mesReferencia' and m.operacao = 'DEBITO' "; 
		
		SystemLog::writeLog("FaturaDao.findFaturaByCartao() " . $sql);
		
		$result = Database::executeQuery($sql);
		
		if($row = mysqli_fetch_array($result)){
			$valorFatura += $row['valor'] != NULL ? $row['valor'] : 0;
		}

		//atualiza a fatura		
		$sql = "update fatura f " .
				"set f.valor = $valorFatura " .
				"where f.cartaoCredito = $cartaoCredito->idEntity and f.mesReferencia = '$mesReferencia' "; 
				
		SystemLog::writeLog("FaturaDao.findFaturaByCartao() " . $sql);
		
		try{
			Database::update($sql);
			
			$mesInt = DateUtil::getMesRepresentationToNumber(substr($mesReferencia,0,3));
			$vencimento = substr($mesReferencia,4,4) . $mesInt . DateUtil::getLastDayMonth($mesInt); 
						
			//calcula o saldo atualizado
			$sql = "select sum(f.valorPagamento) - sum(f.valor) as saldo " .
				   "from fatura f " .
				   "where f.cartaoCredito = $cartaoCredito->idEntity"; 
				   //"where f.cartaoCredito = $cartaoCredito->idEntity and f.vencimento <= '$vencimento'"; 
			
			SystemLog::writeLog("FaturaDao.findFaturaByCartao() " . $sql);
			
			$result = Database::executeQuery($sql);
			
			if($row = mysqli_fetch_array($result)){
				$saldo = $row['saldo'] != NULL ? $row['saldo'] : 0;
			}
			
		}catch(Exception $e){
			throw new Exception('Erro ao atualizar valor da fatura.');
		}
		
		try{
			$sql = SQL_FATURA . " where f.cartaoCredito = $cartaoCredito->idEntity and f.mesReferencia = '$mesReferencia'";
			$result = Database::executeQuery($sql);
			
			if($row = mysqli_fetch_array($result)){
				$fatura = FaturaDao::rowToObject($row);
				$fatura->saldo = $saldo;
				return $fatura;
			}else{
				return null;
			}	
			
		}catch(Exception $e){
			throw new Exception('Erro ao carregar fatura.');
		}
			
	}
	
	public static function removerFaturaCartao($fatura){
		try{
		
			if ( $fatura->status == 'ABERTA' ){
				$result = Database::executeQuery("select count(*) as total from movimentosFatura where fatura = $fatura->idEntity");
				
				if($row = mysqli_fetch_array($result)){
					if ( $row['total'] > 0 ){
						throw new Exception('A fatura possui movimentos associados. Por favor, remova-os antes de excluí-la.');	
					}
				}
				
				Database::remove("delete from fatura where id = $fatura->idEntity");
				
			}else{
				throw new Exception('A fatura não pode ser removida, pois encontra-se fechada. Cancele o pagamento e reabra a fatura para que seja possível removê-la.');	
			}
		
		}catch(Exception $e){
			throw $e;
		}
		
	}
	
	public static function pagarFaturaCartao($fatura, $finalidadePagamento){
		
		$cartaoCredito = $fatura->cartaoCredito;
			
		//--------cadastro do movimento
		$movimento = new MovimentoVO();
		$movimento->descricao = "FATURA (" . $cartaoCredito->descricao . ") - " . strtoupper($fatura->mesReferencia);
		$movimento->emissao = $fatura->emissao;
		$movimento->vencimento = $fatura->vencimento;
		$movimento->valor = $fatura->valorPagamento;
		$movimento->status = 'PAGO';
		$movimento->operacao = 'DEBITO';
		$movimento->finalidade = $finalidadePagamento;
		$movimento->contaBancaria = $fatura->contaBancaria;
		$movimento->formaPagamento = $fatura->formaPagamento;
		$movimento->idUsuario = $fatura->idUsuario;
		$movimento->fatura = $fatura;
		
		try{
			$movimento = MovimentoDao::save($movimento);
			$fatura->status = 'FECHADA';
			$sql = "update fatura set status = 'FECHADA', valorPagamento = $fatura->valorPagamento where id = $fatura->idEntity";
			Database::update($sql);
			SystemLog::writeLog("FaturaDao.pagarFaturaCartao() " . 'Pagamento realizado com sucesso. Movimento gerado: ' . $movimento->idEntity);
			
			$sql = "update movimento set status = 'PAGO' where id in (select movimento from movimentosFatura where fatura = $fatura->idEntity)";
			Database::update($sql);
			
			SystemLog::writeLog("FaturaDao.pagarFaturaCartao(): " . $sql);
			
			return $fatura;
		}catch(Exception $e){
			throw $e;
		}
		
	}
	
	public static function cancelarPagamentoFatura($fatura){
		try{
			$sql = "delete from movimento where fatura = $fatura->idEntity";
			Database::remove($sql);
			SystemLog::writeLog("FaturaDao.cancelarPagamentoFatura(): " . $sql);
			
			$sql = "update fatura set status = 'ABERTA', valorPagamento = NULL where id = $fatura->idEntity";
			SystemLog::writeLog("FaturaDao.cancelarPagamentoFatura(): " . $sql);
			
			Database::update($sql);
			
			$sql = "update movimento set status = '' where id in (select movimento from movimentosFatura where fatura = $fatura->idEntity)";
			Database::update($sql);
			
			SystemLog::writeLog("FaturaDao.cancelarPagamentoFatura(): " . $sql);
			
			return $fatura;	
		}catch(Exception $e){
			throw $e;
		}
	}
	
	private static function resultToArray($result){
		$list = array();
		while($row = mysqli_fetch_array($result)){
			array_push($list, FaturaDao::rowToObject($row));			 
		}
		return $list;
	}
	
	private static function rowToObject($row){
		$fatura                        = new FaturaVO();
		$fatura->idEntity              = $row['id'];
		$emissao = new DateTime();
		$emissao->setDate(substr($row['emissao'],0,4), substr($row['emissao'],4,2), substr($row['emissao'],6,2)); 
		$fatura->emissao               = $emissao;
		$fatura->idUsuario             = $row['usuario'];
		$vencimento = new DateTime();
		$vencimento->setDate(substr($row['vencimento'],0,4), substr($row['vencimento'],4,2), substr($row['vencimento'],6,2));
		$fatura->vencimento			   = $vencimento;
		$fatura->valor                 = $row['valor'];
		$fatura->valorPagamento        = $row['valorPagamento'];
		$fatura->mesReferencia         = $row['mesReferencia'];
		$fatura->status                = $row['status'];
		
		$cartaoCredito                 = new CartaoCreditoVO();
		$cartaoCredito->idEntity       = $row['idCartaoCredito'];
		$cartaoCredito->descricao      = $row['descricaoCartaoCredito'];
		$cartaoCredito->limite         = $row['limite'];
		$cartaoCredito->dataFatura     = $row['dataFatura'];
		$cartaoCredito->dataFechamento = $row['dataFechamento'];
		$cartaoCredito->idUsuario	   = $row['usuario'];
		$fatura->cartaoCredito         = $cartaoCredito;
		
		$formaPagamento                = new FormaPagamentoVO();
		$formaPagamento->idEntity      = $row['idFormaPagamento'];
		$formaPagamento->descricao     = $row['descricaoFormaPagamento'];
		$formaPagamento->sigla         = $row['siglaFormaPagamento'];
		$formaPagamento->idUsuario	   = $row['usuario'];
		$fatura->formaPagamento        = $formaPagamento;
		
		$contaBancaria                 = new ContaBancariaVO();
		$contaBancaria->idEntity       = $row['idContaBancaria'];
		$contaBancaria->descricao      = $row['descricaoContaBancaria'];
		$contaBancaria->numero         = $row['numeroContaBancaria'];
		$contaBancaria->idUsuario      = $row['usuario'];
		$fatura->contaBancaria         = $contaBancaria;
		
		return $fatura;
	}
}

?>
