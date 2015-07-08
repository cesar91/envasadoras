<?php 
require_once('Connections/fwk_db.php');  
require_once('lib/php/constantes.php'); 
require_once('lib/php/mail.php');
require_once "$RUTA_A/functions/Tramite.php";

	//Carga de transacciones a  PARTIR DE ARCHIVOS.
	$APP_EEXPENSES_DIR = $RUTA_A."/amex/outbox/";
	//$APP_EEXPENSES_DIR = "C:/wamp/www/eexpensesv2_bmw/amex/";	
	$date = date("Ymd");
	$file_name = $APP_EEXPENSES_DIR."BMW_DE_MEXICO_GL1025_20130407";
	
	$directorio_array = scandir($APP_EEXPENSES_DIR);
	for($i=0;$i<count($directorio_array);$i++){				
		if(strpos($directorio_array[$i],"MW_DE_MEXICO_GL1025_20130407") != 0){
			$file_name = $APP_EEXPENSES_DIR.$directorio_array[$i];
		}
	}		
	if($con = mysql_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWORD))
		mysql_select_db($MYSQL_DATABASE);
	else
		die("Error de conexxion.");
	
	//Si existe archivo inicia proceso
	if(file_exists($file_name)){
		$archivo = fopen($file_name, "r")or exit("No es posible abrir el archivo");
		$data = fread($archivo, filesize($file_name));
		$linea_array = explode("\n", $data);
		$cantidad_lineas = count($linea_array);
		
		//comienza la transaccion para la insercion
		mysql_query("BEGIN");
		$sql = "INSERT INTO amex (tarjeta,fecha,fecha_cargo,monto,estatus,moneda_local,concepto,rfc_establecimiento,notransaccion,montoAmex,monedaAmex) 
				VALUES ";	
		
		for ($i = 0; $i < $cantidad_lineas; $i++){
			$linea = $linea_array[$i];
			
			//si el primer dato es 1 se toma encuenta para procesar
			if(substr($linea,0,1) == 1){
				//obtiene datos				
				$tarjeta = trim(substr($linea,207,20));
				$fecha_cargo = trim(substr($linea,588,10));
				$no_transaccion = trim(substr($linea,631,50));
				$signo = trim(substr($linea,736,1));
				$monto_amex = (trim(substr($linea,737,15)));
				$monto_amex = intval($monto_amex)/100;
				$moneda_amex = trim(substr($linea,769,3));
				$moneda_amex = codigo_divisa($moneda_amex);				
				//echo "<br>";
				$monto = trim(substr($linea,811,15));
				$monto = intval($monto)/100;
				$moneda_local = trim(substr($linea,858,3));
				$moneda_local = codigo_divisa($moneda_local);
				$rfc_establecimiento = trim(substr($linea,991,45));
				$concepto = mysql_real_escape_string(trim(substr($linea,1953,40)));				
				
				$rfc_establecimiento = (substr($rfc_establecimiento,0,3) != "RFC") ? "" : substr($rfc_establecimiento,3,42);
				error_log("---->>>>RFC: ".$rfc_establecimiento);
				if($signo == "+"){															
					$estatusTarjeta = 0;
					/*$estatusTarjeta = '';
					$sql_empleado = "SELECT idempleado 
										FROM empleado 
										WHERE notarjetacredito = '$tarjeta' AND estatus = 1 
										OR notarjetacredito_gas = '$tarjeta' AND estatus = 1";
					$res = $cnn->consultar($sql_empleado);
					$row = mysql_fetch_array($res);
					if($row['idempleado'] != ''){
						$estatusTarjeta = 0;						
					}else{
						$estatusTarjeta = 0;//-1						
					}*/
					$sql.= "('$tarjeta',NOW(),'$fecha_cargo','$monto','$estatusTarjeta','$moneda_local','$concepto','$rfc_establecimiento','$no_transaccion','$monto_amex','$moneda_amex'),";
				}
			}
		}
		//finaliza transaccion
		$sql = substr($sql,0,-1).";";
		
		if($res = mysql_query($sql)){
			error_log($sql);
			mysql_query("COMMIT");
			log_amex(2,$APP_EEXPENSES_DIR);			
		}else{			
			$sql;
			error_log($sql);
			mysql_query("ROLLBACK");
			log_amex(3,$APP_EEXPENSES_DIR);			
		}
	}else{//si no existe archivo muestra genera log
		log_amex(1,$APP_EEXPENSES_DIR);		
	}
	
	//funccion para crear el archivo de log para monitorear la ejecución de la interfaz
	function log_amex($log,$APP_EEXPENSES_DIR){
		$tramite = new Tramite();
		
		$log_amex = $APP_EEXPENSES_DIR."log_amex.log";		
		
		$fecha    = date("Y/m/d H:i:s");
		switch ($log){
			case 0: $msg = " ======================== ARCHIVO LOG AMEX -BMW creado MANUALMENTE en $fecha ========================\r\n \r\n";
				break;
			case 1: $msg = " Archivo de AMEX procesado MANUALMENTE a las: \t $fecha \t Fracasó- No se encontró archivo \r\n";
				break;
			case 2: $msg = " Archivo de AMEX procesado MANUALMENTE a las: \t $fecha \t Éxito \r\n";
				break;
			case 3: $msg = " Archivo de AMEX procesado MANUALMENTE a las: \t $fecha \t Error- Ocurrió un error inesperado \r\n";
				break;			
			default;				
		}		
		
		$write_log = fopen($log_amex, "a+");
		if(file_exists($log_amex)){
			if( (date("md")=="0701") || (date("md")=="0101") ){
				unlink($log_amex);		
				fopen($log_amex, "x+"); // Crear el archivo x+	
				$msg2 = " ======================== ARCHIVO LOG AMEX -BMW  creado MANUALMENTE en $fecha ========================\r\n \r\n";
				echo $msg2."<br />";	
				fwrite($write_log, $msg2);
			}			
		}else{
			fopen($log_amex, "x+"); // Crear el archivo x+	
			$msg2 = " ======================== ARCHIVO LOG AMEX -BMW creado MANUALMENTE en $fecha ========================\r\n \r\n";
			echo $msg2."<br />";	
			fwrite($write_log, $msg2);
		}	
		echo $msg."<br />";		
		fwrite($write_log, $msg);
		fclose($write_log);
		$tramite->EnviaNotificacionEmailInterfaces(AMEX, $msg);
	}
	
	//funcion para reemplazas las divisas
	function codigo_divisa($divisa){		
		$codigo_amex = array(986,978,976,975,974,973,972,970,969,968,954,953,952,951,950,949,948,947,946,944,943,941,940,938,937,936,910,901,894,891,891,886,882,862,860,858,840,834,826,
							818,810,807,578,566,558,554,548,533,532,528,524,516,512,508,504,498,496,484,480,478,470,466,462,458,454,450,446,442,440,434,430,428,426,422,418,417,414,
							410,408,400,398,392,388,180,174,170,156,152,144,136,132,124,116112,108,104,100,96,90,84,72,70,68,64,60,56,52,51,50,48,44,40,36,32,31,24,20,12,8,4,4);
		$codigo_divisa = array("BRL","EUR","CDF","BGN","BYR","AOA","TJS","COU","MGA","SRD","XEU","XPF","XOF","XCD","XAF","TRY","CHW","CHE","RON","AZN","MZN","RSD","UYI","SDG","VEF","GHS",
							"XAA","TWD","ZMK","YUM","CSD","YER","WST","VEB","UZS","UYU","USD","TZS","GBP","EGP","RUR","MKD","NOK","NGN","NIO","NZD","VUV","AWG","ANG","NLG","NPR",
							"NAD","OMR","MZM","MAD","MDL","MNI","MXN","MUR","MRO","MTL","MLF","MVR","MWK","MGF","MOP","LUF","LTL","LYD","LRD","LVL","LSL","LSP","LAK","KGS","KWD",
							"KRW","KPW","KES","JOD","KZT","JPY","JMD","ZRN","KMG","COP","CNY","CLP","LKR","KYD","CVE","CAD","KHR","BYB","BIF","MMK","BGL","BND","SBD","BZD","BWP",
							"BAD","BOB","BTN","BMW","BEF","BBD","AMD","BDT","BHD","BSD","ATS","AUD","ARS","AZM","AON","ADP","DZD","ALL","AFN","AFA");
		return str_replace($codigo_amex, $codigo_divisa, $divisa);
		
	}			
	mysql_close($con);
?>
