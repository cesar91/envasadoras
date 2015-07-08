<?php
require_once("$RUTA_A/lib/php/class.phpmailer.php");
require_once("$RUTA_A/lib/php/class.pop3.php");
require_once("$RUTA_A/lib/php/constantes.php");

function sendMail($_msubject, $_mbody,$_mto,$_mname){
	global $SMTP_FROMNAME;
$pop = new POP3();
$pop->Authorise('envasadoras.com.mx', 110, 30, 't&e-hod', 'tyeh2014', 1);	
	
$mail = new PHPMailer();

//Luego tenemos que iniciar la validación por SMTP:
$mail->IsSMTP();
$mail->SMTPAuth = true;
$mail->Host = "envasadoras.com.mx"; // SMTP a utilizar. Por ej. smtp.elserver.com
//$mail->Username = "t&e-hod"; // Correo completo a utilizar
//$mail->Password = "tyeh2014"; // Contraseña
$mail->Port = 25; // Puerto a utilizar
$mail->From = "t&e-hod@envasadoras.com.mx"; // Desde donde enviamos (Para mostrar)
$mail->FromName = "Sistema de Gastos de Viaje";



$cnt=0;
if(stristr($_mto,",")===FALSE){ //EVALUAMOS SI TIENE , PARA SACAR A LOS USUARIOS QUE SE MANDARA EL MAIL :)
	$mail->AddAddress($_mto,$_mname);
}
else{
$aux=explode(",",$_mto);
	foreach ($aux as $direccion){
		$mail->AddAddress(trim($direccion),$_mname);
		$cnt++;
	}
		
}



$mail->WordWrap = 50;                              // set word wrap
$mail->IsHTML(TRUE);                               // send as HTML

$mail->Subject  =  $_msubject;
$mail->Body     =  $_mbody;

if(!$mail->Send())
{
	echo "Mailer Error: " . $mail->ErrorInfo;
   return(-1);

}
return(0);

//echo "    Mensaje Enviado.\n";

}
?>
