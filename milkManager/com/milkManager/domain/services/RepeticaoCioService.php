<?php

include_once("com/milkManager/domain/valueObjects/RepeticaoCioVO.php");
include_once("com/milkManager/dao/RepeticaoCioDao.php");

class RepeticaoCioService
{
    public function save($repeticaoCio){
    	return RepeticaoCioDao::save($repeticaoCio);
    }
    
    public function update($repeticaoCio){
    	return RepeticaoCioDao::update($repeticaoCio);
    }
    
    public function remove($repeticaoCio){
    	return RepeticaoCioDao::remove($repeticaoCio);
    }
    
    public function findAll($empresa){
    	return RepeticaoCioDao::findAll($empresa);
    }
    
    public function registrarRepeticao($enseminacao, $data){
    	return RepeticaoCioDao::registrarRepeticao($enseminacao, $data);
    }
	
	public static function findByParam($param, $empresa){
		return RepeticaoCioDao::findByParam($param, $empresa);
	}

	
}
?>