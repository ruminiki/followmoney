<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once "IGenericDao.php";
include_once("Database.php");
include_once("BancoDao.php");

class FornecedorDao implements IGenericDao{
		
	public static function save($fornecedor){
		$sql = "insert into fornecedor (descricao, usuario, endereco, telefone, site, email, numeroDocumento) " .
				"values (" .
				"'$fornecedor->descricao'," .
				"'$fornecedor->idUsuario'," .
				"'$fornecedor->endereco'," .
				"'$fornecedor->telefone'," .
				"'$fornecedor->site'," .
				"'$fornecedor->email'," . 
				"'$fornecedor->numeroDocumento')";
		$id = Database::insert($sql);
		if ( $id > 0 ){
			$fornecedor->idEntity = $id;
			return $fornecedor;
		}
		return null;
	}
	
	public static function update($fornecedor){
		$sql = "update fornecedor set " .
				"descricao = '$fornecedor->descricao', " .
				"usuario = '$fornecedor->idUsuario', " .
				"endereco = '$fornecedor->endereco', " .
				"telefone = '$fornecedor->telefone', " .
				"site = '$fornecedor->site', " .
				"email = '$fornecedor->email', " .
				"numeroDocumento = '$fornecedor->numeroDocumento' where id = $fornecedor->idEntity and usuario ='$fornecedor->idUsuario'";
		if ( Database::update($sql) ){
			return $fornecedor;
		}
		return null; 
	}
	
	public static function remove($fornecedor){
		$sql = "delete from fornecedor where id = $fornecedor->idEntity and usuario = '$fornecedor->idUsuario'";
		if ( Database::remove($sql) ){
			return $fornecedor;
		}
		return null;
	}
	
	public static function findAll($usuario){
		$sql = "select * from fornecedor where usuario = '$usuario' order by descricao ";
		$result = Database::executeQuery($sql);
		return FornecedorDao::resultToArray($result);
	}
	
	public static function findByDescricao($descricao, $usuario){
		$sql = "select * from fornecedor where usuario = '$usuario' and descricao like '%$descricao%' order by descricao ";
		$result = Database::executeQuery($sql);
		return FornecedorDao::resultToArray($result);
	}
	
	public static function findById($id, $usuario){
		$sql = "select * from fornecedor where usuario = '$usuario' and id = '$id' order by descricao ";
		$result = Database::executeQuery($sql);
		if ( $row = mysqli_fetch_array($result) ){
			return FornecedorDao::rowToObject($row);
		}
		return null;	
	}
	
	private static function resultToArray($result){
		$list = array();
		while($row = mysqli_fetch_array($result)){
			array_push($list, FornecedorDao::rowToObject($row));			 
		}
		return $list;
	}
	
	private static function rowToObject($row){
		$fornecedor = new FornecedorVO();
		$fornecedor->idEntity  		 = $row['id'];
		$fornecedor->descricao 		 = $row['descricao'];
		$fornecedor->idUsuario 		 = $row['usuario'];
		$fornecedor->endereco  		 = $row['endereco'];
		$fornecedor->telefone  		 = $row['telefone'];
		$fornecedor->site      		 = $row['site'];
		$fornecedor->email           = $row['email'];
		$fornecedor->numeroDocumento = $row['numeroDocumento'];
		return $fornecedor;
	}
}

?>
