<?
	require_once "/usr/local/zend/apache2/htdocs/eexpensesv2_bmw/lib/php/constantes.php";
	//require_once "C:/wamp/www/eexpensesv2_bmw/lib/php/constantes.php";
	require_once "$RUTA_A/Connections/fwk_db.php";
	require_once "$RUTA_A/functions/Tramite.php";
	
	$cnn = new conexion();
	$tramite = new Tramite();
	
	echo "</div><h1>Generar CSV ANTICIPOS ----> 41201003.T0010360.YXSP PR SPEC</h1>";
	$csv_end = "\r\n";
	$csv_sep = "\t";
	$csv_file = $RUTA_SAP."datas_anticipos.txt";
	$file_log = $RUTA_SAP."backupSAP/backupAnticipos/bitacoraAnticipos".".txt";
	
	if(!file_exists($file_log)){
		fopen($file_log, "x+"); // Crear el archivo x+
	}
	$mensaje = "";
	
	$csv="";
	$sql = "CALL getanticipo();";
	$cnn->ejecutar($sql);
	$sql = "SELECT * FROM resultadoAnticipos;";
	$res = $cnn->consultar($sql);
	
	$aux = true;
	$num_rows = 0;
	while($row=mysql_fetch_array($res)){
		$amount1 = $row['amount'];
		$csv.=date("d.m.Y",strtotime($row['posting_date'])).$csv_sep.
		"MXN".$csv_sep.
		(int)$row['account'].$csv_sep.
		str_pad($amount1, 14, ' ', STR_PAD_LEFT).$csv_sep.
		str_pad(utf8_encode($row['item_text']), 55, ' ', STR_PAD_RIGHT).$csv_sep.
		str_pad(((string)(int)$row['account_2']), 10, ' ', STR_PAD_LEFT).$csv_end;
		$num_rows++;
		
		$id_de_tramite = $row['t_id'];
		
		//$sql2="update eexpensesv2.sv_conceptos_detalle set  svc_enviado_sap='1' where svc_detalle_id=".$row['id'];
		$sql2 = sprintf("UPDATE sv_conceptos_detalle SET svc_enviado_sap = '1' WHERE svc_detalle_tramite = '%s'", $row['t_id']);
		//error_log($sql2);
		$cnn->ejecutar($sql2);
	}
	
	if($csv_sep.mysql_num_rows($res)>0){
		$csv.="TOTAL".$csv_sep.$csv_sep.$csv_sep.$num_rows.$csv_sep.$csv_sep.$csv_sep.$csv_end;

		//Generamos el csv de todos los datos  
		if (!$handle = fopen($csv_file, "w")){
			$mensaje = "Error: \t".date("Y/m/d h:i:s")."\t Cannot open file.\r\n\r\n";
			$file_log = $RUTA_SAP."backupSAP/backupAnticipos/bitacoraAnticipos".".txt";
			$reffichero001 = fopen($file_log, "a+");
			fwrite($reffichero001, $mensaje);
			fclose($reffichero001);
			
			echo "Cannot open file";
			exit;  
		}
		
		if (fwrite($handle, $csv) === FALSE){
			$mensaje = "Error: \t".date("Y/m/d h:i:s")."\t Cannot write to file.\r\n\r\n";
			$file_log = $RUTA_SAP."backupSAP/backupAnticipos/bitacoraAnticipos".".txt";
			$reffichero001 = fopen($file_log, "a+");
			fwrite($reffichero001, $mensaje);
			fclose($reffichero001);
			
			echo "Cannot write to file";  
			exit;  
		}
		fclose($handle);
		$newfile = $RUTA_SAP."41201003.T0010360.YXSP_PR_SPEC";
		
		// L�nea de texto para Control interno
		$mensaje = "Ejecuci�n correcta a las: \t".date("Y/m/d h:i:s")."\t Archivo completo procesado. ";
		
		// Respaldo de reporte generado
		$copiado = false;
		$copydir = $RUTA_SAP."backupSAP/backupAnticipos/EnviadoSAP_Anticipos_".date("Ymd_his").".txt";
		$copiado = copy($csv_file, $copydir);
		
		if($copiado){
			$mensaje .= "Para conocer m�s detalles, refi�rase al archivo EnviadoSAP_Anticipos_".date("Ymd_his").".txt.\r\n\r\n";
		}else{
			$mensaje .= "Error al copiar el archivo: \t ".$newfile."\r\n\r\n";
		}
		
		$file_log = $RUTA_SAP."backupSAP/backupAnticipos/bitacoraAnticipos".".txt";
		$reffichero001 = fopen($file_log, "a+");
		fwrite($reffichero001, $mensaje);
		fclose($reffichero001);
		
		/* Para erradicar la incertidumbre si el archivo es extra�do imediatamente por PIX y este no se ha copiado al respaldo,
		 * ejecutaremos primero la copia y depues renombraremos el archivo, para que sea tomado por PIX.
		*/
		
		unlink($newfile);
		rename($csv_file,$newfile);
		
		// Enviar Reporte de Generaci�n de Archivos
		$tramite->EnviaNotificacionEmailInterfaces(ANTICIPOS, $mensaje, "41201003.T0010360.YXSP_PR_SPEC");
		
		echo "INFO: Archivo csv de ANTICIPOS generado exitosamente!!!";
		
	}else{
		$mensaje = "Ejecuci�n correcta a las: \t".date("Y/m/d h:i:s")."\t Sin datos.\r\n\r\n";
		$file_log = $RUTA_SAP."backupSAP/backupAnticipos/bitacoraAnticipos".".txt";
		$reffichero001 = fopen($file_log, "a+");
		fwrite($reffichero001, $mensaje);
		fclose($reffichero001);
		
		// Enviar Reporte de Generaci�n de Archivos
		$tramite->EnviaNotificacionEmailInterfaces(ANTICIPOS, $mensaje, "41201003.T0010360.YXSP_PR_SPEC");
		
	}
?>
