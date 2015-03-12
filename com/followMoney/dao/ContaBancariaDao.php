<?php
/*
 * Created on 01/03/2011
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("com/followMoney/dao/IGenericDao.php");
include_once("com/followMoney/dao/Database.php");
include_once("com/followMoney/application/util/DateUtil.php");
include_once("com/followMoney/domain/valueObjects/ContaBancariaVO.php");
include_once("com/followMoney/domain/valueObjects/MovimentoReportVO.php");

define("SQL","select cb.id, cb.situacao, cb.descricao, cb.usuario, cb.numero, cb.digito, cb.dataInativacao from contaBancaria cb "); 

class ContaBancariaDao implements IGenericDao{
		
	public static function save($contaBancaria){
		$sql = "insert into contaBancaria (descricao, usuario, numero, digito, situacao) " .
				"values ('$contaBancaria->descricao', '$contaBancaria->idUsuario', '$contaBancaria->numero', '$contaBancaria->digito', '$contaBancaria->situacao')";
		try{
			$id = Database::insert($sql);
			if ( $id > 0 ){
				$contaBancaria->idEntity = $id;
				return $contaBancaria;
			}	
		}catch(Exception $e){
			//throw new Exception('N�o foi poss�vel salvar a conta bancaria selecionada.');
			throw $e;
		}
	}
	
	public static function update($contaBancaria){
		$sql = "update contaBancaria set descricao='$contaBancaria->descricao', " .
				"usuario='$contaBancaria->idUsuario', " .
				"numero='$contaBancaria->numero', " .
				"digito='$contaBancaria->digito', " .
				"situacao='$contaBancaria->situacao', " .
				"dataInativacao='$contaBancaria->dataInativacao' " .
				"where id = $contaBancaria->idEntity and usuario ='$contaBancaria->idUsuario'";
		try{
			Database::update($sql);
			return $contaBancaria;	
		}catch(Exception $e){
			//throw new Exception('N�o foi poss�vel alterar a conta bancaria selecionado.');
			throw $e;
		}
	}
	
	public static function remove($contaBancaria){
		$sql = "delete from contaBancaria where id = $contaBancaria->idEntity and usuario = '$contaBancaria->idUsuario'";
		try{
			Database::remove($sql);
			return $contaBancaria;	
		}catch(Exception $e){
			throw new Exception('Não foi possível remover a conta bancaria selecionado.');
		}
	}
	
	public static function findAll($usuario){
		$whereClause = " where cb.usuario = '$usuario' order by cb.descricao ";
		$result = Database::executeQuery(SQL . $whereClause);
		return ContaBancariaDao::resultToArray($result);
	}
	
	public static function findByDescricao($descricao, $usuario){
		$whereClause = " where usuario = '$usuario' and descricao like '%$descricao%' order by descricao ";
		$result = Database::executeQuery(SQL . $whereClause);
		return ContaBancariaDao::resultToArray($result);
	}

	public static function findById($id, $usuario){
		$whereClause = " where usuario = '$usuario' and id = '$id' order by descricao ";
		$result = Database::executeQuery(SQL . $whereClause);
		if($row = mysqli_fetch_array($result)){
			return ContaBancariaDao::rowToObject($row);
		}
		return null;
	}
	
	//==================GRAFICO====================================
	
	public static function resumeByYear($contaBancaria, $year, $usuario){
		$list = array();
		for ($i = 1; $i <= 12; $i++){
			array_push($list, ContaBancariaDao::getSaldoByMonth($contaBancaria, $i, $year, $usuario));
		}
		return $list;
	}
	
	public static function getSaldoByMonth($contaBancaria, $month, $year, $usuario){
		$movimentos = array();
		
		$date1 = new Zend_Date(array('year' => $year, 'month' => $month, 'day' => '01'));
		$date2 = new Zend_Date(array('year' => $year, 'month' => $month, 'day' => DateUtil::getLastDayMonth($month)));
		
		$movimentos = MovimentoDao::findMovimentosByCBAndRange($contaBancaria, $date1, $date2, $usuario);

		$totalCreditos = 0;
		$totalDebitos = 0;
		foreach($movimentos as $movimento){
			if ( $movimento->operacao == 'DEBITO'){
				$totalDebitos += $movimento->valor;				
			}else{
				$totalCreditos += $movimento->valor;
			}
    	}
    	
    	$saldo = MovimentoDao::calculaSaldoAnteriorCB($contaBancaria, $date2, $usuario);
    	
    	$object = new MovimentoReportVO();
		$object->credito = $totalCreditos;
		$object->debito = $totalDebitos;
		$object->saldo = $saldo;
		$object->mes = $month;
		return $object;
	}

	private static function resultToArray($result){
		$list = array();
		while($row = mysqli_fetch_array($result)){
			array_push($list, ContaBancariaDao::rowToObject($row));			 
		}
		return $list;
	}
	
	private static function rowToObject($row){
		$contaBancaria = new ContaBancariaVO();
		$contaBancaria->idEntity		  = $row['id'];
		$contaBancaria->descricao 	 	  = $row['descricao'];
		$contaBancaria->idUsuario		  = $row['usuario'];
		$contaBancaria->numero   		  = $row['numero'];
		$contaBancaria->digito   		  = $row['digito'];
		$contaBancaria->situacao   		  = $row['situacao'];
		$contaBancaria->dataInativacao	  = $row['dataInativacao'];
		return $contaBancaria;
	}
	
	
}

?>
