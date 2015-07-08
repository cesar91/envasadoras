<?PHP 
require_once("../../lib/php/constantes.php");
require_once("../../Connections/fwk_db.php");
require_once("../../functions/Usuario.php");
require_once("../../functions/Tramite.php");
require_once("../../functions/CentroCosto.php");
require_once("../../functions/Comensales.php");
require_once("../../functions/Divisa.php");
require_once("../../functions/RutaAutorizacion.php");
require_once("../../functions/Notificacion.php");
require_once("../../functions/Delegados.php");
require_once("../../functions/utils.php");
require_once("services/C_SV.php");

	function eliminar_autorizador_iniciador($ruta_de_autorizacion,$id_user){
		$separador = "|";
		$aux3 = "";
		$token = strtok($ruta_de_autorizacion,$separador);
		while($token != false){
			if($token != $id_user){
				if($aux3 == ""){
					$aux3 .= $token;
				}else{
					$aux3 .= "|".$token;
				}
			}
			$token = strtok($separador);
		}
		return $aux3;
	}
	
if (isset($_POST['guardarComp'])) {	
	function existe_substr($cadena,$substr,$separador){
		$aux2 = false;
		$token = strtok($cadena,$separador);
		while($token != false){
			if($token == $substr){
				$aux2 = true;
			}
			$token = strtok($separador);
		}
		return $aux2;
	}
	
	if (isset($_POST['rowCount']) || $_POST['rowCount'] != 1) {
		$motivo = $_POST['motive'];
		$f_invitacion = $_POST['fecha_inv'];
		$inv_lugar = $_POST['lugar_inv'];
		$inv_hubo_exedente = $_POST['banderavalida'];
		$no_invitados = $_POST['numInvitados'];
		$monto_solicitud = str_replace(",","",$_POST['monto_solicitado_invitacion']);
		$divisa_euro = $_POST['valorDivisaEUR'];
		$divisa_dollar = $_POST['valorDivisaUSD'];
		$divisa_solicitud = $_POST['divisa_solicitud_invitacion'];
		//$total_pesos = $_POST['tpesos'];
		$cecos_solicitud = $_POST['ccentro_costos'];
		$observaciones_solicitud = $_POST['observ'];
		$ciudad_solicitud = $_POST['ciudad_invitacion'];
		$sesionDelegado = $_POST['delegado'];
        $fechainvit = fecha_to_mysql($f_invitacion); 
        
        // Total pesos
        $total_pesos = $_POST['tpesos'];
        $total_pesos = str_replace(',', '', $total_pesos);
               
		error_log("solicitudotika:".$divisa_solicitud);
		switch ($divisa_solicitud) {
			case 1: $divisa_solicitud_final = "MXN";
				$monto_pesos_solicitud = (float) $monto_solicitud;
				break;
			case 2: $divisa_solicitud_final = "USD";
				//$monto_pesos_solicitud = (float) $monto_solicitud * (float) $divisaUSD;
				$monto_pesos_solicitud = (float) $monto_solicitud * (float) $divisa_dollar;
				break;
			case 3: $divisa_solicitud_final = "EUR";
				//$monto_pesos_solicitud = (float) $monto_solicitud * (float) $divisaEUR;
				$monto_pesos_solicitud = (float) $monto_solicitud * (float) $divisa_euro;
				break;
		}

		//Datos del empleado
		$iduser = 0;
		$delegado = 0;
		$numempleado = $_POST["empleado"];
		$idempresa = $_POST["empresa"];
		$cnn = new conexion();
		$FechaMySQL = date("Y-m-d");
		
		if($sesionDelegado != 0){
			$iduser = $sesionDelegado;
			$delegado = $_POST["idusuario"];
		}else{
			$iduser = $_POST["idusuario"];
		}

		// Registra nuevo tramite
		$tramite = new Tramite();
		//$tramite->insertar("BEGIN WORK");
		error_log("Motivo".$_POST['motive']);
		$idTramite = $tramite->Crea_Tramite($iduser, $idempresa, SOLICITUD_INVITACION_ETAPA_APROBACION, FLUJO_SOLICITUD_INVITACION, $motivo, $delegado);
		$Solicitud_invitacion = new C_SV();
		
		$delegados = new Delegados();
		$existeDelegado = $delegados->existenciaDelegado($iduser, $delegado);
		
		// Agregamos el nombre del usuario al campo de Observaciones
		if($observaciones_solicitud != ""){
			$HObser = "";
			$notificacion = new Notificacion();
			
			if($existeDelegado){
				$usuarioObserv = $delegado;
			}else{
				$usuarioObserv = $iduser;
			}
			
			$observaciones_solicitud = $notificacion->anotaObservacion($usuarioObserv, $HObser, $observaciones_solicitud, FLUJO_SOLICITUD_INVITACION, SOLICITUD_INVITACION_ETAPA_APROBACION);
		}
		
		$invitacionID = $Solicitud_invitacion->Add_invitacion($motivo, $no_invitados, $monto_solicitud, $monto_pesos_solicitud, $divisa_solicitud_final, $cecos_solicitud, $idTramite, $ciudad_solicitud, $observaciones_solicitud, "",  $fechainvit, $inv_lugar, $inv_hubo_exedente);
		//Inserción del solicitante
		for ($i = 1; $i <= $_POST['rowCount']; $i++) {
			$sNombre = $_POST['nombre' . $i];
			$sPuesto = $_POST['puesto' . $i];
			$sTipo = $_POST['tipo' . $i];
			$sEmpresa = $_POST['empresa' . $i];
			$id_detalle_solicitud_invitacion=add_detalle_solicitud_invitacion($invitacionID, $sNombre, $sPuesto, $sEmpresa, $sTipo, $idTramite);
		}

		// Buscamos quien debe aprobar esta solicitud
		$ruta_autorizacion = new RutaAutorizacion();
		$ruta_autorizacion->generaRutaAutorizacionSolicitudInvitacion($idTramite, $iduser);
		$aprobador = $ruta_autorizacion->getSiguienteAprobador($idTramite, $iduser);
			
		// Envia el tramite a aprobacion
		$usuarioAprobador = new Usuario();
		$usuarioAprobador->Load_Usuario_By_ID($aprobador);
		$duenoActual = new Usuario();
		$duenoActual->Load_Usuario_By_ID($iduser);
		$nombreUsuario = $duenoActual->Get_dato('nombre');
		
		$tramite->Load_Tramite($idTramite);
		$rutaAutorizacion=$tramite->Get_dato('t_ruta_autorizacion');
		$tramite->Modifica_Etapa($idTramite, SOLICITUD_INVITACION_ETAPA_APROBACION, FLUJO_SOLICITUD_INVITACION, $aprobador,$rutaAutorizacion);

		/*$mensaje = sprintf("La solicitud de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>CREADA</strong> por <strong>%s</strong> y te ha sido asignada para su autorizacion.", $idTramite, $duenoActual->Get_dato('nombre'));*/
		if($sesionDelegado != 0){
			$duenoActual->Load_Usuario_By_ID($delegado);
			$nombreDelegado = $duenoActual->Get_dato('nombre');
			$mensaje = sprintf("La solicitud de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> en nombre de: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $idTramite, $nombreDelegado, $nombreUsuario);
			$mensaje_email = sprintf("La solicitud de Invitaci&oacute;n <strong>CREADA</strong> por: <strong>%s</strong> en nombre de: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $nombreDelegado, $nombreUsuario);
		}else{
			$mensaje = sprintf("La solicitud de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $idTramite, $nombreUsuario);
			$mensaje_email = sprintf("La solicitud de Invitaci&oacute;n <strong>CREADA</strong> por: <strong>%s</strong> requiere de su autorizaci&oacute;n.", $nombreUsuario);
		}
		
		//$tramite->EnviaMensaje($idTramite, $mensaje);
		$remitente = $iduser;
		$destinatario = $aprobador;
		$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "1", $mensaje_email); //"0" para no enviar email y "1" para enviarlo

		// Termina transacción
		$tramite->insertar("COMMIT");

		// Regresa a la pagina del index
		//echo "<meta http-equiv='location' content='0; url=http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=2&oksave'>";
		header("Location: ./index.php?docs=docs&type=2&oksave");
	} else {
		//echo "<meta http-equiv='location' content='0; url=http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=2&errsave'>";
		header("Location: ./index.php?docs=docs&type=2&errsave");
		//die();
	}//if row
}//if guarda comp

//guardar comprobación edición:
if (isset($_POST['guardarCompedit'])) {
	function existe_substr($cadena,$substr,$separador){
		$aux2 = false;
		$token = strtok($cadena,$separador);
		while($token != false){
			if($token == $substr){
				$aux2 = true;
			}
			$token = strtok($separador);
		}
		return $aux2;
	}

	function limpiar_invitados($id_tramite){
		$cnn = new conexion();
		$query="DELETE FROM comensales_sol_inv WHERE dci_solicitud='{$id_tramite}'";
		error_log($query);
		$cnn->ejecutar($query);
	}
	
	if (isset($_POST['rowCount']) || $_POST['rowCount'] != 1) {
		$tramite_editar=$_POST['tramite_sol'];
		error_log($tramite_editar);
		$motivo = $_POST['motive'];
		$f_invitacion = $_POST['fecha_inv'];
		$inv_lugar = $_POST['lugar_inv'];
		$inv_hubo_exedente = $_POST['banderavalida'];
		$no_invitados = $_POST['numInvitados'];
		$monto_solicitud = str_replace(",","",$_POST['monto_solicitado_invitacion']);
		$divisa_euro = $_POST['valorDivisaEUR'];
		$divisa_dollar = $_POST['valorDivisaUSD'];
		$divisa_solicitud = $_POST['divisa_solicitud_invitacion'];
		//$total_pesos = $_POST['tpesos'];
		$cecos_solicitud = $_POST['ccentro_costos'];
		$observaciones_solicitud = $_POST['observ'];
		$ciudad_solicitud = $_POST['ciudad_invitacion'];
		$t_etapa = $_POST['etapa'];
		$sesionDelegado = $_POST['delegado'];
		$fechainvit = fecha_to_mysql($f_invitacion);

		// Total pesos
		$total_pesos = $_POST['tpesos'];
		$total_pesos = str_replace(',', '', $total_pesos);

		switch ($divisa_solicitud) {
			case 1: $divisa_solicitud_final = "MXN";
			$monto_pesos_solicitud = (float) $monto_solicitud;
			break;
			case 2: $divisa_solicitud_final = "USD";
			//$monto_pesos_solicitud = (float) $monto_solicitud * (float) $divisaUSD;
			$monto_pesos_solicitud = (float) $monto_solicitud * (float) $divisa_dollar;
			break;
			case 3: $divisa_solicitud_final = "EUR";
			//$monto_pesos_solicitud = (float) $monto_solicitud * (float) $divisaEUR;
			$monto_pesos_solicitud = (float) $monto_solicitud * (float) $divisa_euro;
			break;
		}

		//Datos del empleado
		$iduser = 0;
		$delegado = 0;
		$numempleado = $_POST["empleado"];
		$idempresa = $_POST["empresa"];
		$cnn = new conexion();
		$FechaMySQL = date("Y-m-d");
		
		if($sesionDelegado != 0){
			$iduser = $sesionDelegado;
			$delegado = $_POST["idusuario"];
		}else{
			$iduser = $_POST["idusuario"];
		}
		
		$delegados = new Delegados();
		$tramite = new Tramite();
		$tramite->Load_Tramite($tramite_editar);
		$t_autorizaciones_historial = $tramite->Get_dato('t_autorizaciones_historial');
		
		$tramite->Modifica_Etapa($tramite_editar, SOLICITUD_INVITACION_ETAPA_APROBACION, FLUJO_SOLICITUD_INVITACION,"", "");
		
		$Solicitud_invitacion = new C_SV();
		$existeDelegado = $delegados->existenciaDelegado($iduser, $delegado);
		
		if($t_etapa == SOLICITUD_INVITACION_ETAPA_RECHAZADA || $t_etapa == SOLICITUD_INVITACION_ETAPA_RECHAZADA_POR_DIRECTOR || $t_autorizaciones_historial != ""){
			$HObser = $_POST['historial_observaciones'];
		}else{
			$HObser = "";
		}
		// Agregamos el nombre del usuario al campo de Observaciones
		if($observaciones_solicitud != ""){
			$notificacion = new Notificacion();
			if($existeDelegado){
				$usuarioObserv = $delegado;
			}else{
				$usuarioObserv = $iduser;
			}
			$observaciones_solicitud = $notificacion->anotaObservacion($usuarioObserv, $HObser, $observaciones_solicitud, FLUJO_SOLICITUD_INVITACION, SOLICITUD_INVITACION_ETAPA_APROBACION);
		}else{
			$observaciones_solicitud = $HObser;
		}
		//error_log("----->>>>>>>>>Observaciones: ".$observaciones_solicitud);
		limpiar_invitados($tramite_editar);
		$invitacionID = $Solicitud_invitacion->Edit_invitacion($motivo, $no_invitados, $monto_solicitud, $monto_pesos_solicitud, $divisa_solicitud_final, $cecos_solicitud, $ciudad_solicitud, $observaciones_solicitud, "", $fechainvit,$inv_lugar,$inv_hubo_exedente,$tramite_editar);
		error_log((float)$invitacionID);
		
		//Inserción del solicitante
		for ($i = 1; $i <= $_POST['rowCount']; $i++) {
			$sNombre = $_POST['nombre' . $i];
			$sPuesto = $_POST['puesto' . $i];
			$sTipo = $_POST['tipo' . $i];
			$sEmpresa = $_POST['empresa' . $i];
			$id_detalle_solicitud_invitacion=add_detalle_solicitud_invitacion($invitacionID, $sNombre, $sPuesto, $sEmpresa, $sTipo, $tramite_editar);
		}

		$ruta_autorizacion = new RutaAutorizacion();
		$ruta_autorizacion->generaRutaAutorizacionSolicitudInvitacion($tramite_editar, $iduser);
		$aprobador = $ruta_autorizacion->getSiguienteAprobador($tramite_editar, $iduser);
		
		// Envia el tramite a aprobacion
		$usuarioAprobador = new Usuario();
		$usuarioAprobador->Load_Usuario_By_ID($aprobador);
		$duenoActual = new Usuario();
		$duenoActual->Load_Usuario_By_ID($iduser);
		$nombreUsuario = $duenoActual->Get_dato('nombre');
		
		$tramite->Load_Tramite($tramite_editar);
		$rutaAutorizacion=$tramite->Get_dato('t_ruta_autorizacion');
		$tramite->Modifica_Etapa($tramite_editar, SOLICITUD_INVITACION_ETAPA_APROBACION, FLUJO_SOLICITUD_INVITACION, $aprobador, $rutaAutorizacion);

		/*$mensaje = sprintf("La solicitud de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>CREADA</strong> por <strong>%s</strong> y te ha sido asignada para su autorizacion.", $tramite_editar, $duenoActual->Get_dato('nombre'));*/
		if($sesionDelegado != 0){
			$tramite->Load_Tramite($tramite_editar);
			$delegado = $tramite->Get_dato('t_delegado');
			$duenoActual->Load_Usuario_By_ID($delegado);
			$nombreDelegado = $duenoActual->Get_dato('nombre');
			$mensaje = sprintf("La solicitud de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> en nombre de: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $tramite_editar, $nombreDelegado, $nombreUsuario);
			$mensaje_email = sprintf("La solicitud de Invitaci&oacute;n <strong>CREADA</strong> por: <strong>%s</strong> en nombre de: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $nombreDelegado, $nombreUsuario);
		}else{
			$mensaje = sprintf("La solicitud de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $tramite_editar, $nombreUsuario);
			$mensaje_email = sprintf("La solicitud de Invitaci&oacute;n <strong>CREADA</strong> por: <strong>%s</strong> requiere de su autorizaci&oacute;n.", $nombreUsuario);
		}
		
		//$tramite->EnviaMensaje($idTramite, $mensaje);
		$remitente = $iduser;
		$destinatario = $aprobador;
		$tramite->EnviaNotificacion($tramite_editar, $mensaje, $remitente, $destinatario, "1", $mensaje_email); //"0" para no enviar email y "1" para enviarlo
		
		if(!$existeDelegado){
	    	//Si el previo lo genero un delegado, pero la solicitud, la envio el Director, borraremos el id del delegado que realizo el previo.
	    	$tramite->actualizaDelegado($tramite_editar, 0);
		}else{
	    	//Si el previo lo genero un delegado, pero la solicitud, la envio el Director, y en un rechazo la envio el delegado, se guardará nuevamente el id del delegado.
	    	$tramite->actualizaDelegado($tramite_editar, $delegado);
	    }
		
		// Termina transacción
		$tramite->insertar("COMMIT");

		// Regresa a la pagina del index
		//echo "<meta http-equiv='location' content='0; url=http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=2&oksave'>";
		header("Location: ./index.php?docs=docs&type=2&oksave");
	} else {
		//echo "<meta http-equiv='location' content='0; url=http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=2&errsave'>";
		header("Location: ./index.php?docs=docs&type=2&errsave");
		//die();
	}//if row
}//if guarda comp


if (isset($_POST['guardarprevComp'])) {
	if (isset($_POST['rowCount']) || $_POST['rowCount'] != 1) {
		$motivo = $_POST['motive'];
		$f_invitacion = $_POST['fecha_inv'];
		$inv_lugar = $_POST['lugar_inv'];
		$inv_hubo_exedente = $_POST['banderavalida'];
		$no_invitados = $_POST['numInvitados'];
		$monto_solicitud = str_replace(",","",$_POST['monto_solicitado_invitacion']);
		$divisa_euro = $_POST['valorDivisaEUR'];
		$divisa_dollar = $_POST['valorDivisaUSD'];
		$divisa_solicitud = $_POST['divisa_solicitud_invitacion'];
		//$total_pesos = $_POST['tpesos'];
		$cecos_solicitud = $_POST['ccentro_costos'];
		$observaciones_solicitud = $_POST['observ'];
		//$historial = $_POST['historial_observaciones'];
		$ciudad_solicitud = $_POST['ciudad_invitacion'];
		$t_etapa = $_POST['etapa'];
		$sesionDelegado = $_POST['delegado'];
		$fechainvit = fecha_to_mysql($f_invitacion);
		
		// Total pesos
		$total_pesos = $_POST['tpesos'];
		$total_pesos = str_replace(',', '', $total_pesos);
		
		switch ($divisa_solicitud) {
			case -1: $divisa_solicitud_final = "";
			$monto_pesos_solicitud = (float) $monto_solicitud;
			case 1: $divisa_solicitud_final = "MXN";
			$monto_pesos_solicitud = (float) $monto_solicitud;
			break;
			case 2: $divisa_solicitud_final = "USD";
			//$monto_pesos_solicitud = (float) $monto_solicitud * (float) $divisaUSD;
			$monto_pesos_solicitud = (float) $monto_solicitud * (float) $divisa_dollar;
			break;
			case 3: $divisa_solicitud_final = "EUR";
			//$monto_pesos_solicitud = (float) $monto_solicitud * (float) $divisaEUR;
			$monto_pesos_solicitud = (float) $monto_solicitud * (float) $divisa_euro;
			break;
		}
	
		//Datos del empleado
		$iduser = 0;
		$delegado = 0;
		$numempleado = $_POST["empleado"];
		$idempresa = $_POST["empresa"];
		$cnn = new conexion();
		$FechaMySQL = date("Y-m-d");
		
		if($sesionDelegado != 0){
			$iduser = $sesionDelegado;
			$delegado = $_POST["idusuario"];
		}else{
			$iduser = $_POST["idusuario"];
		}
		
		//error_log(">>>>>>>>Usuario delegado: ".$iduser);
		//error_log(">>>>>>>>Usuario titular: ".$delegado);
		
		// Registra nuevo tramite
		$tramite = new Tramite();
		$idTramite = $tramite->Crea_Tramite($iduser, $idempresa, SOLICITUD_INVITACION_ETAPA_SIN_ENVIAR, FLUJO_SOLICITUD_INVITACION, $motivo, $delegado);
		$Solicitud_invitacion = new C_SV();
		
		$invitacionID = $Solicitud_invitacion->Add_invitacion($motivo, $no_invitados, $monto_solicitud, $monto_pesos_solicitud, $divisa_solicitud_final, $cecos_solicitud, $idTramite, $ciudad_solicitud, "", $observaciones_solicitud, $fechainvit, $inv_lugar, $inv_hubo_exedente);
		//Inserción del solicitante
		for ($i = 1; $i <= $_POST['rowCount']; $i++) {
			$sNombre = $_POST['nombre' . $i];
			$sPuesto = $_POST['puesto' . $i];
			$sTipo = $_POST['tipo' . $i];
			$sEmpresa = $_POST['empresa' . $i];
			$id_detalle_solicitud_invitacion=add_detalle_solicitud_invitacion($invitacionID, $sNombre, $sPuesto, $sEmpresa, $sTipo, $idTramite);
		}
		
		$tramite->insertar("COMMIT");
		
		// Regresa a la pagina del index
		//echo "<meta http-equiv='location' content='0; url=http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=2&oksaveP'>";
		header("Location: ./index.php?docs=docs&type=2&oksaveP");
	} else {
		//echo "<meta http-equiv='location' content='0; url=http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=2&errsaveP'>";
		header("Location: ./index.php?docs=docs&type=2&errsaveP");
		//die();
	}//if row
}

//guardar previo edit
if (isset($_POST['guardarprevCompedit'])) {	
	function existe_substr($cadena,$substr,$separador){
		$aux2 = false;
		$token = strtok($cadena,$separador);
		while($token != false){
			if($token == $substr){
				$aux2 = true;
			}
			$token = strtok($separador);
		}
		return $aux2;
	}

	function limpiar_invitados($id_tramite){
		$cnn = new conexion();
		$query="DELETE FROM comensales_sol_inv WHERE dci_solicitud='{$id_tramite}'";
		error_log($query);
		$cnn->ejecutar($query);
	}
	
	if (isset($_POST['rowCount']) || $_POST['rowCount'] != 1) {
		$tramite_editar=$_POST['tramite_sol'];
		$motivo = $_POST['motive'];
		$f_invitacion = $_POST['fecha_inv'];
		$inv_lugar = $_POST['lugar_inv'];
		$inv_hubo_exedente = $_POST['banderavalida'];
		$no_invitados = $_POST['numInvitados'];
		$monto_solicitud = str_replace(",","",$_POST['monto_solicitado_invitacion']);
		$divisa_euro = $_POST['valorDivisaEUR'];
		$divisa_dollar = $_POST['valorDivisaUSD'];
		$divisa_solicitud = $_POST['divisa_solicitud_invitacion'];
		//$total_pesos = $_POST['tpesos'];
		$cecos_solicitud = $_POST['ccentro_costos'];
		$observaciones_solicitud = $_POST['observ'];		
		$ciudad_solicitud = $_POST['ciudad_invitacion'];
		$t_etapa = $_POST['etapa'];
		$sesionDelegado = $_POST['delegado'];
		$fechainvit = fecha_to_mysql($f_invitacion);
		
		// Total pesos
		$total_pesos = $_POST['tpesos'];
		$total_pesos = str_replace(',', '', $total_pesos);

		switch ($divisa_solicitud) {
			case -1: $divisa_solicitud_final = "";
			$monto_pesos_solicitud = (float) $monto_solicitud;
			case 1: $divisa_solicitud_final = "MXN";
			$monto_pesos_solicitud = (float) $monto_solicitud;
			break;
			case 2: $divisa_solicitud_final = "USD";
			//$monto_pesos_solicitud = (float) $monto_solicitud * (float) $divisaUSD;
			$monto_pesos_solicitud = (float) $monto_solicitud * (float) $divisa_dollar;
			break;
			case 3: $divisa_solicitud_final = "EUR";
			//$monto_pesos_solicitud = (float) $monto_solicitud * (float) $divisaEUR;
			$monto_pesos_solicitud = (float) $monto_solicitud * (float) $divisa_euro;
			break;
		}

		//Datos del empleado
		$iduser = $_POST["idusuario"];
		$numempleado = $_POST["empleado"];
		$idempresa = $_POST["empresa"];
		$cnn = new conexion();
		$FechaMySQL = date("Y-m-d");

		// Registra nuevo tramite
		limpiar_invitados($tramite_editar);
		$Solicitud_invitacion = new C_SV();
		$tramite = new Tramite();
		$tramite->Load_Tramite($tramite_editar);
		$historial_autorizaciones = $tramite->Get_dato('t_autorizaciones_historial');
		
		$invitacionID = $Solicitud_invitacion->Edit_invitacion($motivo, $no_invitados, $monto_solicitud, $monto_pesos_solicitud, $divisa_solicitud_final, $cecos_solicitud,  $ciudad_solicitud, "", $observaciones_solicitud, $fechainvit,$inv_lugar,$inv_hubo_exedente,$tramite_editar);		

		//Inserción del solicitante
		for ($i = 1; $i <= $_POST['rowCount']; $i++){
			$sNombre = $_POST['nombre' . $i];
			$sPuesto = $_POST['puesto' . $i];
			$sTipo = $_POST['tipo' . $i];
			$sEmpresa = $_POST['empresa' . $i];
			$id_detalle_solicitud_invitacion=add_detalle_solicitud_invitacion($invitacionID, $sNombre, $sPuesto, $sEmpresa, $sTipo, $tramite_editar);
		}
		
		if($t_etapa == SOLICITUD_INVITACION_ETAPA_RECHAZADA || $t_etapa == SOLICITUD_INVITACION_ETAPA_RECHAZADA_POR_DIRECTOR || $historial_autorizaciones != ""){
			$historial = $_POST['historial_observaciones'];
			$cnn = new conexion();
			$query = sprintf("UPDATE solicitud_invitacion SET si_observaciones = '%s' WHERE si_tramite = '%s'", $historial, $tramite_editar);
			//error_log($query);
			$cnn->ejecutar($query);
			
			$tramite = new Tramite();
			$tramite->Modifica_Etapa($tramite_editar, SOLICITUD_INVITACION_ETAPA_SIN_ENVIAR, FLUJO_SOLICITUD_INVITACION, "", "");
			
			// Limpiar el campo de la ruta de autorización
			$query = sprintf("UPDATE tramites SET t_ruta_autorizacion = '' WHERE t_id = '%s'", $tramite_editar);
			//error_log($query);
			$cnn->ejecutar($query);
		}
		
		//echo "<meta http-equiv='location' content='0; url=http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=2&oksaveP'>";
		header("Location: ./index.php?docs=docs&type=2&oksaveP");
	} else {
		//echo "<meta http-equiv='location' content='0; url=http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=2&errsaveP'>";
		header("Location: ./index.php?docs=docs&type=2&errsaveP");
		//die();
	}//if row
}

// Enviar Solicitud al Director:
if (isset($_POST['enviaDirector'])){
	function limpiar_invitados($id_tramite){
		$cnn = new conexion();
		$query="DELETE FROM comensales_sol_inv WHERE dci_solicitud='{$id_tramite}'";
		error_log($query);
		$cnn->ejecutar($query);
	}
	
	$motivo = $_POST['motive'];
	$f_invitacion = $_POST['fecha_inv'];
	$inv_lugar = $_POST['lugar_inv'];
	$inv_hubo_exedente = $_POST['banderavalida'];
	$no_invitados = $_POST['numInvitados'];
	$monto_solicitud = str_replace(",","",$_POST['monto_solicitado_invitacion']);
	$divisa_euro = $_POST['valorDivisaEUR'];
	$divisa_dollar = $_POST['valorDivisaUSD'];
	$divisa_solicitud = $_POST['divisa_solicitud_invitacion'];
	//$total_pesos = $_POST['tpesos'];
	$cecos_solicitud = $_POST['ccentro_costos'];
	$observaciones_solicitud = $_POST['observ'];
	$ciudad_solicitud = $_POST['ciudad_invitacion'];
	$sesionDelegado = $_POST['delegado'];
	$fechainvit = fecha_to_mysql($f_invitacion);
		
	// Total pesos
	$total_pesos = $_POST['tpesos'];
	$total_pesos = str_replace(',', '', $total_pesos);
	
	switch ($divisa_solicitud) {
		case 1: $divisa_solicitud_final = "MXN";
		$monto_pesos_solicitud = (float) $monto_solicitud;
		break;
		case 2: $divisa_solicitud_final = "USD";
		//$monto_pesos_solicitud = (float) $monto_solicitud * (float) $divisaUSD;
		$monto_pesos_solicitud = (float) $monto_solicitud * (float) $divisa_dollar;
		break;
		case 3: $divisa_solicitud_final = "EUR";
		//$monto_pesos_solicitud = (float) $monto_solicitud * (float) $divisaEUR;
		$monto_pesos_solicitud = (float) $monto_solicitud * (float) $divisa_euro;
		break;
	}
	
	//Datos del empleado
	$iduser = 0;
	$delegado = 0;
	$numempleado = $_POST["empleado"];
	$idempresa = $_POST["empresa"];
	$cnn = new conexion();
	$FechaMySQL = date("Y-m-d");
		
	if($sesionDelegado != 0){
		$iduser = $sesionDelegado;
		$delegado = $_POST["idusuario"];
	}else{
		$iduser = $_POST["idusuario"];
	}
	
	$tramite = new Tramite();
	$Solicitud_invitacion = new C_SV();
	$notificacion = new Notificacion();
	
	if (isset($_POST['rowCount']) || $_POST['rowCount'] != 1){
		if(isset($_POST['tramiteID']) && $_POST['tramiteID'] != 0){
			$idTramite = $_POST['tramiteID'];
			$tramite->Load_Tramite($idTramite);
			$t_autorizaciones_historial = $tramite->Get_dato("t_autorizaciones_historial");
			$t_etapa = $_POST['etapa'];
						
			if($t_etapa == 4 || $t_etapa == 6 || $t_autorizaciones_historial != ""){
				$HObser = $_POST['historial_observaciones'];
			}else{
				$HObser = "";
			}
				
			// Agregamos el nombre del usuario al campo de Observaciones
			if($observaciones_solicitud != ""){
				$observaciones_solicitud = $notificacion->anotaObservacion($delegado, $HObser, $observaciones_solicitud, FLUJO_SOLICITUD_INVITACION, SOLICITUD_INVITACION_ETAPA_APROBACION);
			}else{
				$observaciones_solicitud = $HObser;
			}
			
			limpiar_invitados($idTramite);
			$invitacionID = $Solicitud_invitacion->Edit_invitacion($motivo, $no_invitados, $monto_solicitud, $monto_pesos_solicitud, $divisa_solicitud_final, $cecos_solicitud,  $ciudad_solicitud, $observaciones_solicitud, "", $fechainvit,$inv_lugar,$inv_hubo_exedente,$idTramite);
			
		}else{
			// Registra nuevo tramite
			$idTramite = $tramite->Crea_Tramite($iduser, $idempresa, SOLICITUD_INVITACION_ETAPA_EN_APROBACION_POR_DIRECTOR, FLUJO_SOLICITUD_INVITACION, $motivo, $delegado);
			
			// Agregamos el nombre del usuario al campo de Observaciones
			if($observaciones_solicitud != ""){
				$HObser = "";
				$observaciones_solicitud = $notificacion->anotaObservacion($delegado, $HObser, $observaciones_solicitud, FLUJO_SOLICITUD_INVITACION, SOLICITUD_INVITACION_ETAPA_APROBACION);
			}else{
				$observaciones_solicitud = $HObser;
			}
			
			$invitacionID = $Solicitud_invitacion->Add_invitacion($motivo, $no_invitados, $monto_solicitud, $monto_pesos_solicitud, $divisa_solicitud_final, $cecos_solicitud, $idTramite, $ciudad_solicitud, $observaciones_solicitud, "", $fechainvit, $inv_lugar, $inv_hubo_exedente);

		}
		
		//Inserción del solicitante
		for ($i = 1; $i <= $_POST['rowCount']; $i++) {
			$sNombre = $_POST['nombre' . $i];
			$sPuesto = $_POST['puesto' . $i];
			$sTipo = $_POST['tipo' . $i];
			$sEmpresa = $_POST['empresa' . $i];
			$id_detalle_solicitud_invitacion=add_detalle_solicitud_invitacion($invitacionID, $sNombre, $sPuesto, $sEmpresa, $sTipo, $idTramite);
		}
		
		$tramite->Modifica_Etapa($idTramite, SOLICITUD_INVITACION_ETAPA_EN_APROBACION_POR_DIRECTOR, FLUJO_SOLICITUD_INVITACION, $iduser, "");
		
		$duenoActual = new Usuario();
		$duenoActual->Load_Usuario_By_ID($delegado);
		$nombreUsuario = $duenoActual->Get_dato('nombre');
		
		$mensaje = sprintf("La solicitud de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> en su nombre y requiere de su aprobaci&oacute;n.", $idTramite, $nombreUsuario);
		$tramite->EnviaNotificacion($idTramite, $mensaje, $delegado, $iduser, "1", ""); //"0" para no enviar email y "1" para enviarlo
		$tramite->actualizaDelegado($idTramite, $delegado);
		
		header("Location: ./index.php?docs=docs&type=2&oksave");
	}else{
		//echo "<meta http-equiv='location' content='0; url=http://".$SERVER.$RUTA_R."flujos/solicitudes/index.php?docs=docs&type=2&errsave'>";
		header("Location: ./index.php?docs=docs&type=2&errsave");
		//die();
	}//if row
}//if guarda comp

if (isset($_GET['new2'])) {
//*****************************//COMPROBACION GENERAL//*****************************//
	require_once("../../functions/RutaAutorizacion.php");
	require_once("$RUTA_A/functions/utils.php");
	
    $UsuOb = new Usuario();

    //$UsuOb->Load_Usuario_By_No_Empleado($_SESSION['empleado']);
    $UsuOb->Load_Usuario($_SESSION['empleado']);
    $idcentrocosto = $UsuOb->Get_dato('idcentrocosto');

    function forma_comprobacion() {
        ?>
        <!-- Inicia forma para comprobación -->
        <script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
        <script language="JavaScript" src="../../lib/js/jquery/jquery.date_input.js" type="text/javascript"></script>        
        <script language="JavaScript" src="../../lib/js/jquery/jquery.autocomplete.js" type="text/javascript"></script>
        <script language="JavaScript" src="../../lib/js/jquery/jquery.bgiframe.js" type="text/javascript"></script>
		<script language="JavaScript" type ="text/javascript" src="../../lib/js/jquery/jquery.jdpicker.js"></script>
		<script language="JavaScript" src="../../lib/js/formatNumber.js" type="text/javascript"></script>
        <link rel="stylesheet" href="../../css/jdpicker.css" type="text/css" media="screen" />  
        
        <script language="JavaScript" type="text/javascript">
            //variables
            var doc;
            doc = $(document);
            doc.ready(inicializarEventos);//cuando el documento esté listo
            function inicializarEventos(){
            	$(document).bind("contextmenu", function(e){ e.preventDefault(); });
            	montoMaximoComidas();
                //ajusta tabla
                //$("#solicitud_table").tablesorter({ 
                    //cabeceras deshabilitadas del ordenamiento
                //    headers: { 
                //        4: {sorter: false }, 
                //        7: {sorter: false },
                //        9: {sorter: false },
                //        11:{sorter: false }  
                //    } //headers
                //}); //tabla
                //<borrarPartida();
                //guardaComprobacion();

                $("#fecha_inv").jdPicker({
                    date_format:"dd/mm/YYYY", 
                    date_min:"<?PHP echo date("d/m/Y"); ?>",
                    month_names: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                    short_month_names: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
                    short_day_names: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sab"]
                });
				
				//Seleccionar el centro de costos del usuario actual
				//select idcentrocosto from empleado where idempleado = "110"
				
				var id_centro_de_costos = "<?PHP 
				$iduser = (isset($_SESSION["iddelegado"])) ? verificaSesion($_SESSION["idusuario"], $_SESSION["iddelegado"]) : $_SESSION["idusuario"];				
				$query = sprintf("SELECT cc_centrocostos FROM cat_cecos WHERE cc_id = (SELECT idcentrocosto FROM empleado WHERE idfwk_usuario = '%s')", $iduser);
				$var = mysql_query($query);
				$aux="";
				while ($arr = mysql_fetch_assoc($var)) {
					$aux.=$arr['cc_centrocostos'];
				}
				echo $aux;?>";
				seleccionar(id_centro_de_costos);

				$("input").bind("keydown", function(e){
					if(!isAlphaNumeric(e)) return false;
				});
				
            }//fin ready ó inicializarEventos

            function montoMaximoComidas(){
            	// Monto máximo de Comidas de Invitación
				$.ajax({
					type: "POST",
					url: "services/ajax_solicitudes.php",
					data: "montoMaximo=ok",
					dataType: "json",		
					success: function(json){
						$("#montoMaximo").val(parseFloat(json[0].montoCantidad));
						$("#montoMaximoDivisa").val(json[0].divisaMonto);
						$("#montoPolitica").html("<span class='style1'>* Monto m&aacute;ximo por persona: " + parseFloat(json[0].montoCantidad) + " " + json[0].divisaMonto + ".</span>");
					}
				});
            }

			//Seleccionar elemento del combo de ccentro_costos
			function seleccionar(elemento) {
			   var combo = document.detallesItinerarios.ccentro_costos;
			   var cantidad = combo.length;

			   for (var i = 0; i < cantidad; i++) {
				  var toks=combo[i].text.split(" ");
				  if (toks[0] == elemento) {
					 combo[i].selected = true;
					 break;
				  }
			   }
			}

			function verificar_tipo_invitado(){
				var esDirector = <?PHP 
				$idusuario = $_SESSION["idusuario"];
				$esDirector = 0;
				$rutaAutorizacion = new RutaAutorizacion();
				$esDirector = $rutaAutorizacion->nivelEmpleado($idusuario);
				echo $esDirector;?>;
				var directorGeneral = <?PHP echo DIRECTOR_GENERAL;?>;
            	if($("#tipo_invitado").val()=="-1"){
            		$("#empresa_invitado").val("");
            		$("#capaDirector").html("");
            		$("#empresa_invitado").attr("disabled", "disable");
                }
            	else{    
                if($("#tipo_invitado").val()=="BMW"){
                    $("#empresa_invitado").val("BMW DE MEXICO SA DE CV.");
                    $("#capaDirector").html("");
                    $("#empresa_invitado").attr("disabled", "disable");
                } else if ($("#tipo_invitado").val() == "Gobierno" && esDirector != directorGeneral){
                	$("#empresa_invitado").val("");
                    $("#empresa_invitado").removeAttr("disabled");
					$("#capaDirector").html("<strong>La solicitud requerir&aacute; ser validada por el Dir. General</strong>");                    
                }else{
                    $("#empresa_invitado").val("");
                    $("#capaDirector").html("");
                    $("#empresa_invitado").removeAttr("disabled");
                                                                                                            
                }
            	}
            }
                            
            function verificar(){
                if($("#motive").val()==""){
                    alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
                    $("#motive").focus();
                    return false;
                } else if($("#lugar_inv").val()==""){
                    alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
                    $("#lugar_inv").focus();
                    return false;
                } else if($("#nombre_invitado").val()==""){
                    alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
                    $("#nombre_invitado").focus();
                    return false;
                }else if($("#puesto_invitado").val()==""){
                    alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
                    $("#puesto_invitado").focus();
                    return false;
                }else if($("#empresa_invitado").val()==""){
                    alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
                    $("#empresa_invitado").focus();
                    return false;
                }else if($("#tipo_invitado").val()==""){
                    alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
                    $("#tipo_invitado").focus();
                    return false;
                }else{
                    return true;
                }
                                
            }
                            
                            
            function agregarPartida(){
                var frm=document.detallesItinerarios;
                                
                id=parseInt($("#invitado_table").find("tr:last").find("div").eq(0).html());
                                                                        
                if(verificar()){
                    
                    if(isNaN(id)){ 
                        id=1; 
                    }else{ 
                        id+=parseInt(1); 
                    }
                    frm.rowCount.value=parseInt(frm.rowCount.value)+parseInt(1);
                    
                    var nuevaFila='<tr>';
                    nuevaFila+="<td>"+"<div id='renglon"+id+"' name='renglon"+id+"'>"+id+"</div>"+"<input type='hidden' name='row"+id+"' id='row"+id+"' value='"+id+"' readonly='readonly' /></td>";
                    nuevaFila+="<td><input type='hidden' name='nombre"+id+"' id='nombre"+id+"' value='"+frm.nombre_invitado.value+"' readonly='readonly' />"+frm.nombre_invitado.value+"</td>"; 
                    nuevaFila+="<td><input type='hidden' name='puesto"+id+"' id='puesto"+id+"' value='"+frm.puesto_invitado.value+"' readonly='readonly' />"+frm.puesto_invitado.value+"</td>"; 
                    nuevaFila+="<td><input type='hidden' name='empresa"+id+"' id='empresa"+id+"' value='"+frm.empresa_invitado.value+"' readonly='readonly' />"+frm.empresa_invitado.value+"</td>"; 
                    nuevaFila+="<td><input type='hidden' name='tipo"+id+"' id='tipo"+id+"' value='"+frm.tipo_invitado.value+"' readonly='readonly' />"+frm.tipo_invitado.value+"</td>";
                    nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+id+"del' id='"+id+"del' onmousedown='borrarPartida(this.id);' style='cursor:pointer;' /></div><div align='center'>Eliminar Partida</div></td>";
                    nuevaFila+= '</tr>';
                                    
                    
                    $("#invitado_table").append(nuevaFila);
                    $("#numInvitados").val(parseInt(frm.rowCount.value));
                    $("#numInvitadosDisabled").val(parseInt(frm.rowCount.value));
                    $("#nombre_invitado").val("");
                    $("#puesto_invitado").val("");
                    $("#empresa_invitado").val("");
                    //$("#capaDirector").html("");                    
                    $("#tipo_invitado").val("-1");
                    //guardaComprobacion();
                    $("#guardarCompprev").removeAttr("disabled");
                    if($("#delegado").val() != 0){
                    	$("#enviaDirector").removeAttr("disabled");
                    }else{
                    	$("#guardarComp").removeAttr("disabled");
	                }
					$("#capaDirector").html("");
					document.getElementById("empresa_invitado").disabled="disable";
					recalculaMontos();
                }
            }

                                                                                     
function borrarPartida(id){
    var frm=document.detallesItinerarios;
    $("#rowCount").bind("restar",function(e,data,data1){
    	e.stopImmediatePropagation();
			$("#rowCount").val(parseInt($("#invitado_table>tbody >tr").length));
			$("#numInvitadosDisabled").val($("#rowCount").val());
			$("#numInvitados").val($("#rowCount").val());
        });

    $("#rowDel").bind("cambiar",function(e,inicio,tope){
        e.stopImmediatePropagation();
        var renglon="";
		var jqueryrenglon="";
		var nextrenglon="";
    	var nextrow="";
		var row="";
		var jqueryrow="";
    	var nextnombre="";
		var nombre="";
		var jquerynombre="";
    	var nextpuesto="";
		var puesto="";
		var jquerypuesto="";
    	var nexttipo="";
		var tipo="";
		var jquerytipo="";           				
    	var nextempresa="";
		var empresa="";
		var jqueryempresa="";
    	var nextdel="";
		var del="";
		var jquerydel="";
		
		 for (var i=parseFloat(inicio);i<=parseFloat(tope);i++){ 

 		   	renglon="renglon"+parseInt(i);
				jqueryrenglon="#renglon"+parseInt(i);
				nextrenglon="#renglon"+((parseInt(i)+(1)));
         	nextrow="#row"+((parseInt(i)+(1)));
				row="row"+parseInt(i);
				jqueryrow="#row"+parseInt(i);
         	nextnombre="#nombre"+((parseInt(i)+(1)));
				nombre="nombre"+parseInt(i);
				jquerynombre="#nombre"+parseInt(i);
         	nextpuesto="#puesto"+((parseInt(i)+(1)));
				puesto="puesto"+parseInt(i);
				jquerypuesto="#puesto"+parseInt(i);
         	nexttipo="#tipo"+((parseInt(i)+(1)));
				tipo="tipo"+parseInt(i);
				jquerytipo="#tipo"+parseInt(i);           				
         	nextempresa="#empresa"+((parseInt(i)+(1)));
				empresa="empresa"+parseInt(i);
				jqueryempresa="#empresa"+parseInt(i);
         	nextdel="#"+((parseInt(i)+(1)))+"del";
				del=parseInt(i)+"del";
				jquerydel="#"+parseInt(i)+"del";
				
				$(nextrenglon).attr("id",renglon);
	         	$(jqueryrenglon).attr("name",renglon);
	         	$(jqueryrenglon).html(parseInt(i));
	         	$(nextrow).attr("id",row);
	         	$(jqueryrow).attr("name",row);
	         	$(jqueryrow).val(parseInt(i));          
	    	  	$(nextnombre).attr("id",nombre);
      	 	$(jquerynombre).attr("name",nombre);  		        	 
	      		$(nextpuesto).attr("id",puesto);
  	      		$(jquerypuesto).attr("name",puesto);      
	      		$(nexttipo).attr("id",tipo);
  			$(jquerytipo).attr("name",tipo);
  			$(nextempresa).attr("id",empresa);
  			$(jqueryempresa).attr("name",empresa);
  			$(nextdel).attr("id",del);
  			$(jquerydel).attr("name",del); 
 		       //next();                                            
    }
    });
    
                $("img.elimina").click(function(){
                    
            		$(this).parent().parent().parent().fadeOut("normal", function () {
						var i=0;
        				$(this).remove();	
						$("#rowCount").trigger("restar");
						$("#rowCount").unbind("restar");
          				 var tope=$("#rowCount").val();
						 recalculaMontos();
          				 i=parseFloat(id);
						$("#rowDel").trigger("cambiar",[i,tope]);
						$("#rowDel").unbind("cambiar");
                    }); 
            		return false;    
                });    
                                                                                                                                                                                                 
            }

                                                                           
            function getCentroCostos(){
                var frm=document.detallesItinerarios;
                $("#Cecos").val(frm.ccentro_costos.value);
            }      

		/***************************************************************************/
		//VARIABLE GLOBAL
		var textoAnterior = '';

		//ESTA FUNCIÓN DEFINE LAS REGLAS DEL JUEGO
		function cumpleReglas(simpleTexto){
			//la pasamos por una poderosa expresión regular
			//var expresion = new RegExp("^(|([0-9]{1,2}(\\.([0-9]{1,2})?)?))$");
			var expresion = new RegExp("^(|([0-9]{1,30}(\\.([0-9]{1,2})?)?))$");

			//si pasa la prueba, es válida
			if(expresion.test(simpleTexto))
				return true;
			return false;
		}//end function checaReglas

		//ESTA FUNCIÓN REVISA QUE TODO LO QUE SE ESCRIBA ESTÉ EN ORDEN
		function revisaCadena(textItem){
			//si comienza con un punto, le agregamos un cero
			if(textItem.value.substring(0,1) == '.') 
				textItem.value = '0' + textItem.value;

			//si no cumples las reglas, no te dejo escribir
			if(!cumpleReglas(textItem.value))
				textItem.value = textoAnterior;
			else //todo en orden
				textoAnterior = textItem.value;
		}//end function revisaCadena
		/***************************************************************************/
			function NumCheck(e, field) { 
				key = e.keyCode ? e.keyCode : e.which ;
				valor = field.value+String.fromCharCode(key);
				// backspace 
				if (key == 8) return true ;
				// 0-9 
				if ((key > 47 && key < 58)) { 
					if (valor == "") return true ;
					//regexp = /.[0-9]{2}$/ 
					regexp = /^[0-9]*(\.[0-9]{0,2})?$/ ;
					return (regexp.test(valor)) ;
				} 
				// . 
				if (key == 46) { 
					if (valor == "") return false ;
					//regexp = /^[0-9]+$/ ;
					regexp = /^[0-9]*(\.[0-9]{0,2})?$/ ;
					return regexp.test(valor) ;
				} 
				// other key 
				return false ;
			}
            function validaNum(valor){                                                                
                cTecla=(document.all)?valor.keyCode:valor.which;
                if(cTecla==8) return true;
                if(cTecla==37) return true;
                if(cTecla==39) return true;
                patron=/^([0-9.]{1,2})?$/;
                cTecla= String.fromCharCode(cTecla);
                return patron.test(cTecla);
            }
            function validaNum2(valor){
                cTecla=(document.all)?valor.keyCode:valor.which;
                if(cTecla==8) return true;
                if(cTecla==37) return "ok";
                if(cTecla==39) return "ok";
                return true;
            }
			function format_input(valor){
				valor = valor.replace(/,/g,"");
				//valor = number_format(valor,2,".",",");
				valor = number_format_sin_redondeo(valor);
				return valor;
			}

			function validaInvitados(){
				var numInv = parseInt($("#numInvitados").val());
				if(numInv < 2){
					alert("Favor de ingresar por lo menos dos invitados.");
					return false;
				}else{
					return true;
				}
			}
                                                       
            function guardaComprobacion(){
                var frm=document.detallesItinerarios;
                if(validaInvitados()){
	                id= parseInt($("#solicitud_table").find("tr:last").find("td").eq(0).html());
	                if(($("#numInvitados").val()!= 1)){
	                	if($("#motive").val()==""){
	                        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
	                        $("#motive").focus();
	                        return false;
	                    }else if($("#lugar_inv").val()==""){
	                        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
	                        $("#lugar_inv").focus();
	                        return false;
	                    }else if($("#monto_solicitado_invitacion").val()==0){
	                        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
	                        $("#monto_solicitado_invitacion").focus();
	                        return false;
	                    }else if($("#divisa_solicitud_invitacion").val()=="" ||$("#divisa_solicitud_invitacion").val()==-1 ){
	                        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
	                        $("#divisa_solicitud_invitacion").focus();
	                        return false;
	                    }else if($("#ccentro_costos").val()==""){
	                        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
	                        $("#ccentro_costos").focus();
	                        return false;
	/*                    }else if($("#observ").val()==""){
	                        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
	                        $("#observ").focus();
	                        return false;*/
	                    }else if($("#ciudad_invitacion").val()==""){
	                        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
	                        $("#ciudad_invitacion").focus();
	                        return false;
	                    } else if ($("#banderavalida").val() == 1){
	                    	if($("#observ").val().length == 0 ){
	                            alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
	                            $("#observ").focus();
	                            return false;
	                        }
	                    } else {
	                    	document.getElementById("motive").setAttribute("readonly","readonly");
	                    	document.getElementById("nombre_invitado").setAttribute("disabled","disabled");
	                    	document.getElementById("puesto_invitado").setAttribute("disabled","disabled");
	                    	document.getElementById("empresa_invitado").setAttribute("disabled","disabled");                    	
	                    	document.getElementById("tipo_invitado").setAttribute("disabled","disabled");						
							document.getElementById("agregar_invitado").setAttribute("disabled","disabled");
	                        $("#guardarCompprev").removeAttr("disabled");
	                        if($("#delegado").val() != 0){
	                        	$("#enviaDirector").removeAttr("disabled");
		                    }else{
		                    	$("#guardarComp").removeAttr("disabled");
			                }
	                    }
	                                            
	                }
                }else{
                    return false;
				}
            }


            function guardaprevioComprobacion(){
                var frm=document.detallesItinerarios;
				//document.getElementById("motive").setAttribute("readonly","readonly");
                if(document.getElementById("motive").value != ""){
                    $("#guardarprevComp").removeAttr("disabled");                    
				}else if(document.getElementById("motive").value == ""){
					frm.guardarprevComp.disabled = true;
				}
            }

            function solicitarConfirmPrevio(){
            	 var frm=document.detallesItinerarios;
        		if(confirm("¿Desea guardar esta Solicitud como previo?")){
        			//frm.submit();
        		}else{
            		return false;
        		}
                }

            function enviarSub(){
                var frm=document.detallesItinerarios;
                document.getElementById("motive").setAttribute("readonly","readonly");                       
            }

            function evaluaMonto(monto){
            	var montoMaximo = 0;
				var montoMaximoDiv = 0;
				
            	var esDirector = <?PHP 
					$idusuario = $_SESSION["idusuario"];
					$esDirector = 0;
					$rutaAutorizacion = new RutaAutorizacion();
					$esDirector = $rutaAutorizacion->nivelEmpleado($idusuario);
					echo $esDirector;?>;
				var directorGeneral = <?PHP echo DIRECTOR_GENERAL;?>;
				
            	$("#capaWarning").html("");
                var mensajeExcedePoliticas = undefined;
                //Variables de las cajas de Texto 
                var divEuro = parseFloat($("#valorDivisaEUR").val());
                //var monto = parseFloat($("#tpesos").val());
                var num_invitados = parseFloat($("#numInvitados").val());
                //Variable para guardar 
                var monto2 = 0;
                monto2 = ((monto / divEuro) / num_invitados );
                
             	// Monto máximo de Comidas de Invitación
				$.ajax({
					type: "POST",
					url: "services/ajax_solicitudes.php",
					data: "montoMaximo=ok",
					dataType: "json",
					async: false, 
					timeout: 10000, 
					success: function(json){
						montoMaximo = parseFloat(json[0].montoCantidad);
						montoMaximoDiv = json[0].divisaMonto;
					}, 
					complete: function (json){
						//alert("Completo: carga de politicas de comida Invitacion:"+montoMaximo+" "+montoMaximoDiv);
						if(monto2 > montoMaximo && mensajeExcedePoliticas == undefined){
		                    if(esDirector != directorGeneral){
		                		mensajeExcedePoliticas = "<strong>Esta rebasando la pol&iacute;tica del concepto. <br>El monto m&aacute;ximo es de " + montoMaximo + " " + montoMaximoDiv + ".<br /> La solicitud requerir&aacute; ser validada por el Dir. General.</strong>";
		                    }else{
		                    	mensajeExcedePoliticas = "<strong>Esta rebasando la pol&iacute;tica del concepto. <br>El monto m&aacute;ximo es de " + montoMaximo + " " + montoMaximoDiv + ".<br /></strong>";
		                    }
		                	conceptoExcedePoliticas = true;                                        
		                } else {
		                	conceptoExcedePoliticas = false;
		                }
		                
		                if(conceptoExcedePoliticas){
		                    $("#capaWarning").html(mensajeExcedePoliticas);
		                    $("#obsjus").html("Agregar justificaci&oacute;n detallada del motivo del excedente<span class='style1'>*</span>:");
		                    document.getElementById("banderavalida").value = 1;                   
		                } else {
		                	$("#obsjus").html("Observaciones:");
		                	document.getElementById("banderavalida").value = 0;
		                }
					},
					error: function(x, t, m) {
						if(t==="timeout") {
							location.reload();
							abort();
						}else if(montoMaximo == 0 || montoMaximo == ""){
							location.reload();
							abort();
						}
					}
				});
            }
            
            function recalculaMontos(){
                var anticipo = parseFloat(($("#monto_solicitado_invitacion").val()).replace(/,/g,""));
				
                var totalAnticipo = 0;
				var divisas = $("#divisa_solicitud_invitacion").val();

				var tasaNueva = 1;
				if(divisas != 1){ //Si la divisa es diferente a MXN
					//Se obtiene las tasas de las divisas
					var tasa = "<?PHP 
					$query = sprintf('SELECT DIV_ID,DIV_TASA FROM divisa');
					$var = mysql_query($query);
					$aux="";
					while ($arr = mysql_fetch_assoc($var)) {
						$aux.=$arr['DIV_ID'].":".$arr['DIV_TASA'].":";
					}
					echo $aux;?>";
					var tasa2 = tasa.split(":");
					
					//Se obtiene la tasa de la divisa seleccionada
					for(i=0;i<=tasa2.length;i=i+2){
						if(tasa2[i] == divisas){
							tasaNueva = tasa2[i+1];
						}
					}
				}

				totalAnticipo = anticipo * parseFloat(tasaNueva);
				document.getElementById("tpesosdisabled").value = number_format(redondea(totalAnticipo),2,".",",");//redondea a 2 decimales
				document.getElementById("tpesos").value = number_format(redondea(totalAnticipo),2,".",",");//redondea a 2 decimales
                evaluaMonto(totalAnticipo);
                	
            }
			
			function redondea(valor){
				return (Math.round(valor * Math.pow(10, 2)) / Math.pow(10, 2));
			}
			            
        </script>

        <link rel="stylesheet" type="text/css" href="../../css/jquery.autocomplete.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/date_input.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/table_style.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/style_Table_Edit.css"/>
        <style type="text/css">
            .style1 {color: #FF0000}

        </style>


        <div id="Layer1" >
            <form action="solicitud_invitacion_new.php?save" method="post" name="detallesItinerarios" id="detallesItinerarios" >
                <center><h3>Solicitud de Invitaci&oacute;n</h3></center>
                <table width="785" border="0" align="center" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left;">
                    <tr>
                    <td colspan="9">&nbsp;</td>
                    </tr>
                    <tr style="text-align:center;">
                        <td colspan="9">Motivo<span class="style1">*</span>: <input name="motive" type="text" id="motive" size=50 maxlength="100" onchange="return guardaprevioComprobacion();" onclick="return guardaprevioComprobacion(); " onkeyup="return guardaprevioComprobacion();" />
                        </td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                    	<td colspan="2" align="right">Fecha de Invitaci&oacute;n<span class="style1">*</span>:</td>
                    	<td align="left"><input name="fecha_inv" id="fecha_inv" value="<?PHP echo date('d/m/Y'); ?>" size="12" readonly="readonly"></td>
						<td>&nbsp;</td>
						<td colspan="2" align="right">Lugar de invitaci&oacute;n/Restaurante<span class="style1">*</span>:</td>
						<td align="left"><input type="text" name="lugar_inv" id="lugar_inv" maxlength="100"/></td>
						<td>&nbsp;</td>
					</tr>
                    <tr>
                    	<td colspan="9"><div>&nbsp;</div></td>
                    </tr>
                </table>
                <br/>
                <center><div id="montoPolitica"></div></center>
                <center><div style="display: none"><span class="style1">* Monto m&aacute;ximo por persona Funcionarios Gubernamentales: 30.00 EUR</span></div></center>
                <br/>
                <center>
                    <table width="785" border="0" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8" >  

                        <tr style="text-align:center;" >
                            <td colspan="4"><h3>Invitados</h3></td>
                        </tr>
                        <tr>  
                            <td>&nbsp;</td>
                            <td width="50%">Nombre<span class="style1">*</span>:&nbsp;&nbsp;<input name="nombre_invitado" type="text" id="nombre_invitado" size=50 maxlength="100" />
                            </td>
                            <td width="50%">Tipo de Invitado<span class="style1">*</span>:&nbsp;&nbsp;<select name="tipo_invitado" id="tipo_invitado" onchange="verificar_tipo_invitado();">
                                    <option value="-1">Seleccione...</option>
                                    <option value="BMW">Empleado BMW de M&eacute;xico</option>
                                    <option value="Externo">Externo</option>
                                    <option value="Gobierno">Gobierno</option>
                                </select>

                            </td>
                            <td>&nbsp;</td>
                        </tr> 
                        <tr>
                            <td>&nbsp;</td>
                            <td width="50%">Puesto<span class="style1">*</span>:&nbsp;&nbsp;&nbsp;<input name="puesto_invitado" type="text" id="puesto_invitado" size=50 maxlength="100" />
                            </td>
                            <td width="50%">Empresa<span class="style1">*</span>:&nbsp;&nbsp;<input name="empresa_invitado" type="text" id="empresa_invitado" size=50 maxlength="100" disabled="disable" />
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><div id="capaDirector"></div></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td colspan="2">
                                <div align="center">
                                    <input name="agregar_invitado" type="button" id="agregar_invitado" value="     Agregar Invitado"  onclick="agregarPartida();" style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>
                                </div>
                            </td>
                            <td>&nbsp;</td>
                        </tr>

                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr><td>&nbsp;</td><td colspan="2" style="text-align:center">
                                <table id="invitado_table" class="tablesorter" cellspacing="1"> 
                                    <thead> 
                                        <tr> 
                                            <th width="5%" align="center" valign="middle">No.</th>
                                            <th width="30%" align="center" valign="middle">Nombre</th> 
                                            <th width="30%" align="center" valign="middle">Puesto</th>
                                            <th width="30%" align="center" valign="middle">Empresa</th>
                                            <th width="30%" align="center" valign="middle">Tipo</th>
                                            <th width="5%" align="center" valign="middle">Eliminar</th>
                                        </tr>
                                       
                                    </thead> 
                                    <tbody> 
                                     <tr>	
                                     		<?php 
											// Obtener el puesto del empleado
                                            //$idempleado = $_SESSION["idusuario"];
											$idempleado = (isset($_SESSION["iddelegado"])) ? verificaSesion($_SESSION["idusuario"], $_SESSION["iddelegado"]) : $_SESSION["idusuario"];
                                     		error_log("Empleado retornado: ". $idempleado);
                                     		$usuarioActivo = new Usuario();
                                     		$usuarioActivo->Load_Usuario_By_ID($idempleado);
                                     		$nombreUsuario = $usuarioActivo->Get_dato("u_paterno")." ".$usuarioActivo->Get_dato("u_materno")." ".$usuarioActivo->Get_dato("u_nombre");
                                     		$id_empresa	= $usuarioActivo->Get_dato("u_empresa");
                                            ?>
                                            <td><div id='renglon1' name='renglon1'>1</div><input type="hidden" name="row1" id="row1" value="1" readonly='readonly'/></td>
                                            <td><input type="hidden" name="nombre1" id="nombre1" value="<?PHP echo $nombreUsuario; ?>" /><?PHP echo $nombreUsuario;?></td> 
                                            <td><?PHP                                                                                         
                                            $cnn = new conexion();
                                            $query = sprintf("SELECT npuesto FROM empleado WHERE idfwk_usuario='%s'", $idempleado);
                                            $rst = $cnn->consultar($query);
                                            $fila = mysql_fetch_assoc($rst);                                            
                                            echo $fila['npuesto'];?><input type="hidden" name="puesto1" id="puesto1" value="<?PHP echo $fila['npuesto']; ?>" /></td>
                                            <td aling="center"><?PHP 
                                            // Obtener el nombre de la empresa
                                            $cnn = new conexion();
                                            $query2 = sprintf("SELECT e_nombre FROM empresas WHERE e_id='%s'", $id_empresa);
                                            $rst2 = $cnn->consultar($query2);
                                            $filab = mysql_fetch_assoc($rst2);                                            
                                            echo $filab['e_nombre'];?><input type="hidden" name="empresa1" id="empresa1" value="<?PHP echo $filab['e_nombre'];?>" /></td>
                                            <td aling="center">BMW<input type="hidden" name="tipo1" id="tipo1" value="BMW" /></td>
                                            <td><div align='center'><img id="1del" class="elimina" style="cursor:pointer;" onmousedown="borrarPartida(this.id);" name="1del" alt="Click aquí para Eliminar" src="../../images/delete.gif"></div><div align="center">Eliminar Partida</div></td>
                                        </tr> 
                                        <!-- cuerpo tabla-->
                                    </tbody> 
                                </table> 

                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td> 
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>

                            <td colspan="2">N&uacute;mero de invitados<span class="style1">*</span>:&nbsp;
                                <input type="text" name="numInvitadosDisabled" id="numInvitadosDisabled" value="1" size="15" disabled="disabled" />
                                <input type="hidden" name="numInvitados" id="numInvitados" value="1" size="15" readonly="readonly" /></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>

                    </table>

                    <br/>
                    <br/>
                    <table width="785" border="0" align="center" cellspacing="1">
                        <tr>
                            <td width="3%">&nbsp;</td>
                            <td width="15%">&nbsp;</td>
                            <td width="24%">&nbsp;</td>
                            <td width="23%">&nbsp;</td>
                            <td width="34%">&nbsp;</td>
                            <td width="1%">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td colspan="3">Total Monto solicitado<span class="style1">*</span>:&nbsp;
                                <!--<input name="monto_solicitado_invitacion" type="text" id="monto_solicitado_invitacion" maxlength="100" value="0.00" onkeydown="aux2 = validaNum2(event); return validaNum(event);" onkeypress="" onkeyup="recalculaMontos(); if(aux2 != 'ok'){this.value = format_input(this.value);}" onchange="recalculaMontos();"/>-->
                                <!--<input name="monto_solicitado_invitacion" type="text" id="monto_solicitado_invitacion" maxlength="100" value="0" onkeydown="/*aux2 = validaNum2(event); return validaNum(event);*/" onkeypress="return NumCheck(event, this);" onkeyup="recalculaMontos(); /*if(aux2 != 'ok'){this.value = format_input(this.value);}*/" onchange="recalculaMontos();"/>-->
                                <input name="monto_solicitado_invitacion" type="text" id="monto_solicitado_invitacion" maxlength="100" value="0" onkeydown="" onkeypress="" onkeyup="revisaCadena(this); recalculaMontos();" onchange="recalculaMontos();"/>
                            </td>
                            <td>Divisa<span class="style1">*</span>:&nbsp;<select name="divisa_solicitud_invitacion" id="divisa_solicitud_invitacion" onchange="recalculaMontos();">
                                    <option value="-1">Seleccione...</option>
                                    <option value="1">MXN</option>
                                    <option value="2">USD</option>
                                    <option value="3">EUR</option>
                                </select></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td colspan="3">Total en Pesos:&nbsp;<input type="text" name="tpesosdisabled" id="tpesosdisabled" value="0.00" size="15" disabled="disabled" />
                            <input type="hidden" name="tpesos" id="tpesos" value="0.00" size="15" readonly="readonly" /> MXN<div id="capaWarning"></div></td>
                            <td>Ciudad<span class="style1">*</span>:&nbsp;<input name="ciudad_invitacion" type="text" id="ciudad_invitacion" maxlength="100" /></td>
                            <td><input type="hidden" name="banderavalida" id="banderavalida" readonly="readonly" /></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td colspan="3">Centro de Costos<span class="style1">*</span>:&nbsp;
                                <select name="ccentro_costos" id="ccentro_costos" onchange="getCentroCostos();">
                                    <option id="-1" value="-1">Seleccione...</option>
									<?PHP 
									$query = sprintf("SELECT cc_id,cc_centrocostos,cc_nombre FROM cat_cecos WHERE cc_estatus = '1' AND cc_empresa_id = '" . $_SESSION["empresa"] . "' order by cc_centrocostos");
									$var = mysql_query($query);
									while ($arr = mysql_fetch_assoc($var)) {
										echo sprintf("<option value='%s'>%s - %s</option>", $arr['cc_id'], $arr['cc_centrocostos'], $arr['cc_nombre']);
									}
									?>                    
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td align="right" valign="top"><div id="obsjus">Observaciones:</div></td>
                            <td colspan="3" ><div id="areatext"><textarea name="observ" cols="80" rows="5" id="observ"></textarea></div></td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                </center>
                <br />
                <div align="center"></div>



            <?PHP }
            forma_comprobacion();
            ?>
            <br />
            <div align="center">
                <input type="submit" id="guardarprevComp" name="guardarprevComp" value="     Guardar Previo"  onclick="return solicitarConfirmPrevio();" style="background:url(../../images/save.gif); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" readonly="readonly" disabled="disabled"/>
                &nbsp;&nbsp;&nbsp;
                <?php if(isset($_SESSION['iddelegado'])){ ?>
                	<input type="submit" id="enviaDirector" name="enviaDirector" value="     Enviar a Director" onclick="getCentroCostos();return guardaComprobacion();enviarSub();" style="background:url(../../images/save.gif); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" readonly="readonly" disabled="disabled"/>
                	&nbsp;&nbsp;&nbsp;
               	<?php }else{ ?>
                	<input type="submit" id="guardarComp" name="guardarComp" value="     Enviar Solicitud"  onclick="getCentroCostos();return guardaComprobacion();enviarSub();" style="background:url(../../images/save.gif); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" readonly="readonly" disabled="disabled"/>
                <?php } ?>
            </div>
            <?PHP 
            $_divisas = new Divisa();
            $_divisas->Load_data(3); //busca Id de divisa
            $divisaEUR = $_divisas->Get_dato("div_tasa");
            $_divisas1 = new Divisa();
            $_divisas1->Load_data(2);
            $divisaUSD = $_divisas1->Get_dato("div_tasa");
            ?>
            <input type="hidden" name="idusuario" id="idusuario" value="<?PHP echo $_SESSION["idusuario"]; ?>" readonly="readonly" />
            <input type="hidden" name="empleado" id="empleado" value="<?PHP echo $_SESSION["idusuario"]; ?>" readonly="readonly" />
            <input type="hidden" name="empresa" id="empresa" value="<?PHP echo $_SESSION["empresa"]; ?>" readonly="readonly" />
            <input type="hidden" name="guarda" id="guarda" value="" readonly="readonly" />
            <input type="hidden" id="rowCount" name="rowCount" value="1" readonly="readonly"/>
            <input type="hidden" id="rowDel" name="rowDel" value="0" readonly="readonly"/>
            <input type="hidden" id="Cecos" name="Cecos" value="<?PHP echo $idcentrocosto; ?>" readonly="readonly"/>
            <input type='hidden' id='valorDivisaEUR' name='valorDivisaEUR' value="<?PHP echo $divisaEUR; ?>">
            <input type='hidden' id='valorDivisaUSD' name='valorDivisaUSD' value="<?PHP echo $divisaUSD; ?>">
            <input type="hidden" name="etapa" id="etapa" value="1" />
            <input type="hidden" name="montoMaximo" id="montoMaximo" value="0" />
            <input type="hidden" name="montoMaximoDivisa" id="montoMaximoDivisa" value="" />
            <input type="hidden" name="delegado" id="delegado" readonly="readonly" value="<?php if(isset($_SESSION['iddelegado'])){ echo $_SESSION['iddelegado']; }else{echo 0;}?>" />
            <input type="hidden" name="tramiteID" id="tramiteID" readonly="readonly" value="0" />
            </center>
        </form>


        <?PHP 
    }

    
    if (isset($_GET['edit2'])) {
//*****************************//COMPROBACION GENERAL//*****************************//

    $UsuOb = new Usuario();

    //$UsuOb->Load_Usuario_By_No_Empleado($_SESSION['empleado']);
    $UsuOb->Load_Usuario($_SESSION['empleado']);
    $idcentrocosto = $UsuOb->Get_dato('idcentrocosto');

    function forma_comprobacion() {
        ?>
        <!-- Inicia forma para comprobación -->
        <script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
        <script language="JavaScript" src="../../lib/js/jquery/jquery.date_input.js" type="text/javascript"></script>       
        <script language="JavaScript" src="../../lib/js/jquery/jquery.autocomplete.js" type="text/javascript"></script>
        <script language="JavaScript" src="../../lib/js/jquery/jquery.bgiframe.js" type="text/javascript"></script>
        <script language="JavaScript" src="../../lib/js/jquery/jquery.blockUI.js" type="text/javascript"></script>
		<script language="JavaScript" type ="text/javascript" src="../../lib/js/jquery/jquery.jdpicker.js"></script>
		<script language="JavaScript" src="../../lib/js/formatNumber.js" type="text/javascript"></script>
		<script language="JavaScript" src="../comprobaciones/js/backspaceGeneral.js" type="text/javascript"></script>
        <link rel="stylesheet" href="../../css/jdpicker.css" type="text/css" media="screen" />  



        <script language="JavaScript" type="text/javascript">
            //variables
            var doc;
            doc = $(document);
            doc.ready(inicializarEventos);//cuando el documento esté listo
            function inicializarEventos(){
            	$.blockUI({
    				message: '<h1>Espere un momento...</h1>',
    				css:{
    					border: 'none', 
    					padding: '15px', 
    					backgroundColor: '#000', 
    					'-webkit-border-radius': '10px', 
    					'-moz-border-radius': '10px', 
    					opacity: .5, 
    					color: '#fff'
    				}
    			});
    			
            	$(document).bind("contextmenu", function(e){ e.preventDefault(); });
				var tramite_id=gup("edit2");
				montoMaximoComidas();
				activa_envio();
				//alert(tramite_id);
				fillform(tramite_id);    
				
                //ajusta tabla
                //$("#solicitud_table").tablesorter({ 
                    //cabeceras deshabilitadas del ordenamiento
                //    headers: { 
                //        4: {sorter: false }, 
                //        7: {sorter: false },
                //        9: {sorter: false },
                //        11:{sorter: false }  
                //    } //headers
                //}); //tabla
                //borrarPartida();
                //guardaComprobacion();
                
                $("#fecha_inv").jdPicker({
                    date_format:"dd/mm/YYYY", 
                    date_min:"<?PHP echo date("d/m/Y"); ?>",
                    month_names: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                    short_month_names: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
                    short_day_names: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sab"]
                });
				
				//Seleccionar el centro de costos del usuario actual
				//select idcentrocosto from empleado where idempleado = "110"
				
                var id_centro_de_costos = "<?PHP 
    			$varDelegado = 0;
				if(isset($_SESSION['iddelegado'])){
					$varDelegado = $_SESSION['iddelegado'];
				}
				$iduser = verificaSesion($_SESSION["idusuario"], $varDelegado); 
				
    			$query = sprintf("SELECT cc_centrocostos FROM cat_cecos WHERE cc_id = (SELECT idcentrocosto FROM empleado WHERE idfwk_usuario = '%s')", $iduser);
    			$var = mysql_query($query);
    			$aux="";
    			while ($arr = mysql_fetch_assoc($var)) {
    				$aux.=$arr['cc_centrocostos'];
    			}
    			echo $aux;?>";
    			seleccionar(id_centro_de_costos);

    			$('#fecha_inv').keydown(function(e){					
					ignoraEventKey(e);				
				});

    			$("input").bind("keydown", function(e){
    				if(!isAlphaNumeric(e)) return false;
    			});
    			
            }//fin ready ó inicializarEventos
            ////IVA
            function gup(name){
            	var regexS = "[\\?&]"+name+"=([^&#]*)";
            	var regex = new RegExp ( regexS );
            	var tmpURL = window.location.href;
            	var results = regex.exec( tmpURL );
            	if( results == null )
            		return"";
            	else
            		return results[1];
            }

            function montoMaximoComidas(){
            	// Monto máximo de Comidas de Invitación
				$.ajax({
					type: "POST",
					url: "services/ajax_solicitudes.php",
					data: "montoMaximo=ok",
					dataType: "json",
					timeout: 10000,		
					success: function(json){
						$("#montoMaximo").val(parseFloat(json[0].montoCantidad));
						$("#montoMaximoDivisa").val(json[0].divisaMonto);
						$("#montoPolitica").html("<span class='style1'>* Monto m&aacute;ximo por persona: " + parseFloat(json[0].montoCantidad) + " " + json[0].divisaMonto + ".</span>");
					}
				});
            }

			function fillform(id_solicitud){
				var frm=document.detallesItinerarios;
				var etapa = 0;
				var historial_autorizaciones = "";
				
					if(id_solicitud != ""){
						$.ajax({
							type: "POST",
							url: "services/ajax_solicitudes.php",
							data: "mntsolicinv="+id_solicitud,
							dataType: "json",
							timeout: 10000,
							success: function(json){
								etapa = json[0].t_etapa;
								historial_autorizaciones = json[0].si_autorizaciones;
								
								$("#motive").val(json[0].si_motivo);
								$("#tramite_sol").val(parseInt(id_solicitud));
								
								$("#fecha_inv").val(json[0].si_fecha_invitacion);

								$("#lugar_inv").val(json[0].si_lugar);
								
								$("#tpesosdisabled").val(json[0].montoPesos);
								$("#tpesos").val(json[0].si_monto_pesos);

								$("#ciudad_invitacion").val(json[0].si_ciudad);

								$("#monto_solicitado_invitacion").val(json[0].si_monto);								

								$("#divisa_solicitud_invitacion").val(parseInt(json[0].si_divisa));

								if(etapa == 4 || etapa == 6 || historial_autorizaciones != ""){
									$("#historial_observaciones").val(json[0].si_observaciones);
								}
								
								$("#observ").val(json[0].si_observaciones_edicion);
								
							}, // Fin de carga de info de Solicitudes de Invitación
							complete: function (json){
								//alert("Completado 001");
								$.ajax({
									type: "POST",
									url: "../comprobaciones/services/Ajax_comprobacion.php",
									data: "t_id="+id_solicitud,
									dataType: "json",
									timeout: 10000,
									success: function(jsonC){
										if(jsonC==null){
										}else{
											seleccionar(jsonC[0]);
										}								
									}, // Fin de Seleccione CECOs
									complete: function (jsonC){
										//alert("Completado 002");
										$.ajax({
											type: "POST",
											url: "../comprobaciones/services/Ajax_comprobacion.php",
											data: "t_id2="+id_solicitud,
											dataType: "json",
											timeout: 10000,
											success: function(jsonINV){			
												if(jsonINV==null){
													VaciarTabla();
													document.getElementById("numInvitadosDisabled").value = 0;
													document.getElementById("numInvitados").value = 0;
												}else{
													VaciarTabla();									
													LlenarTabla(jsonINV,document.getElementById("invitado_table"));
												}
											},
											complete: function(jsonINV){
												//alert("Completado 003");
												guardaprevioComprobacion1();
												recalculaMontos();
												$.unblockUI();
												guardaComprobacion11();
										    }, // Complete 3er AJAX
										    error: function(x, t, m) {
        										if(t==="timeout") {
            										//alert("tiempo de espera agotado 3");
            										location.reload();
           											 abort();
        										} 
   											 }
										}); // Tercer AJAX - Carga de Invitados
									}, // Complete 2do AJAX
									error: function(x, t, m) {
        								if(t==="timeout") {
            								//alert("tiempo de espera agotado 2");
            								location.reload();
            								abort();
        								}
    								}
								}); // Segundo AJAX - Carga de CECO
							}, // Complete 1er AJAX
							error: function(x, t, m) {
        						if(t==="timeout") {
            						//alert("tiempo de espera agotado 1");
            						location.reload();
            						abort();
        						} 
    						}
						}); // Primer AJAX - INFO SI
					}					
				}

	function activa_envio(){
		var delegado = <?php if(isset($_SESSION['iddelegado'])){ echo $_SESSION['iddelegado']; }else{ echo 0;}?>;
        if(delegado != 0){
        	$("#enviaDirector").removeAttr("disabled");
        }else{
        	$("#guardarCompedit").removeAttr("disabled");
        }
	}

    function VaciarTabla() {
    	var TABLE = document.getElementById("invitado_table");
    	for(var i=TABLE.rows.length-1;i>=1;i--){
    		TABLE.deleteRow(i);
    	}
    }


  function LlenarTabla(json, tabla){	
    	var frm=document.detallesItinerarios;
    	frm.rowCount.value=parseInt(0);
    	for(var i=0;i<json.length;i++){
    					
    		var toks=json[i].split(":");
    		
    		//Creamos la nueva fila y sus respectivas columnas
    		var nuevaFila='<tr>';
    		nuevaFila+="<td>"+"<div id='renglon"+(i+1)+"' name='renglon"+(i+1)+"'>"+(i+1)+"</div>"+"<input type='hidden' name='row"+(i+1)+"' id='row"+(i+1)+"' value='"+(i+1)+"' readonly='readonly' /></td>";
    		nuevaFila+="<td><input type='hidden' name='nombre"+(i+1)+"' id='nombre"+(i+1)+"' value='"+toks[0]+"' readonly='readonly' />"+toks[0]+"</td>";
    		nuevaFila+="<td><input type='hidden' name='puesto"+(i+1)+"' id='puesto"+(i+1)+"' value='"+toks[1]+"' readonly='readonly' />"+toks[1]+"</td>";
    		nuevaFila+="<td><input type='hidden' name='empresa"+(i+1)+"' id='empresa"+(i+1)+"' value='"+toks[2]+"' readonly='readonly' />"+toks[2]+"</td>";
    		nuevaFila+="<td ><input type='hidden' name='tipo"+(i+1)+"' id='tipo"+(i+1)+"' value='"+toks[3]+"' readonly='readonly' />"+toks[3]+"</td>";
    		nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+(i+1)+"del' id='"+(i+1)+"del' onmousedown='borrarPartida(this.id);' style='cursor:pointer;' /></div><div align='center'>Eliminar Partida</div></td>";
    		nuevaFila+= '</tr>';
    		frm.rowCount.value=parseInt(frm.rowCount.value)+parseInt(1);
    		$("#invitado_table").append(nuevaFila);
    	}
    	document.getElementById("numInvitados").value = parseInt(frm.rowCount.value);
    	document.getElementById("numInvitadosDisabled").value = parseInt(frm.rowCount.value);
  }
  
  function fecha_to_mysql_normal(strFecha){
		var toks1=strFecha.split(" ");
		var toks=toks1[0].split("-");

		strFechaN = toks[0]+"/"+toks[1]+"/"+toks[2];
		return strFechaN;
	}

  function fecha_to_mysql(strFecha){
		var toks1=strFecha.split(" ");
		var toks=toks1[0].split("-");

		strFechaN = toks[2]+"/"+toks[1]+"/"+toks[0];
		return strFechaN;
	}

//Seleccionar elemento de un combo
  function seleccionar(elemento) {
     var combo = document.invitacion_comp.centro_de_costos;
     var cantidad = combo.length;
     for (var i = 1; i < cantidad; i++) {
        var toks=combo[i].text.split("-");
        if (toks[0] == elemento) {
           combo[i].selected = true;
  		 break;
        }
     }
  }
			//Seleccionar elemento del combo de ccentro_costos
			function seleccionar(elemento) {
			   var combo = document.detallesItinerarios.ccentro_costos;
			   var cantidad = combo.length;

			   for (var i = 0; i < cantidad; i++) {
				  var toks=combo[i].text.split(" ");
				  if (toks[0] == elemento) {
					 combo[i].selected = true;
					 break;
				  }
			   }
			}

			function verificar_tipo_invitado(){
				var esDirector = <?PHP 
				$idusuario = $_SESSION["idusuario"];
				$esDirector = 0;
				$rutaAutorizacion = new RutaAutorizacion();
				$esDirector = $rutaAutorizacion->nivelEmpleado($idusuario);
				echo $esDirector;?>;
				var directorGeneral = <?PHP echo DIRECTOR_GENERAL;?>;
            	if($("#tipo_invitado").val()=="-1"){
            		$("#empresa_invitado").val("");
            		$("#capaDirector").html("");
            		$("#empresa_invitado").attr("disabled", "disable");
                }
            	else{    
                if($("#tipo_invitado").val()=="BMW"){
                    $("#empresa_invitado").val("BMW DE MEXICO SA DE CV.");
                    $("#capaDirector").html("");
                    $("#empresa_invitado").attr("disabled", "disable");
                } else if ($("#tipo_invitado").val() == "Gobierno" && esDirector != directorGeneral){
                	$("#empresa_invitado").val("");
                    $("#empresa_invitado").removeAttr("disabled");
					$("#capaDirector").html("<strong>La solicitud requerir&aacute; ser validada por el Dir. General</strong>");                    
                }else{
                    $("#empresa_invitado").val("");
                    $("#capaDirector").html("");
                    $("#empresa_invitado").removeAttr("disabled");
                                                                                                            
                }
            	}
            }
                            
            function verificar(){
                if($("#motive").val()==""){
                    alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
                    $("#motive").focus();
                    return false;
                } else if($("#lugar_inv").val()==""){
                    alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
                    $("#lugar_inv").focus();
                    return false;
                } else if($("#nombre_invitado").val()==""){
                    alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
                    $("#nombre_invitado").focus();
                    return false;
                }else if($("#puesto_invitado").val()==""){
                    alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
                    $("#puesto_invitado").focus();
                    return false;
                }else if($("#empresa_invitado").val()==""){
                    alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
                    $("#empresa_invitado").focus();
                    return false;
                }else if($("#tipo_invitado").val()==""){
                    alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
                    $("#tipo_invitado").focus();
                    return false;
                }else{
                    return true;
                }
                                
            }
                            
                            
            function agregarPartida(){
                var frm=document.detallesItinerarios;
                                
                id=parseInt($("#invitado_table").find("tr:last").find("div").eq(0).html());
                                                                        
                if(verificar()){
                    
                    if(isNaN(id)){ 
                        id=1; 
                    }else{ 
                        id+=parseInt(1); 
                    }
                    frm.rowCount.value=parseInt(frm.rowCount.value)+parseInt(1);
                    
                    var nuevaFila='<tr>';
                    nuevaFila+="<td>"+"<div id='renglon"+id+"' name='renglon"+id+"'>"+id+"</div>"+"<input type='hidden' name='row"+id+"' id='row"+id+"' value='"+id+"' readonly='readonly' /></td>";                    nuevaFila+="<td><input type='hidden' name='nombre"+id+"' id='nombre"+id+"' value='"+frm.nombre_invitado.value+"' readonly='readonly' />"+frm.nombre_invitado.value+"</td>"; 
                    nuevaFila+="<td><input type='hidden' name='puesto"+id+"' id='puesto"+id+"' value='"+frm.puesto_invitado.value+"' readonly='readonly' />"+frm.puesto_invitado.value+"</td>"; 
                    nuevaFila+="<td><input type='hidden' name='empresa"+id+"' id='empresa"+id+"' value='"+frm.empresa_invitado.value+"' readonly='readonly' />"+frm.empresa_invitado.value+"</td>"; 
                    nuevaFila+="<td><input type='hidden' name='tipo"+id+"' id='tipo"+id+"' value='"+frm.tipo_invitado.value+"' readonly='readonly' />"+frm.tipo_invitado.value+"</td>";
                    nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+id+"del' id='"+id+"del' onmousedown='borrarPartida(this.id);' style='cursor:pointer;' /></div><div align='center'>Eliminar Partida</div></td>";
                    nuevaFila+= '</tr>';
                                    
                    
                    $("#invitado_table").append(nuevaFila);
                    $("#numInvitados").val(parseInt(frm.rowCount.value));
                    $("#numInvitadosDisabled").val(parseInt(frm.rowCount.value));
                    $("#nombre_invitado").val("");
                    $("#puesto_invitado").val("");
                    $("#empresa_invitado").val("");
                    //$("#capaDirector").html("");
                    $("#tipo_invitado").val("-1");
                    //guardaComprobacion();       
                    $("#guardarComp").removeAttr("disabled");
                    $("#guardarCompprev").removeAttr("disabled");                    
					$("#capaDirector").html("");
					document.getElementById("empresa_invitado").disabled="disable";
					recalculaMontos();
                }
            }
                                                                         
            function borrarPartida(id){
                var frm=document.detallesItinerarios;
                $("#rowCount").bind("restar",function(e,data,data1){
                	e.stopImmediatePropagation();
            			$("#rowCount").val(parseInt($("#invitado_table>tbody>tr").length));
            			$("#numInvitados").val($("#rowCount").val());
            			$("#numInvitadosDisabled").val($("#rowCount").val());
                    });

                $("#rowDel").bind("cambiar",function(e,inicio,tope){
                    e.stopImmediatePropagation();
                    var renglon="";
            		var jqueryrenglon="";
            		var nextrenglon="";
                	var nextrow="";
            		var row="";
            		var jqueryrow="";
                	var nextnombre="";
            		var nombre="";
            		var jquerynombre="";
                	var nextpuesto="";
            		var puesto="";
            		var jquerypuesto="";
                	var nexttipo="";
            		var tipo="";
            		var jquerytipo="";
                	var nextempresa="";
            		var empresa="";
            		var jqueryempresa="";
                	var nextdel="";
            		var del="";
            		var jquerydel="";
            		
            		 for (var i=parseFloat(inicio);i<=parseFloat(tope);i++){ 

             		   	renglon="renglon"+parseInt(i);
            				jqueryrenglon="#renglon"+parseInt(i);
            				nextrenglon="#renglon"+((parseInt(i)+(1)));
                     	nextrow="#row"+((parseInt(i)+(1)));
            				row="row"+parseInt(i);
            				jqueryrow="#row"+parseInt(i);
                     	nextnombre="#nombre"+((parseInt(i)+(1)));
            				nombre="nombre"+parseInt(i);
            				jquerynombre="#nombre"+parseInt(i);
                     	nextpuesto="#puesto"+((parseInt(i)+(1)));
            				puesto="puesto"+parseInt(i);
            				jquerypuesto="#puesto"+parseInt(i);
                     	nexttipo="#tipo"+((parseInt(i)+(1)));
            				tipo="tipo"+parseInt(i);
            				jquerytipo="#tipo"+parseInt(i);           				
                     	nextempresa="#empresa"+((parseInt(i)+(1)));
            				empresa="empresa"+parseInt(i);
            				jqueryempresa="#empresa"+parseInt(i);
                     	nextdel="#"+((parseInt(i)+(1)))+"del";
            				del=parseInt(i)+"del";
            				jquerydel="#"+parseInt(i)+"del";
            				
            				$(nextrenglon).attr("id",renglon);
            	         	$(jqueryrenglon).attr("name",renglon);
            	         	$(jqueryrenglon).html(parseInt(i));
            	         	$(nextrow).attr("id",row);
            	         	$(jqueryrow).attr("name",row);
            	         	$(jqueryrow).val(parseInt(i));          
            	    	  	$(nextnombre).attr("id",nombre);
                  	 	$(jquerynombre).attr("name",nombre);  		        	 
            	      		$(nextpuesto).attr("id",puesto);
              	      		$(jquerypuesto).attr("name",puesto);      
            	      		$(nexttipo).attr("id",tipo);
              			$(jquerytipo).attr("name",tipo);
              			$(nextempresa).attr("id",empresa);
              			$(jqueryempresa).attr("name",empresa);
              			$(nextdel).attr("id",del);
              			$(jquerydel).attr("name",del); 
             		       //next();                                            
                }
                });
                
                            $("img.elimina").click(function(){
                                
                        		$(this).parent().parent().parent().fadeOut("normal", function () {
            						var i=0;
                    				$(this).remove();	
            						$("#rowCount").trigger("restar");
            						$("#rowCount").unbind("restar");
                      				 var tope=$("#rowCount").val();
									 recalculaMontos();
                      				 i=parseFloat(id);
            						$("#rowDel").trigger("cambiar",[i,tope]);
            						$("#rowDel").unbind("cambiar");
                                }); 
                        		return false;    
                            });    
                                                                                                                                                                                                             
                        }
                                                                           
            function getCentroCostos(){
                var frm=document.detallesItinerarios;
                $("#Cecos").val(frm.ccentro_costos.value);
            }      
                                                                
		/***************************************************************************/
		//VARIABLE GLOBAL
		var textoAnterior = '';

		//ESTA FUNCIÓN DEFINE LAS REGLAS DEL JUEGO
		function cumpleReglas(simpleTexto){
			//la pasamos por una poderosa expresión regular
			//var expresion = new RegExp("^(|([0-9]{1,2}(\\.([0-9]{1,2})?)?))$");
			var expresion = new RegExp("^(|([0-9]{1,30}(\\.([0-9]{1,2})?)?))$");

			//si pasa la prueba, es válida
			if(expresion.test(simpleTexto))
				return true;
			return false;
		}//end function checaReglas

		//ESTA FUNCIÓN REVISA QUE TODO LO QUE SE ESCRIBA ESTÉ EN ORDEN
		function revisaCadena(textItem){
			//si comienza con un punto, le agregamos un cero
			if(textItem.value.substring(0,1) == '.') 
				textItem.value = '0' + textItem.value;

			//si no cumples las reglas, no te dejo escribir
			if(!cumpleReglas(textItem.value))
				textItem.value = textoAnterior;
			else //todo en orden
				textoAnterior = textItem.value;
		}//end function revisaCadena
		/***************************************************************************/
			function NumCheck(e, field) { 
				key = e.keyCode ? e.keyCode : e.which ;
				valor = field.value+String.fromCharCode(key);
				// backspace 
				if (key == 8) return true ;
				// 0-9 
				if ((key > 47 && key < 58)) { 
					if (valor == "") return true ;
					//regexp = /.[0-9]{2}$/ 
					regexp = /^[0-9]*(\.[0-9]{0,2})?$/ ;
					return (regexp.test(valor)) ;
				} 
				// . 
				if (key == 46) { 
					if (valor == "") return false ;
					//regexp = /^[0-9]+$/ ;
					regexp = /^[0-9]*(\.[0-9]{0,2})?$/ ;
					return regexp.test(valor) ;
				} 
				// other key 
				return false ;
			}
            function validaNum(valor){                                                                
                cTecla=(document.all)?valor.keyCode:valor.which;
                if(cTecla==8) return true;
                if(cTecla==37) return true;
                if(cTecla==39) return true;
                patron=/^([0-9.]{1,2})?$/;
                cTecla= String.fromCharCode(cTecla);
                return patron.test(cTecla);
            }
            function validaNum2(valor){
                cTecla=(document.all)?valor.keyCode:valor.which;
                if(cTecla==8) return true;
                if(cTecla==37) return "ok";
                if(cTecla==39) return "ok";
                return true;
            }
			function format_input(valor){
				valor = valor.replace(/,/g,"");
				//valor = number_format(valor,2,".",",");
				valor = number_format_sin_redondeo(valor);
				return valor;
			}

            function guardaComprobacion11(){
                var etapaTramite = parseInt($("#etapa").val());
                
                var frm=document.detallesItinerarios;
                id= parseInt($("#solicitud_table").find("tr:last").find("td").eq(0).html());
                if(($("#numInvitados").val() >= 1)){
					if($("#divisa_solicitud_invitacion").val()=="" ||$("#divisa_solicitud_invitacion").val()==-1 ){
                        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
                        $("#divisa_solicitud_invitacion").focus();
                        return false;
                    }else if($("#ccentro_costos").val()==""){
                        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
                        $("#ccentro_costos").focus();
                        return false;
                    }else if($("#ciudad_invitacion").val()==""){
                        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
                        $("#ciudad_invitacion").focus();
                        return false;
                    }else if(etapaTramite == 1 && $("#banderavalida").val() == 1 && $("#observ").val().length == 0){
						alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
						$("#observ").focus();
						return false;
                    }else if($("#monto_solicitado_invitacion").val()==0){
                        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes. Monto");
                        $("#monto_solicitado_invitacion").focus();
                        return false;
                    }else{
                    	document.getElementById("motive").setAttribute("readonly","readonly");
                        $("#guardarCompprev").removeAttr("disabled");
                        if($("#delegado").val() != 0){
                        	$("#enviaDirector").removeAttr("disabled");
                        }else{
                        	$("#guardarCompedit").removeAttr("disabled");
                        }
                    }
                                            
                }      
            }

            function validaInvitados(){
				var numInv = parseInt($("#numInvitados").val());
				if(numInv < 2){
					alert("Favor de ingresar por lo menos dos invitados.");
					return false;
				}else{
					return true;
				}
			}
                                                       
            function guardaComprobacion(){
                var frm=document.detallesItinerarios;
                if(validaInvitados()){
	                id= parseInt($("#solicitud_table").find("tr:last").find("td").eq(0).html());
	                if(($("#numInvitados").val() >= 1)){
	                	if($("#motive").val()==""){
	                        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
	                        $("#motive").focus();
	                        return false;
	                    }else if($("#lugar_inv").val()==""){
	                        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
	                        $("#lugar_inv").focus();
	                        return false;
	                    }else if($("#monto_solicitado_invitacion").val()==0){
	                        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes. Monto");
	                        $("#monto_solicitado_invitacion").focus();
	                        return false;
	                    }else if($("#divisa_solicitud_invitacion").val()=="" ||$("#divisa_solicitud_invitacion").val()==-1 ){
	                        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
	                        $("#divisa_solicitud_invitacion").focus();
	                        return false;
	                    }else if($("#ccentro_costos").val()==""){
	                        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
	                        $("#ccentro_costos").focus();
	                        return false;
	                    }else if($("#ciudad_invitacion").val()==""){
	                        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
	                        $("#ciudad_invitacion").focus();
	                        return false;
	                    } else if ($("#banderavalida").val() == 1){
	                    	if($("#observ").val().length == 0 ){
	                            alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
	                            $("#observ").focus();
	                            return false;
	                        }
	                    } else {
	                    	document.getElementById("motive").setAttribute("readonly","readonly");
	                    	document.getElementById("nombre_invitado").setAttribute("disabled","disabled");
	                    	document.getElementById("puesto_invitado").setAttribute("disabled","disabled");
	                    	document.getElementById("empresa_invitado").setAttribute("disabled","disabled");                    	
	                    	document.getElementById("tipo_invitado").setAttribute("disabled","disabled");						
							document.getElementById("agregar_invitado").setAttribute("disabled","disabled");
	                        $("#guardarCompprev").removeAttr("disabled");
	                        if($("#delegado").val() != 0){
	                        	$("#enviaDirector").removeAttr("disabled");
	                        }else{
	                        	$("#guardarCompedit").removeAttr("disabled");
	                        }
	                    }
	                                            
	                }
                }else{
                    return false;
                }
            }


            function guardaprevioComprobacion(){
                var frm=document.detallesItinerarios;
                    	document.getElementById("motive").setAttribute("readonly","readonly");
						if(document.getElementById("motive").value != "")
							$("#guardarprevCompedit").removeAttr("disabled");
						else if(document.getElementById("motive").value == "")
							frm.guardarprevCompedit.disabled = true;
            }
            function guardaprevioComprobacion1(){
                var frm=document.detallesItinerarios;
                $("#guardarprevCompedit").removeAttr("disabled");                            
            }

            function solicitarConfirmPrevio(){
            	 var frm=document.detallesItinerarios;
        		if(confirm("¿Desea guardar esta Solicitud como previo?")){
        			//frm.submit();
        		}else{
            		return false;
        		}
                }

            function enviarSub(){
                var frm=document.detallesItinerarios;
                document.getElementById("motive").setAttribute("readonly","readonly");                       
            }

            function evaluaMonto(monto){
            	var montoMaximo = 0;
				var montoMaximoDiv = 0;
				
            	var esDirector = <?PHP 
					$idusuario = $_SESSION["idusuario"];
					$esDirector = 0;
					$rutaAutorizacion = new RutaAutorizacion();
					$esDirector = $rutaAutorizacion->nivelEmpleado($idusuario);
					echo $esDirector;?>;
				var directorGeneral = <?PHP echo DIRECTOR_GENERAL;?>;
				
            	$("#capaWarning").html("");
                var mensajeExcedePoliticas = undefined;
                //Variables de las cajas de Texto 
                var divEuro = parseFloat($("#valorDivisaEUR").val());
                //var monto = parseFloat($("#tpesos").val());
                var num_invitados = parseFloat($("#numInvitados").val());
                //Variable para guardar 
                var monto2 = 0;
                monto2 = ((monto / divEuro) / num_invitados );
                
             	// Monto máximo de Comidas de Invitación
				$.ajax({
					type: "POST",
					url: "services/ajax_solicitudes.php",
					data: "montoMaximo=ok",
					dataType: "json",
					async: false, 
					timeout: 10000, 
					success: function(json){
						montoMaximo = parseFloat(json[0].montoCantidad);
						montoMaximoDiv = json[0].divisaMonto;
					}, 
					complete: function (json){
						//alert("Completo: carga de politicas de comida Invitacion:"+montoMaximo+" "+montoMaximoDiv);
						if(monto2 > montoMaximo && mensajeExcedePoliticas == undefined){
		                    if(esDirector != directorGeneral){
		                		mensajeExcedePoliticas = "<strong>Esta rebasando la pol&iacute;tica del concepto. <br>El monto m&aacute;ximo es de " + montoMaximo + " " + montoMaximoDiv + ".<br /> La solicitud requerir&aacute; ser validada por el Dir. General.</strong>";
		                    }else{
		                    	mensajeExcedePoliticas = "<strong>Esta rebasando la pol&iacute;tica del concepto. <br>El monto m&aacute;ximo es de " + montoMaximo + " " + montoMaximoDiv + ".<br /></strong>";
		                    }
		                	conceptoExcedePoliticas = true;                                        
		                } else {
		                	conceptoExcedePoliticas = false;
		                }
		                
		                if(conceptoExcedePoliticas){
		                    $("#capaWarning").html(mensajeExcedePoliticas);
		                    $("#obsjus").html("Agregar justificaci&oacute;n detallada del motivo del excedente<span class='style1'>*</span>:");
		                    document.getElementById("banderavalida").value = 1;                   
		                } else {
		                	$("#obsjus").html("Observaciones:");
		                	document.getElementById("banderavalida").value = 0;
		                }
					},
					error: function(x, t, m) {
						if(t==="timeout") {
							location.reload();
							abort();
						}else if(montoMaximo == 0 || montoMaximo == ""){
							location.reload();
							abort();
						}
					}
				});
            }
            
            function recalculaMontos(){
                var anticipo = parseFloat(($("#monto_solicitado_invitacion").val()).replace(/,/g,""));

                var totalAnticipo = 0;
				var divisas = $("#divisa_solicitud_invitacion").val();

				var tasaNueva = 1;
				if(divisas != 1){ //Si la divisa es diferente a MXN
					//Se obtiene las tasas de las divisas
					var tasa = "<?PHP 
					$query = sprintf('SELECT DIV_ID,DIV_TASA FROM divisa');
					$var = mysql_query($query);
					$aux="";
					while ($arr = mysql_fetch_assoc($var)) {
						$aux.=$arr['DIV_ID'].":".$arr['DIV_TASA'].":";
					}
					echo $aux;?>";
					var tasa2 = tasa.split(":");
					
					//Se obtiene la tasa de la divisa seleccionada
					for(i=0;i<=tasa2.length;i=i+2){
						if(tasa2[i] == divisas){
							tasaNueva = tasa2[i+1];
						}
					}
				}

				totalAnticipo = anticipo * parseFloat(tasaNueva);
				document.getElementById("tpesosdisabled").value = number_format(redondea(totalAnticipo),2,".",",");//redondea a 2 decimales
				document.getElementById("tpesos").value = number_format(redondea(totalAnticipo),2,".",",");//redondea a 2 decimales
                evaluaMonto(totalAnticipo);                	
            }
			
			function redondea(valor){
				return (Math.round(valor * Math.pow(10, 2)) / Math.pow(10, 2));
			}
			            
        </script>

        <link rel="stylesheet" type="text/css" href="../../css/jquery.autocomplete.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/date_input.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/table_style.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/style_Table_Edit.css"/>
        <style type="text/css">
            .style1 {color: #FF0000}

        </style>


        <div id="Layer1" >
            <form action="solicitud_invitacion_new.php?save" method="post" name="detallesItinerarios" id="detallesItinerarios" >
                <center><h3>Solicitud de Invitaci&oacute;n</h3></center>
                <table width="785" border="0" align="center" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left;">
                    <tr>
                    <td colspan="9">&nbsp;</td>
                    </tr>
                    <tr style="text-align:center;">
                        <td colspan="9">Motivo<span class="style1">*</span>: <input name="motive" type="text" id="motive" size=50 maxlength="100" onchange="return guardaprevioComprobacion();" onclick="return guardaprevioComprobacion(); " onkeyup="return guardaprevioComprobacion();" />
                        </td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                    	<td colspan="2" align="right">Fecha de Invitaci&oacute;n<span class="style1">*</span>:</td>
                    	<td align="left"><input name="fecha_inv" id="fecha_inv" value="<?PHP echo date('d/m/Y'); ?>" size="12" readonly="readonly"></td>
						<td>&nbsp;</td>
						<td colspan="2" align="right">Lugar de invitaci&oacute;n/Restaurante<span class="style1">*</span>:</td>
						<td align="left"><input type="text" name="lugar_inv" id="lugar_inv" maxlength="100"/></td>
						<td>&nbsp;</td>
					</tr>
                    <tr>
                    	<td colspan="9"><div>&nbsp;</div></td>
                    </tr>
                </table>
                <br/>
                <center><div id="montoPolitica"></div></center>
                <center><div style="display: none"><span class="style1">* Monto m&aacute;ximo por persona Funcionarios Gubernamentales: 30.00 EUR</span></div></center>
                <br/>
                <center>
                    <table width="785" border="0" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8" >  

                        <tr style="text-align:center;" >
                            <td colspan="4"><h3>Invitados</h3></td>
                        </tr>
                        <tr>  
                            <td>&nbsp;</td>
                            <td width="50%">Nombre<span class="style1">*</span>:&nbsp;&nbsp;<input name="nombre_invitado" type="text" id="nombre_invitado" size=50 maxlength="100" />
                            </td>
                            <td width="50%">Tipo de Invitado<span class="style1">*</span>:&nbsp;&nbsp;<select name="tipo_invitado" id="tipo_invitado" onchange="verificar_tipo_invitado();">
                                    <option value="-1">Seleccione...</option>
                                    <option value="BMW">Empleado BMW de M&eacute;xico</option>
                                    <option value="Externo">Externo</option>
                                    <option value="Gobierno">Gobierno</option>
                                </select>

                            </td>
                            <td>&nbsp;</td>
                        </tr> 
                        <tr>
                            <td>&nbsp;</td>
                            <td width="50%">Puesto<span class="style1">*</span>:&nbsp;&nbsp;&nbsp;<input name="puesto_invitado" type="text" id="puesto_invitado" size=50 maxlength="100" />
                            </td>
                            <td width="50%">Empresa<span class="style1">*</span>:&nbsp;&nbsp;<input name="empresa_invitado" type="text" id="empresa_invitado" size=50 maxlength="100" disabled="disable" />
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><div id="capaDirector"></div></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td colspan="2">
                                <div align="center">
                                    <input name="agregar_invitado" type="button" id="agregar_invitado" value="     Agregar Invitado"  onclick="agregarPartida();" style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>
                                </div>
                            </td>
                            <td>&nbsp;</td>
                        </tr>

                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr><td>&nbsp;</td><td colspan="2" style="text-align:center">
                                <table id="invitado_table" class="tablesorter" cellspacing="1"> 
                                    <thead> 
                                        <tr> 
                                            <th width="5%" align="center" valign="middle">No.</th>
                                            <th width="30%" align="center" valign="middle">Nombre</th> 
                                            <th width="30%" align="center" valign="middle">Puesto</th>
                                            <th width="30%" align="center" valign="middle">Empresa</th>
                                            <th width="30%" align="center" valign="middle">Tipo</th>
                                            <th width="5%" align="center" valign="middle">Eliminar</th>
                                        </tr>
                                       
                                    </thead> 
                                    <tbody> 
                                     <tr>	
                                     		<?php 
											// Obtener el puesto del empleado
                                            //$idempleado = $_SESSION["idusuario"];
                                     		$varDelegado = 0;
                                     		if(isset($_SESSION['iddelegado'])){
                                     			$varDelegado = $_SESSION['iddelegado'];
                                     		}
                                     		$idempleado = verificaSesion($_SESSION["idusuario"], $varDelegado);
                                     		
                                     		//$idempleado = verificaSesion($_SESSION["idusuario"], $_SESSION["iddelegado"]);
                                     		error_log("Empleado retornado: ". $idempleado);
                                     		$usuarioActivo = new Usuario();
                                     		$usuarioActivo->Load_Usuario_By_ID($idempleado);
                                     		$nombreUsuario = $usuarioActivo->Get_dato("u_paterno")." ".$usuarioActivo->Get_dato("u_materno")." ".$usuarioActivo->Get_dato("u_nombre");
                                     		$id_empresa	= $usuarioActivo->Get_dato("u_empresa");
                                            ?>
                                            <td><div id='renglon1' name='renglon1'>1</div><input type="hidden" name="row1" id="row1" value="1" readonly='readonly'/></td>
                                            <td><input type="hidden" name="nombre1" id="nombre1" value="<?PHP echo $nombreUsuario; ?>" /><?PHP echo $nombreUsuario;?></td> 
                                            <td><?PHP                                                                                         
                                            $cnn = new conexion();
                                            $query = sprintf("SELECT npuesto FROM empleado WHERE idfwk_usuario='%s'", $idempleado);
                                            $rst = $cnn->consultar($query);
                                            $fila = mysql_fetch_assoc($rst);                                            
                                            echo $fila['npuesto'];?><input type="hidden" name="puesto1" id="puesto1" value="<?PHP echo $fila['npuesto']; ?>" /></td>
                                            <td aling="center"><?PHP 
                                            // Obtener el nombre de la empresa
                                            $cnn = new conexion();
                                            $query2 = sprintf("SELECT e_nombre FROM empresas WHERE e_id='%s'", $id_empresa);
                                            $rst2 = $cnn->consultar($query2);
                                            $filab = mysql_fetch_assoc($rst2);                                            
                                            echo $filab['e_nombre'];?><input type="hidden" name="empresa1" id="empresa1" value="<?PHP echo $filab['e_nombre'];?>" /></td>
                                            <td aling="center">BMW<input type="hidden" name="tipo1" id="tipo1" value="BMW" /></td>
                                            <td><div align='center'><img id="1del" class="elimina" style="cursor:pointer;" onmousedown="borrarPartida(this.id);" name="1del" alt="Click aquí para Eliminar" src="../../images/delete.gif"></div><div align="center">Eliminar Partida</div></td>
                                        </tr> 
                                        <!-- cuerpo tabla-->
                                    </tbody> 
                                </table> 

                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td> 
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>

                            <td colspan="2">N&uacute;mero de invitados<span class="style1">*</span>:&nbsp;
                                <input type="text" name="numInvitadosDisabled" id="numInvitadosDisabled" value="1" size="15" disabled="disabled" />
                                <input type="hidden" name="numInvitados" id="numInvitados" value="1" size="15" readonly="readonly" /></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>

                    </table>

                    <br/>
                    <br/>
                    <table width="785" border="0" align="center" cellspacing="1">
                        <tr>
                            <td width="3%">&nbsp;</td>
                            <td width="15%">&nbsp;</td>
                            <td width="24%">&nbsp;</td>
                            <td width="23%">&nbsp;</td>
                            <td width="34%">&nbsp;</td>
                            <td width="1%">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td colspan="3">Total Monto solicitado<span class="style1">*</span>:&nbsp;
                                <!--<input name="monto_solicitado_invitacion" type="text" id="monto_solicitado_invitacion" maxlength="100" value="0.00" onkeydown="aux2 = validaNum2(event); return validaNum(event);" onkeypress="" onkeyup="recalculaMontos(); if(aux2 != 'ok'){this.value = format_input(this.value);}" onchange="recalculaMontos();"/>-->
                                <!--<input name="monto_solicitado_invitacion" type="text" id="monto_solicitado_invitacion" maxlength="100" value="0" onkeydown="/*aux2 = validaNum2(event); return validaNum(event);*/" onkeypress="return NumCheck(event, this);" onkeyup="recalculaMontos(); /*if(aux2 != 'ok'){this.value = format_input(this.value);}*/" onchange="recalculaMontos();"/>-->
                                <input name="monto_solicitado_invitacion" type="text" id="monto_solicitado_invitacion" maxlength="100" value="0" onkeydown="" onkeypress="" onkeyup="revisaCadena(this); recalculaMontos();" onchange="recalculaMontos();"/>
                            </td>
                            <td>Divisa<span class="style1">*</span>:&nbsp;<select name="divisa_solicitud_invitacion" id="divisa_solicitud_invitacion" onchange="recalculaMontos();">
                                    <option value="-1">Seleccione...</option>
                                    <option value="1">MXN</option>
                                    <option value="2">USD</option>
                                    <option value="3">EUR</option>
                                </select></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td colspan="3">Total en Pesos:&nbsp;<input type="text" name="tpesosdisabled" id="tpesosdisabled" value="0.00" size="15" disabled="disabled" />
                            <input type="hidden" name="tpesos" id="tpesos" value="0.00" size="15" readonly="readonly" /> MXN<div id="capaWarning"></div></td>
                            <td>Ciudad<span class="style1">*</span>:&nbsp;<input name="ciudad_invitacion" type="text" id="ciudad_invitacion" maxlength="100" /></td>
                            <td><input type="hidden" name="banderavalida" id="banderavalida" readonly="readonly" /></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td colspan="3">Centro de Costos<span class="style1">*</span>:&nbsp;
                                <select name="ccentro_costos" id="ccentro_costos" onchange="getCentroCostos();">
                                    <option id="-1" value="-1">Seleccione...</option>
									<?PHP 
									$query = sprintf("SELECT cc_id,cc_centrocostos,cc_nombre FROM cat_cecos WHERE cc_estatus = '1' AND cc_empresa_id = '" . $_SESSION["empresa"] . "' order by cc_centrocostos");
									$var = mysql_query($query);
									while ($arr = mysql_fetch_assoc($var)) {
										echo sprintf("<option value='%s'>%s - %s</option>", $arr['cc_id'], $arr['cc_centrocostos'], $arr['cc_nombre']);
									}
									?>                    
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                         <?PHP 
                         $tramite = $_GET['edit2'];
                         $FTramite =  new Tramite();
                         $FTramite->Load_Tramite($tramite);
                         $tEtapa = $FTramite->Get_dato("t_etapa_actual");
                         $tAutorizacionesHistorial = $FTramite->Get_dato("t_autorizaciones_historial");
//                          error_log("Tramite: ".$tramite);
//                          error_log("Etapa del tramite: ".$tEtapa);
                         if($tEtapa == SOLICITUD_INVITACION_ETAPA_RECHAZADA || $tEtapa == SOLICITUD_INVITACION_ETAPA_RECHAZADA_POR_DIRECTOR || $tAutorizacionesHistorial != ""){?>
							<tr>
	                            <td>&nbsp;</td>
	                            <td>&nbsp;</td>
	                            <td>&nbsp;</td>
	                            <td>&nbsp;</td>
	                            <td>&nbsp;</td>
	                            <td>&nbsp;</td>
							</tr>
	                        <tr>
	                        	<td>&nbsp;</td>
								<td align="right" valign="top">Historial de Observaciones:</td>
								<td colspan="3" rowspan="1" class="alignLeft" >
								<textarea name="historial_observaciones" id="historial_observaciones" cols="80" rows="5" readonly="readonly" onkeypress="confirmaRegreso('historial_observaciones');" onkeydown="confirmaRegreso('historial_observaciones');"></textarea>
								</td>
							</tr>
						<?PHP }?>
                        <tr>
                            <td><input type="hidden" name="etapa" id="etapa" value="<?PHP echo $tEtapa;?>" /></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td align="right" valign="top"><div id="obsjus">Observaciones:</div></td>
                            <td colspan="3" ><div id="areatext"><textarea name="observ" cols="80" rows="5" id="observ"></textarea></div></td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                </center>
                <br />
                <div align="center"></div>



            <?PHP }
            forma_comprobacion();
            ?>
            <br />
            <div align="center">
                <input type="submit" id="guardarprevCompedit" name="guardarprevCompedit" value="     Guardar Previo"  onclick="return solicitarConfirmPrevio();" style="background:url(../../images/save.gif); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" readonly="readonly" disabled="disable"/>
                &nbsp;&nbsp;&nbsp;
                <?php if(isset($_SESSION['iddelegado'])){ ?>
                	<input type="submit" id="enviaDirector" name="enviaDirector" value="     Enviar a Director" onclick="getCentroCostos();return guardaComprobacion();enviarSub();" style="background:url(../../images/save.gif); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" readonly="readonly" disabled="disabled"/>
                	&nbsp;&nbsp;&nbsp;
               <?php }else{ ?>
                	<input type="submit" id="guardarCompedit" name="guardarCompedit" value="     Enviar Solicitud"  onclick="getCentroCostos();return guardaComprobacion();enviarSub();" style="background:url(../../images/save.gif); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" readonly="readonly" disabled="disable"/>
                <?php } ?>
            </div>
            <?PHP 
            $_divisas = new Divisa();
            $_divisas->Load_data(3); //busca Id de divisa
            $divisaEUR = $_divisas->Get_dato("div_tasa");
            $_divisas1 = new Divisa();
            $_divisas1->Load_data(2);
            $divisaUSD = $_divisas1->Get_dato("div_tasa");
            ?>
            <input type="hidden" name="idusuario" id="idusuario" value="<?PHP echo $_SESSION["idusuario"]; ?>" readonly="readonly" />
            <input type="hidden" name="empleado" id="empleado" value="<?PHP echo $_SESSION["idusuario"]; ?>" readonly="readonly" />
            <input type="hidden" name="empresa" id="empresa" value="<?PHP echo $_SESSION["empresa"]; ?>" readonly="readonly" />
            <input type="hidden" name="guarda" id="guarda" value="" readonly="readonly" />
            <input type="hidden" id="rowCount" name="rowCount" value="1" readonly="readonly"/>
			<input type="hidden" id="rowDel" name="rowDel" value="0" readonly="readonly"/>
            <input type="hidden" id="tramite_sol" name="tramite_sol" value="0" readonly="readonly"/>
            <input type="hidden" id="Cecos" name="Cecos" value="<?PHP echo $idcentrocosto; ?>" readonly="readonly"/>
            <input type='hidden' id='valorDivisaEUR' name='valorDivisaEUR' value="<?PHP echo $divisaEUR; ?>">
            <input type='hidden' id='valorDivisaUSD' name='valorDivisaUSD' value="<?PHP echo $divisaUSD; ?>">
            <input type="hidden" name="montoMaximo" id="montoMaximo" value="0" />
            <input type="hidden" name="montoMaximoDivisa" id="montoMaximoDivisa" value="" />
            <input type="hidden" name="delegado" id="delegado" readonly="readonly" value="<?php if(isset($_SESSION['iddelegado'])){ echo $_SESSION['iddelegado']; }else{echo 0;}?>" />
            <input type="hidden" name="tramiteID" id="tramiteID" readonly="readonly" value="<?php if(isset($_GET['edit2'])){ echo $_GET['edit2']; }else{ echo 0;}?>" />
            </center>
        </form>

        <?PHP 
    }
    ?>