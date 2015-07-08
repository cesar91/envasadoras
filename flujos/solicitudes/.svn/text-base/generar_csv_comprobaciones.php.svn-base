<?
	require_once "/usr/local/zend/apache2/htdocs/eexpensesv2_bmw/lib/php/constantes.php";
	//require_once "C:/wamp/www/eexpensesv2_bmw/lib/php/constantes.php";
	require_once "$RUTA_A/Connections/fwk_db.php";
	require_once "$RUTA_A/functions/Tramite.php";
	
	$tramite = new Tramite();
	
	if($con = mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWORD))
		mysql_select_db($MYSQL_DATABASE);
	else
		die("Error de conexxion.");
	
	echo "</div><h1>Generar CSV COMPROBACIONES GASTOS ----> 41201003.T0010360.YXSP PR EXPS</h1>";
	$csv_end = "\r\n";  
	$csv_sep = "\t";
	$csv_file = $RUTA_SAP."datas_comprobaciones.txt";
	$file_log = $RUTA_SAP."backupSAP/backupComprobaciones/bitacoraComprobaciones".".txt";
	
	if(!file_exists($file_log)){
		fopen($file_log, "x+"); // Crear el archivo x+
	}
	$mensaje = "";
	
	$csv="";
	$text = "";
	$sql="CALL getcomprobacionesgastos();";
	mysql_query($sql);
	$sql="SELECT * FROM resultadoComprobaciones;";
	$res=mysql_query($sql);
	$i=0;
	$comprobacionesV = 0;
	$idDetalles = array();
	while($row=mysql_fetch_array($res)){
		if($i == 0){
			$aux = $row['item_text'];

			$csv.=
			date("d.m.Y",strtotime($row['posting_date'])).$csv_sep.
			"MXN".$csv_sep.
			str_pad(utf8_encode($row['item_text']), 55, ' ', STR_PAD_RIGHT).
			$csv_sep.
			$csv_sep.
			$csv_sep.
			$csv_sep.
			$csv_end;
			
			$i++;
		}else{
			if($aux != $row['item_text']){
			$sql_iva16="CALL iva(".$t_anterior.",16,33);";
				//error_log("IVA 16: ".$sql_iva16);
				mysql_query($sql_iva16);
				$sql_iva16="SELECT * FROM resultado2;";
				$res_iva16=mysql_query($sql_iva16);
				while($row_iva16=mysql_fetch_array($res_iva16)){
					//error_log("Amount IVA 16: ".$row_iva16['amount']);
					if($row_iva16['amount'] != ""){
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"I".$csv_sep.
						$row_iva16['account'].$csv_sep.
						str_pad($row_iva16['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;						
						$i++;
					}
				}
				/*$sql_iva11="CALL iva(".$t_anterior.",11,34);";
				error_log("IVA 11: ".$sql_iva11);
				mysql_query($sql_iva11);
				$sql_iva11="SELECT * FROM resultado2;";
				$res_iva11=mysql_query($sql_iva11);
				while($row_iva11=mysql_fetch_array($res_iva11)){
					error_log("Amount IVA 11: ".$row_iva11['amount']);
					if($row_iva11['amount'] != ""){
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"I".$csv_sep.
						$row_iva11['account'].$csv_sep.
						str_pad($row_iva11['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;						
						$i++;
					}
				}*/
				//amnt
				$sql_AA="CALL amnt(".$t_anterior.");";
				//error_log("AMEX-Anticipos: ".$sql_AA);
				mysql_query($sql_AA);
				$sql_iva11="SELECT * FROM resultado2;";
				$res_iva11=mysql_query($sql_iva11);
				while($row_iva11=mysql_fetch_array($res_iva11)){
					//error_log("Amount AMEX-Anticipos: ".$row_iva11['amount']);
					if($row_iva11['amount'] != ""){
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"A".$csv_sep.
						$row_iva11['account'].$csv_sep.
						//$csv_sep.$csv_sep.
						str_pad($row_iva11['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;						
						$i++;
					}
				}
				//reem
				$sql_iva11="CALL rembolso(".$t_anterior.");";
				//error_log("Reembolso: ".$sql_iva11);
				mysql_query($sql_iva11);
				$sql_iva11="SELECT * FROM resultado2;";
				$res_iva11=mysql_query($sql_iva11);
				while($row_iva11=mysql_fetch_array($res_iva11)){
					//error_log("Amount Reembolso: ".$row_iva11['amount']);
					if($row_iva11['amount'] != ""){
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"E".$csv_sep.
						$row_iva11['account'].$csv_sep.
						//$csv_sep.$csv_sep.
						str_pad($row_iva11['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;						
						$i++;
					}
				}				
				//P
				$sql_iva11="CALL getcuentap(".$t_anterior.");";//300371
				//error_log("P: ".$sql_iva11);
				mysql_query($sql_iva11);
				$sql_iva11="SELECT * FROM resultado2;";
				$res_iva11=mysql_query($sql_iva11);
				$num_rowsCVP = mysql_num_rows($res_iva11);
				//error_log($num_rowsCVP);
				while($row_iva11=mysql_fetch_array($res_iva11)){
					//error_log("Amount P: ".$row_iva11['amount']);
					if($row_iva11['amount'] != ""){
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"P".$csv_sep.
						$row_iva11['account'].$csv_sep.
						str_pad($row_iva11['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;							
						$i++;
					}else{
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"P".$csv_sep.
						$row_iva11['account'].$csv_sep.
						str_pad("0.00", 14, ' ', STR_PAD_LEFT).$csv_end;
						$i++;
					}
				}
				
				//B --> Se modificará por una A
				$sql_iva11="CALL getcuentab(".$t_anterior.");";//300371
				//error_log("B: ".$sql_iva11);
				mysql_query($sql_iva11);
				$sql_iva11="SELECT * FROM resultado2;";
				$res_iva11=mysql_query($sql_iva11);
				$num_rowsCVB = mysql_num_rows($res_iva11);
				//error_log($num_rowsCVB);
				while($row_iva11=mysql_fetch_array($res_iva11)){
					//error_log("Amount B: ".$row_iva11['amount']);
					if($row_iva11['amount'] != ""){
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"B".$csv_sep.
						$row_iva11['account'].$csv_sep.
						str_pad($row_iva11['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;				
						$i++;
					}else{
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"B".$csv_sep.
						$row_iva11['account'].$csv_sep.
						str_pad("0.00", 14, ' ', STR_PAD_LEFT).$csv_end;
						$i++;
					}
				}
				
				$csv.=
				date("d.m.Y",strtotime($row['posting_date'])).$csv_sep.
				"MXN".$csv_sep.
				str_pad(utf8_encode($row['item_text']), 55, ' ', STR_PAD_RIGHT).
				$csv_sep.
				$csv_sep.
				$csv_sep.
				$csv_sep.
				$csv_end;
				
				$i++;
			}
		}
		$aux = $row['item_text'];
		$t_anterior = $row['t_id'];

		$csv.=
		$csv_sep.
		$csv_sep.
		$csv_sep.
//		str_pad($row['ID_REGISTRO'], 1, ' ', STR_PAD_LEFT).$csv_sep.
		"G".$csv_sep.
		$row['account'].$csv_sep.
		str_pad($row['amount'], 14, ' ', STR_PAD_LEFT).$csv_sep.
		str_pad($row['cost_center'], 10, ' ', STR_PAD_LEFT).$csv_end;
		
		// Guardamos en un arreglo los id's de los detalles de las comprobaciones que serán enviadas a SAP
		$idDetalles[$i] = $row['id'];		
		$i++;
	}
				$sql_iva16="CALL iva(".$t_anterior.",16,33);";
				//error_log("IVA 16: ".$sql_iva16);
				mysql_query($sql_iva16);
				$sql_iva16="SELECT * FROM resultado2;";
				$res_iva16=mysql_query($sql_iva16);
				while($row_iva16=mysql_fetch_array($res_iva16)){
					//error_log("Amount IVA 16: ".$row_iva16['amount']);
					if($row_iva16['amount'] != ""){
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"I".$csv_sep.
						$row_iva16['account'].$csv_sep.
						str_pad($row_iva16['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;						
						$i++;
					}
				}
				/*$sql_iva11="CALL iva(".$t_anterior.",11,34);";
				error_log("IVA 11: ".$sql_iva11);
				mysql_query($sql_iva11);
				$sql_iva11="SELECT * FROM resultado2;";
				$res_iva11=mysql_query($sql_iva11);
				while($row_iva11=mysql_fetch_array($res_iva11)){
					error_log("Amount IVA 11: ".$row_iva11['amount']);
					if($row_iva11['amount'] != ""){
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"I".$csv_sep.
						$row_iva11['account'].$csv_sep.
						str_pad($row_iva11['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;						
						$i++;
					}
				}*/
				//amnt
				$sql_AA="CALL amnt(".$t_anterior.");";
				//error_log("AMEX-Anticipos: ".$sql_AA);
				mysql_query($sql_AA);
				$sql_iva11="SELECT * FROM resultado2;";
				$res_iva11=mysql_query($sql_iva11);
				while($row_iva11=mysql_fetch_array($res_iva11)){
					//error_log("Amount AMEX-Anticipos: ".$row_iva11['amount']);
					if($row_iva11['amount'] != ""){
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"A".$csv_sep.
						$row_iva11['account'].$csv_sep.
						//$csv_sep.$csv_sep.
						str_pad($row_iva11['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;						
						$i++;
					}
				}				
				//reem
				$sql_iva11="CALL rembolso(".$t_anterior.");";
				//error_log("Reembolso: ".$sql_iva11);
				mysql_query($sql_iva11);
				$sql_iva11="SELECT * FROM resultado2;";
				$res_iva11=mysql_query($sql_iva11);
				while($row_iva11=mysql_fetch_array($res_iva11)){
					//error_log("Amount Reembolso: ".$row_iva11['amount']);
					if($row_iva11['amount'] != ""){
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"E".$csv_sep.
						$row_iva11['account'].$csv_sep.
						//$csv_sep.$csv_sep.
						str_pad($row_iva11['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;						
						$i++;
					}
				}				
				//P
				$sql_iva11="CALL getcuentap(".$t_anterior.");";//300371
				//error_log("P: ".$sql_iva11);
				mysql_query($sql_iva11);
				$sql_iva11="SELECT * FROM resultado2;";
				$res_iva11=mysql_query($sql_iva11);
				$num_rowsCVP = mysql_num_rows($res_iva11);
				//error_log($num_rowsCVP);
				while($row_iva11=mysql_fetch_array($res_iva11)){
					//error_log("Amount P: ".$row_iva11['amount']);
					if($row_iva11['amount'] != ""){
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"P".$csv_sep.
						$row_iva11['account'].$csv_sep.
						str_pad($row_iva11['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;							
						$i++;
					}else{
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"P".$csv_sep.
						$row_iva11['account'].$csv_sep.
						str_pad("0.00", 14, ' ', STR_PAD_LEFT).$csv_end;
						$i++;
					}
				}
				
				//B --> Se modificará por una A
				$sql_iva11="CALL getcuentab(".$t_anterior.");";//300371
				//error_log("B: ".$sql_iva11);
				mysql_query($sql_iva11);
				$sql_iva11="SELECT * FROM resultado2;";
				$res_iva11=mysql_query($sql_iva11);
				$num_rowsCVB = mysql_num_rows($res_iva11);
				//error_log($num_rowsCVB);
				while($row_iva11=mysql_fetch_array($res_iva11)){
					//error_log("Amount B: ".$row_iva11['amount']);
					if($row_iva11['amount'] != ""){
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"B".$csv_sep.
						$row_iva11['account'].$csv_sep.
						str_pad($row_iva11['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;				
						$i++;
					}else{
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"B".$csv_sep.
						$row_iva11['account'].$csv_sep.
						str_pad("0.00", 14, ' ', STR_PAD_LEFT).$csv_end;
						$i++;
					}
				}
	
	// Actualizamos el campo del detalle de la comprobación previamente guardado en el arreglo
	$sql = "SELECT * FROM resultadoComprobaciones;";
	$res = mysql_query($sql);
	$num_comprobaciones = mysql_num_rows($res);
	//error_log("--------------------->>>>>>>>>>>>>>".$num_comprobaciones);
	while($row = mysql_fetch_assoc($res)){
		//$sql2 = "UPDATE detalle_comprobacion SET dc_enviado_sap = '1' WHERE dc_id = '".$row['id']."'";
		$sql2 = sprintf("UPDATE detalle_comprobacion 
				INNER JOIN comprobaciones ON dc_comprobacion = co_id 
				INNER JOIN tramites ON t_id = co_mi_tramite 
				SET dc_enviado_sap = '1' 
				WHERE t_etapa_actual = '%s' 
				AND t_flujo = '%s'", COMPROBACION_ETAPA_APROBADA, FLUJO_COMPROBACION);
		//error_log($sql2);
		mysql_query($sql2);
	}
	
	if($csv_sep.mysql_num_rows($res)>0){
		$comprobacionesV = $i;
		$text = "INFO: Archivo csv de COMPROBACIONES GASTOS generado exitosamente!!!";
		echo $text;
		$mensaje = "Ejecución correcta a las: \t".date("Y/m/d h:i:s")."\t Comprobaciones de Viajes procesadas correctamente. ";
	}else{
		$comprobacionesV = 0;
		$mensaje = "Ejecución correcta a las: \t".date("Y/m/d h:i:s")."\t Sin datos de Comprobaciones de Viajes. ";
		$csv = "";
	}
	
	echo "</div><h1>Generar CSV COMPROBACIONES INVITACION ----> 41201003.T0010360.YXSP PR EXPS INV</h1>";
	//$csv_file = "../../datos/InterfazSAP/datas_comprobaciones.txt";
	//$csv_file = $RUTA_SAP."datas_invitacion.txt";
	$res = "";
	$row = "";
	$res_iva11 = "";
	$res_iva16 = "";
	$row_iva11 = "";
	$row_iva16 = "";
	$sql="call getcomprobacionesinvitacion();";
	mysql_query($sql);
	$sql="select * from resultadoComprobacionesInvitacion;";
	$res=mysql_query($sql);
	$num_rowsCI = mysql_num_rows($res); // Número de comprobaciones obtenidas para ser enviadas a SAP
	$i=0;
	$comprobacionesI = 0;
	while($row=mysql_fetch_array($res)){
		if($i == 0){
			if($num_comprobaciones > 0){
				// Colocamos una línea en blanco, para indicar que iniciarán las comprobaciones de invitación.
				$csv.= $csv_end;
				$comprobacionesV = $comprobacionesV + 1;
			}
			
			$aux = $row['item_text'];
	
			$csv.=
			date("d.m.Y",strtotime($row['posting_date'])).$csv_sep.
			"MXN".$csv_sep.
			str_pad(utf8_encode($row['item_text']), 55, ' ', STR_PAD_RIGHT)." INVITACION".
			$csv_sep.
			$csv_sep.
			$csv_sep.
			$csv_sep.
			$csv_end;
				
			$i++;
		}else{
			if($aux != $row['item_text']){
				$sql_iva16="CALL ivainvitacion(".$t_anterior.",16,33);";
				//error_log("IVA 16: ".$sql_iva16);
				mysql_query($sql_iva16);
				$sql_iva16="SELECT * FROM resultado2;";
				$res_iva16=mysql_query($sql_iva16);
				while($row_iva16=mysql_fetch_array($res_iva16)){
					//error_log("Amount IVA 16: ".$row_iva16['amount']);
					if($row_iva16['amount'] != ""){
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"I".$csv_sep.
						$row_iva16['account'].$csv_sep.
						str_pad($row_iva16['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;
						$i++;
					}
				}
				//amnt
				$sql_AA="CALL amntinv(".$t_anterior.");";
				//error_log("AMEX-Anticipos: ".$sql_AA);
				mysql_query($sql_AA);
				$sql_iva11="SELECT * FROM resultado2;";
				$res_iva11=mysql_query($sql_iva11);
				while($row_iva11=mysql_fetch_array($res_iva11)){
					//error_log("Amount AMEX-Anticipos: ".$row_iva11['amount']);
					//if($row_iva11['amount'] != ""){
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"A".$csv_sep.
						$row_iva11['account'].$csv_sep.
						//$csv_sep.$csv_sep.
						str_pad($row_iva11['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;
						$i++;
					//}
				}
				//reem
				$sql_iva11="CALL rembolsoinv(".$t_anterior.");";
				//error_log("Reembolso: ".$sql_iva11);
				mysql_query($sql_iva11);
				$sql_iva11="SELECT * FROM resultado2;";
				$res_iva11=mysql_query($sql_iva11);
				while($row_iva11=mysql_fetch_array($res_iva11)){
					//error_log("Amount Reembolso: ".$row_iva11['amount']);
					if($row_iva11['amount'] != ""){
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"E".$csv_sep.
						$row_iva11['account'].$csv_sep.
						//$csv_sep.$csv_sep.
						str_pad($row_iva11['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;
						$i++;
					}
				}
				//P
				$sql_iva11="CALL getcuentapinv(".$t_anterior.");";//300371
				//error_log("P: ".$sql_iva11);
				mysql_query($sql_iva11);
				$sql_iva11="SELECT * FROM resultado2;";
				$res_iva11=mysql_query($sql_iva11);
				$num_rowsCIP = mysql_num_rows($res_iva11);
				//($num_rowsCIP);
				while($row_iva11=mysql_fetch_array($res_iva11)){
					//error_log("Amount P: ".$row_iva11['amount']);
					if($row_iva11['amount'] != ""){
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"P".$csv_sep.
						$row_iva11['account'].$csv_sep.
						str_pad($row_iva11['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;							
						$i++;
					}else{
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"P".$csv_sep.
						$row_iva11['account'].$csv_sep.
						str_pad("0.00", 14, ' ', STR_PAD_LEFT).$csv_end;
						$i++;
					}
				}
				
				//B --> Se modificará por una A
				$sql_iva11="CALL getcuentabinv(".$t_anterior.");";//300371
				//error_log("B: ".$sql_iva11);
				mysql_query($sql_iva11);
				$sql_iva11="SELECT * FROM resultado2;";
				$res_iva11=mysql_query($sql_iva11);
				$num_rowsCIB = mysql_num_rows($res_iva11);
				//error_log($num_rowsCIB);
				while($row_iva11=mysql_fetch_array($res_iva11)){
					//error_log("Amount B: ".$row_iva11['amount']);
					if($row_iva11['amount'] != ""){
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"B".$csv_sep.
						$row_iva11['account'].$csv_sep.
						str_pad($row_iva11['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;				
						$i++;
					}else{
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"B".$csv_sep.
						$row_iva11['account'].$csv_sep.
						str_pad("0.00", 14, ' ', STR_PAD_LEFT).$csv_end;
						$i++;
					}
				}
	
				$csv.=
				date("d.m.Y",strtotime($row['posting_date'])).$csv_sep.
				"MXN".$csv_sep.
				str_pad(utf8_encode($row['item_text']), 55, ' ', STR_PAD_RIGHT)." INVITACION".
				$csv_sep.
				$csv_sep.
				$csv_sep.
				$csv_sep.
				$csv_end;
	
				$i++;
			}
		}
		$aux = $row['item_text'];
		$t_anterior = $row['t_id'];
	
		$csv.=
		$csv_sep.
		$csv_sep.
		$csv_sep.
		"G".$csv_sep.
		$row['account'].$csv_sep.
		str_pad($row['amount'], 14, ' ', STR_PAD_LEFT).$csv_sep.
		str_pad($row['cost_center'], 10, ' ', STR_PAD_LEFT).$csv_end;
	
		$i++;
	}
	
	$sql_iva16="CALL ivainvitacion(".$t_anterior.",16,33);";
	//error_log("IVA 16: ".$sql_iva16);
	mysql_query($sql_iva16);
	$sql_iva16="SELECT * FROM resultado2;";
	$res_iva16=mysql_query($sql_iva16);
	while($row_iva16=mysql_fetch_array($res_iva16)){
		//error_log("Amount IVA 16: ".$row_iva16['amount']);
		if($row_iva16['amount'] != ""){
			$csv.=
			$csv_sep.
			$csv_sep.
			$csv_sep.
			"I".$csv_sep.
			$row_iva16['account'].$csv_sep.
			str_pad($row_iva16['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;
			$i++;
		}
	}
	//amnt
	$sql_AA="CALL amntinv(".$t_anterior.");";
	//error_log("AMEX-Anticipos: ".$sql_AA);
	mysql_query($sql_AA);
	$sql_iva11="SELECT * FROM resultado2;";
	$res_iva11=mysql_query($sql_iva11);
	while($row_iva11=mysql_fetch_array($res_iva11)){
		//error_log("Amount AMEX-Anticipos: ".$row_iva11['amount']);
		//if($row_iva11['amount'] != ""){
			$csv.=
			$csv_sep.
			$csv_sep.
			$csv_sep.
			"A".$csv_sep.
			$row_iva11['account'].$csv_sep.
			//$csv_sep.$csv_sep.
			str_pad($row_iva11['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;
			$i++;
		//}
	}
	//reem
	$sql_iva11="CALL rembolsoinv(".$t_anterior.");";
	//error_log("Reembolso: ".$sql_iva11);
	mysql_query($sql_iva11);
	$sql_iva11="SELECT * FROM resultado2;";
	$res_iva11=mysql_query($sql_iva11);
	while($row_iva11=mysql_fetch_array($res_iva11)){
		//error_log("Amount Reembolso: ".$row_iva11['amount']);
		if($row_iva11['amount'] != ""){
			$csv.=
			$csv_sep.
			$csv_sep.
			$csv_sep.
			"E".$csv_sep.
			$row_iva11['account'].$csv_sep.
			//$csv_sep.$csv_sep.
			str_pad($row_iva11['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;
			$i++;
		}
	}
	//P
	$sql_iva11="CALL getcuentapinv(".$t_anterior.");";//300371
	//error_log("P: ".$sql_iva11);
	mysql_query($sql_iva11);
	$sql_iva11="SELECT * FROM resultado2;";
	$res_iva11=mysql_query($sql_iva11);
	$num_rowsCIP = mysql_num_rows($res_iva11);
	//($num_rowsCIP);
	while($row_iva11=mysql_fetch_array($res_iva11)){
		//error_log("Amount P: ".$row_iva11['amount']);
		if($row_iva11['amount'] != ""){
			$csv.=
			$csv_sep.
			$csv_sep.
			$csv_sep.
			"P".$csv_sep.
			$row_iva11['account'].$csv_sep.
			str_pad($row_iva11['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;							
			$i++;
		}else if($num_rowsCI > 0 && $row_iva11['amount'] == ""){
			$csv.=
			$csv_sep.
			$csv_sep.
			$csv_sep.
			"P".$csv_sep.
			$row_iva11['account'].$csv_sep.
			str_pad("0.00", 14, ' ', STR_PAD_LEFT).$csv_end;
			$i++;
		}
	}
	//B --> Se modificará por una A
	$sql_iva11="CALL getcuentabinv(".$t_anterior.");";//300371
	//error_log("B: ".$sql_iva11);
	mysql_query($sql_iva11);
	$sql_iva11="SELECT * FROM resultado2;";
	$res_iva11=mysql_query($sql_iva11);
	$num_rowsCIB = mysql_num_rows($res_iva11);
	//error_log($num_rowsCIB);
	while($row_iva11=mysql_fetch_array($res_iva11)){
		//error_log("Amount B: ".$row_iva11['amount']);
		if($row_iva11['amount'] != ""){
			$csv.=
			$csv_sep.
			$csv_sep.
			$csv_sep.
			"B".$csv_sep.
			$row_iva11['account'].$csv_sep.
			str_pad($row_iva11['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;				
			$i++;
		}else if($num_rowsCI > 0 && $row_iva11['amount'] == ""){
			$csv.=
			$csv_sep.
			$csv_sep.
			$csv_sep.
			"B".$csv_sep.
			$row_iva11['account'].$csv_sep.
			str_pad("0.00", 14, ' ', STR_PAD_LEFT).$csv_end;
			$i++;
		}
	}

	// Actualizamos el campo del detalle de la comprobación previamente guardado en el arreglo
	$sql="select * from resultadoComprobacionesInvitacion;";
	$res = mysql_query($sql);
	$num_comprobacionesinv = mysql_num_rows($res);
	//error_log("--------------------->>>>>>>>>>>>>>".$num_comprobaciones);
	while($row = mysql_fetch_assoc($res)){
		//$sql2 = "UPDATE detalle_comprobacion_invitacion SET dc_enviado_sap = '1' WHERE dc_id = '".$row['id']."'";
		$sql2 = sprintf("UPDATE detalle_comprobacion_invitacion 
				INNER JOIN comprobacion_invitacion ON dc_comprobacion = co_id 
				INNER JOIN tramites ON t_id = co_mi_tramite 
				SET dc_enviado_sap = '1' 
				WHERE t_etapa_actual = '%s' 
				AND t_flujo = '%s'", COMPROBACION_INVITACION_ETAPA_APROBADA, FLUJO_COMPROBACION_INVITACION);
		//error_log($sql2);
		mysql_query($sql2);
	}
	
	
	if($csv_sep.mysql_num_rows($res) > 0 || $num_comprobaciones > 0){
		
		$csv.="TOTAL".$csv_sep.$csv_sep.$csv_sep.$csv_sep.$csv_sep.($i+$comprobacionesV).$csv_sep.$csv_end;
	
		//Generamos el csv de todos los datos
		if (!$handle = fopen($csv_file, "w")){
			$mensaje = "Error: \t".date("Y/m/d h:i:s")."\t Cannot open file.\r\n\r\n";
			$file_log = $RUTA_SAP."backupSAP/backupComprobaciones/bitacoraComprobaciones".".txt";
			$reffichero001 = fopen($file_log, "a+");
			fwrite($reffichero001, $mensaje);
			fclose($reffichero001);
			
			echo "Cannot open file";
			exit;
		}
		
		if (fwrite($handle, $csv) === FALSE){
			$mensaje = "Error: \t".date("Y/m/d h:i:s")."\t Cannot write to file.\r\n\r\n";
			$file_log = $RUTA_SAP."backupSAP/backupComprobaciones/bitacoraComprobaciones".".txt";
			$reffichero001 = fopen($file_log, "a+");
			fwrite($reffichero001, $mensaje);
			fclose($reffichero001);
			
			echo "Cannot write to file";
			exit;
		}
		fclose($handle);
		$newfile = $RUTA_SAP."41201003.T0010360.YXSP_PR_EXPS";
		
		if($csv_sep.mysql_num_rows($res) > 0){
			// Línea de texto para Control interno
			$mensaje .= "Ejecución correcta a las: \t".date("Y/m/d h:i:s")."\t Comprobaciones de Invitación procesadas correctamente. ";
		}else{
			$mensaje .= "Ejecución correcta a las: \t".date("Y/m/d h:i:s")."\t Sin datos de Comprobaciones de Invitación.\r\n\r\n";
		}
		
		// Respaldo de reporte generado
		$copiado = false;
		$copydir = $RUTA_SAP."backupSAP/backupComprobaciones/EnviadoSAP_Comprobaciones_".date("Ymd_his").".txt";
		$copiado = copy($csv_file, $copydir);
		
		if($copiado){
			$mensaje .= "Para conocer más detalles, refiérase al archivo EnviadoSAP_Comprobaciones_".date("Ymd_his").".txt.\r\n\r\n";
		}else{
			$mensaje .= "Error al copiar el archivo: \t ".$newfile."\r\n\r\n";
		}
		
		$file_log = $RUTA_SAP."backupSAP/backupComprobaciones/bitacoraComprobaciones".".txt";
		$reffichero001 = fopen($file_log, "a+");
		fwrite($reffichero001, $mensaje);
		fclose($reffichero001);
		
		/* Para erradicar la incertidumbre si el archivo es extraído imediatamente por PIX y este no se ha copiado al respaldo,
		 * ejecutaremos primero la copia y depues renombraremos el archivo, para que sea tomado por PIX.
		*/
		
		unlink($newfile);
		rename($csv_file,$newfile);
		
		// Enviar Reporte de Generación de Archivos
		$tramite->EnviaNotificacionEmailInterfaces(COMPROBACIONES, $mensaje, "41201003.T0010360.YXSP_PR_EXPS");
		
		if($csv_sep.mysql_num_rows($res) > 0){
			echo "INFO: Archivo csv de COMPROBACIONES GASTOS generado exitosamente!!!";
		}
		
	}else{
		$mensaje = "Ejecución correcta a las: \t".date("Y/m/d h:i:s")."\t Sin datos.\r\n\r\n";
		$file_log = $RUTA_SAP."backupSAP/backupComprobaciones/bitacoraComprobaciones".".txt";
		$reffichero001 = fopen($file_log, "a+");
		fwrite($reffichero001, $mensaje);
		fclose($reffichero001);
		
		// Enviar Reporte de Generación de Archivos
		$tramite->EnviaNotificacionEmailInterfaces(COMPROBACIONES, $mensaje, "41201003.T0010360.YXSP_PR_EXPS");
		
	}
	
	mysql_close($con);
?>
