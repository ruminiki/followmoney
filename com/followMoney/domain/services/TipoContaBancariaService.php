<?php

include_once("dao/TipoContaBancariaDao.php");
include_once("valueObjects/TipoContaBancariaVO.php");

class TipoContaBancariaService
{
    public function save(TipoContaBancariaVO $tipoContaBancaria){
    	return TipoContaBancariaDao::save($tipoContaBancaria);
    }
    
    public function update(TipoContaBancariaVO $tipoContaBancaria){
    	return TipoContaBancariaDao::update($tipoContaBancaria);
    }
    
    public function remove(TipoContaBancariaVO $tipoContaBancaria){
    	return TipoContaBancariaDao::remove($tipoContaBancaria);
    }
    
    public function findAll($usuario){
    	return TipoContaBancariaDao::findAll($usuario);
    }
    
    public function findByDescricao($descricao, $usuario){
    	return TipoContaBancariaDao::findByDescricao($descricao, $usuario);
    }
}
?>