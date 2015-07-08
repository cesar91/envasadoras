<?
	require_once "/usr/local/zend/apache2/htdocs/eexpensesv2_bmw/lib/php/constantes.php";
	//require_once "C:/wamp/www/eexpensesv2_bmw/lib/php/constantes.php";
	require_once "$RUTA_A/Connections/fwk_db.php";
	
	if($con = mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWORD))
		mysql_select_db($MYSQL_DATABASE);
	else
		die("Error de conexxion.");
	
	echo "</div><h1>Generar CSV COMPROBACIONES GASTOS ----> 41201003.T0010360.YXSP PR EXPS</h1>";
	$csv_end = "
";  
	$csv_sep = "\t";
	$csv_file = $RUTA_SAP."datas_comprobaciones.txt";
	$csv="";
	$sql="CALL getcomprobacionesgastos();";
	mysql_query($sql);
	$sql="SELECT * FROM resultadoComprobaciones;";
	$res=mysql_query($sql);
	$i=0;
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
						//$row_iva11['account'].$csv_sep.
						$csv_sep.$csv_sep.
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
						//$row_iva11['account'].$csv_sep.
						$csv_sep.$csv_sep.
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
				while($row_iva11=mysql_fetch_array($res_iva11)){
					//error_log("Amount P: ".$row_iva11['amount']);
					if($row_iva11['amount'] != ""){
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"P".$csv_sep.
						"300371".$csv_sep.
						str_pad($row_iva11['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;							
						$i++;
					}else{
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"P".$csv_sep.
						"300371".$csv_sep.
						str_pad("0.00", 14, ' ', STR_PAD_LEFT).$csv_end;
						$i++;
					}
				}
				//B
				$sql_iva11="CALL getcuentap(".$t_anterior.");";//300371
				//error_log("B: ".$sql_iva11);
				mysql_query($sql_iva11);
				$sql_iva11="SELECT * FROM resultado2;";
				$res_iva11=mysql_query($sql_iva11);
				while($row_iva11=mysql_fetch_array($res_iva11)){
					//error_log("Amount B: ".$row_iva11['amount']);
					if($row_iva11['amount'] != ""){
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"B".$csv_sep.
						$csv_sep.$csv_sep.
						str_pad($row_iva11['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;				
						$i++;
					}else{
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"B".$csv_sep.
						$csv_sep.$csv_sep.
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
						//$row_iva11['account'].$csv_sep.
						$csv_sep.$csv_sep.
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
						//$row_iva11['account'].$csv_sep.
						$csv_sep.$csv_sep.
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
				while($row_iva11=mysql_fetch_array($res_iva11)){
					//error_log("Amount P: ".$row_iva11['amount']);
					if($row_iva11['amount'] != ""){
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"P".$csv_sep.
						"300371".$csv_sep.
						str_pad($row_iva11['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;							
						$i++;
					}else{
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"P".$csv_sep.
						"300371".$csv_sep.
						str_pad("0.00", 14, ' ', STR_PAD_LEFT).$csv_end;
						$i++;
					}
				}
				//B
				$sql_iva11="CALL getcuentap(".$t_anterior.");";//300371
				//error_log("B: ".$sql_iva11);
				mysql_query($sql_iva11);
				$sql_iva11="SELECT * FROM resultado2;";
				$res_iva11=mysql_query($sql_iva11);
				while($row_iva11=mysql_fetch_array($res_iva11)){
					//error_log("Amount B: ".$row_iva11['amount']);
					if($row_iva11['amount'] != ""){
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"B".$csv_sep.
						$csv_sep.$csv_sep.
						str_pad($row_iva11['amount'], 14, ' ', STR_PAD_LEFT).$csv_end;				
						$i++;
					}else{
						$csv.=
						$csv_sep.
						$csv_sep.
						$csv_sep.
						"B".$csv_sep.
						$csv_sep.$csv_sep.
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
		$sql2 = "UPDATE detalle_comprobacion SET dc_enviado_sap = '1' WHERE dc_id = '".$row['id']."'";
		//error_log($sql2);
		mysql_query($sql2);
	}
	
	if($csv_sep.mysql_num_rows($res)>0){
		$csv.="TOTAL".$csv_sep.$csv_sep.$csv_sep.$csv_sep.$csv_sep.$i.$csv_sep.$csv_end;

		//Generamos el csv de todos los datos  
		if (!$handle = fopen($csv_file, "w")) {  
			echo "Cannot open file";  
			exit;  
		}  
		if (fwrite($handle, $csv) === FALSE) {  
			echo "Cannot write to file";  
			exit;  
		}
		fclose($handle);
	
		//$newfile="../../datos/InterfazSAP/41201003.T0010360.YXSP PR EXPS";
		$newfile=$RUTA_SAP."41201003.T0010360.YXSP_PR_EXPS";
		unlink($newfile);
		rename($csv_file,$newfile);
		echo "INFO: Archivo csv de COMPROBACIONES GASTOS generado exitosamente!!!";
	}
	mysql_close($con);
?>
