<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once "IGenericDao.php";
include_once("Database.php");

class BancoDao implements IGenericDao{
		
	public static function save($banco){
		$sql = "insert into banco (descricao, usuario, codigo) values ('$banco->descricao', '$banco->idUsuario', '$banco->codigo')";
		try{
			$id = Database::insert($sql);
			if ( $id > 0 ){
				$banco->idEntity = $id;
				return $banco;
			}	
		}catch(Exception $e){
			//throw new Exception('Não foi possível salvar o banco selecionado.');
			throw $e;
		}
	}
	
	public static function update($banco){
		$sql = "update banco set descricao = '$banco->descricao ' " .
				"where id = $banco->idEntity and usuario ='$banco->idUsuario'";
		try{
			Database::update($sql);
			return $banco;	
		}catch(Exception $e){
			throw new Exception('Não foi possível alterar o banco selecionado.');
		}
	}
	
	public static function remove($banco){
		$sql = "delete from banco where id = $banco->idEntity and usuario = '$banco->idUsuario'";
		try{
			Database::remove($sql);
			return $banco;	
		}catch(Exception $e){
			throw new Exception('Não é possivel remover o banco selecionado.');
		}
	}
	
	public static function findAll($usuario){
		$sql = "select * from banco where usuario = '$usuario' order by descricao ";
		$result = Database::executeQuery($sql);
		return BancoDao::resultToArray($result);
	}
	
	public static function findByDescricao($descricao, $usuario){
		$sql = "select * from banco where usuario = '$usuario' and descricao like '%$descricao%' order by descricao ";
		$result = Database::executeQuery($sql);
		return BancoDao::resultToArray($result);
	}

	public static function findById($id, $usuario){
		$sql = "select * from banco where usuario = '$usuario' and id = '$id' order by descricao ";
		$result = Database::executeQuery($sql);
		if($row = mysqli_fetch_array($result)){
			return BancoDao::rowToObject($row);			 
		}
		return null;
	}

	private static function resultToArray($result){
		$list = array();
		while($row = mysqli_fetch_array($result)){
			array_push($list, BancoDao::rowToObject($row));			 
		}
		return $list;
	}
	
	private static function rowToObject($row){
		$banco = new BancoVO();
		$banco->idEntity  = $row['id'];
		$banco->descricao = $row['descricao'];
		$banco->idUsuario = $row['usuario'];
		$banco->codigo    = $row['codigo'];
		return $banco;
	}
}

?>
