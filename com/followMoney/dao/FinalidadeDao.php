<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("com/followMoney/dao/IGenericDao.php");
include_once("com/followMoney/dao/Database.php");
include_once("com/followMoney/application/util/DateUtil.php");
include_once("com/followMoney/application/util/SystemLog.php");
include_once("com/followMoney/domain/valueObjects/FinalidadeVO.php");
include_once("com/followMoney/domain/valueObjects/FinalidadeReportVO.php");
include_once("com/followMoney/domain/valueObjects/MovimentoReportVO.php");

define("SQL_FINALIDADE","select f.id, f.descricao, f.usuario, s.id as idSuperior, s.descricao as descricaoSuperior " .
		"from finalidade f " .
		"left join finalidade s on (s.id = f.finalidadeSuperior) "); 
		
define("SQL_GRAFICO_MENSAL", "select fl.id as idFinalidade, fl.descricao as descricaoFinalidade, m.operacao, " .
	   "sum(m.valor) as total from movimento m  " .
	   "inner join finalidade fl on (fl.id = m.finalidade and m.usuario = fl.usuario)  "); 
	   
define("SQL_SINTETICO", "select  f.id as idFinalidade, f.descricao as descricaoFinalidade, m.operacao as operacao, sum(m.valor) as total from  movimento m inner join finalidade f " .
		"on (f.id = m.finalidade) ");

class FinalidadeDao implements IGenericDao{
		
	public static function save($finalidade){
		$finalidadeSuperior = ($finalidade->finalidadeSuperior != null && $finalidade->finalidadeSuperior->idEntity > 0) ? ($finalidade->finalidadeSuperior->idEntity) : 'NULL';
		$sql = "insert into finalidade (descricao, usuario, finalidadeSuperior) values ('$finalidade->descricao', '$finalidade->idUsuario', $finalidadeSuperior)";
		$id = Database::insert($sql);
		if ( $id > 0 ){
			$finalidade->idEntity = $id;
			return $finalidade;
		}
		return null;
	}
	
	public static function update($finalidade){
		$finalidadeSuperior = ($finalidade->finalidadeSuperior != null && $finalidade->finalidadeSuperior->idEntity > 0) ? ($finalidade->finalidadeSuperior->idEntity) : 'NULL';
		$sql = "update finalidade set descricao = '$finalidade->descricao ', finalidadeSuperior=$finalidadeSuperior where id = $finalidade->idEntity and usuario ='$finalidade->idUsuario'";
		if ( Database::update($sql) ){
			return $finalidade;
		}
		return null; 
	}
	
	public static function remove($finalidade){
		$sql = "delete from finalidade where id = $finalidade->idEntity and usuario = '$finalidade->idUsuario'";
		if ( Database::remove($sql) ){
			return $finalidade;
		}
		return null;
	}
	
	public static function findAll($usuario){
		$sql = SQL_FINALIDADE . " where f.usuario = '$usuario' order by f.descricao ";
		$result = Database::executeQuery($sql);
		return FinalidadeDao::resultToArray($result);
	}
	
	public static function findByDescricao($descricao, $usuario){
		$sql = SQL_FINALIDADE . " where f.usuario = '$usuario' and f.descricao like '%$descricao%' order by f.descricao ";
		$result = Database::executeQuery($sql);
		return FinalidadeDao::resultToArray($result);
	}
	
	public static function findById($id, $usuario){
		$sql = SQL_FINALIDADE . " where f.usuario = '$usuario' and f.id = '$id' order by f.descricao ";
		$result = Database::executeQuery($sql);
		if ( $row = mysqli_fetch_array($result) ){
			return FinalidadeDao::rowToObject($row);
		}
		return null;	
	}
	
	private static function resultToArray($result){
		$list = array();
		while($row = mysqli_fetch_array($result)){
			array_push($list, FinalidadeDao::rowToObject($row));			 
		}
		return $list;
	}
	
