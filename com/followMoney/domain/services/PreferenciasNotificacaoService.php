<?php

include_once("com/followMoney/dao/PreferenciasNotificacaoDao.php");

class PreferenciasNotificacaoService
{
    public function save($preferenciasNotificacao){
    	return PreferenciasNotificacaoDao::save($preferenciasNotificacao);
    }
    
    public function update($preferenciasNotificacao){
    	return PreferenciasNotificacaoDao::update($preferenciasNotificacao);
    }
    
    public function remove($preferenciasNotificacao){
    	return PreferenciasNotificacaoDao::remove($preferenciasNotificacao);
    }
    
    public function findAll($usuario){
    	return PreferenciasNotificacaoDao::findAll($usuario);
    }
    
    public function findByDescricao($descricao, $usuario){
    	return PreferenciasNotificacaoDao::findByDescricao($descricao, $usuario);
    }
}
?>