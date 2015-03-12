<?php

include_once("com/milkManager/dao/UsuarioDao.php");
include_once("com/milkManager/domain/valueObjects/UsuarioVO.php");
include_once("com/milkManager/application/util/Email.php");
include_once("com/milkManager/application/util/SystemLog.php");

class UsuarioService
{
    public function save($usuario){
    	$usuario = UsuarioDao::save($usuario);
		if ( $usuario->idEntity > 0 ){
    		Email::sendMailAccountCreated($usuario->email, $usuario->nome);
    	}
		
	   	return $usuario;
    }
    
    public function update($usuario){
    	return UsuarioDao::update($usuario);
    }
    
    public function remove($usuario){
    	return UsuarioDao::remove($usuario);
    }
    
    public function findAll(){
    	return UsuarioDao::findAll();
    }
    
    public function login($login, $senha){
		
		SystemLog::writeLog($senha);
	
    	$usuario = UsuarioDao::findByLogin($login);
    	if ( $usuario != null ){
    		if ( $usuario->senha == $senha ){
				return $usuario;
			}else{
    			throw new Exception('Senha incorreta. Tente novamente.');
    		}
    	}
    	throw new Exception('Usuário não encontrado.');
    }
}
?>
