<?php
require_once("mailgun.php");

function parametroMail ($subject, $destinatario, $body, $cc=NULL){
	$mail = new MailGum();
	
	$datosMail['para']=$destinatario;
    $datosMail['cuerpo']=utf8_decode($body);
    $datosMail['bcc']=$cc;
    
	if(empty($datosMail['bcc'])){
		unset($datosMail['bcc']);
	}
	
	$datosMail['reply_to']="t&e-hod@envasadoras.com.mx";
	$datosMail['subject']=utf8_encode($subject);
	$datosMail['password']="api:key-5wec77ueem3hnnmzg6yn-nbg4bq2vom2";
	$datosMail['correo']="https://api.mailgun.net/v2/rs377.mailgun.org/messages";
	
	$resultado = $mail->enviarMail($datosMail) ? 'El correo ha sido enviado' : 'El correo no ha sido enviado';
	error_log("MENSAJE DE ENVIO:" . $resultado);
}
?>		