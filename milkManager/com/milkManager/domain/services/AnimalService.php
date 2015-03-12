<?php

include_once("com/milkManager/domain/valueObjects/AnimalVO.php");
include_once("com/milkManager/dao/AnimalDao.php");

class AnimalService
{
    public function save($animal){
    	return AnimalDao::save($animal);
    }
    
    public function update($animal){
    	return AnimalDao::update($animal);
    }
    
    public function remove($animal){
    	return AnimalDao::remove($animal);
    }
    
    public function findAll($empresa){
    	return AnimalDao::findAll($empresa);
    }
    
    public function findByDescricao($descricao, $empresa){
    	return AnimalDao::findByDescricao($descricao, $empresa);
    }
	
	public function findBySexo($sexo, $empresa){
    	return AnimalDao::findBySexo($sexo, $empresa);
    }
	
	public function findByParam($param, $empresa){
    	return AnimalDao::findByParam($param, $empresa);
    }
	
}
?>