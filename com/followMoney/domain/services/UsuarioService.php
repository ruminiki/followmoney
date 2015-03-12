<?php

include_once("com/followMoney/dao/UsuarioDao.php");
include_once("com/followMoney/domain/valueObjects/UsuarioVO.php");
include_once("com/followMoney/domain/valueObjects/FinalidadeVO.php");
include_once("com/followMoney/domain/valueObjects/ContaBancariaVO.php");
include_once("com/followMoney/domain/valueObjects/MovimentoVO.php");
include_once("com/followMoney/dao/FinalidadeDao.php");
include_once("com/followMoney/dao/ContaBancariaDao.php");
include_once("com/followMoney/dao/MovimentoDao.php");
include_once("com/followMoney/dao/UsuarioDao.php");
include_once("com/followMoney/application/util/Email.php");

class UsuarioService
{
    public function save($usuario){
    	$usuario = UsuarioDao::save($usuario);
		if ( $usuario->idEntity > 0 ){
    			Email::sendMailAccountCreated($usuario->email, $usuario->nome);
	  	}
		/* configura as finalidades iniciais do usuário, bem como a primeira conta bancária */
		if( $usuario->idEntity > 0 ){
		
			$salario = new FinalidadeVO();
			$salario->descricao = 'SALÁRIO';
			$salario->idUsuario = $usuario->idEntity;
			$salario = FinalidadeDao::save($salario);
		
			$educacao = new FinalidadeVO();
			$educacao->descricao = 'EDUCAÇÃO';
			$educacao->idUsuario = $usuario->idEntity;
			$educacao = FinalidadeDao::save($educacao);
			
			$lazer = new FinalidadeVO();
			$lazer->descricao = 'LAZER';
			$lazer->idUsuario = $usuario->idEntity;
			$lazer = FinalidadeDao::save($lazer);
			
			$carro = new FinalidadeVO();
			$carro->descricao = 'VEÍCULO';
			$carro->idUsuario = $usuario->idEntity;
			$carro = FinalidadeDao::save($carro);
			
			$combustivel = new FinalidadeVO();
			$combustivel->descricao = 'COMBUSTÍVEL';
			$combustivel->finalidadeSuperior = $carro;
			$combustivel->idUsuario = $usuario->idEntity;
			$combustivel = FinalidadeDao::save($combustivel);
			
			$ipva = new FinalidadeVO();
			$ipva->descricao = 'IPVA';
			$ipva->finalidadeSuperior = $carro;
			$ipva->idUsuario = $usuario->idEntity;
			$ipva = FinalidadeDao::save($ipva);
			
			$manutencao = new FinalidadeVO();
			$manutencao->descricao = 'MANUTENÇÃO';
			$manutencao->finalidadeSuperior = $carro;
			$manutencao->idUsuario = $usuario->idEntity;
			$manutencao = FinalidadeDao::save($manutencao);
			
			$moradia = new FinalidadeVO();
			$moradia->descricao = 'MORADIA';
			$moradia->idUsuario = $usuario->idEntity;
			$moradia = FinalidadeDao::save($moradia);
    	
			$aluguel = new FinalidadeVO();
			$aluguel->descricao = 'ALUGUEL';
			$aluguel->finalidadeSuperior = $moradia;
			$aluguel->idUsuario = $usuario->idEntity;
			$aluguel = FinalidadeDao::save($aluguel);
			
			$iptu = new FinalidadeVO();
			$iptu->descricao = 'IPTU';
			$iptu->finalidadeSuperior = $moradia;
			$iptu->idUsuario = $usuario->idEntity;
			$iptu = FinalidadeDao::save($iptu);
    
			//------cadastro de conta bancária
			
			$conta = new ContaBancariaVO();
			$conta->numero = '00000';
			$conta->digito = '0';
			$conta->descricao = 'CARTEIRA';
			$conta->idUsuario = $usuario->idEntity;
			$conta = ContaBancariaDao::save($conta);
			
			//--------cadastro de movimento teste
			
			$movimento = new MovimentoVO();
			$movimento->descricao = 'MOVIMENTO DE EXEMPLO';
			$movimento->emissao = new Zend_Date();
			$movimento->vencimento = new Zend_Date();
			$movimento->valor = 10;
			$movimento->status = 'PAGO';
			$movimento->operacao = 'CREDITO';
			$movimento->finalidade = $salario;
			$movimento->contaBancaria = $conta;
			$movimento->idUsuario = $usuario->idEntity;
			
			MovimentoDao::save($movimento);
		}
	   	return $usuario;
    }
    
    public function update($usuario){
    	return UsuarioDao::update($usuario);
    }
    
    public function remove($usuario){
    	return UsuarioDao::remove($usuario);
    }
    
    public function findAll(){
    	return UsuarioDao::findAll();
    }
    
    public function login($login, $senha){
    	$usuario = UsuarioDao::findByLogin($login);
    	if ( $usuario != null ){
    		if ( $usuario->senha == $senha ){
				$usr = $usuario->cloneUsuarioVO();
				$usr->ultimoAcesso = date_format(date_create(),"j F, Y, g:i a");
				UsuarioDao::update($usr);
				return $usuario;
			}else{
    			throw new Exception('Senha incorreta. Tente novamente.');
    		}
    	}
    	throw new Exception('Usuário não encontrado.');
    }
}
?>
