<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("com/milkManager/dao/IGenericDao.php");
include_once("com/milkManager/dao/Database.php");
include_once("com/milkManager/domain/valueObjects/RepeticaoCioVO.php");
include_once("com/milkManager/domain/valueObjects/AnimalVO.php");
include_once("com/milkManager/application/util/SystemLog.php");
include_once("com/milkManager/dao/AcompanhamentoDao.php");
include_once("com/milkManager/domain/valueObjects/EnseminacaoVO.php");
include_once("com/milkManager/dao/PartoDao.php");

define("SQL_REPETICAO"," select rep.*, a.nome as nomeFemea, a.id as idFemea, a.numero as numeroFemea, e.id as idEnseminacao " .
				   " from repeticaoCio rep inner join enseminacao e on e.id = rep.enseminacao " .
				   " inner join animal a on a.id = e.femea ");

class RepeticaoCioDao implements IGenericDao{
		
	public static function save($repeticaoCio){
	
		$enseminacao = ($repeticaoCio->enseminacao != null && $repeticaoCio->enseminacao->idEntity > 0) ? ($repeticaoCio->enseminacao->idEntity) : 'NULL';
		
		$sql = "insert into repeticaoCio (observacao, empresa, dataRepeticao, enseminacao) " .
				"values ('$repeticaoCio->observacao','$repeticaoCio->empresa','". $repeticaoCio->dataRepeticao->toString('yyyyMMdd') . "', $enseminacao)";
	
		//antes de inserir, remove parto e/ou repeticao de cio registrados para a enseminacao
		PartoDao::removePartoByEnseminacao($enseminacao);				
		RepeticaoCioDao::removeRepeticaoCioByEnseminacao($enseminacao);
	
		$id = Database::insert($sql);
		
		if ( $id > 0 ){
			$repeticaoCio->idEntity = $id;
			RepeticaoCioDao::generateAcompanhamento($repeticaoCio);
			return $repeticaoCio;
		}
		return null;
	}
	
	public static function update($repeticaoCio){
		$enseminacao = ($repeticaoCio->enseminacao != null && $repeticaoCio->enseminacao->idEntity > 0) ? ($repeticaoCio->enseminacao->idEntity) : 'NULL';
		
		$sql = "update repeticaoCio set observacao = '$repeticaoCio->observacao', dataRepeticao = '". $repeticaoCio->dataRepeticao->toString('yyyyMMdd') . "', enseminacao = $enseminacao " .
			   " where id = $repeticaoCio->idEntity";
			   
		if ( Database::update($sql) ){
		
			//hash identifica qual caso de uso gerou o registro na tabela de acompanhamento
			$hash = sha1('repeticaoCio'.$repeticaoCio->idEntity);
			$sql = "delete from acompanhamento where hash = '$hash'";
			if ( Database::remove($sql) ){
				RepeticaoCioDao::generateAcompanhamento($repeticaoCio);
			}
			return $repeticaoCio;
			
		}
		return null; 
	}
	
	public static function remove($repeticaoCio){
		$sql = "delete from repeticaoCio where id = $repeticaoCio->idEntity";
		if ( Database::remove($sql) ){
			//hash identifica qual caso de uso gerou o registro na tabela de acompanhamento
			$hash = sha1('repeticaoCio'.$repeticaoCio->idEntity);
			$sql = "delete from acompanhamento where hash = '$hash'";
			Database::remove($sql);
			return $repeticaoCio;	
		}
		return null;
	}
	
	public static function removeRepeticaoCioByEnseminacao($enseminacao){
		
		//recupera o id da repeticao no banco para remover o acompanhamento
		$repeticao = RepeticaoCioDao::findByEnseminacao($enseminacao);
		
		if ( isset($repeticao) ){
			
			$sql = "delete from repeticaoCio where id = $repeticao->idEntity";
			
			SystemLog::writeLog($sql);	
			
			if ( Database::remove($sql) ){
				
				//hash identifica qual caso de uso gerou o registro na tabela de acompanhamento
				$hash = sha1('repeticaoCio'.$repeticao->idEntity);
				$sql = "delete from acompanhamento where hash = '$hash'";
				Database::remove($sql);
				
			}
			
		}
		
	}
	
