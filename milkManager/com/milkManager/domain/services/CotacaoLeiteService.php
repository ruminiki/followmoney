<?php

include_once("com/milkManager/domain/valueObjects/CotacaoLeiteVO.php");
include_once("com/milkManager/dao/CotacaoLeiteDao.php");

class CotacaoLeiteService
{
    public function save($cotacaoLeite){
    	return CotacaoLeiteDao::save($cotacaoLeite);
    }
    
    public function update($cotacaoLeite){
    	return CotacaoLeiteDao::update($cotacaoLeite);
    }
    
    public function remove($cotacaoLeite){
    	return CotacaoLeiteDao::remove($cotacaoLeite);
    }
    
    public function findAll($empresa){
    	return CotacaoLeiteDao::findAll($empresa);
    }
    
    public function findByYear($year, $empresa){
    	return CotacaoLeiteDao::findByYear($year, $empresa);
    }
	
	public function findByMes($mes, $empresa){
    	return CotacaoLeiteDao::findByMes($mes, $empresa);
    }
}
?>