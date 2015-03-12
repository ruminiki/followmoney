<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("com/followMoney/dao/Database.php");
include_once("com/followMoney/domain/valueObjects/MovimentoProgramadoVO.php");
include_once("com/followMoney/domain/valueObjects/MovimentoVO.php");
include_once("com/followMoney/application/util/SystemLog.php");

define("SQL_PROGRAMADOS","select m.id, m.descricao, m.usuario, m.emissao, m.vencimento, m.valor, m.status, m.operacao, m.movimentoOrigem," .
		"m.parcela, m.hashParcelas, m.hashTransferencia, mp.id as idMovimentoProgramado, mp.ultimaGeracao, mp.periodicidade, fl.id as idFinalidade, fl.descricao as descricaoFinalidade," .
		"c.id as idContaBancaria, c.descricao as descricaoContaBancaria, c.numero as numeroContaBancaria," .
		"c.digito as digitoContaBancaria, f.id as idFornecedor, f.descricao as descricaoFornecedor, " .
		"cr.id as idCartaoCredito, cr.descricao as descricaoCartaoCredito, cr.limite as limite, cr.dataFatura as dataFatura, cr.dataFechamento as dataFechamento," .
		"fp.id as idFormaPagamento, fp.descricao as descricaoFormaPagamento, fp.sigla as siglaFormaPagamento " .
		"from movimento m " .
		"left join contaBancaria c on (c.id = m.contaBancaria and c.usuario = m.usuario) " .
		"left join formaPagamento fp on (fp.id = m.formaPagamento and fp.usuario = m.usuario) " .
		"left join cartaoCredito cr on (cr.id = m.cartaoCredito and cr.usuario = m.usuario) " .
		"left join fornecedor f on (f.id = m.fornecedor and f.usuario = m.usuario) " .
		"inner join finalidade fl on (fl.id = m.finalidade and fl.usuario = m.usuario) " .
		"inner join movimentosProgramados mp on (mp.movimento = m.id) "); 

class MovimentoProgramadoDao {
		
	public static function findMovimentosProgramados($usuario){
		$sql =  SQL_PROGRAMADOS . " where m.usuario = '$usuario' order by m.vencimento, m.id ";
		
		SystemLog::writeLog("MovimentoProgramadoDao.findMovimentosProgramados(): " . $sql);
		
		$result = Database::executeQuery($sql);
		return MovimentoProgramadoDao::resultToArray($result);
	}
	
	public static function cancelarAgendamento($movimentoProgramado){
		$sql = "delete from movimentosProgramados where id = " . $movimentoProgramado->idEntity;
		
		SystemLog::writeLog("MovimentoProgramadoDao.cancelarAgendamento(): " . $sql);
		
		Database::update($sql);
		return;
	}
	
	public static function agendarLancamentoProgramado($movimentoProgramado){
		$movimento = $movimentoProgramado->movimento;
    
		SystemLog::writeLog("MovimentoProgramadoDao.agendarLancamentoProgramado(): " . $movimento->idEntity);
		
		$sql = "select * from movimentosProgramados where movimento = $movimento->idEntity or movimento = $movimento->movimentoOrigem";
		$result = Database::executeQuery($sql);
		$row = mysqli_fetch_array($result);
		if ( $row ){
			throw new Exception("O movimento selecionado já está agendado.");
		}
	
		/*$sql = "select * from movimentosProgramados where movimento =" . $movimento->movimentoOrigem;
		$result = Database::executeQuery($sql);
		$row = mysqli_fetch_array($result);
		if ( $row ){
			throw new Exception("O movimento selecionado já está agendado.");
		}*/
		
		$sql = "insert into movimentosProgramados ( inicio, movimento, ultimaGeracao, periodicidade ) " .
			   "values (".
			   "'" . $movimentoProgramado->inicio->toString('yyyyMMdd') . "', " . 
			   $movimento->idEntity . ",".
			   "'" . $movimentoProgramado->ultimaGeracao->toString('yyyyMMdd') . "', ".
			   "'$movimentoProgramado->periodicidade')";
		
		try{	   
			$id = Database::insert($sql);
			if ( $id > 0 ){
				$movimentoProgramado->idEntity = $id;
			}	
			return $movimentoProgramado;	
		}catch(Exception $e){
			throw $e;
		}		
	}
	
