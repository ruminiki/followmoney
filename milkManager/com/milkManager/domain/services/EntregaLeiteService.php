<?php

include_once("com/milkManager/domain/valueObjects/EntregaLeiteVO.php");
include_once("com/milkManager/dao/EntregaLeiteDao.php");

class EntregaLeiteService
{
    public function save($raca){
    	return EntregaLeiteDao::save($raca);
    }
    
    public function update($entregaLeite){
    	return EntregaLeiteDao::update($entregaLeite);
    }
    
    public function remove($entregaLeite){
    	return EntregaLeiteDao::remove($entregaLeite);
    }
    
    public function findAll($empresa){
    	return EntregaLeiteDao::findAll($empresa);
    }
	
	public function findByYearAndMonth($empresa, $year, $month){
    	return EntregaLeiteDao::findByYearAndMonth($empresa, $year, $month);
    }
	
	public function findByYear($empresa, $year){
    	return EntregaLeiteDao::findByYear($empresa, $year);
    }

}
?>