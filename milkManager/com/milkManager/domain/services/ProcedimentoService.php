<?php

include_once("com/milkManager/domain/valueObjects/ProcedimentoVO.php");
include_once("com/milkManager/dao/ProcedimentoDao.php");

class ProcedimentoService
{
    public function save($procedimento){
    	return ProcedimentoDao::save($procedimento);
    }
    
    public function update($procedimento){
    	return ProcedimentoDao::update($procedimento);
    }
    
    public function remove($procedimento){
    	return ProcedimentoDao::remove($procedimento);
    }
    
    public function findAll($empresa){
    	return ProcedimentoDao::findAll($empresa);
    }
    
    public function findByDescricao($descricao, $empresa){
    	return ProcedimentoDao::findByDescricao($descricao, $empresa);
    }
}
?>