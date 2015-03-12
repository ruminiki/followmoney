<?php

include_once("dao/AgenciaDao.php");
include_once("valueObjects/AgenciaVO.php");

class AgenciaService
{
    public function save(AgenciaVO $agencia){
    	return AgenciaDao::save($agencia);
    }
    
    public function update(AgenciaVO $agencia){
    	return AgenciaDao::update($agencia);
    }
    
    public function remove(AgenciaVO $agencia){
    	return AgenciaDao::remove($agencia);
    }
    
    public function findAll($usuario){
    	return AgenciaDao::findAll($usuario);
    }
    
    public function findByDescricao($descricao, $usuario){
    	return AgenciaDao::findByDescricao($descricao, $usuario);
    }
}
?>