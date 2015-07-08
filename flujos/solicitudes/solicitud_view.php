<?php 
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
require_once "$RUTA_A/functions/Notificacion.php";
require_once "$RUTA_A/functions/Delegados.php";
require_once "$RUTA_A/functions/CentroCosto.php";
require_once("../../functions/SolicitudesGastos.php");
require_once "$RUTA_A/functions/Presupuesto.php";
require_once("$RUTA_A/flujos/solicitudes/services/C_SV.php");
require_once("$RUTA_A/lib/php/mobile_device_detect.php");

	function get_siguiente_autorizador($ruta_autorizacion,$dueno){
		$separador = "|";
		$token = strtok($ruta_autorizacion,$separador);
		while($token != false){
			if($token == $dueno){
				break;
			}
			$token = strtok($separador);
		}
		$token = strtok($separador);
		return $token;
	}

// Detecta si es un dispositivo movil (IPhone, Android, Blackberry)
$mobile_type = null;
//$mobile = mobile_device_detect(true,true,true,true,true,true,false,false,&$mobile_type);
$mobile = false;  
if(isset($_POST['devolver_to_emple'])){
	$t_id = $_POST["tramite"];	
	
	$rutaAutorizacion=new RutaAutorizacion();
	$rutaAutorizacion->asignaraAgencia($t_id);
	$tramite = new Tramite();
	
	//observaciones
	$texto = $_POST['observ_to_emple'];
	$t_id = $_POST["tramite"];
	$u_id = $_POST["iu"];
	$cnn = new conexion();
	
	if($texto != ""){
		$query = sprintf("INSERT INTO observaciones(
					ob_id,
					ob_texto,
					ob_fecha,
					ob_tramite,
					ob_usuario
				)VALUES(
					default,
					'%s',
					now(),
					%s,
					%s
				)", 
					$texto,
					$t_id,
					$u_id
		);
			
		$ob_id = $cnn->insertar($query);
	}
	//Se envia la notificacion a agencia (como es un grupo se enviara la notificacion a los integrantes)
	
	$agrup_usu = new AgrupacionUsuarios();
	
	if($texto != ""){		
		//error_log("Entra para devolver a agencia");
		$aux= array();
		
		//agencia = 3000
		$rst=$agrup_usu->Load_Homologacion_Usuarios(3000);	
		while($arre=mysql_fetch_assoc($rst)){
			array_push($aux,$arre);
		}		
		foreach($aux as $datosAux){	
		//se enviara el mensaje a los distintos destinatarios (grupo)-->3000
		$mensaje  = sprintf("El empleado ha realizado <strong>nuevas observaciones</strong> sobre la solicitud <strong>%05s</strong>.",$t_id);
		$tramite->EnviaNotificacion($t_id, $mensaje, $u_id,$datosAux["hd_u_id"], "0", ""); //"0" para no enviar email y "1" para enviarlo		
		//error_log($mensaje);
		}
	}
	
	//Tomamos el valor de agencia 
	$agrup_usu->Load_Grupo_de_Usuario_By_Nombre("Agencia");
	$agencia=$agrup_usu->Get_dato("au_id");
	
	//Tomamos el valor de la etapa en cuestion
	$tramite->Modifica_Etapa($t_id,SOLICITUD_ETAPA_EN_COTIZACION, FLUJO_SOLICITUD, $agencia,"");
	
	 header("Location: ./index.php");
}
/////////////////////////////////////////////
//para setear el boton d devolver a empleado
if(isset($_POST['devolver_empleado'])  && isset($_POST['rowDelAvion'])){	
	$t_id = $_POST["tramite"];
	$CotizacionAvion=$_POST["rowDelAvion"];
	$CotizacionAuto = $_POST['rowDelAuto'];
	$CotizacionHotel = $_POST['rowDelHotel'];
	$rutaAutorizacion=new RutaAutorizacion();
	$rutaAutorizacion->asignaraEmpleado($t_id);
	$tramite = new Tramite();
	$hoteles = new Hotel();
	
	$texto = $_POST['observ_to_emple'];
 	$t_id = $_POST["tramite"];
 	$u_id = $_POST["iu"];
 	$cnn = new conexion();
 	$CViaje = new C_SV();
 	 	
 	if($texto != ""){
 		$query = sprintf("INSERT INTO observaciones(
 				ob_id,
 				ob_texto,
 				ob_fecha,
 				ob_tramite,
 				ob_usuario
 		)VALUES(
 				default,
 				'%s',
 				now(),
 				%s,
 				%s
 		)",
 				$texto,
 				$t_id,
 				$u_id
 		);
 		$ob_id = $cnn->insertar($query);
 	}
 	
 	//Insertamos el valor que agencia cotizo para el boleto de avion : 1° cotizacion.
 	$tramite->valorAvionOrignal($t_id); 	
  	
	//Se envia la notificacion al empleado	
	$destinatario=$rutaAutorizacion->getIniciador($t_id);
	
	if(($texto == "" && $CotizacionAvion >= 1) || ($texto != "" && $CotizacionAvion >= 1) ){//aereo
		
		//------------------------		
		//se validara que las cotizaciones que pidio, siendo un viaje terrestre, a la agencia se haya realizado
		$cotAuto = 0;
		$cotHotel = 0;
		
		$queryVerificaCotizacion="SELECT svi_hotel_agencia, svi_renta_auto_agencia FROM sv_itinerario svi
			INNER JOIN solicitud_viaje sv
			ON sv.sv_id = svi.svi_solicitud
			INNER JOIN tramites tr
			ON tr.t_id = sv.sv_tramite
			WHERE t_id ={$t_id}";
		$rstVerificaCotizaciones = $cnn->consultar($queryVerificaCotizacion);
		//obtenemos la cotizacion realizada a agencia
		while($fila=mysql_fetch_array($rstVerificaCotizaciones)){
		if($fila['svi_hotel_agencia'] == 1){
			$cotHotel++;
		}
			if($fila['svi_renta_auto_agencia'] == 1){
				$cotAuto++;
			}
		}
		
		error_log("cotizacion solicitada por usuarioH".$cotHotel);
		error_log("cotizacion solicitada por usuarioA".$cotAuto);
		error_log("cotizacion hecha agenciaH".$CotizacionHotel);
		error_log("cotizacion hecha agenciaA".$CotizacionAuto);
		
		if($CotizacionAuto == 0 && $CotizacionHotel == 0){
				if(($cotHotel != 0 && $cotAuto != 0) || ($cotHotel == 0 && $cotAuto != 0) || $cotHotel != 0 && $cotAuto == 0){				
					$mensaje  = sprintf("La solicitud de viaje <strong>%05s</strong> no ha terminado de ser cotizada por la <strong>Agencia</strong>.",$t_id);
					$tramite->Modifica_Etapa($t_id, SOLICITUD_ETAPA_EN_COTIZACION, FLUJO_SOLICITUD, $destinatario, "");					
				}else{
					$mensaje  = sprintf("La solicitud de viaje <strong>%05s</strong> ha sido cotizada por <strong>Agencia</strong>.",$t_id);
 					$tramite->Modifica_Etapa($t_id, SOLICITUD_ETAPA_COTIZADA, FLUJO_SOLICITUD, $destinatario, "");					
				}				
 		}else{
 				if(($CotizacionAuto >= $cotAuto) && ($CotizacionHotel >= $cotHotel)){
 					$mensaje  = sprintf("La solicitud de viaje <strong>%05s</strong> ha sido cotizada por <strong>Agencia</strong>.",$t_id);
 					$tramite->Modifica_Etapa($t_id, SOLICITUD_ETAPA_COTIZADA, FLUJO_SOLICITUD, $destinatario, "");
 				}else{
 					$mensaje  = sprintf("La solicitud de viaje <strong>%05s</strong> no ha terminado de ser cotizada por la <strong>Agencia</strong>.",$t_id);
 					$tramite->Modifica_Etapa($t_id, SOLICITUD_ETAPA_EN_COTIZACION, FLUJO_SOLICITUD, $destinatario, "");
 				}	
 		}
		//------------------------------------------------	
			
 	}elseif(($texto == "" &&  $CotizacionAvion == 0) || ( $texto != "" && $CotizacionAvion == 0)){//Terrestre ó Aereo
 		$banderaTTransporte=0;
 		$banderaTTransporte = $tramite->solViajeTransporte($t_id);
 		
 		if($banderaTTransporte == 0){ 			
 			//Viaje totalmente terrestre 			
 			//se validara que las cotizaciones que pidio, siendo un viaje terrestre, a la agencia se haya realizado
 			$cotAuto = 0;
 			$cotHotel = 0;
 			error_log("Viaje terrestre");
 			$queryVerificaCotizacion="SELECT svi_hotel_agencia, svi_renta_auto_agencia FROM sv_itinerario svi						
								INNER JOIN solicitud_viaje sv
								ON sv.sv_id = svi.svi_solicitud
								INNER JOIN tramites tr
								ON tr.t_id = sv.sv_tramite
								WHERE t_id ={$t_id}";
 			$rstVerificaCotizaciones = $cnn->consultar($queryVerificaCotizacion);
 			//obtenemos la cotizacion realizada a agencia
 			while($fila=mysql_fetch_array($rstVerificaCotizaciones)){
 				if($fila['svi_hotel_agencia'] == 1){
 					$cotHotel++;
 				}
 				if($fila['svi_renta_auto_agencia'] == 1){
 					$cotAuto++;
 				}
 			}

 			error_log("cotizacion solicitada por usuarioH".$cotHotel);
 			error_log("cotizacion solicitada por usuarioA".$cotAuto);
 			error_log("cotizacion hecha agenciaH".$CotizacionHotel);
 			error_log("cotizacion hecha agenciaA".$CotizacionAuto);
 			
 			if($CotizacionAuto == 0 && $CotizacionHotel == 0){
 				$mensaje  = sprintf("La solicitud de viaje <strong>%05s</strong> no ha terminado de ser cotizada por la <strong>Agencia</strong>.",$t_id);
 				$tramite->Modifica_Etapa($t_id, SOLICITUD_ETAPA_EN_COTIZACION, FLUJO_SOLICITUD, $destinatario, "");
 			}else{
 				if(($CotizacionAuto >= $cotAuto) && ($CotizacionHotel >= $cotHotel)){
 					$mensaje  = sprintf("La solicitud de viaje <strong>%05s</strong> ha sido cotizada por <strong>Agencia</strong>.",$t_id);
 					$tramite->Modifica_Etapa($t_id, SOLICITUD_ETAPA_COTIZADA, FLUJO_SOLICITUD, $destinatario, "");
 				}else{
 					$mensaje  = sprintf("La solicitud de viaje <strong>%05s</strong> no ha terminado de ser cotizada por la <strong>Agencia</strong>.",$t_id);
 					$tramite->Modifica_Etapa($t_id, SOLICITUD_ETAPA_EN_COTIZACION, FLUJO_SOLICITUD, $destinatario, "");
 				}	
 			}
 						
 		}else{
 			$mensaje  = sprintf("La solicitud de viaje <strong>%05s</strong> no ha terminado de ser cotizada por la <strong>Agencia</strong>.",$t_id);
 			$tramite->Modifica_Etapa($t_id, SOLICITUD_ETAPA_EN_COTIZACION, FLUJO_SOLICITUD, $destinatario, "");
 		}
		
 	}
	 
 	$tramite->EnviaNotificacion($t_id, $mensaje, $u_id, $destinatario, "0", "");

 	
 	$dato="";
 	
 	//$_POST["hotelesIngresados"] indica el numero de hoteles cotizados-pin-pong Agencia empleado
 	$itinerariosCotizados = $_POST["hotelesIngresados"];
 	
 	//Se realiza el objeto para la clase CViaje
 	$CViaje = new C_SV();
 	$bandera=$_POST['banderaHotel'];
 	
 	//eliminamos los hoteles, pero no los cotizados por el usuario
 	//svi_hotel_agencia=1 : agencia - svi_hotel_agencia=0 : usuario
 	for ($i = 1; $i <= $_POST['rowCount']; $i++) {
 		$svi_id	= $_POST["svi_id".$i];
 		$hotelAgencia = 0;
 		$hotelAgencia = $tramite->hotelesAgencia($svi_id);
 		 		
 		if($hotelAgencia == 1){ 			
 			$query_elimina_hotel="DELETE FROM hotel WHERE svi_id = {$svi_id}"; 			
 			$cnn->ejecutar($query_elimina_hotel);
 		}	 	
 	}
 	
 	//indica si esta dentro del pin-pong Agencia Empleado 
 	if($bandera == 2 && $itinerariosCotizados != ""){
 		for ($i = 1; $i <= $_POST['rowCount']; $i++) {
 			$svi_id	= $_POST["svi_id".$i];
 			
 			//Actualizamos los datos de las cotizaciones de auto
 			$empresaAuto 	= $_POST["empresaAuto".$i];
 			$tipoAuto 		= $_POST["tipoAuto".$i];
 			$diasRenta 		= $_POST["diasRenta".$i];
 			$costoDia 		= str_replace(',', '',$_POST["costoDia".$i]);
 			$totalAuto 		= str_replace(",","",$_POST["totalAuto".$i]);
 			$montoPesos 	= str_replace(",","",$_POST["montoPesos".$i]);
 			$divisaAuto		= $_POST['tipoDivisa'.$i];
 			$hotel_agencia	= $_POST["hotel_agencia".$i];
 			$auto_agencia	= $_POST["auto_agencia".$i];
 			
 			//Realizamos la inserccion de el auto 			
 			if($auto_agencia == 1){ 				 				
 				$CViaje->addAutoAE($empresaAuto,$tipoAuto,$diasRenta,$costoDia,$totalAuto,$montoPesos,$divisaAuto,$svi_id);
 			}
 			
 			if($hotel_agencia == 1){
 				for($j = 1 ; $j <= $itinerariosCotizados; $j++){
 					if($_POST['itinerarioHotelEA'.$j] == $i){
 						$sHotel = $_POST['nombreHotelEA'.$j];
 						$sCiudad = $_POST['ciudadHotelEA'.$j];
 						$sComentario = $_POST['comentarioHotelEA'.$j];
 						$stipoHotel = $_POST['tipoHotelEA'.$j];
 						$sNoches = $_POST['nochesHotelEA'.$j];
 						$sDivisa = $_POST['divisaHotelEA'.$j];
 						//transformamos la divisa en su valor numerico
 						if(($sDivisa == "MXN") || ($sDivisa == 1)){
 							$divisaHotel=1;
 						}else if(($sDivisa == "USD")||($sDivisa == 2)){
 							$divisaHotel=2;
 						}else if(($sDivisa == "EUR") || ($sDivisa == 3)){
 							$divisaHotel=3;
 						}
 						$sLlegada = $_POST['llegadaHotelEA'.$j];
 						$sSalida = $_POST['salidaHotelEA'.$j];
 						
 						$sNo_reservacion = 0;
 						if($_POST['reservacionHotelEA'.$j] != ''){
 							$sNo_reservacion = $_POST['reservacionHotelEA'.$j];
 						}
 						
 						//datos relacionados con los montos
 						$sCostoNoche = $_POST['montonochesHotelEA'.$j];
 						//subtotal es el resultado de la multiplicacion de las noches por el costo por noche
 						$sSubtotal = $sNoches * $sCostoNoche;
 						$sIva = $_POST['ivaHotelEA'.$j];
 						//El total sera la suma del iva y su  subtotal
 						$sTotal = $sSubtotal + $sIva;
 						$sMontoH_pesos = $_POST['h_total_pesos'.$j];
 						//datos de fecha
 						$date = explode("/", $sLlegada);
 						if (count($date) != 3) {
 							return "";
 						}
 						$sLlegada = $date[2] . "-" . $date[1] . "-" . $date[0];
 						$date = explode("/", $sSalida);
 						if (count($date) != 3) {
 							return "";
 						}
 						$sSalida = $date[2] . "-" . $date[1] . "-" . $date[0];
 						
 						// Insertamos el hotel correspondiente
 						$hoteles->agregarHotel($svi_id, $divisaHotel, $sComentario, $stipoHotel, $sCostoNoche, $sHotel, $sIva, $sLlegada, $sMontoH_pesos, $sCiudad, $sNoches, $sNo_reservacion, $sSalida, $sSubtotal, $sTotal, 0);
 					}
 				}
 			} 			
 		} 			
 	}else{
 		// Ingresa a la primera cotizacion normal se toman los datos de las tablas virtuales.
 		for ($i = 1; $i <= $_POST['rowCount']; $i++) {
 			$empresaAuto 	= $_POST["empresaAuto".$i];
 			$tipoAuto 		= $_POST["tipoAuto".$i];
 			$diasRenta 		= $_POST["diasRenta".$i];
 			$costoDia 		= str_replace(',', '',$_POST["costoDia".$i]);
 			$totalAuto 		= str_replace(",","",$_POST["totalAuto".$i]);
 			$montoPesos 	= str_replace(",","",$_POST["montoPesos".$i]);
 			$divisaAuto		= $_POST['tipoDivisa'.$i];
 			$svi_id 		= $_POST["svi_id".$i];
 			$hotel_agencia	= $_POST["hotel_agencia".$i];
 			$auto_agencia	= $_POST["auto_agencia".$i];
 			
 			if($auto_agencia == 1){ 				
 				$CViaje->addAutoAE($empresaAuto,$tipoAuto,$diasRenta,$costoDia,$totalAuto,$montoPesos,$divisaAuto,$svi_id);				
 			}
 			
 			if($hotel_agencia == 1 && $_POST['rowCount_hotel'.$i] >0){
 				for($j =1; $j <= $_POST['rowCount_hotel'.$i]; $j++){
 					$sCiudad = $_POST['ciudad_'.$i.'_'.$j];
 					$sNoches = $_POST['noches_'.$i.'_'.$j];
 					$sHotel = $_POST['hotel_'.$i.'_'.$j];
 					$sCostoNoche = $_POST['costoNoche_'.$i.'_'.$j];
 					$stipoHotel = $_POST['tipoHotel_'.$i."_".$j];
 					$sLlegada = $_POST['llegada_'.$i.'_'.$j];
 					$sSalida = $_POST['salida_'.$i.'_'.$j];
 					$sIva = $_POST['iva_'.$i.'_'.$j];
 					
 					$sNo_reservacion = 0;
 					if($_POST['noreservacion_'.$i.'_'.$j] != ''){
 						$sNo_reservacion = $_POST['noreservacion_'.$i.'_'.$j];
 					}
 					
 					$sTotal = $_POST['total_'.$i.'_'.$j];
 					$sMontoH_pesos = $_POST['montoP_'.$i.'_'.$j];
 					$sDivisa = $_POST['selecttipodivisa_'.$i.'_'.$j];
 					$sComentario = $_POST['comentario_'.$i.'_'.$j];
 					$sSubtotal = $_POST['subtotal_'.$i.'_'.$j];
 					$date = explode("/", $sLlegada);
 					if (count($date) != 3) {
 						return "";
 					}
 					$sLlegada = $date[2] . "-" . $date[1] . "-" . $date[0];
 					$date = explode("/", $sSalida);
 					if (count($date) != 3) {
 						return "";
 					}
 					$sSalida = $date[2] . "-" . $date[1] . "-" . $date[0];
 					
 					// Insertamos el hotel correspondiente
 					$hoteles->agregarHotel($svi_id, $sDivisa, $sComentario, $stipoHotel, $sCostoNoche, $sHotel, $sIva, $sLlegada, $sMontoH_pesos, $sCiudad, $sNoches, $sNo_reservacion, $sSalida, $sSubtotal, $sTotal, 0);
 				}
 			}
 				
 		}
 	}

 	$TA=str_replace(",","",$_POST['TotAuto']);
 	//error_log("TA".$TA);
 	$TH=str_replace(",","",$_POST['TotHotel']);
 	//error_log("TH".$TH);
 	$TAvion=$_POST['TotAvion'];
 	//error_log("TAvion".$TAvion); 	
 	$AutoFila=$_POST['rowDelAuto'];
 	//error_log("autofilas".$AutoFila);
 	$HotelFila=$_POST['rowDelHotel'];
 	//error_log("hotelfilas".$HotelFila);
 	$AvionFila=$_POST["rowDelAvion"];
 	//error_log("avionfilas".$AvionFila);
 	//cotizaciones que el empleado hay realizado (Auto y Hotel)
 	$cotizaAutoEmpleado=$_POST['rowDelAutoEmpleado'];
 	$cotizaHotelEmpleado=$_POST['rowDelHotelEmpleado'];
 	
 	$tramiteAgencia=new Tramite();
 	$tramiteAgencia->calculoTotalSolicitud($t_id, $TH, $TA,$TAvion, $AutoFila, $HotelFila,$AvionFila,$cotizaAutoEmpleado,$cotizaHotelEmpleado);
 	header("Location: ../notificaciones/index.php?okdevuelta");
}

