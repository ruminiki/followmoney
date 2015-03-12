<?php
/*
 * Created on 30/01/2013
 
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

set_include_path('/var/www/followMoney'); 

include_once("com/followMoney/dao/Database.php");
include_once("com/followMoney/application/util/Email.php");
include_once("com/followMoney/application/util/SystemLog.php");

define("SQL_VENCIDOS","select m.descricao, DATE_FORMAT(STR_TO_DATE(m.vencimento,'%Y%m%d'),'%d/%m/%Y') as vencimento, m.valor from movimento m " .
	   "where m.status <> 'PAGO' and vencimento <= DATE_FORMAT(CURRENT_DATE(),'%Y%m%d') ");

//seleciona os usuarios que aceitam receber email	   
$sql = "select pn.usuario as usuario, u.email as email from preferenciasNotificacao pn inner join usuario u on (u.id = pn.usuario) where recebeEmail = 'S' and chave = 'MV'";

$result = Database::executeQuery($sql);

while($row = mysql_fetch_array($result)){

	$sql = SQL_VENCIDOS . "and usuario = " . $row['usuario'];
	SystemLog::writeLog("alertaVencimentoMovimento.findMovimentos(): " . $sql);
	
	$result1 = Database::executeQuery($sql);
	
	$subject = '[FM] Movimento Vencido';
	$to      = $row['email'];

	$msg     = "<p><b>O(s) movimento(s) abaixo encontra(m)-se vencido(s):</b></p>";

	while($row1 = mysql_fetch_array($result1)){

		$msg .= "<tr height='20' bgcolor='#cccccc'>";
		$msg .= "<td width='220'>";
		$msg .= $row1['descricao'];
		$msg .= "</td>";
		$msg .= "<td width='80'>";
		$msg .= $row1['vencimento'];
		$msg .= "</td>";
		$msg .= "<td align='right' width='110'>";
		$msg .= "R$ " . number_format($row1['valor'], 2, ',', '.');
		$msg .= "</td>";
		$msg .= "</tr>";
	}

	$msg .= '<br><p>Para maiores informações acesse http://www.followmoney.com.br/sistema.html.</p>';
	$msg .= '<p><font size="2">Você está recebendo esse e-mail porque optou por receber as notificações de movimentos vencidos. Caso não deseje mais receber, acesse o sistema e altere suas preferências.</font></p>';

	SystemLog::writeLog("alertaVencimentoMovimento.sendMail(): " . $to);
	
	Email::sendAlertMail($subject, $to, $msg);	

}





?>

