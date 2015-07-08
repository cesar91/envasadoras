<?PHP 
require_once("../../lib/php/constantes.php");
require_once("$RUTA_A/Connections/fwk_db.php");
require_once("$RUTA_A/flujos/solicitudes/services/C_SV.php");
require_once("$RUTA_A/functions/utils.php");
require_once("$RUTA_A/functions/RutaAutorizacion.php");
require_once "$RUTA_A/functions/Notificacion.php";
require_once("$RUTA_A/functions/Delegados.php");
require_once("$RUTA_A/flujos/comprobaciones/services/func_comprobacion.php");

//obtener el centro de costos asociado al  empleado
//==========================================================================
if(isset($_POST['guardarComp']) || isset($_POST['guardarprevComp']) || isset($_POST['autorizar_cotizacion']) || isset($_POST['envia_a_director'])){
	
    if(isset($_POST['rowCount']) || $_POST['rowCount'] != 0){
        // Datos del empleado    	
        $iduser = 0;
		$delegado = 0;
        $numempleado = $_POST["empleado"];
        $idempresa = $_POST["empresa"];
		$sMotivo = $_POST['motive'];
		$sesionDelegado = $_POST['delegado'];
		
		$CViaje = new C_SV();
		$delegados = new Delegados();
		$hoteles = new Hotel();
		
    	if($sesionDelegado != 0){
			$iduser = $_POST["idusuario"];
			$delegado = $_POST["delegado"];
		}else{
			$iduser = $_POST["idusuario"];
		}
		$guardadoPrevio=false;
		$autorizaCotizacionRechazada=false;
		$sCat_cecos = $_POST['cat_cecos_cargado'];
		
		// Fecha de viaje (salida)
        $sFecha = $_POST['fecha'];
        $FechaMySQL = fecha_to_mysql($sFecha);
		$total_solicitud = str_replace(',', '',$_POST["totalSol"]);		
        $sTipo_viaje = $_POST['select_tipo_viaje_pasaje_val'];
       
		switch($sTipo_viaje){
			case 1:
				$sTipo_viaje = "Sencillo";
				break;
			case 2:
				$sTipo_viaje = "Redondo";
				break;
			case 3:
				$sTipo_viaje = "Multidestinos";
				break;
		}
		// Registra nuevo tramite
        $tramite = new Tramite();
        $tramite->insertar("BEGIN WORK");
		if(isset($_POST['tramiteID'])){
			$tramiteID = $_POST['tramiteID'];
			//error_log("ACTUALIZANDO..............".$tramiteID);			
			//*****************************************************************************
			//Elimina datos del tramite menos el tramite
			$cnn = new conexion();
			//query que obtendra el id de la solicitud de viajes dependiendo a el tramite seleccionado
			$query_obtiene_idsv="SELECT sv_id FROM solicitud_viaje WHERE sv_tramite=".$tramiteID."";
			$res = $cnn->consultar($query_obtiene_idsv);//$res = mysql_query($query_obtiene_idsv);
			if($row = mysql_fetch_array($res)){
				$id_sv=$row[0];
			}
			
			//Tomamos los datos del avion ( si la solicitud fue rechazada)
			$tramite->Load_Tramite($tramiteID);
			if(($tramite->Get_dato('t_etapa_actual') == SOLICITUD_ETAPA_RECHAZADA) || ($tramite->Get_dato('t_etapa_actual') == SOLICITUD_ETAPA_RECHAZADA_POR_DIRECTOR)){
				
				//reiniciamos los campos t_autorizaciones y t_ruta_autorizacion
				$queryR1="UPDATE tramites SET t_ruta_autorizacion='' WHERE t_id={$tramiteID}";
				$cnn->ejecutar($queryR1);
				$queryR2="UPDATE tramites SET t_autorizaciones='' WHERE t_id={$tramiteID}";
				$cnn->ejecutar($queryR2);
				
				$query = "SELECT 
				svi_aerolinea,
				svi_monto_vuelo,
				svi_iva,
				svi_tua,
				svi_monto_vuelo_total,
				svi_solicitud,
				svi_fecha_salida_avion,
				svi_fecha_regreso_avion,
				svi_tipo_aerolinea,				
				svi_monto_vuelo_cotizacion
				FROM sv_itinerario AS svi
				INNER JOIN solicitud_viaje AS sv
				ON sv.sv_id = svi.svi_solicitud
				WHERE svi.svi_solicitud = '{$id_sv}' GROUP BY svi_solicitud";
				$rst=$cnn->consultar($query);
				
				// Datos del avion
				$svi_aerolinea = mysql_result($rst,0,"svi_aerolinea");
				$svi_monto_vuelo = mysql_result($rst,0,"svi_monto_vuelo");
				$svi_iva = mysql_result($rst,0,"svi_iva");
				$svi_tua = mysql_result($rst,0,"svi_tua");
				$svi_monto_vuelo_total = mysql_result($rst,0,"svi_monto_vuelo_total");
				$fecha_salida = mysql_result($rst,0,"svi_fecha_salida_avion");
				$fecha_llegada = mysql_result($rst,0,"svi_fecha_regreso_avion");
				$svi_tipo_aerolinea = mysql_result($rst,0,"svi_tipo_aerolinea");				
				$svi_monto_vuelo_cotizacion = mysql_result($rst,0,"svi_monto_vuelo_cotizacion");
				
				$svi_monto_vuelo = (empty($svi_monto_vuelo)) ? '0.00' : $svi_monto_vuelo;
				$svi_iva = (empty($svi_iva)) ? '0.00' : $svi_iva;
				$svi_tua = (empty($svi_tua)) ? '0.00' : $svi_tua;
				$svi_monto_vuelo_total = (empty($svi_monto_vuelo_total)) ? '0.00' : $svi_monto_vuelo_total;
				$svi_monto_vuelo_cotizacion = (empty($svi_monto_vuelo_cotizacion)) ? '0.00' : $svi_monto_vuelo_cotizacion;
				$fecha_salida = (empty($fecha_salida)) ? '0000-00-00' : $fecha_salida;
				$fecha_llegada = (empty($fecha_llegada)) ? '0000-00-00' : $fecha_llegada;
			}
			
			//query que obtendra el id de itinerarios dependiendo al id de solicitud_viaje
			$query_obtiene_idsvi="SELECT svi_id FROM sv_itinerario WHERE svi_solicitud=".$id_sv."";
			$res2= $cnn->consultar($query_obtiene_idsvi);
			
			while ($campo = mysql_fetch_array($res2)){
				$id_svi = $campo["svi_id"];
				$query_elimina_hotel="DELETE FROM hotel WHERE svi_id=".$id_svi."";
				$cnn->ejecutar($query_elimina_hotel);			
				//query que eliminara el itinerario al cual este ligado a una  solicitud de viaje
				$query_elimina_itinerario="DELETE FROM sv_itinerario WHERE svi_id =".$id_svi."";
				$cnn->ejecutar($query_elimina_itinerario);				
			}
			//query que eliminara la solicitud de viaje con la que este ligado a el tramite seleccionado
			$query_elimina_sv="DELETE FROM solicitud_viaje WHERE sv_id=".$id_sv."";
			$cnn->ejecutar($query_elimina_sv);
			$sql = "DELETE FROM excepciones 
					WHERE ex_solicitud = ".$id_sv;
			$cnn->ejecutar($sql);
			//query que eliminara los conceptos de el tramite seleccionado
			$query_elimina_obsv="DELETE FROM sv_conceptos_detalle WHERE svc_detalle_tramite=".$tramiteID."";
			$cnn->ejecutar($query_elimina_obsv);
			//*****************************************************************************
			//Actualizar tramite
			$idTramite = $tramiteID;
			$tramite->Modifica_Etapa($idTramite, SOLICITUD_ETAPA_SIN_ENVIAR, FLUJO_SOLICITUD, "106", "");
			$query=sprintf("update tramites set t_fecha_ultima_modificacion = now() where t_id ='%s'",$tramiteID);
			$cnn->ejecutar($query);
			
		}else{
			$idTramite = $tramite->Crea_Tramite($iduser, $idempresa, SOLICITUD_ETAPA_SIN_ENVIAR, FLUJO_SOLICITUD, $sMotivo, $delegado);
			
			if ($idTramite <= 0){
				$CViaje->insertar("ROLL BACK");
				header("Location: ./index.php?errsave");
			}
		}
		
		if (isset($_POST['observ']) && $_POST['observ'] != ""){
            $sObservaciones = $_POST['observ'];
		}else{
            $sObservaciones = "";
		}
		
		if (isset($_POST['Accion'])== 'true') {
			$check =1;
		}else{
			$check=0;
		}	
		$total_anticipo = $_POST["totalanticipo"];
		
		 
		$idSolViaje = $CViaje->Add($sTipo_viaje, $sMotivo, $sObservaciones, $idTramite, $FechaMySQL, $sCat_cecos, $tramite->truncate($total_solicitud,2) ,$check,$total_anticipo);
		
		//se ingresaran las excepciones si es que existen sobre la fecha seleccionada de la solicitud de viaje.
		$cnn = new conexion();
		$mensajeExcept=$_POST['mensaje_excepcion'];
		$Except=$_POST['excepciones'];
		
		if($mensajeExcept != ""){
			$query_excep = sprintf("INSERT INTO excepciones (ex_id,ex_mensaje,ex_diferencia,ex_solicitud,ex_comprobacion,ex_comprobacion_detalle, ex_concepto, ex_solicitud_detalle, ex_solicitud_itinerario) 
								VALUES(DEFAULT,'%s','%s',%s,'%s','%s','%s','%s','%s')", $mensajeExcept, 0, $idSolViaje, 0, 0, 0, 0, 0);
			$cnn->ejecutar($query_excep); 		
		}
		
		//================Actualizamos el campo ex_solicitud para la escepcion del monto si es que rebaso.
		$query_excep_pre=sprintf("UPDATE excepciones SET ex_solicitud='%s' where ex_solicitud='%s'",$idSolViaje,1);
		$cnn->ejecutar($query_excep_pre);
		//================================================================================================
		if ($idSolViaje <= 0){				
			$CViaje->insertar("ROLL BACK");
			exit(header("Location: ./index.php?errsave"));
		}
        for ($i = 1; $i <= $_POST['rowCount']; $i++) {
			$sDestino = $_POST['destino'.$i];
			$sAgencia_auto = $_POST['CheckTEnviarAgencia'.$i];
			$sAgencia_hotel = $_POST['CheckHEnviarAgencia'.$i];
			$sFecha_regreso = $_POST['fechaLlegada'.$i];
			$sFecha_salida = $_POST['salida'.$i];
			$sHora_regreso = $_POST['select_hora_llegada'.$i];
			$sHora_salida = $_POST['hora'.$i];
			$sRenta_hotel = $_POST['CheckHAgencia'.$i];
			
			$noDiasViaje = $_POST['noDias'.$i];
			
			$sKilometraje = $_POST['kilometraje'.$i];
			
			if($sKilometraje == "" || $sKilometraje == "N/A"){
				$sKilometraje = 0;
			}else{
				$sKilometraje = $_POST['kilometraje'.$i];
			}
			
			$sOrigen = $_POST['origen'.$i];
			$sRenta_auto = $_POST['CheckTAgencia'.$i];
			$sMedio_transporte = $_POST['medio'.$i];
			$sTipo_transporte = $_POST['select_tipo_transporte'.$i];
			if($sTipo_transporte == "Seleccione..."){
				$sTipo_transporte="";
			}
			$sZona_geografica = $_POST['zona_geo'.$i];
			error_log("zona".$sZona_geografica);
			$sRegion = $_POST['regionId'.$i];
			
			if($sAgencia_hotel == "true"){
				$sAgencia_hotel = "1";
			}else{
				$sAgencia_hotel = "0";
			}
			if($sAgencia_auto == "true"){
				$sAgencia_auto = "1";
			}else{
				$sAgencia_auto = "0";
			}
			if($sRenta_hotel == "true"){
				$sRenta_hotel = "1";
			}else{
				$sRenta_hotel = "0";
				$sAgencia_hotel = "0";
			}
			if($sRenta_auto == "true"){
				$sRenta_auto = "1";
			}else{
				$sRenta_auto = "0";
				$sAgencia_auto = "0";
			}
			if ($_POST['CheckTAgencia'.$i] == 'true') {
				$sEmpresa_auto = $_POST['empresaAuto'.$i];
				
				$sCosto_dia = 0;
				$sTipo_divisa = 0;
				$sTipo_auto = $_POST['tipoAuto'.$i];
				$sDias_renta = 0;
				$sMontoA_pesos = 0;
				$sTotal_auto = 0;
				
				if($_POST['costoDia'.$i] != "NaN"){
					$sCosto_dia = $_POST['costoDia'.$i];
				}
				
				if($_POST['tipoDivisa'.$i] != ""){
					$sTipo_divisa = $_POST['tipoDivisa'.$i];
				}
				
				if($_POST['diasRenta'.$i] != ""){
					$sDias_renta = $_POST['diasRenta'.$i];
				}
				
				if($_POST['montoPesos'.$i] != ""){
					$sMontoA_pesos	= $_POST['montoPesos'.$i];
				}
				
				if($_POST['totalAuto'.$i] != ""){
					$sTotal_auto = $_POST['totalAuto'.$i];
				}
				
			}else{
				$sEmpresa_auto = "";
				$sCosto_dia = 0;
				$sTipo_divisa = 0;
				$sTipo_auto = 0;
				$sDias_renta = 0;
				$sMontoA_pesos	= 0;
				$sTotal_auto = 0;
			}
			
			$idItinerario = $CViaje->Add_Itinerario($sDestino, $sAgencia_hotel, $sAgencia_auto, $sFecha_regreso, $sFecha_salida, $sHora_regreso, $sHora_salida, $sRenta_hotel, $sKilometraje, $sOrigen, $sRegion, $sRenta_auto, $sMedio_transporte,$sTipo_transporte,$idSolViaje, $sCosto_dia, $sDias_renta, $sTipo_divisa, $sEmpresa_auto, $sTipo_auto, $sMontoA_pesos, $sTotal_auto, $sZona_geografica, $noDiasViaje);
			if ($idItinerario <= 0) {
				$CViaje->insertar("ROLL BACK");
				exit(header("Location: ./index.php?errsave"));
            }
			
			if ($_POST['CheckHAgencia'.$i] == 'true') {
				for ($j =1; $j <= $_POST['rowCount_hotel'.$i]; $j++){
					$sCiudad = $_POST['ciudad_'.$i.'_'.$j];
					$sNoches = $_POST['noches_'.$i.'_'.$j];
					$sHotel = $_POST['hotel_'.$i.'_'.$j];
					$sCostoNoche = $_POST['costoNoche_'.$i.'_'.$j];
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
					$stipoHotel = $_POST['tipoHotel_'.$i.'_'.$j];
					
					// Transformar la fechas a formato de MySQL
					$sLlegada = fecha_to_mysql($sLlegada);
					$sSalida = fecha_to_mysql($sSalida);
					
					$idHotel = $hoteles->agregarHotel($idItinerario, $sDivisa, $sComentario, $stipoHotel, $sCostoNoche, $sHotel, $sIva, $sLlegada, $sMontoH_pesos, $sCiudad, $sNoches, $sNo_reservacion, $sSalida, $sSubtotal, $sTotal, 1);
					
					
					if ($idHotel <= 0) {
						$CViaje->insertar("ROLL BACK");
						header("Location: ./index.php?errsave");
					}
				}
			}
			// Registra los conceptos
			$CViaje = new C_SV();			
			if (isset($_POST['Accion'])== 'true') {
				for ($j = 1; $j <= $_POST['rowCount_concepto'.$i]; $j++) {
					$MontoU = str_replace(',', '',$_POST["monto_u_".$i."_".$j]);
					$DivisaU = $_POST["divisa_u_".$i."_".$j];
					$MontoTotalU = str_replace(',', '',$_POST["montototalmo_u_".$i."_".$j]);
					$MontoTotalMxnU = str_replace(',', '',$_POST["montomxn_u_".$i."_".$j]);
					$DiasConceptoU = $_POST["no_dias_".$i."_".$j];
					$IDconcepto = $_POST["id_cpto_".$i."_".$j];
					$IDitinerario = $idItinerario;
					$idConcepto_detalle = $CViaje->add_conceptos_detalle($IDitinerario,$idTramite,$tramite->truncate($MontoU, 2),$DivisaU,$MontoTotalU,$MontoTotalMxnU,$DiasConceptoU,$IDconcepto);					
					$conceptoItinerarioId = $CViaje->add_concepto_itinerario($IDconcepto,$idConcepto_detalle,$IDitinerario);
					$conceptoItinerarioId2[]=$conceptoItinerarioId;
				}				
		
			}
		}
		
		if($_POST["totalExcepciones"] != ""){
			$exceptions = explode(",",$_POST["totalExcepciones"]);
			foreach($exceptions as $exc){
				$_POST["itinerarios_cbx".$exc];
				$mensaje = $_POST["e_row_mensaje".$exc];
				$diferencia = $_POST["e_row_diferencia".$exc];
				$concepto = $_POST["e_row_concepto".$exc];
				$conceptoItinerarioID = $conceptoItinerarioId2[$exc-1];
				Add_excepcion($mensaje, $diferencia, $idSolViaje, 0, 0, $concepto, $conceptoItinerarioID, 0);
			}
		}
		if($tramite->Get_dato('t_etapa_actual') == SOLICITUD_ETAPA_RECHAZADA || $tramite->Get_dato('t_etapa_actual') == SOLICITUD_ETAPA_RECHAZADA_POR_DIRECTOR || $tramite->Get_dato('t_autorizaciones_historial') != ""){
			$HObser = $_POST['historial_obser'];
		}else{
			$HObser = "";
		}
	
    if(!isset($_POST['guardarprevComp'])){
	    	//reestablecemos los valores de avion si es que la solicitud esta en etapa rechazada
	    	if(($tramite->Get_dato('t_etapa_actual') == SOLICITUD_ETAPA_RECHAZADA) || ($tramite->Get_dato('t_etapa_actual') == SOLICITUD_ETAPA_RECHAZADA_POR_DIRECTOR)){
	    		$query_Avion="UPDATE sv_itinerario SET
	    		svi_aerolinea = '".$svi_aerolinea."',
	    		svi_monto_vuelo = '".$svi_monto_vuelo."',
	    		svi_iva = '".$svi_iva."',
	    		svi_tua = '".$svi_tua."',
	    		svi_monto_vuelo_total = '".$svi_monto_vuelo_total."',
	    		svi_fecha_salida_avion = '".$fecha_salida."',
	    		svi_fecha_regreso_avion = '".$fecha_llegada."',
	    		svi_tipo_aerolinea = '".$svi_tipo_aerolinea."',
	    		svi_monto_vuelo_cotizacion = '".$svi_monto_vuelo_cotizacion."'
	    		WHERE svi_solicitud = '{$idSolViaje}'";
	    		$cnn->ejecutar($query_Avion);
	    	}
			
	    	if(isset($_POST['autorizar_cotizacion'])){
	    		$iniciador = $tramite->Get_dato("t_iniciador");
	    		$delegado = $tramite->Get_dato("t_delegado");
	    		//Datos usuario
	    		$ruta_autorizacion = new RutaAutorizacion();
				
				/**
				 * Validacion y guardado de excepcion de presupuesto
				 **/
				$presupuesto = new Presupuesto();
				$objetoPresupuesto = $presupuesto->validarPresupuesto($idTramite);
				$ruta_autorizacion->generaExcepcion($idTramite, $objetoPresupuesto);
						
	    		$ruta_autorizacion->generaRutaAutorizacionSolicitudViaje($idTramite,$iniciador,true);
				$excepciones = $ruta_autorizacion->get_Excepciones($idTramite);
				$ruta_autorizacion->agregaAutorizadoresExcedentes($idTramite, $excepciones);
				
	    		$aprobador=$ruta_autorizacion->getSiguienteAprobador($idTramite, $iniciador);
	    		
	    		// Actualizar el campo de observaciones de la solicitud de viaje
	    		if($sObservaciones != ""){
	    			$notificacion = new Notificacion();
	    			$observaciones = $notificacion->anotaObservacion($iduser, $HObser, $sObservaciones, FLUJO_SOLICITUD, "");
	    			$queryObserv = sprintf("UPDATE solicitud_viaje SET sv_observaciones = '%s' WHERE sv_tramite = '%s'", $observaciones, $idTramite);
	    			$cnn->ejecutar($queryObserv);
	    		}else{
	    			$queryObserv = sprintf("UPDATE solicitud_viaje SET sv_observaciones = '%s' WHERE sv_tramite = '%s'", $HObser, $idTramite);
	    			$cnn->ejecutar($queryObserv);
	    		}
	    		
	    		$duenoActual = new Usuario();
	    		$duenoActual->Load_Usuario_By_ID($iniciador);
	    		$usuarioNombre = $duenoActual->Get_dato('nombre');
	    		
	    		if($delegado != 0){
	    			$duenoActual->Load_Usuario_By_ID($delegado);
	    			$nombreDelegado = $duenoActual->Get_dato('nombre');
	    			$mensaje = sprintf("La Solicitud de Viaje <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> en nombre de: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $idTramite, $nombreDelegado, $usuarioNombre);
	    			$mensajeemail = sprintf("La Solicitud de Viaje <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> en nombre de: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $idTramite, $nombreDelegado, $usuarioNombre);
	    		}else{
	    			$mensaje = sprintf("La Solicitud de Viaje <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $idTramite, $usuarioNombre);
	    			$mensajeemail = sprintf("La Solicitud de Viaje <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $idTramite, $usuarioNombre);
	    		}
	    		
	    		$tramite->Modifica_Etapa($idTramite, SOLICITUD_ETAPA_EN_APROBACION, FLUJO_SOLICITUD, $aprobador, "");
	    		$tramite->EnviaNotificacion($idTramite, $mensaje, $iniciador, $aprobador, "1", ""); //"0" para no enviar email y "1" para enviarlo
				
	    		$autorizaCotizacionRechazada=true;
	    		
	    	}else if(isset($_POST['envia_a_director'])){
	    		$iniciador = $tramite->Get_dato("t_iniciador");
	    		$delegado = $tramite->Get_dato("t_delegado");
	    		
	    		// Actualizar el campoe de observaciones de la solicitud de viaje
	    		if($sObservaciones != ""){
	    			$notificacion = new Notificacion();
	    			$observaciones = $notificacion->anotaObservacion($delegado, $HObser, $sObservaciones, FLUJO_SOLICITUD, "");
	    			$queryObserv = sprintf("UPDATE solicitud_viaje SET sv_observaciones = '%s' WHERE sv_tramite = '%s'", $observaciones, $idTramite);
	    			$cnn->ejecutar($queryObserv);
	    		}else{
	    			$queryObserv = sprintf("UPDATE solicitud_viaje SET sv_observaciones = '%s' WHERE sv_tramite = '%s'", $HObser, $idTramite);
	    			$cnn->ejecutar($queryObserv);
	    		}
	    		
	    		$duenoActual = new Usuario();
				$duenoActual->Load_Usuario_By_ID($delegado);
				$nombreUsuario = $duenoActual->Get_dato('nombre');
				
				$mensaje = sprintf("La Solicitud de Viaje <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> en su nombre y requiere de su aprobaci&oacute;n.", $idTramite, $nombreUsuario);
				$remitente = $delegado;
				$destinatario = $iniciador;
				
				$tramite->Modifica_Etapa($idTramite, SOLICITUD_ETAPA_EN_APROBACION_POR_DIRECTOR, FLUJO_SOLICITUD, $iniciador, "");
				$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "1", ""); //"0" para no enviar email y "1" para enviarlo
				$tramite->actualizaDelegado($idTramite, $delegado);
	    		
	    	}else{
	    		$existeDelegado = $delegados->existenciaDelegado($iduser, $delegado);
	    		// Buscamos quien debe aprobar esta comprobacion
	    		//Pasamos el parametro de la zona geografica para la validacion de la ruta de autorizacion
				$ruta_autorizacion = new RutaAutorizacion();
	    		$es_agencia=$ruta_autorizacion->generaRutaAutorizacionSolicitudViaje($idTramite, $iduser,false);				
	    		error_log($es_agencia);
	    		$duenoActual = new Usuario();
	    		$duenoActual->Load_Usuario_By_ID($iduser);
	    		$nombreUsuario = $duenoActual->Get_dato('nombre');
	    		
	    		if($es_agencia == "Agencia"){
	    			// Se agrega la observacion tambien a la tabla de observaciones
	    			//error_log("observaciones......".$sObservaciones);
	    			$texto = $sObservaciones;
	    			$t_id = $idTramite;
	    			$u_id = $iduser;
	    			// Registra las observaciones
	    			if($texto != ""){
	    				if($tramite->Get_dato('t_etapa_actual') == SOLICITUD_ETAPA_RECHAZADA || $tramite->Get_dato('t_etapa_actual') == SOLICITUD_ETAPA_EN_APROBACION || $tramite->Get_dato('t_etapa_actual') == SOLICITUD_ETAPA_RECHAZADA_POR_DIRECTOR){	    					
	    					
	    					//Query para obtener observaciones
	    					$queryObs=sprintf("SELECT ob_id, ob_texto FROM observaciones  WHERE ob_tramite=%s ORDER BY ob_id DESC LIMIT 0,1",$t_id);
	    					$rstObs=$cnn->consultar($queryObs);
	    					$idObs=mysql_result($rstObs,0,"ob_id");
	    					$UObs=mysql_result($rstObs,0,"ob_texto");
	    					
	    					//Query para obtener las observaciones de solicitud_viaje
	    					$queryObserv = sprintf("UPDATE solicitud_viaje SET sv_observaciones = '%s' WHERE sv_tramite = '%s'", $HObser, $t_id);
	    					$cnn->ejecutar($queryObserv);
	    					
	    					if($tramite->Get_dato('t_etapa_actual') == SOLICITUD_ETAPA_RECHAZADA  || $tramite->Get_dato('t_etapa_actual') == SOLICITUD_ETAPA_RECHAZADA_POR_DIRECTOR){
	    						$CViaje->InsertObservaciones($texto, $t_id, $u_id);//que pasa si fue regresado a agencia, acaso el usuario no tiene algo que decirle?
	    					}else{//SOLICITUD_ETAPA_EN_APROBACION
	    						if($UObs != "" && $idObs != ""){
	    							if($UObs != $texto){
	    								//Actualizara las observaciones editadas
	    								$queryUObs=sprintf("UPDATE observaciones set ob_texto='%s' WHERE ob_id=%s",$texto,$idObs);
	    								$cnn->consultar($queryUObs);
	    							}
	    						}
	    					}    					    					
	    				}else{
	    					$query = sprintf("DELETE FROM observaciones WHERE ob_tramite = '%s'", $t_id);
	    					$cnn->consultar($query);
	    					$CViaje->InsertObservaciones($texto, $t_id, $u_id);
	    					
	    					// Inicializar campo sv_observaciones.
	    					$queryObserv = sprintf("UPDATE solicitud_viaje SET sv_observaciones = '' WHERE sv_tramite = '%s'", $idTramite);
	    					$cnn->ejecutar($queryObserv);
	    				}
	    			}else{
	    				if( $tramite->Get_dato('t_etapa_actual') == SOLICITUD_ETAPA_RECHAZADA  || $tramite->Get_dato('t_etapa_actual') == SOLICITUD_ETAPA_RECHAZADA_POR_DIRECTOR){
		    				$queryObserv = sprintf("UPDATE solicitud_viaje SET sv_observaciones = '%s' WHERE sv_tramite = '%s'", $HObser, $idTramite);
		    				$cnn->ejecutar($queryObserv);
	    				}else{
	    					$queryObserv = sprintf("UPDATE solicitud_viaje SET sv_observaciones = '' WHERE sv_tramite = '%s'", $idTramite);
	    					$cnn->ejecutar($queryObserv);
	    				}
	    			}
	    			
	    			$agencia=$ruta_autorizacion->getDueno($idTramite);
	    			$tramite->Modifica_Etapa($idTramite, SOLICITUD_ETAPA_AGENCIA, FLUJO_SOLICITUD, $agencia,"");
	    			if($sesionDelegado != 0){
	    				$duenoActual->Load_Usuario_By_ID($delegado);
	    				$nombreDelegado = $duenoActual->Get_dato('nombre');
	    				$mensaje = sprintf("La Solicitud de Viaje <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> en nombre de: <strong>%s</strong> y requiere de su cotizaci&oacute;n.", $idTramite, $nombreDelegado, $nombreUsuario);
	    				$mensaje_email = sprintf("La Solicitud de Viaje <strong>CREADA</strong> por: <strong>%s</strong> requiere de su cotizaci&oacute;n.", $nombreUsuario);
	    			}else{
	    				$mensaje = sprintf("La Solicitud de Viaje <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> y requiere de su cotizaci&oacute;n.", $idTramite, $nombreUsuario);
	    				$mensaje_email = sprintf("La Solicitud de Viaje <strong>CREADA</strong> por: <strong>%s</strong> requiere de su cotizaci&oacute;n.", $nombreUsuario);
	    			}
	    			
	    			$remitente = $iduser;
	    			$destinatario = $agencia;
	    			$agrup_usu = new AgrupacionUsuarios();
	    			$rst=$agrup_usu->Load_Homologacion_Usuarios($destinatario);
	    			$aux = array();
	    			while($arre=mysql_fetch_assoc($rst)){
	    				array_push($aux,$arre);
	    			}
					
	    			foreach($aux as $datosAux){
	    				//se enviara el mensaje a los distintos destinatarios (grupo)
	    				$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente,$datosAux["hd_u_id"], "0", $mensaje_email);
	    			}
	    			if(!$existeDelegado){
	    				//Si el previo lo genero un delegado, pero la solicitud, la envio el Director, borraremos el id del delegado que realizo el previo.
	    				$tramite->actualizaDelegado($idTramite, 0);
	    			}else{
	    				//Si el previo lo genero un delegado, pero la solicitud, la envio el Director, y en un rechazo la envio el delegado, se guardará nuevamente el id del delegado.
	    				$tramite->actualizaDelegado($idTramite, $delegado);
	    			}
	    		}else{
	    			if($existeDelegado){
	    				//Se agrega la observacion también a la tabla de observaciones
	    				$duenoActual = new Usuario();
	    				$duenoActual->Load_Usuario_By_ID($delegado);
	    				$nombreUsuario = $duenoActual->Get_dato('nombre');
	    				 
	    				$iniciador = $tramite->Get_dato("t_iniciador");
	    			
	    				if($sObservaciones != ""){
	    					$notificacion = new Notificacion();
	    					$observaciones = $notificacion->anotaObservacion($delegado, $HObser, $sObservaciones, FLUJO_SOLICITUD, "");
	    					$queryObserv = sprintf("UPDATE solicitud_viaje SET sv_observaciones = '%s' WHERE sv_tramite = '%s'", $observaciones, $idTramite);
	    					$cnn->ejecutar($queryObserv);
	    				}else{
			    			$queryObserv = sprintf("UPDATE solicitud_viaje SET sv_observaciones = '%s' WHERE sv_tramite = '%s'", $HObser, $idTramite);
			    			$cnn->ejecutar($queryObserv);
			    		}
	    				 
	    				$tramite->Modifica_Etapa($idTramite, SOLICITUD_ETAPA_EN_APROBACION_POR_DIRECTOR, FLUJO_SOLICITUD, $iduser,"");
	    				$mensaje = sprintf("La Solicitud de Viaje <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> en su nombre y requiere de su aprobaci&oacute;n.", $idTramite, $nombreUsuario);
	    				 
	    				$remitente = $delegado;
	    				$destinatario = $iduser;
	    				$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "1", ""); //"0" para no enviar email y "1" para enviarlo
	    			}else{
						/**
						 * Validacion y guardado de excepcion de presupuesto
						 **/
						$presupuesto = new Presupuesto();
						$objetoPresupuesto = $presupuesto->validarPresupuesto($idTramite);
						$ruta_autorizacion->generaExcepcion($idTramite, $objetoPresupuesto);
	    				//Se agrega la observacion tambien a la tabla de observaciones
		    			if(!$existeDelegado){
		    				//Si el previo lo genero un delegado, pero la solicitud, la envio el Director, borraremos el id del delegado que realizo el previo.
		    				$tramite->actualizaDelegado($idTramite, 0);
		    			}else{
		    				//Si el previo lo genero un delegado, pero la solicitud, la envio el Director, y en un rechazo la envio el delegado, se guardará nuevamente el id del delegado.
		    				$tramite->actualizaDelegado($idTramite, $delegado);
		    			}
	    				$iniciador = $tramite->Get_dato("t_iniciador");
	    				 
	    				if($sObservaciones != ""){
	    					$notificacion = new Notificacion();
	    					$observaciones = $notificacion->anotaObservacion($iniciador, $HObser, $sObservaciones, FLUJO_SOLICITUD, "");
	    					$queryObserv = sprintf("UPDATE solicitud_viaje SET sv_observaciones = '%s' WHERE sv_tramite = '%s'", $observaciones, $idTramite);
	    					$cnn->ejecutar($queryObserv);
	    				}else{
			    			$queryObserv = sprintf("UPDATE solicitud_viaje SET sv_observaciones = '%s' WHERE sv_tramite = '%s'", $HObser, $idTramite);
			    			$cnn->ejecutar($queryObserv);
			    		}
	    				
						$excepciones = $ruta_autorizacion->get_Excepciones($idTramite);
						$ruta_autorizacion->agregaAutorizadoresExcedentes($idTramite, $excepciones);
	    				$aprobador=$ruta_autorizacion->getSiguienteAprobador($idTramite,$iduser);
	    			
	    				$tramite->Modifica_Etapa($idTramite, SOLICITUD_ETAPA_EN_APROBACION, FLUJO_SOLICITUD, $aprobador,"");
	    				if($sesionDelegado != 0){
	    					$duenoActual->Load_Usuario_By_ID($delegado);
	    					$nombreDelegado = $duenoActual->Get_dato('nombre');
	    					$mensaje = sprintf("La Solicitud de Viaje <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> en nombre de: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $idTramite, $nombreDelegado, $nombreUsuario);
	    					$mensaje_email = sprintf("La Solicitud de Viaje <strong>CREADA</strong> por: <strong>%s</strong> requiere de su autorizaci&oacute;n.", $nombreUsuario);
	    				}else{
	    					$mensaje = sprintf("La Solicitud de Viaje <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $idTramite, $nombreUsuario);
	    					$mensaje_email = sprintf("La Solicitud de Viaje <strong>CREADA</strong> por: <strong>%s</strong> requiere de su autorizaci&oacute;n.", $nombreUsuario);
	    				}
	    			
	    				$remitente = $iduser;
	    				$destinatario = $aprobador;
	    				$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "1", $mensaje_email); //"0" para no enviar email y "1" para enviarlo
	    			}
	    		}
	    	}
		}else{
			$ruta_autorizacion = new RutaAutorizacion();
			$es_agencia = $ruta_autorizacion->generaRutaAutorizacionSolicitudViaje($idTramite, $iduser,false);
			
			if($es_agencia == "Agencia"){
				// Se agrega la observacion tambien a la tabla de observaciones
				$texto = $sObservaciones;
				$t_id = $idTramite;
				$u_id = $iduser;
				// Registra las observaciones
				if($texto != ""){
					if($tramite->Get_dato('t_etapa_actual') == SOLICITUD_ETAPA_RECHAZADA){
						$queryObs=sprintf("SELECT ob_id, ob_texto FROM observaciones  WHERE ob_tramite=%s ORDER BY ob_id DESC LIMIT 0,1",$t_id);
						$rstObs=$cnn->consultar($queryObs);
						$idObs=mysql_result($rstObs,0,"ob_id");
						$UObs=mysql_result($rstObs,0,"ob_texto");
						if($UObs != $texto){
							//Actualizara las observaciones editadas
							$queryUObs=sprintf("UPDATE observaciones set ob_texto='%s' WHERE ob_id=%s",$texto,$idObs);
							$cnn->consultar($queryUObs);
						}
					}else{
						$query_elimina_obsv="DELETE FROM observaciones WHERE ob_tramite=".$t_id."";						
						$cnn->ejecutar($query_elimina_obsv);
						$CViaje->InsertObservaciones($texto, $t_id, $u_id);
					}
				}
			}else{
				//Se agrega la observacion tambien a la tabla de observaciones			
				if($sObservaciones != ""){
					$queryObserv = sprintf("UPDATE solicitud_viaje SET sv_observaciones = '%s' WHERE sv_tramite = '%s'", $sObservaciones, $idTramite);
					$cnn->ejecutar($queryObserv);
				}
			}
			
			$guardadoPrevio=true;
		}
		
        // Termina transacción
        $tramite->insertar("COMMIT");
		// Manda a pagina de confirmacion
		if($guardadoPrevio == true){
			header("Location: ./index.php?oksaveP");
		}else{
			if($autorizaCotizacionRechazada == true){
				header("Location: ./index.php?oksaveE");
			}else{
				header("Location: ./index.php?oksave");
			}
		}
       
    }else{
        // Manda pagina de error 
        header("Location: ./index.php?errsave");
    } 
}

// Esta seccion muestra la forma de alta de nueva solicitud
if (isset($_GET['new'])) {

    $UsuOb = new Usuario();
    $tramite = new Tramite();
    $UsuOb->Load_Usuario($_SESSION['empleado']);
    $idcentrocosto = $UsuOb->Get_dato('idcentrocosto');

    // Obtiene el usuario actual de la sesion
    $idusuario_actual = $_SESSION["idusuario"];
    $perfil_usuario_actual = $_SESSION["perfil"];
    
    // Cargar la etapa del tramite en edición
    $etapaEdicion = 0;
    if(isset($_GET['id'])){
    	$tramite->Load_Tramite($_GET['id']);
    	$etapaEdicion = $tramite->Get_dato('t_etapa_actual');    	
    }    

    //Obtiene el valor del parámetro para el límite de anticipos abiertos por persona
    $_parametros = new Parametro();
    $_parametros->Load(5); //busca parametro de limite de dias de anticipación para hacer solicitud de viaje Nacional
    $RP_LIM_ANTICIPOSABIERTOS = $_parametros->Get_dato("pr_cantidad");

    // Checa si el usuario tiene comprobaciones pendientes
    $sql = "SELECT * FROM solicitud_viaje s inner join tramites on sv_tramite = t_id where t_comprobado = 0 and t_iniciador =" . $idusuario_actual;
    $rst = mysql_query($sql);
    $no_solicitudes_pendientes_de_aprobar = mysql_num_rows($rst);
    ?> 
    <?PHP 
    if ($no_solicitudes_pendientes_de_aprobar > $RP_LIM_ANTICIPOSABIERTOS && !isset($_GET['id'])) {// Checa si el usuario tiene comprobaciones pendientes //26082011 EL requerimiento para BMW es de 8 documentos
        ?>

        <center><h3>Hay solicitudes pendientes de comprobar, no se pueden crear solicitudes nuevas.</h3></center> 

    <?PHP } else { ?>
		
		<meta http-equiv="Pragma" content="no-cache">
					
        <script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
        <script language="JavaScript" src="../../lib/js/jquery/jquery.date_input.js" type="text/javascript"></script>        
        <script language="JavaScript" src="../../lib/js/jquery/jquery.bgiframe.js" type="text/javascript"></script>
		<script language="JavaScript" src="../../lib/js/dom-drag.js" type="text/javascript"></script>
        <script language="JavaScript" src="../../lib/js/jquery/jquery.blockUI.js" type="text/javascript"></script>
        <script language="JavaScript" src="../../lib/js/formatNumber.js" type="text/javascript"></script>
        <script language="JavaScript" src="../../lib/js/withoutReloading.js" type="text/javascript"></script>
        <script language="JavaScript" src="js/solicitud_viaje.js" type="text/javascript"></script>
        <script language="JavaScript" src="js/functionsCalcularConceptos.js" type="text/javascript"></script>
        <script language="JavaScript" src="../comprobaciones/js/backspaceGeneral.js" type="text/javascript"></script>
        <script language="JavaScript" type ="text/javascript" src="../../lib/js/jquery.datepick.js"></script>
		<script language="JavaScript" src="../../lib/js/jqueryui/jquery-ui.min.js"></script>
		<script language="JavaScript" src="../../lib/js/jquery/jquery.autocomplete.js" type="text/javascript"></script>
        <link rel="stylesheet" href="../../css/jquery.datepick.css" type="text/css" media="screen" />
        <script language="JavaScript" src="js/validarPoliticas.js" type="text/javascript"></script>

        <link rel="stylesheet" type="text/css" href="../../css/jquery.autocomplete.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/date_input.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/table_style.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/style_Table_Edit.css"/>
        <style type="text/css">
            .style1 {color: #FF0000}
            .hideable { position: relative; visibility: hidden; }			
        </style>
		<!------------------------->
        <!-- NUEVA CONFIGURACION -->
        <!------------------------->		
		<link rel="stylesheet" href="../../lib/js/jquery-ui-1.10.4/development-bundle/themes/base/jquery.ui.all.css">
		<script src="../../lib/js/jquery-ui-1.10.4/js/jquery-1.10.2.js"></script>
		<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.core.js"></script>
		<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.widget.js"></script>
		<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.tabs.js"></script>
		<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.position.js"></script>
		<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.menu.js"></script>
		<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.autocomplete.js"></script>


		
        <script language="JavaScript" src="js/generales/configuracion.js" type="text/javascript"></script>
        <script language="JavaScript" src="js/generales/cargarDatos.js" type="text/javascript"></script>
        <script language="JavaScript" src="js/generales/js_solicitud_viaje_new.js" type="text/javascript"></script>
        <!------------------------->
        <!-- NUEVA CONFIGURACION -->
        <!------------------------->        
		<script language="JavaScript" type="text/javascript">
		// Variables
		var doc;
		var j=1;
		var p=1;
        var bandera_auto=0;
        var bandera_hotel=0;
		var estatus_en_edicion_de_itinerario = false;
		var estatus_tipo_de_viaje = false;
		var var_combo_itinerarios = "";
		var bandera_edicion = false;
		var FechaSalida = "";
		var visita=0;
		var editaDia=0;
		var banderaCancelaHotel=0;
		var formato=0;
		var regionNacional = 1;
		var idEmpleadoSession = "idEmpleado="+<?PHP echo $iduser = (isset($_SESSION["iddelegado"])) ? verificaSesion($_SESSION["idusuario"], $_SESSION["iddelegado"]) : $_SESSION["idusuario"]; ?>;
		var sol_etapa_rechazada = <?php echo SOLICITUD_ETAPA_RECHAZADA; ?>;
		var tasaDivisa = "<?PHP
			$query = sprintf('SELECT DIV_ID,DIV_TASA FROM divisa');
			$var = mysql_query($query);
			$aux="";
			while ($arr = mysql_fetch_assoc($var)){
				$aux.=$arr['DIV_ID'].":".$arr['DIV_TASA'].":";
			}
			echo $aux;?>";		
		//cuando el documento este listo manda llamar la funcion de inicializacion
		doc = $(document);
		doc.ready(inicializarEventos);
        </script>
		<script type="text/javascript">
		var j = jQuery.noConflict();
		j(document).ready(function() {
			j( "#tabs" ).tabs({
			  active: 0
			});
			j("#tabs2").click(function(){
				var val1 = j("#motive").val();
				var val2 = j("#select_tipo_viaje_pasaje").val();
				var val1len = j("#motive").val().length;
				if(val1len >= 5){
					if(val1 != "" && val2 != "-1"){
						j('#motive').removeClass('red');
						j('#select_tipo_viaje_pasaje').removeClass('red');
					}else{
						j( "#tabs" ).tabs({ active: 0 });
						alert("Es necesario llenar los campos indicados de Datos Generales del Viaje");
						j('#motive').addClass('red');
						j('#select_tipo_viaje_pasaje').addClass('red');						
						return false;
					}
				}else{
					j( "#tabs" ).tabs({ active: 0 });
					alert("El motivo debe tener almenos 5 caracteres");					
					return false;
				}
			});
			j("#tabs3").click(function(){
				var val1 = j("#motive").val();
				var val2 = j("#select_tipo_viaje_pasaje").val();
				var rowCount = j('#solicitud_table tr').length;
				if(val1 != "" && val2 != "-1"){
					if(val2 != "3"){
						if(rowCount > 1){
							j('#motive').removeClass('red');
							j('#select_tipo_viaje_pasaje').removeClass('red');
						}else{
							j( "#tabs" ).tabs({ active: 1 });
							alert("No existen registros de Itinerario en Datos de Itinerario");
							return false;
						}
					}else{
						if(rowCount > 2){
							j('#motive').removeClass('red');
							j('#select_tipo_viaje_pasaje').removeClass('red');
						}else{
							alert("Deben de existir almenos 2 registros en Datos de Itinerario");
							return false;
						}
					}
				}else{
					j( "#tabs" ).tabs({ active: 0 });
					alert("Es necesario llenar los campos indicados de Datos Generales del Viaje");
					j('#motive').addClass('red');
					j('#select_tipo_viaje_pasaje').addClass('red');
					return false;
				}
			});
			j("#select_tipo_viaje").change(function(){
				var reg = j("#select_tipo_viaje").val();
				j("#ciudad").val('');
				j("#hotelDiv").html('');
				j("#hotel").hide();
				if(reg == 1){
					j(function() {
						j( "#ciudad" ).autocomplete({
							source: "services/carga_hoteles.php",
							 select: function( event, ui ) {
								hoteles(ui.item.label);
							}
						});
					});
					function hoteles(label){
					datos="label="+label;
						var jqxhr = j.getJSON( "services/carga_hoteles.php",datos, function(data) {
						console.log( "success" );
						})
						.done(function() {
						console.log( "second success" );
						})
						.fail(function() {
						console.log( "error" );
						})
						.always(function(data) {
						var option = "";
						option += '<select name="hotel1" type="text" id="hotel1">';
							option+='<option precio="0" value="0" selected=selected>Selecciona</option>';
							for(prop in data){
								var nombreHotel = data[prop].nombre;
								var costoHotel = data[prop].costo;
								option+='<option precio="'+costoHotel+'" value="'+nombreHotel+","+costoHotel+'">'+nombreHotel+",  "+costoHotel+'</option>';
							}
						option += '</select>';
						j("#hotelDiv").html(option);
						});
						jqxhr.complete(function() {
						});
					}
					j( "body" ).on( "change", "#hotel1", function() {
						var result = j("#hotel1").val().split(',');
						price = result[1];
						ht = result[0];
						j("#costoNoche").val(price);
						j("#hotel").val(ht);
					});
				}else{
					j("#hotel").show();
						if ($("#ciudad").hasClass("ui-autocomplete-input")) {
							j("#ciudad").autocomplete("destroy");
						}
				}
			});			
		});
		</script>
		<div id="Layer1">
			<form action="solicitud_viaje_new.php?save" method="post" name="detallesItinerarios" id="detallesItinerarios">
				<div id="tabs">
					<ul>
						<li id="tabs1"><a href="#tabs-1"><h3>1.- Datos Generales del Viaje</h3></a></li>
						<li id="tabs2"><a href="#tabs-2"><h3>2.- Datos de Itinerario</h3></a></li>
						<li id="tabs3"><a href="#tabs-3"><h3>3.- Información General</h3></a></li>
					</ul>
				
				<div id="tabs-1">					
				<table width="785" border="0" align="center" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left;">
					<tr>
						<td colspan="6">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="6"><center><h3>Datos Generales del Viaje</h3></center></td>
					</tr>
					<tr><td colspan="6">&nbsp;</td></tr>
					<tr style="text-align:center;">
						<td>&nbsp;</td>
						<td  width="32%">
							<div align="right">
								Motivo del viaje<span class="style1">*</span>: 
							</div>
						</td>
						<td colspan="3" width="65%">
							<div align="left">
								<input name="motive" type="text" id="motive" value="" size="50" maxlength="100"/>
								<input type="hidden" name="anticipos" type="text" id="anticipos" value="" size="50" maxlength="100"/>
							</div>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr style="text-align:center;">
						<td>&nbsp;</td>
						<td><div align="right">Tipo de viaje <span class="style1">*</span>:</div></td>
						<td>
							<div  align="left">
								<select name="select_tipo_viaje_pasaje" id="select_tipo_viaje_pasaje" onchange="verificar_tipo_boleto(this.value);">
								<option value="-1">Seleccione...</option>         
								<option value="1">Sencillo</option>
								<option value="2">Redondo</option>
								<option value="3">Multidestinos</option>
								</select>								
							</div>
						</td>
						<td colspan="3">&nbsp;</td>
					</tr>					
                        <tr>
							<td colspan="2"><div align="right">Centro de costos al que se cargar&aacute;: <span class="style1">*</span>:</div></td>                           
                            <td colspan="2">
                                <div align="left">
                                    <select name='cat_cecos_cargado' id="cat_cecos_cargado" onChange="/*habilitar()*/">
                                        <?PHP
                                        $query = sprintf("SELECT c.cc_id, c.cc_centrocostos, c.cc_nombre from cat_cecos c INNER JOIN empleado e ON c.cc_id = e.idcentrocosto WHERE c.cc_estatus = 1 AND e.idempleado = " . $_SESSION["idusuario"] . " order by c.cc_centrocostos");
                                        $var = mysql_query($query);
                                        while ($arr = mysql_fetch_assoc($var)) {
											echo sprintf("<option value='%s'>%s - %s</option>", $arr['cc_id'], $arr['cc_centrocostos'], $arr['cc_nombre']);
                                        }
                                        ?>                    
                                    </select></div> 
                                  
                            </td>

                        </tr>
					<tr><td colspan="6"><div>&nbsp;</div></td></tr>		
				</table>			
				</div>
				<div id="tabs-2">						
					<table width="785" border="0" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">  
						<tr>
							<td>&nbsp;</td>
							<td colspan="4">&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td colspan="4"><center><h3>Datos de Itinerario</h3></center></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td colspan="2"><h3><center>Salida</center></h3></td>
							<td colspan="2"><div id="regreso_titulo" style="display:block; visibility:hidden"><h3><center>Regreso</center></h3></div></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td nowrap="nowrap">
								<div id="salida_viaje_dato" name="salida_viaje_dato">
									Fecha<span class="style1">*</span>:<br />
									<input name="fecha" id="fecha" value="<?PHP echo date('d/m/Y'); ?>" size="12" readonly="readonly"/>
								</div>
							</td>
							<td>
								<div align="left">
									Hora<span class="style1">*</span>: <br />
									<select name="select_hora_salida" id="select_hora_salida" onchange="">
										<?PHP 
										$tipoViaje = array("Cualquier horario", "Mañana", "Tarde", "Noche", "Madrugada");
										echo "<option id=1 value=" . $tipoViaje[1] . ">" . $tipoViaje[1] . " [6:00-11:59]</option>";
										echo "<option id=2 value=" . $tipoViaje[2] . ">" . $tipoViaje[2] . " [12:00-17:59]</option>";
										echo "<option id=3 value=" . $tipoViaje[3] . ">" . $tipoViaje[3] . " [18:00-11:59]</option>";
										echo "<option id=4 value=" . $tipoViaje[4] . ">" . $tipoViaje[4] . " [00:00-5:59]</option>";
										?>
									</select>
								</div>
							</td>
							<td nowrap="nowrap">
								<div id="llegada_viaje_dato" name ="llegada_viaje_dato" style="display:block; visibility:hidden;">
									Fecha<span class="style1">*</span>:<br />
									<input name="fechaLlegada" id="fechaLlegada" value="<?PHP echo date('d/m/Y'); ?>" size="12" readonly="readonly" onchange="/*diasDeViaje(this.value,'llegada')*/" />
								</div>
							</td>
							<td>
								<div id="llegada_viaje_dato2" name ="llegada_viaje_dato2" style="display:block; visibility:hidden" align="left">
									Hora<span class="style1">*</span>: <br />
									<select name="select_hora_llegada" id="select_hora_llegada" onchange="">
										<?PHP 
										$tipoViaje = array("Cualquier horario", "Mañana", "Tarde", "Noche", "Madrugada");
										echo "<option id=1 value=" . $tipoViaje[1] . ">" . $tipoViaje[1] . " [6:00-11:59]</option>";
										echo "<option id=2 value=" . $tipoViaje[2] . ">" . $tipoViaje[2] . " [12:00-17:59]</option>";
										echo "<option id=3 value=" . $tipoViaje[3] . ">" . $tipoViaje[3] . " [18:00-11:59]</option>";
										echo "<option id=4 value=" . $tipoViaje[4] . ">" . $tipoViaje[4] . " [00:00-5:59]</option>";
										?>
									</select>
								</div>
							</td>
							<td>
								<div align="left" style="display:none;">
									<input type="hidden" name="fechaActual" id="fechaActual" value="<?PHP echo date('d/m/Y'); ?>" size="15" readonly="readonly"  />
								</div>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td colspan="4">&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr align="center">
							<td >&nbsp;</td>
							<td>
								<div align="right">
									Zona geogr&aacute;fica <span class="style1">*</span>: <br />
								</div>
							</td>
							<td>
								<div align="left">
									<select name="select_tipo_viaje" id="select_tipo_viaje" onchange="verificar_region(this.value); DiasAnterioresViaje(this.value); "><!-- DiasAnterioresViaje($('#fecha').val()); -->
										<option value="-1">Seleccione...</option>  
										<option value="1">Nacional</option>
										<option value="2">Continental</option>
										<option value="3">Intercontinental</option>
									</select>
								</div>
							</td>
							<td colspan="2">
								<div id="region_solicitud_viaje" name="region_solicitud_viaje" align="left" style="display:none;">
									Regi&oacute;n<span class="style1">*</span>: <!--<br />->
								<!--</div>
								<div align="left">-->
									<select name="select_region_viaje" id="select_region_viaje" onchange="" style="display:none;">
									</select>
								</div>
							</td>
							<td >&nbsp;</td>
						</tr>
						<tr>
							<td >&nbsp;</td>
							<td >
								<div align="right">Origen<span class="style1">*</span>: <br />
								</div>
							</td>
							<td colspan="1">
								<div align="left">
									<input name="origen" type="text" id="origen" value="" size="20" />
								</div>
							</td>
							<td colspan="2">&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td >&nbsp;</td>
							<td ><div align="right">Destino<span class="style1">*</span>: <br />
								</div>
							</td>
							<td colspan="1">
								<div align="left">
									<input name="destino" type="text" id="destino" value="" size="20" onblur='' onchange=""/>
								</div>
							</td>
							<td colspan="2">&nbsp;</td>
							<td >&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td colspan="4"><center><strong>Medio de transporte</strong></center></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td colspan="2">
								<div align="right">Medio de transporte<span class="style1">*</span>: <br />
								</div>
							</td>
							<td colspan="2">
								<div align="left">
									<select name="select_medio_transporte" id="select_medio_transporte" onchange="activaCb();">
									<?PHP 
										$transp = array("Seleccione", "Terrestre", "A&eacute;reo");
										for ($i = 0; $i < count($transp); $i++) {
											echo "<option id=" . $i . " value=" . $transp[$i] . ">" . $transp[$i] . "</option>";
										}
										?>
									</select>
								</div>
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td></td>   
							<td  colspan="2">
								<div id="tipo_transporte_label" name="tipo_transporte_label" align="right" style="display:none;">
									Tipo de transporte<span class="style1">*</span>: <br/>
								</div>
							</td>
							<td colspan="2">
								<div align="left">                                    
									<select name="select_tipo_transporte" id="select_tipo_transporte" onchange="cargar_tipo_transporte(this.value);" style="display:none;">
										<option value="-1">Seleccione...</option>
										<option value="1">Particular</option>
										<option value="2">Flotilla/Asignado</option>
										<option value="3">Autobús</option>
									</select>
								</div>
							</td>
							<td></td>
						</tr>
						<tr>
							<td></td>   
							<td  colspan="2">
								<div id="kilometraje_label" name="kilometraje_label" align="right" style="display:none;">
									Kilometraje<span class="style1">*</span>: <br />
								</div>
							</td>
							<td colspan="2">
								<div id="kilometraje_data" name="kilometraje_data" align="left" style="display:none;"><input name="kilometraje" type="text" id="kilometraje" value="" size="10" onkeypress="return validaNum (event)" maxlength="9" /><a href="https://maps.google.com/" target="_blank">&nbsp;Buscar Kilometraje</a></div>
							</td>
							<td></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td colspan="4">&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td colspan="2"><h3><center>Hospedaje</center></h3></td>
							<td colspan="2"><h3><center>Renta de auto</center></h3></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<div id="" name="" align="right">
									Hospedaje<span class="style1">*</span>: <br />
								</div>
							</td>
							<td>
								<div align="left">
									Sí<input type="radio"  value="1" name="hospedaje" id="hospedaje" onclick="radioH(value)"/>  
									No<input type="radio" value="0" name="hospedaje" id="hospedaje" onclick='mensaje_confirmacion_hotel1(); /*radioH(value)*/' />
								</div>
							</td>
							<td>
								<div id="" name="" align="right">
									Renta de auto<span class="style1">*</span>: <br />
								</div>
							</td>
							<td>
								<div align="left">
									Sí<input type="radio"  value="1" name="auto" id="auto" onclick="radioA(value)"/>  
									No<input type="radio" value="0" name="auto" id="auto" onclick='mensaje_confirmacion_auto1(); if($("#empresaAuto").val() == ""){radioA(value);}' />
								</div>
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td></td>
							<td>
								<div id="enviar_agencia_hotel" name="enviar_agencia_hotel" align="right" style="display:none" >
									Enviar a </br>Agencia de Viajes<span class="style1">*</span>: <br />
								</div>
							</td>
							<td>
								<div id="enviar_agencia_hotel_input" name="enviar_agencia_hotel_input" align="left" style="display:none" >
									Sí<input type="radio"  value="1" name="enviar_hosp_agencia" id="enviar_hosp_agencia" onclick="mensaje_confirmacion_hotel2();" />  
									No<input type="radio" value="0" name="enviar_hosp_agencia" id="enviar_hosp_agencia" onclick='verificar_itinerario2(); if($("#etapaID").val()==<?php echo SOLICITUD_ETAPA_RECHAZADA;?>){mensaje_confirmacion_rechazadoHotel();}else{ flotanteActive(1); $("#divbotonHotel").css("display", "block");}calculaTotalHospedaje();'/>
								</div>
							</td>
							<td>
								<div id="enviar_agencia_auto" name="enviar_agencia_auto" align="right" style="display:none" >
									Enviar a </br>Agencia de Viajes<span class="style1">*</span>: <br />
								</div>
							</td>
							<td>
								<div id="enviar_agencia_auto_input" name="enviar_agencia_auto_input" align="left" style="display:none" >
									Sí<input type="radio"  value="1" name="enviar_auto_agencia" id="enviar_auto_agencia" onclick='mensaje_confirmacion_auto2();' />  
									No<input type="radio" value="0" name="enviar_auto_agencia" id="enviar_auto_agencia" onclick='verificar_itinerario(); if($("#etapaID").val()==<?php echo SOLICITUD_ETAPA_RECHAZADA;?>){mensaje_confirmacion_rechazadoAuto();}else{flotanteActive2(1);$("#divbotonAuto").css("display", "block");}'/>
								</div>
							</td>
							<td></td>
						</tr>
						<!-- lo nuevo-->
						<tr>
							<td>&nbsp;</td>
							<td colspan="4" >&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td align="right">	
								<div id="divbotonHotel" name="divbotonHotel" align="right" style="display:none" >
								<input type="button"  value="Editar hospedaje" name="botonHotel" id="botonHotel" onclick="verificar_itinerario2(); flotanteActive(1);calculaTotalHospedaje();" />  
								</div>
							</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td align="left">	
								<div id="divbotonAuto" name="divbotonAuto" align="left" style="display:none" >
								<input type="button"  value="Editar Renta de auto" name="botonAuto" id="botonAuto" onclick="verificar_itinerario(); flotanteActive2(1);" />  
								</div>
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td colspan="4">&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td colspan="4">
								<div align="center">
									<input name="registrar_comp" type="button" id="registrar_comp" value="     Registrar Itinerario"  onclick="agregarItinerario();" style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>
								</div>
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td colspan="4">&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
				<br />
				
				<tr>
				<td colspan="8" style="text-align:center">
				<div id="solicitud_div" ></div>
                </td>
               </tr> 
			   </table>
				</div>
				<div id="tabs-3">                
                    <table  width="785" border="0" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">
					    

						<tr><td>&nbsp;</td></tr>
                        <tr>
                            <td colspan="8"><h3 align="center">Informaci&oacute;n General </h3></td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>

                        <tr>
                            <td>&nbsp;</td>
                            <td colspan="6">
                                <?PHP 
                                if ($perfil_usuario_actual == '3') {
                                    //echo("Es externo");
                                }
                                if ($perfil_usuario_actual != 3) {
                                    //echo("No Es externo");
                                    ?>
                                    <div align="left">
                                        <INPUT type="checkbox"  name="Accion" id="Accion" onClick='verificar_conceptos2(); /*desplegar(Sconcepto),desplegar(lConceptos)*/'>Requerimientos de anticipo</INPUT> 
                                    </div>
                                <?PHP } ?>
                            </td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr>
                            <td colspan="8">&nbsp;  	  </td>
                        </tr>
                        <tr id="Sconcepto" name="Sconcepto" style="display:none" >
                            <td colspan="2">&nbsp;</td>
                            <td width="11%" >
                                <div align="right">
                                    Concepto: <br/>
                                </div>
                            </td>
                            <td colspan="2">
                                <div align="left">
									<div id="div_conceptos">
										<select name="select_concepto" id="select_concepto" >  
										</select>
									</div>
								 </div>
                            </td>
                            <td colspan="2">
                                <input name="addConcepto" type="button" id="addConcepto" value="    Agregar a mi lista"  onclick="construyePartidaConcepto2();" disabled="disabled" style="background:url(../../images/add.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>
                            </td>

                        </tr>   
                        <tr id="lConceptos" name="lConceptos" style="display:none">
							<td colspan="8">
                                <center>
                                    <div align="center" style="width:100%">
                                        <br/>
                                        <h3>Lista de mis Conceptos por los que solicito anticipos</h3>
                                        <table id="conceptos_table" class="tablesorter" cellspacing="1">
                                            <thead> 
                                                <tr> 
                                                    <th width="2%">No.</th>                         
                                                    <th width="10%">Itinerario</th>
                                                    <th width="15%">Concepto</th>
                                                    <th width="10%">Monto</th>
                                                    <th width="10%">Divisa</th>
                                                    <th width="10%">D&iacute;as</th>
                                                    <th width="10%">Monto total moneda original</th>
                                                    <th width="10%">Monto en MXN</th>
                                                    <th width="10%">Eliminar</th>
                                                </tr>						
                                            </thead>
                                            <tbody>
                                                <!-- cuerpo tabla-->
                                            </tbody>						
                                        </table>					
                                        <div align="right">
											<br/>
											<strong>Anticipo Solicitado<!--<label id="TDias">0</label>d&iacute;a(s):--></strong>&nbsp;&nbsp;
											<input name="anticipoC" type="text" id="anticipoC" disabled="disabled" value="0.00" size="12" maxlength="12" onkeypress="return validaNum (event);confirmaRegreso('anticipoC');" onkeydown="confirmaRegreso('anticipoC');"  readonly="readonly" style="text-align:right"/>
											<input type="hidden" id="totalanticipo" name="totalanticipo" value="0.00" readonly="readonly"/>
											<div id="Moneda2" style="display:inline">MXN</div>
											<br/>
											<br/>
											<!--<strong>Anticipo solicitado<span class="style1">*</span>:</strong>$--><input name="anticipo" type="hidden" id="anticipo" value="0.00" size="12" maxlength="12" onkeypress="return validaNum (event)" onkeyup="comparaMontos()" onblur='this.value=number_format(this.value,2,".",",");' readonly="readonly"/>
										</div>
										<div id="capaWarning" align="left"></div>
									</div>
                                </center>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="8" style="display:none">
								<!-- Inicio Tabla de PArtidas -->
								<table id="excepcion_table" class="tablesorter" cellspacing="1"> 
									<thead> 									
										<tr> 
											<th>No.</th>
											<th>Concepto</th>
											<th>Mensaje</th>
											<th>Fecha</th>
											<th>Partidas</th>
											<th>Total</th>
											<th>Excedente</th>
										</tr> 
									</thead> 
									<tbody>                     
									</tbody> 
								</table>  
								<!-- Fin Tabla de Partidas -->									
							</td>
                        </tr>
                        <tr>
                            <td colspan="8">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="8">&nbsp;    </td>
                        </tr>  

                        <tr>
							<td align="right" colspan="8">
								<strong>Total solicitud:<!--<label id="TDias">0</label>d&iacute;a(s):--></strong>&nbsp;&nbsp;
								<input name="totalA" type="text" id="totalA" value="0.00" size="12" maxlength="12" disabled="disabled" style="text-align:right"/>
								<input type="hidden" id="totalSol" name="totalSol" value="0.00" readonly="readonly"/>
								<div style="display:inline">MXN</div>
							
						    </td>
						</tr>											
						<tr>
							<td width="10%">&nbsp;</td>
							<td colspan="2">
                                <div align="right" id="hist">Historial de observaciones:</div>    </td>
								<td colspan="4">
								<div align="left">
                                    <textarea name="historial_obser" id="historial_obser" cols="80"rows='3' readonly="readonly" onkeypress="confirmaRegreso('historial_obser');" onkeydown="confirmaRegreso('historial_obser');"></textarea>
                                </div>
                            </td>
							<td width="16%">&nbsp;</td>
						</tr>
						<tr>
                            <td width="10%">&nbsp;</td>
                            <td colspan="2">
                                <div align="right" id="req">Observaciones:</div>    </td>
                            <td colspan="4">
                                <div align="left">
                                    <textarea name="observ" cols="80"rows='3' id="observ"></textarea>
                                </div>
                            </td>
                            <td width="16%">&nbsp;</td>
                        </tr>
						<tr>
                            <td colspan="8">&nbsp;</td>
                        </tr>

                    </table>
					</div>
				</div>					
                <br />

				<!--<<<<TABLE QUE FUNCIONA COMO FLOTANTE>>>>>>>>>>-->
	<div name = "ventanita" id="ventanita" style="display:none; visibility:hidden; position:absolute; top:50%; left:45%; width:820px; margin-left:-350px; height:; margin-top:; border:1px solid #808080; padding:5px; background-color:#F0F0FF">
		<table bordercolor="#333333" style="font-size:11px" border="0" width="100%">
			<tr bgcolor="#8C8CFF">
				<td colspan="6">
					<font color="#ffffff"><strong>&nbsp;&nbsp;&nbsp;Cotización de hospedaje por empleado</strong></font>
				</td>
				<td width="16px">
					<img src="../../images/close2.ico" alt="Cerrar" align="right" onclick="cancelarOperacionHotel(); flotanteActive(0);" style="cursor:pointer"/>
				</td>
			</tr>
		</table>
		<table width="820" border="0" align="center" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left;">
			<tr >
				<td colspan="8" ><center><h3>Datos de itinerario</h3></center></td>
			</tr>
			<tr >
			    <td colspan="2">&nbsp;</td>
				<td align="center">No de itinerario:</td>
				<td ><div id="no_itinerario"></div></td>
				<td >Origen:</td>
				<td ><div id="origen_label"></div></td>
				<td >Destino:</td>
				<td ><div id="destino_label"></div></td>
			</tr>
		</table>
			<table width="820" align="center" border="0" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left;">
					</tr>
					<tr>
					    <td>&nbsp;</td>
						<td colspan="8" ><center><h3>Hotel</h3></center></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td colspan="8">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr colspan="2" style="text-align:center;">
						<td>&nbsp;</td>
						<td width="20%">
							<div align="right">
								Ciudad<span class="style1">*</span>: 
							</div>
						</td>
						<td width="30%">
							<div align="left">
								<input name="ciudad" type="text" id="ciudad" value="" size="40" maxlength="20"/>
							</div>
						</td>
						<td width="20%">
							<div align="right">
								Noches<span class="style1">*</span>: 
							</div>
						</td>
						<td width="20%">
							<div align="left">
								<input name="noches" disabled type="text" id="noches" value="" maxlength="10" onkeypress="" onkeyup="revisaCadena(this); getSubtotal();" onchange="getSubtotal();" readonly="readonly" />
							</div>
						</td>
						<td colspan="4">&nbsp;</td>
						<td>&nbsp;</td>
						
					</tr>
					<tr style="text-align:center;">
						<td>&nbsp;</td>
						<td>
							<div align="right">
								Hotel<span class="style1">*</span>:
							</div>
						</td>
						<td width="30%">
							<div align="left">
							<div id="hotelDiv"></div>
							<input style="display:none" name="hotel" type="text" id="hotel" value="" />
							</div>
						</td>
						<td width="20%">
							<div align="right">
								Costo por noche<span class="style1">*</span>: 
							</div>
						</td>
						<td width="20%">
							<div align="left">
								<input name="costoNoche" type="text" id="costoNoche" value="" size="15" maxlength="10"  onkeypress=" validaZeroIzquierda(this.value,this.id); getSubtotal(); return validaNum(event);" onkeyup=" getSubtotal(); validaZeroIzquierda(this.value,this.id); return validaNum(event);" onchange="getSubtotal();" />
							</div>
						</td>
						<td width="20%" colspan="3">
							<div align="right">
								Divisa<span class="style1">*</span>: 
							</div>
						</td>
						<td colspan="1">
							<div  align="left">
								<select name="selecttipodivisa" id="selecttipodivisa" onChange="getSubtotal();" >
								<!--    <option value="-1">Seleccione...</option>         -->
									<option value="1">MXN</option>
									<option value="2">USD</option>
									<option value="3">EUR</option>
								</select>
							</div>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr style="text-align:center;">
						<td>&nbsp;</td>
						<td nowrap="nowrap">
							<div align="right">
								Tipo de Hotel<span class="style1">*</span>:
							</div>
						</td>
						<td width="30%">
							<div align="left">
								<select name="tipoHotel" id="tipoHotel">
								<option value="-1">Seleccione ...</option>
								<?php 
								$cnn = new conexion();
								$query = sprintf("SELECT se.se_id AS servicio_id, se_nombre
										FROM servicios AS se
										INNER JOIN bandos_servicios AS bs ON (bs.se_id = se.se_id) 
										INNER JOIN bandos AS bn ON (bn.b_id = bs.b_id) 
										INNER JOIN cat_conceptos AS cp ON (cp.cp_id = se.cp_id)
										INNER JOIN empleado AS ep ON (ep.b_id = bn.b_id) 
										WHERE bs.f_id = %s 
										AND cp.cp_id = %s 
										AND ep.idfwk_usuario = %s", FLUJO_SOLICITUD, CONCEPTO_HOTEL, $_SESSION['idusuario']);
								//error_log("--->>Consulta Servicios(Hotel) según el flujo: ".$query);
								$rst = $cnn->consultar($query);
								while ($fila = mysql_fetch_assoc($rst)) {
									echo "<option value=".$fila["servicio_id"].">".$fila["se_nombre"]."</option>";
								} ?>
								</select>
							</div>
						</td>
						<td width="20%">
							<div align="right">
								Subtotal<span class="style1">*</span>: 
							</div>
						</td>
						<td width="20%">
							<div align="left">
								<input name="subtotal" disabled type="text" id="subtotal" value="0" size="15" maxlength="10" autocomplete="off" readonly="readonly"/>
							</div>
						</td>
						<td colspan="4">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr style="text-align:center;">
						<td>&nbsp;</td>
						<td nowrap="nowrap">
							<div align="right">
								Llegada<span class="style1">*</span>:
							</div>
						</td>
						<td width="30%">
							<div align="left">
								<input name="llegada" type="text" id="llegada" value="" size="10" maxlength="100" onchange="limpiaFechaL();"/>
							</div>
						</td>
						<td width="20%">
							<div align="right">
								IVA<span class="style1">*</span>: 
							</div>
						</td>
						<td width="20%">
							<div align="left">
								<input name="iva"  type="text" id="iva" size="15" maxlength="5" onkeypress=" validaZeroIzquierda(this.value,this.id); getSubtotal(); return validaNum(event);" onkeyup=" getSubtotal(); validaZeroIzquierda(this.value,this.id); return validaNum(event);"/>
							</div>
						</td>
						<td colspan="4">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr style="text-align:center;">
						<td>&nbsp;</td>
						<td>
							<div align="right">
								Salida<span class="style1">*</span>:
							</div>
						</td>
						<td width="30%">
							<div align="left">
								<input name="salida" type="text" id="salida" value="" size="10" maxlength="100"  />
							</div>
						</td>
						<td width="20%">
							<div align="right">
								Total: 
							</div>
						</td>
						<td width="20%">
							<div align="left">
								<input name="total" type="text" id="total" value="0" size="15" maxlength="15" autocomplete="off" disabled="disabled" readonly="readonly"/>
							</div>
						</td>
						<td colspan="4">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>
							<div align="right">
								No. de reservaci&oacute;n:
							</div>
						</td>
						<td width="30%">
							<div align="left">
								<input name="noreservacion" type="text" id="noreservacion" value="" size="10" maxlength="10" onkeyup="revisaCadena(this);" />
							</div>
						</td>
						<td width="20%" >
							<div align="right">
								Monto en pesos: 
							</div>
						</td>
						<td colspan="5" width="20%">
							<div align="left">
								<input name="montoP" type="text" disabled id="montoP" value="0.00" size="15" maxlength="15" autocomplete="off" readonly="readonly"/>&nbsp;MXN
							</div>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td colspan="10"><div>&nbsp;</div></td>
					</tr>
					<tr>
						<td colspan="10"><div id="warning_msg" style="display: none;">&nbsp;</div></td>
					</tr>
					<tr>
						<td colspan="10"><input type="hidden" id="excedeMontoHospedaje" name="excedeMontoHospedaje" value="0" size="5" readonly="readonly" /></td>
					</tr>
					<tr>
                        <td>&nbsp;</td>
                        <td align="right" valign="top"><div id="obsjus">Comentarios:&nbsp;</div></td>
                        <td colspan="8" ><div id="areatext"><textarea name="comentario" cols="95"rows='5' id="comentario"></textarea></div></td>   
                    </tr>
					<tr>
						<td colspan="10"><div>&nbsp;</div></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td width="20%" colspan="8">
							<div align="center">
							<!--onclick="agregarPartida();" style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>-->
								<input type="button" name="agregar_Hotel" id="agregar_Hotel" value="     Agregar Hotel" onclick="agregarHotel();" style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/> 
							</div>
						</td>
						<td>&nbsp;</td>
					</tr>
                    <tr>
						<td>&nbsp;</td>
						<td colspan="8" style="text-align:center">
							<div id="area_tabla_div" ></div>
						</td>
						<tr>
						    <td>&nbsp;</td>
							<td colspan="6" align="right">Total hospedaje:</td><td align="center"><div  name="TotalHospedaje" id="TotalHospedaje">0.00 </div></td><td>&nbsp;MXN</td>
						    <td>&nbsp;</td>
						</tr>
                        <td>&nbsp;</td>
						<td colspan="8">&nbsp;</td>
						<td>&nbsp;</td>
                    </tr>					
				</table>
		<br />
		<table cellspacing="1" width="100%">
			<tr>
				<td colspan="8">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="8">
					<div align="center">
						<input type="button" name="aceptarHotel" id="aceptarHotel" value="Aceptar" disabled="disabled" onClick="limpiarCamposDeHotel();flotanteActive(0);setBanderaCancelaHotel();" />&nbsp;
						<input type="button" name="volver" id="volver" value="Cancelar" onclick=" cancelarOperacionHotel(); flotanteActive(0);" /> &nbsp;
						<input type="button" name="cancelarHotel" id="cancelarHotel" value="Borrar Todo" disabled="disabled" onClick='cancelar_cotizacion_hospedaje(1); flotanteActive(0);'/> 
					</div>
				</td>
			</tr>
        </table>
        <!--Para el total de noches-->
		<input type='hidden' name='total_noches' id='total_noches' value='0' readonly='readonly' />
	</div>
    <!--<<<<TABLE QUE FUNCIONA COMO FLOTANTE RENTA AUTO NO>>>>>>>>>>-->
	<div name = "ventanita_auto" id="ventanita_auto" style="display:none; visibility:hidden; position:absolute; top:85%; left:28%; width:600px;  border:1px solid #808080; padding:5px; background-color:#F0F0FF">
		<table bordercolor="#333333" style="font-size:11px" border="0" width="100%">
			<tr bgcolor="#8C8CFF">
				<td colspan="8">
					<font color="#ffffff"><strong>&nbsp;&nbsp;&nbsp;Cotización de renta de auto por empleado</strong></font>
				</td>
				<td width="16px">
					<img src="../../images/close2.ico" alt="Cerrar" align="right" onclick="limpiarCamposDeAuto(0,1);flotanteActive2(0);" style="cursor:pointer"/>
				</td>
			</tr>
		</table>
					<table width="600" align="center" border="0" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left;">
					<tr >
						<td>&nbsp;</td>
						<td colspan="6" ><center><h3>Renta de Auto</h3></center></td>
						<td>&nbsp;</td>
					</tr>
					<tr  style="text-align:center;">
						<td>&nbsp;</td>
						<td>
							<div align="right">
								Empresa<span class="style1">*</span>: 
							</div>
						</td>
						<td>
							<div align="left">
								<input name="empresaAuto" type="text" id="empresaAuto" value="" onkeyup="deshabilitaBotones();" onchange="deshabilitaBotones();" size="20" maxlength="100"/>
							</div>
						</td>
						<td>
							<div align="right">
								Costo por día<span class="style1">*</span>: 
							</div>
						</td>
						<td>
							<div align="left">
								<input name="costoDia" type="text" id="costoDia" value="" size="10" maxlength="10" onkeypress=" validaZeroIzquierda(this.value,this.id);  getTotal_Auto(); return validaNum(event);" onChange="getTotal_Auto();" onkeyup="getTotal_Auto(); deshabilitaBotones(); validaZeroIzquierda(this.value,this.id); return validaNum(event);"/>
							</div>
						</td>
						<td colspan="1">
							<div align="right">
								Divisa<span class="style1">*</span>: 
							</div>
						</td>
						<td>
							<div  align="left">
								<select name="tipoDivisa" id="tipoDivisa" onChange="deshabilitaBotones();getTotal_Auto();" >
									<option value="1">MXN</option>
									<option value="2">USD</option>
									<option value="3">EUR</option>
								</select>
							</div>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr style="text-align:center;">
						<td>&nbsp;</td>
						<td>
							<div align="right">
								Tipo de auto<span class="style1">*</span>:
							</div>
						</td>
						<td colspan="1">
							<div  align="left">
								<select name="tipoAto" id="tipoAuto" onChange="deshabilitaBotones();" >
								<?php 
								$cnn = new conexion();
								$query = sprintf("SELECT se.se_id AS servicio_id, se_nombre
										FROM servicios AS se
										INNER JOIN bandos_servicios AS bs ON (bs.se_id = se.se_id) 
										INNER JOIN bandos AS bn ON (bn.b_id = bs.b_id) 
										INNER JOIN cat_conceptos AS cp ON (cp.cp_id = se.cp_id)
										INNER JOIN empleado AS ep ON (ep.b_id = bn.b_id) 
										WHERE bs.f_id = %s 
										AND cp.cp_id = %s 
										AND ep.idfwk_usuario = %s", FLUJO_SOLICITUD, CONCEPTO_RENTA_AUTO, $_SESSION['idusuario']);
								//error_log("--->>Consulta Servicios(Renta de Auto) según el flujo: ".$query);
								$rst = $cnn->consultar($query);
								while ($fila = mysql_fetch_assoc($rst)) {
									echo "<option value=".$fila["servicio_id"].">".$fila["se_nombre"]."</option>";
								} ?>
								</select>
							</div>
						</td>
						<td>
							<div align="right">
								Total: 
							</div>
						</td>
						<td>
							<div align="left">
								<input name="totalAuto" disabled type="text" id="totalAuto" value="0.00" size="15" maxlength="10" readonly="readonly"/>
							</div>
						</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr >
						<td>&nbsp;</td>
						<td>
							<div align="right">
								Dias de renta<span class="style1">*</span>:
							</div>
						</td>
						<td>
							<div align="left">
								<input name="diasRenta" type="text" id="diasRenta" value="" size="3" maxlength="2" onkeypress="deshabilitaBotones();validaZeroIzquierda(this.value,this.id);" onkeyup="revisaCadena(this); getTotal_Auto();validaZeroIzquierda(this.value,this.id);" onchange="getTotal_Auto();"/>
							</div>
						</td>
						<td colspan="1">
							<div align="right">
								Monto en pesos: 
							</div>
						</td>
						<td >
							<div align="left">
								<input name="montoPesos" disabled type="text" id="montoPesos" value="0.00" size="15" maxlength="15" autocomplete="off" readonly="readonly"/><td>&nbsp;MXN</td>
							</div>
						</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr >
						<td>&nbsp;</td>
						<td colspan="6" >&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr >
						<td>&nbsp;</td>
						<td colspan="6" align="center" valign="middle"><div id="warning_msg_auto" style="display: none;">&nbsp;</div></td>
						<td><input type="hidden" id="excedeMontoAuto" name="excedeMontoAuto" value="0" size="5" readonly="readonly" /></td>
					</tr>
				</table>
		<br />
		<table cellspacing="1" width="100%">
			<tr>
				<td colspan="8">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="8">
					<div align="center">
						<input type="button" name="aceptarAuto" id="aceptarAuto" value="Aceptar" disabled="disabled"  onClick="agregarAuto();" /> 
						<input type="button" name="volver" id="volver" value="Cancelar"  onclick="limpiarCamposDeAuto(1,0);flotanteActive2(0);" /> &nbsp;
						<input type="button" name="cancelarAuto" id="cancelarAuto" value="Borrar Todo" disabled="disabled"  onClick='borrarTodo(0);flotanteActive2(0);'/> 
					</div>
				</td>
			</tr>
        </table>		
	</div>
				<br />
				<div align="center">
					<input type="submit" id="guardarprevComp" name="guardarprevComp" value="     Guardar Previo"  onclick="return solicitarConfirmPrevio(this.id);"  disabled="disabled" style="background:url(../../images/save.gif); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" readonly="readonly"/>
					&nbsp;&nbsp;&nbsp;
                	<input type="submit" id="guardarComp" name="guardarComp" value="     Enviar Solicitud" onclick="return confirmacion(this.id);" disabled="disabled" style="background:url(../../images/save.gif); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>
					&nbsp;&nbsp;&nbsp;
					<input type="submit" id="autorizar_cotizacion" name="autorizar_cotizacion" value="    Enviar a Autorizador"  onclick="return confirmacion(this.id);" style="background:url(../../images/Arrow_Right.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; display:none;"/>
					<input type="submit" id="envia_a_director" name="envia_a_director" value="    Enviar a Director"  onclick="return confirmacion(this.id);" style="background:url(../../images/Arrow_Right.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; display:none;"/>
				</div>
                <?PHP 
//Obtiene los valores de los parámetros requeridos para esta pantalla
                $_parametros = new Parametro();
                $_parametros->Load(32); //busca parametro de limite de dias de anticipación para hacer solicitud de viaje Nacional
                $DiasAnticipacionSolicitudNacional = $_parametros->Get_dato("pr_cantidad");
                $_parametros->Load(33); //busca parametro de limite de dias de anticipación para hacer solicitud de viaje Internacional
                $DiasAnticipacionSolicitudInternacional = $_parametros->Get_dato("pr_cantidad");
				
                $divisa = new Divisa();
                $divisa->Load_data(2);
                $div_USD = $divisa->Get_dato("div_tasa");
                
                $divisa->Load_data(3);
                $div_EUR = $divisa->Get_dato("div_tasa");
                ?>
                <input type='hidden' id='dias_anticipacion_solicitud_nacional' name='dias_anticipacion_solicitud_nacional' value="<?PHP echo $DiasAnticipacionSolicitudNacional; ?>">
                <input type='hidden' id='dias_anticipacion_solicitud_internacional' name='dias_anticipacion_solicitud_internacional' value="<?PHP echo $DiasAnticipacionSolicitudInternacional; ?>">
                <input type='hidden' id='excepciones' name='excepciones' value="0" readonly="readonly">
                <input type='hidden' id='mensaje_excepcion' name='mensaje_excepcion' value="" readonly="readonly">
				<input type='hidden' id='totalExcepciones' name='totalExcepciones' value="" readonly="readonly">
                
                <input type="hidden" name="guarda" id="guarda" value="" readonly="readonly" />
                <input type="hidden" name="idusuario" id="idusuario" value="<?PHP echo $_SESSION["idusuario"]; ?>" readonly="readonly" />
                <input type="hidden" name="empleado" id="empleado" value="<?PHP echo $_SESSION["idusuario"]; ?>" readonly="readonly" />
                <input type="hidden" name="empresa" id="empresa" value="<?PHP echo $_SESSION["empresa"]; ?>" readonly="readonly" />
                <input type="hidden" id="select_tipo_viaje_pasaje_val" name="select_tipo_viaje_pasaje_val" value="0" readonly="readonly"/>
                <input type="hidden" id="rowCount" name="rowCount" value="0" readonly="readonly"/>
                <input type="hidden" id="rowDel" name="rowDel" value="0" readonly="readonly"/>
				<input type="hidden" id="rowCount_auto" name="rowCount_auto" value="0" readonly="readonly"/>
				<input type="hidden" id="rowDel_auto" name="rowDel_auto" value="0" readonly="readonly"/>
                <input type="hidden" id="rowCountConceptos" name="rowCountConceptos" value="0" readonly="readonly"/>
                <input type="hidden" name="TotalDias" id="TotalDias" value="0" size="3" maxlength="10"/>
                <input type="hidden" name="TotalNoches" id="TotalNoches" value="0" size="3" maxlength="10"/>
                <input type="hidden" id="itinerarioActualOPosible" name="itinerarioActualOPosible" value="1" readonly="readonly"/>
                <input type="hidden" name="tipoDeViaje" id="tipoDeViaje" value="0" size="3" maxlength="5"/>
                <input type="hidden" name="idDeRegion" id="idDeRegion" value="" size="3" maxlength="5"/>
                <input type="hidden" name="idsDeFilasConceptos" id="idsDeFilasConceptos" value="" size="30" />
                <input type="hidden" name="idRentaAutoC" id="idRentaAutoC" value="" size="30" />
                <input type="hidden" name="idHotelC" id="idHotelC" value="" size="30" />
                <input type="hidden" name="montoAvionSol" id="montoAvionSol" value="" size="30" />
                <input type="hidden" name="tasaUSD" id="tasaUSD" value="<?PHP echo $div_USD;?>" readonly="readonly" />
                <input type="hidden" name="tasaEUR" id="tasaEUR" value="<?PHP echo $div_EUR;?>" readonly="readonly" />
				<input type="hidden" name="delegado" id="delegado" readonly="readonly" value="<?php if(isset($_SESSION['idrepresentante'])){ echo $_SESSION['idrepresentante']; }else{echo 0;}?>" />
				<input type="hidden" name="etapaActual" id="etapaActual" value="<?php echo $etapaEdicion;?>" size="3" maxlength="5"/>
				<input type="hidden" name="noTramite" id="noTramite" value="<?PHP if(isset($_GET['id'])){echo $_GET['id']; }else{ echo "0";} ?>" size="3" maxlength="5"/>
				<input type="hidden" name="diasAnticipos" id="diasAnticipos" value="0" size="3" maxlength="5" readonly="readonly"/>
				<input type="hidden" name="desactivandoCheck" id="desactivandoCheck" value="0" size="3" maxlength="5" readonly="readonly"/>
				<input type="hidden" name="flujoId" id="flujoId" value="<?PHP echo FLUJO_SOLICITUD;?>" readonly="readonly" />
				<div id="area_counts_div"></div>
				<div id="tipo_explorador" style="display:none"></div>

				<!-- Tablas de conceptos y contadores respectivos -->
				<div id="area_tablas_conceptos_div" style="display: none"></div>
				<div id="area_counts_conceptos_div" style="display: none"></div>
							
				<?PHP if(isset($_GET['id'])){?>
					<input type="hidden" name="tramiteID" id="tramiteID" value="<?PHP echo $_GET['id']?>" size="3" maxlength="5"/>
					<input type="hidden" name="etapaID" id="etapaID" value="0" size="3" maxlength="5"/>
				<?PHP }?>
                </center>
            </form>

            <?PHP 
        } // termina seccion cuando no hay solicitudes pendientes de aprobar
    }
?>
