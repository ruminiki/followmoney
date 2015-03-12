<?php

include_once("com/milkManager/domain/valueObjects/EnseminacaoVO.php");
include_once("com/milkManager/dao/EnseminacaoDao.php");

class EnseminacaoService
{
    public function save($enseminacao){
    	return EnseminacaoDao::save($enseminacao);
    }
    
    public function update($enseminacao){
    	return EnseminacaoDao::update($enseminacao);
    }
    
    public function remove($enseminacao){
    	return EnseminacaoDao::remove($enseminacao);
    }
    
    public function findAll($empresa){
    	return EnseminacaoDao::findAll($empresa);
    }
    
    public function findByFemea($femea){
    	return EnseminacaoDao::findByFemea($femea);
    }
	
	public static function findByParam($param, $empresa){
		return EnseminacaoDao::findByParam($param, $empresa);
	}
	
}
?>