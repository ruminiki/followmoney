<?php

include_once("com/milkManager/domain/valueObjects/RacaVO.php");
include_once("com/milkManager/dao/RacaDao.php");

class RacaService
{
    public function save($raca){
    	return RacaDao::save($raca);
    }
    
    public function update($raca){
    	return RacaDao::update($raca);
    }
    
    public function remove($raca){
    	return RacaDao::remove($raca);
    }
    
    public function findAll($empresa){
    	return RacaDao::findAll($empresa);
    }
    
    public function findByDescricao($descricao, $empresa){
    	return RacaDao::findByDescricao($descricao, $empresa);
    }
}
?>