<?php
include_once("com/followMoney/dao/FormaPagamentoDao.php");
include_once("com/followMoney/domain/valueObjects/FormaPagamentoVO.php");

class FormaPagamentoService
{
    public function save($formaPagamento){
    	return FormaPagamentoDao::save($formaPagamento);
    }
    
    public function update($formaPagamento){
    	return FormaPagamentoDao::update($formaPagamento);
    }
    
    public function remove($formaPagamento){
    	return FormaPagamentoDao::remove($formaPagamento);
    }
    
    public function findAll($usuario){
    	return FormaPagamentoDao::findAll($usuario);
    }
	
}
?>