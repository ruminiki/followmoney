<?php
/*
 * Created on 01/03/2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 interface IGenericDao{
 	
 	public static function save($entity);
 	public static function update($entity);
 	public static function remove($entity);
 	public static function findAll($empresa);
 }
?>
