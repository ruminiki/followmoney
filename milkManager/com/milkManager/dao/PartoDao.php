<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("com/milkManager/dao/IGenericDao.php");
include_once("com/milkManager/dao/Database.php");
include_once("com/milkManager/domain/valueObjects/PartoVO.php");
include_once("com/milkManager/domain/valueObjects/EnseminacaoVO.php");
include_once("com/milkManager/domain/valueObjects/AnimalVO.php");
include_once("com/milkManager/application/util/SystemLog.php");
include_once("com/milkManager/dao/AcompanhamentoDao.php");
include_once("com/milkManager/dao/RepeticaoCioDao.php");

define("SQL_PARTO"," select p.*, a.nome as nomeFemea, a.id as idFemea, a.numero as numeroFemea, e.id as idEnseminacao, " .
				   " filho.numero as numeroFilho, filho.nome as nomeFilho " .
				   " from parto p inner join enseminacao e on e.id = p.enseminacao " .
				   " inner join animal a on a.id = e.femea " .
				   " left join animal filho on filho.nascimento = p.id ");


class PartoDao implements IGenericDao{
		
	public static function save($parto){
		$enseminacao = ($parto->enseminacao != null && $parto->enseminacao->idEntity > 0) ? ($parto->enseminacao->idEntity) : 'NULL';
	
		$sql = "insert into parto (observacao, empresa, dataParto, horaParto, enseminacao) " .
				"values ('$parto->observacao','$parto->empresa','". $parto->dataParto->toString('yyyyMMdd') . "','$parto->horaParto', $enseminacao)";
				
		//antes de inserir, remove parto e/ou repeticao de cio registrados para a enseminacao
		PartoDao::removePartoByEnseminacao($enseminacao);				
		RepeticaoCioDao::removeRepeticaoCioByEnseminacao($enseminacao);
		
		$id = Database::insert($sql);
		
		if ( $id > 0 ){
			$parto->idEntity = $id;
			PartoDao::generateAcompanhamento($parto);
			return $parto;
		}
		return null;
	}
	
	public static function update($parto){
		$enseminacao = ($parto->enseminacao != null && $parto->enseminacao->idEntity > 0) ? ($parto->enseminacao->idEntity) : 'NULL';
		
		$sql = "update parto set observacao = '$parto->observacao', dataParto = '". $parto->dataParto->toString('yyyyMMdd') . "', horaParto = '$parto->horaParto', enseminacao = $enseminacao " .
			   " where id = $parto->idEntity";
			   
		if ( Database::update($sql) ){
		
			//hash identifica qual caso de uso gerou o registro na tabela de acompanhamento
			$hash = sha1('parto'.$parto->idEntity);
			$sql = "delete from acompanhamento where hash = '$hash'";
			if ( Database::remove($sql) ){
				PartoDao::generateAcompanhamento($parto);
			}
			return $parto;
			
		}
		return null; 
	}
	
	public static function remove($parto){
		$sql = "delete from parto where id = $parto->idEntity";
		if ( Database::remove($sql) ){
			
			//hash identifica qual caso de uso gerou o registro na tabela de acompanhamento
			$hash = sha1('parto'.$parto->idEntity);
			$sql = "delete from acompanhamento where hash = '$hash'";
			Database::remove($sql);
			return $parto;	
			
		}
		return null;
	}
	
	public static function removePartoByEnseminacao($enseminacao){
		//recupera o id do parto no banco para remover o acompanhamento
		$parto = PartoDao::findByEnseminacao($enseminacao);
		
		if ( isset($parto) ){
			$sql = "delete from parto where id = $parto->idEntity";
			SystemLog::writeLog($sql);	
			if ( Database::remove($sql) ){
				//hash identifica qual caso de uso gerou o registro na tabela de acompanhamento
				$hash = sha1('parto'.$parto->idEntity);
				$sql = "delete from acompanhamento where hash = '$hash'";
				Database::remove($sql);
			}
		}
		
	}
	
	public static function findAll($empresa){
		$sql = SQL_PARTO . " where p.empresa = '$empresa' order by p.dataParto ";
		
		SystemLog::writeLog($sql);	
		
		$result = Database::executeQuery($sql);
		return PartoDao::resultToArray($result);
	}
	
	public static function findByDescricao($descricao, $empresa){
		$sql = SQL_PARTO . " where p.empresa = '$empresa' and descricao like '%$descricao%' order by dataParto ";
		$result = Database::executeQuery($sql);
		return PartoDao::resultToArray($result);
	}
	
	public static function findById($id){
		$sql = SQL_PARTO . " where id = '$id'";
		$result = Database::executeQuery($sql);
		if ( $row = mysql_fetch_array($result) ){
			return PartoDao::rowToObject($row);
		}
		return null;	
	}
	
	public static function findByEnseminacao($enseminacao){
		$sql = SQL_PARTO . " where enseminacao = '$enseminacao'";
		$result = Database::executeQuery($sql);
		if ( $row = mysql_fetch_array($result) ){
			return PartoDao::rowToObject($row);
		}
		return null;	
	}
	
	public static function findByParam($param, $empresa){
		$sql = SQL_PARTO . " where (a.nome like '%$param%' or " .
						   " a.numero like '%$param%' or " .
						   " p.dataParto like '%$param%') and p.empresa = $empresa order by p.dataParto";
		
		SystemLog::writeLog($sql);			
						   
		$result = Database::executeQuery($sql);
		return PartoDao::resultToArray($result);
	
	}

	
	private static function resultToArray($result){
		$list = array();
		while($row = mysql_fetch_array($result)){
			array_push($list, PartoDao::rowToObject($row));			 
		}
		return $list;
	}
	
	private static function rowToObject($row){
		$parto                 = new PartoVO();
		$parto->idEntity       = $row['id'];
		$parto->observacao     = $row['observacao'];
		$parto->empresa        = $row['empresa'];
		$parto->horaParto      = $row['horaParto'];
		
		$dataParto             = new DateTime();
		$dataParto->setDate(substr($row['dataParto'],0,4), substr($row['dataParto'],4,2), substr($row['dataParto'],6,2));
		$parto->dataParto      = $dataParto;
		
		$animal                = new AnimalVO();
		$animal->idEntity      = $row['idFemea'];
		$animal->nome          = $row['nomeFemea'];
		$animal->numero        = $row['numeroFemea'];
		
		$enseminacao           = new EnseminacaoVO();
		$enseminacao->idEntity = $row['idEnseminacao'];
		$enseminacao->situacao = 'PARIDA';
		$enseminacao->femea    = $animal;
		
		if ( !isset($row['numeroFilho']) ){
			$parto->nascidos = 'Filho não cadastrado.';
		}else{
			$parto->nascidos       = $row['numeroFilho'] . ' - ' . $row['nomeFilho'];
		}
		
		$parto->enseminacao    = $enseminacao;
		
		return $parto;
	}
	
		//======GENERATE ACOMPANHAMENTO
	private static function generateAcompanhamento($parto){
	
		$acompanhamento                   = new AcompanhamentoVO();
		$acompanhamento->animal           = $parto->enseminacao->femea;
		$acompanhamento->dataProcedimento = $parto->dataParto;
		$acompanhamento->empresa          = $parto->empresa;
		$acompanhamento->hash             = sha1('parto'.$parto->idEntity);
		
		SystemLog::writeLog($acompanhamento->hash);	
		
		$acompanhamento->observacao = "Parto: Dia=> " . $parto->dataParto->toString('dd-MM-yyyy')  . " - " . $parto->horaParto;
		
		AcompanhamentoDao::save($acompanhamento);
	
	}
	
}

?>
