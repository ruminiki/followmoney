<?php
/*
 * Created on 01/03/2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Database{

	//produ��o
	//const  host       = 'mysql03.redehost.com.br';
	//const  user       = 'sa_fmdb';
	//const  password   = '5a_FmdB!';
	//const  database   = 'fmdb';
	//local
	const  host       = 'localhost';
	const  user       = 'root';
	const  password   = 'dust258';
	const  database   = 'milkManager';
	
	public static function getInstance ()
    // this implements the 'singleton' design pattern.
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new Database();
        } // if

        return $instance;

    } // getInstance  		
    
    public static function closeConnection($connection){
    	mysql_close($connection);
    }

   	public static function insert($query){
   		$con = mysql_connect(Database::host, Database::user, Database::password) or die(Database::erro(mysql_error()));
    	mysql_select_db(Database::database, $con);
    	$result = mysql_query($query);
    	if (mysql_errno() != ''){
    		 throw new Exception(mysql_error());
    	}
    	$id = mysql_insert_id();
    	Database::closeConnection($con);
    	return $id;
   	}
   	
   	public static function update($query){
    	$con = mysql_connect(Database::host, Database::user, Database::password) or die(Database::erro(mysql_error()));
    	mysql_select_db(Database::database, $con);
		$result = mysql_query($query);
    	if (mysql_errno() != ''){
    		 throw new Exception(mysql_error());
    	} 
    	Database::closeConnection($con);
		return $result;
   	}
   	
   	public static function remove($query){
    	$con = mysql_connect(Database::host, Database::user, Database::password) or die(Database::erro(mysql_error()));
    	mysql_select_db(Database::database, $con);
    	$result = mysql_query($query);
    	if (mysql_errno() != ''){
    		 throw new Exception(mysql_error());
    	} 
    	Database::closeConnection($con);
		return $result;
   	}
   	
	public static function executeQuery($query){
	    $con = mysql_connect(Database::host, Database::user, Database::password) or die(Database::erro(mysql_error()));
    	mysql_select_db(Database::database, $con);
    	$result = mysql_query($query);
    	if (!$result) {
		    $message  = 'Invalid query: ' . mysql_error() . "\n";
		    $message .= 'Whole query: ' . $query;
		    die($message);
		}
		Database::closeConnection($con);
    	return $result;  
	}   	
 }
 
?>
