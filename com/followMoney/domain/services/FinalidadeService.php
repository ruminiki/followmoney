<?php

include_once("com/followMoney/dao/FinalidadeDao.php");
include_once("com/followMoney/dao/MovimentoDao.php");

class FinalidadeService
{
    public function save($finalidade){
    	return FinalidadeDao::save($finalidade);
    }
    
    public function update($finalidade){
    	return FinalidadeDao::update($finalidade);
    }
    
    public function remove($finalidade){
    	return FinalidadeDao::remove($finalidade);
    }
    
    public function findAll($usuario){
    	return FinalidadeDao::findAll($usuario);
    }
    
    public function findByDescricao($descricao, $usuario){
    	return FinalidadeDao::findByDescricao($descricao, $usuario);
    }
    
    public function findMovimentosByFinalidade($finalidade, $usuario){
    	return MovimentoDao::findByFinalidade($finalidade, $usuario);
    }
    
	public function resumeByYear($finalidade, $year, $usuario){
    	return FinalidadeDao::resumeByYear($finalidade, $year, $usuario);
    }
	
	public function findSaldosByMonth($month, $year, $usuario, $operacao){
		return FinalidadeDao::findSaldosByMonth($month, $year, $usuario, $operacao);
	}
	
	public function findSaldoAnalitico($month, $year, $usuario){
		return FinalidadeDao::findSaldoAnalitico($month, $year, $usuario);
	}
    
    public function findMovimentosByRange($finalidade, $dataInicial, $dataFinal, $usuario){
    	return MovimentoDao::findMovimentosByFinalidadeAndRange($finalidade, $dataInicial, $dataFinal, $usuario);
    }
    
}
?>