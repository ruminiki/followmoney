<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("com/milkManager/dao/IGenericDao.php");
include_once("com/milkManager/dao/Database.php");
include_once("com/milkManager/domain/valueObjects/CotacaoLeiteVO.php");
include_once("com/milkManager/application/util/SystemLog.php");

class CotacaoLeiteDao implements IGenericDao{
		
	public static function save($cotacaoLeite){
		$sql = "insert into cotacaoLeite (mes, valor, empresa) values ('$cotacaoLeite->mes',$cotacaoLeite->valor,'$cotacaoLeite->empresa')";
				
		$id = Database::insert($sql);
		
		if ( $id > 0 ){
			$cotacaoLeite->idEntity = $id;
			return $cotacaoLeite;
		}
		return null;
	}
	
	public static function update($cotacaoLeite){
		$cotacaoLeite->valor = str_replace(",", ".", $cotacaoLeite->valor);
		
		$sql = "update cotacaoLeite set valor = $cotacaoLeite->valor where id = $cotacaoLeite->idEntity";
		if ( Database::update($sql) ){
			return $cotacaoLeite;
		}
		return null; 
	}
	
	public static function remove($cotacaoLeite){
		$sql = "delete from cotacaoLeite where id = $cotacaoLeite->idEntity";
		if ( Database::remove($sql) ){
			return $cotacaoLeite;
		}
		return null;
	}
	
	public static function findAll($empresa){
		$sql = "select * from cotacaoLeite where empresa = '$empresa' order by mes ";
		$result = Database::executeQuery($sql);
		return CotacaoLeiteDao::resultToArray($result);
	}
	
	public static function findById($id){
		$sql = "select * from cotacaoLeite where id = '$id' order by mes ";
		$result = Database::executeQuery($sql);
		if ( $row = mysql_fetch_array($result) ){
			return CotacaoLeiteDao::rowToObject($row);
		}
		return null;	
	}
	
	public static function findByMes($mes, $empresa){
		$sql = "select * from cotacaoLeite where mes = '$mes' and empresa = $empresa order by mes ";
		$result = Database::executeQuery($sql);
		if ( $row = mysql_fetch_array($result) ){
			return CotacaoLeiteDao::rowToObject($row);
		}
		return null;	
	}
	
	public static function findByYear($year, $empresa){
		
		$sql = "select * from cotacaoLeite where substring(mes, 1, 4) = '$year' and empresa = $empresa order by mes ";
		SystemLog::writeLog($sql);
		$result = Database::executeQuery($sql);
		
		if ( mysql_num_rows($result) > 0 ){
			return CotacaoLeiteDao::resultToArray($result);
		}else{
			for ( $i = 1; $i <= 12; $i++ ) {
				$monthString = "";
				if ( $i < 10 ){
					$monthString = '0'.$i;
				}else{
					$monthString = $i;
				}
				$sql = "insert into cotacaoLeite (valor, mes, empresa) values (0.0, '$year$monthString', $empresa)";
				SystemLog::writeLog($sql);
				Database::insert($sql);
			}
			return CotacaoLeiteDao::findByYear($year, $empresa);
		}
		return null;	
	}
	
	private static function resultToArray($result){
		$list = array();
		while($row = mysql_fetch_array($result)){
			array_push($list, CotacaoLeiteDao::rowToObject($row));			 
		}
		return $list;
	}
	
	private static function rowToObject($row){
		$cotacaoLeite            = new CotacaoLeiteVO();
		$cotacaoLeite->idEntity  = $row['id'];
		$cotacaoLeite->mes       = $row['mes'];
		$cotacaoLeite->valor     = $row['valor'];
		$cotacaoLeite->empresa   = $row['empresa'];
		return $cotacaoLeite;
	}
}

?>
