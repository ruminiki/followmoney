<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once "IGenericDao.php";
include_once("Database.php");
include_once("com/followMoney/domain/valueObjects/PreferenciasNotificacaoVO.php");
include_once("com/followMoney/application/util/SystemLog.php");

class PreferenciasNotificacaoDao implements IGenericDao{
		
	public static function save($preferenciasNotificacao){
	
		$sql = "insert into preferenciasNotificacao (descricao, chave, usuario, recebeEmail) values ('$preferenciasNotificacao->descricao', '$preferenciasNotificacao->chave', '$preferenciasNotificacao->idUsuario', '$preferenciasNotificacao->recebeEmail')";
		try{
			$id = Database::insert($sql);
			if ( $id > 0 ){
				$preferenciasNotificacao->idEntity = $id;
				return $preferenciasNotificacao;
			}	
		}catch(Exception $e){
			throw $e;
		}
	}
	
	public static function update($preferenciasNotificacao){
		$sql = "update preferenciasNotificacao set recebeEmail = '$preferenciasNotificacao->recebeEmail' " .
				"where id = $preferenciasNotificacao->idEntity";
				
		SystemLog::writeLog("PreferenciasNotificacaoDao.update() " . $sql);
				
		try{
			Database::update($sql);
			return $preferenciasNotificacao;	
		}catch(Exception $e){
			throw new Exception('Não foi possível alterar o preferenciasNotificacao selecionado.');
		}
	}
	
	public static function remove($preferenciasNotificacao){
		$sql = "delete from preferenciasNotificacao where id = $preferenciasNotificacao->idEntity";
		try{
			Database::remove($sql);
			return $preferenciasNotificacao;	
		}catch(Exception $e){
			throw new Exception('Não é possivel remover o preferenciasNotificacao selecionado.');
		}
	}
	
	public static function findAll($usuario){
		$sql = "select * from preferenciasNotificacao where usuario = '$usuario' order by descricao ";
		$result = Database::executeQuery($sql);
		
		if ( mysql_num_rows($result)==0 ){
			try{
				$sql = "insert into preferenciasNotificacao (descricao, chave, usuario, recebeEmail) values ('ENVIA E-MAIL MOVIMENTO VENCIDO?', 'MV', $usuario, 'N')";
				Database::insert($sql);
				$sql = "insert into preferenciasNotificacao (descricao, chave, usuario, recebeEmail) values ('ENVIA E-MAIL VENCIMENTO FATURA CARTÃO CRÉDITO?', 'FC', $usuario, 'N')";
				Database::insert($sql);
				return PreferenciasNotificacaoDao::findAll($usuario);
			}catch(Exception $e){
				throw $e;
			}
		}else{
			return PreferenciasNotificacaoDao::resultToArray($result);
		}
	}
	
	public static function findByChave($chave, $usuario){
		$sql = "select * from preferenciasNotificacao where usuario = '$usuario' and chave like '%$chave%' order by chave ";
		$result = Database::executeQuery($sql);
		return PreferenciasNotificacaoDao::resultToArray($result);
	}

	public static function findById($id, $usuario){
		$sql = "select * from preferenciasNotificacao where usuario = '$usuario' and id = '$id' order by chave ";
		$result = Database::executeQuery($sql);
		if($row = mysqli_fetch_array($result)){
			return PreferenciasNotificacaoDao::rowToObject($row);			 
		}
		return null;
	}

	private static function resultToArray($result){
		$list = array();
		while($row = mysqli_fetch_array($result)){
			array_push($list, PreferenciasNotificacaoDao::rowToObject($row));			 
		}
		return $list;
	}
	
	private static function rowToObject($row){
		$preferenciasNotificacao              = new PreferenciasNotificacaoVO();
		$preferenciasNotificacao->idEntity    = $row['id'];
		$preferenciasNotificacao->descricao   = $row['descricao'];
		$preferenciasNotificacao->chave       = $row['chave'];
		$preferenciasNotificacao->idUsuario   = $row['usuario'];
		$preferenciasNotificacao->recebeEmail = $row['recebeEmail'];
		return $preferenciasNotificacao;
	}
}

?>
