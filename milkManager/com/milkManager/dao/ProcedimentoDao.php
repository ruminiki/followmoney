<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("com/milkManager/dao/IGenericDao.php");
include_once("com/milkManager/dao/Database.php");
include_once("com/milkManager/domain/valueObjects/ProcedimentoVO.php");

class ProcedimentoDao implements IGenericDao{
		
	public static function save($procedimento){
		$sql = "insert into procedimento (descricao, empresa) " .
				"values (" .
				"'$procedimento->descricao'," .
				"'$procedimento->empresa')";
				
		$id = Database::insert($sql);
		
		if ( $id > 0 ){
			$procedimento->idEntity = $id;
			return $procedimento;
		}
		return null;
	}
	
	public static function update($procedimento){
		$sql = "update procedimento set " .
				"descricao = '$procedimento->descricao', " .
				"empresa = '$procedimento->empresa' where id = $procedimento->idEntity";
		if ( Database::update($sql) ){
			return $procedimento;
		}
		return null; 
	}
	
	public static function remove($procedimento){
		$sql = "delete from procedimento where id = $procedimento->idEntity";
		if ( Database::remove($sql) ){
			return $procedimento;
		}
		return null;
	}
	
	public static function findAll($empresa){
		$sql = "select * from procedimento where empresa = '$empresa' order by descricao ";
		$result = Database::executeQuery($sql);
		return ProcedimentoDao::resultToArray($result);
	}
	
	public static function findByDescricao($descricao, $empresa){
		$sql = "select * from procedimento where empresa = '$empresa' and descricao like '%$descricao%' order by descricao ";
		$result = Database::executeQuery($sql);
		return ProcedimentoDao::resultToArray($result);
	}
	
	public static function findById($id){
		$sql = "select * from procedimento where id = '$id' order by descricao ";
		$result = Database::executeQuery($sql);
		if ( $row = mysql_fetch_array($result) ){
			return ProcedimentoDao::rowToObject($row);
		}
		return null;	
	}
	
	private static function resultToArray($result){
		$list = array();
		while($row = mysql_fetch_array($result)){
			array_push($list, ProcedimentoDao::rowToObject($row));			 
		}
		return $list;
	}
	
	private static function rowToObject($row){
		$procedimento            = new ProcedimentoVO();
		$procedimento->idEntity  = $row['id'];
		$procedimento->descricao = $row['descricao'];
		$procedimento->empresa   = $row['empresa'];
		return $procedimento;
	}
}

?>