// Autoriza la SOLICITUD DE GASTOS
if(isset($_POST['autorizar_sol_gts']) && isset($_POST['idt']) && $_POST['idt']!="" && isset($_POST['iu']) && $_POST['iu']!=""){
    // Datos del tramite
    $cnn = new conexion();
	$HObser = $_POST['historial_observaciones'];
    $sObser = $_POST['observ'];
    $idTramite = $_POST['idt'];
    $idrepresentante = $_POST['representante'];
    $iduser = $_POST['iu'];
    $fin_ruta = false;
    $etapa = SOLICITUD_GASTOS_ETAPA_APROBACION;
    
    $solicitudes = new SolicitudesGastos();
    $tramite = new Tramite();
    $rutaAuto = new RutaAutorizacion();
    $duenoActual = new Usuario();
    
    // Informacion del Tramite
    $tramite->Load_Tramite($idTramite);
    $t_ruta_autorizacion = $tramite->Get_dato("t_ruta_autorizacion");
    $t_dueno = $tramite->Get_dato("t_dueno");
    $iniciador = $tramite->Get_dato("t_iniciador");
    $t_delegado = $tramite->Get_dato("t_delegado");
    $t_etapa_actual = $tramite->Get_dato("t_etapa_actual");
    
    $iniciador = new Usuario();
	$iniciador->Load_Usuario_By_ID($tramite->Get_dato("t_iniciador"));
	$nombreIniciador = $iniciador->Get_dato('nombre');
    // Actualiza el campo de Observaciones
    if($sObser != ""){
    	$notificacion = new Notificacion();
    	$observaciones = $notificacion->anotaObservacion($t_dueno, $HObser, $sObser, FLUJO_SOLICITUD_GASTOS, "", $idrepresentante);
    	$solicitudes->actualizaObservaciones($observaciones, '', $idTramite);
    }
	$siguienteAutorizador = $rutaAuto->getSiguienteAprobador($idTramite,$t_dueno);
	$etapa = (empty($siguienteAutorizador)) ? SOLICITUD_GASTOS_ETAPA_APROBADA : SOLICITUD_GASTOS_ETAPA_APROBACION;
	$siguienteAutorizador = (empty($siguienteAutorizador)) ? $iniciador : $siguienteAutorizador;
	$duenoActual01 = new Usuario();
	if($duenoActual01->Load_Usuario_By_ID($t_dueno))
		$dueno_act_nombre = $duenoActual01->Get_dato('nombre');
	else{
		$agrup_usu = new AgrupacionUsuarios();
		$agrup_usu->Load_Grupo_de_Usuario_By_ID($t_dueno);
		$dueno_act_nombre = $agrup_usu->Get_dato("au_nombre");
	}
	if($etapa == SOLICITUD_GASTOS_ETAPA_APROBADA){
		$mensaje = sprintf("La Solicitud De Gastos<strong>%05s</strong> ha sido <strong>APROBADA</strong> por <strong>%05s</strong>.",$idTramite,$dueno_act_nombre);
		$queryCierre = sprintf("UPDATE tramites SET t_fecha_cierre=now() WHERE t_id='%s'", $idTramite);
		$cnn->ejecutar($queryCierre);
	}else{
		$mensaje = sprintf("La Solicitud De Gastos <strong>%05s</strong> ha sido <strong>AUTORIZADA</strong> por <strong>%05s</strong>.",$idTramite,$dueno_act_nombre);
	}
	$remitente = $t_dueno;
	$destinatario = $tramite->Get_dato("t_iniciador");
	
	//Notificación para el iniciador/empleado
	$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, 1, ""); //"0" para no enviar email y "1" para enviarlo	
	
	
	$tramite->Modifica_Etapa($idTramite, $etapa, FLUJO_SOLICITUD_GASTOS, $siguienteAutorizador, "");

	if($etapa != SOLICITUD_GASTOS_ETAPA_APROBADA){
		if($t_delegado != 0){
			$iniciador = new Usuario();
			$iniciador->Load_Usuario_By_ID($t_delegado);
			$nombreDelegado = $iniciador->Get_dato('nombre');
			$mensaje2 = sprintf("La Solicitud de Gasto <strong>%05s</strong> creada por el usuario: <strong>%s</strong> en nombre de: <strong>%s</strong> ha sido <strong>AUTORIZADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.",$idTramite, $nombreDelegado, $nombreIniciador, $dueno_act_nombre);
		}else{
			$mensaje2 = sprintf("La Solicitud de Gasto <strong>%05s</strong> creada por el usuario: <strong>%s</strong> ha sido <strong>AUTORIZADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.",$idTramite, $nombreIniciador, $dueno_act_nombre);
		}
		
		// Enviar Notificacion para Aprobador
		if(empty($siguienteAutorizador))
			$tramite->EnviaNotificacion($idTramite, $mensaje2, $t_dueno, $t_delegado, 1, "");
		else{
			$tramite->EnviaNotificacion($idTramite, $mensaje2, $t_dueno, $siguienteAutorizador, 1, "");
		}
	}	
	if($mobile){
     	echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=2&action=autorizar'>";
    } else {
    	echo ("<script language='Javascript'> location.href='http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=2&action=autorizar';</script>");
    }
}