	/*private static function gerarMovimentosProgramados(){
		
		$endDate = new Zend_Date();
		$endDate->add(4, Zend_Date::MONTH);
		
		$sql = "select * from movimentosProgramados where " .
				"substring(ultimaGeracao, 1,6) < '".$endDate->toString("yyyyMM")."' or " .
						"(ultimaGeracao = '' and substring(inicio, 1,6) <= '".$endDate->toString("yyyyMM")."')";

		$result = Database::executeQuery($sql);
		
		//$f = fopen("dates.txt", 'a');
		//fwrite($f, "SQL: " . $sql . "\n");
		//fwrite($f, "EndDate: " . $endDate->toString("dd/MM/yyyy"). "\n");
				
		while($row = mysqli_fetch_array($result)){
			
			//fwrite($f, "Inicio: " . $row['inicio']. "\n");
			//fwrite($f, "UltimaGeracao: " . $row['ultimaGeracao']. "\n");
						
			$last = null;
			if ( $row['ultimaGeracao'] == '' || $row['ultimaGeracao'] == null || isset($row['ultimaGeracao']) ){
				$last = new Zend_Date(array('year' => substr($row['inicio'],0,4), 'month' => substr($row['inicio'],4,2), 'day' => substr($row['inicio'],6,2)));
			}else{
				$last = new Zend_Date(array('year' => substr($row['ultimaGeracao'],0,4), 'month' => substr($row['ultimaGeracao'],4,2), 'day' => substr($row['ultimaGeracao'],6,2)));
			}
			
			//fwrite($f, "LastDate1: " . $last->toString("dd/MM/yyyy"). "\n");
			
			while ( $last->toString('yyyyMM') < $endDate->toString('yyyyMM') ){ 
				$last->add(1, Zend_Date::MONTH);
				
				//fwrite($f, "LastDate2: " . $last->toString("dd/MM/yyyy"). "\n");
				
				$sql = "insert into movimento (descricao, usuario, emissao, vencimento, " .
 								"valor, status, operacao, hashParcelas, hashTransferencia, parcela, " .
   								"finalidade, contaBancaria, fornecedor, movimentoOrigem) " . 
								"select descricao, usuario, " .
									"concat('". $last->toString("yyyyMM") . "',substring(emissao,7,2)) as emissao, " .
									"concat('". $last->toString("yyyyMM") . "',substring(vencimento,7,2)) as vencimento, " .
									"valor, status, operacao, hashParcelas, hashTransferencia, parcela, " .
									"finalidade, contaBancaria, fornecedor, id from movimento m where m.id = " . $row['movimento'];
				
				Database::insert($sql);
			}
			
			$sql = "update movimentosProgramados set ultimaGeracao = '" . $last->toString("yyyyMM") . substr($row['inicio'],6,2) . "' " .
					"where id = " . $row['id'];
			Database::update($sql);		
		}
	}*/
	
	private static function resultToArray($result){
		$list = array();
		while($row = mysqli_fetch_array($result)){
			array_push($list, MovimentoProgramadoDao::rowToObject($row));			 
		}
		return $list;
	}
	
	private static function rowToObject($row){
		$movimentoProgramado                = new MovimentoProgramadoVO();
		
		$ultimaGeracao = new DateTime();
		$ultimaGeracao->setDate(substr($row['ultimaGeracao'],0,4), substr($row['ultimaGeracao'],4,2), substr($row['ultimaGeracao'],6,2));
		
		$movimentoProgramado->ultimaGeracao	= $ultimaGeracao;
		$movimentoProgramado->periodicidade	= $row['periodicidade'];
		$movimentoProgramado->idEntity      = $row['idMovimentoProgramado'];
		
		$movimento                          = new MovimentoVO();
		$movimento->idEntity		        = $row['id'];
		$movimento->descricao 	 	        = $row['descricao'];
		$movimento->idUsuario		        = $row['usuario'];
		$movimento->valor					= $row['valor'];
		$emissao = new DateTime();
		$emissao->setDate(substr($row['emissao'],0,4), substr($row['emissao'],4,2), substr($row['emissao'],6,2)); 
		$movimento->emissao   		        = $emissao;
		$vencimento = new DateTime();
		$vencimento->setDate(substr($row['vencimento'],0,4), substr($row['vencimento'],4,2), substr($row['vencimento'],6,2));
		$movimento->vencimento		        = $vencimento;
		
		$movimentoProgramado->movimento     = $movimento;
		
		return $movimentoProgramado;
	}
}

?>
