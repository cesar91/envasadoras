<?php 
require_once('Connections/fwk_db.php');  
require_once('lib/php/constantes.php'); 
require_once('lib/php/mail.php'); 

	//Carga de transacciones a  PARTIR DE ARCHIVOS.
	$APP_EEXPENSES_DIR = $RUTA_A."/amex/";
	//$APP_EEXPENSES_DIR = "C:/wamp/www/eexpensesv2/amex/";	
	$date = date("Ymd");
	$file_name = $APP_EEXPENSES_DIR."BMW_DE_MEXICO_GL1025_".$date;
	
	$directorio_array = scandir($APP_EEXPENSES_DIR);
	for($i=0;$i<count($directorio_array);$i++){				
		if(strpos($directorio_array[$i],"MW_DE_MEXICO_GL1025_".$date) != 0){
			$file_name = $APP_EEXPENSES_DIR.$directorio_array[$i];
		}
	}		
	$cnn = new conexion();	
	//$msg_emision         = "\r\n Reporte: \r\n
	//			              \r\n BUSCANDO ARCHIVOS DE AMEX.....";
	//$msg_email           = " Reporte:<br><br>";
	
	//Si existe archivo inicia proceso
	
	if(file_exists($file_name)){
		//lee archivo y lo separa por lineas
		$archivo = fopen($file_name, "r")or exit("No es posible abrir el archivo");
		$data = fread($archivo, filesize($file_name));
		$linea_array = explode("\n", $data);
		$cantidad_lineas = count($linea_array);
		//$msg_emision.= "\r\n ".date("Y-m-d H:i:s")." procesando archivo:......\r\n";		
		//$msg_email.= "Fecha: ".date("Y-m-d H:i:s")." Procesando archivo: <strong>".$archivo."</strong>......<br>";
		
		//comienza la transaccion para la insercion
		$transaction = mysql_query("BEGIN");
		$sql = "INSERT INTO amex (tarjeta,fecha,fecha_cargo,monto,estatus,moneda_local,concepto,rfc_establecimiento,notransaccion,conversion_pesos,montoAmex,monedaAmex) 
				VALUES ";	
		for ($i = 0; $i <= $cantidad_lineas; $i++){
			$linea = $linea_array[$i];
			//si el primer dato es 1 se toma encuenta para procesar
			if(substr($linea,0,1) == 1){
				//obtiene datos
				$tarjeta = trim(substr($linea,207,20));
				$fecha_cargo = trim(substr($linea,588,10));
				$monto_amex = (trim(substr($linea,737,15)));
				$monto_amex = intval($monto_amex)/100;
				$moneda_amex = trim(substr($linea,769,3));
				$moneda_amex = codigo_divisa($moneda_amex);
				$monto = trim(substr($linea,811,15));
				$monto = intval($monto)/100;
				$moneda_local = trim(substr($linea,858,3));
				$moneda_local = codigo_divisa($moneda_local);
				$concepto = mysql_real_escape_string(trim(substr($linea,1837,30)));
				$no_transaccion = trim(substr($linea,631,50));
				$rfc_establecimiento = trim(substr($linea,991,45));
				if(substr($rfc_establecimiento,0,2) != "TX"){	
					$rfc_establecimiento = "";
				}else{
					$rfc_establecimiento = preg_replace("/TX#/","",$rfc_establecimiento);					
				}
				$signo = trim(substr($linea,736,1));
				$sql_divisa = "SELECT CONVERT('$monto_amex'*div_tasa,DECIMAL(10,2)) as total 
								FROM divisa 
								WHERE div_nombre = '$moneda_amex'";
				$res = $cnn->consultar($sql_divisa);
				$row = mysql_fetch_assoc($res);
				$conversion_pesos = $row['total'];
				
				if($signo == "+"){								
					//genera reporte email
					//$msg_email.= "";					
					//genera reporte pantalla
					//$msg_emision.= "";					
					$estatusTarjeta = '';
					$sql_empleado = "SELECT idempleado 
										FROM empleado 
										WHERE notarjetacredito = '$tarjeta' AND estatus = 1 
										OR notarjetacredito_gas = '$tarjeta' AND estatus = 1";
					$res = $cnn->consultar($sql_empleado);
					$row = mysql_fetch_array($res);
					if($row['idempleado'] != ''){
						$estatusTarjeta = 0;
						//$msg_emision.=", << CONTABILIZADO";
						//$msg_email.=", << CONTABILIZADO<br>";				
					}else{
						$estatusTarjeta = 0;//-1
						//$msg_emision.=", >> TARJETA NO ENCONTRADA EN EL SISTEMA EEXPENSES";
						//$msg_email.=", >> TARJETA NO ENCONTRADA EN EL SISTEMA EEXPENSES<br>";					
					}					
					$sql.= "('$tarjeta',NOW(),'$fecha_cargo','$monto','$estatusTarjeta','$moneda_local','$concepto','$rfc_establecimiento','$no_transaccion','$conversion_pesos','$monto_amex','$moneda_amex'),";
				}else{
					 //$msg_emision.=" >> PAGO DE TARJETA";
					 //$msg_email.=" >> PAGO DE TARJETA <br>";				
				}
			}
		}
		//finaliza transaccion
		$sql = substr($sql,0,-1).";";
		if($res = $cnn->consultar($sql)){
			$transaction = $cnn->ejecutar("COMMIT");
			//log_amex(2);
			//send_mail($msg_email);
			//$msg_emision;
		}else{
			$transaction = $cnn->ejecutar("ROLLBACK");
			//log_amex(3);
			//$msg_emision;
		}
	}else{//si no existe archivo muestra genera log
		log_amex(1);
		//$msg_email.= "No hay archivos actualmente";
		//$msg_emision.= "No hay archivos actualmente";
	}
	
	//funccion para crear el archivo de log para monitorear la ejecución de la interfaz
	function log_amex($log){
		$log_amex = "/usr/local/zend/apache2/htdocs/eexpensesv2/amex/log_amex.log";
		//$log_amex = "C:/wamp/www/eexpensesv2/amex/log_amex.log";
		
		$fecha    = date("Y/m/d H:i:s");
		switch ($log){
			case 0: $msg = " ======================== ARCHIVO LOG AMEX -BMW creado en $fecha ========================\r\n \r\n";
				break;
			case 1: $msg = " Archivo de AMEX procesado a las: \t $fecha \t Fracasó- No se encontró archivo \r\n";
				break;
			case 2: $msg = " Archivo de AMEX procesado a las: \t $fecha \t Éxito \r\n";
				break;
			case 3: $msg = " Archivo de AMEX procesado a las: \t $fecha \t Error- Ocurrió un error inesperado \r\n";
				break;			
			default;				
		}		
		
		$write_log = fopen($log_amex, "a+");
		if(file_exists($log_amex)){
			if( (date("md")=="0701") || (date("md")=="0101") ){
				unlink($log_amex);		
				fopen($log_amex, "x+"); // Crear el archivo x+	
				$msg2 = " ======================== ARCHIVO LOG AMEX -BMW creado en $fecha ========================\r\n \r\n";
				echo $msg2."<br />";	
				fwrite($write_log, $msg2);
			}			
		}else{
			fopen($log_amex, "x+"); // Crear el archivo x+	
			$msg2 = " ======================== ARCHIVO LOG AMEX -BMW creado en $fecha ========================\r\n \r\n";
			echo $msg2."<br />";	
			fwrite($write_log, $msg2);
		}	
		echo $msg."<br />";		
		fwrite($write_log, $msg);
		fclose($write_log);	
	}
	
	//funcion para reemplazas las divisas
	function codigo_divisa($divisa){
		$codigo_amex = array("840","484","978","985","203","32","986","124","152","188","170","156","320","558","578","590","600","604","858","937");
		$codigo_divisa = array("USD","MXN","EUR","PLN","CZK","ARS","BRL","CAD","CLP","CRC","COP","CNY","GTQ","NIO","NOK","PAB","PYG","PEN","UYU","VEF");
		$divisa = str_replace($codigo_amex, $codigo_divisa, $divisa);
		return $divisa;		
	}			
	
	//Envia notificacion vía correo
	function send_mail($msg){		
		$_msubject = "CARGAS AMEX";
		$_mto = "productosweb@masnegocio.com";
		$_mbody = $msg;
		$_mname = "iaejeanx@gamil.com";
		sendMail($_msubject, $_mbody,$_mto,$_mname);
	}	
?>
