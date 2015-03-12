<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("com/followMoney/dao/IGenericDao.php");
include_once("com/followMoney/dao/Database.php");
include_once("com/followMoney/domain/valueObjects/CartaoCreditoVO.php");

class CartaoCreditoDao implements IGenericDao{
		
	public static function save($cartaoCredito){
		$sql = "insert into cartaoCredito (descricao, usuario, dataFatura, dataFechamento, limite) " .
				"values (" .
				"'$cartaoCredito->descricao'," .
				"'$cartaoCredito->idUsuario'," .
				"'$cartaoCredito->dataFatura'," . 
				"'$cartaoCredito->dataFechamento'," . 
				"'$cartaoCredito->limite')";
		$id = Database::insert($sql);
		if ( $id > 0 ){
			$cartaoCredito->idEntity = $id;
			return $cartaoCredito;
		}
		return $sql;
	}
	
	public static function update($cartaoCredito){
		$sql = "update cartaoCredito set " .
				"descricao = '$cartaoCredito->descricao'," .
				"usuario = '$cartaoCredito->idUsuario'," .
				"dataFatura = '$cartaoCredito->dataFatura'," . 
				"dataFechamento = '$cartaoCredito->dataFechamento'," . 
				"limite = '$cartaoCredito->limite'";
		if ( Database::update($sql) ){
			return $cartaoCredito;
		}
		return null; 
	}
	
	public static function remove($cartaoCredito){
		$sql = "delete from cartaoCredito where id = $cartaoCredito->idEntity and usuario = '$cartaoCredito->idUsuario'";
		if ( Database::remove($sql) ){
			return $cartaoCredito;
		}
		return null;
	}
	
	public static function findAll($usuario){
		$sql = "select * from cartaoCredito where usuario = '$usuario' order by descricao ";
		$result = Database::executeQuery($sql);
		return CartaoCreditoDao::resultToArray($result);
	}
	
	public static function findById($id, $usuario){
		$sql = "select * from cartaoCredito where usuario = '$usuario' and id = '$id' order by descricao ";
		$result = Database::executeQuery($sql);
		if ( $row = mysqli_fetch_array($result) ){
			return CartaoCreditoDao::rowToObject($row);
		}
		return null;	
	}
		
	public static function getDataFechamento($cartao){
		$sql = "select dataFechamento from cartaoCredito where id = '$cartao->idEntity'";
		$result = Database::executeQuery($sql);
		if ( $row = mysqli_fetch_array($result) ){
			return $row['dataFechamento'];
		}
		return null;	
	}
	
	public static function getDataFatura($cartao){
		$sql = "select dataFatura from cartaoCredito where id = '$cartao->idEntity'";
		$result = Database::executeQuery($sql);
		if ( $row = mysqli_fetch_array($result) ){
			return $row['dataFatura'];
		}
		return null;	
	}
	
	private static function resultToArray($result){
		$list = array();
		while($row = mysqli_fetch_array($result)){
			array_push($list, CartaoCreditoDao::rowToObject($row));			 
		}
		return $list;
	}
	
	private static function rowToObject($row){
		$cartaoCredito = new CartaoCreditoVO();
		$cartaoCredito->idEntity       = $row['id'];
		$cartaoCredito->descricao      = $row['descricao'];
		$cartaoCredito->idUsuario      = $row['usuario'];
		$cartaoCredito->dataFatura     = $row['dataFatura'];
		$cartaoCredito->dataFechamento = $row['dataFechamento'];
		$cartaoCredito->limite  	   = $row['limite'];
		return $cartaoCredito;
	}
}

?>