//Rechaza la SOLICITUD DE GASTOS
if(isset($_POST['rechazar_sol_gts']) && isset($_POST['idt']) && $_POST['idt']!=""){
	// Datos del tramite
	$HObser = $_POST['historial_observaciones'];
	$sObser = $_POST['observ'];
	$idTramite = $_POST['idt'];
	$idrepresentante = $_POST['representante'];
	$iduser = $_POST['iu'];
	
	$solicitudes = new SolicitudesGastos();
	$tramite = new Tramite();
	$rutaAuto = new RutaAutorizacion();
	$duenoActual = new Usuario();
	$agrup_usu = new AgrupacionUsuarios();
	
	// Informacion del Tramite
	$tramite->Load_Tramite($idTramite);
	$t_ruta_autorizacion = $tramite->Get_dato("t_ruta_autorizacion");
	$t_dueno = $tramite->Get_dato("t_dueno");
	$iniciador = $tramite->Get_dato("t_iniciador");
	$t_delegado = $tramite->Get_dato("t_delegado");
	$t_etapa_actual = $tramite->Get_dato("t_etapa_actual");
	
	//Se obtienen los ids de Controlling y de Finanzas
	$agrup_usu->Load_Grupo_de_Usuario_By_Nombre("Finanzas");
	$idFinanzas = $agrup_usu->Get_dato("au_id");
	
	// Actualiza el campo de Observaciones
	if($sObser != ""){
		$notificacion = new Notificacion();
		$observaciones = $notificacion->anotaObservacion($t_dueno, $HObser, $sObser, FLUJO_SOLICITUD_GASTOS, "", $idrepresentante);
		$solicitudes->actualizaObservaciones($observaciones, '', $idTramite);
	}
	
	// Definición de mensajes
	$mensajeUsuario = $tramite->crearMensaje($idTramite, SOLICITUD_GASTOS_ETAPA_RECHAZADA, false, true, $idrepresentante); // Mensaje para usuario iniciador
//  	error_log("--->>".$mensajeUsuario);
	
	switch ($t_etapa_actual){
		case SOLICITUD_GASTOS_ETAPA_EN_APROBACION_POR_DIRECTOR:
			// Modificaremos la Etapa de la Comprobacion
			$tramite->Modifica_Etapa($idTramite, SOLICITUD_GASTOS_ETAPA_RECHAZADA_POR_DIRECTOR, FLUJO_SOLICITUD_GASTOS, $iniciador, "");
			
			// Notificamos al Usuario
			$tramite->EnviaNotificacion($idTramite, $mensajeUsuario, $iduser, $iniciador, 0, "");
			break;
		default:
			//Modificar la Etapa de la Solicitud
			$tramite->Modifica_Etapa($idTramite, SOLICITUD_GASTOS_ETAPA_RECHAZADA, FLUJO_SOLICITUD_GASTOS, $iniciador, "");
	
			// Enviar Notificacion para Usuario
			$tramite->EnviaNotificacion($idTramite, $mensajeUsuario, $t_dueno, $iniciador, 0, "");
			break;
	}
	
	if($mobile){
		echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=2&action=rechazar'>";
    } else {
		echo("<script language='Javascript'> location.href='http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=2&action=rechazar';</script>");
    }
}


