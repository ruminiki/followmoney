<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("com/milkManager/dao/IGenericDao.php");
include_once("com/milkManager/dao/Database.php");
include_once("com/milkManager/domain/valueObjects/EnseminacaoVO.php");
include_once("com/milkManager/domain/valueObjects/AnimalVO.php");
include_once("com/milkManager/application/util/SystemLog.php");
include_once("com/milkManager/dao/AcompanhamentoDao.php");
include_once("com/milkManager/dao/PartoDao.php");
include_once("com/milkManager/dao/RepeticaoCioDao.php");

define("SQL_BUSCA"," select e.*, a.id as idFemea, a.nome as nomeFemea, a.numero as numeroFemea, " .
			       " r.id as idReprodutor, r.nome as nomeReprodutor, r.numero as numeroReprodutor, " .
				   " p.id as idParto, rep.id as idRepeticao " .
			       " from enseminacao e inner join animal a on a.id = e.femea " .
			       " left join animal r on r.id = e.reprodutor " .
				   " left join parto p on p.enseminacao = e.id " .
				   " left join repeticaoCio rep on rep.enseminacao = e.id ");

class EnseminacaoDao implements IGenericDao{
		
	public static function save($enseminacao){
		$femea      = ($enseminacao->femea != null && $enseminacao->femea->idEntity > 0) ? ($enseminacao->femea->idEntity) : 'NULL';
		$reprodutor = ($enseminacao->reprodutor != null && $enseminacao->reprodutor->idEntity > 0) ? ($enseminacao->reprodutor->idEntity) : 'NULL';
	
		$sql = "insert into enseminacao (observacao, femea, reprodutor, dataEnseminacao, previsaoParto, valor, tipo, semenReprodutor, empresa) " .
				"values ('$enseminacao->observacao', $femea, $reprodutor, '" . $enseminacao->dataEnseminacao->toString('yyyyMMdd') . "'," . 
				"'" . $enseminacao->previsaoParto->toString('yyyyMMdd'). "', $enseminacao->valor, '$enseminacao->tipo', '$enseminacao->semenReprodutor', $enseminacao->empresa)";
				
		if ( EnseminacaoDao::hasEnseminacaoAguardandoParto($femea) ){
			throw new Exception("Já existe uma enseminação cadastrada para a fêmea " . $enseminacao->femea->numero . " - " . $enseminacao->femea->nome. ". Registre o parto ou a repetição.");
		}
				
		$id = Database::insert($sql);
		
		if ( $id > 0 ){
			$enseminacao->idEntity = $id;
			EnseminacaoDao::generateAcompanhamento($enseminacao);
			return $enseminacao;
		}
		return null;
	}
	
	public static function update($enseminacao){
		$femea      = ($enseminacao->femea != null && $enseminacao->femea->idEntity > 0) ? ($enseminacao->femea->idEntity) : 'NULL';
		$reprodutor = ($enseminacao->reprodutor != null && $enseminacao->reprodutor->idEntity > 0) ? ($enseminacao->reprodutor->idEntity) : 'NULL';
	
		$sql = "update enseminacao set observacao = '$enseminacao->observacao', " .
			   "femea = $femea, reprodutor = $reprodutor, dataEnseminacao = '" . $enseminacao->dataEnseminacao->toString('yyyyMMdd') . "'," . 
			   "previsaoParto = '" . $enseminacao->previsaoParto->toString('yyyyMMdd'). "', " .
			   "valor = $enseminacao->valor, tipo = '$enseminacao->tipo', semenReprodutor =  '$enseminacao->semenReprodutor' where id = $enseminacao->idEntity";
		
		SystemLog::writeLog($sql);		
		
		//ao alterar a enseminacao para o status AGUARDANDO_PARTO deve-se remover parto e/ou repeticao de cio registrados para a enseminacao
		if ( $enseminacao->situacao == 'AGUARDANDO PARTO' ){
			PartoDao::removePartoByEnseminacao($enseminacao->idEntity);				
			RepeticaoCioDao::removeRepeticaoCioByEnseminacao($enseminacao->idEntity);
		}
		
		if ( Database::update($sql) ){
			
			//hash identifica qual caso de uso gerou o registro na tabela de acompanhamento
			$hash = sha1('enseminacao'.$enseminacao->idEntity);
			$sql = "delete from acompanhamento where hash = '$hash'";
			if ( Database::remove($sql) ){
				EnseminacaoDao::generateAcompanhamento($enseminacao);
			}
			return $enseminacao;
			
		}
		return null; 
	}
	
	public static function remove($enseminacao){
		$sql = "delete from enseminacao where id = $enseminacao->idEntity";
		if ( Database::remove($sql) ){
			//hash identifica qual caso de uso gerou o registro na tabela de acompanhamento
			$hash = sha1('enseminacao'.$enseminacao->idEntity);
			$sql = "delete from acompanhamento where hash = '$hash'";
			Database::remove($sql);
			return $enseminacao;
		}
		return null;
	}
	
	public static function findAll($empresa){
		$sql = SQL_BUSCA . " where e.empresa = $empresa order by e.dataEnseminacao";
		
		SystemLog::writeLog($sql);	
		
		$result = Database::executeQuery($sql);
		return EnseminacaoDao::resultToArray($result);
	}
	
