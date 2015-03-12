<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("com/milkManager/dao/IGenericDao.php");
include_once("com/milkManager/dao/Database.php");
include_once("com/milkManager/domain/valueObjects/AnimalVO.php");
include_once("com/milkManager/domain/valueObjects/RacaVO.php");
include_once("com/milkManager/domain/valueObjects/LocalizacaoVO.php");
include_once("com/milkManager/domain/valueObjects/SituacaoAnimalVO.php");
include_once("com/milkManager/application/util/SystemLog.php");
include_once("com/milkManager/domain/valueObjects/PartoVO.php");
include_once("com/milkManager/domain/valueObjects/EnseminacaoVO.php");

define("SQL"," select a.id as id, a.nome as nome, a.sexo as sexo, a.numero as numero, a.origem as origem, a.dataNascimento as dataNascimento, " .
			 " a.localizacao as localizacao, a.empresa as empresa, r.id as idRaca, r.descricao as descricaoRaca, " .
			 " s.id as idSituacao, s.descricao as descricaoSituacao, " .
			 " l.id as idLocalizacao, l.descricao as descricaoLocalizacao, " .
			 " p.id as idNascimento, mae.id as idMae, mae.numero as numeroMae, mae.nome as nomeMae, " .
			 " es.id as idEnseminacao " .
			 " from animal a " .
			 " inner join raca r on r.id = a.raca " .
			 " inner join situacaoAnimal s on s.id = a.situacao " .
			 " inner join localizacao l on l.id = a.localizacao " .
			 " left join parto p on p.id = a.nascimento " .
			 " left join enseminacao es on es.id = p.enseminacao " .
			 " left join animal mae on mae.id = es.femea " );
			 

class AnimalDao implements IGenericDao{
		
	public static function save($animal){
		$raca        = ($animal->raca != null && $animal->raca->idEntity > 0) ? ($animal->raca->idEntity) : 'NULL';
		$localizacao = ($animal->localizacao != null && $animal->localizacao->idEntity > 0) ? ($animal->localizacao->idEntity) : 'NULL';
		$situacao    = ($animal->situacao != null && $animal->situacao->idEntity > 0) ? ($animal->situacao->idEntity) : 'NULL';
		$nascimento  = ($animal->nascimento != null && $animal->nascimento->idEntity > 0) ? ($animal->nascimento->idEntity) : 'NULL';
	
		$sql = "insert into animal (numero, raca, nome, sexo, origem, dataNascimento, localizacao, situacao, empresa, nascimento ) " .
				"values ('$animal->numero', $raca," .
				"'$animal->nome', " . 
				"'$animal->sexo', " .
				"'$animal->origem', " .
				"'" . $animal->dataNascimento->toString('yyyyMMdd') . "', " .
				"$localizacao, " .
				"$situacao, " .
				"$animal->empresa, " . 
				"$nascimento)";
				
		SystemLog::writeLog($sql);				
				
		$id = Database::insert($sql);
		
		if ( $id > 0 ){
			$animal->idEntity = $id;
			return $animal;
		}
		return null;
	}
	
	public static function update($animal){
		$raca        = ($animal->raca != null && $animal->raca->idEntity > 0) ? ($animal->raca->idEntity) : 'NULL';
		$localizacao = ($animal->localizacao != null && $animal->localizacao->idEntity > 0) ? ($animal->localizacao->idEntity) : 'NULL';
		$situacao    = ($animal->situacao != null && $animal->situacao->idEntity > 0) ? ($animal->situacao->idEntity) : 'NULL';
		$nascimento  = ($animal->nascimento != null && $animal->nascimento->idEntity > 0) ? ($animal->nascimento->idEntity) : 'NULL';
		
		$sql = "update animal set " .
				"numero = '$animal->numero'," . 
				"raca = $raca ," .
				"nome = '$animal->nome', " . 
				"sexo = '$animal->sexo', " .
				"origem = '$animal->origem', " .
				"dataNascimento = '" . $animal->dataNascimento->toString('yyyyMMdd'). "', " .
				"localizacao = $localizacao, " .
				"situacao = $situacao, nascimento = $nascimento where id = $animal->idEntity";
				
		SystemLog::writeLog($sql);						
				
		if ( Database::update($sql) ){
			return $animal;
		}
		return null; 
	}
	
