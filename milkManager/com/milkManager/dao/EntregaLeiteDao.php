<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("com/milkManager/dao/IGenericDao.php");
include_once("com/milkManager/dao/Database.php");
include_once("com/milkManager/application/util/DateUtil.php");
include_once("com/milkManager/application/util/SystemLog.php");
include_once("com/milkManager/domain/valueObjects/EntregaLeiteVO.php");
include_once("com/milkManager/domain/valueObjects/EntregaLeiteReport.php");

class EntregaLeiteDao implements IGenericDao{
		
	public static function save($entregaLeite){
		$sql = "insert into entregaLeite (quantidade, dataEntrega, numeroAnimais, observacao, empresa) " .
				"values (" .
				"$entregaLeite->quantidade," .
				"'".$entregaLeite->dataEntrega->toString('yyyyMMdd')."'," .
				"$entregaLeite->numeroAnimais," .
				"'$entregaLeite->observacao'," .
				"$entregaLeite->empresa)";
				
		$id = Database::insert($sql);
		
		if ( $id > 0 ){
			$entregaLeite->idEntity = $id;
			return $entregaLeite;
		}
		return null;
	}
	
	public static function update($entregaLeite){
		$sql = "update entregaLeite set " .
				"quantidade = $entregaLeite->quantidade, " .
				"dataEntrega = '".$entregaLeite->dataEntrega->toString('yyyyMMdd')."', " .
				"numeroAnimais = $entregaLeite->numeroAnimais," .
				"observacao = '$entregaLeite->observacao'," .
				"empresa = $entregaLeite->empresa where id = $entregaLeite->idEntity";
		SystemLog::writeLog($sql);		
		if ( Database::update($sql) ){
			return $entregaLeite;
		}
		return null; 
	}
	
	public static function remove($entregaLeite){
		$sql = "delete from entregaLeite where id = $entregaLeite->idEntity";
		if ( Database::remove($sql) ){
			return $entregaLeite;
		}
		return null;
	}
	
	public static function findByYearAndMonth($empresa, $year, $month){
		$monthString = "";
		
		if ( $month < 10 ){
			$monthString = '0'.$month;
		}else{
			$monthString = $month;
		}
		
		$sql = "select el.id, el.quantidade, el.dataEntrega, el.empresa, el.numeroAnimais, el.observacao, (quantidade * cotacao.valor) as valor " .
		       "from entregaLeite el left join cotacaoLeite cotacao on (cotacao.mes = '$year$monthString' and cotacao.empresa = $empresa) where el.empresa = $empresa and substring(el.dataEntrega, 1, 6) = '$year$monthString' order by el.dataEntrega ";	
		SystemLog::writeLog($sql);
				
		$result = Database::executeQuery($sql);
		
		if ( mysql_num_rows($result) > 0 ){
			return EntregaLeiteDao::resultToArray($result);
		}else{
			for ( $i = 1; $i <= DateUtil::getLastDayMonth($month); $i++ ) {
				$sql = "";
				$dayString = "";
				if ( $i < 10 ){
					$dayString = '0'.$i;
				}else{
					$dayString = $i;
				}
				$sql = "insert into entregaLeite (dataEntrega, empresa) values ('$year$monthString$dayString', $empresa)";
				
				SystemLog::writeLog($sql . ' - ' . $i . ' - ' . DateUtil::getLastDayMonth($month));
				
				Database::insert($sql);
			}
			return EntregaLeiteDao::findByYearAndMonth($empresa, $year, $month);
		}
	}
	
	public static function findByYear($empresa, $year){
		$monthString = "";
		$list = array();
		
		for ($month = 1; $month <= 12; $month++){
		
			if ( $month < 10 ){
				$monthString = '0'.$month;
			}else{
				$monthString = $month;
			}
		
			$sql = "select sum(quantidade) as quantidade from entregaLeite where empresa = $empresa and substring(dataEntrega, 1, 6) = '$year$monthString'";	
			SystemLog::writeLog($sql);
				
			$result = Database::executeQuery($sql);
		
			if ( $row = mysql_fetch_array($result) ){
			
				$report = new EntregaLeiteReport();
				$report->quantidade = $row['quantidade'];
				$report->data = $monthString;
				
				array_push($list, $report);	
							
			}
		}
		
		return $list;
		
	}
	
	public static function findAll($empresa){
		
		
	}
		
	private static function resultToArray($result){
		$list = array();
		while($row = mysql_fetch_array($result)){
			array_push($list, EntregaLeiteDao::rowToObject($row));			 
		}
		return $list;
	}
	
	private static function rowToObject($row){
		$entregaLeite                = new EntregaLeiteVO();
		$entregaLeite->idEntity      = $row['id'];
		$dataEntrega = new DateTime();
		$dataEntrega->setDate(substr($row['dataEntrega'],0,4), substr($row['dataEntrega'],4,2), substr($row['dataEntrega'],6,2)); 
		$entregaLeite->dataEntrega   = $dataEntrega;
		$entregaLeite->empresa       = $row['empresa'];
		$entregaLeite->numeroAnimais = $row['numeroAnimais'];
		$entregaLeite->quantidade    = $row['quantidade'];
		$entregaLeite->observacao    = $row['observacao'];
		$entregaLeite->valor         = $row['valor'];
		
		return $entregaLeite;
	}
}

?>
