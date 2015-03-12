<?php
include_once("com/followMoney/dao/LancamentoRapidoDao.php");

class LancamentoRapidoService
{
    public function lancar($movimento){
    	return LancamentoRapidoDao::lancar($movimento);
    }
 
}
?>
