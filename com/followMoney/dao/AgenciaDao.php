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

class AgenciaDao implements IGenericDao{
		
	public static function save($agencia){
		$sql = "insert into agencia (descricao, usuario, endereco, telefone, site, email, banco, numero, digito) " .
				"values (" .
				"'$agencia->descricao'," .
				"'$agencia->idUsuario'," .
				"'$agencia->endereco'," .
				"'$agencia->telefone'," .
				"'$agencia->site'," .
				"'$agencia->email'," . 
				$agencia->banco->idEntity . "," . 
				"'$agencia->numero', " .
				"'$agencia->digito')";
		$id = Database::insert($sql);
		if ( $id > 0 ){
			$agencia->idEntity = $id;
			return $agencia;
		}
		return null;
	}
	
	public static function update($agencia){
		$sql = "update agencia set " .
				"descricao = '$agencia->descricao', " .
				"usuario = '$agencia->idUsuario', " .
				"endereco = '$agencia->endereco', " .
				"telefone = '$agencia->telefone', " .
				"site = '$agencia->site', " .
				"email = '$agencia->email', " .
				"banco = " . $agencia->banco->idEntity . "," . 
				"numero = '$agencia->numero', " .
				"digito = '$agencia->digito' " .
				"where id = $agencia->idEntity and usuario ='$agencia->idUsuario'";
		if ( Database::update($sql) ){
			return $agencia;
		}
		return null; 
	}
	
	public static function remove($agencia){
		$sql = "delete from agencia where id = $agencia->idEntity and usuario = '$agencia->idUsuario'";
		if ( Database::remove($sql) ){
			return $agencia;
		}
		return null;
	}
	
	public static function findAll($usuario){
		$sql = "select * from agencia where usuario = '$usuario' order by descricao ";
		$result = Database::executeQuery($sql);
		return AgenciaDao::resultToArray($result);
	}
	
	public static function findByDescricao($descricao, $usuario){
		$sql = "select * from agencia where usuario = '$usuario' and descricao like '%$descricao%' order by descricao ";
		$result = Database::executeQuery($sql);
		return AgenciaDao::resultToArray($result);
	}
	
	public static function findById($id, $usuario){
		$sql = "select * from agencia where usuario = '$usuario' and id = '$id' order by descricao ";
		$result = Database::executeQuery($sql);
		if ( $row = mysqli_fetch_array($result) ){
			return AgenciaDao::rowToObject($row);
		}
		return null;	
	}
	
	private static function resultToArray($result){
		$list = array();
		while($row = mysqli_fetch_array($result)){
			array_push($list, AgenciaDao::rowToObject($row));			 
		}
		return $list;
	}
	
	private static function rowToObject($row){
		$agencia = new AgenciaVO();
		$agencia->idEntity  = $row['id'];
		$agencia->descricao = $row['descricao'];
		$agencia->idUsuario = $row['usuario'];
		$agencia->endereco  = $row['endereco'];
		$agencia->telefone  = $row['telefone'];
		$agencia->site      = $row['site'];
		$agencia->email     = $row['email'];
		$agencia->numero    = $row['numero'];
		$agencia->digito    = $row['digito'];
		$agencia->banco     = BancoDao::findById($row['banco'], $row['usuario']);
		return $agencia;
	}
}

?>
