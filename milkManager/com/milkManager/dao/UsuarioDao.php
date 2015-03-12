<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("com/milkManager/dao/IGenericDao.php");
include_once("com/milkManager/dao/Database.php");

class UsuarioDao implements IGenericDao{
		
	public static function save($usuario){
		$sql = "insert into usuario (senha, email, empresa) values ('$usuario->senha', '$usuario->email', '$usuario->empresa')";
		$id = Database::insert($sql);
		if ( $id > 0 ){
			$usuario->idEntity = $id;
			return $usuario;
		}
		throw ErrorException::__construct('Erro ao salvar usuário.');
	}
	
	public static function update($usuario){
		$sql = "update usuario set " .
				"	email ='$usuario->email', " .
				"	senha ='$usuario->senha', " .
				"	empresa ='$usuario->empresa' " .
				"where id = $usuario->idEntity";
		if ( Database::update($sql) ){
			return $usuario;
		}
		return null; 
	}
	
	public static function remove($usuario){
		$sql = "delete from usuario where id = $usuario->idEntity";
		if ( Database::remove($sql) ){
			return $usuario;
		}
		return null;
	}
	
	public static function findByLogin($login){
		$sql = "select * from usuario where (email = '$login')";
		$result = Database::executeQuery($sql);
		if ( $row = mysql_fetch_array($result) ){
			$usuario = new UsuarioVO();
			$usuario->idEntity     = $row['id'];
			$usuario->senha        = $row['senha'];
			$usuario->email        = $row['email'];
			$usuario->empresa      = $row['empresa'];
			return $usuario;			 
		}
		return null;
	}
	
	public static function findAll($empresa){
		return null;
	}
	
}

?>
