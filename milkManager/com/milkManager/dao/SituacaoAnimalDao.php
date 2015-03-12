<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("com/milkManager/dao/IGenericDao.php");
include_once("com/milkManager/dao/Database.php");
include_once("com/milkManager/domain/valueObjects/SituacaoAnimalVO.php");

class SituacaoAnimalDao implements IGenericDao{
		
	public static function save($situacaoAnimal){
		$sql = "insert into situacaoAnimal (descricao, empresa) " .
				"values (" .
				"'$situacaoAnimal->descricao'," .
				"'$situacaoAnimal->empresa')";
				
		$id = Database::insert($sql);
		
		if ( $id > 0 ){
			$situacaoAnimal->idEntity = $id;
			return $situacaoAnimal;
		}
		return null;
	}
	
	public static function update($situacaoAnimal){
		$sql = "update situacaoAnimal set " .
				"descricao = '$situacaoAnimal->descricao', " .
				"empresa = '$situacaoAnimal->empresa' where id = $situacaoAnimal->idEntity";
		if ( Database::update($sql) ){
			return $situacaoAnimal;
		}
		return null; 
	}
	
	public static function remove($situacaoAnimal){
		$sql = "delete from situacaoAnimal where id = $situacaoAnimal->idEntity";
		if ( Database::remove($sql) ){
			return $situacaoAnimal;
		}
		return null;
	}
	
	public static function findAll($empresa){
		$sql = "select * from situacaoAnimal where empresa = '$empresa' order by descricao ";
		$result = Database::executeQuery($sql);
		return SituacaoAnimalDao::resultToArray($result);
	}
	
	public static function findByDescricao($descricao, $empresa){
		$sql = "select * from situacaoAnimal where empresa = '$empresa' and descricao like '%$descricao%' order by descricao ";
		$result = Database::executeQuery($sql);
		return SituacaoAnimalDao::resultToArray($result);
	}
	
	public static function findById($id){
		$sql = "select * from situacaoAnimal where id = '$id' order by descricao ";
		$result = Database::executeQuery($sql);
		if ( $row = mysql_fetch_array($result) ){
			return SituacaoAnimalDao::rowToObject($row);
		}
		return null;	
	}
	
	private static function resultToArray($result){
		$list = array();
		while($row = mysql_fetch_array($result)){
			array_push($list, SituacaoAnimalDao::rowToObject($row));			 
		}
		return $list;
	}
	
	private static function rowToObject($row){
		$situacaoAnimal            = new SituacaoAnimalVO();
		$situacaoAnimal->idEntity  = $row['id'];
		$situacaoAnimal->descricao = $row['descricao'];
		$situacaoAnimal->empresa   = $row['empresa'];
		return $situacaoAnimal;
	}
}

?>