// Autoriza la SOLICITUD DE VIAJE
if(isset($_POST['autorizar']) && isset($_POST['idT']) && $_POST['idT']!="" && isset($_POST['iu']) && $_POST['iu']!=""){
	$cnn = new conexion();
	$centoCostoNew=$_POST['centro_de_costos_new'];
	$centroCostoOld=$_POST['centro_de_costos_old'];
	
	// Datos del tramite
	$texto = $_POST['observ_to_emple'];
	$idTramite = $_POST['idT'];
	$delegado = $_POST['delegado'];
	$delegadoNombre = $_POST['delegadoNombre'];
	
	// Si se ha ingresado como delegado, se enviará como parámetro el id de la persona quien delego, 
	// de lo contrario será el id de la sesión actual.
	if($delegado != 0)
		$iduser = $delegado;
	else
		$iduser = $_POST['iu'];
	
	$sObser = $_POST['observ_to_emple'];
	
	$rutaAuto = new RutaAutorizacion();
	$t_dueno = $rutaAuto->getDueno($idTramite);
	
	$tramite = new Tramite();
	$tramite->Load_Tramite($idTramite);
	$t_autorizaciones = $tramite->Get_dato("t_autorizaciones");
	$t_etapa_actual = $tramite->Get_dato("t_etapa_actual");
	$t_dueno = $tramite->Get_dato("t_dueno");
	$t_delegado = $tramite->Get_dato("t_delegado");
	$HObser = $_POST['campo_historial'];
	
	$iniciador = new Usuario();
	$iniciador->Load_Usuario_By_ID($tramite->Get_dato("t_iniciador"));
	$nombreIniciador = $iniciador->Get_dato('nombre');
	
	// Insertamos observaciones
	if($texto != ""){
		$notificacion = new Notificacion();
		$observaciones = $notificacion->anotaObservacion($t_dueno, $HObser, $texto, FLUJO_SOLICITUD, "");
		$queryObserv = sprintf("UPDATE solicitud_viaje SET sv_observaciones = '%s' WHERE sv_tramite = '%s'", $observaciones, $idTramite);
		$cnn->ejecutar($queryObserv);
	}
	
	$duenoActual01 = new Usuario();
	if($duenoActual01->Load_Usuario_By_ID($t_dueno))
		$dueno_act_nombre = $duenoActual01->Get_dato('nombre');
	else{
		$agrup_usu = new AgrupacionUsuarios();
		$agrup_usu->Load_Grupo_de_Usuario_By_ID($t_dueno);
		$dueno_act_nombre = $agrup_usu->Get_dato("au_nombre");
	}
	
	$reqAgencia = $rutaAuto->requiereAgencia($idTramite);
	$siguienteAutorizador = $rutaAuto->getSiguienteAprobador($idTramite,$t_dueno);
	$etapa = (empty($siguienteAutorizador)) ? SOLICITUD_ETAPA_APROBADA : SOLICITUD_ETAPA_EN_APROBACION;
	$siguienteAutorizador = (empty($siguienteAutorizador)) ? '3000' : $siguienteAutorizador;
	
	if($t_etapa_actual == SOLICITUD_ETAPA_SEGUNDA_APROBACION){
		$siguienteAutorizador = $rutaAuto->getAgencia();
		$etapa = SOLICITUD_ETAPA_APROBADA;
	//Descontar el Anticipo
	}elseif($etapa == SOLICITUD_ETAPA_APROBADA && $reqAgencia == 0){
			//inicia la resta del presupuesto.
		$queryCierre = sprintf("UPDATE tramites SET t_fecha_cierre=now() WHERE t_id='%s'", $idTramite);
		$cnn->ejecutar($queryCierre);
		$totalSolicitud = str_replace(",","", $_POST['totalSolicitud']);
		$idCeco = $iniciador->Get_dato('idcentrocosto');
		$queryFCierre=sprintf("SELECT * FROM tramites where t_id = '%s'",$idTramite);
		$rstFCierre=$cnn->consultar($queryFCierre);
			while ($filaFR = mysql_fetch_array($rstFCierre)){
				$fechaCierre=($filaFR["t_fecha_cierre"]);
			}
		$f = explode("-",$fechaCierre);
		$mes = $f[1];
		$queryPresupuestoE=sprintf("SELECT * FROM periodo_presupuestal WHERE cc_id = '%s' AND MONTH(pp_periodo_inicial) = '%s'",$idCeco,$mes);
		$rstPresupuestoE=$cnn->consultar($queryPresupuestoE);
			while ($fila = mysql_fetch_array($rstPresupuestoE)){
				$idPP=($fila["pp_id"]);
				$DPPdisponible=($fila["pp_presupuesto_disponible"]-$totalSolicitud);
				$DPPutilizado=($fila["pp_presupuesto_utilizado"]+$totalSolicitud);
			}
		$queryActualizaE=sprintf("UPDATE periodo_presupuestal SET pp_presupuesto_disponible='%s',pp_presupuesto_utilizado='%s' WHERE pp_id='%s'",$DPPdisponible,$DPPutilizado,$idPP);
		$cnn->ejecutar($queryActualizaE);
	}
	
	if($etapa == SOLICITUD_ETAPA_APROBADA && $reqAgencia == 0){
		$mensaje = sprintf("La Solicitud de Viaje <strong>%05s</strong> ha sido <strong>APROBADA</strong> por <strong>%05s</strong>.",$idTramite,$dueno_act_nombre);
	}else{
		$mensaje = sprintf("La Solicitud de Viaje <strong>%05s</strong> ha sido <strong>AUTORIZADA</strong> por <strong>%05s</strong>.",$idTramite,$dueno_act_nombre);
	}
	$remitente = $t_dueno;
	$destinatario = $tramite->Get_dato("t_iniciador");
	
	//Modificar la Etapa de la Solicitud
	$tramite->Modifica_Etapa($idTramite, $etapa, FLUJO_SOLICITUD, $siguienteAutorizador, "");
	
	//Notificación para el iniciador/empleado
	$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, 1, ""); //"0" para no enviar email y "1" para enviarlo
	
	if($t_delegado != 0){
		$iniciador = new Usuario();
		$iniciador->Load_Usuario_By_ID($t_delegado);
		$nombreDelegado = $iniciador->Get_dato('nombre');
		$mensaje2 = sprintf("La Solicitud de Viaje <strong>%05s</strong> creada por el usuario: <strong>%s</strong> en nombre de: <strong>%s</strong> ha sido <strong>AUTORIZADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.",$idTramite, $nombreDelegado, $nombreIniciador, $dueno_act_nombre);
	}else{
		$mensaje2 = sprintf("La Solicitud de Viaje <strong>%05s</strong> creada por el usuario: <strong>%s</strong> ha sido <strong>AUTORIZADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.",$idTramite, $nombreIniciador, $dueno_act_nombre);
	}
	
	// Enviar Notificacion para Aprobador
	if(empty($siguienteAutorizador))
		$tramite->EnviaNotificacion($idTramite, $mensaje2, $t_dueno, $t_delegado, 1, "");
	else{
		$tramite->EnviaNotificacion($idTramite, $mensaje2, $t_dueno, $siguienteAutorizador, 1, "");
	}
	
	if($mobile){
		echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=1&action=autorizar'>";
	}else{
		echo("<script language='Javascript'> location.href='http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=1&action=autorizar';</script>");
	}    
}

