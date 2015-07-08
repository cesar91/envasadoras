<?php

class MailGum{
	
	/**
	 * @param $datosMail, es un arreglo donde debe de tener el idEmpresa de la empresa, para obtener los datos de contacto,
	 * 		 	los siguientes datos nos indican el detalle del mail, los valores contenidos pueden ser:
	 * 
	 *  
	 * 		Parameter	Description
	 * 		from	    Mail al cual se debe de responder o bien el mail a nombre de quien se desea enviar.
	 * 		to			Mail o mail's de los destinatarios. Ejemplo: "Bob <bob@host.com>". Si quieres enviar a varios destinatarios se debe de usar una coma
	 * 					para separarlos.
	 * 		cc			Carbon copy o Copia de Carbón, tiene las mismas reglas que 'to'.
	 * 		bcc			Blind carbon copy  o Copia de carbón oculta, tiene las mismas reglas que 'to'.
	 * 		subject		Asunto del Mensaje.
	 * 		text		Cuerpo del mensaje, pero en version texto.
	 * 		html		Cuerpo del mensaje. pero en version html.
	 *		attachment	Archivo adjunto. Se pueden enviar distintos archivos adjuntos, solo cuidar en no repetir el numero de ajunto. Para el caso de FACE
	 * 					se sugiere que todos los archivos a enviar se encuentre en la siguiente ruta: face55/face5/f4/extensiones/tmp
	 * 
	 * 
	 * 		documentación tomado de:  http://documentation.mailgun.com/api-sending.html
	 * 
	 * 
	 * 		Ejemplo de arreglo:
	 * 
	 * 		'from' => 'Excited User <me@samples.mailgun.org>',
	 *  	'to' => 'obukhov.sergey.nickolayevich@yandex.ru',
	 * 		'cc' => 'serobnic@mail.ru',
	 * 		'bcc' => 'sergeyo@profista.com',
	 * 		'subject' => 'Hello',
	 * 		'text' => 'Testing some Mailgun awesomness!',
	 * 		'html' => '<html>HTML version of the body</html>',
	 * 		'attachment[1]' => '@/path/to/file.txt',
	 * 		'attachment[2]' => '@/path/to/pic.jpg
	 * 
	 *  
	 *		Funcion attachment (Face 5)
	*private  function attachment(&$datosMail,$pathQR="",$tmp=""){
	*		$contadorAdjuntos = 1;
	*
	*		if (isset($datosMail['xml']) && !empty($datosMail['xml'])) {
	*			$nombreXML = $datosMail['serie'].$datosMail['folio'].".xml";
	*			$archivoXML = "../f4/extensiones/tmp/" . $nombreXML;
	*			$fp = fopen($archivoXML, "w+");
	*			fputs($fp, base64_decode($datosMail['facturaTimbrada']));
	*			fclose($fp);
	*			$datosMail['attachment['.$contadorAdjuntos.']']  = '@'.$archivoXML;
	*			$contadorAdjuntos++;
	*		}
	*	
	*		if (isset($datosMail['pdf']) && !empty($datosMail['pdf'])) {
	*			$formato="";
	*			$colname_idempresa=$datosMail['idEmpresa'];
	*			$formato='../f4/extensiones/formatopdfcfdi';
	*			if($colname_idempresa==2){
	*				$formato='../f4/extensiones/formatopdfcfdiac';
	*			}
	*			log_action("FORMATO Envio Mail: ".$formato.'.php');
	*			require_once($formato.'.php');
	*			
	*			$archivos=creaPDF($datosMail['idFactura'],FALSE);
	*			log_action(print_r($archivos[1],TRUE));
	*			$datosMail['attachment['.$contadorAdjuntos.']']  = '@'.$archivos[1];
	*			$contadorAdjuntos++;
	*		}
	*	}	 
	*
	*
	*
	*/
	
	function enviarMail($datosMail){

		//$this->attachment($datosMail);
		
			//$datosMail['idEmpresa'] = $datosMail['idEmpresa'];
			$datosMail['to']        = $datosMail['para'];
			$datosMail['html']      = nl2br($datosMail['cuerpo']);
			$datosMail['bcc']       = $datosMail['bcc'];
			if(empty($datosMail['bcc'])){
				unset($datosMail['bcc']);
			}
			$datosMail['from']      = $datosMail['reply_to'];
			$datosMail['subject']   = $datosMail['subject'];
			$datosMail['password']  = $datosMail['password'];
			$datosMail['correo']    = $datosMail['correo'];	

		return $this->realizarEnvio($datosMail);
	}
	
	private function realizarEnvio($datosMail){
		$ch = curl_init();
  		curl_setopt($ch, CURLOPT_HTTPAUTH,       CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD,        $datosMail['password']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  'POST');
		curl_setopt($ch, CURLOPT_URL,            $datosMail['correo']);
		curl_setopt($ch, CURLOPT_POSTFIELDS,     $datosMail);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$result = curl_exec($ch);
		curl_close($ch);
		$resultJson = json_decode($result);
		if($resultJson->{'message'}==='Queued. Thank you.'){
			return TRUE;
		}else{
			return FALSE;
		}
		
	}
	
	
}
?>