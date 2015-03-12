<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once "IGenericDao.php";
include_once("Database.php");

class TipoContaBancariaDao implements IGenericDao{
		
	public static function save($tipoContaBancaria){
		$sql = "insert into tipoContaBancaria (descricao, usuario) values ('$tipoContaBancaria->descricao', '$tipoContaBancaria->idUsuario')";
		$id = Database::insert($sql);
		if ( $id > 0 ){
			$tipoContaBancaria->idEntity = $id;
			return $tipoContaBancaria;
		}
		return null;
	}
	
	public static function update($tipoContaBancaria){
		$sql = "update tipoContaBancaria set descricao = '$tipoContaBancaria->descricao ' where id = $tipoContaBancaria->idEntity and usuario ='$tipoContaBancaria->idUsuario'";
		if ( Database::update($sql) ){
			return $tipoContaBancaria;
		}
		return null; 
	}
	
	public static function remove($tipoContaBancaria){
		$sql = "delete from tipoContaBancaria where id = $tipoContaBancaria->idEntity and usuario = '$tipoContaBancaria->idUsuario'";
		if ( Database::remove($sql) ){
			return $tipoContaBancaria;
		}
		return null;
	}
	
	public static function findAll($usuario){
		$sql = "select * from tipoContaBancaria where usuario = '$usuario' order by descricao ";
		$result = Database::executeQuery($sql);
		return TipoContaBancariaDao::resultToArray($result);
	}
	
	public static function findByDescricao($descricao, $usuario){
		$sql = "select * from tipoContaBancaria where usuario = '$usuario' and descricao like '%$descricao%' order by descricao ";
		$result = Database::executeQuery($sql);
		return TipoContaBancariaDao::resultToArray($result);
	}
	
	public static function findById($id, $usuario){
		$sql = "select * from tipoContaBancaria where usuario = '$usuario' and id = '$id' order by descricao ";
		$tipoContaBancaria = TipoContaBancariaDao::resultToArray(Database::executeQuery($sql));
		return $tipoContaBancaria[0];
	}
	
	private static function resultToArray($result){
		$list = array();
		while($row = mysqli_fetch_array($result))
		{
			$tipoContaBancaria = new TipoContaBancariaVO();
			$tipoContaBancaria->idEntity  = $row['id'];
			$tipoContaBancaria->descricao = $row['descricao'];
			$tipoContaBancaria->idUsuario = $row['usuario'];
			array_push($list, $tipoContaBancaria);			 
		}
		return $list;
	}
	
}

?>