if(isset($_POST['rechazar']) && isset($_POST['idT']) && $_POST['idT']!=""){
    // Datos del tramite
    $sObser=$_POST['observ_to_emple'];
    $idTramite=$_POST['idT'];    
    $delegado = $_POST['delegado'];
    $delegadoNombre = $_POST['delegadoNombre'];
    $valBoton= trim($_POST['rechazar']);
    $HObser = $_POST['campo_historial'];
    
    $cnn = new conexion();
    
    //error_log("valor boton".$valBoton);
    
    if($valBoton == "Cancelar"){
    	$accion = "CANCELADA";
    }else{
    	$accion = "RECHAZADA";
    }
    
    // Si se ha ingresado como delegado, se enviará como parámetro el id de la persona quien delego,
    // de lo contrario será el id de la sesión actual.
    if($delegado != 0){
    	$iduser = $delegado;
    }else{
    	$iduser = $_POST['iu'];
    }
    
    // Envia el tramite a cancelacion
    $tramite = new Tramite();
    $tramite->Load_Tramite($idTramite);
    $iniciador = $tramite->Get_dato("t_iniciador");
    $aprobador = $tramite->Get_dato("t_dueno");
    $t_etapa_actual = $tramite->Get_dato("t_etapa_actual");
    $usuarioAprobador = new Usuario();
    $usuarioAprobador->Load_Usuario_By_ID($aprobador);
    
    if($usuarioAprobador->Load_Usuario_By_ID($aprobador)){
    	$usuarioAprobador->Load_Usuario_By_ID($aprobador);
    	$nombre_autorizador = $usuarioAprobador->Get_dato("u_nombre")." ".$usuarioAprobador->Get_dato("u_paterno");
    }else{
    	$agrup_usu = new AgrupacionUsuarios();
    	$agrup_usu->Load_Grupo_de_Usuario_By_ID($aprobador);
    	$nombre_autorizador = $agrup_usu->Get_dato("au_nombre");
    }
    
    // Actualiza el campo de observaciones
	if($sObser != ""){
		$notificacion = new Notificacion();
		$observaciones = $notificacion->anotaObservacion($aprobador, $HObser, $sObser, FLUJO_SOLICITUD, "");
		$queryObserv = sprintf("UPDATE solicitud_viaje SET sv_observaciones = '%s' WHERE sv_tramite = '%s'", $observaciones, $idTramite);
		$cnn->ejecutar($queryObserv);
	}
    
	// Regresa el monto apartado al ceco
	$Csv = new C_SV();
    $Cc=new CentroCosto();
    
    if($delegado != 0){
    	$duenoActual_delegado = new Usuario();
    	$duenoActual_delegado->Load_Usuario_By_ID($_POST['iu']);
    	$delegado_act_nombre = $duenoActual_delegado->Get_dato('nombre');
    	$mensaje = sprintf("La Solicitud de Viaje <strong>%05s</strong> ha sido <strong>%s</strong> por <strong>%s</strong> en nombre de: <strong>%s</strong>.",$idTramite, $accion, $delegado_act_nombre, $nombre_autorizador);
    }else{
    	$mensaje = sprintf("La Solicitud de Viaje <strong>%05s</strong> ha sido <strong>%s</strong> por <strong>%s</strong>.",$idTramite, $accion, $nombre_autorizador);
    }

    if($valBoton == "Cancelar"){    
    	$tramite->Modifica_Etapa($idTramite, SOLICITUD_ETAPA_CANCELADA, FLUJO_SOLICITUD, $iniciador, "");
    }else{
    	$tramite->EnviaNotificacion($idTramite, $mensaje, $aprobador, $iniciador, "0", ""); //"0" para no enviar email y "1" para enviarlo
    	$tramite->Modifica_Dueno($idTramite, SOLICITUD_ETAPA_RECHAZADA, FLUJO_SOLICITUD, $aprobador, $iniciador);
    }
    
    // Asigno el valor que trae del campo de texto, debido a que en el index, la variable de sesión, se convertia en un entero(ID del usuario delegado).
    $_SESSION['delegado'] = $delegadoNombre;
    
	if($mobile){
		if($valBoton == "Cancelar"){
			echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=1&action=cancel'>";
		}else{
			echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=1&action=rechazar'>";
		}
	} else {
		if($valBoton == "Cancelar"){
			echo("<script language='Javascript'> location.href='http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=1&action=cancel';</script>");
		}else{
			echo("<script language='Javascript'> location.href='http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=1&action=rechazar';</script>");
		}
    }
}
/*
 * Aprobar solicitud por director
*
*/
if(isset($_POST['aprobar_si'])){
	// Datos del tramite
	$HObser=$_POST['historial_observaciones'];
	$sObser=$_POST['observ'];
	$idTramite=$_POST['idt'];
	$delegado = $_POST['delegado'];
	$delegadoNombre = $_POST['delegadoNombre'];
	$iduser = $_POST['iu'];

	// Actualiza el campo de observaciones
	$Csv=new C_SV();
	$tramite = new Tramite();
	$ruta_autorizacion = new RutaAutorizacion();

	$tramite->Load_Tramite($idTramite);
	$t_ruta_autorizacion = $tramite->Get_dato("t_ruta_autorizacion");
	$t_dueno = $tramite->Get_dato("t_dueno");
	$iniciador = $tramite->Get_dato("t_iniciador");
	$t_delegado = $tramite->Get_dato("t_delegado");
	// Buscamos quien debe aprobar esta solicitud
	/**
	 * Validacion y guardado de excepcion de presupuesto
	 **/
	$presupuesto = new Presupuesto();
	$objetoPresupuesto = $presupuesto->validarPresupuesto($idTramite);
	$ruta_autorizacion->generaExcepcion($idTramite, $objetoPresupuesto);
	$ruta_autorizacion->generaRutaAutorizacionSolicitudGastos($idTramite, $iniciador);
	$excepciones = $ruta_autorizacion->get_Excepciones($idTramite);
	$ruta_autorizacion->agregaAutorizadoresExcedentes($idTramite, $excepciones);
	$aprobador = $ruta_autorizacion->getAprobador($idTramite, $iduser);

	//$Csv->Load_Solicitud_Invitacion_Tramite($idTramite);
	if($sObser != ""){
		$notificacion = new Notificacion();
		$observaciones = $notificacion->anotaObservacion($t_dueno, $HObser, $sObser, FLUJO_SOLICITUD_INVITACION, "");
		$Csv->Modifica_Observaciones($idTramite, $observaciones, FLUJO_SOLICITUD_INVITACION);
	}

	// Envia el tramite a aprobacion
	$usuarioAprobador = new Usuario();
	$usuarioAprobador->Load_Usuario_By_ID($aprobador);
	$duenoActual = new Usuario();
	$duenoActual->Load_Usuario_By_ID($iduser);
	$nombreUsuario = $duenoActual->Get_dato('nombre');

	$tramite->Load_Tramite($idTramite);
	$rutaAutorizacion=$tramite->Get_dato('t_ruta_autorizacion');
	$tramite->Modifica_Etapa($idTramite, SOLICITUD_INVITACION_ETAPA_APROBACION, FLUJO_SOLICITUD_INVITACION, $aprobador, $rutaAutorizacion);

	$duenoActual->Load_Usuario_By_ID($t_delegado);
	$nombreDelegado = $duenoActual->Get_dato('nombre');
	$mensaje = sprintf("La solicitud de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> en nombre de: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $idTramite, $nombreDelegado, $nombreUsuario);

	$remitente = $iduser;
	$destinatario = $aprobador;
	$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "1", ""); //"0" para no enviar email y "1" para enviarlo
	
	
	if($mobile){
		echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=2&action=autorizar'>";
	} else {
		echo ("<script language='Javascript'> location.href='http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=2&action=autorizar';</script>");
	}
}

