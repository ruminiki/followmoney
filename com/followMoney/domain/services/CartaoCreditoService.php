<?php
include_once("com/followMoney/dao/CartaoCreditoDao.php");
include_once("com/followMoney/dao/FaturaDao.php");
include_once("com/followMoney/domain/valueObjects/CartaoCreditoVO.php");

class CartaoCreditoService
{
    public function save($cartaoCredito){
    	return CartaoCreditoDao::save($cartaoCredito);
    }
    
    public function update($cartaoCredito){
    	return CartaoCreditoDao::update($cartaoCredito);
    }
    
    public function remove($cartaoCredito){
    	return CartaoCreditoDao::remove($cartaoCredito);
    }
    
    public function findAll($usuario){
    	return CartaoCreditoDao::findAll($usuario);
    }
	
	public function gerarFatura($fatura, $movimentos){
		return FaturaDao::gerarFatura($fatura, $movimentos);
	}
	
	public function findFaturaByCartao($cartao, $mesReferencia){
		return FaturaDao::findFaturaByCartao($cartao, $mesReferencia);
	}
	
	public function removerFaturaCartao($fatura){
		return FaturaDao::removerFaturaCartao($fatura);
	}
	
	public function pagarFaturaCartao($fatura, $finalidadePagamento){
		return FaturaDao::pagarFaturaCartao($fatura, $finalidadePagamento);
	}
	
	public function cancelarPagamentoFatura($fatura){
		return FaturaDao::cancelarPagamentoFatura($fatura);
	}
	
}
?>
