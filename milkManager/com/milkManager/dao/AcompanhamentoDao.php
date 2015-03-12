<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("com/milkManager/dao/IGenericDao.php");
include_once("com/milkManager/dao/Database.php");
include_once("com/milkManager/domain/valueObjects/AcompanhamentoVO.php");
include_once("com/milkManager/domain/valueObjects/ProcedimentoVO.php");
include_once("com/milkManager/domain/valueObjects/AnimalVO.php");
include_once("com/milkManager/application/util/SystemLog.php");

define("SQL"," select a.id as id, a.observacao as observacao, a.fotografia as fotografia, " .
			 " a.dataProcedimento as dataProcedimento, a.empresa as empresa, " .
			 " an.id as idAnimal, an.nome as nomeAnimal, an.numero as numeroAnimal, " .
			 " p.id as idProcedimento, p.descricao as descricaoProcedimento " .
			 " from acompanhamento a " .
			 " inner join animal an on an.id = a.animal " .
			 " left join procedimento p on p.id = a.procedimento ");


class AcompanhamentoDao implements IGenericDao{
		
	public static function save($acompanhamento){
		$animal       = ($acompanhamento->animal != null && $acompanhamento->animal->idEntity > 0) ? ($acompanhamento->animal->idEntity) : 'NULL';
		$procedimento = ($acompanhamento->procedimento != null && $acompanhamento->procedimento->idEntity > 0) ? ($acompanhamento->procedimento->idEntity) : 'NULL';
	
		$sql = "insert into acompanhamento (observacao, fotografia, procedimento, animal, empresa, dataProcedimento, hash) " .
				"values ('$acompanhamento->observacao','$acompanhamento->fotografia',$procedimento, $animal, $acompanhamento->empresa, '" . $acompanhamento->dataProcedimento->toString('yyyyMMdd'). "', '$acompanhamento->hash')";
		
		SystemLog::writeLog($sql);		
		
		$id = Database::insert($sql);
		
		if ( $id > 0 ){
			$acompanhamento->idEntity = $id;
			return $acompanhamento;
		}
		return null;
	}
	
	public static function update($acompanhamento){
		$animal       = ($acompanhamento->animal != null && $acompanhamento->animal->idEntity > 0) ? ($acompanhamento->animal->idEntity) : 'NULL';
		$procedimento = ($acompanhamento->procedimento != null && $acompanhamento->procedimento->idEntity > 0) ? ($acompanhamento->procedimento->idEntity) : 'NULL';
		
		$sql = "update acompanhamento set observacao = '$acompanhamento->observacao', fotografia = '$acompanhamento->fotografia', " .
			   " animal = $animal, procedimento = $procedimento, dataProcedimento = '" . $acompanhamento->dataProcedimento->toString('yyyyMMdd') . "' where id = $acompanhamento->idEntity";
			   
		if ( Database::update($sql) ){
			return $acompanhamento;
		}
		return null; 
	}
	
	public static function remove($acompanhamento){
		$sql = "delete from acompanhamento where id = $acompanhamento->idEntity";
		if ( Database::remove($sql) ){
			return $acompanhamento;
		}
		return null;
	}
	
	public static function findById($id){
		$sql = SQL . " where a.id = '$id'";
		$result = Database::executeQuery($sql);
		if ( $row = mysql_fetch_array($result) ){
			return AcompanhamentoDao::rowToObject($row);
		}
		return null;	
	}
	
	public static function findByAnimal($animal){
		$sql = SQL . " where a.animal = $animal->idEntity order by a.dataProcedimento";
		SystemLog::writeLog($sql);
		$result = Database::executeQuery($sql);
		return AcompanhamentoDao::resultToArray($result);
	}
	
	public static function findAll($empresa){
		$sql = SQL . " where a.empresa = '$empresa' order by a.dataProcedimento ";
		SystemLog::writeLog($sql);
		$result = Database::executeQuery($sql);
		return AcompanhamentoDao::resultToArray($result);
	}
	
	public static function findByParam($param, $empresa){
		$sql = SQL . " where (an.nome like '%$param%' or " .
						   " an.numero like '%$param%' or " .
						   " a.observacao like '%$param%' or " .
						   " p.descricao like '%$param%' or " .
						   " a.dataProcedimento like '%$param%') and a.empresa = $empresa order by a.dataProcedimento";
		
		SystemLog::writeLog($sql);			
						   
		$result = Database::executeQuery($sql);
		return AcompanhamentoDao::resultToArray($result);
	}

	
	private static function resultToArray($result){
		$list = array();
		while($row = mysql_fetch_array($result)){
			array_push($list, AcompanhamentoDao::rowToObject($row));			 
		}
		return $list;
	}
	
	private static function rowToObject($row){
		$acompanhamento                   = new AcompanhamentoVO();
		$acompanhamento->idEntity         = $row['id'];
		$acompanhamento->observacao       = $row['observacao'];
		$acompanhamento->empresa          = $row['empresa'];
		$acompanhamento->fotografia       = $row['fotografia'];
		
		$dataProcedimento                 = new DateTime();
		$dataProcedimento->setDate(substr($row['dataProcedimento'],0,4), substr($row['dataProcedimento'],4,2), substr($row['dataProcedimento'],6,2));
		$acompanhamento->dataProcedimento = $dataProcedimento;
		
		$animal                           = new AnimalVO();
		$animal->idEntity  				  = $row['idAnimal'];
		$animal->nome 					  = $row['nomeAnimal'];
		$animal->numero 				  = $row['numeroAnimal'];
		$acompanhamento->animal    		  = $animal;
		
		$procedimento           		  = new ProcedimentoVO();
		$procedimento->idEntity  	      = $row['idProcedimento'];
		$procedimento->descricao		  = $row['descricaoProcedimento'];
		$acompanhamento->procedimento     = $procedimento;
		
		return $acompanhamento;
	}
}

?>
