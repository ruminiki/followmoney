<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("com/milkManager/dao/IGenericDao.php");
include_once("com/milkManager/dao/Database.php");
include_once("com/milkManager/domain/valueObjects/LocalizacaoVO.php");

class LocalizacaoDao implements IGenericDao{
		
	public static function save($localizacao){
		$sql = "insert into localizacao (descricao, empresa) " .
				"values (" .
				"'$localizacao->descricao'," .
				"'$localizacao->empresa')";
				
		$id = Database::insert($sql);
		
		if ( $id > 0 ){
			$localizacao->idEntity = $id;
			return $localizacao;
		}
		return null;
	}
	
	public static function update($localizacao){
		$sql = "update localizacao set " .
				"descricao = '$localizacao->descricao', " .
				"empresa = '$localizacao->empresa' where id = $localizacao->idEntity";
		if ( Database::update($sql) ){
			return $localizacao;
		}
		return null; 
	}
	
	public static function remove($localizacao){
		$sql = "delete from localizacao where id = $localizacao->idEntity";
		if ( Database::remove($sql) ){
			return $localizacao;
		}
		return null;
	}
	
	public static function findAll($empresa){
		$sql = "select * from localizacao where empresa = '$empresa' order by descricao ";
		$result = Database::executeQuery($sql);
		return LocalizacaoDao::resultToArray($result);
	}
	
	public static function findByDescricao($descricao, $empresa){
		$sql = "select * from localizacao where empresa = '$empresa' and descricao like '%$descricao%' order by descricao ";
		$result = Database::executeQuery($sql);
		return LocalizacaoDao::resultToArray($result);
	}
	
	public static function findById($id){
		$sql = "select * from localizacao where id = '$id' order by descricao ";
		$result = Database::executeQuery($sql);
		if ( $row = mysql_fetch_array($result) ){
			return LocalizacaoDao::rowToObject($row);
		}
		return null;	
	}
	
	private static function resultToArray($result){
		$list = array();
		while($row = mysql_fetch_array($result)){
			array_push($list, LocalizacaoDao::rowToObject($row));			 
		}
		return $list;
	}
	
	private static function rowToObject($row){
		$localizacao            = new LocalizacaoVO();
		$localizacao->idEntity  = $row['id'];
		$localizacao->descricao = $row['descricao'];
		$localizacao->empresa   = $row['empresa'];
		return $localizacao;
	}
}

?>
