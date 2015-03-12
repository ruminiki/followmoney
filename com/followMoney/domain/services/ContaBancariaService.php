<?php

include_once("com/followMoney/dao/ContaBancariaDao.php");
include_once("com/followMoney/domain/valueObjects/ContaBancariaVO.php");
include_once("com/followMoney/dao/MovimentoDao.php");

class ContaBancariaService
{
    public function save($contaBancaria){
    	return ContaBancariaDao::save($contaBancaria); 
    }
    
    public function update($contaBancaria){
    	return ContaBancariaDao::update($contaBancaria);
    }
    
    public function remove($contaBancaria){
    	return ContaBancariaDao::remove($contaBancaria);
    }
    
    public function findAll($usuario){
    	return ContaBancariaDao::findAll($usuario);
    }
    
    public function findByDescricao($descricao, $usuario){
    	return ContaBancariaDao::findByDescricao($descricao, $usuario);
    }
    
    public function findMovimentosByContaBancaria($contaBancaria, $usuario){
    	return MovimentoDao::findByContaBancaria($contaBancaria, $usuario);
    }
    
    public function findMovimentosByRange($contaBancaria, $dataInicial, $dataFinal, $usuario){
    	return MovimentoDao::findMovimentosByCBAndRange($contaBancaria, $dataInicial, $dataFinal, $usuario);
    }
    
    public function calculaSaldoAnterior($contaBancaria, $data, $usuario){
        return MovimentoDao::calculaSaldoAnteriorCB($contaBancaria, $data, $usuario);
    }

    public function estornarTransferencia($movimento, $usuario){
    	if ( $movimento->hashTransferencia != '' && $movimento->hashTransferencia != null ){
    		return MovimentoDao::removeTransferencia($movimento, $usuario);	
    	}
    }
	
	public function inativarContaBancaria($contaBancaria){
		$contaBancaria->situacao = 'INATIVO';
		$contaBancaria->dataInativacao = date("Ymd");
    	return ContaBancariaDao::update($contaBancaria);	
    }
	
	public function reativarContaBancaria($contaBancaria){
		$contaBancaria->situacao = 'ATIVO';
		$contaBancaria->dataInativacao = '';
    	return ContaBancariaDao::update($contaBancaria);	
    }
    
    public function transferirValor($contaOrigem, $contaDestino, $valor, $emissao, $vencimento, $finalidade, $usuario){
    	$hashTransferencia = md5(date("F j, Y, g:i a"));
    	try{
			//salva o d�bito na conta original
			$debito                    = new MovimentoVO();
			$debito->idUsuario         = $usuario;
			$debito->descricao         = 'DÉBITO TRANSFERÊNCIA (' .$contaDestino->descricao. ')';
			$debito->operacao          = 'DEBITO';				
			$debito->status            = 'PAGO';
			$debito->emissao           = $emissao;
			$debito->valor             = $valor;
			$debito->vencimento        = $vencimento;
			$debito->finalidade        = $finalidade;
			$debito->contaBancaria     = $contaOrigem;
			$debito->fornecedor        = null;
			$debito->hashTransferencia = $hashTransferencia; 
			MovimentoDao::save($debito);
			//salva o cr�dito na conta destino
			$credito                    = new MovimentoVO();
			$credito->idUsuario         = $usuario;
			$credito->descricao         = 'CRÉDITO TRANSFERÊNCIA (' .$contaOrigem->descricao. ')';
			$credito->operacao          = 'CREDITO';				
			$credito->status            = 'PAGO';
			$credito->emissao           = $emissao;
			$credito->valor             = $valor;
			$credito->vencimento        = $vencimento;
			$credito->finalidade        = $finalidade;
			$credito->fornecedor        = null;
			$credito->contaBancaria     = $contaDestino;
			$credito->hashTransferencia = $hashTransferencia;
			MovimentoDao::save($credito);
			return $debito;
		}catch(Exception $e){
			throw new Exception('Ocorreu um erro ao realizar a transferência: ' . $e->getMessage());
		}
    }  

    public function resumeByYear($contaBancaria, $year, $usuario){
    	return ContaBancariaDao::resumeByYear($contaBancaria, $year, $usuario);
    }
    
    
}
?>