	public static function findByDescricao($descricao, $empresa){
		throw new Exception('Not yet implemented!');
	}
	
	public static function findById($id){
		$sql = SQL_BUSCA . " where e.id = '$id' order by e.dataEnseminacao ";
		$result = Database::executeQuery($sql);
		if ( $row = mysql_fetch_array($result) ){
			return EnseminacaoDao::rowToObject($row);
		}
		return null;	
	}
	
	public static function findByFemea($femea){
		$sql = SQL_BUSCA . " where e.femea = $femea order by e.dataEnseminacao";
		$result = Database::executeQuery($sql);
		return EnseminacaoDao::resultToArray($result);
	}
	
	public static function hasEnseminacaoAguardandoParto($femea){
			
		$sql = "select r.id as repeticaoCio, p.id as parto from enseminacao e left join repeticaoCio r on r.enseminacao = e.id left join parto p on p.enseminacao = e.id where e.femea = $femea";
		
		SystemLog::writeLog($sql);	
		
		$result = Database::executeQuery($sql);
		
		while($row = mysql_fetch_array($result)){
			if ( !isset($row['repeticaoCio']) && !isset($row['parto']) ){
				return true;
			}
		}
		return false;
	}
	
	public static function findByParam($param, $empresa){
		$sql = SQL_BUSCA . " where (a.nome like '%$param%' or " .
						   " r.nome like '%$param%' or " .
						   " a.numero like '%$param%' or " .
						   " r.numero like '%$param%' or " .
						   " e.dataEnseminacao like '%$param%' or " .
						   " e.semenReprodutor like '%$param%') and e.empresa = $empresa order by e.dataEnseminacao";
		
		SystemLog::writeLog($sql);			
						   
		$result = Database::executeQuery($sql);
		return EnseminacaoDao::resultToArray($result);
	
	}
		
	private static function resultToArray($result){
		$list = array();
		while($row = mysql_fetch_array($result)){
			array_push($list, EnseminacaoDao::rowToObject($row));			 
		}
		return $list;
	}
	
	private static function rowToObject($row){
		$enseminacao                  = new EnseminacaoVO();
		$enseminacao->idEntity        = $row['id'];
		$enseminacao->observacao      = $row['observacao'];
		$enseminacao->valor           = $row['valor'];
		$enseminacao->semenReprodutor = $row['semenReprodutor'];
		
		if ( $row['idRepeticao'] != '' ){
			$enseminacao->situacao = 'REPETIU CIO';
		}
		
		if ( $row['idParto'] != '' ){
			$enseminacao->situacao = 'PARIDA';
		}
		
		if ( $row['idParto'] == ''  && $row['idRepeticao'] == '' ){
			$enseminacao->situacao = 'AGUARDANDO PARTO';
		}
		
		$dataEnseminacao              = new DateTime();
		$dataEnseminacao->setDate(substr($row['dataEnseminacao'],0,4), substr($row['dataEnseminacao'],4,2), substr($row['dataEnseminacao'],6,2));
		$enseminacao->dataEnseminacao = $dataEnseminacao;
		
		$previsaoParto                = new DateTime();
		$previsaoParto->setDate(substr($row['previsaoParto'],0,4), substr($row['previsaoParto'],4,2), substr($row['previsaoParto'],6,2));
		$enseminacao->previsaoParto   = $previsaoParto;
		
		$femea                        = new AnimalVO();
		$femea->idEntity  			  = $row['idFemea'];
		$femea->nome 			      = $row['nomeFemea'];
		$femea->numero 				  = $row['numeroFemea'];
		$enseminacao->femea    		  = $femea;
		
		$reprodutor                   = new AnimalVO();
		$reprodutor->idEntity  		  = $row['idReprodutor'];
		$reprodutor->nome 			  = $row['nomeReprodutor'];
		$reprodutor->numero 		  = $row['numeroReprodutor'];
		
		if ( !isset($reprodutor->idEntity) ){
			$reprodutor->nome   = '-';
			$reprodutor->numero = '-';
		}
		
		$enseminacao->reprodutor      = $reprodutor;
	
		return $enseminacao;
	}
	
	//======GENERATE ACOMPANHAMENTO
	private static function generateAcompanhamento($enseminacao){
	
		$acompanhamento = new AcompanhamentoVO();
		$acompanhamento->animal = $enseminacao->femea;
		$acompanhamento->dataProcedimento = $enseminacao->dataEnseminacao;
		$acompanhamento->empresa = $enseminacao->empresa;
		$acompanhamento->hash = sha1('enseminacao'.$enseminacao->idEntity);
		
		SystemLog::writeLog($acompanhamento->hash);	
		
		if ( $enseminacao->semenReprodutor == "" ){
			$acompanhamento->observacao = "Enseminação: Reprodutor=> " . $enseminacao->reprodutor->numero . " - " . $enseminacao->reprodutor->nome . " / Previsão de parto=> " . $enseminacao->previsaoParto->toString('dd-MM-yyyy');
		}else{
			$acompanhamento->observacao = "Enseminação: Reprodutor=> " . $enseminacao->semenReprodutor . " / Previsão de parto=> " . $enseminacao->previsaoParto->toString('dd-MM-yyyy');
		}
		
		AcompanhamentoDao::save($acompanhamento);
	
	}
	
	
	
}

?>
