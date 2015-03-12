<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("com/followMoney/dao/IGenericDao.php");
include_once("com/followMoney/dao/Database.php");

class UsuarioDao implements IGenericDao{
		
	public static function save($usuario){
		$sql = "insert into usuario (login, nome, senha, email, codigoAcesso) values ('$usuario->login', '$usuario->nome', '$usuario->senha', '$usuario->email', '$usuario->codigoAcesso')";
		$id = Database::insert($sql);
		if ( $id > 0 ){
			$usuario->idEntity = $id;
			return $usuario;
		}
		throw ErrorException::__construct('Erro ao salvar usuário.');
	}
	
	public static function update($usuario){
		$sql = "update usuario set " .
				"	login ='$usuario->login', " .
				"	nome ='$usuario->nome', " .
				"	email ='$usuario->email', " .
				"	senha ='$usuario->senha', " .
				"	codigoAcesso ='$usuario->codigoAcesso', " .
				"	ultimoAcesso = '$usuario->ultimoAcesso' " .
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
		$sql = "select * from usuario where (login = '$login' or email = '$login')";
		$result = Database::executeQuery($sql);
		if ( $row = mysqli_fetch_array($result) ){
			$usuario = new UsuarioVO();
			$usuario->idEntity     = $row['id'];
			$usuario->nome         = $row['nome'];
			$usuario->senha        = $row['senha'];
			$usuario->email        = $row['email'];
			$usuario->login        = $row['login'];
			$usuario->ultimoAcesso = $row['ultimoAcesso'];
			$usuario->codigoAcesso = $row['codigoAcesso'];
			return $usuario;			 
		}
		return null;
	}
	
	public static function findAll($empresa){
		return null;
	}
	
}

?>