if(isset($_POST['rechazar_si'])){
	// Datos del tramite
	$HObser=$_POST['historial_observaciones'];
	$sObser=$_POST['observ'];
	$idTramite=$_POST['idt'];
	$delegado = $_POST['delegado'];
	$iduser = $_POST['iu'];

	// Actualiza el campo de observaciones
	$Csv=new C_SV();
	$tramite = new Tramite();
	$tramite->Load_Tramite($idTramite);
	$t_dueno = $tramite->Get_dato("t_dueno");
	$iniciador = $tramite->Get_dato("t_iniciador");
	$t_delegado = $tramite->Get_dato("t_delegado");

	//$Csv->Load_Solicitud_Invitacion_Tramite($idTramite);
	if($sObser != ""){
		$notificacion = new Notificacion();
		$observaciones = $notificacion->anotaObservacion($t_dueno, $HObser, $sObser, FLUJO_SOLICITUD_INVITACION, "");
		//$observaciones = anotaObservacion($t_dueno,$HObser,$sObser);
		$Csv->Modifica_Observaciones($idTramite, $observaciones, FLUJO_SOLICITUD_INVITACION);
	}
	 
	$duenoActual = new Usuario();
	$duenoActual->Load_Usuario_By_ID($t_dueno);

	// Modifica la etapa
	$tramite->Modifica_Dueno($idTramite, SOLICITUD_INVITACION_ETAPA_RECHAZADA_POR_DIRECTOR, FLUJO_SOLICITUD_INVITACION, $t_dueno, $iniciador);

	//Envia notificacion al iniciador de la solicitud de invitacion ----------------------------------
	$mensaje = sprintf("La Solicitud de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>RECHAZADA</strong> por <strong>%s</strong>.", $idTramite, $duenoActual->Get_dato('nombre'));

	$remitente = $t_dueno;
	$destinatario = $iniciador;
	$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "0", ""); //"0" para no enviar email y "1" para enviarlo

	// Asigno el valor que trae del campo de texto, debido a que en el index, la variable de sesión, se convertia en un entero(ID del usuario delegado).
	$_SESSION['delegado'] = $delegadoNombre;
	
	
	if($mobile){
		echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=2&action=rechazar'>";
	}else{
		echo ("<script language='Javascript'> location.href='http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=2&action=rechazar';</script>");
	}
}

