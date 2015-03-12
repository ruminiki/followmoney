<?php

include_once("dao/BancoDao.php");
include_once("valueObjects/BancoVO.php");

class BancoService
{
    public function save(BancoVO $banco){
    	return BancoDao::save($banco);
    }
    
    public function update(BancoVO $banco){
    	return BancoDao::update($banco);
    }
    
    public function remove(BancoVO $banco){
    	return BancoDao::remove($banco);
    }
    
    public function findAll($usuario){
    	return BancoDao::findAll($usuario);
    }
    
    public function findByDescricao($descricao, $usuario){
    	return BancoDao::findByDescricao($descricao, $usuario);
    }
}
?>