	public static function findByEnseminacao($enseminacao){
		$sql = SQL_REPETICAO . " where enseminacao = $enseminacao";
		
		SystemLog::writeLog($sql);
		
		$result = Database::executeQuery($sql);
		if ( $row = mysql_fetch_array($result) ){
			return RepeticaoCioDao::rowToObject($row);
		}
		return null;	
	}
	
	public static function findAll($empresa){
		$sql = SQL_REPETICAO . " where rep.empresa = '$empresa' order by dataRepeticao ";
		$result = Database::executeQuery($sql);
		return RepeticaoCioDao::resultToArray($result);
	}
	
	public static function findByDescricao($descricao, $empresa){
		$sql = SQL_REPETICAO . " where rep.empresa = '$empresa' and descricao like '%$descricao%' order by dataRepeticao ";
		$result = Database::executeQuery($sql);
		return RepeticaoCioDao::resultToArray($result);
	}
	
	public static function findById($id){
		$sql = SQL_REPETICAO . " where rep.id = '$id' ";
		$result = Database::executeQuery($sql);
		if ( $row = mysql_fetch_array($result) ){
			return RepeticaoCioDao::rowToObject($row);
		}
		return null;	
	}
	
	public static function findByParam($param, $empresa){
		$sql = SQL_REPETICAO . " where (a.nome like '%$param%' or " .
						   " a.numero like '%$param%' or " .
						   " rep.dataRepeticao like '%$param%') and rep.empresa = $empresa order by rep.dataRepeticao";
		
		SystemLog::writeLog($sql);			
						   
		$result = Database::executeQuery($sql);
		return RepeticaoCioDao::resultToArray($result);
	
	}
	
	private static function resultToArray($result){
		$list = array();
		while($row = mysql_fetch_array($result)){
			array_push($list, RepeticaoCioDao::rowToObject($row));			 
		}
		return $list;
	}
	
	private static function rowToObject($row){
		$repeticaoCio                 = new RepeticaoCioVO();
		$repeticaoCio->idEntity       = $row['id'];
		$repeticaoCio->observacao     = $row['observacao'];
		$repeticaoCio->empresa        = $row['empresa'];
		
		$dataRepeticao                = new DateTime();
		$dataRepeticao->setDate(substr($row['dataRepeticao'],0,4), substr($row['dataRepeticao'],4,2), substr($row['dataRepeticao'],6,2));
		$repeticaoCio->dataRepeticao  = $dataRepeticao;
		  
		$animal                       = new AnimalVO();
		$animal->idEntity             = $row['idFemea'];
		$animal->nome                 = $row['nomeFemea'];
		$animal->numero               = $row['numeroFemea'];
		
		$enseminacao                  = new EnseminacaoVO();
		$enseminacao->idEntity        = $row['idEnseminacao'];
		$enseminacao->femea           = $animal;
		
		$repeticaoCio->enseminacao    = $enseminacao;
		
		return $repeticaoCio;
	}
	
	//======GENERATE ACOMPANHAMENTO
	private static function generateAcompanhamento($repeticaoCio){
	
		$acompanhamento                   = new AcompanhamentoVO();
		$acompanhamento->animal           = $repeticaoCio->enseminacao->femea;
		$acompanhamento->dataProcedimento = $repeticaoCio->dataRepeticao;
		$acompanhamento->empresa          = $repeticaoCio->empresa;
		$acompanhamento->hash             = sha1('repeticaoCio'.$repeticaoCio->idEntity);
		
		SystemLog::writeLog($acompanhamento->hash);	
		
		$acompanhamento->observacao = "Repetição de Cio: Dia=> " . $repeticaoCio->dataRepeticao->toString('dd-MM-yyyy');
		
		AcompanhamentoDao::save($acompanhamento);
	
	}
}

?>
