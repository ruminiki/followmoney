<?php

include_once("com/milkManager/domain/valueObjects/SituacaoAnimalVO.php");
include_once("com/milkManager/dao/SituacaoAnimalDao.php");

class SituacaoAnimalService
{
    public function save($situacaoAnimal){
    	return SituacaoAnimalDao::save($situacaoAnimal);
    }
    
    public function update($situacaoAnimal){
    	return SituacaoAnimalDao::update($situacaoAnimal);
    }
    
    public function remove($situacaoAnimal){
    	return SituacaoAnimalDao::remove($situacaoAnimal);
    }
    
    public function findAll($empresa){
    	return SituacaoAnimalDao::findAll($empresa);
    }
    
    public function findByDescricao($descricao, $empresa){
    	return SituacaoAnimalDao::findByDescricao($descricao, $empresa);
    }
}
?>