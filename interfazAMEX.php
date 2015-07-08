<?php 
	require_once('Connections/fwk_db.php');  
	require_once('lib/php/constantes.php'); 
	require_once('lib/php/mail.php');
	require_once "$RUTA_A/functions/Tramite.php";
	
	// Conexion a BD
	if($con = mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWORD))
		mysql_select_db($MYSQL_DATABASE);
	else
		die("Error de conexion.");

	//Carga de transacciones a  PARTIR DE ARCHIVOS.
	$APP_EEXPENSES_DIR	= $RUTA_AMEX;
	$directorio_array	= scandir($APP_EEXPENSES_DIR);
	$tramite			= new Tramite();
	$msg				= "";
	$contador			= 0;
	$amex				= array();
	$dataElements 		= array(
		"code"					=> 0,
		"url"					=> $APP_EEXPENSES_DIR,
		"file"					=> '',
		"totalCargos"			=> 0,
		"cargosInsertados"		=> 0,
		"cargosnoInsertados"	=> 0
	);
	
	for($j = 0; $j < count($directorio_array); $j++){
		if(!is_dir($APP_EEXPENSES_DIR.$directorio_array[$j]) && 
			file_exists($APP_EEXPENSES_DIR.$directorio_array[$j]) && 
			strpos($directorio_array[$j], "ENVASADORAS_DE_AGUAS_EN_MEXICO_GL_1025_001_") !== false){
			$amex[$contador] = $directorio_array[$j];
			$contador ++;
		}
	}
	
	if(empty($amex)){
		$dataElements["code"] = 1;
		// Si no existe archivo genera log
		$msg = log_amex($dataElements)."<br />";
			
		$log_querys = " Fracasó- No se encontró archivo \r\n";
		log_amex_querys($log_querys, $log_querys, $APP_EEXPENSES_DIR);
	}else{
		foreach ($amex as $archivo){
			if(!is_dir($APP_EEXPENSES_DIR.$archivo) && file_exists($APP_EEXPENSES_DIR.$archivo) && strpos($archivo, "ENVASADORAS_DE_AGUAS_EN_MEXICO_GL_1025_001_") !== false){
				// Comienza la transaccion para la insercion
				mysql_query("BEGIN");
				$sql = "INSERT INTO amex (tarjeta, fecha, fecha_cargo, monto, estatus, moneda_local, concepto, rfc_establecimiento, notransaccion, conversion_pesos, montoAmex, monedaAmex)
				VALUES ";
				$insertar = procesaArchivo($APP_EEXPENSES_DIR, $archivo);
				
				$dataElements["file"]				= $archivo;
				$dataElements["totalCargos"]		= $insertar["total_registros"];
				$dataElements["cargosInsertados"]	= $insertar["registros_insertados"];
				$dataElements["cargosnoInsertados"]	= $insertar["registros_no_insertados"];
				
				$sql .= $insertar["consulta"];
				$sql = substr($sql, 0, -1).";";
				
				if(strpos($sql, "VALUES (") === false){
					$dataElements["code"] = 4;
					$sql;
					mysql_query("ROLLBACK");
					$msg .= log_amex($dataElements)."<br />";
				}else{
					if($res = mysql_query($sql, $con)){
						mysql_query("COMMIT");
						
						if($dataElements["cargosnoInsertados"] === 0) 
							$dataElements["code"] = 2;
						else 
							$dataElements["code"] = 4;
						
						$msg .= log_amex($dataElements)."<br />";
					}else{
						$dataElements["code"] = 3;
						$sql;
						mysql_query("ROLLBACK");
						$msg .= log_amex($dataElements)."<br />";
					}
				}
				
				crearRespaldo($APP_EEXPENSES_DIR, $archivo);
			}
		}
	}
	$tramite->EnviaNotificacionEmailInterfaces(AMEX, $msg);
	
	/**
	 * Obtiene linea por linea de cada archivo para insertar el registro de cada cargo en la tabla AMEX
	 * @param char $dir		=> URL donde se almacenan los archivos
	 * @param char $file	=> Nombre del archivo
	 */
	
	function procesaArchivo($dir, $file){
		$con 					= new conexion();
		$archivo 				= fopen($dir.$file, "r")or exit("No es posible abrir el archivo");
		$data 					= fread($archivo, filesize($dir.$file));
		$linea_array 			= explode("\n", $data);
		$cantidad_lineas 		= count($linea_array);
		$noRegistros 			= 0;
		$registrosInsertados	= 0;
		$registrosnoInsertados	= 0;
		$sql					= "";
		
		$_array =  array(
				"consulta" 					=> $sql,
				"total_registros"			=> $noRegistros,
				"registros_insertados"		=> $registrosInsertados,
				"registros_no_insertados"	=> $registrosnoInsertados
		);
		
		$log_querys = "# Línea\t\tIndicador\t  Signo\t\tExiste\t\t\t\t Inserción en BD ";
		
		for ($i = 0; $i < $cantidad_lineas; $i++){
			$linea = $linea_array[$i];
			
			$log_querys .= "\r\n\t".$i." \t\t\t".substr($linea,0,1)." \t\t\t";
			
			// Si el primer dato es 1 se toma encuenta para procesar
			if(substr($linea,0,1) == 1){
				//obtiene datos
				$tarjeta 			= trim(substr($linea,207,20));
				$fecha_cargo 		= trim(substr($linea,588,10));
				$no_transaccion 	= trim(substr($linea,631,50));
				$signo 				= trim(substr($linea,736,1));
				$monto_amex 		= (trim(substr($linea,737,15)));
				$monto_amex 		= intval($monto_amex)/100;
				$moneda_amex 		= trim(substr($linea,769,3));
				$moneda_amex		= codigo_divisa($moneda_amex);
				$monto 				= trim(substr($linea,811,15));
				$monto 				= intval($monto)/100;
				$moneda_local 		= trim(substr($linea,858,3));
				$moneda_local 		= codigo_divisa($moneda_local);
				$rfc_establecimiento = trim(substr($linea,991,16));
				$concepto 			= mysql_real_escape_string(trim(substr($linea,1953,40)));
				$rfc_establecimiento = (substr($rfc_establecimiento,0,3) != "RFC") ? "" : trim(substr($rfc_establecimiento,3,16));
				
				$log_querys 		.= $signo." \t\t";
				
				$query		= "SELECT idamex, notransaccion FROM amex WHERE notransaccion = '" . $no_transaccion . "'";
				$res		= $con->consultar($query);
				$response	= mysql_fetch_assoc($res);
				
				if($signo == "+"){
					$estatusTarjeta = 0;
					
					if(empty($response)){
						$sql.= "('$tarjeta', NOW(), '$fecha_cargo', '$monto', '$estatusTarjeta', '$moneda_local', '$concepto', '$rfc_establecimiento', '$no_transaccion', '0.00', '$monto_amex', '$moneda_amex'),";
						$log_querys .= "  No \t\t";
						
						$registrosInsertados ++;
					}else{
						$log_querys .= "  Si \t\t";
						
						$registrosnoInsertados ++;
					}
					
					$log_querys .= "INSERT INTO amex (tarjeta, fecha, fecha_cargo, monto, estatus, moneda_local, concepto, rfc_establecimiento, notransaccion, conversion_pesos, montoAmex, monedaAmex) VALUES ('$tarjeta', NOW(), '$fecha_cargo', '$monto', '$estatusTarjeta', '$moneda_local', '$concepto', '$rfc_establecimiento', '$no_transaccion', '0.00', '$monto_amex', '$moneda_amex');";
					
					$noRegistros ++;
				}
			}
		}
				
		// Guardamos un log de los datos que deberian registrarse en la tabla
		log_amex_querys($log_querys, $file, $dir);
		
		$_array["consulta"]					= $sql;
		$_array["total_registros"]			= $noRegistros;
		$_array["registros_insertados"]		= $registrosInsertados;
		$_array["registros_no_insertados"]	= $registrosnoInsertados;
		
		return $_array;
	}
	
	// Funccion para crear el archivo de log para monitorear la ejecución de la interfaz
	function log_amex($data){
		if(!is_dir($data["url"].'logs')) mkdir($data["url"].'logs', 0777, true);
		
		$log_amex = $data["url"]."logs/log_amex.log";
		
		$fecha    = date("Y/m/d H:i:s");
		switch ($data["code"]){
			case 0: $msg = " ======================== ARCHIVO LOG AMEX - ENVASADORAS creado en $fecha ========================\r\n \r\n";
				break;
			case 1: $msg = " Fracasó: No se encontró archivo.  \t $fecha \t \r\n";
				break;
			case 2: $msg = " Archivo de AMEX procesado: " . $data["file"] . " a las: \t $fecha \t Archivo procesado correctamente, se insertaron " . $data["totalCargos"] . " cargos. \r\n";
				break;
			case 3: $msg = " Archivo de AMEX procesado: " . $data["file"] . " a las: \t $fecha \t Ocurrió un error inesperado, NO se insertaron los cargos. \r\n";
				break;
			case 4: $msg = " Archivo de AMEX procesado: " . $data["file"] . " a las: \t $fecha \t El archivo contiene " . $data["totalCargos"] . " cargos, de los cuales se insertaron " . $data["cargosInsertados"] . " y ya se encontraban registrados " . $data["cargosnoInsertados"] . ". \r\n";
				break;
			case 5: $msg = " Archivo de AMEX procesado: " . $data["file"] . " a las: \t $fecha \t Error al copiar archivo. \r\n";
				break;
			default;				
		}		
		
		$write_log = fopen($log_amex, "a+");
		
		if(file_exists($log_amex)){
			if( (date("md")=="0701") || (date("md")=="0101") ){
				unlink($log_amex);
				fopen($log_amex, "x+"); // Crear el archivo x+
				$msg2 = " ======================== ARCHIVO LOG AMEX - ENVASADORAS creado en $fecha ========================\r\n \r\n";
				echo $msg2."<br />";
				fwrite($write_log, $msg2);
			}
		}else{
			fopen($log_amex, "x+"); // Crear el archivo x+
			$msg2 = " ======================== ARCHIVO LOG AMEX - ENVASADORAS creado en $fecha ========================\r\n \r\n";
			echo $msg2."<br />";
			fwrite($write_log, $msg2);
		}
		
		echo $msg."<br />";
				
		fwrite($write_log, $msg);
		fclose($write_log);
		
		return $msg;
	}
	
	// Funcion para reemplazas las divisas
	function codigo_divisa($divisa){		
		$codigo_amex = array(986,978,976,975,974,973,972,970,969,968,954,953,952,951,950,949,948,947,946,944,943,941,940,938,937,936,910,901,894,891,891,886,882,862,860,858,840,834,826,
							818,810,807,710,578,566,558,554,548,533,532,528,524,516,512,508,504,498,496,484,480,478,470,466,462,458,454,450,446,442,440,434,430,428,426,422,418,417,414,
							410,408,400,398,392,388,180,174,170,156,152,144,136,132,124,116112,108,104,100,96,90,84,72,70,68,64,60,56,52,51,50,48,44,40,36,32,31,24,20,12,8,4,4);
		$codigo_divisa = array("BRL","EUR","CDF","BGN","BYR","AOA","TJS","COU","MGA","SRD","XEU","XPF","XOF","XCD","XAF","TRY","CHW","CHE","RON","AZN","MZN","RSD","UYI","SDG","VEF","GHS",
							"XAA","TWD","ZMK","YUM","CSD","YER","WST","VEB","UZS","UYU","USD","TZS","GBP","EGP","RUR","MKD","ZAR","NOK","NGN","NIO","NZD","VUV","AWG","ANG","NLG","NPR",
							"NAD","OMR","MZM","MAD","MDL","MNI","MXN","MUR","MRO","MTL","MLF","MVR","MWK","MGF","MOP","LUF","LTL","LYD","LRD","LVL","LSL","LSP","LAK","KGS","KWD",
							"KRW","KPW","KES","JOD","KZT","JPY","JMD","ZRN","KMG","COP","CNY","CLP","LKR","KYD","CVE","CAD","KHR","BYB","BIF","MMK","BGL","BND","SBD","BZD","BWP",
							"BAD","BOB","BTN","BMW","BEF","BBD","AMD","BDT","BHD","BSD","ATS","AUD","ARS","AZM","AON","ADP","DZD","ALL","AFN","AFA");
		return str_replace($codigo_amex, $codigo_divisa, $divisa);
		
	}
	
	/**
	 * Funcion para crear un respaldo de los archivos procesados de AMEX
	 **/
	function crearRespaldo($APP_EEXPENSES_DIR, $archivo){
		$dirBackup = $APP_EEXPENSES_DIR.'backup/';
		$dirbackupMes = $dirBackup.'/'.date("Y_m");
		
		if(!is_dir($dirBackup)) mkdir($dirBackup, 0777, true);
		if(!is_dir($dirbackupMes)) mkdir($dirbackupMes, 0777, true);
		
		if(copy($APP_EEXPENSES_DIR.$archivo, $dirbackupMes.'/'.$archivo))
			unlink($APP_EEXPENSES_DIR.$archivo);
		else{
			$dataElements["code"] 	= 5;
			$dataElements["file"] 	= $archivo;
			$dataElements["url"] 	= $APP_EEXPENSES_DIR;
			log_amex($dataElements);
		}
	}

	// Funccion para crear log y monitorear la ejecución de los querys
	function log_amex_querys($text, $file, $APP_EEXPENSES_DIR){
		if(!is_dir($APP_EEXPENSES_DIR.'logs')) mkdir($APP_EEXPENSES_DIR.'logs', 0777, true);
		
		$log_amex = $APP_EEXPENSES_DIR."logs/log_querys_amex.log";
		$fecha    = date("Y/m/d H:i:s");
		
		$msg = "[".$fecha."]\t\t Archivo procesado:\t".$file."\r\n".$text."\r\n\r\n";
		
		$cabecera = "";
		if(!file_exists($log_amex)){
			$cabecera = " ======================== ARCHIVO LOG - ENVASADORAS creado en $fecha ========================\r\n \r\n";
			fopen($log_amex, "x+"); // Crear el archivo x+
		}
		
		$write_log = fopen($log_amex, "a+");
		
		$msg = $cabecera.$msg;
		
		fwrite($write_log, $msg);
		fclose($write_log);
	}
	
	
	mysql_close($con);
?>