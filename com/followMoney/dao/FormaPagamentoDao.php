<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("com/followMoney/dao/IGenericDao.php");
include_once("com/followMoney/dao/Database.php");
include_once("com/followMoney/domain/valueObjects/FormaPagamentoVO.php");

class FormaPagamentoDao implements IGenericDao{
		
	public static function save($formaPagamento){
		$sql = "insert into formaPagamento (descricao, usuario, sigla) " .
				"values (" .
				"'$formaPagamento->descricao'," .
				"'$formaPagamento->idUsuario'," .
				"'$formaPagamento->sigla')";
		$id = Database::insert($sql);
		if ( $id > 0 ){
			$formaPagamento->idEntity = $id;
			return $formaPagamento;
		}
		return $sql;
	}
	
	public static function update($formaPagamento){
		$sql = "update formaPagamento set " .
				"descricao = '$formaPagamento->descricao'," .
				"usuario = '$formaPagamento->idUsuario'," .
				"sigla = '$formaPagamento->sigla' where id = $formaPagamento->idEntity";
		if ( Database::update($sql) ){
			return $formaPagamento;
		}
		return null; 
	}
	
	public static function remove($formaPagamento){
		$sql = "delete from formaPagamento where id = $formaPagamento->idEntity and usuario = '$formaPagamento->idUsuario'";
		if ( Database::remove($sql) ){
			return $formaPagamento;
		}
		return null;
	}
	
	public static function findAll($usuario){
		$sql = "select * from formaPagamento where usuario = '$usuario' order by descricao ";
		$result = Database::executeQuery($sql);
		return FormaPagamentoDao::resultToArray($result);
	}
	
	public static function findById($id, $usuario){
		$sql = "select * from formaPagamento where usuario = '$usuario' and id = '$id' order by descricao ";
		$result = Database::executeQuery($sql);
		if ( $row = mysqli_fetch_array($result) ){
			return FormaPagamentoDao::rowToObject($row);
		}
		return null;	
	}
	
	private static function resultToArray($result){
		$list = array();
		while($row = mysqli_fetch_array($result)){
			array_push($list, FormaPagamentoDao::rowToObject($row));			 
		}
		return $list;
	}
	
	private static function rowToObject($row){
		$formaPagamento = new FormaPagamentoVO();
		$formaPagamento->idEntity       = $row['id'];
		$formaPagamento->descricao      = $row['descricao'];
		$formaPagamento->idUsuario      = $row['usuario'];
		$formaPagamento->sigla 		    = $row['sigla'];
		return $formaPagamento;
	}
}

?>