if(isset($_POST['aprobar_sv'])){
	// Datos del tramite
	$texto = $_POST['observ_to_emple'];
    $idTramite=$_POST['idT'];
    $delegado = $_POST['delegado'];
    $delegadoNombre = $_POST['delegadoNombre'];
    $iduser = $_POST['iu'];
    $sObser = $_POST['observ_to_emple'];
    $HObser = $_POST['campo_historial'];
    
    $cnn = new conexion();
    $tramite = new Tramite();
    $tramite->Load_Tramite($idTramite);
    $t_delegado = $tramite->Get_dato('t_delegado');
    
    // Limpiamos los campos de
    $query = sprintf("UPDATE tramites SET t_autorizaciones = '', t_autorizaciones_historial = '' WHERE t_id = '%s'", $idTramite);
    //error_log($query);
    $cnn->ejecutar($query);
    
    //Pasamos el parametro de la zona geografica para la validacion de la ruta de autorizacion
    $ruta_autorizacion = new RutaAutorizacion();
	/**
	 * Validacion y guardado de excepcion de presupuesto
	 **/
	$presupuesto = new Presupuesto();
	$objetoPresupuesto = $presupuesto->validarPresupuesto($idTramite);
	$ruta_autorizacion->generaExcepcion($idTramite, $objetoPresupuesto);
	
	$ruta_autorizacion->generaRutaAutorizacionSolicitudViaje($idTramite,$iduser,true);
	$excepciones = $ruta_autorizacion->get_Excepciones($idTramite);
	$ruta_autorizacion->agregaAutorizadoresExcedentes($idTramite, $excepciones);
	$aprobador = $ruta_autorizacion->getAprobador($idTramite, $iduser);
    //error_log($es_agencia);
    $duenoActual = new Usuario();
    $duenoActual->Load_Usuario_By_ID($iduser);
    $nombreUsuario = $duenoActual->Get_dato('nombre');
    $duenoActual->Load_Usuario_By_ID($t_delegado);
    $nombreDelegado = $duenoActual->Get_dato('nombre');
    
    //Se agrega la observacion tambien a la tabla de observaciones
    //error_log("observaciones......".$sObservaciones);
    $iniciador = $tramite->Get_dato("t_iniciador");
	
	if($sObser != ""){
    	$notificacion = new Notificacion();
    	$observaciones = $notificacion->anotaObservacion($iniciador, $HObser, $sObser, FLUJO_SOLICITUD, "");
    	$queryObserv = sprintf("UPDATE solicitud_viaje SET sv_observaciones = '%s' WHERE sv_tramite = '%s'", $observaciones, $idTramite);
    	$cnn->ejecutar($queryObserv);
    }
    
    //error_log($aprobador);
    $tramite->Modifica_Etapa($idTramite, SOLICITUD_ETAPA_EN_APROBACION, FLUJO_SOLICITUD, $aprobador,"");
    $mensaje = sprintf("La Solicitud de Viaje <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> en nombre de: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $idTramite, $nombreDelegado, $nombreUsuario);
        
    $remitente = $iduser;
    $destinatario = $aprobador;
    $tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "1", ""); //"0" para no enviar email y "1" para enviarlo
	
	if($mobile){
		echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=1&action=autorizar'>";
	} else {
		echo ("<script language='Javascript'> location.href='http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=1&action=autorizar';</script>");
	}
}

if(isset($_POST['rechazar_sv'])){
	// Datos del tramite
	$texto = $_POST['observ_to_emple'];
    $idTramite=$_POST['idT'];
    $delegado = $_POST['delegado'];
    $delegadoNombre = $_POST['delegadoNombre'];
    $iduser = $_POST['iu'];
    $sObser = $_POST['observ_to_emple'];
    $HObser = $_POST['campo_historial'];
    
	$cnn = new conexion();
	// Envia el tramite a cancelacion
    $tramite = new Tramite();
    $tramite->Load_Tramite($idTramite);
    $iniciador = $tramite->Get_dato("t_iniciador");
    $aprobador = $tramite->Get_dato("t_dueno");
    $t_etapa_actual = $tramite->Get_dato("t_etapa_actual");
    $usuarioAprobador = new Usuario();
    $usuarioAprobador->Load_Usuario_By_ID($aprobador);
    $nombreDueno = $usuarioAprobador->Get_dato('nombre');
    
    // Actualiza el campo de observaciones
	if($sObser != ""){
		$notificacion = new Notificacion();
		$observaciones = $notificacion->anotaObservacion($aprobador, $HObser, $sObser, FLUJO_SOLICITUD, "");
		$queryObserv = sprintf("UPDATE solicitud_viaje SET sv_observaciones = '%s' WHERE sv_tramite = '%s'", $observaciones, $idTramite);
		$cnn->ejecutar($queryObserv);
	}
    
    $mensaje = sprintf("La Solicitud de Viaje <strong>%05s</strong> ha sido <strong>RECHAZADA</strong> por <strong>%s</strong>.",$idTramite, $nombreDueno);
	
	// Modifica la etapa
	$tramite->Modifica_Dueno($idTramite, SOLICITUD_ETAPA_RECHAZADA_POR_DIRECTOR, FLUJO_SOLICITUD, $aprobador, $iniciador);
	
	$remitente = $aprobador;
	$destinatario = $iniciador;
	$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "0", ""); //"0" para no enviar email y "1" para enviarlo
	
	// Asigno el valor que trae del campo de texto, debido a que en el index, la variable de sesión, se convertia en un entero(ID del usuario delegado).
	$_SESSION['delegado'] = $delegadoNombre;
	
	if($mobile){
		echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=1&action=rechazar'>";
	}else{
		echo ("<script language='Javascript'> location.href='http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=1&action=rechazar';</script>");
	}
}

/*
 * Muestra la pantalla de Autorización
 */