	public static function findSaldoAnalitico($month, $year, $usuario){
		
		$date1 = new Zend_Date(array('year' => $year, 'month' => $month, 'day' => '01'));
		$date2 = new Zend_Date(array('year' => $year, 'month' => $month, 'day' => DateUtil::getLastDayMonth($month)));
		
		//SystemLog::writeLog("FinalidadeDao.findSaldoAnalitico(): " . $sql);
		
		$sql = "select * from ( " .
				"select " .
				"	f.id as id, " .
				"	f.descricao as finalidade, " .
				"	(select " .
				"		sum(m1.valor)  " .
				"		from movimento m1  " .
				"		where m1.finalidade = f.id and m1.operacao = 'CREDITO' and  " .
				"			m1.vencimento between '" . $date1->toString('yyyyMMdd') . "' and '" . $date2->toString('yyyyMMdd'). "' and  " .
				"			m1.hashTransferencia = '') as credito, " .
				"	(select  " .
				"		sum(m2.valor)  " .
				"		from movimento m2  " .
				"		where m2.finalidade = f.id and m2.operacao = 'DEBITO' and  " .
				"			m2.vencimento between '" . $date1->toString('yyyyMMdd') . "' and '" . $date2->toString('yyyyMMdd'). "' and  " .
				"			m2.hashTransferencia = '') as debito " .
				"from finalidade f " .
				"where  " .
				"	f.usuario = $usuario " .
				") as result where result.credito is not NULL or result.debito is not NULL ";

		SystemLog::writeLog("FinalidadeDao.findSaldoAnalitico(): " . $sql);
		
		$result = Database::executeQuery($sql);
		
		$list = array();
		while($row = mysqli_fetch_array($result)){
		
			$report             = new FinalidadeReportVO();
			$report->credito    = $row['credito'];
			$report->debito     = $row['debito'];
			$report->id         = $row['id'];
			$report->finalidade = $row['finalidade'];
		
			array_push($list, $report);			 
		}
		
		return $list;
		
		return FinalidadeDao::resultToArray($result);
	}
	
	//==================GRAFICO====================================
	
	public static function resumeByYear($finalidade, $year, $usuario){
		$list = array();
		for ($i = 1; $i <= 12; $i++){
			array_push($list, FinalidadeDao::getSaldoByMonth($finalidade, $i, $year, $usuario));
		}
		return $list;
	}
	
	public static function getSaldoByMonth($finalidade, $month, $year, $usuario){
		$movimentos = array();
		
		$date1 = new Zend_Date(array('year' => $year, 'month' => $month, 'day' => '01'));
		$date2 = new Zend_Date(array('year' => $year, 'month' => $month, 'day' => DateUtil::getLastDayMonth($month)));
		
		$movimentos = MovimentoDao::findMovimentosByFinalidadeAndRange($finalidade, $date1, $date2, $usuario);
		
		$totalCreditos = 0;
		$totalDebitos = 0;
		foreach($movimentos as $movimento){
			if ( $movimento->operacao == 'DEBITO'){
				$totalDebitos += $movimento->valor;				
			}else{
				$totalCreditos += $movimento->valor;
			}
    	}
    	$object = new MovimentoReportVO();
		$object->credito = $totalCreditos;
		$object->debito = $totalDebitos;
		$object->mes = $month;
		return $object;
	}
	
	
	public static function findSaldosByMonth($month, $year, $usuario, $operacao){
		
		$dataInicial = new Zend_Date(array('year' => $year, 'month' => $month, 'day' => '01'));
		$dataFinal = new Zend_Date(array('year' => $year, 'month' => $month, 'day' => DateUtil::getLastDayMonth($month)));
		
		SystemLog::writeLog("FinalidadeDao.findSaldosByMonth(): month " . $month);
		SystemLog::writeLog("FinalidadeDao.findSaldosByMonth(): DateUtil::getLastDayMonth($month)) " . DateUtil::getLastDayMonth($month));
		SystemLog::writeLog("FinalidadeDao.findSaldosByMonth(): dataFinal " . $dataFinal->toString('yyyyMMdd'));
		
		$sql = SQL_GRAFICO_MENSAL . " where m.usuario = '$usuario' and m.hashTransferencia = '' and m.fatura is NULL and m.vencimento between '" . 
									$dataInicial->toString('yyyyMMdd') . "' and '" . $dataFinal->toString('yyyyMMdd') . "' and m.operacao = '$operacao' group by m.operacao, fl.descricao";

		SystemLog::writeLog("FinalidadeDao.findSaldosByMonth(): " . $sql);
									
		$result = Database::executeQuery($sql);
		
		$list = array();
		while($row = mysqli_fetch_array($result)){
		
			$report             = new FinalidadeReportVO();
			$report->valor      = $row['total'];
			$report->operacao   = $row['operacao'];
			$report->id         = $row['idFinalidade'];
			$report->finalidade = $row['descricaoFinalidade'];
		
			array_push($list, $report);			 
		}
		
		return $list;
		
	}
	
	private static function rowToObject($row){
		$finalidade = new FinalidadeVO();
		$finalidade->idEntity  = $row['id'];
		$finalidade->descricao = $row['descricao'];
		$finalidade->idUsuario = $row['usuario'];
		$superior = new FinalidadeVO();
		$superior->idEntity = $row['idSuperior'];
		$superior->descricao = $row['descricaoSuperior'];
		$superior->idUsuario = $row['usuario'];
		$finalidade->finalidadeSuperior = $superior;
		return $finalidade;
	}
	
}

?>
