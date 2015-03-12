<?php

include_once("com/followMoney/dao/FornecedorDao.php");
include_once("com/followMoney/domain/valueObjects/FornecedorVO.php");

class FornecedorService
{
    public function save($fornecedor){
    	return FornecedorDao::save($fornecedor);
    }
    
    public function update($fornecedor){
    	return FornecedorDao::update($fornecedor);
    }
    
    public function remove($fornecedor){
    	return FornecedorDao::remove($fornecedor);
    }
    
    public function findAll($usuario){
    	return FornecedorDao::findAll($usuario);
    }
    
    public function findByDescricao($descricao, $usuario){
    	return FornecedorDao::findByDescricao($descricao, $usuario);
    }
}
?>