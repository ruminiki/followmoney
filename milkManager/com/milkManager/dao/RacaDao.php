<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("com/milkManager/dao/IGenericDao.php");
include_once("com/milkManager/dao/Database.php");
include_once("com/milkManager/domain/valueObjects/RacaVO.php");

class RacaDao implements IGenericDao{
		
	public static function save($raca){
		$sql = "insert into raca (descricao, empresa) " .
				"values (" .
				"'$raca->descricao'," .
				"'$raca->empresa')";
				
		$id = Database::insert($sql);
		
		if ( $id > 0 ){
			$raca->idEntity = $id;
			return $raca;
		}
		return null;
	}
	
	public static function update($raca){
		$sql = "update raca set " .
				"descricao = '$raca->descricao', " .
				"empresa = '$raca->empresa' where id = $raca->idEntity";
		if ( Database::update($sql) ){
			return $raca;
		}
		return null; 
	}
	
	public static function remove($raca){
		$sql = "delete from raca where id = $raca->idEntity";
		if ( Database::remove($sql) ){
			return $raca;
		}
		return null;
	}
	
	public static function findAll($empresa){
		$sql = "select * from raca where empresa = '$empresa' order by descricao ";
		$result = Database::executeQuery($sql);
		return RacaDao::resultToArray($result);
	}
	
	public static function findByDescricao($descricao, $empresa){
		$sql = "select * from raca where empresa = '$empresa' and descricao like '%$descricao%' order by descricao ";
		$result = Database::executeQuery($sql);
		return RacaDao::resultToArray($result);
	}
	
	public static function findById($id){
		$sql = "select * from raca where id = '$id' order by descricao ";
		$result = Database::executeQuery($sql);
		if ( $row = mysql_fetch_array($result) ){
			return RacaDao::rowToObject($row);
		}
		return null;	
	}
	
	private static function resultToArray($result){
		$list = array();
		while($row = mysql_fetch_array($result)){
			array_push($list, RacaDao::rowToObject($row));			 
		}
		return $list;
	}
	
	private static function rowToObject($row){
		$raca            = new RacaVO();
		$raca->idEntity  = $row['id'];
		$raca->descricao = $row['descricao'];
		$raca->empresa   = $row['empresa'];
		return $raca;
	}
}

?>
