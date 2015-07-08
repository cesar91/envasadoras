<?
	require_once "/usr/local/zend/apache2/htdocs/eexpensesv2_bmw/lib/php/constantes.php";
	//require_once "C:/wamp/www/eexpensesv2_bmw/lib/php/constantes.php";
	require_once "$RUTA_A/Connections/fwk_db.php";
	require_once "$RUTA_A/functions/Tramite.php";
	
	$cnn = new conexion();
	$tramite = new Tramite();
	
	echo "</div><h1>Generar CSV VUELOS ----> 41201003.T0010360.YXSP PR REQS</h1>";
	$csv_end = "\r\n";  
	$csv_sep = "\t";
	$csv_file = $RUTA_SAP."datas_vuelos.txt";
	$file_log = $RUTA_SAP."backupSAP/backupVuelos/bitacoraVuelos".".txt";
	
	if(!file_exists($file_log)){
		fopen($file_log, "x+"); // Crear el archivo x+
	}
	$mensaje = "";
	$usuarioMarketing_BMW = 78;
	
	$csv="";
	$sql = "CALL getvuelos();";
	$cnn->ejecutar($sql);
	$sql = "SELECT * FROM resultadoVuelos;";
	$res = $cnn->consultar($sql);
	while($row = mysql_fetch_array($res)){
			$amount1 = $row['amount'];
			$amount2 = $row['amount_2'];
			$imp_total = $row['importe_total'];
			
		/*Se obtiene el tipo de usuario 
		 * $usuario = "";
		 * $sql5=sprintf("SELECT u_usuario FROM usuario WHERE u_id = '%s'",$row['u_id']);
		 * $res4 = mysql_query($sql5);
		 * $row4=mysql_fetch_row($res4);
		 * $usuario = $row4[0];
		*/
		/* Se saca la cuenta de gastos de acuerdo al tipo de usuario
		 * u_id: 78 es para el usuario de Marketing BMW, cualquier otro se tomará como un usuario normal
		 */
		
		$cuenta_de_gastos = 0;
		if($row['u_id'] != $usuarioMarketing_BMW){
			$sql4="SELECT cp_cuenta FROM cat_conceptosbmw WHERE dc_id = '4'";			
		}else{
			$sql4="SELECT cp_cuenta FROM cat_conceptosbmw WHERE dc_id = '27'";
		}		
		$res3 = $cnn->consultar($sql4);
		$row3 = mysql_fetch_row($res3);
		$cuenta_de_gastos = $row3[0];
		
		$csv.=date("d.m.Y",strtotime($row['posting_date'])).$csv_sep.
		"MXN".$csv_sep.
		$cuenta_de_gastos.$csv_sep./*$row['ACCOUNT'].$csv_sep.*/
		str_pad(((float)$amount1), 14, ' ', STR_PAD_LEFT).$csv_sep.
		str_pad($row['cost_center'], 10, ' ', STR_PAD_LEFT).$csv_sep.
		str_pad($row['checkout'].' '.utf8_encode($row['item_text']), 55, ' ', STR_PAD_RIGHT).$csv_sep.
		$row['account2'].$csv_sep.
		str_pad($amount2, 14, ' ', STR_PAD_LEFT).$csv_sep.
		str_pad(((float)$imp_total), 14, ' ', STR_PAD_LEFT).$csv_end;
		
		$sql2 = sprintf("UPDATE sv_itinerario 
			INNER JOIN solicitud_viaje ON sv_id = svi_solicitud
			INNER JOIN tramites ON t_id = sv_tramite 			
			SET svi_enviado_sap = '1'
			WHERE t_etapa_actual = '%s'
			AND t_flujo = '%s'", SOLICITUD_ETAPA_COMPRADA, FLUJO_SOLICITUD);
		//error_log($sql2);
		$cnn->ejecutar($sql2);
	}
	if($csv_sep.mysql_num_rows($res)>0){
		$csv.="TOTAL".$csv_sep.$csv_sep.$csv_sep.mysql_num_rows($res).$csv_sep.$csv_sep.$csv_sep.$csv_sep.$csv_end;

		//Generamos el csv de todos los datos  
		if (!$handle = fopen($csv_file, "w")){
			$mensaje = "Error: \t".date("Y/m/d h:i:s")."\t Cannot open file.\r\n\r\n";
			$file_log = $RUTA_SAP."backupSAP/backupVuelos/bitacoraVuelos".".txt";
			$reffichero001 = fopen($file_log, "a+");
			fwrite($reffichero001, $mensaje);
			fclose($reffichero001);
			
			echo "Cannot open file";  
			exit;  
		}  

		if (fwrite($handle, $csv) === FALSE){
			$mensaje = "Error: \t".date("Y/m/d h:i:s")."\t Cannot write to file.\r\n\r\n";
			$file_log = $RUTA_SAP."backupSAP/backupVuelos/bitacoraVuelos".".txt";
			$reffichero001 = fopen($file_log, "a+");
			fwrite($reffichero001, $mensaje);
			fclose($reffichero001);
			
			echo "Cannot write to file";  
			exit;  
		}
		fclose($handle);
		$newfile=$RUTA_SAP."41201003.T0010360.YXSP_PR_REQS";
		
		// Línea de texto para Control interno
		$mensaje = "Ejecución correcta a las: \t".date("Y/m/d h:i:s")."\t Archivo completo procesado. ";
		
		// Respaldo de reporte generado
		$copiado = false;
		$copydir = $RUTA_SAP."backupSAP/backupVuelos/EnviadoSAP_Vuelos_".date("Ymd_his").".txt";
		$copiado = copy($csv_file, $copydir);
		
		if($copiado){
			$mensaje .= "Para conocer más detalles, refiérase al archivo EnviadoSAP_Vuelos_".date("Ymd_his").".txt.\r\n\r\n";
		}else{
			$mensaje .= "Error al copiar el archivo: \t ".$newfile."\r\n\r\n";
		}
		
		$file_log = $RUTA_SAP."backupSAP/backupVuelos/bitacoraVuelos".".txt";
		$reffichero001 = fopen($file_log, "a+");
		fwrite($reffichero001, $mensaje);
		fclose($reffichero001);
		
		/* Para erradicar la incertidumbre si el archivo es extraído imediatamente por PIX y este no se ha copiado al respaldo,
		 * ejecutaremos primero la copia y depues renombraremos el archivo, para que sea tomado por PIX.
		 */
		
		unlink($newfile);
		rename($csv_file,$newfile);
		
		// Enviar Reporte de Generación de Archivos
		$tramite->EnviaNotificacionEmailInterfaces(VUELOS, $mensaje, "41201003.T0010360.YXSP_PR_REQS");
		
		echo "INFO: Archivo csv de VUELOS generado exitosamente!!!";
		
	}else{
		$mensaje = "Ejecución correcta a las: \t".date("Y/m/d h:i:s")."\t Sin datos.\r\n\r\n";
		$file_log = $RUTA_SAP."backupSAP/backupVuelos/bitacoraVuelos".".txt";
		$reffichero001 = fopen($file_log, "a+");
		fwrite($reffichero001, $mensaje);
		fclose($reffichero001);
		
		// Enviar Reporte de Generación de Archivos
		$tramite->EnviaNotificacionEmailInterfaces(VUELOS, $mensaje, "41201003.T0010360.YXSP_PR_REQS");
	}
?>
