<?php 

require_once('Connections/fwk_db.php');  
require_once('lib/php/constantes.php'); 
require_once('lib/php/mail.php'); 

//Carga de transacciones a  PARTIR DE ARCHIVOS.
	$APP_EEXPENSES_DIR=$RUTA_A."/ENTRADA/AMEX/";
	$Directorio_array=Array();
	
	$msgEmision="";
	//BUSCA ARCHIVOS DE ENTRADA QUE IMPORTAR
	$msgEmision="\n Reporte:\n";
	$msgMail="Reporte:<br><br>";
	$msgEmision.="\nBUSCANDO ARCHIVOS DE AMEX.....";
	$flagNotifica=0;
	$procesadasGlobal=0;
	$conexitoGlobal=0;
	$montoProcesadoGlobal=0;
	$content_array=array();
	
	$cnn = new conexion();
	
	//El directorio local donde almacenas solo los archivos de texto
	if ($handle = opendir($APP_EEXPENSES_DIR)) {
	 $i=0;
	 while (false !== ($file = readdir($handle))) {
	  if (!is_dir($APP_EEXPENSES_DIR.$file) && $file != "." && $file != ".." && $file!=basename($file, '.txt')) {
	   $content_array[$i]= $file;
	   $i++;
	  }
	 }
	//cierra el directorio
	closedir($handle);
	}
	if(count($content_array)>0){
	  $msgEmision.="\n".count($content_array)." archivos encontrados.....";
	    //hay archivos que importar
	  for($i=0;$i<count($content_array);$i++){
	  	$reporte="";
		$procesadas=0;
	    	$montoprocesado=0;
		$aceptadas=0;
		$montoaceptado=0;
		$conexito=0;
	    	$file_path=$APP_EEXPENSES_DIR.$content_array[$i];
	  if (strstr(strtolower($file_path),"itdatempgpoddm")==FALSE){
		//extrae el nombre del archivo sin la ruta para la notificacion
		$file_p_log=substr($file_path,18,5);
		$flagNotifica=1;
		$msgEmision.="\n ".date("Y-m-d H:i:s")." procesando archivo:......\n";
		$msgMail.="Fecha: ".date("Y-m-d H:i:s")." Procesando archivo: <strong>".$file_p_log."</strong>......<br>";
		//obtenemos el id consecutivo para el archivo
		$newfile_path=tempnam ($APP_EEXPENSES_DIR, "danoneamex");
		unlink($newfile_path);
		$newfile_path=strstr($newfile_path,"danoneamex");
		$result  =  rename($file_path,$newfile_path);
		//abre y lee el nuevo archivo
		$handle = fopen($newfile_path, "r");
			if(!$handle){
			$msgEmision.="No hay archivos actualmente";
			die("No hay archivo");
			}
		$factura=NULL;
			while (!feof($handle)) {
			   $buffer = fgets($handle, 4096);
			   $buffer = trim($buffer);
			   $marca= substr($buffer,0,1);
				if($marca=="1"){ //NUEVA FACTURA
				  //transaccion
				  $tarjeta=trim(substr($buffer,113,19));
				  $msgEmision.="\n".$tarjeta.",";
				  $msgMail.="Tarjeta:".$tarjeta.", ";
				  $fechastr =trim(substr($buffer,389,8));

				  $fecha=substr($fechastr,0,4)."-".substr($fechastr,4,2)."-".substr($fechastr,6,2);
				  $msgEmision.=$fecha.",";
				  $msgMail.="Fecha:".$fecha.", ";
				  $monto=trim(substr($buffer,245,15));

				  $monto=doubleval($monto/100);
				  $msgEmision.=$monto.",";
  				  $msgMail.="Monto:".$monto.", ";
				  $signo=trim(substr($buffer,244,1));

				  if($signo!="-"){
					  $moneda=$TYE_MONEDAS_AMEX[trim(substr($buffer,275,3))];
					  $msgEmision.=$moneda.",";
					  $msgMail.="Moneda:".$moneda.", ";
					  $TC=trim(substr($buffer,313,15));
					  $TC=doubleval($TC);
					  if($TC>0)
						$TC=$TC/100000000;
					  else
						$TC=1.0;
					  $concepto=ereg_replace("'","",trim(substr($buffer,445,42)));
					  $concepto=mysql_real_escape_string($concepto);
					  $notransaccion=trim(substr($buffer,374,15));
					   $msgEmision.=($monto*$TC).",";
					  //$msgMail.="TC:".($monto*$TC).", ";
					  $msgEmision.=$notransaccion;
					  $msgMail.="Transaccion:".$notransaccion."";

					  //busca la tarjeta en la base de datos.
					  $sqlTarjetahabientes=sprintf("SELECT idempleado from empleado where notarjetacredito='%s' and estatus>=0",$tarjeta);
					  //mysql_select_db($database_fwk_db, $fwk_db);
					  $rs_tarjetas = $cnn->consultar($sqlTarjetahabientes);
					  if(mysql_errno()>0){
						echo __LINE__.mysql_error();
					  }
					  $estatusTarjeta=-1;
					  if(mysql_num_rows($rs_tarjetas)>0){
						$estatusTarjeta=0;
						$msgEmision.=",<< CONTABILIZADO";
						$msgMail.=", << CONTABILIZADO<br>";
						$aceptadas++;
						$montoaceptado+=($monto*$TC);
					  }else{
						$msgEmision.=",>> TARJETA NO ENCONTRADA EN EL SISTEMA T&E";
						$msgMail.=", >> TARJETA NO ENCONTRADA EN EL SISTEMA T&E<br>";
					  }

					  $sqlInsert=sprintf("INSERT INTO amex(corporacion, tarjeta, fecha, fecha_cargo, monto, estatus, moneda, TC, concepto, rfc_establecimiento, notransaccion) VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
					  $corporacion,$tarjeta,$fecha,$fecha_cargo,$monto,$estatusTarjeta,$moneda,$TC,$concepto,$rfc_establecimiento,$notransaccion);
					  $rs_solicitudes = $cnn->insertar($sqlInsert);
						echo "----------\n";
						echo "Tarjeta: ".$tarjeta."\n";
						echo "fecha: ".$fecha."\n";
						echo "monto: ".$monto."\n";
						echo "estatus: ".$estatusTarjeta."\n";
						echo "moneda: ".$moneda."\n";
						echo "TC: ".$TC."\n";
						echo "concepto: ".$concepto."\n";
						echo "no.t: ".$notransaccion."\n";
					if(mysql_errno()>0){
						echo __LINE__.mysql_error();
					}
					  $procesadas++;
					  $montoprocesado+=($monto*$TC);
					}else{
					  $msgEmision.=">> PAGO DE TARJETA";
					  $msgMail.=" >> PAGO DE TARJETA <br>";
					}
				}
			}//while lines.
		fclose($handle);
		$msgEmision.="\n\n cargos procesados: $procesadas   Monto procesado: $montoprocesado";
		$msgEmision.="\n\n cargos aceptados : $aceptadas    Monto aceptado : $montoaceptado";

		$msgMail.="<p> Cargos procesados:".$procesadas." Monto procesado:".$montoprocesado."<br />";
		$msgMail.="Cargos aceptados :".$aceptadas."  Monto aceptado :".$montoaceptado."</p>";

	}//if txt
	}//fin for

		$msgEmision.="\n\nFIN";
		$msgEmision.="\n\nvalor de bandera:".$flagNotifica;
		if ($flagNotifica==1){
		//envia notificacion.
		$_msubject="CARGAS AMEX";
		$_mto="productosweb@masnegocio.com";
		$_mbody=$msgMail;
		$_mname="";
		sendMail($_msubject, $_mbody,$_mto,$_mname);

		//NSC CASO 15081//01/09/2010
		$_mto="ulises.jimenez@external.danone.com";
		sendMail($_msubject, $_mbody,$_mto,$_mname);
		//NSC CASO 16014//30/09/2010
		$_mto="OscarDiter.CRUZ@danone.com";
		sendMail($_msubject, $_mbody,$_mto,$_mname);
		//NSC CASO 18119//16/12/2010
		$_mto="hli@masnegocio.com";
		sendMail($_msubject, $_mbody,$_mto,$_mname);
		$_mto="dll@masnegocio.com";
		sendMail($_msubject, $_mbody,$_mto,$_mname);
		$_mto="nancy.clementeescamilla@danone.com";
		sendMail($_msubject, $_mbody,$_mto,$_mname);
	

		}//if notifica

	}//fin if


echo $msgEmision;
?>
