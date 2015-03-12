<?php

include_once("com/milkManager/domain/valueObjects/LocalizacaoVO.php");
include_once("com/milkManager/dao/LocalizacaoDao.php");

class LocalizacaoService
{
    public function save($localizacao){
    	return LocalizacaoDao::save($localizacao);
    }
    
    public function update($localizacao){
    	return LocalizacaoDao::update($localizacao);
    }
    
    public function remove($localizacao){
    	return LocalizacaoDao::remove($localizacao);
    }
    
    public function findAll($empresa){
    	return LocalizacaoDao::findAll($empresa);
    }
    
    public function findByDescricao($descricao, $empresa){
    	return LocalizacaoDao::findByDescricao($descricao, $empresa);
    }
}
?>