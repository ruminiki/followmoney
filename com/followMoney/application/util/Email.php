<?php
/*
 * Created on 14/04/2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
//include_once("com/followMoney/application/util/class.phpmailer.php");
include_once("class.phpmailer.php");
include("class.smtp.php");

class Email{
	
	public static function sendMailError(){
		$mail = Email::getMailSender();
		$mail->Subject =   "[Follow Money] Error";
		$mail->AddAddress("followmoneybr@gmail.com");
		$mail->Body    = "Ocorreu um erro no Sistema.";
		$mail->AltBody = $mail->Body;
		$enviado       = $mail->Send();
	}

        public static function sendMailAttach(){
                $mail = Email::getMailSender();
                $mail->Subject =   "[Follow Money] Backup";
                $mail->AddAddress("followmoneybr@gmail.com");
                $mail->Body    = "Ocorreu um erro no Sistema.";
                $mail->AltBody = $mail->Body;
                $enviado       = $mail->Send();
        }
	
	public static function sendAlertMail($subject, $to, $msg='', $file_path=null){
		$mail             = new PHPMailer();
                $mail->IsSMTP();
                $mail->SMTPAuth   = true;
		$mail->SMTPDebug  = 2;
                $mail->Port       = 587;
                $mail->SMTPSecure = "tls";
                $mail->Host       = "smtp.gmail.com";
                $mail->SMTPDebug  = true;
                $mail->Username   = "followmoneybr@gmail.com";
                $mail->Password   = "";
                $mail->From       = "no-reply@followmoney.com.br";
                $mail->FromName   = "Follow Money";
		$mail->CharSet    = "utf-8";
		$mail->Subject    = $subject;;
                $mail->AddAddress($to);
                $mail->Body       = $msg;
                $mail->AltBody    = $mail->Body;
		if (isset($file_path)){
			$mail->AddAttachment($file_path);
		}
                $enviado          = $mail->Send();
		$mail->SmtpClose();
        }

	public static function sendMailAccountCreated($to, $name){
		$mail             = new PHPMailer();
		//try {
			//$mail->Mailer = "smtp";			
			$mail->IsSMTP();
			$mail->SMTPAuth   = true;
			$mail->Port       = 587;
			$mail->SMTPSecure = "tls";
			$mail->Host       = "smtp.gmail.com";
			$mail->SMTPDebug  = true;
			$mail->Username   = "followmoneybr@gmail.com";
			$mail->Password   = "";
			$mail->From       = "no-reply@followmoney.com.br";
			$mail->FromName   = "Follow Money";
			$mail->Subject    = "[Follow Money] Bem vindo(a) ao FollowMoney ";
			$mail->CharSet    = "utf-8";
			$mail->AddAddress($to);
			//ail->Body       = "Sua conta no @FollowMoney foi criada com sucesso. <br> Inicie agora mesmo sua gestão financeira acessando: www.followmoney.com.br.";
			//$mail->AltBody    = $mail->Body;
			//$mail->AltBody    = "Sua conta no @FollowMoney foi criada com sucesso. <br> Inicie agora mesmo sua gestão financeira acessando: www.followmoney.com.br.";
			//$mail->isHTML(true);
			$html = file_get_contents(dirname(__FILE__) . '/emailNovoUsuario.html');
			$mail->MsgHTML($html);
			$mail->Send();
	/*
		} catch (phpmailerException $e) {
			$fp = fopen('/var/www/mail_log.txt', 'a');
			fwrite($fp, $e->errorMessage());
			fclose($fp);
		} catch (Exception $e) {
			$fp = fopen('/var/www/mail_log.txt', 'a');
                        fwrite($fp, $e->getMessage());
                        fclose($fp);
		}*/

	}
	
	private static function getMailSender(){
		$mail             = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->Port       = 25;
		$mail->Host       = "smtp.gmail.com";    
		$mail->Username   = "followmoneybr@gmail.com";
		$mail->Password   = "";
		$mail->From       = $mail->Username;
		$mail->FromName   = "Follow Money";
	}
}

?>
