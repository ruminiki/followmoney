<?php

include_once("com/milkManager/domain/valueObjects/PartoVO.php");
include_once("com/milkManager/dao/PartoDao.php");

class PartoService
{
    public function save($parto){
    	return PartoDao::save($parto);
    }
    
    public function update($parto){
    	return PartoDao::update($parto);
    }
    
    public function remove($parto){
    	return PartoDao::remove($parto);
    }
    
    public function findAll($empresa){
    	return PartoDao::findAll($empresa);
    }

    public static function findByParam($param, $empresa){
		return PartoDao::findByParam($param, $empresa);
	}
	
}
?>