if((isset($_GET['id']) && $_GET['id']!="" && isset( $_GET['view'])) || isset( $_GET['edit_view'] )&& isset( $_GET['id'] )){
				
    $empleado = $_SESSION["idusuario"];
    $cnn = new conexion();
    $aux = array();

   $query="SELECT t_flujo from tramites where t_id=".$_GET['id'] ;

    $rst=$cnn->consultar($query);
    $datos=mysql_fetch_assoc($rst);
    if($datos["t_flujo"]==FLUJO_SOLICITUD_GASTOS) {
        require_once("solicitud_view_edit_gastos.php");            
    } elseif($datos["t_flujo"]==FLUJO_SOLICITUD){
        /*if($mobile){
            require_once("solicitud_view_edit_viaje_movil.php");    
        } else {*/
            require_once("solicitud_view_edit_viaje.php");    
        //}
        
    }

?>
	</html>
<?php		
}

if((isset($_GET['id']) && $_GET['id']!="" && isset( $_GET['view'])) || isset( $_GET['edit_view'] )&& isset( $_GET['id'] )){

	$empleado = $_SESSION["idusuario"];
	$cnn = new conexion();
	$aux = array();

		$query="SELECT t_flujo from tramites where t_id=".$_GET['id'] ;

	$rst=$cnn->consultar($query);
	$datos=mysql_fetch_assoc($rst);
	if($datos["t_flujo"]==FLUJO_SOLICITUD_GASTOS) {
		require_once("solicitud_view_edit_gastos.php");
	} elseif($datos["t_flujo"]==FLUJO_SOLICITUD){
		/*if($mobile){
			require_once("solicitud_view_edit_viaje_movil.php");
		} else {*/
			require_once("solicitud_view_edit_viaje.php");
		//}

	}

	?>
	</html>
<?php		
} 

if(isset($_POST['autorizar_cotizacion']) && isset($_POST['idT']) && $_POST['idT']!="" && isset($_POST['iu']) && $_POST['iu']!=""){
	$idTramite = $_POST['idT'];
	$sObser = $_POST['observ_to_emple'];
	$sv_total = str_replace(',', '',$_POST['totalSolicitud']);
	$delegado = $_POST['delegado'];
	$HObser = $_POST['campo_historial'];
	
	$cnn = new conexion();
	$delegados = new Delegados();
	$ruta_autorizacion = new RutaAutorizacion();
	
	$tramite=new Tramite();
	$tramite->Load_Tramite($idTramite);
	$t_dueno = $tramite->Get_dato("t_dueno");
	$t_delegado = $tramite->Get_dato("t_delegado");
	
	//error_log("Delegado: ".$delegado);
	//Datos usuario
	if($delegado == 0){
		$iduser = $_POST['iu'];
		$delegado = 0;
	}else{
		$iduser = $delegado;
		$delegado = $_POST['iu'];
	}
	
	$existeDelegado = $delegados->existenciaDelegado($iduser, $delegado);
	/*
	 * Actualizamos el campo de t_delegado, pues si es el delegado quien aprueba la cotización entonces hay que guardar su ID.
	*/
	$tramite->actualizaDelegado($idTramite, $delegado);
	
	/**
	 * Validacion y guardado de excepcion de presupuesto
	 **/
	$presupuesto = new Presupuesto();
	$objetoPresupuesto = $presupuesto->validarPresupuesto($idTramite);
	$ruta_autorizacion->generaExcepcion($idTramite, $objetoPresupuesto);
	
	if($existeDelegado){
		$duenoActual = new Usuario();
		$duenoActual->Load_Usuario_By_ID($delegado);
		$nombreUsuario = $duenoActual->Get_dato('nombre');
		
		$iniciador = $tramite->Get_dato("t_iniciador");
		
		if($sObser != ""){
			$notificacion = new Notificacion();
			$observaciones = $notificacion->anotaObservacion($delegado, $HObser, $sObser, FLUJO_SOLICITUD, "");
			$queryObserv = sprintf("UPDATE solicitud_viaje SET sv_observaciones = '%s' WHERE sv_tramite = '%s'", $observaciones, $idTramite);
			$cnn->ejecutar($queryObserv);
		}else{
			$queryObserv = sprintf("UPDATE solicitud_viaje SET sv_observaciones = '%s' WHERE sv_tramite = '%s'", $HObser, $idTramite);
			$cnn->ejecutar($queryObserv);
		}
		
		$tramite->Modifica_Etapa($idTramite, SOLICITUD_ETAPA_EN_APROBACION_POR_DIRECTOR, FLUJO_SOLICITUD, $iduser,"");
		$mensaje = sprintf("La Solicitud de Viaje <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> en su nombre y requiere de su aprobaci&oacute;n.", $idTramite, $nombreUsuario);
		
		$remitente = $delegado;
		$destinatario = $iniciador;
		$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "1", ""); //"0" para no enviar email y "1" para enviarlo
		
		if($mobile){
			echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?okdirector'>";
		}else{
			echo ("<script language='Javascript'> location.href='http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?okdirector';</script>");
		}
				
	}else{
		//$iduser=$_POST['iu'];
		if($sObser != ""){
			$notificacion = new Notificacion();
			$observaciones = $notificacion->anotaObservacion($iduser, $HObser, $sObser, FLUJO_SOLICITUD, "");
			$queryObserv = sprintf("UPDATE solicitud_viaje SET sv_observaciones = '%s' WHERE sv_tramite = '%s'", $observaciones, $idTramite);
			$cnn->ejecutar($queryObserv);
		}else{
			$queryObserv = sprintf("UPDATE solicitud_viaje SET sv_observaciones = '%s' WHERE sv_tramite = '%s'", $HObser, $idTramite);
			$cnn->ejecutar($queryObserv);
		}
		
		// Alteraremos el total de la solicitud, se anotará el total cotizado por las agencias
		$query = sprintf("UPDATE solicitud_viaje SET sv_total = '%s' WHERE sv_tramite = '%s'", $sv_total, $idTramite);
		$cnn->ejecutar($query);
		$ruta_autorizacion->generaRutaAutorizacionSolicitudViaje($idTramite, $iduser, true);
		$excepciones = $ruta_autorizacion->get_Excepciones($idTramite);
		$ruta_autorizacion->agregaAutorizadoresExcedentes($idTramite, $excepciones);
		$aprobador = $ruta_autorizacion->getSiguienteAprobador($idTramite, $iduser);
		
		//$ruta_autorizacion->agregarAutorizacion($idusuario, $tramite);
		$tramite->Modifica_Etapa($idTramite, SOLICITUD_ETAPA_EN_APROBACION, FLUJO_SOLICITUD, $aprobador, "");
		//FIXME código para notificacion.	
		$duenoActual = new Usuario();
	    $duenoActual->Load_Usuario_By_ID($iduser);
	    $nombreIniciador = $duenoActual->Get_dato('nombre');
	    
	    $mensaje = sprintf("La Solicitud de Viaje <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $idTramite, $nombreIniciador);
		$mensajeemail = sprintf("La Solicitud de Viaje <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $idTramite, $nombreIniciador);
		
		$tramite->EnviaNotificacion($idTramite, $mensaje, $iduser, $aprobador, "1", $mensajeemail); //"0" para no enviar email y "1" para enviarlo
		
		if($mobile){
			echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?okcotizacion'>";
		}else{
			echo ("<script language='Javascript'> location.href='http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?okcotizacion';</script>");
		}
	}
}
// else {
// 	echo "<font color='#FF0000'><b>Se encontr&oacute; error en el tr&aacute;mite. Verifique e intente de nuevo.</b></font>";
// }
?>