	public static function remove($animal){
		$sql = "delete from animal where id = $animal->idEntity";
		if ( Database::remove($sql) ){
			return $animal;
		}
		return null;
	}
	
	public static function findAll($empresa){
		$sql = SQL . " where a.empresa = '$empresa' order by a.nome ";
		SystemLog::writeLog($sql);
		$result = Database::executeQuery($sql);
		return AnimalDao::resultToArray($result);
	}
	
	public static function findByNome($nome, $empresa){
		$sql = SQL . " where a.empresa = '$empresa' and nome like '%$nome%' order by a.nome";
		$result = Database::executeQuery($sql);
		return AnimalDao::resultToArray($result);
	}
	
	public static function findById($id){
		$sql = SQL . " where a.id = '$id' order by a.nome ";
		$result = Database::executeQuery($sql);
		if ( $row = mysql_fetch_array($result) ){
			return AnimalDao::rowToObject($row);
		}
		return null;	
	}
	
	public static function findBySexo($sexo, $empresa){
		$sql = SQL . " where a.empresa = '$empresa' and sexo like '%$sexo%' order by a.nome";
		$result = Database::executeQuery($sql);
		return AnimalDao::resultToArray($result);
	}
	
	private static function resultToArray($result){
		$list = array();
		while($row = mysql_fetch_array($result)){
			array_push($list, AnimalDao::rowToObject($row));			 
		}
		return $list;
	}
	
	public static function findByParam($param, $empresa){
		$sql = SQL . " where (a.nome like '%$param%' or " .
						   " a.numero like '%$param%') and a.empresa = $empresa order by a.nome, a.numero";
		
		SystemLog::writeLog($sql);			
						   
		$result = Database::executeQuery($sql);
		return AnimalDao::resultToArray($result);
	
	}
	
	private static function rowToObject($row){
	
		$animal            		 = new AnimalVO();
		$animal->idEntity  		 = $row['id'];
		$animal->nome      		 = $row['nome'];
		$animal->empresa   		 = $row['empresa'];
		$animal->numero      	 = $row['numero'];
		$animal->sexo     		 = $row['sexo'];
		$animal->origem   		 = $row['origem'];
		$animal->localizacao     = $row['localizacao'];
		                         
		$situacao                = new SituacaoAnimalVO();
		$situacao->idEntity      = $row['idSituacao'];
		$situacao->descricao     = $row['descricaoSituacao'];
		$animal->situacao        = $situacao;
		                         
		$localizacao             = new LocalizacaoVO();
		$localizacao->idEntity   = $row['idLocalizacao'];
		$localizacao->descricao  = $row['descricaoLocalizacao'];
		$animal->localizacao     = $localizacao;
		                         
		$dataNascimento          = new DateTime();
		$dataNascimento->setDate(substr($row['dataNascimento'],0,4), substr($row['dataNascimento'],4,2), substr($row['dataNascimento'],6,2));
		$animal->dataNascimento	 = $dataNascimento;
		
		$raca                    = new RacaVO();
		$raca->idEntity          = $row['idRaca'];
		$raca->descricao         = $row['descricaoRaca'];
		$animal->raca            = $raca;
				
		$mae            		 = new AnimalVO();
		$mae->idEntity  	 	 = $row['idMae'];
		$mae->nome      		 = $row['nomeMae'];
		$mae->numero        	 = $row['numeroMae'];
		
		$nascimento              = new PartoVO();
		$nascimento->idEntity    = $row['idNascimento'];
		
		$enseminacao             = new EnseminacaoVO();
		$enseminacao->idEntity   = $row['idEnseminacao'];
		$enseminacao->femea      = $mae;
		
		$nascimento->enseminacao = $enseminacao;
		$animal->nascimento      = $nascimento;
		
		return $animal;
	}
}

?>
