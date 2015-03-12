<?php

include_once("com/milkManager/domain/valueObjects/AcompanhamentoVO.php");
include_once("com/milkManager/dao/AcompanhamentoDao.php");

class AcompanhamentoService
{
    public function save($acompanhamento){
    	return AcompanhamentoDao::save($acompanhamento);
    }
    
    public function update($acompanhamento){
    	return AcompanhamentoDao::update($acompanhamento);
    }
    
    public function remove($acompanhamento){
    	return AcompanhamentoDao::remove($acompanhamento);
    }
    
    public function findAll($empresa){
    	return AcompanhamentoDao::findAll($empresa);
    }
    
    public function findByDescricao($descricao, $empresa){
    	return AcompanhamentoDao::findByDescricao($descricao, $empresa);
    }
	
	 public function findByAnimal($animal){
    	return AcompanhamentoDao::findByAnimal($animal);
    }
	
	public static function findByParam($param, $empresa){
		return AcompanhamentoDao::findByParam($param, $empresa);
	}
}
?>