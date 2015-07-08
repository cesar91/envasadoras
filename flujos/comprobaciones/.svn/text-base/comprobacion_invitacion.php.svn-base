<?php
//*****************************//COMPROBACION DE INVITACIONES//*****************************//// </editor-fold>
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
	require_once("services/func_comprobacion.php");
	require_once("../solicitudes/services/C_SV.php");
	require_once("../../lib/php/constantes.php");
	require_once("../solicitudes/services/C_SV.php");
	require_once("../../functions/Usuario.php");
	require_once("../../functions/RutaAutorizacion.php");
	require_once("../../functions/Notificacion.php");
	
	if (isset($_POST['solicitud_de_invitacion']) || $_POST['solicitud_de_invitacion'] != 0) {
		$Sol_Invitacion = $_POST['solicitud_de_invitacion'];
		if ($Sol_Invitacion == "" || $Sol_Invitacion == 0 || $Sol_Invitacion == -1 || $Sol_Invitacion == "n") {
			header("Location: ./index.php?errsol");
			die();
		}
		
		if(isset($_POST['tipo'])&&$_POST['tipo']=="amex"){

			$t_Anticipo_Amex=$_POST['t_amex_comprobado'];
			$t_Total_Comprobacion=$_POST['t_comprobado'];
			$co_subtotal=$_POST['co_subtotal'];
			$co_iva=$_POST['co_iva'];
			$mnt_Reembolso=$_POST['t_reembolso'];
			$observ = $_POST['observ'];
			$invitacionLug = $_POST['lugar_inv'];
			$fecha_inv = $_POST['fechainvitacion'];
			$excedente = $_POST['banderavalida'];
			$tot_invitados = $_POST['numInvitados'];
			$co_ciudad = $_POST['co_ciudad_data'];
			$sesionDelegado = $_POST['delegado'];
			$co_amex_comprobado = $_POST['t_amex_comprobado'];
			$co_efectivo_comprobado = $_POST['t_reembolso'];
			
			// Datos del empleado
			$iduser = 0;
			$delegado = 0;
			//$iduser = $_POST['iu'];
			$idempresa = $_POST["empresa"];		
			$motivoComprobacion = $_POST["motive"];
			$comentario = $_POST['comentarios'];			
			$cMotive = $motivoComprobacion;
			
			if($sesionDelegado != 0){
				$iduser = $sesionDelegado;
				$delegado = $_POST['iu'];
			}else{
				$iduser = $_POST['iu'];
			}
			
			// Datos de la Solicitud
			if($_POST['Cecos_refacturado']==1){
				$refacturar = "1";
			}else{
				$refacturar = "0";
			}
			
			$idSolicitud = $_POST["solicitud_de_invitacion"];
			$cnn = new conexion();

			$aux = $_POST['centro_de_costos'];
			$Vsql = "SELECT cc_id FROM cat_cecos WHERE cc_centrocostos = $aux";
			$Res_Vsql = $cnn->consultar($Vsql);
			$centroCosto = mysql_result($Res_Vsql, 0, "cc_id");
			
			// Registra nuevo tramite
			$tramite = new Tramite();
			$tramite->insertar("BEGIN WORK");
			$idTramite = $tramite->Crea_Tramite($iduser, $idempresa, COMPROBACION_INVITACION_ETAPA_APROBACION, FLUJO_COMPROBACION_INVITACION, $cMotive, $delegado);
			
			$cAmt = str_replace(",","",$_POST['monto']);
			$cRate = $_POST['tasa'];
			$fact=$_POST['fact_chk1'];
			
			$cImp = 0;
			$cImp_porc = 0;
			$cProv ="";
			$cP_RFC ="";
			$cFolio ="";
			$cFlagFactura = 0;
			$idProv ="";
			
			if($fact == "on"){
				$cImp = str_replace(",","",$_POST['impuesto']);
				$cImp_porc = 0;
				if($cImp != 0){
					$cImp_porc = "16";
				}
				$cP_RFC = $_POST['rfc'];
				$cFolio = $_POST['d_folio'];
				$cFlagFactura = 1;
				// ID proveedor
				$cnn = new conexion();
				$query = sprintf("SELECT pro_id FROM proveedores WHERE pro_rfc = '%s'", $cP_RFC);
				//error_log($query);
				$rst = $cnn->consultar($query);
				$num_rows = mysql_num_rows($rst);
				$fila = mysql_fetch_assoc($rst);
				$idProv = $fila['pro_id'];
				if ($num_rows > 0)
					$idProv = $fila['pro_id'];
				else
					$idProv = "-1";
			}else{
				$cImp = 0;
				$cImp_porc = 0;
				$cProv ="-1";
				$cP_RFC ="";
				$cFolio ="";
				$cFlagFactura = 0;	
				$idProv = "-1";
			}
			
			$cPropina=str_replace(",","",$_POST['propina_dato']);
			//Se obtiene el subtotal
			$co_subtotal = $cAmt + $cPropina;
			//Se obtiene el iva
			$co_iva = $cImp;
			//Total de la comprobacion
			$t_Total_Comprobacion = str_replace(",","",$_POST['total']);
			$cTipo_id = 1;
			
			// Agregamos el nombre del usuario al campo de Observaciones
			if($observ != ""){
				$HObser = "";
				
				if($delegado != 0){
					$usuarioObserv = $delegado;
				}else{
					$usuarioObserv = $iduser;
				}
				
				$notificacion = new Notificacion();
				$observ = $notificacion->anotaObservacion($usuarioObserv, $HObser, $observ, FLUJO_COMPROBACION_INVITACION, COMPROBACION_INVITACION_ETAPA_APROBACION);
			}
			
			// Registra nueva comprobacion
			$comprobacion = new Comprobacion();
			$idComprobacion = $comprobacion->Crea_Comprobacion_Invitacion2($t_Total_Comprobacion, $Sol_Invitacion, $co_subtotal, $co_iva, $idTramite, $centroCosto, $cMotive, $cTipo_id, $observ, "", $tot_invitados, $invitacionLug, $fecha_inv, $excedente, $co_ciudad, $co_amex_comprobado, $co_efectivo_comprobado);
			$concepto = new Concepto();
			$concepto->Load_Concepto_By_Nombre("Comidas  Invitacion");
			// Guarda los detalles de la comprobacion
			$cDate = $_POST['fecha'];
			$cConc = $concepto->Get_Dato("dc_id");
			
			if($_POST['select_tarjeta_cargo']!=0){
					$cCargo_asociado_amex=$_POST['select_tarjeta_cargo'];
					$cnn3 = new conexion();
					$id_cargo_amex_anterior = $_POST['select_tarjeta_cargo'];
					$query3 = sprintf("update amex set estatus='1', comprobacion_id ='%s' where idamex='%s'", $idComprobacion, $cCargo_asociado_amex);
					$rst3 = $cnn3->ejecutar($query3);
				}else{
					$cCargo_asociado_amex=0;
				}
			if(isset($_POST['moneda'])){
				$cExch = $_POST['moneda'];
			}else{
				$cExch = "1";
			}
			
			// Guardar campo del IVA
			if($cExch == "1"){
				$cImp = str_replace(",","",$_POST['impuesto']);
				$cImp_porc = 0;
				if($cImp != 0){
					$cImp_porc = "16";
				}
			}
			
			$divisa = new Divisa();
			$divisa->Load_data($cExch); //busca Id de divisa
			$divisa_nombre = $divisa->Get_dato("div_nombre");
			$cTotal = str_replace(",","",$_POST['total']);
			$cTotalPesos = str_replace(",","",$_POST['monto_pesos']);		
			
			// Validar que el concepto no venga vacio
			if (!isset($cConc) || strlen(trim($cConc)) <= 0) {
				continue;
				if($cConc == "Comidas  Invitacion"){
					$cConc = 7;
				}
			}

			// Detalle Comprobación
			$comprobacion->Agrega_Detalle_Comp_Invitacion2($idComprobacion,$cConc,$cP_RFC,$cAmt,$cImp_porc,$cImp,$cTotal,$idProv,$cDate,$cExch,$cRate,$tot_invitados,$cPropina,$cFolio,$cTotalPesos,$cCargo_asociado_amex,$comentario);
			
			$comensales = new Comensales();
			for ($i = 1; $i <= $_POST['numInvitados']; $i++) {
				$sNombre = $_POST['nombre' . $i];
				$sPuesto = $_POST['puesto' . $i];
				$sTipo = $_POST['tipoinv' . $i];
				$sEmpresa = $_POST['empresa' . $i];	
				$id_detalle_solicitud_invitacion=add_detalle_solicitud_invitacion("0", $sNombre, $sPuesto, $sEmpresa, $sTipo, $idTramite);
			}
			
			
			// Buscamos quien debe aprobar esta solicitud
			$ruta_autorizacion = new RutaAutorizacion();
			$ruta_autorizacion->generRutaAutorizacionComprobacionInvitacion($idTramite, $iduser);
			$aprobador = $ruta_autorizacion->getSiguienteAprobador($idTramite, $iduser);
			
			// Envia el tramite a aprobacion
			$usuarioAprobador = new Usuario();
			$usuarioAprobador->Load_Usuario_By_ID($aprobador);
			$duenoActual = new Usuario();
			$duenoActual->Load_Usuario_By_ID($iduser);
			$nombreUsuario = $duenoActual->Get_dato('nombre');
		
			$tramite->Load_Tramite($idTramite);
			$rutaAutorizacion=$tramite->Get_dato('t_ruta_autorizacion');
			$tramite->Modifica_Etapa($idTramite, COMPROBACION_INVITACION_ETAPA_APROBACION, FLUJO_COMPROBACION_INVITACION, $aprobador, $rutaAutorizacion);
			
			if($sesionDelegado != 0){
				$duenoActual->Load_Usuario_By_ID($delegado);
				$nombreDelegado = $duenoActual->Get_dato('nombre');
				$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> en nombre de: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $idTramite, $nombreDelegado, $nombreUsuario);
				$mensaje_email = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>CREADA</strong> por: <strong>%s</strong> en nombre de: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $nombreDelegado, $nombreUsuario);
			}else{
				$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $idTramite, $nombreUsuario);
				$mensaje_email = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>CREADA</strong> por: <strong>%s</strong> requiere de su autorizaci&oacute;n.", $nombreUsuario);
			}
						
			$remitente = $iduser;
			$destinatario = $aprobador;
			$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "1", $mensaje_email); //"0" para no enviar email y "1" para enviarlo
			// Termina transacción
			$tramite->insertar("COMMIT");
			//echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?oksave&docs=docs&type=4'>";
			header("Location: ./index.php?oksave&docs=docs&type=4");
		}else if(isset($_POST['tipo'])&&$_POST['tipo']=="reembolso_para_empleado"){
			$t_Anticipo_Amex=$_POST['t_amex_comprobado'];
			$t_Total_Comprobacion=$_POST['t_comprobado'];
			$co_subtotal=$_POST['co_subtotal'];
			$co_iva=$_POST['co_iva'];
			//$mnt_Descuento=$_POST['t_nocomprobado'];
			$mnt_Reembolso=$_POST['t_reembolso'];
			$iduser = 0;
			$delegado = 0;
			$observ = $_POST['observ'];
			$invitacionLug = $_POST['lugar_inv'];
			$fecha_inv = $_POST['fechainvitacion'];
			$excedente = $_POST['banderavalida'];
			$tot_invitados = $_POST['numInvitados'];
			$co_ciudad = $_POST['co_ciudad_data'];
			$comentario = $_POST['comentarios'];
			$sesionDelegado = $_POST['delegado'];
			$co_amex_comprobado = $_POST['t_amex_comprobado'];
			$co_efectivo_comprobado = $_POST['t_reembolso'];
		
		if($sesionDelegado != 0){
			$iduser = $sesionDelegado;
			$delegado = $_POST['iu'];
		}else{
			$iduser = $_POST['iu'];
		}

		// Agregamos el nombre del usuario al campo de Observaciones
		if($observ != ""){
			$HObser = "";
			
			if($delegado != 0){
				$usuarioObserv = $delegado;
			}else{
				$usuarioObserv = $iduser;
			}
			
			$notificacion = new Notificacion();
			$observ = $notificacion->anotaObservacion($usuarioObserv, $HObser, $observ, FLUJO_COMPROBACION_INVITACION, COMPROBACION_INVITACION_ETAPA_APROBACION);
		}
		
		$idempresa = $_POST["empresa"];		
		$motivoComprobacion = $_POST["motive"];
		$cMotive = $motivoComprobacion;

		// Datos de la Solicitud
		if($_POST['Cecos_refacturado']==1){
			$refacturar = "1";
		}else{
			$refacturar = "0";
		}
		$idSolicitud = $_POST["solicitud_de_invitacion"];
		$cnn = new conexion();
		
		$aux = $_POST['centro_de_costos'];
		$Vsql = "SELECT cc_id FROM cat_cecos WHERE cc_centrocostos = $aux";
		$Res_Vsql = $cnn->consultar($Vsql);
		$centroCosto = mysql_result($Res_Vsql, 0, "cc_id");

		// Registra nuevo tramite
		$tramite = new Tramite();
		$tramite->insertar("BEGIN WORK");
		$idTramite = $tramite->Crea_Tramite($iduser, $idempresa, COMPROBACION_INVITACION_ETAPA_APROBACION, FLUJO_COMPROBACION_INVITACION, $cMotive, $delegado);

		$cAmt = str_replace(",","",$_POST['monto']);
		$cRate = $_POST['tasa'];
		
		$fact=$_POST['fact_chk1'];
					$cImp = 0;
			$cImp_porc = 0;
			$cProv ="";
			$cP_RFC ="";
			$cFolio ="";
			$cFlagFactura = 0;
			$idProv ="";
			
			if($fact == "on"){
				$cImp = str_replace(",","",$_POST['impuesto']);
				$cImp_porc = 0;
				if($cImp != 0){
					$cImp_porc = "16";
				}
				$cP_RFC = $_POST['rfc'];
				$cFolio = $_POST['d_folio'];
				$cFlagFactura = 1;
				// ID proveedor
				$cnn = new conexion();
				$query = sprintf("SELECT pro_id FROM proveedores WHERE pro_rfc = '%s'", $cP_RFC);
				//error_log($query);
				$rst = $cnn->consultar($query);
				$num_rows = mysql_num_rows($rst);
				$fila = mysql_fetch_assoc($rst);
				$idProv = $fila['pro_id'];
				if ($num_rows > 0)
					$idProv = $fila['pro_id'];
				else
					$idProv = "-1";
			}else{
				$cImp = 0;
				$cImp_porc = 0;
				$cProv ="-1";
				$cP_RFC ="";
				$cFolio ="";
				$cFlagFactura = 0;	
				$idProv = "-1";
			}
		$cPropina=str_replace(",","",$_POST['propina_dato']);
		//Se obtiene el subtotal
		$co_subtotal = $cAmt + $cPropina;
		//Se obtiene el iva
		$co_iva = $cImp;
		//Total de la comprobacion
		$t_Total_Comprobacion = str_replace(",","",$_POST['total']);

		$cTipo=$_POST['tipo'];
		$cTipo_id = 3;

		// Registra nueva comprobacion
		$comprobacion = new Comprobacion();
		$idComprobacion = $comprobacion->Crea_Comprobacion_Invitacion2($t_Total_Comprobacion, $Sol_Invitacion, $co_subtotal, $co_iva, $idTramite, $centroCosto, $cMotive, $cTipo_id, $observ, "", $tot_invitados, $invitacionLug, $fecha_inv, $excedente, $co_ciudad, $co_amex_comprobado, $co_efectivo_comprobado);

		$concepto = new Concepto();
		$concepto->Load_Concepto_By_Nombre("Comidas  Invitacion");
		// Guarda los detalles de la comprobacion
			$cDate = $_POST['fecha'];
			$cConc = $concepto->Get_Dato("dc_id");
			$cCargo_asociado_amex=0;

			if(isset($_POST['moneda'])){
				$cExch = $_POST['moneda'];
			}else{
				$cExch = "1";
			}
			
			// Guardar campo del IVA
			if($cExch == "1"){
				$cImp = str_replace(",","",$_POST['impuesto']);
				$cImp_porc = 0;
				if($cImp != 0){
					$cImp_porc = "16";
				}
			}
			
			$divisa = new Divisa();
			$divisa->Load_data($cExch); //busca Id de divisa
			$divisa_nombre = $divisa->Get_dato("div_nombre");
			$cTotal = str_replace(",","",$_POST['total']);
			$cTotalPesos = str_replace(",","",$_POST['monto_pesos']);

			// Validar que el concepto no venga vacio
			if (!isset($cConc) || strlen(trim($cConc)) <= 0) {
				continue;
				if($cConc == "Comidas  Invitacion"){
					$cConc = 7;
				}
			}

			// Detalle Comprobación
			$comprobacion->Agrega_Detalle_Comp_Invitacion2($idComprobacion,$cConc,$cP_RFC,$cAmt,$cImp_porc,$cImp,$cTotal,$idProv,$cDate,$cExch,$cRate,$tot_invitados,$cPropina,$cFolio,$cTotalPesos,$cCargo_asociado_amex,$comentario);

		$comensales = new Comensales();
		for ($i = 1; $i <= $_POST['numInvitados']; $i++) {
			$sNombre = $_POST['nombre' . $i];
			$sPuesto = $_POST['puesto' . $i];
			$sTipo = $_POST['tipoinv' . $i];
			$sEmpresa = $_POST['empresa' . $i];
			
			$id_detalle_solicitud_invitacion=add_detalle_solicitud_invitacion("0", $sNombre, $sPuesto, $sEmpresa, $sTipo, $idTramite);
		}


		// Buscamos quien debe aprobar esta solicitud
		$ruta_autorizacion = new RutaAutorizacion();
		$ruta_autorizacion->generRutaAutorizacionComprobacionInvitacion($idTramite, $iduser);
		$aprobador = $ruta_autorizacion->getSiguienteAprobador($idTramite, $iduser);
			
		// Envia el tramite a aprobacion
		$usuarioAprobador = new Usuario();
		$usuarioAprobador->Load_Usuario_By_ID($aprobador);
		$duenoActual = new Usuario();
		$duenoActual->Load_Usuario_By_ID($iduser);
		$nombreUsuario = $duenoActual->Get_dato('nombre');
		
		$tramite->Load_Tramite($idTramite);
		$rutaAutorizacion=$tramite->Get_dato('t_ruta_autorizacion');
		$tramite->Modifica_Etapa($idTramite, COMPROBACION_INVITACION_ETAPA_APROBACION, FLUJO_COMPROBACION_INVITACION, $aprobador, $rutaAutorizacion);
		
		if($sesionDelegado != 0){
			$duenoActual->Load_Usuario_By_ID($delegado);
			$nombreDelegado = $duenoActual->Get_dato('nombre');
			$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> en nombre de: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $idTramite, $nombreDelegado, $nombreUsuario);
			$mensaje_email = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>CREADA</strong> por: <strong>%s</strong> en nombre de: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $nombreDelegado, $nombreUsuario);
		}else{
			$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $idTramite, $nombreUsuario);
			$mensaje_email = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>CREADA</strong> por: <strong>%s</strong> requiere de su autorizaci&oacute;n.", $nombreUsuario);
		}
		
		$remitente = $iduser;
		$destinatario = $aprobador;
		$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "1", $mensaje_email); //"0" para no enviar email y "1" para enviarlo
		// Termina transacción
		$tramite->insertar("COMMIT");
		//echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?oksave&docs=docs&type=4'>";
		header("Location: ./index.php?oksave&docs=docs&type=4");
		}
	} else {
		//echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?errsave'>";
		header("Location: ./index.php?errsave");
		//die();
	}//if row
}//if guarda comp


if (isset($_POST['guardarCompprev'])) {
	require_once("services/func_comprobacion.php");
	require_once("../solicitudes/services/C_SV.php");
	require_once("../../lib/php/constantes.php");
	require_once("../solicitudes/services/C_SV.php");
	require_once("../../functions/Notificacion.php");
	
	if (isset($_POST['solicitud_de_invitacion']) || $_POST['solicitud_de_invitacion'] != 0) {
		$Sol_Invitacion = $_POST['solicitud_de_invitacion'];
		if ($Sol_Invitacion == "" || $Sol_Invitacion == 0 || $Sol_Invitacion == -1 || $Sol_Invitacion == "n") {
			header("Location: ./index.php?errsol");
			die();
		}
	
		if(isset($_POST['tipo'])&&$_POST['tipo']=="amex"){
	
			$t_Anticipo_Amex=$_POST['t_amex_comprobado'];
			$t_Total_Comprobacion=$_POST['t_comprobado'];
			$co_subtotal=$_POST['co_subtotal'];
			$co_iva=$_POST['co_iva'];
			$mnt_Reembolso=$_POST['t_reembolso'];
			$observ = $_POST['observ'];
			$invitacionLug = $_POST['lugar_inv'];
			$fecha_inv = $_POST['fechainvitacion'];
			$excedente = $_POST['banderavalida'];
			$tot_invitados = $_POST['numInvitados'];
			$co_ciudad = $_POST['co_ciudad_data'];
			$sesionDelegado = $_POST['delegado'];
			$co_amex_comprobado = $_POST['t_amex_comprobado'];
			$co_efectivo_comprobado = $_POST['t_reembolso'];
			
			// Datos del empleado
			$iduser = 0;
			$delegado = 0;
			//$iduser = $_POST['iu'];			
			$idempresa = $_POST["empresa"];			
			$motivoComprobacion = $_POST["motive"];
			$comentario = $_POST['comentarios'];
			$cMotive = $motivoComprobacion;
			
			if($sesionDelegado != 0){
				$iduser = $sesionDelegado;
				$delegado = $_POST['iu'];
			}else{
				$iduser = $_POST['iu'];
			}
				
			// Datos de la Solicitud
			if($_POST['Cecos_refacturado']==1){
				$refacturar = "1";
			}else{
				$refacturar = "0";
			}
				
			$idSolicitud = $_POST["solicitud_de_invitacion"];
			$cnn = new conexion();
				
			$aux = $_POST['centro_de_costos'];
			$Vsql = "SELECT cc_id FROM cat_cecos WHERE cc_centrocostos = $aux";
			$Res_Vsql = $cnn->consultar($Vsql);
			$centroCosto = mysql_result($Res_Vsql, 0, "cc_id");

			// Registra nuevo tramite
			$tramite = new Tramite();
			$tramite->insertar("BEGIN WORK");
			$idTramite = $tramite->Crea_Tramite($iduser, $idempresa, COMPROBACION_INVITACION_ETAPA_SIN_ENVIAR, FLUJO_COMPROBACION_INVITACION, $cMotive, $delegado);
				
			$cAmt = str_replace(",","",$_POST['monto']);
			$cRate = $_POST['tasa'];
			$fact=$_POST['fact_chk1'];
				
			$cImp = 0;
			$cImp_porc = 0;
			$cProv ="";
			$cP_RFC ="";
			$cFolio ="";
			$cFlagFactura = 0;
			$idProv ="";
				
			if($fact == "on"){
				$cImp = str_replace(",","",$_POST['impuesto']);
				$cImp_porc = 0;
				if($cImp != 0){
					$cImp_porc = "16";
				}
				$cP_RFC = $_POST['rfc'];
				$cFolio = $_POST['d_folio'];
				$cFlagFactura = 1;
				// ID proveedor
				$cnn = new conexion();
				$query = sprintf("SELECT pro_id FROM proveedores WHERE pro_rfc = '%s'", $cP_RFC);
				//error_log($query);
				$rst = $cnn->consultar($query);
				$num_rows = mysql_num_rows($rst);
				$fila = mysql_fetch_assoc($rst);
				$idProv = $fila['pro_id'];
				if ($num_rows > 0)
					$idProv = $fila['pro_id'];
				else
					$idProv = "-1";
			}else{
				$cImp = 0;
				$cImp_porc = 0;
				$cProv ="-1";
				$cP_RFC ="";
				$cFolio ="";
				$cFlagFactura = 0;
				$idProv = "-1";
			}
				
			$cPropina=str_replace(",","",$_POST['propina_dato']);
			//Se obtiene el subtotal
			$co_subtotal = $cAmt + $cPropina;
			//Se obtiene el iva
			$co_iva = $cImp;
			//Total de la comprobacion
			$t_Total_Comprobacion = str_replace(",","",$_POST['total']);
			$cTipo_id = 1;
	
			// Registra nueva comprobacion
			$comprobacion = new Comprobacion();
			$idComprobacion = $comprobacion->Crea_Comprobacion_Invitacion2($t_Total_Comprobacion, $Sol_Invitacion, $co_subtotal, $co_iva, $idTramite, $centroCosto, $cMotive, $cTipo_id, "", $observ, $tot_invitados, $invitacionLug, $fecha_inv, $excedente, $co_ciudad, $co_amex_comprobado, $co_efectivo_comprobado);
			$concepto = new Concepto();
			$concepto->Load_Concepto_By_Nombre("Comidas  Invitacion");
			// Guarda los detalles de la comprobacion
			$cDate = $_POST['fecha'];
			$cConc = $concepto->Get_Dato("dc_id");
				
			if($_POST['select_tarjeta_cargo']!=0){
				$cCargo_asociado_amex=$_POST['select_tarjeta_cargo'];
				$cnn3 = new conexion();
				$id_cargo_amex_anterior = $_POST['select_tarjeta_cargo'];
				$query3 = sprintf("UPDATE amex SET estatus = '0', comprobacion_id = '0' WHERE comprobacion_id = '%s'", $idComprobacion);
				$rst3 = $cnn3->ejecutar($query3);
				$query3 = sprintf("update amex set estatus='0', comprobacion_id ='%s' where idamex='%s'", $idComprobacion, $cCargo_asociado_amex);
				$rst3 = $cnn3->ejecutar($query3);
			}else{
				$cCargo_asociado_amex=0;
			}
			if(isset($_POST['moneda'])){
				$cExch = $_POST['moneda'];
			}else{
				$cExch = "1";
			}
			
			// Guardar campo del IVA
			if($cExch == "1"){
				$cImp = str_replace(",","",$_POST['impuesto']);
				$cImp_porc = 0;
				if($cImp != 0){
					$cImp_porc = "16";
				}
			}
			
			$divisa = new Divisa();
			$divisa->Load_data($cExch); //busca Id de divisa
			$divisa_nombre = $divisa->Get_dato("div_nombre");
			$cTotal = str_replace(",","",$_POST['total']);
			$cTotalPesos = str_replace(",","",$_POST['monto_pesos']);
				
			// Validar que el concepto no venga vacio
			if (!isset($cConc) || strlen(trim($cConc)) <= 0) {
				continue;
				if($cConc == "Comidas  Invitacion"){
					$cConc = 7;
				}
			}
	
			// Detalle Comprobación
			$comprobacion->Agrega_Detalle_Comp_Invitacion2($idComprobacion,$cConc,$cP_RFC,$cAmt,$cImp_porc,$cImp,$cTotal,$idProv,$cDate,$cExch,$cRate,$tot_invitados,$cPropina,$cFolio,$cTotalPesos,$cCargo_asociado_amex,$comentario);
				
			$comensales = new Comensales();
			for ($i = 1; $i <= $_POST['numInvitados']; $i++) {
				$sNombre = $_POST['nombre' . $i];
				$sPuesto = $_POST['puesto' . $i];
				$sTipo = $_POST['tipoinv' . $i];
				$sEmpresa = $_POST['empresa' . $i];
				$id_detalle_solicitud_invitacion=add_detalle_solicitud_invitacion("0", $sNombre, $sPuesto, $sEmpresa, $sTipo, $idTramite);
			}
			// Termina transacción
			$tramite->insertar("COMMIT");
			//echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?oksaveprev&docs=docs&type=4'>";
			header("Location: ./index.php?oksaveprev&docs=docs&type=4");
		}else if(isset($_POST['tipo'])&&$_POST['tipo']=="reembolso_para_empleado"){
			$t_Anticipo_Amex=$_POST['t_amex_comprobado'];
			$t_Total_Comprobacion=$_POST['t_comprobado'];
			$co_subtotal=$_POST['co_subtotal'];
			$co_iva=$_POST['co_iva'];
			//$mnt_Descuento=$_POST['t_nocomprobado'];
			$mnt_Reembolso=$_POST['t_reembolso'];
			$sesionDelegado = $_POST['delegado'];
			$co_amex_comprobado = $_POST['t_amex_comprobado'];
			$co_efectivo_comprobado = $_POST['t_reembolso'];
	
			$observ = $_POST['observ'];
			$invitacionLug = $_POST['lugar_inv'];
			$fecha_inv = $_POST['fechainvitacion'];
			$excedente = $_POST['banderavalida'];
			$tot_invitados = $_POST['numInvitados'];
			$co_ciudad = $_POST['co_ciudad_data'];
			$comentario = $_POST['comentarios'];	
	
			// Datos del empleado
			$iduser = 0;
			$delegado = 0;
			//$iduser = $_POST['iu'];			
			$idempresa = $_POST["empresa"];			
			$motivoComprobacion = $_POST["motive"];
			$cMotive = $motivoComprobacion;
			
		
			if($sesionDelegado != 0){
				$iduser = $sesionDelegado;
				$delegado = $_POST["iu"];
			}else{
				$iduser = $_POST["iu"];
			}
	
			// Datos de la Solicitud
			if($_POST['Cecos_refacturado']==1){
				$refacturar = "1";
			}else{
				$refacturar = "0";
			}
			$idSolicitud = $_POST["solicitud_de_invitacion"];
			$cnn = new conexion();
			
			$aux = $_POST['centro_de_costos'];
			$Vsql = "SELECT cc_id FROM cat_cecos WHERE cc_centrocostos = $aux";
			$Res_Vsql = $cnn->consultar($Vsql);
			$centroCosto = mysql_result($Res_Vsql, 0, "cc_id");

			// Registra nuevo tramite
			$tramite = new Tramite();
			$tramite->insertar("BEGIN WORK");
			$idTramite = $tramite->Crea_Tramite($iduser, $idempresa, COMPROBACION_INVITACION_ETAPA_SIN_ENVIAR, FLUJO_COMPROBACION_INVITACION, $cMotive, $delegado);
	
			$cAmt = str_replace(",","",$_POST['monto']);
			$cRate = $_POST['tasa'];
	
			$fact=$_POST['fact_chk1'];
			$cImp = 0;
			$cImp_porc = 0;
			$cProv ="";
			$cP_RFC ="";
			$cFolio ="";
			$cFlagFactura = 0;
			$idProv ="";
				
			if($fact == "on"){
				$cImp = str_replace(",","",$_POST['impuesto']);
				$cImp_porc = 0;
				if($cImp != 0){
					$cImp_porc = "16";
				}
				$cP_RFC = $_POST['rfc'];
				$cFolio = $_POST['d_folio'];
				$cFlagFactura = 1;
				// ID proveedor
				$cnn = new conexion();
				$query = sprintf("SELECT pro_id FROM proveedores WHERE pro_rfc = '%s'", $cP_RFC);
				//error_log($query);
				$rst = $cnn->consultar($query);
				$num_rows = mysql_num_rows($rst);
				$fila = mysql_fetch_assoc($rst);
				$idProv = $fila['pro_id'];
				if ($num_rows > 0)
				 $idProv = $fila['pro_id'];
				else
				 $idProv = "-1";
			}else{
				$cImp = 0;
				$cImp_porc = 0;
				$cProv ="-1";
				$cP_RFC ="";
				$cFolio ="";
				$cFlagFactura = 0;
				$idProv = "-1";
			}
			$cPropina=str_replace(",","",$_POST['propina_dato']);
			//Se obtiene el subtotal
			$co_subtotal = $cAmt + $cPropina;
			//Se obtiene el iva
			$co_iva = $cImp;
			//Total de la comprobacion
			$t_Total_Comprobacion = str_replace(",","",$_POST['total']);
	
			$cTipo=$_POST['tipo'];
			$cTipo_id = 3;
	
			// Registra nueva comprobacion
			$comprobacion = new Comprobacion();
			$idComprobacion = $comprobacion->Crea_Comprobacion_Invitacion2($t_Total_Comprobacion, $Sol_Invitacion, $co_subtotal, $co_iva, $idTramite, $centroCosto, $cMotive, $cTipo_id, "", $observ, $tot_invitados, $invitacionLug, $fecha_inv, $excedente, $co_ciudad, $co_amex_comprobado, $co_efectivo_comprobado);
	
			$concepto = new Concepto();
			$concepto->Load_Concepto_By_Nombre("Comidas  Invitacion");
			// Guarda los detalles de la comprobacion
			$cDate = $_POST['fecha'];
			$cConc = $concepto->Get_Dato("dc_id");
			$cCargo_asociado_amex=0;
	
			if(isset($_POST['moneda'])){
				$cExch = $_POST['moneda'];
			}else{
				$cExch = "1";
			}
			
			// Guardar campo del IVA
			if($cExch == "1"){
				$cImp = str_replace(",","",$_POST['impuesto']);
				$cImp_porc = 0;
				if($cImp != 0){
					$cImp_porc = "16";
				}
			}
			
			$divisa = new Divisa();
			$divisa->Load_data($cExch); //busca Id de divisa
			$divisa_nombre = $divisa->Get_dato("div_nombre");
			$cTotal = str_replace(",","",$_POST['total']);
			$cTotalPesos = str_replace(",","",$_POST['monto_pesos']);
	
			// Validar que el concepto no venga vacio
			if (!isset($cConc) || strlen(trim($cConc)) <= 0) {
				continue;
				if($cConc == "Comidas  Invitacion"){
					$cConc = 7;
				}
			}
	
			// Detalle Comprobación
			$comprobacion->Agrega_Detalle_Comp_Invitacion2($idComprobacion,$cConc,$cP_RFC,$cAmt,$cImp_porc,$cImp,$cTotal,$idProv,$cDate,$cExch,$cRate,$tot_invitados,$cPropina,$cFolio,$cTotalPesos,$cCargo_asociado_amex,$comentario);
	
			$comensales = new Comensales();
			for ($i = 1; $i <= $_POST['numInvitados']; $i++) {
				$sNombre = $_POST['nombre' . $i];
				$sPuesto = $_POST['puesto' . $i];
				$sTipo = $_POST['tipoinv' . $i];
				$sEmpresa = $_POST['empresa' . $i];
					
				$id_detalle_solicitud_invitacion=add_detalle_solicitud_invitacion("0", $sNombre, $sPuesto, $sEmpresa, $sTipo, $idTramite);
			}
 			// Termina transacción
			$tramite->insertar("COMMIT");
			//echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?oksaveprev&docs=docs&type=4'>";
			header("Location: ./index.php?oksaveprev&docs=docs&type=4");
		}else{
			$t_Anticipo_Amex=$_POST['t_amex_comprobado'];
			$t_Total_Comprobacion=$_POST['t_comprobado'];
			$co_subtotal=$_POST['co_subtotal'];
			$co_iva=$_POST['co_iva'];
			//$mnt_Descuento=$_POST['t_nocomprobado'];
			$mnt_Reembolso=$_POST['t_reembolso'];
			$sesionDelegado = $_POST['delegado'];
			$co_amex_comprobado = $_POST['t_amex_comprobado'];
			$co_efectivo_comprobado = $_POST['t_reembolso'];
			
			$observ = $_POST['observ'];
			$invitacionLug = $_POST['lugar_inv'];
			$fecha_inv = $_POST['fechainvitacion'];
			$excedente = $_POST['banderavalida'];
			$tot_invitados = $_POST['numInvitados'];
			$co_ciudad = $_POST['co_ciudad_data'];
			$comentario = $_POST['comentarios'];
			
			// Datos del empleado
			$iduser = 0;
			$delegado = 0;
			//$iduser = $_POST['iu'];
			$idempresa = $_POST["empresa"];
			$motivoComprobacion = $_POST["motive"];
			$cMotive = $motivoComprobacion;
			
			if($sesionDelegado != 0){
				$iduser = $sesionDelegado;
				$delegado = $_POST['iu'];
			}else{
				$iduser = $_POST['iu'];
			}
			
			// Datos de la Solicitud
			if($_POST['Cecos_refacturado']==1){
				$refacturar = "1";
			}else{
				$refacturar = "0";
			}
			$idSolicitud = $_POST["solicitud_de_invitacion"];
			$cnn = new conexion();
			$aux = $_POST['centro_de_costos'];
			$Vsql = "SELECT cc_id FROM cat_cecos WHERE cc_centrocostos = $aux";
			$Res_Vsql = $cnn->consultar($Vsql);
			$centroCosto = mysql_result($Res_Vsql, 0, "cc_id");
			
			// Registra nuevo tramite
			$tramite = new Tramite();
			$tramite->insertar("BEGIN WORK");
			$idTramite = $tramite->Crea_Tramite($iduser, $idempresa, COMPROBACION_INVITACION_ETAPA_SIN_ENVIAR, FLUJO_COMPROBACION_INVITACION, $cMotive, $delegado);
			
			$cAmt = str_replace(",","",$_POST['monto']);
			$cRate = $_POST['tasa'];
			
			$fact=$_POST['fact_chk1'];
			$cImp = 0;
			$cImp_porc = 0;
			$cProv ="";
			$cP_RFC ="";
			$cFolio ="";
			$cFlagFactura = 0;
			$idProv ="";
			
			if($fact == "on"){
				$cImp = str_replace(",","",$_POST['impuesto']);
				$cImp_porc = 0;
				if($cImp != 0){
					$cImp_porc = "16";
				}
				$cP_RFC = $_POST['rfc'];
				$cFolio = $_POST['d_folio'];
				$cFlagFactura = 1;
				// ID proveedor
				$cnn = new conexion();
				$query = sprintf("SELECT pro_id FROM proveedores WHERE pro_rfc = '%s'", $cP_RFC);
				//error_log($query);
				$rst = $cnn->consultar($query);
				$num_rows = mysql_num_rows($rst);
				$fila = mysql_fetch_assoc($rst);
				$idProv = $fila['pro_id'];
				if ($num_rows > 0)
					$idProv = $fila['pro_id'];
				else
					$idProv = "-1";
			}else{
				$cImp = 0;
				$cImp_porc = 0;
				$cProv ="-1";
				$cP_RFC ="";
				$cFolio ="";
				$cFlagFactura = 0;
				$idProv = "-1";
			}
			$cPropina=str_replace(",","",$_POST['propina_dato']);
			//Se obtiene el subtotal
			$co_subtotal = $cAmt + $cPropina;
			//Se obtiene el iva
			$co_iva = $cImp;
			//Total de la comprobacion
			$t_Total_Comprobacion = str_replace(",","",$_POST['total']);
			
			$cTipo=$_POST['tipo'];
			
			// Registra nueva comprobacion
			$comprobacion = new Comprobacion();
			$idComprobacion = $comprobacion->Crea_Comprobacion_Invitacion2($t_Total_Comprobacion, $Sol_Invitacion, $co_subtotal, $co_iva, $idTramite, $centroCosto, $cMotive, $cTipo_id, "", $observ, $tot_invitados, $invitacionLug, $fecha_inv, $excedente, $co_ciudad, $co_amex_comprobado, $co_efectivo_comprobado);
			
			$concepto = new Concepto();
			$concepto->Load_Concepto_By_Nombre("Comidas  Invitacion");
			// Guarda los detalles de la comprobacion
			$cDate = $_POST['fecha'];
			$cConc = $concepto->Get_Dato("dc_id");
			$cCargo_asociado_amex=0;
			
			if(isset($_POST['moneda'])){
				$cExch = $_POST['moneda'];
			}else{
				$cExch = "1";
			}
				
			// Guardar campo del IVA
			if($cExch == "1"){
				$cImp = str_replace(",","",$_POST['impuesto']);
				$cImp_porc = 0;
				if($cImp != 0){
					$cImp_porc = "16";
				}
			}
				
			$divisa = new Divisa();
			$divisa->Load_data($cExch); //busca Id de divisa
			$divisa_nombre = $divisa->Get_dato("div_nombre");
			$cTotal = str_replace(",","",$_POST['total']);
			$cTotalPesos = str_replace(",","",$_POST['monto_pesos']);
			
			// Validar que el concepto no venga vacio
			if (!isset($cConc) || strlen(trim($cConc)) <= 0) {
				continue;
				if($cConc == "Comidas  Invitacion"){
					$cConc = 7;
				}
			}
			
			// Detalle Comprobación
			$comprobacion->Agrega_Detalle_Comp_Invitacion2($idComprobacion,$cConc,$cP_RFC,$cAmt,$cImp_porc,$cImp,$cTotal,$idProv,$cDate,$cExch,$cRate,$tot_invitados,$cPropina,$cFolio,$cTotalPesos,$cCargo_asociado_amex,$comentario);
			
			$comensales = new Comensales();
			for ($i = 1; $i <= $_POST['numInvitados']; $i++) {
				$sNombre = $_POST['nombre' . $i];
				$sPuesto = $_POST['puesto' . $i];
				$sTipo = $_POST['tipoinv' . $i];
				$sEmpresa = $_POST['empresa' . $i];
					
				$id_detalle_solicitud_invitacion=add_detalle_solicitud_invitacion("0", $sNombre, $sPuesto, $sEmpresa, $sTipo, $idTramite);
			}
			// Termina transacción
			$tramite->insertar("COMMIT");
			//echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?oksaveprev&docs=docs&type=4'>";
			header("Location: ./index.php?oksaveprev&docs=docs&type=4");
		}
	} else {
		//echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?errsave'>";
		header("Location: ./index.php?errsave");
		//die();
	}//if row
}//if guarda comp

if (isset($_POST['guardarCompedit'])) {
	require_once("services/func_comprobacion.php");
	require_once("../solicitudes/services/C_SV.php");
	require_once("../../lib/php/constantes.php");
	require_once("../solicitudes/services/C_SV.php");
	require_once("../../functions/Usuario.php");
	require_once("../../functions/RutaAutorizacion.php");
	require_once("../../functions/Notificacion.php");
	require_once("$RUTA_A/functions/utils.php");
	require_once("$RUTA_A/functions/Delegados.php");
	
	function limpiar_invitados($id_tramite){
		$cnn = new conexion();
		$query="delete from comensales_sol_inv where dci_solicitud='".$id_tramite."'";
		//error_log($query);
		$cnn->ejecutar($query);
	}
	function limpiar_detalles($id_tramite){
		$cnn = new conexion();
		$query="delete from detalle_comprobacion_invitacion where dc_comprobacion='".$id_tramite."'";
		$cnn->ejecutar($query);
	}

	if (isset($_POST['solicitud_de_invitacion']) || $_POST['solicitud_de_invitacion'] != 0) {
		$Sol_Invitacion = $_POST['solicitud_de_invitacion'];
		if ($Sol_Invitacion == "" || $Sol_Invitacion == 0 || $Sol_Invitacion == -1 || $Sol_Invitacion == "n") {
			header("Location: ./index.php?errsol");
			die();
		}
		
		// Volvemos los cargos AMEX a su estado original
		$cnn3 = new conexion();
		$query3 = sprintf("UPDATE amex 
				JOIN detalle_comprobacion_invitacion ON dc_idamex_comprobado = idamex 
				JOIN comprobacion_invitacion ON dc_comprobacion = co_id 
				SET estatus = '0', comprobacion_id = '0', dc_idamex_comprobado = '0' 
				WHERE co_mi_tramite = '%s'", $_POST['tramite_id']);
		//error_log($query3);
		$rst3 = $cnn3->ejecutar($query3);
	
		if(isset($_POST['tipo'])&&$_POST['tipo']=="amex"){
			$tramite_editar=$_POST['tramite_id'];
			$t_Anticipo_Amex=$_POST['t_amex_comprobado'];
			$t_Total_Comprobacion=$_POST['t_comprobado'];
			$co_subtotal=$_POST['co_subtotal'];
			$co_iva=$_POST['co_iva'];
			$mnt_Reembolso=$_POST['t_reembolso'];
			$observ = $_POST['observ'];
			$invitacionLug = $_POST['lugar_inv'];
			$fecha_inv = $_POST['fechainvitacion'];
			$excedente = $_POST['banderavalida'];
			$tot_invitados = $_POST['numInvitados'];
			$co_ciudad = $_POST['co_ciudad_data'];
			$sesionDelegado = $_POST['delegado'];
			$co_amex_comprobado = $_POST['t_amex_comprobado'];
			$co_efectivo_comprobado = $_POST['t_reembolso'];
			
			// Datos del empleado
			$iduser = 0;
			$delegado = 0;
			$idempresa = $_POST["empresa"];			
			$motivoComprobacion = $_POST["motive"];
			$comentario = $_POST['comentarios'];
			//$HObser = $_POST['historial_observaciones'];
			$etapaTramite = $_POST['etapa'];
			$cMotive = $motivoComprobacion;
			
			// Registra nuevo tramite
		$tramite = new Tramite();
		$delegados = new Delegados();
		$tramite->Load_Tramite($tramite_editar);
		$t_autorizaciones_historial = $tramite->Get_dato("t_autorizaciones_historial");
			
			if($sesionDelegado != 0){
				$iduser = $sesionDelegado;
				$delegado = $_POST["iu"];
			}else{
				$iduser = $_POST["iu"];
			}
			
			if($etapaTramite == COMPROBACION_INVITACION_ETAPA_RECHAZADA || $etapaTramite == COMPROBACION_INVITACION_ETAPA_RECHAZADA_POR_DIRECTOR || $t_autorizaciones_historial != "" || $etapaTramite == COMPROBACION_INVITACION_ETAPA_DEVUELTA_CON_OBSERVACIONES){
				$HObser = $_POST['historial_observaciones'];
			}else{
				$HObser = "";
			}
			
			// Datos de la Solicitud
			if($_POST['Cecos_refacturado']==1){
				$refacturar = "1";
			}else{
				$refacturar = "0";
			}
				
			$idSolicitud = $_POST["solicitud_de_invitacion"];
			$cnn = new conexion();
			
			$aux = $_POST['centro_de_costos'];
			$Vsql = "SELECT cc_id FROM cat_cecos WHERE cc_centrocostos = $aux";
			$Res_Vsql = $cnn->consultar($Vsql);
			$centroCosto = mysql_result($Res_Vsql, 0, "cc_id");
		
		$tramite->insertar("BEGIN WORK");
		
		if($etapaTramite != COMPROBACION_INVITACION_ETAPA_DEVUELTA_CON_OBSERVACIONES){
			$existeDelegado = $delegados->existenciaDelegado($iduser, $delegado);
			if(!$existeDelegado){
		    	//Si el previo lo genero un delegado, pero la solicitud, la envio el Director, borraremos el id del delegado que realizo el previo.
		    	$tramite->actualizaDelegado($tramite_editar, 0);
		    }else{
		    	//Si el previo lo genero un delegado, pero la solicitud, la envio el Director, y en un rechazo la envio el delegado, se guardará nuevamente el id del delegado.
		    	$tramite->actualizaDelegado($tramite_editar, $delegado);
		    }
			$tramite->Modifica_Etapa($tramite_editar, COMPROBACION_INVITACION_ETAPA_APROBACION, FLUJO_COMPROBACION_INVITACION, "", "");
		}else{			
			$rutaAuto=new RutaAutorizacion();
			$t_dueno=$rutaAuto->getDueno($tramite_editar);
			
			$duenoActual = new Usuario();
			$duenoActual->Load_Usuario_By_ID($iduser);
			
			// Enviamos notificación a Finanzas que el usuario ha regresado la comprobación a Finanzas
			$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>DEVUELTA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $tramite_editar, $duenoActual->Get_dato('nombre'));
			$remitente = $iduser;
			$destinatario = $t_dueno;
			$tramite->EnviaNotificacion($tramite_editar, $mensaje, $remitente, $destinatario, "0", ""); //"0" para no enviar email y "1" para enviarlo
			
			$tramite->Modifica_Etapa($tramite_editar, COMPROBACION_INVITACION_ETAPA_APROBACION, FLUJO_COMPROBACION_INVITACION, $t_dueno, "");
		}
				
			$cAmt = str_replace(",","",$_POST['monto']);
			$cRate = $_POST['tasa'];
			$fact=$_POST['fact_chk1'];
				
			$cImp = 0;
			$cImp_porc = 0;
			$cProv ="";
			$cP_RFC ="";
			$cFolio ="";
			$cFlagFactura = 0;
			$idProv ="";
				
			if($fact == "on"){
				$cImp = str_replace(",","",$_POST['impuesto']);
				$cImp_porc = 0;
				if($cImp != 0){
					$cImp_porc = "16";
				}
				$cP_RFC = $_POST['rfc'];
				$cFolio = $_POST['d_folio'];
				$cFlagFactura = 1;
				// ID proveedor
				$cnn = new conexion();
				$query = sprintf("SELECT pro_id FROM proveedores WHERE pro_rfc = '%s'", $cP_RFC);
				//error_log($query);
				$rst = $cnn->consultar($query);
				$num_rows = mysql_num_rows($rst);
				$fila = mysql_fetch_assoc($rst);
				$idProv = $fila['pro_id'];
				if ($num_rows > 0)
					$idProv = $fila['pro_id'];
				else
					$idProv = "-1";
			}else{
				$cImp = 0;
				$cImp_porc = 0;
				$cProv ="-1";
				$cP_RFC ="";
				$cFolio ="";
				$cFlagFactura = 0;
				$idProv = "-1";
			}
				
			$cPropina=str_replace(",","",$_POST['propina_dato']);
			//Se obtiene el subtotal
			$co_subtotal = $cAmt + $cPropina;
			//Se obtiene el iva
			$co_iva = $cImp;
			//Total de la comprobacion
			$t_Total_Comprobacion = str_replace(",","",$_POST['total']);
			$cTipo_id = 1;
	
			// Agregamos el nombre del usuario al campo de Observaciones
			if($observ != ""){
				if($delegado != 0){
					$usuarioObserv = $delegado;
				}else{
					$usuarioObserv = $iduser;
				}
				$notificacion = new Notificacion();
				$observ = $notificacion->anotaObservacion($usuarioObserv, $HObser, $observ, FLUJO_COMPROBACION_INVITACION, COMPROBACION_INVITACION_ETAPA_APROBACION);
			}else{
				$observ = $HObser;
			}
			
		// Registra nueva comprobacion
		$comprobacion = new Comprobacion();
		$idComprobacion = $comprobacion->edita_Comprobacion_Invitacion2($t_Total_Comprobacion, $Sol_Invitacion, $co_subtotal, $co_iva, $centroCosto, $cMotive, $cTipo_id, $observ, "", $tot_invitados, $invitacionLug, $fecha_inv, $excedente, $co_ciudad, $co_amex_comprobado, $co_efectivo_comprobado, $tramite_editar);
			
			$concepto = new Concepto();
			$concepto->Load_Concepto_By_Nombre("Comidas  Invitacion");
			// Guarda los detalles de la comprobacion
			$cDate = $_POST['fecha'];
			$cConc = $concepto->Get_Dato("dc_id");
				
			if($_POST['select_tarjeta_cargo']!=0){
				$cCargo_asociado_amex=$_POST['select_tarjeta_cargo'];
				$cnn3 = new conexion();
				$id_cargo_amex_anterior = $_POST['select_tarjeta_cargo'];
				$query3 = sprintf("update amex set estatus='0', comprobacion_id ='%s' where idamex='%s'", $idComprobacion, $cCargo_asociado_amex);
				$rst3 = $cnn3->ejecutar($query3);
			}else{
				$cCargo_asociado_amex=0;
			}
			if(isset($_POST['moneda'])){
				$cExch = $_POST['moneda'];
			}else{
				$cExch = "1";
			}
			
			// Guardar campo del IVA
			if($cExch == "1"){
				$cImp = str_replace(",","",$_POST['impuesto']);
				$cImp_porc = 0;
				if($cImp != 0){
					$cImp_porc = "16";
				}
			}
			
			$divisa = new Divisa();
			$divisa->Load_data($cExch); //busca Id de divisa
			$divisa_nombre = $divisa->Get_dato("div_nombre");
			$cTotal = str_replace(",","",$_POST['total']);
			$cTotalPesos = str_replace(",","",$_POST['monto_pesos']);
				
			// Validar que el concepto no venga vacio
			if (!isset($cConc) || strlen(trim($cConc)) <= 0) {
				continue;
				if($cConc == "Comidas  Invitacion"){
					$cConc = 7;
				}
			}
			limpiar_detalles($idComprobacion);
			// Detalle Comprobación
			$comprobacion->Agrega_Detalle_Comp_Invitacion2($idComprobacion,$cConc,$cP_RFC,$cAmt,$cImp_porc,$cImp,$cTotal,$idProv,$cDate,$cExch,$cRate,$tot_invitados,$cPropina,$cFolio,$cTotalPesos,$cCargo_asociado_amex,$comentario);
			limpiar_invitados($tramite_editar);
				
			$comensales = new Comensales();
			for ($i = 1; $i <= $_POST['numInvitados']; $i++) {
				$sNombre = $_POST['nombre' . $i];
				$sPuesto = $_POST['puesto' . $i];
				$sTipo = $_POST['tipoinv' . $i];
				$sEmpresa = $_POST['empresa' . $i];
			$id_detalle_solicitud_invitacion=add_detalle_solicitud_invitacion("0", $sNombre, $sPuesto, $sEmpresa, $sTipo, $tramite_editar);
			}
				
		if($etapaTramite != COMPROBACION_INVITACION_ETAPA_DEVUELTA_CON_OBSERVACIONES){
			// Buscamos quien debe aprobar esta solicitud
			$ruta_autorizacion = new RutaAutorizacion();
			$ruta_autorizacion->generRutaAutorizacionComprobacionInvitacion($tramite_editar, $iduser);
			$aprobador = $ruta_autorizacion->getSiguienteAprobador($tramite_editar, $iduser);
				
			// Envia el tramite a aprobacion
			$usuarioAprobador = new Usuario();
			$usuarioAprobador->Load_Usuario_By_ID($aprobador);
			$duenoActual = new Usuario();
			$duenoActual->Load_Usuario_By_ID($iduser);
			$nombreUsuario = $duenoActual->Get_dato('nombre');
			
			$tramite->Load_Tramite($tramite_editar);
			$rutaAutorizacion=$tramite->Get_dato('t_ruta_autorizacion');
			$tramite->Modifica_Etapa($tramite_editar, COMPROBACION_INVITACION_ETAPA_APROBACION, FLUJO_COMPROBACION_INVITACION, $aprobador, $rutaAutorizacion);
			
			if($sesionDelegado != 0){
				$duenoActual->Load_Usuario_By_ID($delegado);
				$nombreDelegado = $duenoActual->Get_dato('nombre');
				$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> en nombre de: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $tramite_editar, $nombreDelegado, $nombreUsuario);
				$mensaje_email = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>CREADA</strong> por: <strong>%s</strong> en nombre de: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $nombreDelegado, $nombreUsuario);
			}else{
				$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $tramite_editar, $nombreUsuario);
				$mensaje_email = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>CREADA</strong> por: <strong>%s</strong> requiere de su autorizaci&oacute;n.", $nombreUsuario);
			}			
			
			$remitente = $iduser;
			$destinatario = $aprobador;
			$tramite->EnviaNotificacion($tramite_editar, $mensaje, $remitente, $destinatario, "1", $mensaje_email); //"0" para no enviar email y "1" para enviarlo
		}
		// Termina transacción
		$tramite->insertar("COMMIT");
		//echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?oksave&docs=docs&type=4'>";
		header("Location: ./index.php?oksave&docs=docs&type=4");
		}else if(isset($_POST['tipo'])&&$_POST['tipo']=="reembolso_para_empleado"){
			$tramite_editar=$_POST['tramite_id'];
			$t_Anticipo_Amex=$_POST['t_amex_comprobado'];
			$t_Total_Comprobacion=$_POST['t_comprobado'];
			$co_subtotal=$_POST['co_subtotal'];
			$co_iva=$_POST['co_iva'];
			//$mnt_Descuento=$_POST['t_nocomprobado'];
			$mnt_Reembolso=$_POST['t_reembolso'];
			$iduser = $_POST['iu'];
			$observ = $_POST['observ'];
			$invitacionLug = $_POST['lugar_inv'];
			$fecha_inv = $_POST['fechainvitacion'];
			$excedente = $_POST['banderavalida'];
			$tot_invitados = $_POST['numInvitados'];
			$co_ciudad = $_POST['co_ciudad_data'];
			$comentario = $_POST['comentarios'];
			$HObser = $_POST['historial_observaciones'];
			$etapaTramite = $_POST['etapa'];
			$sesionDelegado = $_POST['delegado'];
			$co_amex_comprobado = $_POST['t_amex_comprobado'];
			$co_efectivo_comprobado = $_POST['t_reembolso'];
			
			// Datos del empleado
			$iduser = 0;
			$delegado = 0;
			$idempresa = $_POST["empresa"];			
			$motivoComprobacion = $_POST["motive"];
			$cMotive = $motivoComprobacion;
			
			if($sesionDelegado != 0){
				$iduser = $sesionDelegado;
				$delegado = $_POST["iu"];
			}else{
				$iduser = $_POST["iu"];
			}
			
			// Registra nuevo tramite
			$tramite = new Tramite();
			$delegados = new Delegados();
			$tramite->Load_Tramite($tramite_editar);
			$t_autorizaciones_historial = $tramite->Get_dato("t_autorizaciones_historial");
			
			if($etapaTramite == COMPROBACION_INVITACION_ETAPA_RECHAZADA || $etapaTramite == COMPROBACION_INVITACION_ETAPA_RECHAZADA_POR_DIRECTOR || $t_autorizaciones_historial != "" || $etapaTramite == COMPROBACION_INVITACION_ETAPA_DEVUELTA_CON_OBSERVACIONES){
				$HObser = $_POST['historial_observaciones'];
			}else{
				$HObser = "";
			}
				
			// Agregamos el nombre del usuario al campo de Observaciones
			if($observ != ""){
				if($delegado != 0){
					$usuarioObserv = $delegado;
				}else{
					$usuarioObserv = $iduser;
				}
				$notificacion = new Notificacion();
				$observ = $notificacion->anotaObservacion($usuarioObserv, $HObser, $observ, FLUJO_COMPROBACION_INVITACION, COMPROBACION_INVITACION_ETAPA_APROBACION);
				//$observ = anotaObservacion($iduser,$HObser,$observ);
			}else{
				$observ = $HObser;
			}
			
			// Datos de la Solicitud
			if($_POST['Cecos_refacturado']==1){
				$refacturar = '1';
			}else{
				$refacturar = '0';
			}
			$idSolicitud = $_POST['solicitud_de_invitacion'];
			$cnn = new conexion();

			$aux = $_POST['centro_de_costos'];
			$Vsql = "SELECT cc_id FROM cat_cecos WHERE cc_centrocostos = $aux";
			$Res_Vsql = $cnn->consultar($Vsql);
			$centroCosto = mysql_result($Res_Vsql, 0, "cc_id");
			
		$tramite->insertar("BEGIN WORK");
		
		if($etapaTramite != COMPROBACION_INVITACION_ETAPA_DEVUELTA_CON_OBSERVACIONES){
		$existeDelegado = $delegados->existenciaDelegado($iduser, $delegado);
			if(!$existeDelegado){
		    	//Si el previo lo genero un delegado, pero la solicitud, la envio el Director, borraremos el id del delegado que realizo el previo.
		    	$tramite->actualizaDelegado($tramite_editar, 0);
		    }else{
		    	//Si el previo lo genero un delegado, pero la solicitud, la envio el Director, y en un rechazo la envio el delegado, se guardará nuevamente el id del delegado.
		    	$tramite->actualizaDelegado($tramite_editar, $delegado);
		    }
			$tramite->Modifica_Etapa($tramite_editar, COMPROBACION_INVITACION_ETAPA_APROBACION, FLUJO_COMPROBACION_INVITACION, "", "");
		}else{			
			$rutaAuto=new RutaAutorizacion();
			$t_dueno=$rutaAuto->getDueno($tramite_editar);
			
			$duenoActual = new Usuario();
			$duenoActual->Load_Usuario_By_ID($iduser);
			
			// Enviamos notificación a Finanzas que el usuario ha regresado la comprobación a Finanzas
			$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>DEVUELTA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $tramite_editar, $duenoActual->Get_dato('nombre'));
			$remitente = $iduser;
			$destinatario = $t_dueno;
			$tramite->EnviaNotificacion($tramite_editar, $mensaje, $remitente, $destinatario, "0", ""); //"0" para no enviar email y "1" para enviarlo
			
			$tramite->Modifica_Etapa($tramite_editar, COMPROBACION_INVITACION_ETAPA_APROBACION, FLUJO_COMPROBACION_INVITACION, $t_dueno, "");
		}
	
			$cAmt = str_replace(",","",$_POST['monto']);
			$cRate = $_POST['tasa'];
	
			$fact=$_POST['fact_chk1'];
			$cImp = 0;
			$cImp_porc = 0;
			$cProv ="";
			$cP_RFC ="";
			$cFolio ="";
			$cFlagFactura = 0;
			$idProv ="";
				
			if($fact == "on"){
				$cImp = str_replace(",","",$_POST['impuesto']);
				$cImp_porc = 0;
				if($cImp != 0){
					$cImp_porc = "16";
				}
				$cP_RFC = $_POST['rfc'];
				$cFolio = $_POST['d_folio'];
				$cFlagFactura = 1;
				// ID proveedor
				$cnn = new conexion();
				$query = sprintf("SELECT pro_id FROM proveedores WHERE pro_rfc = '%s'", $cP_RFC);
				//error_log($query);
				$rst = $cnn->consultar($query);
				$num_rows = mysql_num_rows($rst);
				$fila = mysql_fetch_assoc($rst);
				$idProv = $fila['pro_id'];
				if ($num_rows > 0)
					$idProv = $fila['pro_id'];
				else
					$idProv = "-1";
			}else{
				$cImp = 0;
				$cImp_porc = 0;
				$cProv ="-1";
				$cP_RFC ="";
				$cFolio ="";
				$cFlagFactura = 0;
				$idProv = "-1";
			}
			$cPropina=str_replace(",","",$_POST['propina_dato']);
			//Se obtiene el subtotal
			$co_subtotal = $cAmt + $cPropina;
			//Se obtiene el iva
			$co_iva = $cImp;
			//Total de la comprobacion
			$t_Total_Comprobacion = str_replace(",","",$_POST['total']);
	
			$cTipo=$_POST['tipo'];
			$cTipo_id = 3;
	
		// Registra nueva comprobacion
		$comprobacion = new Comprobacion();
		$idComprobacion = $comprobacion->edita_Comprobacion_Invitacion2($t_Total_Comprobacion, $Sol_Invitacion, $co_subtotal, $co_iva, $centroCosto, $cMotive, $cTipo_id, $observ, "", $tot_invitados, $invitacionLug, $fecha_inv, $excedente, $co_ciudad, $co_amex_comprobado, $co_efectivo_comprobado, $tramite_editar);
			
			$concepto = new Concepto();
			$concepto->Load_Concepto_By_Nombre("Comidas  Invitacion");
			// Guarda los detalles de la comprobacion
			$cDate = $_POST['fecha'];
			$cConc = $concepto->Get_Dato("dc_id");
			$cCargo_asociado_amex=0;
	
			if(isset($_POST['moneda'])){
				$cExch = $_POST['moneda'];
			}else{
				$cExch = "1";
			}
			
			// Guardar campo del IVA
			if($cExch == "1"){
				$cImp = str_replace(",","",$_POST['impuesto']);
				$cImp_porc = 0;
				if($cImp != 0){
					$cImp_porc = "16";
				}
			}
			
			$divisa = new Divisa();
			$divisa->Load_data($cExch); //busca Id de divisa
			$divisa_nombre = $divisa->Get_dato("div_nombre");
			$cTotal = str_replace(",","",$_POST['total']);
			$cTotalPesos = str_replace(",","",$_POST['monto_pesos']);
	
			// Validar que el concepto no venga vacio
			if (!isset($cConc) || strlen(trim($cConc)) <= 0) {
				continue;
				if($cConc == "Comidas  Invitacion"){
					$cConc = 7;
				}
			}
			limpiar_detalles($idComprobacion);
			// Detalle Comprobación
			$comprobacion->Agrega_Detalle_Comp_Invitacion2($idComprobacion,$cConc,$cP_RFC,$cAmt,$cImp_porc,$cImp,$cTotal,$idProv,$cDate,$cExch,$cRate,$tot_invitados,$cPropina,$cFolio,$cTotalPesos,$cCargo_asociado_amex,$comentario);
			limpiar_invitados($tramite_editar);
			
			$comensales = new Comensales();
			for ($i = 1; $i <= $_POST['numInvitados']; $i++) {
				$sNombre = $_POST['nombre' . $i];
				$sPuesto = $_POST['puesto' . $i];
				$sTipo = $_POST['tipoinv' . $i];
				$sEmpresa = $_POST['empresa' . $i];
					
			$id_detalle_solicitud_invitacion=add_detalle_solicitud_invitacion("0", $sNombre, $sPuesto, $sEmpresa, $sTipo, $tramite_editar);
			}
		
		if($etapaTramite != COMPROBACION_INVITACION_ETAPA_DEVUELTA_CON_OBSERVACIONES){
			// Buscamos quien debe aprobar esta solicitud
			$ruta_autorizacion = new RutaAutorizacion();
			$ruta_autorizacion->generRutaAutorizacionComprobacionInvitacion($tramite_editar, $iduser);
			$aprobador = $ruta_autorizacion->getSiguienteAprobador($tramite_editar, $iduser);
				
			// Envia el tramite a aprobacion
			$usuarioAprobador = new Usuario();
			$usuarioAprobador->Load_Usuario_By_ID($aprobador);
			$duenoActual = new Usuario();
			$duenoActual->Load_Usuario_By_ID($iduser);
			$nombreUsuario = $duenoActual->Get_dato('nombre');
			
			$tramite->Load_Tramite($tramite_editar);
			$rutaAutorizacion=$tramite->Get_dato('t_ruta_autorizacion');
			$tramite->Modifica_Etapa($tramite_editar, COMPROBACION_INVITACION_ETAPA_APROBACION, FLUJO_COMPROBACION_INVITACION, $aprobador, $rutaAutorizacion);
			
			if($sesionDelegado != 0){
				$duenoActual->Load_Usuario_By_ID($delegado);
				$nombreDelegado = $duenoActual->Get_dato('nombre');
				$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> en nombre de: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $tramite_editar, $nombreDelegado, $nombreUsuario);
				$mensaje_email = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>CREADA</strong> por: <strong>%s</strong> en nombre de: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $nombreDelegado, $nombreUsuario);
			}else{
				$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $tramite_editar, $nombreUsuario);
				$mensaje_email = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>CREADA</strong> por: <strong>%s</strong> requiere de su autorizaci&oacute;n.", $nombreUsuario);
			}
			
			$remitente = $iduser;
			$destinatario = $aprobador;
			$tramite->EnviaNotificacion($tramite_editar, $mensaje, $remitente, $destinatario, "1", $mensaje_email); //"0" para no enviar email y "1" para enviarlo
		}
		// Termina transacción
		$tramite->insertar("COMMIT");
		//echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?oksave&docs=docs&type=4'>";
		header("Location: ./index.php?oksave&docs=docs&type=4");
		}
	} else {
		//echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?errsave'>";
		header("Location: ./index.php?errsave");
		//die();
	}//if row
}//if guarda comp


if (isset($_POST['guardarCompprevedit'])){
	require_once("services/func_comprobacion.php");
	require_once("../solicitudes/services/C_SV.php");
	require_once("../../lib/php/constantes.php");
	require_once("../solicitudes/services/C_SV.php");
	require_once("../../functions/Notificacion.php");
	
	function limpiar_invitados($id_tramite){
		$cnn = new conexion();
		$query="delete from comensales_sol_inv where dci_solicitud='".$id_tramite."'";
		//error_log($query);
		$cnn->ejecutar($query);
	}
	function limpiar_detalles($id_tramite){
		$cnn = new conexion();
		$query="delete from detalle_comprobacion_invitacion where dc_comprobacion='".$id_tramite."'";
		$cnn->ejecutar($query);
	}	

	if (isset($_POST['solicitud_de_invitacion']) || $_POST['solicitud_de_invitacion'] != 0) {
		$Sol_Invitacion = $_POST['solicitud_de_invitacion'];
		if ($Sol_Invitacion == "" || $Sol_Invitacion == 0 || $Sol_Invitacion == -1 || $Sol_Invitacion == "n") {
			header("Location: ./index.php?errsol");
			die();
		}
		
		// Volvemos los cargos AMEX a su estado original
		$cnn3 = new conexion();
		$query3 = sprintf("UPDATE amex 
				JOIN detalle_comprobacion_invitacion ON dc_idamex_comprobado = idamex 
				JOIN comprobacion_invitacion ON dc_comprobacion = co_id 
				SET estatus = '0', comprobacion_id = '0', dc_idamex_comprobado = '0' 
				WHERE co_mi_tramite = '%s'", $_POST['tramite_id']);
		//error_log($query3);
		$rst3 = $cnn3->ejecutar($query3);
		
		if(isset($_POST['tipo'])&&$_POST['tipo']=="amex"){
			$tramite_editar=$_POST['tramite_id'];
			$t_Anticipo_Amex=$_POST['t_amex_comprobado'];
			$t_Total_Comprobacion=$_POST['t_comprobado'];
			$co_subtotal=$_POST['co_subtotal'];
			$co_iva=$_POST['co_iva'];
			$mnt_Reembolso=$_POST['t_reembolso'];
			$observ = $_POST['observ'];
			$invitacionLug = $_POST['lugar_inv'];
			$fecha_inv = $_POST['fechainvitacion'];
			$excedente = $_POST['banderavalida'];
			$tot_invitados = $_POST['numInvitados'];
			$co_ciudad = $_POST['co_ciudad_data'];
			$co_amex_comprobado = $_POST['t_amex_comprobado'];
			$co_efectivo_comprobado = $_POST['t_reembolso'];
			
			// Datos del empleado
			$iduser = $_POST['iu'];			
			$idempresa = $_POST["empresa"];			
			$motivoComprobacion = $_POST["motive"];
			$comentario = $_POST['comentarios'];
			$t_etapa = $_POST['etapa'];
			$cMotive = $motivoComprobacion;
				
			// Datos de la Solicitud
			if($_POST['Cecos_refacturado']==1){
				$refacturar = "1";
			}else{
				$refacturar = "0";
			}
				
			$idSolicitud = $_POST["solicitud_de_invitacion"];
			$cnn = new conexion();
			
			$aux = $_POST['centro_de_costos'];
			$Vsql = "SELECT cc_id FROM cat_cecos WHERE cc_centrocostos = $aux";
			$Res_Vsql = $cnn->consultar($Vsql);
			$centroCosto = mysql_result($Res_Vsql, 0, "cc_id");
			
			// Registra nuevo tramite
			$tramite = new Tramite();
			$tramite->insertar("BEGIN WORK");
			$tramite->Modifica_Etapa($tramite_editar, COMPROBACION_INVITACION_ETAPA_SIN_ENVIAR, FLUJO_COMPROBACION_INVITACION, $iduser, "");
			$tramite->Load_Tramite($tramite_editar);
			$t_autorizaciones_historial = $tramite->Get_dato("t_autorizaciones_historial");
			
			$cAmt = str_replace(",","",$_POST['monto']);
			$cRate = $_POST['tasa'];
			$fact=$_POST['fact_chk1'];
				
			$cImp = 0;
			$cImp_porc = 0;
			$cProv ="";
			$cP_RFC ="";
			$cFolio ="";
			$cFlagFactura = 0;
			$idProv ="";
				
			if($fact == "on"){
				$cImp = str_replace(",","",$_POST['impuesto']);
				$cImp_porc = 0;
				if($cImp != 0){
					$cImp_porc = "16";
				}
				$cP_RFC = $_POST['rfc'];
				$cFolio = $_POST['d_folio'];
				$cFlagFactura = 1;
				// ID proveedor
				$cnn = new conexion();
				$query = sprintf("SELECT pro_id FROM proveedores WHERE pro_rfc = '%s'", $cP_RFC);
				//error_log($query);
				$rst = $cnn->consultar($query);
				$num_rows = mysql_num_rows($rst);
				$fila = mysql_fetch_assoc($rst);
				$idProv = $fila['pro_id'];
				if ($num_rows > 0)
					$idProv = $fila['pro_id'];
				else
					$idProv = "-1";
			}else{
				$cImp = 0;
				$cImp_porc = 0;
				$cProv ="-1";
				$cP_RFC ="";
				$cFolio ="";
				$cFlagFactura = 0;
				$idProv = "-1";
			}
				
			$cPropina=str_replace(",","",$_POST['propina_dato']);
			//Se obtiene el subtotal
			$co_subtotal = $cAmt + $cPropina;
			//Se obtiene el iva
			$co_iva = $cImp;
			//Total de la comprobacion
			$t_Total_Comprobacion = str_replace(",","",$_POST['total']);
			$cTipo_id = 1;
	
			// Registra nueva comprobacion
			$comprobacion = new Comprobacion();
			$idComprobacion = $comprobacion->edita_Comprobacion_Invitacion2($t_Total_Comprobacion, $Sol_Invitacion, $co_subtotal, $co_iva, $centroCosto, $cMotive, $cTipo_id, "", $observ, $tot_invitados, $invitacionLug, $fecha_inv, $excedente, $co_ciudad, $co_amex_comprobado, $co_efectivo_comprobado, $tramite_editar);
					
			$concepto = new Concepto();
			$concepto->Load_Concepto_By_Nombre("Comidas  Invitacion");
			// Guarda los detalles de la comprobacion
			$cDate = $_POST['fecha'];
			$cConc = $concepto->Get_Dato("dc_id");
				
			if($_POST['select_tarjeta_cargo']!=0){
				$cCargo_asociado_amex=$_POST['select_tarjeta_cargo'];
				$cnn3 = new conexion();
				$id_cargo_amex_anterior = $_POST['select_tarjeta_cargo'];
				$query3 = sprintf("UPDATE amex SET estatus = '0', comprobacion_id = '0' WHERE comprobacion_id = '%s'", $idComprobacion);
				$rst3 = $cnn3->ejecutar($query3);
				$query3 = sprintf("update amex set estatus='0', comprobacion_id ='%s' where idamex='%s'", $idComprobacion, $cCargo_asociado_amex);
				$rst3 = $cnn3->ejecutar($query3);
			}else{
				$cCargo_asociado_amex=0;
			}
			
			if(isset($_POST['moneda'])){
				$cExch = $_POST['moneda'];
			}else{
				$cExch = "1";
			}
			
			// Guardar campo del IVA
			if($cExch == "1"){
				$cImp = str_replace(",","",$_POST['impuesto']);
				$cImp_porc = 0;
				if($cImp != 0){
					$cImp_porc = "16";
				}
			}
			
			$divisa = new Divisa();
			$divisa->Load_data($cExch); //busca Id de divisa
			$divisa_nombre = $divisa->Get_dato("div_nombre");
			$cTotal = str_replace(",","",$_POST['total']);
			$cTotalPesos = str_replace(",","",$_POST['monto_pesos']);
				
			// Validar que el concepto no venga vacio
			if (!isset($cConc) || strlen(trim($cConc)) <= 0) {
				continue;
				if($cConc == "Comidas  Invitacion"){
					$cConc = 7;
				}
			}
		limpiar_detalles($idComprobacion);
		// Detalle Comprobación
		$comprobacion->Agrega_Detalle_Comp_Invitacion2($idComprobacion,$cConc,$cP_RFC,$cAmt,$cImp_porc,$cImp,$cTotal,$idProv,$cDate,$cExch,$cRate,$tot_invitados,$cPropina,$cFolio,$cTotalPesos,$cCargo_asociado_amex,$comentario);
		limpiar_invitados($tramite_editar);
				
			$comensales = new Comensales();
			for ($i = 1; $i <= $_POST['numInvitados']; $i++) {
				$sNombre = $_POST['nombre' . $i];
				$sPuesto = $_POST['puesto' . $i];
				$sTipo = $_POST['tipoinv' . $i];
				$sEmpresa = $_POST['empresa' . $i];
			$id_detalle_solicitud_invitacion=add_detalle_solicitud_invitacion("0", $sNombre, $sPuesto, $sEmpresa, $sTipo, $tramite_editar);
			}
				
		if($t_etapa == COMPROBACION_INVITACION_ETAPA_RECHAZADA || $t_etapa == COMPROBACION_INVITACION_ETAPA_RECHAZADA_POR_DIRECTOR || $t_autorizaciones_historial != "" || $t_etapa == COMPROBACION_INVITACION_ETAPA_DEVUELTA_CON_OBSERVACIONES){
			$historial = $_POST['historial_observaciones'];
			// Agregamos el nombre del usuario al campo de Observaciones
			$cnn = new conexion();
			$query = sprintf("UPDATE comprobacion_invitacion SET co_observaciones = '%s' WHERE co_mi_tramite = '%s'", $historial, $tramite_editar);
			//error_log($query);
			$cnn->ejecutar($query);
			
			$tramite->Modifica_Etapa($tramite_editar, COMPROBACION_INVITACION_ETAPA_SIN_ENVIAR, FLUJO_COMPROBACION_INVITACION, "", "");
			
			// Limpiar el campo de la ruta de autorización
			$query = sprintf("UPDATE tramites SET t_ruta_autorizacion = '' WHERE t_id = '%s'", $tramite_editar);
			//error_log($query);
			$cnn->ejecutar($query);
		}
		$tramite->insertar("COMMIT");
		//echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?oksaveprev&docs=docs&type=4'>";
		header("Location: ./index.php?oksaveprev&docs=docs&type=4");
		}else if(isset($_POST['tipo'])&&$_POST['tipo']=="reembolso_para_empleado"){
			$tramite_editar=$_POST['tramite_id'];
			$t_Anticipo_Amex=$_POST['t_amex_comprobado'];
			$t_Total_Comprobacion=$_POST['t_comprobado'];
			$co_subtotal=$_POST['co_subtotal'];
			$co_iva=$_POST['co_iva'];
			//        $mnt_Descuento=$_POST['t_nocomprobado'];
			$mnt_Reembolso=$_POST['t_reembolso'];
	
			$observ = $_POST['observ'];
			$invitacionLug = $_POST['lugar_inv'];
			$fecha_inv = $_POST['fechainvitacion'];
			$excedente = $_POST['banderavalida'];
			$tot_invitados = $_POST['numInvitados'];
			$co_ciudad = $_POST['co_ciudad_data'];
			$comentario = $_POST['comentarios'];
			$t_etapa = $_POST['etapa'];
			$co_amex_comprobado = $_POST['t_amex_comprobado'];
			$co_efectivo_comprobado = $_POST['t_reembolso'];
	
			// Datos del empleado
			$iduser = $_POST['iu'];
			$idempresa = $_POST["empresa"];			
			$motivoComprobacion = $_POST["motive"];
			$cMotive = $motivoComprobacion;
	
			// Datos de la Solicitud
			if($_POST['Cecos_refacturado']==1){
				$refacturar = "1";
			}else{
				$refacturar = "0";
			}
			$idSolicitud = $_POST["solicitud_de_invitacion"];
			$cnn = new conexion();
			
			$aux = $_POST['centro_de_costos'];
			$Vsql = "SELECT cc_id FROM cat_cecos WHERE cc_centrocostos = $aux";
			$Res_Vsql = $cnn->consultar($Vsql);
			$centroCosto = mysql_result($Res_Vsql, 0, "cc_id");
			
			// Registra nuevo tramite
			$tramite = new Tramite();
			$tramite->insertar("BEGIN WORK");
			$tramite->Modifica_Etapa($tramite_editar, COMPROBACION_INVITACION_ETAPA_SIN_ENVIAR, FLUJO_COMPROBACION_INVITACION, $iduser, "");
			$tramite->Load_Tramite($tramite_editar);
			$t_autorizaciones_historial = $tramite->Get_dato("t_autorizaciones_historial");
			
			$cAmt = str_replace(",","",$_POST['monto']);
			$cRate = $_POST['tasa'];
	
			$fact=$_POST['fact_chk1'];
			$cImp = 0;
			$cImp_porc = 0;
			$cProv ="";
			$cP_RFC ="";
			$cFolio ="";
			$cFlagFactura = 0;
			$idProv ="";
				
			if($fact == "on"){
				$cImp = str_replace(",","",$_POST['impuesto']);
				$cImp_porc = 0;
				if($cImp != 0){
					$cImp_porc = "16";
				}
				$cP_RFC = $_POST['rfc'];
				$cFolio = $_POST['d_folio'];
				$cFlagFactura = 1;
				// ID proveedor
				$cnn = new conexion();
				$query = sprintf("SELECT pro_id FROM proveedores WHERE pro_rfc = '%s'", $cP_RFC);
				//error_log($query);
				$rst = $cnn->consultar($query);
				$num_rows = mysql_num_rows($rst);
				$fila = mysql_fetch_assoc($rst);
				$idProv = $fila['pro_id'];
				if ($num_rows > 0)
					$idProv = $fila['pro_id'];
				else
					$idProv = "-1";
			}else{
				$cImp = 0;
				$cImp_porc = 0;
				$cProv ="-1";
				$cP_RFC ="";
				$cFolio ="";
				$cFlagFactura = 0;
				$idProv = "-1";
			}
			$cPropina=str_replace(",","",$_POST['propina_dato']);
			//Se obtiene el subtotal
			$co_subtotal = $cAmt + $cPropina;
			//Se obtiene el iva
			$co_iva = $cImp;
			//Total de la comprobacion
			$t_Total_Comprobacion = str_replace(",","",$_POST['total']);
	
			$cTipo=$_POST['tipo'];
			$cTipo_id = 3;
	
		// Registra nueva comprobacion
		$comprobacion = new Comprobacion();
		$idComprobacion = $comprobacion->edita_Comprobacion_Invitacion2($t_Total_Comprobacion, $Sol_Invitacion, $co_subtotal, $co_iva, $centroCosto, $cMotive, $cTipo_id, "", $observ, $tot_invitados, $invitacionLug, $fecha_inv, $excedente, $co_ciudad, $co_amex_comprobado, $co_efectivo_comprobado, $tramite_editar);

						
			$concepto = new Concepto();
			$concepto->Load_Concepto_By_Nombre("Comidas  Invitacion");
			// Guarda los detalles de la comprobacion
			$cDate = $_POST['fecha'];
			$cConc = $concepto->Get_Dato("dc_id");
			$cCargo_asociado_amex=0;
	
			if(isset($_POST['moneda'])){
				$cExch = $_POST['moneda'];
			}else{
				$cExch = "1";
			}
			
			// Guardar campo del IVA
			if($cExch == "1"){
				$cImp = str_replace(",","",$_POST['impuesto']);
				$cImp_porc = 0;
				if($cImp != 0){
					$cImp_porc = "16";
				}
			}
			
			$divisa = new Divisa();
			$divisa->Load_data($cExch); //busca Id de divisa
			$divisa_nombre = $divisa->Get_dato("div_nombre");
			$cTotal = str_replace(",","",$_POST['total']);
			$cTotalPesos = str_replace(",","",$_POST['monto_pesos']);
	
			// Validar que el concepto no venga vacio
			if (!isset($cConc) || strlen(trim($cConc)) <= 0) {
				continue;
				if($cConc == "Comidas  Invitacion"){
					$cConc = 7;
				}
			}
		limpiar_detalles($idComprobacion);
		// Detalle Comprobación
		$comprobacion->Agrega_Detalle_Comp_Invitacion2($idComprobacion,$cConc,$cP_RFC,$cAmt,$cImp_porc,$cImp,$cTotal,$idProv,$cDate,$cExch,$cRate,$tot_invitados,$cPropina,$cFolio,$cTotalPesos,$cCargo_asociado_amex,$comentario);
		limpiar_invitados($tramite_editar);
			
			$comensales = new Comensales();
			for ($i = 1; $i <= $_POST['numInvitados']; $i++) {
				$sNombre = $_POST['nombre' . $i];
				$sPuesto = $_POST['puesto' . $i];
				$sTipo = $_POST['tipoinv' . $i];
				$sEmpresa = $_POST['empresa' . $i];
					
			$id_detalle_solicitud_invitacion=add_detalle_solicitud_invitacion("0", $sNombre, $sPuesto, $sEmpresa, $sTipo, $tramite_editar);
			}
			
			if($t_etapa == COMPROBACION_INVITACION_ETAPA_RECHAZADA || $t_etapa == COMPROBACION_INVITACION_ETAPA_RECHAZADA_POR_DIRECTOR || $t_autorizaciones_historial != "" || $etapaTramite == COMPROBACION_INVITACION_ETAPA_DEVUELTA_CON_OBSERVACIONES){
				$historial = $_POST['historial_observaciones'];
				// Agregamos el nombre del usuario al campo de Observaciones
				$cnn = new conexion();
				$query = sprintf("UPDATE comprobacion_invitacion SET co_observaciones = '%s' WHERE co_mi_tramite = '%s'", $historial, $tramite_editar);
				//error_log($query);
				$cnn->ejecutar($query);
					
				$tramite = new Tramite();
				$tramite->Modifica_Etapa($tramite_editar, COMPROBACION_INVITACION_ETAPA_SIN_ENVIAR, FLUJO_COMPROBACION_INVITACION, "", "");
					
				// Limpiar el campo de la ruta de autorización
				$query = sprintf("UPDATE tramites SET t_ruta_autorizacion = '' WHERE t_id = '%s'", $tramite_editar);
				//error_log($query);
				$cnn->ejecutar($query);
			}
		// Termina transacción
		$tramite->insertar("COMMIT");
		//echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?oksaveprev&docs=docs&type=4'>";
		header("Location: ./index.php?oksaveprev&docs=docs&type=4");
		}else{
			$t_Anticipo_Amex=$_POST['t_amex_comprobado'];
			$t_Total_Comprobacion=$_POST['t_comprobado'];
			$co_subtotal=$_POST['co_subtotal'];
			$co_iva=$_POST['co_iva'];
			//        $mnt_Descuento=$_POST['t_nocomprobado'];
			$mnt_Reembolso=$_POST['t_reembolso'];
			
			$observ = $_POST['observ'];
			$invitacionLug = $_POST['lugar_inv'];
			$fecha_inv = $_POST['fechainvitacion'];
			$excedente = $_POST['banderavalida'];
			$tot_invitados = $_POST['numInvitados'];
			$co_ciudad = $_POST['co_ciudad_data'];
			$comentario = $_POST['comentarios'];
			$sesionDelegado = $_POST['delegado'];
			$t_etapa = $_POST['etapa'];
			$co_amex_comprobado = $_POST['t_amex_comprobado'];
			$co_efectivo_comprobado = $_POST['t_reembolso'];
			
			// Datos del empleado
			$iduser = 0;
			$delegado = 0;
			//$iduser = $_POST['iu'];
			if($sesionDelegado != 0){
				$iduser = $sesionDelegado;
				$delegado = $_POST["iu"];
			}else{
				$iduser = $_POST["iu"];
			}
			$idempresa = $_POST["empresa"];
			$motivoComprobacion = $_POST["motive"];
			$cMotive = $motivoComprobacion;
			
			// Datos de la Solicitud
			if($_POST['Cecos_refacturado']==1){
				$refacturar = "1";
			}else{
				$refacturar = "0";
			}
			$idSolicitud = $_POST["solicitud_de_invitacion"];
			$cnn = new conexion();
			$aux = $_POST['centro_de_costos'];
			$Vsql = "SELECT cc_id FROM cat_cecos WHERE cc_centrocostos = $aux";
			$Res_Vsql = $cnn->consultar($Vsql);
			$centroCosto = mysql_result($Res_Vsql, 0, "cc_id");
			
			// Registra nuevo tramite
			$tramite = new Tramite();
			$tramite->insertar("BEGIN WORK");
			$idTramite = $tramite->Crea_Tramite($iduser, $idempresa, COMPROBACION_INVITACION_ETAPA_SIN_ENVIAR, FLUJO_COMPROBACION_INVITACION, $cMotive);
			
			$cAmt = str_replace(",","",$_POST['monto']);
			$cRate = $_POST['tasa'];
			
			$fact=$_POST['fact_chk1'];
			$cImp = 0;
			$cImp_porc = 0;
			$cProv ="";
			$cP_RFC ="";
			$cFolio ="";
			$cFlagFactura = 0;
			$idProv ="";
			
			if($fact == "on"){
				$cImp = str_replace(",","",$_POST['impuesto']);
				$cImp_porc = 0;
				if($cImp != 0){
					$cImp_porc = "16";
				}
				$cP_RFC = $_POST['rfc'];
				$cFolio = $_POST['d_folio'];
				$cFlagFactura = 1;
				// ID proveedor
				$cnn = new conexion();
				$query = sprintf("SELECT pro_id FROM proveedores WHERE pro_rfc = '%s'", $cP_RFC);
				//error_log($query);
				$rst = $cnn->consultar($query);
				$num_rows = mysql_num_rows($rst);
				$fila = mysql_fetch_assoc($rst);
				$idProv = $fila['pro_id'];
				if ($num_rows > 0)
					$idProv = $fila['pro_id'];
				else
					$idProv = "-1";
			}else{
				$cImp = 0;
				$cImp_porc = 0;
				$cProv ="-1";
				$cP_RFC ="";
				$cFolio ="";
				$cFlagFactura = 0;
				$idProv = "-1";
			}
			$cPropina=str_replace(",","",$_POST['propina_dato']);
			//Se obtiene el subtotal
			$co_subtotal = $cAmt + $cPropina;
			//Se obtiene el iva
			$co_iva = $cImp;
			//Total de la comprobacion
			$t_Total_Comprobacion = str_replace(",","",$_POST['total']);
			
			$cTipo=$_POST['tipo'];
			
			// Registra nueva comprobacion
			$comprobacion = new Comprobacion();
			$idComprobacion = $comprobacion->Crea_Comprobacion_Invitacion2($t_Total_Comprobacion, $Sol_Invitacion, $co_subtotal, $co_iva, $idTramite, $centroCosto, $cMotive, $cTipo_id, $observ, "", $tot_invitados, $invitacionLug, $fecha_inv, $excedente, $co_ciudad, $co_amex_comprobado, $co_efectivo_comprobado);
			
			$concepto = new Concepto();
			$concepto->Load_Concepto_By_Nombre("Comidas  Invitacion");
			// Guarda los detalles de la comprobacion
			$cDate = $_POST['fecha'];
			$cConc = $concepto->Get_Dato("dc_id");
			$cCargo_asociado_amex=0;
			
			if(isset($_POST['moneda'])){
				$cExch = $_POST['moneda'];
			}else{
				$cExch = "1";
			}
				
			// Guardar campo del IVA
			if($cExch == "1"){
				$cImp = str_replace(",","",$_POST['impuesto']);
				$cImp_porc = 0;
				if($cImp != 0){
					$cImp_porc = "16";
				}
			}
				
			$divisa = new Divisa();
			$divisa->Load_data($cExch); //busca Id de divisa
			$divisa_nombre = $divisa->Get_dato("div_nombre");
			$cTotal = str_replace(",","",$_POST['total']);
			$cTotalPesos = str_replace(",","",$_POST['monto_pesos']);
			
			// Validar que el concepto no venga vacio
			if (!isset($cConc) || strlen(trim($cConc)) <= 0) {
				continue;
				if($cConc == "Comidas  Invitacion"){
					$cConc = 7;
				}
			}
			
			// Detalle Comprobación
			$comprobacion->Agrega_Detalle_Comp_Invitacion2($idComprobacion,$cConc,$cP_RFC,$cAmt,$cImp_porc,$cImp,$cTotal,$idProv,$cDate,$cExch,$cRate,$tot_invitados,$cPropina,$cFolio,$cTotalPesos,$cCargo_asociado_amex,$comentario);
			
			$comensales = new Comensales();
			for ($i = 1; $i <= $_POST['numInvitados']; $i++) {
				$sNombre = $_POST['nombre' . $i];
				$sPuesto = $_POST['puesto' . $i];
				$sTipo = $_POST['tipoinv' . $i];
				$sEmpresa = $_POST['empresa' . $i];
					
				$id_detalle_solicitud_invitacion=add_detalle_solicitud_invitacion("0", $sNombre, $sPuesto, $sEmpresa, $sTipo, $idTramite);
			}
			
			if($t_etapa == COMPROBACION_INVITACION_ETAPA_RECHAZADA || $t_etapa == COMPROBACION_INVITACION_ETAPA_RECHAZADA_POR_DIRECTOR || $t_autorizaciones_historial != ""  || $etapaTramite == COMPROBACION_INVITACION_ETAPA_DEVUELTA_CON_OBSERVACIONES){
				$historial = $_POST['historial_observaciones'];
				// Agregamos el nombre del usuario al campo de Observaciones
				$cnn = new conexion();
				$query = sprintf("UPDATE comprobacion_invitacion SET co_observaciones = '%s' WHERE co_mi_tramite = '%s'", $historial, $tramite_editar);
				//error_log($query);
				$cnn->ejecutar($query);
					
				$tramite = new Tramite();
				$tramite->Modifica_Etapa($tramite_editar, COMPROBACION_INVITACION_ETAPA_SIN_ENVIAR, FLUJO_COMPROBACION_INVITACION, "", "");
					
				// Limpiar el campo de la ruta de autorización
				$query = sprintf("UPDATE tramites SET t_ruta_autorizacion = '' WHERE t_id = '%s'", $tramite_editar);
				//error_log($query);
				$cnn->ejecutar($query);
			}
			
			// Termina transacción
			$tramite->insertar("COMMIT");
			//echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?oksaveprev&docs=docs&type=4'>";
			header("Location: ./index.php?oksaveprev&docs=docs&type=4");
		}
	} else {
		//echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?errsave'>";
		header("Location: ./index.php?errsave");
		//die();
	}//if row
}//if guarda comp

/*
 * Se enviará la Comprobación para el Vobo. del Director
 */
if (isset($_POST['enviaDirector'])){
	require_once("services/func_comprobacion.php");
	require_once("../solicitudes/services/C_SV.php");
	require_once("../../lib/php/constantes.php");
	require_once("../solicitudes/services/C_SV.php");
	require_once("../../functions/Usuario.php");
	require_once("../../functions/RutaAutorizacion.php");
	require_once("../../functions/Notificacion.php");
	require_once("$RUTA_A/functions/utils.php");
	
	function limpiar_invitados($id_tramite){
		$cnn = new conexion();
		$query="delete from comensales_sol_inv where dci_solicitud='".$id_tramite."'";
		//error_log($query);
		$cnn->ejecutar($query);
	}
	
	function limpiar_detalles($id_tramite){
		$cnn = new conexion();
		$query="delete from detalle_comprobacion_invitacion where dc_comprobacion='".$id_tramite."'";
		$cnn->ejecutar($query);
	}
	
	if (isset($_POST['solicitud_de_invitacion']) || $_POST['solicitud_de_invitacion'] != 0) {
		$Sol_Invitacion = $_POST['solicitud_de_invitacion'];
		
		if ($Sol_Invitacion == "" || $Sol_Invitacion == 0 || $Sol_Invitacion == -1 || $Sol_Invitacion == "n") {
			header("Location: ./index.php?errsol");
			die();
		}
		
		// Datos del empleado
		$iduser = 0;
		$delegado = 0;
		//$iduser = $_POST['iu'];
		$idempresa = $_POST["empresa"];
		$motivoComprobacion = $_POST["motive"];
		$comentario = $_POST['comentarios'];
		$cMotive = $motivoComprobacion;
		$sesionDelegado = $_POST['delegado'];
		$etapaCI = $_POST['etapa'];
		
		if($sesionDelegado != 0){
			$iduser = $sesionDelegado;
			$delegado = $_POST["iu"];
		}else{
			$iduser = $_POST["iu"];
		}
		
		// Datos de la Solicitud
		if($_POST['Cecos_refacturado']==1){
			$refacturar = "1";
		}else{
			$refacturar = "0";
		}
		
		$idSolicitud = $_POST["solicitud_de_invitacion"];
		$cnn = new conexion();
		
		$aux = $_POST['centro_de_costos'];
		$Vsql = "SELECT cc_id FROM cat_cecos WHERE cc_centrocostos = $aux";
		$Res_Vsql = $cnn->consultar($Vsql);
		$centroCosto = mysql_result($Res_Vsql, 0, "cc_id");
			
		// Registra nuevo tramite
		$tramite = new Tramite();
		$tramite->insertar("BEGIN WORK");
		
		$cAmt = str_replace(",","",$_POST['monto']);
		$cRate = $_POST['tasa'];
		$fact=$_POST['fact_chk1'];
		
		$cImp = 0;
		$cImp_porc = 0;
		$cProv ="";
		$cP_RFC ="";
		$cFolio ="";
		$cFlagFactura = 0;
		$idProv ="";
		
		if($fact == "on"){
			$cImp = str_replace(",","",$_POST['impuesto']);
			$cImp_porc = 0;
			if($cImp != 0){
				$cImp_porc = "16";
			}
			$cP_RFC = $_POST['rfc'];
			$cFolio = $_POST['d_folio'];
			$cFlagFactura = 1;
			// ID proveedor
			$cnn = new conexion();
			$query = sprintf("SELECT pro_id FROM proveedores WHERE pro_rfc = '%s'", $cP_RFC);
			//error_log($query);
			$rst = $cnn->consultar($query);
			$num_rows = mysql_num_rows($rst);
			$fila = mysql_fetch_assoc($rst);
			$idProv = $fila['pro_id'];
			if ($num_rows > 0)
				$idProv = $fila['pro_id'];
			else
				$idProv = "-1";
		}else{
			$cImp = 0;
			$cImp_porc = 0;
			$cProv ="-1";
			$cP_RFC ="";
			$cFolio ="";
			$cFlagFactura = 0;
			$idProv = "-1";
		}
		
		$cPropina=str_replace(",","",$_POST['propina_dato']);
		//Se obtiene el subtotal
		$co_subtotal = $cAmt + $cPropina;
		//Se obtiene el iva
		$co_iva = $cImp;
		//Total de la comprobacion
		$t_Total_Comprobacion = str_replace(",","",$_POST['total']);
		
		if(isset($_POST['tipo'])&&$_POST['tipo']=="amex"){
			$cTipo_id = 1;
		}else{
			$cTipo_id = 3;
		}
		
		$co_subtotal=$_POST['co_subtotal'];
		$co_iva=$_POST['co_iva'];
		$observ = $_POST['observ'];
		$invitacionLug = $_POST['lugar_inv'];
		$fecha_inv = $_POST['fechainvitacion'];
		$excedente = $_POST['banderavalida'];
		$tot_invitados = $_POST['numInvitados'];
		$co_ciudad = $_POST['co_ciudad_data'];
		$sesionDelegado = $_POST['delegado'];
		$co_amex_comprobado = $_POST['t_amex_comprobado'];
		$co_efectivo_comprobado = $_POST['t_reembolso'];
		
		// Registra nueva comprobacion
		$comprobacion = new Comprobacion();
		
		$concepto = new Concepto();
		$concepto->Load_Concepto_By_Nombre("Comidas  Invitacion");
		// Guarda los detalles de la comprobacion
		$cDate = $_POST['fecha'];
		$cConc = $concepto->Get_Dato("dc_id");		
		
		if(isset($_POST['moneda'])){
			$cExch = $_POST['moneda'];
		}else{
			$cExch = "1";
		}
			
		// Guardar campo del IVA
		if($cExch == "1"){
			$cImp = str_replace(",","",$_POST['impuesto']);
			$cImp_porc = 0;
			if($cImp != 0){
				$cImp_porc = "16";
			}
		}
			
		$divisa = new Divisa();
		$divisa->Load_data($cExch); //busca Id de divisa
		$divisa_nombre = $divisa->Get_dato("div_nombre");
		$cTotal = str_replace(",","",$_POST['total']);
		$cTotalPesos = str_replace(",","",$_POST['monto_pesos']);
		
		// Validar que el concepto no venga vacio
		if (!isset($cConc) || strlen(trim($cConc)) <= 0) {
			continue;
			if($cConc == "Comidas  Invitacion"){
				$cConc = 7;
			}
		}
		
		if(isset($_POST['tramiteID']) && $_POST['tramiteID'] != 0){
			$idTramite = $_POST['tramiteID'];
			$etapaTramite = $_POST['etapa'];
			
			// Volvemos los cargos AMEX a su estado original
			$cnn3 = new conexion();
			$query3 = sprintf("UPDATE amex 
				JOIN detalle_comprobacion_invitacion ON dc_idamex_comprobado = idamex 
				JOIN comprobacion_invitacion ON dc_comprobacion = co_id 
				SET estatus = '0', comprobacion_id = '0', dc_idamex_comprobado = '0' 
				WHERE co_mi_tramite = '%s'", $idTramite);
			//error_log($query3);
			$rst3 = $cnn3->ejecutar($query3);
			
			$tramite->Modifica_Etapa($idTramite, COMPROBACION_INVITACION_ETAPA_EN_APROBACION_POR_DIRECTOR, FLUJO_COMPROBACION_INVITACION, $iduser, "");
			$tramite->Load_Tramite($idTramite);
			$t_autorizaciones_historial = $tramite->Get_dato("t_autorizaciones_historial");
			
			if($etapaCI == COMPROBACION_INVITACION_ETAPA_RECHAZADA || $etapaCI == COMPROBACION_INVITACION_ETAPA_RECHAZADA_POR_DIRECTOR || $t_autorizaciones_historial != ""  || $etapaTramite == COMPROBACION_INVITACION_ETAPA_DEVUELTA_CON_OBSERVACIONES){
				$HObser = $_POST['historial_observaciones'];
			}else{
				$HObser = "";
			}
			
			// Agregamos el nombre del usuario al campo de Observaciones
			if($observ != ""){
				$notificacion = new Notificacion();
				$observ = $notificacion->anotaObservacion($delegado, $HObser, $observ, FLUJO_COMPROBACION_INVITACION, COMPROBACION_INVITACION_ETAPA_EN_APROBACION_POR_DIRECTOR);
			}else{
				$observ = $HObser;
			}
			
			$idComprobacion = $comprobacion->edita_Comprobacion_Invitacion2($t_Total_Comprobacion, $Sol_Invitacion, $co_subtotal, $co_iva, $centroCosto, $cMotive, $cTipo_id, $observ, "", $tot_invitados, $invitacionLug, $fecha_inv, $excedente, $co_ciudad, $co_amex_comprobado, $co_efectivo_comprobado, $idTramite);
			
			limpiar_detalles($idComprobacion);
			limpiar_invitados($idTramite);
			
		}else{
			$idTramite = $tramite->Crea_Tramite($iduser, $idempresa, COMPROBACION_INVITACION_ETAPA_EN_APROBACION_POR_DIRECTOR, FLUJO_COMPROBACION_INVITACION, $cMotive, $delegado);
			$idComprobacion = $comprobacion->Crea_Comprobacion_Invitacion2($t_Total_Comprobacion, $Sol_Invitacion, $co_subtotal, $co_iva, $idTramite, $centroCosto, $cMotive, $cTipo_id, $observ, "", $tot_invitados, $invitacionLug, $fecha_inv, $excedente, $co_ciudad, $co_amex_comprobado, $co_efectivo_comprobado);
		}
		
		if(isset($_POST['tipo']) && $_POST['tipo']=="amex"){
			$t_Anticipo_Amex=$_POST['t_amex_comprobado'];
			$t_Total_Comprobacion=$_POST['t_comprobado'];
			
			if($_POST['select_tarjeta_cargo']!=0){
				$cCargo_asociado_amex=$_POST['select_tarjeta_cargo'];
				$cnn3 = new conexion();
				$id_cargo_amex_anterior = $_POST['select_tarjeta_cargo'];
				$query3 = sprintf("UPDATE amex SET estatus = '1', comprobacion_id = '%s' WHERE idamex = '%s'", $idComprobacion, $cCargo_asociado_amex);
				//error_log($query3);
				$rst3 = $cnn3->ejecutar($query3);
			}else{
				$cCargo_asociado_amex=0;
			}		
			// Detalle Comprobación
			$comprobacion->Agrega_Detalle_Comp_Invitacion2($idComprobacion,$cConc,$cP_RFC,$cAmt,$cImp_porc,$cImp,$cTotal,$idProv,$cDate,$cExch,$cRate,$tot_invitados,$cPropina,$cFolio,$cTotalPesos,$cCargo_asociado_amex,$comentario);
		}else{
			$t_Anticipo_Amex=$_POST['t_amex_comprobado'];
			$cCargo_asociado_amex=0;
			$comprobacion->Agrega_Detalle_Comp_Invitacion2($idComprobacion,$cConc,$cP_RFC,$cAmt,$cImp_porc,$cImp,$cTotal,$idProv,$cDate,$cExch,$cRate,$tot_invitados,$cPropina,$cFolio,$cTotalPesos,$cCargo_asociado_amex,$comentario);			
		}
		
		$comensales = new Comensales();
		for ($i = 1; $i <= $_POST['numInvitados']; $i++) {
			$sNombre = $_POST['nombre' . $i];
			$sPuesto = $_POST['puesto' . $i];
			$sTipo = $_POST['tipoinv' . $i];
			$sEmpresa = $_POST['empresa' . $i];
			$id_detalle_solicitud_invitacion=add_detalle_solicitud_invitacion("0", $sNombre, $sPuesto, $sEmpresa, $sTipo, $idTramite);
		}
		
		$duenoActual = new Usuario();
		$duenoActual->Load_Usuario_By_ID($delegado);
		$nombreUsuario = $duenoActual->Get_dato('nombre');
		$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> en su nombre y requiere de su autorizaci&oacute;n.", $idTramite, $nombreUsuario);
		
		$remitente = $delegado;
		$destinatario = $iduser;
		$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "1", ""); //"0" para no enviar email y "1" para enviarlo
		$tramite->actualizaDelegado($idTramite, $delegado);
		
		// Termina transacción
		$tramite->insertar("COMMIT");
		//echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?oksave&docs=docs&type=4'>";
		header("Location: ./index.php?oksave&docs=docs&type=4");
	} else {
		//echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?errsave'>";
		header("Location: ./index.php?errsave");
	}
}

if (isset($_GET['comp_solicitud'])) {
	require_once("$RUTA_A/functions/utils.php");
	
	function forma_comprobacion() {
		$tipoUsuario = $_SESSION["perfil"];
		?>
<!-- Inicia forma para comprobación -->
<script language="JavaScript" src="js/backspaceGeneral.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.date_input.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.tablesorter.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.autocomplete.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.bgiframe.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.tableEditor.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.fadeSliderToggle.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.blockUI.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/formatNumber.js" type="text/javascript"></script>
<script language="JavaScript" src="js/communication_ajax.js" type="text/javascript"></script>
<script language="JavaScript" type ="text/javascript" src="../../lib/js/jquery/jquery.jdpicker.js"></script>
<script language="JavaScript" type ="text/javascript" src="../../lib/js/jquery/jquery.jdpicker2.js"></script>
<link rel="stylesheet" href="../../css/jdpicker.css" type="text/css" media="screen" />
<link rel="stylesheet" href="../../css/jdpicker2.css" type="text/css" media="screen" />
<script language="JavaScript" src="../solicitudes/js/solicitud_viaje.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
//variables
var doc;
var arreglovalores=new Array();
var arregloestatus=new Array();
var arreglodescripcion=new Array();

doc = $(document);
doc.ready(inicializarEventos);//cuando el documento esté listo
function inicializarEventos(){
	var frm=document.invitacion_comp;
	//genera la lista de sugerencia: nombre proveedor
	$("#proveedor").autocomplete("catalogo_proveedores.php", {
		minChars:1,
		matchSubset:1,
		matchContains:1,
		cacheLength:10,
		onItemSelect:seleccionaItem2,
		onFindValue:buscaRFC,
		formatItem:arreglaItem,
		maxItemsToShow:5,
		autoFill:false,
		extraParams:{tip:1}
	});//fin autocomplete

	//genera la lista de sugerencia: rfc proveedor
	$("#rfc").autocomplete("catalogo_proveedores.php", {
		minChars:1,
		matchSubset:1,
		matchContains:1,
		cacheLength:10,
		onItemSelect:seleccionaItem,
		onFindValue:buscaProveedor,
		formatItem:arreglaItem,
		maxItemsToShow:5,
		autoFill:false,
		extraParams:{tip:2}
	});//fin autocomplete

	// Se usa para controlar el div de agregar proveedor
	$(".fadeNext").click(function(){
		$(this).next().fadeSliderToggle()

		return false;
	});

	$("#fecha").jdPicker({
		date_format:"dd/mm/YYYY", 
		//date_min:"<?php echo date("d/m/Y"); ?>",
		month_names: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
		short_month_names: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
		short_day_names: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sab"]
	});

	if(navigator.appName!='Netscape'){
		document.getElementById('msg_div').style.width="800px";
		document.getElementById('msg_div').style.height="300px";
		document.getElementById('msg_div').style.top="25%";
		document.getElementById('msg_div').style.left="18.5%";
	}
	
	$('#fecha').keydown(function(e){
		ignoraEventKey(e);				
	});

	$("input").bind("keydown", function(e){
		if(!isAlphaNumeric(e)) return false;
	});

	//bloqueo de teclas en caracteres extraños para proveedor
	
	$("#new_proveedor").bind("keydown", function(e){
		if(!isAlphaNumericRFC(e)) return false;
	});

	$("#new_p_rfc").bind("keydown", function(e){					
		if(!isAlphaNumericRFC(e)) return false;
	});

	$("#rfc").bind("keydown", function(e){					
		if(!isAlphaNumericRFC(e)) return false;
	});		
	
	//Validaciones para  TAB + backspace 
	$('#agregarInv').focus(function() {
		confirmaRegreso('agregarInv');
	});
	
	$('#add_rem_prv').focus(function() {
		confirmaRegreso('add_rem_prv');
	});

	$('#agregar').focus(function() {
		confirmaRegreso('agregar');
	});
	
	$('#guardarComp').focus(function() {
		confirmaRegreso('guardarComp');
	});

	$('#guardarCompprev').focus(function() {
		confirmaRegreso('guardarCompprev');
	});

	$('#enviaDirector').focus(function() {
		confirmaRegreso('enviaDirector');
	});
	
	$('#fact_chk').focus(function() {
		confirmaRegreso('fact_chk');
	});	

	$('#cambia_table').focus(function() {
		confirmaRegreso('cambia_table');
	});

	//funcion para limitar comentarios del gasto a 36 caracteres
	 $('#comentarios').keyup(function(){  
		 var limit = parseInt($(this).attr('maxlength')); 
		 var text = $(this).val(); 
		 var chars = text.length; 
		 if(chars > limit){  	             
	            var new_text = text.substr(0, limit);              
	            $(this).val(new_text);	            
	        } 
   });
	   
}//fin ready ó inicializarEventos

function enviar_formulario(){
	document.invitacion_comp.submit();
}

//solicitar confirmación de previo
function solicitarConfirmPrevio(){
	 var frm=document.invitacion_comp;
	if(confirm("¿Desea guardar esta Comprobación como previo?")){
		if($("#tipo").val() == -1){
			alert("Seleccione el tipo de comprobación que desea Guardar.");
			return false;
		}else if($("#centro_de_costos").val() == -1){
			alert("Seleccione un Centro de Costos.");
			return false;
		}else{
			frm.submit();
		}
	}else{
		return false;
	}
}

//Solicitar confirmación de guardado
function solicitarConfirmarGuardado(){
	 var frm=document.invitacion_comp;
	if(confirm("¿Desea enviar la Comprobación?")){
		return true;
	}else{
		return false;
	}
}
   
function guardaComprobacionprev(){
	var frm=document.invitacion_comp;
	if(parseInt($("#solicitud_de_invitacion").val())>-1)
	{
		$("#guardarCompprev").removeAttr("disabled");
	}
	else
	{
		$("#guardarCompprev").attr("disabled", "disabled");
	}
}

function buscaProveedor(li) {
    var rfc_pro = $("#rfc").val();        
    var url = "services/catalogo_proveedores.php";
    $.post(url,{nombre:rfc_pro,tip:1},function(data){
            $("#proveedor").val(data);
            $("#load_div").html("");                
    });
}//fin buscaProveedor

function buscaRFC(li) {
    var name_pro = $("#proveedor").val();        
    var url = "services/catalogo_proveedores.php";
    $.post(url,{nombre:name_pro,tip:2},function(data){
            $("#rfc").val(data);
            $("#load_div").html("");                
    });
}//fin buscaRFC

function seleccionaItem(li) {
buscaProveedor(li);
}//fin seleccionaItem

function seleccionaItem2(li) {
buscaRFC(li);
}//fin seleccionaItem

function arreglaItem(row) {
//da el formato a la lista
return row[0];
}//fin arreglaItem

function tipo_de_comprobacion(valor){
	var frm=document.invitacion_comp;
	
	if(frm.tipo.value == "amex"){
		$("#seccion_amex").slideDown(500);
		$("#seccion_amex").css("display", "block");
		$("#seccion_amex").addClass("visible")
	}else{
		//ocultar "seccion_amex"
		if($("#seccion_amex").hasClass("visible")){
			$("#seccion_amex").slideUp(500);
			$("#seccion_amex").removeClass("visible");
			$("#fact_chk").removeAttr("disabled");
			//$("#seccion_amex").css("display", "none");
		}
	}
	if(valor == "reembolso_para_empleado"){
		$("#g_reembolso").html($("#monto_pesos").val().replace(/,/g,"")+" MXN");
		$("#t_reembolso").val($("#monto_pesos").val());
		$("#g_amex_comprobado").html("0.00 MXN");
		$("#t_amex_comprobado").val(0);
		$("#g_comprobado").html($("#monto_pesos").val().replace(/,/g,"")+" MXN");
		$("#t_comprobado").val($("#monto_pesos").val());
	}else if(valor == "amex"){
		$("#g_amex_comprobado").html($("#monto_pesos").val().replace(/,/g,"")+" MXN");
		$("#t_amex_comprobado").val($("#monto_pesos").val());
		$("#g_reembolso").html("0.00 MXN");
		$("#t_reembolso").val(0);
		$("#g_comprobado").html($("#monto_pesos").val().replace(/,/g,"")+" MXN");
		$("#t_comprobado").val($("#monto_pesos").val());
	}else if(valor == "-1"){
		$("#g_amex_comprobado").html("0.00 MXN");
		$("#t_amex_comprobado").val(0);
		$("#g_reembolso").html("0.00 MXN");
		$("#t_reembolso").val(0);
		$("#g_comprobado").html("0.00 MXN");
		$("#t_comprobado").val(0);
	}
	limpiar_cuadro_amex();
}

function verificar_tipo_tarjeta(){
	var frm= document.invitacion_comp;
	var tipo_tarjeta=frm.select_tipo_tarjeta.value;
	var noemple=<?php 
	$varDelegado = 0;
	if(isset($_SESSION['iddelegado'])){
		$varDelegado = $_SESSION['iddelegado'];
	}
	$iduser = verificaSesion($_SESSION["idusuario"], $varDelegado); 
	echo $iduser;?>;
	
	if(tipo_tarjeta != "" && tipo_tarjeta != "-1"){
		$.ajax({
			type: "POST",
			url: "services/Ajax_comprobacion.php",
			data: "tipo_tarjeta="+tipo_tarjeta+"&usuario="+noemple,
			dataType: "html",
			success: function(dato){
				if(dato==""){
					$("#no_tarjeta_credito").html("Datos no Encontrados");
					$("#select_tarjeta_cargo").empty();
					$("#select_tarjeta_cargo").append('<option value="-1">Sin Datos</option>');
				}else{
					$("#no_tarjeta_credito").html(dato);
					obtenercargos(dato);
				}
			}
		});
	}else{
		limpiar_cuadro_amex();
	}
}

function limpiar_cuadro_amex(){
	var frm= document.invitacion_comp;
	arreglovalores.splice(0,arreglovalores.length);
	arregloestatus.splice(0,arregloestatus.length);
	arreglodescripcion.splice(0,arreglodescripcion.length);
	LimpiarCombo(frm.select_tarjeta_cargo);
	
	$("#fecha_cargo").html("");
	$("#establecimiento_cargo").html("");
	$("#monto_cargo").html(""); 
	$("#rfc_cargo").html("");
	$("#moneda_local").html("");
	$("#amex_pesos").html("");
	$("#amex_dolar").html("");
	$("#moneda_fact_val").val("");
	$("#fecha_cargo_val").val("");
	$("#establecimiento_cargo_val").val("");
	$("#monto_cargo_val").val("");
	$("#amex_pesos_val").val("");
	$("#amex_dolar_val").val("");
	$("#rfc_cargo_val").val("");
	$("#moneda_local_val").val("");
	$("#no_tarjeta_credito").html("");
	
	$("#select_tipo_tarjeta").val("-1");
}

function obtenercargos(valor){
	var frm=document.invitacion_comp;
	var tramite = $("#tramiteID").val();
	LimpiarCombo(frm.select_tarjeta_cargo);
	if(valor != ""){
		$.ajax({
			type: "POST",
			url: "services/Ajax_comprobacion.php",
			data: "no_tarjeta="+valor+"&comprobacion="+tramite,
			dataType: "json",
			timeout: 10000,
			success: function(json){
				if(json==null){
					$("#select_tarjeta_cargo").append(new Option("Sin Datos"));
				}else{
					arreglovalores.splice(0,arreglovalores.length);
					arregloestatus.splice(0,arregloestatus.length);
					arreglodescripcion.splice(0,arreglodescripcion.length);
					LlenarCombo(json, frm.select_tarjeta_cargo);
					LlenarCombo2(arreglovalores,arreglodescripcion,arregloestatus,frm.select_tarjeta_cargo);
				}
			},
			error: function(x, t, m) {
				if(t==="timeout") {
					location.reload();
					abort();
				} 
			}
		});
	}
}

function LimpiarCombo(combo){
while(combo.length > 0){
	combo.remove(combo.length-1);
}
}
function LlenarCombo(json, combo){
	combo.options[0] = new Option('Selecciona un item', '');
	for(var i=0;i<json.length;i++){
		var str=json[i];
		var str1=str.slice(str.search(":")+1);
		var str2=str.substr(0,str.search(":"));
		//combo.options[combo.length] = new Option(str1,str2);
		arreglovalores[arreglovalores.length]=str1;
		arreglodescripcion[arreglodescripcion.length]=str2;
		if(arregloestatus[arregloestatus.length]!=false)
		arregloestatus[arregloestatus.length]=true;	
	}
}

function LlenarCombo2(arregloval,arreglodesc,arreglosts,combo){
	var frm=document.invitacion_comp;

	LimpiarCombo(combo);
	combo.options[0]=new Option('Selecciona un item', '');
	for(var i=0;i<arregloval.length;i++){
		if(arreglosts[i]==true){
			combo.options[combo.length] = new Option(arregloval[i],arreglodesc[i]);
			//alert(combo.options[i+1].value);
			if(combo.options[i+1].value == frm.id_cargo_amex_seleccionado.value){
				combo.options[i+1].selected = true;
				cargar_detalles();
			}
		}
	}
}

function cambiarestatusamex(valor,estatus){
	for(var i=0;i<arreglovalores.length;i++){
		if(arreglovalores[i]==valor){
			arregloestatus[i]=estatus;
			}
		}
}

// Función para rellenar combos.
	function cargar_detalles(){
		var frm=document.invitacion_comp;
		var cargo_localizar=frm.select_tarjeta_cargo.value;
		$.ajax({
			type: "POST",
			url: "services/Ajax_comprobacion.php",
			data: "no_cargo="+cargo_localizar,
			dataType: "json",
			success: function(json){	
				if(json==""){
					//$("#no_corporacion").html("Datos no Encontrados");
					$("#fecha_cargo").html("Datos no Encontrados");
					$("#establecimiento_cargo").html("Datos no Encontrados");
					$("#monto_cargo").html("Datos no Encontrados"); 
					$("#rfc_cargo").html("NA");
					$("#moneda_local").html("Datos no Encontrados");
					$("#amex_pesos").html("Datos no Encontrados");
					$("#amex_dolar").html("Datos no Encontrados");
					//$("#no_corporacion_val").val("");
					$("#moneda_fact_val").val("");
					$("#fecha_cargo_val").val("");
					$("#establecimiento_cargo_val").val("");
					$("#monto_cargo_val").val("");
					$("#amex_pesos_val").val("");
					$("#amex_dolar_val").val("");
					$("#rfc_cargo_val").val("");
					$("#moneda_local_val").val("");
	
				}else{					
		               $("#rfc_cargo").html(json[0].rfc_establecimiento);
					   $("#rfc_cargo_val").val(json[0].rfc_establecimiento);
					   //$("#no_corporacion").html(json[0].corporacion);
		               $("#fecha_cargo").html(json[0].fecha_cargo);   
		               $("#establecimiento_cargo").html(json[0].concepto);   
		               $("#monto_cargo").html(number_format(json[0].monto,2,".",",")+" "+json[0].moneda_local);
		               //$("#amex_pesos").html("$"+json[0].monto);
		               $("#amex_dolar").html(number_format(json[0].montoAmex,2,".",",")+" "+json[0].monedaAmex);
		               $("#amex_dolar_val").val(json[0].montoAmex);
		               //$("#rfc_cargo").html(json[0].rfc_establecimiento);   
		               $("#moneda_local").html(json[0].moneda_local);
		               //$("#no_corporacion_val").val(json[0].corporacion);
		               $("#moneda_fact_val").val(json[0].monedaAmex);   
		               $("#fecha_cargo_val").val(json[0].fecha_cargo);   
		               $("#establecimiento_cargo_val").val(json[0].concepto);   
		               $("#monto_cargo_val").val(json[0].monto);
		               //$("#amex_pesos_val").val(json[0].monto);
		               $("#moneda_local_val").val(json[0].moneda_local);
		               
		               var tipo_cambio = parseFloat(json[0].montoAmex/json[0].monto);
						$("#tipo_cambio").val(tipo_cambio.toFixed(2));
						$("#div_tipo_cambio").html(tipo_cambio.toFixed(5));
		               
		               if(json[0].conversion_pesos != "" || json[0].conversion_pesos != null){
							$("#amex_pesos").html(number_format(json[0].conversion_pesos,2,".",","));
							$("#amex_pesos_val").val(json[0].conversion_pesos);
						}else{
							$("#amex_pesos").html("0");
							$("#amex_pesos_val").val("0");
						}
	
		               if(json[0].rfc_establecimiento == "" || json[0].rfc_establecimiento == null){
							$("#fact_chk").attr("checked",false);
							$("#fact_chk").removeAttr("disabled");
							verDatosProveedor();
						}else{
							$("#fact_chk").attr("checked",true);
							$("#fact_chk").attr("disabled",true);
							verDatosProveedor();
						}
				}
			}
		});
	}

// Muestra el div y el campo de IVA
function muestraIva(){
	var divisa = $("#moneda").val();
	if (divisa == "MXN"){
		$("#div_iva").css("display", "block"); 
		$("#impuesto").css("display", "block"); 
		$("#impuesto").val($("#monto").val()*.16);
		$("#totalDisabled").val( parseFloat($("#monto").val()) + parseFloat($("#impuesto").val()) );
		$("#total").val( parseFloat($("#monto").val()) + parseFloat($("#impuesto").val()) );									
	}else if(!$("#fact_chk").is(":checked")){
		$("#div_iva").css("display", "none"); 
		$("#impuesto").css("display", "none"); 				
		$("#impuesto").val(0);
		$("#totalDisabled").val( parseFloat($("#monto").val()));
		$("#total").val( parseFloat($("#monto").val()));	
	}			
	calculaTotalDolares();
}

function calculaTotalDolares(){
	var divisa = $("#moneda option:selected").text();
	var monto = parseFloat($("#monto").val().replace(/,/g,""));
	var iva = ($("#impuesto").val() =="")? 0 : parseFloat($("#impuesto").val().replace(/,/g,""));				
	var propina = ($("#propina_dato").val() == "") ? 0 : parseFloat($("#propina_dato").val().replace(/,/g,""));
	var total = parseFloat(iva)+parseFloat(monto)+parseFloat(propina);
	total = total.toFixed(2);
	
	$("#total").val(number_format(total,2,".",","));
	var montoPesos = 0.00;
	$.ajax({
		type: "POST",
		url: "services/Ajax_comprobacion.php",
		data: "convierteDivisa="+total+"&divisa="+divisa,
		dataType: "json",
		timeout: 10000,
		success: function(json){
			montoPesos = json;
		},
		complete: function(json){
			$("#monto_pesos").val(number_format(montoPesos,2,".",","));
		}
	});
}

function obtener_tasa_USD(){
	var cargo_val = parseFloat($("#monto_cargo_val").val());
	var dolar = parseFloat($("#valorDivisaUSD").val());
	var euro = parseFloat($("#valorDivisaEUR").val());

	if($("#moneda_fact_val").val() == "MXN"){
		var monto_dolar = (cargo_val / dolar);
		var redondea_dolar = Math.round(monto_dolar * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
		$("#amex_dolar").html(number_format(redondea_dolar,2,".",",")+" USD");
		$("#amex_dolar_val").val(redondea_dolar);
		$("#amex_pesos").html(number_format(cargo_val,2,".",",")+" MXN");
		$("#amex_pesos_val").val(cargo_val);
	}else if($("#moneda_fact_val").val() == "EUR"){
		var monto_pesos = (cargo_val * euro);
		var monto_dolar = (monto_pesos / dolar);
		var redondea_dolar = Math.round(monto_dolar * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
		$("#amex_dolar").html(number_format(redondea_dolar,2,".",",")+" USD");
		$("#amex_dolar_val").val(redondea_dolar);
		$("#amex_pesos").html(number_format(cargo_val,2,".",",")+" MXN");
		$("#amex_pesos_val").val(cargo_val);
	}else{
		var monto_dolar = (cargo_val * dolar);
		var redondea_dolar = Math.round(monto_dolar * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
		$("#amex_dolar").html(number_format(cargo_val,2,".",",")+" USD");
		$("#amex_dolar_val").val(cargo_val);
		$("#amex_pesos").html(number_format(redondea_dolar,2,".",",")+" MXN");
		$("#amex_pesos_val").val(redondea_dolar);
	}
}

// Convierte una fecha del formato "2011-02-27 00:00:00" al formato "27/02/2011"
function fecha_to_mysql(strFecha){
	var toks1=strFecha.split(" ");
	var toks=toks1[0].split("-");

	var strFechaN = toks[2]+"/"+toks[1]+"/"+toks[0];
	return strFechaN;
}

function fecha_to_mysql_normal(strFecha){
	var toks1=strFecha.split(" ");
	var toks=toks1[0].split("-");

	var strFechaN = toks[0]+"/"+toks[1]+"/"+toks[2];
	return strFechaN;
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
//validación campos numericos
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
	//valor = number_format(valor,0,".",",")
	valor = number_format_sin_redondeo(valor);
	return valor;
}

function getTotal(){
	var frm=document.invitacion_comp;

	var propinaDato = frm.propina_dato.value;
	propinaDato = propinaDato.replace(/,/g,"");

	var montoValue = frm.monto.value;
	montoValue = montoValue.replace(/,/g,"");
	
	var impuestoValue = frm.impuesto.value;
	impuestoValue = impuestoValue.replace(/,/g,"");
	
	if (impuestoValue == 0 || impuestoValue == ""){
		if(propinaDato == "" || propinaDato == 0){
			if(montoValue == "" || montoValue == 0){
				frm.totalDisabled.value=0;
				frm.total.value=0;
			} else {
				//total
				var tot = parseFloat(montoValue);
				frm.totalDisabled.value =Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
				frm.total.value =Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			}
		} else {
			if(montoValue == "" || montoValue == 0){
				var tot = parseFloat(propinaDato);
				frm.totalDisabled.value =Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
				frm.total.value =Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			} else {
				//total
				var tot = parseFloat(montoValue)+parseFloat(propinaDato);
				frm.totalDisabled.value = Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
				frm.total.value = Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			}
		}
	} else { 
		if(propinaDato == "" || propinaDato == 0){
			if(montoValue == "" || montoValue == 0){
				var tot = parseFloat(impuestoValue);
				frm.totalDisabled.value = Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
				frm.total.value = Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			} else {
				//total
				var tot = parseFloat(montoValue) + parseFloat(impuestoValue);
				frm.totalDisabled.value =Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
				frm.total.value =Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			}
		} else {
			if(montoValue == "" || montoValue == 0){
				var tot = parseFloat(propinaDato) + parseFloat(impuestoValue);
				frm.totalDisabled.value =Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
				frm.total.value =Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			} else {
				//total
				var tot = parseFloat(montoValue)+parseFloat(propinaDato) + parseFloat(impuestoValue);
				frm.totalDisabled.value = Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
				frm.total.value = Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			}
		}
	}
	frm.totalDisabled.value = number_format(frm.total.value,2,".",",");
	frm.total.value = number_format(frm.total.value,2,".",",");
}

function evaluaMonto(monto){
	$("#capaWarning").html("");
    var mensajeExcedePoliticas = undefined;
    //Variables de las cajas de Texto 
    var divEuro = parseFloat($("#valorDivisaEUR").val());
    //var monto = parseFloat($("#tpesos").val());
    var num_invitados = parseFloat($("#numInvitados").val());
    //Variable para guardar 
    var monto2 = 0;
    monto2 = ((monto / divEuro) / num_invitados );
    
    if(monto2 > 50 && mensajeExcedePoliticas == undefined){
    	mensajeExcedePoliticas = "<strong>Esta rebasando la pol&iacute;tica del concepto. <br>El monto m&aacute;ximo es de 50.00 Euros por persona.</strong>";
    	conceptoExcedePoliticas = true;                                        
    } else {
    	conceptoExcedePoliticas = false;
    }
    
    if(conceptoExcedePoliticas){
        $("#capaWarning").html(mensajeExcedePoliticas);
        $("#obsjus").html("Agregar justificaci&oacute;n detallada del motivo del excedente<span class='style1'>*</span>:");
        //$("#comment").html("Comentarios <span class='style1'>*</span>: "); 
        document.getElementById("banderavalida").value = 1;                   
    } else {
    	$("#obsjus").html("Observaciones: ");
    	//$("#comment").html("Comentarios: "); 
    	document.getElementById("banderavalida").value = 0;
    }
}

function recalculaMontos(){
    var total = parseFloat(($("#total").val()).replace(/,/g,""));
    var totalAnticipo = 0;
	var divisas = $("#moneda").val();

	var tasaNueva = 1;
	if(divisas != 1){ //Si la divisa es diferente a MXN
		//Se obtiene las tasas de las divisas
		var tasa = "<?
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
	document.getElementById("tasa").value = tasaNueva;

	var redondear = 0;
	var frm=document.invitacion_comp;
	var totalTotal = total * parseFloat(tasaNueva);
	var redondear = Math.round(totalTotal * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
	//document.invitacion_comp.total.value = Math.round(totalTotal * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
	document.invitacion_comp.monto_pesosDisabled.value = number_format(redondear,2,".",",");
	document.invitacion_comp.monto_pesos.value = number_format(redondear,2,".",",");
	if(document.invitacion_comp.tipo.value == "reembolso_para_empleado"){
		$("#g_reembolso").html(number_format(redondear,2,".",",")+" MXN");
		$("#t_reembolso").val(redondear);
		$("#g_amex_comprobado").html("0.00 MXN");
		$("#t_amex_comprobado").val(0);
		$("#g_comprobado").html(number_format(redondear,2,".",",")+" MXN");
		$("#t_comprobado").val(redondear);
	}else if(document.invitacion_comp.tipo.value == "amex"){
		$("#g_amex_comprobado").html(number_format(redondear,2,".",",")+" MXN");
		$("#t_amex_comprobado").val(redondear);
		$("#g_reembolso").html("0.00 MXN");
		$("#t_reembolso").val(0);
		$("#g_comprobado").html(number_format(redondear,2,".",",")+" MXN");
		$("#t_comprobado").val(redondear);
	}else if(document.invitacion_comp.tipo.value == "-1"){
		$("#g_amex_comprobado").html("0.00 MXN");
		$("#t_amex_comprobado").val(0);
		$("#g_reembolso").html("0.00 MXN");
		$("#t_reembolso").val(0);
		$("#g_comprobado").html("0.00 MXN");
		$("#t_comprobado").val(0);
	}	
    evaluaMonto(totalTotal);
}

function getTotal2(){
	var frm=document.invitacion_comp;
	if(frm.propina_dato.value=="")
		frm.propina_dato.value=0;				
	frm.select_impuesto.value=0;
	if(frm.impuesto.value!="" && frm.impuesto.value>0 )
		frm.totalDisabled.value=parseFloat(frm.impuesto.value)+parseFloat(frm.monto.value)+parseFloat(frm.propina_dato.value);
		frm.total.value=parseFloat(frm.impuesto.value)+parseFloat(frm.monto.value)+parseFloat(frm.propina_dato.value);
}

//IVA
function valor($combo){
	var frm=document.invitacion_comp;
	if(frm.monto.value=="" || frm.monto.value==0)
	{
		frm.monto.value=0;
		frm.impuesto.value=0;
		frm.totalDisabled.value=0;
		frm.total.value=0;
		if(frm.select_impuesto.value==0)
		{
			frm.impuesto.value=0;
			frm.impuesto.disabled=true;
			frm.totalDisabled.value=parseFloat(frm.monto.value);
			frm.total.value=parseFloat(frm.monto.value);
		}
		else
		{
			//frm.impuesto.disabled=false;
			//impuesto
			Total = frm.total.value;
			Iva = ((frm.select_impuesto.options[frm.select_impuesto.selectedIndex].value / 100)+1);
			var imp = Total / Iva;
			//SubTotal
			frm.impuesto.disabled=false;
			frm.monto.value=Math.round(imp * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			//Iva
			MontoIva = (Iva - 1) * imp;
			var tot=MontoIva;
			frm.impuesto.value=Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
		}
	}
	else
	{
		if(frm.select_impuesto.value==0)
		{
			frm.impuesto.value=0;
			frm.impuesto.disabled=true;
			frm.totalDisabled.value=parseFloat(frm.monto.value);
			frm.total.value=parseFloat(frm.monto.value);
		}
		else
		{
			//impuesto
			var imp=(frm.select_impuesto.options[frm.select_impuesto.selectedIndex].value*0.01)*(frm.monto.value);
			frm.impuesto.disabled=false;
			frm.impuesto.value=Math.round(imp * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			//total
			var tot=parseFloat(frm.impuesto.value)+parseFloat(frm.monto.value);
			frm.totalDisabled.value=Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			frm.total.value=Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
		}

	}
}//fin valor

//Checar fechas
function checafecha(strFecha, diasmax){
	var toks=strFecha.split("/");

	strFechaN=toks[2]+"/"+toks[1]+"/"+toks[0];
	var fecha = new Date();
	fecha.setTime(Date.parse(strFechaN));
	var today=new Date()
	var agedays = Math.floor((today - fecha) / 24 / 60 / 60 / 1000);
	if(agedays>diasmax){
		var hoystr=<?php echo "\"" . date("d/m/Y") . "\";"; ?>
		document.invitacion_comp.fecha.value=hoystr;
		return false;
	}else{
		return true;
	}
}
//FIN checar fechas

function limpiarCamposDeProveedor(valor){
	if(valor){
		$("#new_proveedor").val("");
		$("#new_p_rfc").val("");
		$("#new_p_addr").val("");
	}else{
		$("#proveedor").val("");
		$("#rfc").val("");
		$("#d_folio").val("");
	}
}

// Ver los datos del proveedor
function verDatosProveedor(){
	var frm=document.invitacion_comp;
	if(frm.fact_chk.checked){
		$("#iva_label").css("display", "block");
		$("#impuesto").css("display", "block");
		$("#datosProveedor").css("display", "block");
		frm.moneda.selectedIndex = 1;
		activaIva();
		getTotal();
		recalculaMontos();
		$("#fact_chk1").val("on");		
	}else{
		$("#iva_label").css("display", "none");
		$("#impuesto").css("display", "none");
		$("#datosProveedor").css("display", "none");
		activaIva();
		getTotal();
		recalculaMontos();
		limpiarCamposDeProveedor(0);
	}
}
//FIN ver los datos del proveedor

// Activar campo IVA
function activaIva(){
	var proveedor_amex = ($("#fact_chk").is(':checked'))?true:false;
	var moneda = $("#moneda option:selected").val();
	if(moneda == 1 || proveedor_amex){
		var monto = $("#monto").val()*.16;
		$("#iva_label").css("display", "block");
		$("#impuesto").css("display", "block");
		$("#impuesto").val(monto.toFixed(2));
		getTotal();
		recalculaMontos();		
	}else if(!$("#fact_chk").is(":checked")){
		$("#iva_label").css("display", "none");
		$("#impuesto").css("display", "none");
		$("#impuesto").val(0);
		getTotal();
		recalculaMontos();
	}
}
//FIN de activar campo IVA

//Divisa
function cambiaTasa(divisa){
	var frm=document.invitacion_comp;
	if(divisa != ""){
		$.ajax({
			type: "POST",
			url: "services/Ajax_comprobacion.php",
			data: "divisa="+divisa+"&divisa2='1'",
			dataType: "json",
			success: function(json){
				if(json==null){
				}else{
					document.getElementById("tasa").value = json[0];
				}
			}
		});
	}
}

function seleccionar_centro_de_costos(valor){
var frm=document.invitacion_comp;
//centro_de_costos;
document.getElementById("tipo").removeAttribute("disabled");
document.getElementById("centro_de_costos").removeAttribute("disabled");

if (frm.solicitud_de_invitacion.selectedIndex ==0 || frm.solicitud_de_invitacion.value=="-1"){
	document.getElementById("tipo").setAttribute("disabled","disabled");
	document.getElementById("centro_de_costos").setAttribute("disabled","disabled");
}
if(valor != "" && valor != "-1"){
	$.ajax({
		type: "POST",
		url: "services/Ajax_comprobacion.php",
		data: "t_id="+valor,
		dataType: "json",
		success: function(json){
			if(json==null){
			}else{
				seleccionar(json[0]);
			}
		}
	});
}
}
//Seleccionar elemento de un combo
function seleccionar(elemento) {
   var combo = document.invitacion_comp.centro_de_costos;
   var cantidad = combo.length;
   for (i = 1; i < cantidad; i++) {
      var toks=combo[i].text.split("-");
      if (toks[0] == elemento) {
         combo[i].selected = true;
		 break;
      }
   }
}

//Cargar invitados en la tabla
function cargarInvitados(valor){
	var frm=document.invitacion_comp;
	if(valor != "" && valor!="-1"){
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
		
		$.ajax({
			type: "POST",
			url: "services/Ajax_comprobacion.php",
			data: "t_id2="+valor,
			dataType: "json",
			timeout: 10000, 
			success: function(json){			
				if(json==null){
					VaciarTabla();
					document.getElementById("numInvitadosDisabled").value = 0;
					document.getElementById("numInvitados").value = 0;
				}else{
					VaciarTabla();
					LlenarTabla(json, frm.invitado_table);
				}
			},
			complete: function(json){
				$.unblockUI();
			},
			error: function(x, t, m){
				if(t==="timeout"){
					location.reload();
					abort();
				}else{
					cargarInvitados(valor);
				}
			}
		});
	}
}

function cargarCiudad(valor){
	if(valor!="" && valor!="-1"){
		$.ajax({
			type: "POST",
			url: "services/Ajax_comprobacion.php",
			data: "sol_ciud="+valor,
			success: function(json){			
				if(json==null){
					$("#ciudad_data").html("Datos no encontrados");
				}else{
					$("#ciudad_data").html(json);
					$("#co_ciudad_data").val(json);
				}
			}
		});
		}
}

function cargarLugar(valor){
	if(valor!="" && valor!="-1"){
		$.ajax({
			type: "POST",
			url: "services/Ajax_comprobacion.php",
			data: "idsol="+valor,
			success: function(json){
				if(json == null){
					document.getElementById("lugar_inv").value = "NULL";
				} else {
					document.getElementById("lugar_inv").value = json;
				}
			}
		});
	}
}

function cargarFecha(valor){
	var finvitacion = "";
	var finvitacionbd = "";
	if(valor!="" && valor!="-1"){
		$.ajax({
			type: "POST",
			url: "services/Ajax_comprobacion.php",
			data: "tramiteid="+valor,
			success: function(json){
				if(json==null){
					$("#invitacion_dato").html("Datos no encontrados");
				} else {
					finvitacion = fecha_to_mysql_normal(json);
					$("#invitacion_dato").html(finvitacion);
					finvitacionbd = fecha_to_mysql(json);
					document.getElementById("fechainvitacion").value = finvitacionbd;
				}
			}
		});
	}
}

function cargarMontoPesos(valor){
	if(valor!=""){
		$.ajax({
			type: "POST",
			url: "services/Ajax_comprobacion.php",
			data: "idsolic="+valor,
			success: function(json){
				if(json == null){					
					document.getElementById("monto_pesosDisabled").value = "0";
					document.getElementById("monto_pesos").value = "NULL";
				} else {
					document.getElementById("monto_pesosDisabled").value = number_format(json,0,".",",");
					document.getElementById("monto_pesos").value = number_format(json,0,".",",");
				}
			}
		});
	}
}

function cambiaTitulo(){
	var divEuro = parseFloat($("#valorDivisaEUR").val());
	var total = parseFloat(($("#monto_pesos").val()).replace(/,/g,""));
	var invitados = parseFloat($("#numInvitados").val());
	var conceptoExcedePoliticas="";
	//Variable para guardar 
    var monto2 = 0;
    monto2 = ((total / divEuro) / invitados );
    
    if(monto2 > 50){
    	var mensajeExcedePoliticas = "<strong>Esta rebasando la pol&iacute;tica del concepto. <br>El monto m&aacute;ximo es de 50.00 Euros por persona.</strong>";
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
    	$("#capaWarning").html("");
    	document.getElementById("banderavalida").value = 0;
    }
}

function LlenarTabla(json, tabla){	
	var frm=document.invitacion_comp;
	for(var i=0;i<json.length;i++){
		
		var toks=json[i].split(":");
		
		//Creamos la nueva fila y sus respectivas columnas
		var nuevaFila='<tr>';
		nuevaFila+="<td>"+"<div id='renglonI"+(i+1)+"' name='renglonI"+(i+1)+"'>"+(i+1)+"</div>"+"<input type='hidden' name='row2"+(i+1)+"' id='row2"+(i+1)+"' value='"+(i+1)+"' readonly='readonly' /></td>";
		nuevaFila+="<td><input type='hidden' name='nombre"+(i+1)+"' id='nombre"+(i+1)+"' value='"+toks[0]+"' readonly='readonly' />"+toks[0]+"</td>";
		nuevaFila+="<td><input type='hidden' name='puesto"+(i+1)+"' id='puesto"+(i+1)+"' value='"+toks[1]+"' readonly='readonly' />"+toks[1]+"</td>";
		nuevaFila+="<td><input type='hidden' name='empresa"+(i+1)+"' id='empresa"+(i+1)+"' value='"+toks[2]+"' readonly='readonly' />"+toks[2]+"</td>";
		nuevaFila+="<td ><input type='hidden' name='tipoinv"+(i+1)+"' id='tipoinv"+(i+1)+"' value='"+toks[3]+"' readonly='readonly' />"+toks[3]+"</td>";
		nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+(i+1)+"del' id='"+(i+1)+"del' onmousedown='eliminarInvitado(this.id);' style='cursor:pointer;' /></div><div align='center'>Eliminar Partida</div></td>";
		nuevaFila+= '</tr>';
		frm.rowCount2.value=parseInt(frm.rowCount2.value)+parseInt(1);
		$("#invitado_table").append(nuevaFila);
	}
	document.getElementById("numInvitadosDisabled").value = json.length;
	document.getElementById("numInvitados").value = json.length;
}

function VaciarTabla() {
	var TABLE = document.getElementById("invitado_table");
	for(i=TABLE.rows.length-1;i>=1;i--){
		TABLE.deleteRow(i);
	}
}
//Agregar nuevo invitado
function agregarNuevoInvitado(){
	if(document.getElementById("agregarInv").value == "Agregar Invitado"){
		document.getElementById("agregarInv").value = "Guardar";
		document.getElementById("agregarInvitado").style.display = "";
		//document.getElementById("registrar_comp").setAttribute("disabled","disabled");
	}else{ //en caso de que se oprima el boton Guardar		
		document.getElementById("agregarInv").value = "Agregar Invitado";
		document.getElementById("agregarInvitado").style.display = "none";
		insertarInvitadoTabla();
		//document.getElementById("registrar_comp").removeAttribute("disabled");
	}
}
// FIN Agregar nuevo invitado
// Agregar partida
function insertarInvitadoTabla(){
	var nuevaFila='<tr>';
	var frm=document.invitacion_comp;
	var invitados=parseFloat($("#numInvitados").val());
	id=parseInt($("#invitado_table").find("tr:last").find("div").eq(0).html());

	if($("#nombre_invitado").val()==""){
		alert("Los campos con (*) son obligatorios. Fafdvor de llenar los datos faltantes.");
		document.getElementById("agregarInv").click();
		$("#nombre_invitado").focus();
	}else if($("#puesto_invitado").val()==""){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
		document.getElementById("agregarInv").click();
		$("#puesto_invitado").focus();
	}else if($("#empresa_invitado").val()==""){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
		document.getElementById("agregarInv").click();
		$("#empresa_invitado").focus();
	}else if($("#tipo_invitado").val()==-1){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");		
		document.getElementById("agregarInv").click();
		$("#tipo_invitado").focus();
	}else{
		if(isNaN(id)){
			id=1;
		}else{
			id+=parseInt(1);
		}
		nuevaFila+="<td>"+"<div id='renglonI"+id+"' name='renglonI"+id+"'>"+id+"</div>"+"<input type='hidden' name='row2"+id+"' id='row2"+id+"' value='"+id+"' readonly='readonly' /></td>";
		nuevaFila+="<td><input type='hidden' name='nombre"+id+"' id='nombre"+id+"' value='"+$("#nombre_invitado").val()+"' readonly='readonly' />"+$("#nombre_invitado").val()+"</td>";
		nuevaFila+="<td><input type='hidden' name='puesto"+id+"' id='puesto"+id+"' value='"+$("#puesto_invitado").val()+"' readonly='readonly' />"+$("#puesto_invitado").val()+"</td>";
		nuevaFila+="<td><input type='hidden' name='empresa"+id+"' id='empresa"+id+"' value='"+$("#empresa_invitado").val()+"' readonly='readonly' />"+$("#empresa_invitado").val()+"</td>";
		nuevaFila+="<td ><input type='hidden' name='tipoinv"+id+"' id='tipoinv"+id+"' value='"+$("#tipo_invitado").val()+"' readonly='readonly' />"+$("#tipo_invitado").val()+"</td>";
		nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+id+"del' id='"+id+"del' onmousedown='eliminarInvitado(this.id);' style='cursor:pointer;' /></div><div align='center'>Eliminar Partida</div></td>";
		nuevaFila+= '</tr>';

		$("#invitado_table").append(nuevaFila);
		invitados=invitados+1;
		$("#numInvitadosDisabled").val(invitados);
		$("#numInvitados").val(invitados);
		frm.rowCount2.value=parseInt(frm.rowCount2.value)+parseInt(1);
		$("#nombre_invitado").val("");
		$("#puesto_invitado").val("");
		$("#empresa_invitado").val("");
		$("#tipo_invitado").val("-1");
		// Verificamos que el monto no exceda con el npumero de invitados que resten.
		evaluaMonto($("#monto_pesos").val().replace(/,/g,""));
	}
}
// FIN Agregar partida
// Guardar comprobacion
function guardaComprobacion(){
	var frm=document.invitacion_comp;
	if(parseInt(frm.no_Comprobaciones_parciales.value)>=1){
		if($("#delegado").val() != 0){
        	$("#enviaDirector").removeAttr("disabled");
        }else{
        	$("#guardarComp").removeAttr("disabled");
        }		
	}else{
		if($("#delegado").val() != 0){
			$("#enviaDirector").attr("disabled", "disabled");
        }else{
        	$("#guardarComp").attr("disabled", "disabled");
        }		
	}
}
// FIN Guardar comprobacion
// Verificar tipo de invitado
function verificar_tipo_invitado(){
	if($("#tipo_invitado").val()=="BMW"){
		$("#empresa_invitado").val("BMW DE MEXICO SA DE CV.");
		$("#empresa_invitado").attr("disabled", "disable");
	}else{
		$("#empresa_invitado").val("");
		$("#empresa_invitado").removeAttr("disabled");

	}
}
// FIN Verificar tipo de invitado

//Borrar invitado
function eliminarInvitado(id){
	var no_partidas = parseInt($("#invitado_table>tbody>tr").length);
	// Quitamos el registro de Invitado
	borrarRenglon4(id, "invitado_table", "rowCount2", 0,"renglonI", "edit", "del", "");
	$("#numInvitadosDisabled").val(parseInt(no_partidas - 1));
	$("#numInvitados").val(parseInt(no_partidas - 1));
	$("#rowCount2").val(parseInt(no_partidas - 1));
	evaluaMonto($("#monto_pesos").val().replace(/,/g,""));
}// Borrar invitado
// FIN Borrar partida

function cambiaNombreBtn(obj){
	if(obj.value=='     Agregar Nuevo Proveedor'){
		limpiarCamposDeProveedor(1);
		$("#msg_div").removeAttr('style');
		$('#add_rem_prv').attr("style","background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;");
		obj.value='     Cerrar Panel de Nuevo Proveedor' ;
	}else{
		limpiarCamposDeProveedor(1);
		$("#msg_div").removeAttr('style');
		$('#add_rem_prv').attr("style","background:url(../../images/add.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;");
		obj.value='     Agregar Nuevo Proveedor' ;
	}
}
//Agrega Nuevo Proveedor al catálogo
function nuevoProveedor(nombreProveedor,rfcProveedor,dirFiscal){
	if(nombreProveedor == "" ){
		alert("Debe ingresar la Razón Social del proveedor.");
		$("#new_proveedor").focus();
		return false;
	}else if(rfcProveedor == ""){
		alert("Debe ingresar el RFC del proveedor.");
		$("#new_p_rfc").focus();
		return false;
	}else if(dirFiscal == ""){
		alert("Debe ingresar el Domicilio Fiscal del proveedor.");
		$("#new_p_addr").focus();
		return false;
	}else{
		if(!valida_formatoRFC(rfcProveedor)){
			$("#new_p_rfc").focus();
			return false;
		}else{
			$.ajax({
				url: 'services/catalogo_proveedores.php',
				type: "POST",
				data: "submit=&nameprov="+nombreProveedor+"&rfcprov="+rfcProveedor+"&dirf="+dirFiscal,
				success: function(datos){
					if (datos==""){
						$("#proveedor").val("");
						$("#rfc").val("");
						$('#new_proveedor').val("");
						$('#new_p_rfc').val("");
						$('#new_p_addr').val("");
						$("#proveedor").focus();
					}else{
						alert(datos);
						$("#new_proveedor").focus();
					}
				}
			});//fin ajax
			return false;
		}
	}//fin if rfc
}
//fin nuevoProveedor

function validaInvitados(){
	var numInv = parseInt($("#numInvitados").val());
	if(numInv < 2){
		alert("Favor de ingresar por lo menos dos invitados.");
		return false;
	}else{
		return true;
	}
}

//Verificar estructura de RFC
function valida_formatoRFC(campo){
	var resultado = campo.match(/[A-Z]{3,4}[0-9]{6}((([A-Z]|[a-z]|[0-9]){3}))/);
	var resultado2 = campo.match(/^[a-zA-Z]{4,4}[0-9]{6,6}[a-zA-Z0-9]*$/) ;
	
	if(resultado == null && resultado2 == null){
		alert("El RFC que intenta ingresar es incorrecto. Favor de verificarlo.");
		return false;
	}else{
		return true;
	}	
}

//VALIDA TODOS LOS CAMPOS Y PARAMETROS marca
function validarcampos(){
	var frm= document.invitacion_comp;

	// Desactivar las listas Tipo de Tarjeta y Lista de Cargos en el caso 
	// que la Comprobación sea Devuelta con Observaciones por Finanzas 
	if($("#etapaComprobacion").val() == 5){
		$("#select_tipo_tarjeta").removeAttr("disabled");
		$("#select_tarjeta_cargo").removeAttr("disabled");
	}
	
	var sol_inv=$("#solicitud_de_invitacion").val();
	var tipo_comp=$("#tipo option:selected").val();
	var cc_costos=$("#centro_de_costos").val();

	if(validaInvitados() && solicitarConfirmarGuardado()){
		if(parseInt(sol_inv)==-1){
			alert("Debe seleccionar una solicitud de invitacion para comprobar. Favor de llenar los datos faltantes.");
			return false;
		}else if(parseInt(tipo_comp)==-1){
			alert("Debe seleccionar un tipo de comprobación. Favor de llenar los datos faltantes.");
			return false;
		}else{
			if(tipo_comp=="amex"){
					var lugar_amex=$("#lugar_inv").val();
					var proveedor_amex=($("#fact_chk").is(':checked'))?true:false;
					var monto_amex=$("#monto").val();
					var monto_pesos_amex=($("#monto_pesos").val()).replace(/,/g,"");
					var moneda_amex=$("#moneda").val();

					var total_comp = ($("#total").val()).replace(/,/g,"");
					var monto_amex_comp = ($("#amex_dolar_val").val()).replace(/,/g,"");
					total_comp = parseFloat(total_comp);
					monto_amex_comp = parseFloat(monto_amex_comp);
					//var dat_fact= checafecha(frm.fecha.value, 60);
					var dat_fact= true;
					var total_amex=$("#total").val();
					var observaciones_amex=$("#banderavalida").val();
					//alert(parseFloat(frm.total.value)+"-"+parseFloat(frm.monto_cargo_val.value));
					var aux_fecha_cargo = frm.fecha_cargo_val.value.split(" ");
					aux_fecha_cargo = aux_fecha_cargo[0];
					aux_fecha_cargo = aux_fecha_cargo.split("-").reverse().join("/");
					if(frm.select_tipo_tarjeta.value==-1){ //tipo de tarjeta
						alert("Debe seleccionar un tipo de cargo para comprobar. Favor de llenar los datos faltantes.");
						return false;
					}else if(frm.select_tarjeta_cargo.value==-1 || frm.select_tarjeta_cargo.value==""){ //lista de cargos
						alert("Debe seleccionar un cargo para comprobar. Favor de llenar los datos faltantes.");
						return false;
					}else{
						if(lugar_amex==""){
							alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
							$("#lugar_inv").focus();
							return false;
						}else if(proveedor_amex){ // Verfificar parte de la edición
							if(monto_amex=="" || monto_amex == "0.00" || monto_amex == 0){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#monto").focus();
								return false;
							}else if(frm.impuesto.value==""){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								frm.impuesto.focus();
								return false;
							}else if (parseFloat((frm.impuesto.value).replace(/,/g,"")) < 0 || frm.impuesto.value == ""){
								alert("El IVA introducido es inválido. Favor de verificarlo.");
								frm.impuesto.focus();
								return false;
							}else if($("#d_folio").val()==""){
								alert("Debe ingresar un folio de factura. Favor de llenar los datos faltantes.");
								$("#d_folio").focus();
								return false;
							}else if((frm.rfc.value.length < 12) || (frm.rfc.value.length > 13)){
								alert("El RFC que intenta ingresar es incorrecto. Favor de verificarlo.");
								frm.rfc.focus();
								return false;
							}else if(frm.proveedor.value==""){
								alert("Los datos del proveedor estan incompletos. Favor de llenar los datos faltantes.");
								frm.rfc.focus();
								return false;
							}else if(!valida_formatoRFC($("#rfc").val()) || (frm.rfc_cargo_val.value.toUpperCase() != frm.rfc.value && ($("#rfc_cargo_val").val()).length > 1)){
								alert("El RFC ingresado difiere del RFC registrado al cargo.");
								frm.rfc.focus();
								return false;
							}else if(($("#moneda option:selected").text()) != ($("#moneda_fact_val").val())){
								alert("La moneda de facturación difiere de la divisa ingresada.");
								return false;
							}else if(total_comp != monto_amex_comp){
								alert("El monto ingresado difiere del Total Amex.");
								return false;
							}else if(observaciones_amex == 1 && $("#observ").val() == "" ){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#observ").focus();
								return false;
							}else if(!validaProveedor($("#rfc").val(),$("#proveedor").val())){
								return false;
							}
						}else if(!proveedor_amex){
							if(monto_amex == "" || monto_amex == "0.00" || monto_amex == 0){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#monto").focus();
								return false;
							}else if(parseFloat(monto_amex)<=0){
								alert("El monto introducido no es válido. Favor de verificarlo.");
								$("#monto").focus();
								return false;
							}else if(monto_pesos_amex==""){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#monto").focus();
								return false;
							}else if(parseFloat(monto_pesos_amex)<=0){
								alert("El monto en pesos introducido no es válido. Favor de verificarlo.");
								$("#monto").focus();
								return false;
							}else if(moneda_amex==-1){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#moneda").focus();
								return false;
							}else if(moneda_amex == 1){
								if (parseFloat((frm.impuesto.value).replace(/,/g,"")) < 0 || frm.impuesto.value == ""){
									alert("El IVA introducido es inválido. Favor de verificarlo.");
									frm.impuesto.focus();
									return false;
								}else if(($("#moneda option:selected").text()) != ($("#moneda_fact_val").val())){
									alert("La moneda de facturación difiere de la divisa ingresada.");
									return false;
								}else if(total_comp != monto_amex_comp){
									alert("El monto ingresado difiere del Total Amex.");
									return false;
								}else if(observaciones_amex==1 && $("#observ").val().length == 0){
									alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
									$("#observ").focus();
									return false;
								}
							}else if(dat_fact==false){
								alert("Tu comprobante excede el plazo permitido de 60 dias.");                      
								return false;
							}else if(total_amex==""){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#total").focus();
								return false;
							}else if(($("#moneda option:selected").text()) != ($("#moneda_fact_val").val())){
								alert("La moneda de facturación difiere de la divisa ingresada.");
								return false;
							}else if(total_comp != monto_amex_comp){
								alert("El monto ingresado difiere del Total Amex.");
								return false;
							}else if(observaciones_amex==1 && $("#observ").val().length == 0){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#observ").focus();
								return false;
							}else{
								return true;
							}
						}else{
							if(monto_amex == "" || monto_amex == "0.00" || monto_amex == 0){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#monto").focus();
								return false;
							}else if(parseFloat(monto_amex)<=0){
								alert("El monto introducido no es válido. Favor de verificarlo.");
								$("#monto").focus();
								return false;
							}else if(monto_pesos_amex==""){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#monto").focus();
								return false;
							}else if(parseFloat(monto_pesos_amex)<=0){
								alert("El monto en pesos introducido no es válido. Favor de verificarlo.");
								$("#monto").focus();
								return false;
							}else if(moneda_amex==-1){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#moneda").focus();
								return false;
							}else if(moneda_amex == 1){
								if (parseFloat((frm.impuesto.value).replace(/,/g,"")) < 0 || frm.impuesto.value == ""){
									alert("El IVA introducido es inválido. Favor de verificarlo.");
									frm.impuesto.focus();
									return false;
								}else if(($("#moneda option:selected").text()) != ($("#moneda_fact_val").val())){
									alert("La moneda de facturación difiere de la divisa ingresada.");
									return false;
								}else if(total_comp != monto_amex_comp){
									alert("El monto ingresado difiere del Total Amex.");
									return false;
								}else if(observaciones_amex==1 && $("#observ").val().length == 0){
									alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
									$("#observ").focus();
									return false;
								}
							}else if(dat_fact==false){
								alert("Tu comprobante excede el plazo permitido de 60 dias.");                      
								return false;
							}else if(total_amex==""){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#total").focus();
								return false;
							}else if(($("#moneda option:selected").text()) != ($("#moneda_fact_val").val())){
								alert("La moneda de facturación difiere de la divisa ingresada.");
								return false;
							}else if(total_comp != monto_amex_comp){
								alert("El monto ingresado difiere del Total Amex.");
								return false;
							}else if(observaciones_amex==1 && $("#observ").val().length == 0){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#observ").focus();
								return false;
							}else{
								return true;
							}
						} 
					}
			}else if(tipo_comp == "reembolso_para_empleado"){
					var lugar_reembolso=$("#lugar_inv").val();
					var proveedor_reembolso=($("#fact_chk").is(':checked'))?true:false;
					var monto_amex=$("#monto").val();
					var moneda_amex=$("#moneda").val();
					//var dat_fact= checafecha(frm.fecha.value, 60);
					var dat_fact= true;
					var total_amex=$("#total").val();
					var observaciones_amex=$("#banderavalida").val();
					
					if(lugar_reembolso==""){
						alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
						$("#lugar_inv").focus();
						return false;
					}else if(proveedor_reembolso){
						if(monto_amex == "" || monto_amex == "0.00" || monto_amex == 0){
							alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
							$("#monto").focus();
							return false;
						}else if (frm.impuesto.value==""){
							alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
							frm.impuesto.focus();
							return false;
						}else if (parseFloat((frm.impuesto.value).replace(/,/g,"")) < 0  || frm.impuesto.value == ""){
							alert("El IVA introducido es inválido. Favor de verificarlo.");
							frm.impuesto.focus();
							return false;
						} else if($("#d_folio").val()==""){
							alert("Debe ingresar un folio de factura. Favor de llenar los datos faltantes.");
							$("#d_folio").focus();
							return false;
						}else if((frm.rfc.value.length<12)||(frm.rfc.value.length>13)){
							alert("El RFC que intenta ingresar es incorrecto. Favor de verificarlo e intente nuevamente.");
							frm.rfc.focus();
							return false;
						}else if(frm.proveedor.value==""){
							alert("Los datos del proveedor estan incompletos. Favor de llenar los datos faltantes.");
							frm.rfc.focus();
							return false;
						}else if(!validaProveedor($("#rfc").val(), $("#proveedor").val())){
							return false;
						}						
					}else if(monto_amex==""){
						alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
						$("#monto").focus();
						return false;
					}else if(parseFloat(monto_amex)<=0){
						alert("El monto introducido no es válido. Favor de verificarlo.");
						$("#monto").focus();
						return false;
					}else if(monto_pesos_amex==""){
						alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
						$("#monto").focus();
						return false;
					}else if(parseFloat(monto_pesos_amex)<=0){
						alert("El monto en pesos introducido no es válido. Favor de verificarlo.");
						$("#monto").focus();
						return false;
					}else if(moneda_amex==-1){
						alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
						$("#moneda").focus();
						return false;
					}else if(moneda_amex == 1){
						if(parseFloat((frm.impuesto.value).replace(/,/g,"")) < 0 || frm.impuesto.value == ""){
							alert("El IVA introducido es inválido. Favor de verificarlo.");
							frm.impuesto.focus();
							return false;
						}else if(observaciones_amex == 1 && $("#observ").val() == "" ){
							alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
							$("#observ").focus();
							return false;
						}
					}else if(dat_fact==false){
						alert("Tu comprobante excede el plazo permitido de 60 dias.");                      
						return false;
					}else if(total_amex==""){
						alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
						$("#total").focus();
						return false;
					}else if(parseFloat(total_amex)<=0){
						alert("El Total introducido no es válido. Favor de verificarlo.");
						$("#total").focus();
						return false;
					}else if(observaciones_amex==1 && $("#observ").val() == "" ){
						alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
						$("#observ").focus();
						return false;
					}else{
						return true;
					}
			}
		}
	}else{
		return false;
	}
}
// FIN de Validar campos

function cambiaMotivo(){
	var frm=document.invitacion_comp;

	if(frm.solicitud_de_invitacion.value!="-1" && frm.solicitud_de_invitacion.value!="n"){                                                                                                                                        	
		var tramite=frm.solicitud_de_invitacion.options[frm.solicitud_de_invitacion.selectedIndex].value;			
		frm.motive.value=$("#solicitud_de_invitacion option:selected").text();
		//frm.typedoc.value="1|"+tramite;			
		//getSaldoAnticipo(tramite);		
	}else{
		frm.motive.value="";		
		//frm.typedoc.value="";			
		//$("#g_saldo").html("");
	}

}
function clean_comprobacion(){
	$("#monto").val(0);
	$("#monto_pesosDisabled").val(0);
	$("#monto_pesos").val(0);
	$("#propina_dato").val(0);
	$("totalDisabled").val("0");
	$("total").val("0");
	$("#observ").val("");
	$("input[name=fact_chk]").attr("checked",false);
	$("#impuesto").fadeOut();
	$("#datosProveedor").fadeOut();
	document.getElementById("moneda").removeAttribute("disabled");
	document.getElementById("impuesto").value = 0;
  	$("#proveedor").val("");
  	$("#rfc").val("");
  	$("#d_folio").val("");
  	$("#numInvitadosDisabled").val("0");
  	$("#numInvitados").val("0");
  	$("#impuesto").val(""); 
	$("#moneda").val("-1");
	$("#lugar_inv").val("");
	$("#invitacion_dato").html("");
	document.getElementById("fechainvitacion").value = "";
	VaciarTabla();

  	getTotal();
  	//recalculaMontos();
}

function cargar_solicitud(valor){
	guardaComprobacionprev();
	clean_comprobacion();
	cambiaMotivo();
	seleccionar_centro_de_costos(valor);
	cargarInvitados(valor);
	cargarCiudad(valor);
	cargarLugar(valor);
	cargarFecha(valor);
	//cargarMontoPesos(valor);
	cambiaTitulo();
}

function cambiar_divisa(valor){
	$("#divisa_sol").val(valor);
}

function validaZeroIzquierda(monto,campo){			
	if( monto.substring(monto.length-1,monto.length) === "." || monto.substring(monto.length-2,monto.length) === ".0" ){
		$("#"+campo).val(monto);
	}else if(monto == 0 || monto == "" || monto == "NaN"){
		$("#"+campo).val(0);
	}else{
		$("#"+campo).val(parseFloat(monto));
	}
}
</script>
<link rel="stylesheet" type="text/css" href="../../css/jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="../../css/date_input.css" />
<link rel="stylesheet" type="text/css" href="../../css/table_style.css" />
<style type="text/css">
.style1 {
	color: #FF0000
}

.fader {
	opacity: 0;
	display: none;
}

.trans {
	background-color: #D7D7D7;
	color: #0000FF;
	position: absolute;
	vertical-align: middle;
	width: 690px;
	height: 200px;
	padding: 65px;
	font-size: 15px;
	font-weight: bold;
	top: 26%;
	left: 18%;
}

.boton {
	background: #666666;
	color: #FFFFFF;
	border-color: #CCCCCC;
}
</style>

<form action="comprobacion_invitacion.php?save" method="post" name="invitacion_comp" id="invitacion_comp">
	<table id="cambia_table" name="cambia_table" width="785" border="0" align="center" cellspacing="1" style="border: 1px #CCCCCC solid; margin: auto; margin-top: 5px; text-align: left;" onmouseover="cambiaTitulo();" onblur="cambiaTitulo();" onmouseout="cambiaTitulo();">
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<?
			$cnn = new conexion();
			$idusuario = (isset($_SESSION["iddelegado"])) ? verificaSesion($_SESSION["idusuario"], $_SESSION["iddelegado"]) : $_SESSION["idusuario"];
			// $idusuario=$_SESSION["idusuario"];
			/*$query = sprintf("select distinct si.si_id as 'id',
si.si_tramite as 't',
t.t_etiqueta as 'e'
from solicitud_invitacion as si
inner join tramites as t
on si.si_tramite = t.t_id
inner join usuario as u
on t.t_iniciador = $idusuario
where t.t_comprobado = 0
and t.t_etapa_actual = 3 AND t.t_id NOT IN (
SELECT co_tramite
FROM comprobacion_invitacion as coi
inner join tramites as t2
on coi.co_mi_tramite = t2.t_id
where t2.t_flujo = '4'
and (t2.t_etapa_actual != '0' and t2.t_etapa_actual != '4')
)");*/
			$query = sprintf("select distinct si.si_id as 'id',
					si.si_tramite as 't',
					t.t_etiqueta as 'e'
					from solicitud_invitacion as si
					inner join tramites as t
					on si.si_tramite = t.t_id
					inner join usuario as u
					on t.t_iniciador = $idusuario
					where t.t_comprobado = 0
					and t.t_etapa_actual = 3 AND t.t_id NOT IN (SELECT co_tramite FROM comprobacion_invitacion)");
			//error_log($query);
			$rst = $cnn->consultar($query);
			$fila = mysql_num_rows($rst);
			?>
			<td colspan="3">Solicitud de invitaci&oacute;n<span class="style1">*</span><!--cargarMontoPesos(this.value);  -->
				<select name="solicitud_de_invitacion" id="solicitud_de_invitacion" onchange="cargar_solicitud(this.value);">
					<option id="-1" value="-1">Seleccione...</option>
					<?php
						if($fila>0){
							while($fila=mysql_fetch_assoc($rst)){
								echo "<option id=".$fila["id"]." value=".$fila["t"].">".$fila["t"]." - ".$fila["e"]."</option>";
							}
						}else{
							echo "<option id='-1' value='-1'>No hay Solicitudes Pendientes</option>";
						}
					?>
				</select>
			</td>
			<td colspan="3">Tipo de comprobaci&oacute;n<span class="style1">*</span>:
				<select name="tipo" id="tipo" onChange="tipo_de_comprobacion(value);"
				disabled="disabled">
					<option id="-1" value="-1">Seleccione...</option>
					<?php if($tipoUsuario != 3){?>
						<option name="amex" id="amex" value="amex">Amex</option>
					<?php }?>
					<option id="reembolso_para_empleado" value="reembolso_para_empleado">Reembolso</option>
			</select>
			</td>
			<?
			$cnn = new conexion();
			$idusuario=$_SESSION["idusuario"];
			$query = sprintf("SELECT CC.CC_ID AS 'ID',
				CC.CC_CENTROCOSTOS AS 'CC',
				CC.CC_NOMBRE AS 'NOMBRE'
				FROM cat_cecos AS CC
				WHERE cc_estatus = '1' AND cc_empresa_id = '".$_SESSION["empresa"]."' 
				ORDER BY CC.CC_CENTROCOSTOS");
			$rst = $cnn->consultar($query);
			$fila = mysql_num_rows($rst);
			?>
			<td colspan="3">Centro de costos<span class="style1">*</span>: <select name="centro_de_costos" id="centro_de_costos" disabled="disabled">
					<option id="-1" value="-1">Seleccione...</option>
					<?php
						if($fila>0){
							while($fila=mysql_fetch_assoc($rst)){
								echo "<option id=".$fila["ID"]." value=".$fila["CC"].">".$fila["CC"]."-".$fila["NOMBRE"]."</option>";
							}
						}else{
							echo "<option id='n' value='n'>No hay centro de costos</option>";
						}
					?>
				</select>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<br />
	<!-- Seccion Amex -->
	<!--<div id="seccion_amex" align="right" style="display: none;" onmouseover="obtener_tasa_USD();" onblur="obtener_tasa_USD();" onmouseout="obtener_tasa_USD();">-->
	<div id="seccion_amex" align="right" style="display: none;" >
		<table width="785" border="0" align="center" cellspacing="1" style="border: 1px #CCCCCC solid; margin: auto; margin-top: 5px; text-align: left;">
			<tr>
				<td colspan="3"><h3 align="center">Amex</h3></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="3">Tipo de cargo: <select name="select_tipo_tarjeta" id="select_tipo_tarjeta" onchange="verificar_tipo_tarjeta();">
						<option id="-1" value="-1">Seleccione...</option>
						<option id="1" value="1">Amex corporativa gastos</option>
						<!-- option id="2" value="2">Amex corporativa Gasolina</option -->
				</select>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="3">Tarjeta de Cr&eacute;dito:&nbsp;
					<div name="no_tarjeta_credito" id="no_tarjeta_credito" style="width: 70%"></div>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<!--<td colspan="3">Lista de Cargos: <select name="select_tarjeta_cargo" id="select_tarjeta_cargo" onchange="cargar_detalles();obtener_tasa_USD();" onmouseover="obtener_tasa_USD();" onblur="obtener_tasa_USD();" onmouseout="obtener_tasa_USD();">-->
				<td colspan="3">Lista de Cargos: <select name="select_tarjeta_cargo" id="select_tarjeta_cargo" onchange="cargar_detalles();">
				</select>
				</td>
			</tr>
			<tr>
				<td colspan="3"><h3 align="center">Detalle del cargo</h3></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td width="50%">Establecimiento: <input type="hidden" id="establecimiento_cargo_val" name="establecimiento_cargo_val" value="0" readonly="readonly" />
					<div id="establecimiento_cargo" name="establecimiento_cargo" align="left"></div>
				</td>
				<td width="50%">Total Factura: <input type="hidden" id="monto_cargo_val" name="monto_cargo_val" value="0" readonly="readonly" />
					<div id="monto_cargo" name="monto_cargo" align="left"></div>
					<input type="hidden" id="moneda_fact_val" name="moneda_fact_val" readonly="readonly" />
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td width="50%">Fecha: <input type="hidden" id="fecha_cargo_val" name="fecha_cargo_val" value="0" readonly="readonly" />
					<div id="fecha_cargo" name="fecha_cargo"></div>
				</td>
				<td width="50%">Total Amex: <input type="hidden" id="amex_dolar_val" name="amex_dolar_val" value="0" readonly="readonly" /> 
					<div id="amex_dolar" name="amex_dolar" align="left"></div>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td width="50%">RFC: <input type="hidden" id="rfc_cargo_val" name="rfc_cargo_val" value="0" readonly="readonly" /><div id="rfc_cargo" name="rfc_cargo" align="left"></div></td>
				<td width="50%">Total MXN: <input type="hidden" id="amex_pesos_val" name="amex_pesos_val" value="0.00" readonly="readonly" /><div id="amex_pesos" name="amex_pesos" align="left"></div></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td colspan="4" width="50%">Tipo de cambio: <input type="hidden" id="tipo_cambio" name="tipo_cambio" value="0.00" readonly="readonly" /><div id="div_tipo_cambio" name="div_tipo_cambio" align="left"></div></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td width="50%">&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<br />
	</div>
	<!-- FIN Seccion Amex -->

	<!-- Conceptos a comprobar -->
	<table width="785" border="0" align="center" cellspacing="1" style="border: 1px #CCCCCC solid; margin: auto; margin-top: 5px; text-align: left;" onmouseover="cambiaTitulo();" onblur="cambiaTitulo();" onmouseout="cambiaTitulo();">
		<tr>
			<td colspan="2"></td>
		</tr>
		<tr>
			<td width="2">&nbsp;</td>
			<td colspan="12"><h3 align="center">Conceptos a comprobar</h3></td>
			<td width="2">&nbsp;</td>
		</tr>
		<tr>
			<td width="2">&nbsp;</td>
			<td ><div id="ciudad_label">Ciudad: </div></td>
			<td colspan="3" align="left">
				<div id="ciudad_data" name="ciudad_data"></div>
				<input type="hidden" id="co_ciudad_data" name="co_ciudad_data" value="" readonly="readonly"/>
			</td>
			<td >&nbsp;</td>
			<td align="left" colspan="2">
				<table>
					<tr>
						<td align="left"><div id="fecha_inv">Fecha de invitaci&oacute;n: </div></td>
						<td align="left"><div id="invitacion_dato" name="invitacion_dato" align="left"></div><input type="hidden" name="fechainvitacion" id="fechainvitacion"/></td>
					</tr>
				</table>
			</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="1" width="2">&nbsp;</td>
			<td colspan="12">
			<table>
				<tr>
					<td>Lugar invitaci&oacute;n/Restaurante<span class="style1">*</span>:&nbsp;
					<input type="text" name="lugar_inv" id="lugar_inv" /></td>
				</tr>
			</table>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="2">Proveedor Nacional: <input type="checkbox" name="fact_chk" id="fact_chk" onclick="verDatosProveedor();" /></td>
			<td colspan="2"><a href="http://www.oanda.com/lang/es/currency/converter/" target="_black">Realiza la conversi&oacute;n a tu divisa</a></td>
			<td>&nbsp;</td>
			<td colspan="7">
				<table>
					<tr>
						<td><div align="left">Fecha Comprobante<span class="style1">*</span>:&nbsp;</div></td>
						<td><div align="left"><input name="fecha" id="fecha" value="<?php echo date('d/m/Y'); ?>" size="10" onfocus="return checafecha(document.invitacion_comp.fecha.value, 60);" /></div></td>
					</tr>
				</table>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="2" height="26">&nbsp;</td>
			<td width="70"><div align="left">Subtotal/Monto<span class="style1">*</span>: </div></td>
			<!--<td width="83"><div align="left"><input name="monto" id="monto" value="0" size="10" onkeyup="getTotal();recalculaMontos(); this.value = format_input(this.value);" onchange="getTotal();recalculaMontos();" onkeypress="return validaNum(event)" size="10" autocomplete="off" /></div></td>-->
			<!--<td width="83"><div align="left"><input name="monto" id="monto" value="0" size="10" onkeyup="getTotal();recalculaMontos();" onchange="getTotal();recalculaMontos();" onkeydown="" onkeypress="return NumCheck(event, this);" size="10" autocomplete="off" /></div></td>-->
			<td width="83"><div align="left"><input name="monto" id="monto" value="0" size="10" onkeyup="revisaCadena(this); getTotal(); recalculaMontos();" onchange="getTotal();recalculaMontos();" onkeypress='validaCeros(this.value,this.id); return validaNum (event);' size="10" autocomplete="off"/></div></td>
			<td width="55" colspan="2">
				<table>
				<tr>
					<td><div align="left">Divisa<span class="style1">*</span>:</div></td>
					<td><div align="left">
					<select name="moneda" id="moneda" onchange="cambiaTasa(this.value);recalculaMontos();cambiar_divisa(this.value);activaIva();">
						<option value="-1">Seleccione...</option>
						<option value="1">MXN</option>
						<option value="2">USD</option>
						<option value="3">EUR</option>
					</select>
					</div></td>
				</tr>
				</table>
				<input type="hidden" name="tasa" id="tasa" value="1" />
			</td>
			<td width="4">&nbsp;</td>
			<td width="150">&nbsp;</td>
			<td width="4">&nbsp;</td>
			<td colspan="5" rowspan="8" style="vertical-align: top">&nbsp;</td>
			<td width="4">&nbsp;</td>
		</tr>
		<tr>
			<td width="2">&nbsp;</td>
			<td width="70"><div align="left" id="iva_label" style="color: #333333; display: none;">IVA<span class="style1">*</span>: </div></td>
			<!--<td width="83"><div align="left"><input name="impuesto" id="impuesto" value="0" size="10" onkeyup="getTotal();recalculaMontos(); this.value = format_input(this.value);" onchange="getTotal();recalculaMontos();" onkeypress="return validaNum(event);" autocomplete="off" style="display:none;"/></div></td>-->
			<!--<td width="83"><div align="left"><input name="impuesto" id="impuesto" value="0" size="10" onkeyup="getTotal();recalculaMontos();" onchange="getTotal();recalculaMontos();" onkeydown="" onkeypress="return NumCheck(event, this);" autocomplete="off" style="display:none;"/></div></td>-->
			<td width="83"><div align="left"><input name="impuesto" id="impuesto" value="0" size="10" onkeyup="revisaCadena(this); getTotal(); recalculaMontos();" onchange="getTotal(); recalculaMontos();" onkeypress='validaCeros(this.value,this.id); return validaNum (event);' autocomplete="off" style="display:none;"/></div></td>
			<td width="55" colspan="2"><input type="hidden" name="bandera_iva" id="bandera_iva" size="15" /></td>
			<td width="4">&nbsp;</td>
			<td colspan="1" rowspan="3">
				<div align="left" id="datosProveedor" style="display: none;">
					<div align="left" id="div_folio">
						Folio Factura<span class="style1">*</span>:
					</div>
					<div align="left">
						<input name="d_folio" id="d_folio" size="15" onkeypress="return validaNum(event)" />
					</div>
					<div id="rfc_prov_busq_div" align="left">
						RFC<span class="style1">*</span>:
					</div>
					<div align="left">
						<input name="rfc" type="text" id="rfc" value="" size="30" maxlength="13" onkeyup="this.value = this.value.toUpperCase();"/>
					</div>
					<div align="left">
						Raz&oacute;n Social<span class="style1">*</span>:
					</div>
					<div align="left">
						<input name="proveedor" type="text" id="proveedor" value="" size="30" disabled />
					</div>
					<br />
					<input type="button" class="fadeNext" style='background: url(../../images/add.png); background-position: left; background-repeat: no-repeat; background-color: #E1E4EC;' name="add_rem_prv" id="add_rem_prv" onclick="cambiaNombreBtn(this);" value="     Agregar Nuevo Proveedor" /><div class="fader" align="right">
					<table width="295" border="0" align="center" cellspacing="1" style="border: 1px #CCCCCC solid; margin: auto; margin-top: 5px; padding-left: 5px; text-align: left;">
							<tr>
								<td><div align="left">
										<h3>Agregar nuevo proveedor</h3>
									</div></td>
							</tr>
							<tr>
								<td><div align="left">
										<em><strong>Raz&oacute;n Social<span class="style1">*</span>: </strong></em>
									</div>
									<div align="left">
										<input name="new_proveedor" type="text" id="new_proveedor" value="" size="45" />
									</div></td>
							</tr>
							<tr>
								<td><div align="left">
										<em><strong>RFC<span class="style1">*</span>: </strong></em>
									</div>
									<div align="left">
										<input name="new_p_rfc" type="text" id="new_p_rfc" value="" size="30" maxlength="13" onkeyup="this.value = this.value.toUpperCase();" onblur="valida_formatoRFC(this.value);"/>
									</div></td>
							</tr>
							<tr>
								<td><div align="left">
										<em><strong>Domicilio Fiscal<span class="style1">*</span>:</strong></em>
									</div> <input name="new_p_addr" id="new_p_addr" value="" size="45" /></td>
							</tr>
							<tr>
								<td>
									<div align="left">
										<input type="button" name="agregar" value="    Agregar" onclick="nuevoProveedor(new_proveedor.value,new_p_rfc.value,new_p_addr.value);" style='background: url(../../images/add.png); background-position: left; background-repeat: no-repeat; background-color: #E1E4EC;' />
									</div>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</td>
			<td colspan="1">&nbsp;</td>
			<td >&nbsp;</td>
		</tr>
		<tr>
			<td width="2">&nbsp;</td>
			<td width="70"><div name="propina" id="propina" align="left">Propina<span class="style1"></span>: </div></td>
			<!--<td width="83"><div align="left"><input name="propina_dato" value="0" id="propina_dato" size="10" onkeyup="getTotal();recalculaMontos(); this.value = format_input(this.value);" onchange="getTotal();recalculaMontos();" onkeypress="return validaNum(event);" /></div></td>-->
			<!--<td width="83"><div align="left"><input name="propina_dato" value="0" id="propina_dato" size="10" onkeyup="getTotal();recalculaMontos();" onchange="getTotal();recalculaMontos();" onkeydown="" onkeypress="return NumCheck(event, this);" /></div></td>-->
			<td width="83"><div align="left"><input name="propina_dato" value="0" id="propina_dato" size="10" onkeyup="revisaCadena(this); getTotal(); recalculaMontos();" onchange="getTotal(); recalculaMontos();" onkeypress='validaCeros(this.value,this.id); return validaNum (event);' /></div></td>
			<td width="55">&nbsp;</td>
			<td width="83" colspan="1">&nbsp;</td>
			<td width="4">&nbsp;</td>
			<td colspan="1">&nbsp;</td>
			<td colspan="1">&nbsp;</td>
		</tr>
		<tr>
			<td width="2">&nbsp;</td>
			<td width="70"><div align="left">Total<span class="style1">*</span>: </div></td>
			<td ><div align="left"><input type="text" name="totalDisabled" id="totalDisabled" value="0.00" size="10" disabled="disabled" />
				<input type="hidden" name="total" id="total" value="0.00" size="10" readonly="readonly" /></div></td>
			<td colspan="3" align="center">Monto Total en pesos: &nbsp;<input type="text" name="monto_pesosDisabled" id="monto_pesosDisabled" value="0.00" size="12" disabled="disabled"/>
			<input type="hidden" name="monto_pesos" id="monto_pesos" value="0.00" size="12" readonly="readonly"/>&nbsp;MXN</td>
			<td >&nbsp;</td>
			<td >&nbsp;</td>
		</tr>
		<tr>
			<td width="2">&nbsp;</td>
			<td colspan="6"><div id="capaWarning"></div><input type="hidden" name="banderavalida" id="banderavalida" readonly="readonly" /></td>
			<td width="2">&nbsp;</td>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr>
			<td width="2">&nbsp;</td>
			<td colspan="2" valign="top"><div id="comment" align="right">Comentarios: </div></td>
			<td colspan="10"><textarea maxlength="36" name="comentarios" id="comentarios" cols="80" rows="5"></textarea></td>
			<td >&nbsp;</td>
		</tr>
		<tr>
			<td width="2">&nbsp;</td>
			<td colspan="6">&nbsp;</td>
			<td width="2">&nbsp;</td>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr>
			<td width="2">&nbsp;</td>
			<td colspan="6">
				<h3 align="center">Personas que asistieron:</h3>
				<table id="invitado_table" class="tablesorter" cellspacing="1">
					<thead>
						<tr>
							<th width="5%">No.</th>
							<th width="35%">Nombre</th>
							<th width="25%">Puesto</th>
							<th width="20%">Empresa</th>
							<th width="15%">Tipo de Invitado</th>
							<th width="5%">Eliminar</th>
						</tr>
					</thead>
					<tbody>
						<!-- cuerpo tabla-->
					</tbody>
				</table>
			</td>
			<td colspan="5" rowspan="2">
			<div name="agregarInvitado" id="agregarInvitado" align="left" style="display: none;">
					Agregar un Invitado<br /> 
					Nombre:<span class="style1">*</span><br> <input name="nombre_invitado" type="text" id="nombre_invitado" size="50" maxlength="100" /><br> Puesto:&nbsp;<span class="style1">*</span><br>
					<input name="puesto_invitado" type="text" id="puesto_invitado" size="50" maxlength="100" /><br> 
					Tipo de Invitado:&nbsp;<span class="style1">*</span><br> 
					<select name="tipo_invitado" id="tipo_invitado" onchange="verificar_tipo_invitado();">
						<option value="-1">Seleccione...</option>
						<option value="BMW">Empleado BMW de M&eacute;xico</option>
						<option value="Externo">Externo</option>
						<option value="Gobierno">Gobierno</option>
					</select><br> 
					Empresa:&nbsp;<span class="style1">*</span><br> 
					<input name="empresa_invitado" type="text" id="empresa_invitado" size="50" maxlength="100" disabled="disable" />
				</div></td>
			<td colspan="1" rowspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="4"><div name="asistentes" id="asistentes" align="left">Total de Asistentes:&nbsp; <input type="text" name="numInvitadosDisabled" id="numInvitadosDisabled" value="0" size="2" disabled="disabled" />
			<input type="hidden" name="numInvitados" id="numInvitados" value="0" size="2" readonly="readonly" /><br></div></td>
			<td colspan="2" align="center" valign="middle"><input type="button" id="agregarInv" name="agregarInv" value="Agregar Invitado" onclick="agregarNuevoInvitado();" /></td>
		</tr>
		<tr>
			<td width="2">&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>
			</td>
			<td colspan="6"><div align="center" class="style1" id="load_div"></div>
			</td>
			<td width="2">&nbsp;</td>
			<td >&nbsp;</td>
		</tr>
	</table>
	<!-- FIN Conceptos a comprobar -->

	<div id="msg_div" align="center"></div>
	<br />
	
	<!--FIN DE: function forma_comprobacion() -->
	<?php
            }
            ?> 

            <center><h3>Comprobaci&oacute;n de solicitud de invitaci&oacute;n</h3></center>
            <?php
            forma_comprobacion();
            ?>
            <div align="right">
    <!--                <div id="g_saldo">&nbsp;</div><input type="hidden" readonly="readonly" name="t_saldo" id="t_saldo" value="0.00" /><br />-->
    <!--                <div id="g_sbt">Total: 0.00</div><input type="hidden" readonly="readonly" name="t_subtotal" id="t_subtotal" value="0.00" /><br />-->
    <!--                <div id="g_iva">IVA: 0.00</div><input type="hidden" readonly="readonly" name="t_iva" id="t_iva" value="0.00" /><br />-->
    <!--                <div id="g_tot">Saldo a reembolsar: 0.00</div><input type="hidden" readonly="readonly" name="t_total" id="t_total" value="0.00" />-->
            </div>
            <div id="ficha_deposito" style="display: none;">
                <table width="550" align="center" border="0" cellspacing="1" style="border: 1px #CCCCCC solid; margin: auto; margin-top: 5px; text-align: left;">
                    <tr>
                        <td colspan="4" align="center" style="color: #DF0101">
                            <b>El monto a comprobar es menor al anticipo, la comprobaci&oacute;n no se puede guardar.</b><br>
                        </td>
                    </tr>
                </table>
            </div>

            <br></br>
            <br></br>
            <div align="center">
                <table border="0" width='785 px'>
                    <tr>
                        <td width="20%" rowspan="4" align='right' valign="top" width="5"><div id="obsjus">Observaciones:</div></td>
                        <td width="40%" colspan="2" rowspan="4" class="alignLeft" valign="top">
                            <textarea name="observ" id="observ" cols="60" rows="5" ></textarea>
                        </td>
                        <td width="40%" align="right" valign="top">
                            <table style="border: 1px #CCCCCC solid; margin: auto; margin-top: 5px;" width="80%">
                                <tr>
                                    <td colspan="2" style="color: #000000; text-align: center;"><h3>Resumen</h3></td>
                                </tr>
                                <tr>
                                    <td><div id="g_saldo"></div><input type="hidden" readonly="readonly" name="t_saldo" id="t_saldo" value="0.00" /></td>
                                </tr>
                                <tr>
                                    <td><div id="g_sbt"></div><input type="hidden" readonly="readonly" name="t_subtotal" id="t_subtotal" value="0.00" /></td>
                                </tr><tr>
                                    <td>Total Amex comprobado:</td>
                                    <td><div id="g_amex_comprobado" align="right">0.00 MXN</div><input type="hidden" readonly="readonly" name="t_amex_comprobado" id="t_amex_comprobado" value="0.00" /></td>
                                </tr>
                                <tr>
                                    <td style="color: #DF0101">Monto a reembolsar:</td>
                                    <td style="color: #DF0101"><div id="g_reembolso" align="right">0.00 MXN</div><input type="hidden" readonly="readonly" name="t_reembolso" id="t_reembolso" value="0.00" /></td>
                                </tr>
                                <tr>
                                    <td style="color: #DF0101"></td>
                                    <td style="color: #DF0101"><div id="g_nocomprobado"></div><input type="hidden" readonly="readonly" name="t_nocomprobado" id="t_nocomprobado" value="0.00" /></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Total comprobaci&oacute;n:</td>
                                    <td><div id="g_comprobado" align="right">0.00 MXN</div><input type="hidden" readonly="readonly" name="t_comprobado" id="t_comprobado" value="0.00" /></td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                </table>
            </div>

	<div align="center">
		<br />
		<div align="center">
			<input type="submit" id="guardarCompprev" name="guardarCompprev" value="     Guardar Previo"  onclick="return solicitarConfirmPrevio();" style="background: url('../../images/save.gif'); background-position: left; background-repeat: no-repeat; background-color: #E1E4EC;" readonly="readonly" disabled="disabled"/>
			<?php if(isset($_SESSION['iddelegado'])){ ?>
                <input type="submit" id="enviaDirector" name="enviaDirector" value="     Enviar a Director"  onclick="return validarcampos();" style='background: url("../../images/save.gif"); background-position: left; background-repeat: no-repeat; background-color: #E1E4EC;' readonly="readonly" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php }else{ ?>
				<input type="submit" id="guardarComp" name="guardarComp" value="     Enviar Comprobaci&oacute;n"  onclick="return validarcampos();" style='background: url("../../images/save.gif"); background-position: left; background-repeat: no-repeat; background-color: #E1E4EC;' readonly="readonly" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<?php }?>			
		</div>
		<?php
		$query_centrocostos = sprintf("SELECT idcentrocosto FROM empleado INNER JOIN cat_cecos ON empleado.idcentrocosto = cat_cecos.cc_id WHERE empleado.idempleado = %s", $_SESSION["idusuario"]);
		$rst_centrocostos = mysql_query($query_centrocostos);
		$fila_idcentro_costos1 = mysql_fetch_assoc($rst_centrocostos);
		$idcentrocosto_original = $fila_idcentro_costos1['idcentrocosto'];
		//Obtener Divisa Euro
		$_divisas = new Divisa();
		$_divisas->Load_data(3); //busca Id de divisa
		$divisaEUR = $_divisas->Get_dato("div_tasa");
		//Obtener Divisa Dólar
		$_divisas1 = new Divisa();
		$_divisas1->Load_data(2);
		$divisaUSD = $_divisas1->Get_dato("div_tasa");
		?>
		<input type="hidden" name="typedoc" id="typedoc" readonly="readonly"  value="" />

		<input type="hidden" name="iu" id="iu" readonly="readonly" value='<? echo $_SESSION["idusuario"]; ?>' />
		<input type="hidden" name="ne" id="ne" readonly="readonly" value='<? echo $_SESSION["idusuario"]; ?>' />
		<input type="hidden" name="empresa" id="empresa" value='<?php echo $_SESSION["empresa"]; ?>' readonly="readonly" />
		<input type="hidden" name="guarda" id="guarda" value="" readonly="readonly" />
		<input type="hidden" id="rowCount" name="rowCount" value="0" readonly="readonly"/>
		<input type="hidden" id="rowCount2" name="rowCount2" value="0" readonly="readonly"/>
		<input type="hidden" id="rowDel" name="rowDel" value="0" readonly="readonly"/>
		<input type="hidden" name="datos_empleado2" id="datos_empleado2" value="" readonly="readonly" />
		<input type="hidden" id="sol_id" name="sol_id" value="0" readonly="readonly"/>
		<input type="hidden" id="rowCountCecos" name="rowCountCecos" value="0" readonly="readonly"/>
		<input type="hidden" id="Cecos" name="Cecos" value="0" readonly="readonly"/>
		<input type="hidden" id="Cecos_usuario" name="Cecos_usuario" value="<?php echo $idcentrocosto_original; ?>"readonly="readonly" />
		<input type="hidden" id="Cecos_refacturado" name="Cecos_refacturado" value="0" readonly="readonly"/>
		<input type="hidden" id="concepto_alim_hot" name="concepto_alim_hot" value="0" readonly="readonly"/>
		<input type="hidden" id="porcentA" name="porcentA" value="0" readonly="readonly"/>
		<input type="hidden" id="no_Comprobaciones_parciales" name="no_Comprobaciones_parciales" value="0" readonly="readonly"/>
		<input type="hidden" name="fact_chk1" id="fact_chk1" value="" readonly="readonly" />
		<input type="hidden" name="motive" id="motive" value="" readonly="readonly" />
		<input type="hidden" id="divisa_sol" name="divisa_sol" value="0" readonly="readonly"/>
		<input type="hidden" name="co_subtotal" id="co_subtotal" value="0.00" readonly="readonly" />
		<input type="hidden" name="co_iva" id="co_iva" value="0.00" readonly="readonly" />
		<!-- Divisa Euro -->
		<input type='hidden' id='valorDivisaEUR' name='valorDivisaEUR' value="<?php echo $divisaEUR; ?>">
		<!-- Divisa Dólar -->
		<input type='hidden' id='valorDivisaUSD' name='valorDivisaUSD' value="<?php echo $divisaUSD; ?>">
		<!-- aqui se carga el id del cargo amex seleccionado cuando edita un previo de tipo amex -->
		<input type='hidden' id='id_cargo_amex_seleccionado' name='id_cargo_amex_seleccionado' value="">
		<input type="hidden" name="delegado" id="delegado" readonly="readonly" value="<?php if(isset($_SESSION['iddelegado'])){ echo $_SESSION['iddelegado']; }else{echo 0;}?>" />
		<input type="hidden" name="tramiteID" id="tramiteID" readonly="readonly" value="0" />
		<input type="hidden" name="etapa" id="etapa" readonly="readonly" value="0" />
	</div>

</form>
<!-- FIN DE: if (isset($_GET['comp_solicitud'])) -->

<?php
}
/*--------------Begin  edición------------------------------------------------------------------------------*/
if (isset($_GET['edit2'])) {
	require_once("$RUTA_A/functions/utils.php");
	
	function forma_comprobacion() {
		$tipoUsuario = $_SESSION["perfil"];
		?>
<!-- Inicia forma para comprobación -->
<script language="JavaScript" src="js/backspaceGeneral.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.date_input.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.tablesorter.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.autocomplete.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.bgiframe.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.tableEditor.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.fadeSliderToggle.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.blockUI.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/formatNumber.js" type="text/javascript"></script>
<script language="JavaScript" type ="text/javascript" src="../../lib/js/jquery/jquery.jdpicker.js"></script>
<script language="JavaScript" type ="text/javascript" src="../../lib/js/jquery/jquery.jdpicker2.js"></script>
<script language="JavaScript" src="../solicitudes/js/solicitud_viaje.js" type="text/javascript"></script>
<script language="JavaScript" src="js/communication_ajax.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../css/jdpicker.css" type="text/css" media="screen" />
<link rel="stylesheet" href="../../css/jdpicker2.css" type="text/css" media="screen" />	
<script language="JavaScript" type="text/javascript">
//variables
var doc;
var arreglovalores=new Array();
var arregloestatus=new Array();
var arreglodescripcion=new Array();


doc = $(document);
doc.ready(inicializarEventos);//cuando el documento esté listo
function inicializarEventos(){
	var frm=document.invitacion_comp;
	// Bloqueamos la pantalla
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
	
	var tramite_id=gup("edit2");
	//alert(tramite_id);
	fillform(tramite_id);
	    $("#tramite_id").val(tramite_id);
	//genera la lista de sugerencia: nombre proveedor
	$("#proveedor").autocomplete("catalogo_proveedores.php", {
		minChars:1,
		matchSubset:1,
		matchContains:1,
		cacheLength:10,
		onItemSelect:seleccionaItem2,
		onFindValue:buscaRFC,
		formatItem:arreglaItem,
		maxItemsToShow:5,
		autoFill:false,
		extraParams:{tip:1}
	});//fin autocomplete

	//genera la lista de sugerencia: rfc proveedor
	$("#rfc").autocomplete("catalogo_proveedores.php", {
		minChars:1,
		matchSubset:1,
		matchContains:1,
		cacheLength:10,
		onItemSelect:seleccionaItem,
		onFindValue:buscaProveedor,
		formatItem:arreglaItem,
		maxItemsToShow:5,
		autoFill:false,
		extraParams:{tip:2}
	});//fin autocomplete
	
	// Se usa para controlar el div de agregar proveedor
	$(".fadeNext").click(function(){
		$(this).next().fadeSliderToggle();

		return false;
	});

	$("#fecha").jdPicker({
		date_format:"dd/mm/YYYY", 
		//date_min:"<?php echo date("d/m/Y"); ?>",
		month_names: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
		short_month_names: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
		short_day_names: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sab"]
	});

	if(navigator.appName!='Netscape'){
		document.getElementById('msg_div').style.width="800px";
		document.getElementById('msg_div').style.height="300px";
		document.getElementById('msg_div').style.top="25%";
		document.getElementById('msg_div').style.left="18.5%";
	}

	$('#fecha').keydown(function(e){
		ignoraEventKey(e);				
	});
	
	$("input").bind("keydown", function(e){
		if(!isAlphaNumeric(e)) return false;
	});

	//bloqueo de teclas en caracteres extraños para proveedor
	
	$("#new_proveedor").bind("keydown", function(e){		
		if(!isAlphaNumericRFC(e)) return false;
	});	

	$("#new_p_rfc").bind("keydown", function(e){					
		if(!isAlphaNumericRFC(e)) return false;
	});

	$("#rfc").bind("keydown", function(e){					
		if(!isAlphaNumericRFC(e)) return false;
	});
	
	/* 
	 * Impedimos que salga de la pantalla al teclear el backspace en algún botón
	 */
	 
	$('#agregarInv').focus(function() {
		confirmaRegreso('agregarInv');
	});
	
	$('#add_rem_prv').focus(function() {
		confirmaRegreso('add_rem_prv');
	});

	$('#agregar').focus(function() {
		confirmaRegreso('agregar');
	});
	
	$('#guardarCompprevedit').focus(function() {
		confirmaRegreso('guardarCompprevedit');
	});

	$('#guardarCompedit').focus(function() {
		confirmaRegreso('guardarCompedit');
	});
	
	$('#fact_chk').focus(function() {
		confirmaRegreso('fact_chk');
	});	

	$('#cambia_table').focus(function() {
		confirmaRegreso('cambia_table');
	});

	$('#enviaDirector').focus(function() {
		confirmaRegreso('enviaDirector');
	});
	
}//fin ready ó inicializarEventos

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

/*--------------Begin  Fillform------------------------------------------------------------------------------*/
function fillform(id_solicitud){
	var frm=document.invitacion_comp;
	var etapa = 0;
	var tipo_comp = 0;
	var Cecos = 0;
	var divisa = 0;
	var rfcFactura = "";
	var dc_rfc = "";
	var dc_folio_factura = "";

	    $.ajax({
			type: "POST",
			url: "services/Ajax_comprobacion.php",
			data: "comprobacionSI="+id_solicitud,
			dataType: "json",
			timeout: 10000,
			success: function(json){
				// Etapa de la Comprobación 
				etapa = json[0].t_etapa_actual;
				$("#etapa").val(etapa);
				
				// Cargar ID del Tramite
				$("#solicitud_de_invitacion").val(json[0].co_tramite);

				// Cargar Ciudad de la Solicitud de Invitación
				$("#ciudad_data").html(json[0].si_ciudad);
				$("#co_ciudad_data").val(json[0].si_ciudad);

				// Cargar el lugar de Invitación
				$("#lugar_inv").val(json[0].co_lugar);

				// Cargar Fecha de Factura
				$("#fecha").val(json[0].dc_factura);

				// Cargar Fecha de Invitación
				$("#invitacion_dato").html(json[0].si_fecha_invitacion);
				$("#fechainvitacion").val(json[0].si_fecha_invitacion_bd);

				// Cargar Monto de la Comprobación
				$("#monto").val(json[0].dc_monto);

				// Cargar IVA de la Comprobación
				$("#impuesto").val(json[0].dc_iva);

				// Cargar Propinas de la Comprobación
				$("#propina_dato").val(json[0].dc_propinas);
				
				// Carga de Observaciones
				if(etapa == 4 || etapa == 5 || etapa == 10 || json[0].t_autorizaciones_historial != ""){
					$("#historial_observaciones").val(json[0].co_observaciones);
				}

				$("#observ").val(json[0].co_observaciones_edicion);
				
				/* Almacenamos el tipo de comprobación para cuando este se haya completado, 
				*  enviemos el dato como parametro 
				*/
				tipo_comp = json[0].co_tipo;

				/*
				* Guardamos el CECO en una variable
				*/
				Cecos = json[0].cc_centrocostos;

				/*
				* Guardamos la Divisa de la Comprobación
				*/
				divisa = json[0].dc_divisa;
				
				/*
				* Guardamos el RFC de la Comprobación
				*/
				rfcFactura = json[0].pro_proveedor;
				$("#rfcComprobacion").val(rfcFactura);
				if(rfcFactura != ""){
					dc_rfc = json[0].dc_rfc;
					dc_folio_factura = json[0].dc_folio_factura;
				}
			}, 
			complete: function(json){
				// Traer el compentario de la Comprobación
				$.ajax({
					type: "POST",
					url: "services/Ajax_comprobacion.php",
					data: "tram_coment="+id_solicitud,
					timeout: 10000,
					success: function(jsonComentarios){
						// Carga de los comentarios de la Comprobación
						$("#comentarios").val(jsonComentarios);
					},
					complete: function(jsonComentarios){
						// Carga Tipo de Comprobación
						var combo = document.invitacion_comp.tipo;
						var cantidad = combo.length;
						
						for (var i = 1; i < cantidad; i++) {
							var toks=combo[i].value;
							if (toks==tipo_comp){
								combo[i].selected = true;
								
								if(tipo_comp=="amex"){
									//En este ajax se obtiene el id del cargo que se guardo en el previo
									$.ajax({
										type: "POST",
										url: "services/Ajax_comprobacion.php",
										data: "id_de_comp="+id_solicitud,
										timeout: 10000,
										success: function(jsontipo){
											if(jsontipo != 0){
												$("#select_tipo_tarjeta").val(1);
												//Se guarda el id del cargo amex seleccionado en un campo oculto en el formulario para despues seleccionarlo
												$("#id_cargo_amex_seleccionado").val(jsontipo);
												//La seleccion de un cargo de la lista de cargos se realiza en la funcion "LlenarCombo2"
											}
										},
										complete: function(jsontipo){
											verificar_tipo_tarjeta();
										},
										error: function(x, t, m) {
											if(t==="timeout") {
												location.reload();
												abort();
											}
										}
									});
									tipo_de_comprobacion(tipo_comp);
								}
								break;
							}
						}
						
						// Carga el CECO de la Comprobación
						/* Seleccionar CECO */
						if(Cecos != null){
							seleccionar(Cecos);
						}

						// Carga la divisa de la Comprobación
						var combo = frm.moneda;
						var cantidad = combo.length;
						for (var i = 1; i < cantidad; i++) {
							var toks=combo[i].value;
							if (toks == divisa) {
								combo[i].selected = true;
								break;
							}
						}
						
						// Verificamos si la Comprobación se registro con un RFC
						if(rfcFactura != ""){
							$("input[name=fact_chk]").attr("checked",true);
							$("#fact_chk1").val("on");
							
							$("#iva_label").fadeIn();
							$("#impuesto").fadeIn();
							$("#datosProveedor").fadeIn();
							
						  	$("#proveedor").val(rfcFactura);
						  	$("#rfc").val(dc_rfc);
						  	$("#d_folio").val(dc_folio_factura);

						  	cargarInvitados(id_solicitud, "cargaInicial");
						}else{
							$("input[name=fact_chk]").attr("fact_chk",false);
						  	$("#proveedor").val("");
						  	$("#rfc").val("");
						  	$("#d_folio").val("");
						  	cargarInvitados(id_solicitud, "cargaInicial");
						}
						
						// Inhabilita los campos cuando sea la etapa Devuelto con Observaciones
						verificaEtapaTramite();
					},
					error: function(x, t, m) {
						if(t==="timeout") {
							location.reload();
							abort();
						} 
					}
				});
			},
			error: function(x, t, m) {
				if(t==="timeout") {
					location.reload();
					abort();
				}
			}
		});
		
		if($("#delegado").val != 0){
			$("#enviaDirector").removeAttr("disabled");
		}else{
			$("#guardarCompedit").removeAttr("disabled");
		}
}
/*--------------end Fillform----------------------------------------------------------------------------------*/

function verificaEtapaTramite(){
	var etapa = $("#etapa").val();
	if(etapa != 5){
		// Habilitar botón de Envio de Solicitud
		$("#guardarCompprevedit").removeAttr("disabled");

		// Habilitar Solicitud de Invitación, Tipo de Comprobación y CECOS
		$("#solicitud_de_invitacion").removeAttr("disabled");
		$("#tipo").removeAttr("disabled");
		$("#centro_de_costos").removeAttr("disabled");

		// Habilitar listas de Tipo de Tarjeta y Lista de Cargos 
		$("#solicitud_de_invitacion").removeAttr("disabled");
		$("#select_tipo_tarjeta").removeAttr("disabled");
		$("#select_tarjeta_cargo").removeAttr("disabled");
	}else{
		// Deshabilitar botón de Guardado Previo
		$("#guardarCompprevedit").attr("disabled", "disabled");

		// Desactivar Solicitud de Invitación, Tipo de Comprobación y CECOS
		$("#solicitud_de_invitacion").attr("disabled", "disabled");
		$("#tipo").attr("disabled", "disabled");
		$("#centro_de_costos").attr("disabled", "disabled");
		
		// Desactivar las listas Tipo de Tarjeta y Lista de Cargos
		$("#select_tipo_tarjeta").attr("disabled", "disabled");
		$("#select_tarjeta_cargo").attr("disabled", "disabled");
	}
	$.unblockUI();
}

function clean_comprobacion(){
	$("#monto").val(0);
	$("#monto_pesosDisabled").val(0);
	$("#monto_pesos").val(0);
	$("#propina_dato").val(0);
	$("totalDisabled").val("0");
	$("total").val("0");
	$("#observ").val("");
	$("input[name=fact_chk]").attr("checked",false);
	$("#impuesto").fadeOut();
	$("#datosProveedor").fadeOut();
	document.getElementById("moneda").removeAttribute("disabled");
	document.getElementById("impuesto").value = 0;
  	$("#proveedor").val("");
  	$("#rfc").val("");
  	$("#d_folio").val("");
  	$("#numInvitadosDisabled").val("0");
  	$("#numInvitados").val("0");
  	$("#impuesto").val(""); 
	$("#moneda").val("-1");
	$("#lugar_inv").val("");
	$("#invitacion_dato").html("");
	document.getElementById("fechainvitacion").value = "";
	VaciarTabla();

  	getTotal();
  	//recalculaMontos();
}

function enviar_formulario(){
	document.invitacion_comp.submit();
}
//solicitar confirmación de previo
function solicitarConfirmPrevio(){
	 var frm=document.invitacion_comp;
	if(confirm("¿Desea guardar esta Comprobación como previo?")){
		if($("#tipo").val() == -1){
			alert("Seleccione el tipo de comprobación que desea Guardar.");
			return false;
		}else if($("#centro_de_costos").val() == -1){
			alert("Seleccione un Centro de Costos.");
			return false;
		}else{
			frm.submit();
		}
	}else{
		return false;
	}
}

//Solicitar confirmación de guardado
function solicitarConfirmarGuardado(){
	var frm=document.invitacion_comp;
	var etapa = $("#etapa").val();
	
	if(confirm("¿Desea enviar la Comprobación?")){
		if(etapa == 5){ // Etapa Devuelto con observaciones
			// Habilitar Solicitud de Invitacipon, Tipo de Comprobación y CECOS
			$("#solicitud_de_invitacion").removeAttr("disabled");
			$("#tipo").removeAttr("disabled");
			$("#centro_de_costos").removeAttr("disabled");

			// Habilitar listas de Tipo de Tarjeta y Lista de Cargos 
			$("#solicitud_de_invitacion").removeAttr("disabled");
			$("#select_tipo_tarjeta").removeAttr("disabled");
			$("#select_tarjeta_cargo").removeAttr("disabled");
		}
		return true;
	}else{
		return false;
	}
}
   
function guardaComprobacionprev(){
	var frm=document.invitacion_comp;
	if(parseInt($("#solicitud_de_invitacion").val())>-1)
	{
		$("#guardarCompprevedit").removeAttr("disabled");
	}
	else
	{
		$("#guardarCompprevedit").attr("disabled", "disabled");
	}
}

function buscaProveedor(li) {
    var rfc_pro = $("#rfc").val();        
    var url = "services/catalogo_proveedores.php";
    $.post(url,{nombre:rfc_pro,tip:1},function(data){
            $("#proveedor").val(data);
            $("#load_div").html("");                
    });
}//fin buscaProveedor

function buscaRFC(li) {
    var name_pro = $("#proveedor").val();        
    var url = "services/catalogo_proveedores.php";
    $.post(url,{nombre:name_pro,tip:2},function(data){
            $("#rfc").val(data);
            $("#load_div").html("");                
    });
}//fin buscaRFC

function seleccionaItem(li) {
buscaProveedor(li);
}//fin seleccionaItem

function seleccionaItem2(li) {
buscaRFC(li);
}//fin seleccionaItem

function arreglaItem(row) {
//da el formato a la lista
return row[0];
}//fin arreglaItem

function tipo_de_comprobacion(valor){
	var frm=document.invitacion_comp;
	
	if(frm.tipo.value == "amex"){
		$("#seccion_amex").slideDown(500);
		$("#seccion_amex").css("display", "block");
		$("#seccion_amex").addClass("visible");
	}else{
		//ocultar "seccion_amex"
		if($("#seccion_amex").hasClass("visible")){
			$("#seccion_amex").slideUp(500);
			$("#seccion_amex").removeClass("visible");
			$("#fact_chk").removeAttr("disabled");
		}
	}
	if(valor == "reembolso_para_empleado"){
		$("#g_reembolso").html($("#monto_pesos").val().replace(/,/g,"")+" MXN");
		$("#t_reembolso").val($("#monto_pesos").val());
		$("#g_amex_comprobado").html("0.00 MXN");
		$("#t_amex_comprobado").val(0);
		$("#g_comprobado").html($("#monto_pesos").val().replace(/,/g,"")+" MXN");
		$("#t_comprobado").val($("#monto_pesos").val());
	}else if(valor == "amex"){
		$("#g_amex_comprobado").html($("#monto_pesos").val().replace(/,/g,"")+" MXN");
		$("#t_amex_comprobado").val($("#monto_pesos").val());
		$("#g_reembolso").html("0.00 MXN");
		$("#t_reembolso").val(0);
		$("#g_comprobado").html($("#monto_pesos").val().replace(/,/g,"")+" MXN");
		$("#t_comprobado").val($("#monto_pesos").val());
	}else if(valor == "-1"){
		$("#g_amex_comprobado").html("0.00 MXN");
		$("#t_amex_comprobado").val(0);
		$("#g_reembolso").html("0.00 MXN");
		$("#t_reembolso").val(0);
		$("#g_comprobado").html("0.00 MXN");
		$("#t_comprobado").val(0);
	}
	limpiar_cuadro_amex();
}

function verificar_tipo_tarjeta(){
	var frm= document.invitacion_comp;
	var tipo_tarjeta=frm.select_tipo_tarjeta.value;
	var etapa = $("#etapa").val();
	var cargoAMEX = $("#id_cargo_amex_seleccionado").val();
	
	var noemple=<?php 
		if(isset($_SESSION['iddelegado'])){ 
			$iduser = $_SESSION['iddelegado']; 
		}else{
			$iduser = $_SESSION["idusuario"];
		} 
		echo $iduser;?>;
	var noTarjetaEmpleado = 0;
			
	if(tipo_tarjeta != "" && tipo_tarjeta != "-1"){
		$.ajax({
			type: "POST",
			url: "services/Ajax_comprobacion.php",
			data: "tipo_tarjeta="+tipo_tarjeta+"&usuario="+noemple,
			dataType: "html",
			timeout: 10000,
			success: function(dato){
				if(dato==""){
					$("#no_tarjeta_credito").html("Datos no Encontrados");
					$("#select_tarjeta_cargo").empty();
					$("#select_tarjeta_cargo").append('<option value="-1">Sin Datos</option>');
				}else{
					$("#no_tarjeta_credito").html(dato);
					noTarjetaEmpleado = dato;
				}
			},
			complete: function(dato){
				if(etapa == 5){
					// Etapa Devuelto con Observaciones solo cargará el cargo que se asocio a la Comprobación 
					cargoAmex(cargoAMEX);
				}else{
					obtenercargos(noTarjetaEmpleado);
				}
			},
			error: function(x, t, m) {
				if(t==="timeout") {
					location.reload();
					abort();
				} 
			}
		});
	}else{
		limpiar_cuadro_amex();
	}
}

function limpiar_cuadro_amex(){
	var frm= document.invitacion_comp;
	arreglovalores.splice(0,arreglovalores.length);
	arregloestatus.splice(0,arregloestatus.length);
	arreglodescripcion.splice(0,arreglodescripcion.length);
	LimpiarCombo(frm.select_tarjeta_cargo);
	
	$("#fecha_cargo").html("");
	$("#establecimiento_cargo").html("");
	$("#monto_cargo").html(""); 
	$("#rfc_cargo").html("");
	$("#moneda_local").html("");
	$("#amex_pesos").html("");
	$("#amex_dolar").html("");
	$("#moneda_fact_val").val("");
	$("#fecha_cargo_val").val("");
	$("#establecimiento_cargo_val").val("");
	$("#monto_cargo_val").val("");
	$("#amex_pesos_val").val("");
	$("#amex_dolar_val").val("");
	$("#rfc_cargo_val").val("");
	$("#moneda_local_val").val("");
	$("#no_tarjeta_credito").html("");
	
	$("#select_tipo_tarjeta").val("-1");
}

function deshabilitar_tipo(){
	var etapa = $("#etapa").val();
	var elemento = $("#tipo option:selected").val();

	if(etapa == 5){
		document.getElementById("tipo").setAttribute("disabled","disabled");
		$("#dato002").val(elemento);
		$("#dato002").attr("name", "tipo");
		$("#dato002").attr("id", "tipo");
	}
}

function obtenercargos(valor){
	var frm=document.invitacion_comp;
	var tramite = $("#tramiteID").val();
	LimpiarCombo(frm.select_tarjeta_cargo);
	if(valor != ""){
		$.ajax({
			type: "POST",
			url: "services/Ajax_comprobacion.php",
			data: "no_tarjeta="+valor+"&comprobacion="+tramite,
			dataType: "json",
			timeout: 10000,
			success: function(json){
				if(json==null){
					$("#select_tarjeta_cargo").append(new Option("Sin Datos"));
				}else{
					arreglovalores.splice(0,arreglovalores.length);
					arregloestatus.splice(0,arregloestatus.length);
					arreglodescripcion.splice(0,arreglodescripcion.length);
					LlenarCombo(json, frm.select_tarjeta_cargo);
					LlenarCombo2(arreglovalores,arreglodescripcion,arregloestatus,frm.select_tarjeta_cargo);
				}
			},
			error: function(x, t, m) {
				if(t==="timeout") {
					location.reload();
					abort();
				} 
			}
		});
	}
}

function LimpiarCombo(combo){
while(combo.length > 0){
	combo.remove(combo.length-1);
}
}
function LlenarCombo(json, combo){
	combo.options[0] = new Option('Selecciona un item', '');
	for(var i=0;i<json.length;i++){
		var str=json[i];
		var str1=str.slice(str.search(":")+1);
		var str2=str.substr(0,str.search(":"));
		//combo.options[combo.length] = new Option(str1,str2);
		arreglovalores[arreglovalores.length]=str1;
		arreglodescripcion[arreglodescripcion.length]=str2;
		if(arregloestatus[arregloestatus.length]!=false)
		arregloestatus[arregloestatus.length]=true;	
	}
}

function LlenarCombo2(arregloval,arreglodesc,arreglosts,combo){
	var frm=document.invitacion_comp;

	LimpiarCombo(combo);
	combo.options[0]=new Option('Selecciona un item', '');
	for(var i=0;i<arregloval.length;i++){
		if(arreglosts[i]==true){
			combo.options[combo.length] = new Option(arregloval[i],arreglodesc[i]);
			//alert(combo.options[i+1].value);
			if(combo.options[i+1].value == frm.id_cargo_amex_seleccionado.value){
				combo.options[i+1].selected = true;
				cargar_detalles();
			}
		}
	}
}

function cambiarestatusamex(valor,estatus){
	for(var i=0;i<arreglovalores.length;i++){
		if(arreglovalores[i]==valor){
			arregloestatus[i]=estatus;
			}
		}
}

//Función para rellenar combos.
function cargar_detalles(){
	var frm=document.invitacion_comp;
	var cargo_localizar=frm.select_tarjeta_cargo.value;
	$.ajax({
		type: "POST",
		url: "services/Ajax_comprobacion.php",
		data: "no_cargo="+cargo_localizar,
		dataType: "json",
		success: function(json){	
			if(json==""){
				//$("#no_corporacion").html("Datos no Encontrados");
				$("#fecha_cargo").html("Datos no Encontrados");
				$("#establecimiento_cargo").html("Datos no Encontrados");
				$("#monto_cargo").html("Datos no Encontrados"); 
				$("#rfc_cargo").html("NA");
				$("#moneda_local").html("Datos no Encontrados");
				$("#amex_pesos").html("Datos no Encontrados");
				$("#amex_dolar").html("Datos no Encontrados");
				//$("#no_corporacion_val").val("");
				$("#moneda_fact_val").val("");
				$("#fecha_cargo_val").val("");
				$("#establecimiento_cargo_val").val("");
				$("#monto_cargo_val").val("");
				$("#amex_pesos_val").val("");
				$("#amex_dolar_val").val("");
				$("#rfc_cargo_val").val("");
				$("#moneda_local_val").val("");

			}else{					
	               $("#rfc_cargo").html(json[0].rfc_establecimiento);
				   $("#rfc_cargo_val").val(json[0].rfc_establecimiento);
				   //$("#no_corporacion").html(json[0].corporacion);
	               $("#fecha_cargo").html(json[0].fecha_cargo);   
	               $("#establecimiento_cargo").html(json[0].concepto);   
	               $("#monto_cargo").html(number_format(json[0].monto,2,".",",")+" "+json[0].moneda_local);
	               //$("#amex_pesos").html("$"+json[0].monto);
	               $("#amex_dolar").html(number_format(json[0].montoAmex,2,".",",")+" "+json[0].monedaAmex);
	               $("#amex_dolar_val").val(json[0].montoAmex);
	               //$("#rfc_cargo").html(json[0].rfc_establecimiento);   
	               $("#moneda_local").html(json[0].moneda_local);
	               //$("#no_corporacion_val").val(json[0].corporacion);
	               $("#moneda_fact_val").val(json[0].monedaAmex);   
	               $("#fecha_cargo_val").val(json[0].fecha_cargo);   
	               $("#establecimiento_cargo_val").val(json[0].concepto);   
	               $("#monto_cargo_val").val(json[0].monto);
	               //$("#amex_pesos_val").val(json[0].monto);
	               $("#moneda_local_val").val(json[0].moneda_local);
	               
	               var tipo_cambio = parseFloat(json[0].montoAmex/json[0].monto);
					$("#tipo_cambio").val(tipo_cambio.toFixed(2));
					$("#div_tipo_cambio").html(tipo_cambio.toFixed(5));
	               
	               if(json[0].conversion_pesos != "" || json[0].conversion_pesos != null){
						$("#amex_pesos").html(number_format(json[0].conversion_pesos,2,".",","));
						$("#amex_pesos_val").val(json[0].conversion_pesos);
					}else{
						$("#amex_pesos").html("0");
						$("#amex_pesos_val").val("0");
					}

	               if($("#rfcComprobacion").val() != "" && json[0].rfc_establecimiento != ""){
						$("#fact_chk").attr("checked",true);
						$("#fact_chk").attr("disabled",true);
						verDatosProveedor();
	               }else if(json[0].rfc_establecimiento == "" || json[0].rfc_establecimiento == null){
						$("#fact_chk").attr("checked",false);
						$("#fact_chk").removeAttr("disabled");
						verDatosProveedor();
					}else{
						$("#fact_chk").attr("checked",true);
						$("#fact_chk").attr("disabled",true);
						verDatosProveedor();
					}
			}
		}
	});
}
	
	// Muestra el div y el campo de IVA
	function muestraIva(){
		var divisa = $("#moneda").val();
		if (divisa == "MXN"){
			$("#div_iva").css("display", "block"); 
			$("#impuesto").css("display", "block"); 
			$("#impuesto").val($("#monto").val()*.16);
			$("#totalDisabled").val( parseFloat($("#monto").val()) + parseFloat($("#impuesto").val()) );
			$("#total").val( parseFloat($("#monto").val()) + parseFloat($("#impuesto").val()) );									
		}else if(!$("#fact_chk").is(":checked")){
			$("#div_iva").css("display", "none"); 
			$("#impuesto").css("display", "none"); 				
			$("#impuesto").val(0);
			$("#totalDisabled").val( parseFloat($("#monto").val()));
			$("#total").val( parseFloat($("#monto").val()));	
		}			
		calculaTotalDolares();
	}

function calculaTotalDolares(){
	var divisa = $("#moneda option:selected").text();
	var monto = parseFloat($("#monto").val().replace(/,/g,""));
	var iva = ($("#impuesto").val() =="")? 0 : parseFloat($("#impuesto").val().replace(/,/g,""));				
	var propina = ($("#propina_dato").val() == "") ? 0 : parseFloat($("#propina_dato").val().replace(/,/g,""));
	var total = parseFloat(iva)+parseFloat(monto)+parseFloat(propina);
	total = total.toFixed(2);
	
	$("#total").val(number_format(total,2,".",","));
	var montoPesos = 0.00;
	$.ajax({
		type: "POST",
		url: "services/Ajax_comprobacion.php",
		data: "convierteDivisa="+total+"&divisa="+divisa,
		dataType: "json",
		timeout: 10000,
		success: function(json){
			montoPesos = json;
		},
		complete: function(json){
			$("#monto_pesos").val(number_format(montoPesos,2,".",","));
		}
	});
}

function obtener_tasa_USD(){
	var cargo_val = parseFloat($("#monto_cargo_val").val());
	var dolar = parseFloat($("#valorDivisaUSD").val());
	var euro = parseFloat($("#valorDivisaEUR").val());

	if($("#moneda_fact_val").val() == "MXN"){
		var monto_dolar = (cargo_val / dolar);
		var redondea_dolar = Math.round(monto_dolar * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
		$("#amex_dolar").html(number_format(redondea_dolar,2,".",",")+" USD");
		$("#amex_dolar_val").val(redondea_dolar);
		$("#amex_pesos").html(number_format(cargo_val,2,".",",")+" MXN");
		$("#amex_pesos_val").val(cargo_val);
	}else if($("#moneda_fact_val").val() == "EUR"){
		var monto_pesos = (cargo_val * euro);
		var monto_dolar = (monto_pesos / dolar);
		var redondea_dolar = Math.round(monto_dolar * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
		$("#amex_dolar").html(number_format(redondea_dolar,2,".",",")+" USD");
		$("#amex_dolar_val").val(redondea_dolar);
		$("#amex_pesos").html(number_format(cargo_val,2,".",",")+" MXN");
		$("#amex_pesos_val").val(cargo_val);
	}else{
		var monto_dolar = (cargo_val * dolar);
		var redondea_dolar = Math.round(monto_dolar * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
		$("#amex_dolar").html(number_format(cargo_val,2,".",",")+" USD");
		$("#amex_dolar_val").val(cargo_val);
		$("#amex_pesos").html(number_format(redondea_dolar,2,".",",")+" MXN");
		$("#amex_pesos_val").val(redondea_dolar);
	}
}

// Convierte una fecha del formato "2011-02-27 00:00:00" al formato "27/02/2011"
function fecha_to_mysql(strFecha){
	var toks1=strFecha.split(" ");
	var toks=toks1[0].split("-");

	var strFechaN = toks[2]+"/"+toks[1]+"/"+toks[0];
	return strFechaN;
}

function fecha_to_mysql_normal(strFecha){
	var toks1=strFecha.split(" ");
	var toks=toks1[0].split("-");

	var strFechaN = toks[0]+"/"+toks[1]+"/"+toks[2];
	return strFechaN;
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
//validación campos numericos
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
	//valor = number_format(valor,0,".",",")
	valor = number_format_sin_redondeo(valor);
	return valor;
}

function getTotal(){
	var frm=document.invitacion_comp;

	var propinaDato = frm.propina_dato.value;
	propinaDato = propinaDato.replace(/,/g,"");
	
	var montoValue = frm.monto.value;
	montoValue = montoValue.replace(/,/g,"");

	var impuestoValue = frm.impuesto.value;
	impuestoValue = impuestoValue.replace(/,/g,"");
	
	if (impuestoValue == 0 || impuestoValue == ""){
		if(propinaDato == "" || propinaDato == 0){
			if(montoValue == "" || montoValue == 0){
				frm.totalDisabled.value=0;
				frm.total.value=0;
			} else {
				//total
				var tot = parseFloat(montoValue);
				frm.totalDisabled.value =Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
				frm.total.value =Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			}
		} else {
			if(montoValue == "" || montoValue == 0){
				var tot = parseFloat(propinaDato);
				frm.totalDisabled.value =Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
				frm.total.value =Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			} else {
				//total
				var tot = parseFloat(montoValue)+parseFloat(propinaDato);
				frm.totalDisabled.value = Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
				frm.total.value = Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			}
		}
	} else { 
		if(propinaDato == "" || propinaDato == 0){
			if(montoValue == "" || montoValue == 0){
				var tot = parseFloat(impuestoValue);
				frm.totalDisabled.value = Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
				frm.total.value = Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			} else {
				//total
				var tot = parseFloat(montoValue) + parseFloat(impuestoValue);
				frm.totalDisabled.value =Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
				frm.total.value =Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			}
		} else {
			if(montoValue == "" || montoValue == 0){
				var tot = parseFloat(propinaDato) + parseFloat(impuestoValue);
				frm.totalDisabled.value =Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
				frm.total.value =Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			} else {
				//total
				var tot = parseFloat(montoValue)+parseFloat(propinaDato) + parseFloat(impuestoValue);
				frm.totalDisabled.value = Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);
				frm.total.value = Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			}
		}
	}
	frm.totalDisabled.value = number_format(frm.total.value,2,".",",");
	frm.total.value = number_format(frm.total.value,2,".",",");
}

function evaluaMonto(monto){
	$("#capaWarning").html("");
    var mensajeExcedePoliticas = undefined;
    //Variables de las cajas de Texto 
    var divEuro = parseFloat($("#valorDivisaEUR").val());
    //var monto = parseFloat($("#tpesos").val());
    var num_invitados = parseFloat($("#numInvitados").val());
    //Variable para guardar 
    var monto2 = 0;
    monto2 = ((monto / divEuro) / num_invitados );
    
    if(monto2 > 50 && mensajeExcedePoliticas == undefined){
    	mensajeExcedePoliticas = "<strong>Esta rebasando la pol&iacute;tica del concepto. <br>El monto m&aacute;ximo es de 50.00 Euros por persona.</strong>";
    	conceptoExcedePoliticas = true;                                        
    } else {
    	conceptoExcedePoliticas = false;
    }
    
    if(conceptoExcedePoliticas){
        $("#capaWarning").html(mensajeExcedePoliticas);
        $("#obsjus").html("Agregar justificaci&oacute;n detallada del motivo del excedente<span class='style1'>*</span>:");
        //$("#comment").html("Comentarios <span class='style1'>*</span>: "); 
        document.getElementById("banderavalida").value = 1;                   
    } else {
    	$("#obsjus").html("Observaciones: ");
    	//$("#comment").html("Comentarios: "); 
    	document.getElementById("banderavalida").value = 0;
    }
}

function recalculaMontos(){
    var total = parseFloat(($("#total").val()).replace(/,/g,""));
    var totalAnticipo = 0;
	var divisas = $("#moneda").val();
	var etapa = $("#etapa").val();
	var tipo = "";

	var tasaNueva = 1;
	if(divisas != 1){ //Si la divisa es diferente a MXN
		//Se obtiene las tasas de las divisas
		var tasa = "<?
		$query = sprintf('SELECT DIV_ID,DIV_TASA FROM divisa');
		$var = mysql_query($query);
		$aux="";
		while ($arr = mysql_fetch_assoc($var)) {
			$aux.=$arr['DIV_ID'].":".$arr['DIV_TASA'].":";
		}
		echo $aux;?>";
		var tasa2 = tasa.split(":");
		
		//Se obtiene la tasa de la divisa seleccionada
		for(var i=0;i<=tasa2.length;i=i+2){
			if(tasa2[i] == divisas){
				tasaNueva = tasa2[i+1];
			}
		}
	}
	document.getElementById("tasa").value = tasaNueva;
	var redondear = 0;
	var frm=document.invitacion_comp;
	var totalTotal = total * parseFloat(tasaNueva);
	var redondear = Math.round(totalTotal * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
	//document.invitacion_comp.total.value = Math.round(totalTotal * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
	document.invitacion_comp.monto_pesosDisabled.value = number_format(redondear,2,".",",");	
	document.invitacion_comp.monto_pesos.value = number_format(redondear,2,".",",");
	
	if(etapa != 5){
		tipo = document.invitacion_comp.tipo.value;
	}else{
		tipo = $("#tipo").val();
	}
	
	if(tipo == "reembolso_para_empleado"){
		$("#g_reembolso").html(number_format(redondear,2,".",",")+" MXN");
		$("#t_reembolso").val(redondear);
		$("#g_amex_comprobado").html("0.00 MXN");
		$("#t_amex_comprobado").val(0);
		$("#g_comprobado").html(number_format(redondear,2,".",",")+" MXN");
		$("#t_comprobado").val(redondear);
	}else if(tipo == "amex"){
		$("#g_amex_comprobado").html(number_format(redondear,2,".",",")+" MXN");
		$("#t_amex_comprobado").val(redondear);
		$("#g_reembolso").html("0.00 MXN");
		$("#t_reembolso").val(0);
		$("#g_comprobado").html(number_format(redondear,2,".",",")+" MXN");
		$("#t_comprobado").val(redondear);
	}else if(tipo == "-1"){
		$("#g_amex_comprobado").html("0.00 MXN");
		$("#t_amex_comprobado").val(0);
		$("#g_reembolso").html("0.00 MXN");
		$("#t_reembolso").val(0);
		$("#g_comprobado").html("0.00 MXN");
		$("#t_comprobado").val(0);
	}
	
    evaluaMonto(totalTotal);
    
}

function getTotal2(){
	var frm=document.invitacion_comp;
	if(frm.propina_dato.value=="")
		frm.propina_dato.value=0;				
	frm.select_impuesto.value=0;
	if(frm.impuesto.value!="" && frm.impuesto.value>0 )
		frm.totalDisabled.value=parseFloat(frm.impuesto.value)+parseFloat(frm.monto.value)+parseFloat(frm.propina_dato.value);
		frm.total.value=parseFloat(frm.impuesto.value)+parseFloat(frm.monto.value)+parseFloat(frm.propina_dato.value);
}

//IVA
function valor($combo){
	var frm=document.invitacion_comp;
	if(frm.monto.value=="" || frm.monto.value==0)
	{
		frm.monto.value=0;
		frm.impuesto.value=0;
		frm.totalDisabled.value=0;
		frm.total.value=0;
		if(frm.select_impuesto.value==0)
		{
			frm.impuesto.value=0;
			frm.impuesto.disabled=true;
			frm.totalDisabled.value=parseFloat(frm.monto.value);
			frm.total.value=parseFloat(frm.monto.value);
		}
		else
		{
			//frm.impuesto.disabled=false;
			//impuesto
			Total = frm.total.value;
			Iva = ((frm.select_impuesto.options[frm.select_impuesto.selectedIndex].value / 100)+1);
			var imp = Total / Iva;
			//SubTotal
			frm.impuesto.disabled=false;
			frm.monto.value=Math.round(imp * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			//Iva
			MontoIva = (Iva - 1) * imp;
			var tot=MontoIva;
			frm.impuesto.value=Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
		}
	}
	else
	{
		if(frm.select_impuesto.value==0)
		{
			frm.impuesto.value=0;
			frm.impuesto.disabled=true;
			frm.totalDisabled.value=parseFloat(frm.monto.value);
			frm.total.value=parseFloat(frm.monto.value);
		}
		else
		{
			//impuesto
			var imp=(frm.select_impuesto.options[frm.select_impuesto.selectedIndex].value*0.01)*(frm.monto.value);
			frm.impuesto.disabled=false;
			frm.impuesto.value=Math.round(imp * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			//total
			var tot=parseFloat(frm.impuesto.value)+parseFloat(frm.monto.value);
			frm.totalDisabled.value=Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			frm.total.value=Math.round(tot * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
		}

	}
}//fin valor

//Checar fechas
function checafecha(strFecha, diasmax){
	var toks=strFecha.split("/");

	strFechaN=toks[2]+"/"+toks[1]+"/"+toks[0];
	var fecha = new Date();
	fecha.setTime(Date.parse(strFechaN));
	var today=new Date();
	var agedays = Math.floor((today - fecha) / 24 / 60 / 60 / 1000);
	if(agedays>diasmax){
		var hoystr=<?php echo "\"" . date("d/m/Y") . "\";"; ?>
		document.invitacion_comp.fecha.value=hoystr;
		return false;
	}else{
		return true;
	}
}
//FIN checar fechas

function limpiarCamposDeProveedor(valor){
	if(valor){
		$("#new_proveedor").val("");
		$("#new_p_rfc").val("");
		$("#new_p_addr").val("");
	}else{
		$("#proveedor").val("");
		$("#rfc").val("");
		$("#d_folio").val("");
	}
}

// Ver los datos del proveedor
function verDatosProveedor(){
	var frm=document.invitacion_comp;
	if(frm.fact_chk.checked){
		$("#iva_label").css("display", "block");
		$("#impuesto").css("display", "block");
		$("#datosProveedor").css("display", "block");
		frm.moneda.selectedIndex = 1;
		activaIva();
		getTotal();
		recalculaMontos();
		$("#fact_chk1").val("on");		
	}else{
		$("#iva_label").css("display", "none");
		$("#impuesto").css("display", "none");
		$("#datosProveedor").css("display", "none");
		activaIva();
		getTotal();
		recalculaMontos();
		limpiarCamposDeProveedor(0);
	}
	// Bandera para indicar que se ha concluido la carga de la página
	$("#cargaEdicion").val(0);
}
//FIN ver los datos del proveedor

//Activar campo IVA
function activaIva(){
	var cargaPagina = $("#cargaEdicion").val();
	var proveedor_amex = ($("#fact_chk").is(':checked'))?true:false;
	var moneda = $("#moneda option:selected").val();
	if(moneda == 1 || proveedor_amex){
		var monto = $("#monto").val().replace(/,/g,"");
		$("#iva_label").css("display", "block");
		$("#impuesto").css("display", "block");

		if(cargaPagina != 1){
			$("#impuesto").val(monto * .16);
		}
		
		getTotal();
		recalculaMontos();		
	}else if(!$("#fact_chk").is(":checked")){
		$("#iva_label").css("display", "none");
		$("#impuesto").css("display", "none");
		$("#impuesto").val(0);
		getTotal();
		recalculaMontos();
	}
}
//FIN de activar campo IVA

//Divisa
function cambiaTasa(divisa){
	var frm=document.invitacion_comp;
	if(divisa != ""){
		$.ajax({
			type: "POST",
			url: "services/Ajax_comprobacion.php",
			data: "divisa="+divisa+"&divisa2='1'",
			dataType: "json",
			success: function(json){
				if(json==null){
				}else{
					document.getElementById("tasa").value = json[0];
				}
			}
		});
	}
}

function seleccionar_centro_de_costos(valor){
	var frm=document.invitacion_comp;
	//centro_de_costos;
	document.getElementById("tipo").removeAttribute("disabled");
	document.getElementById("centro_de_costos").removeAttribute("disabled");

	if (frm.solicitud_de_invitacion.selectedIndex ==0 || frm.solicitud_de_invitacion.value=="-1"){
		document.getElementById("tipo").setAttribute("disabled","disabled");
		document.getElementById("centro_de_costos").setAttribute("disabled","disabled");
	}

	
	if(valor != "" && valor != "-1"){
		$.ajax({
			type: "POST",
			url: "services/Ajax_comprobacion.php",
			data: "t_id4="+valor,
			dataType: "json",
			success: function(json){
				if(json==null){
				}else{
					seleccionar(json[0]);
				}
			}
		});
	}
}
//Seleccionar elemento de un combo
function seleccionar(elemento) {
	var etapa = "<?php $tramite = $_GET['edit2'];
		$FTramite =  new Tramite();
		$FTramite->Load_Tramite($tramite);
		$tEtapa = $FTramite->Get_dato("t_etapa_actual");
		//error_log("Ettttttttttttttapa: ".$tEtapa);
		echo $tEtapa;?>";
		
   var combo = document.invitacion_comp.centro_de_costos;
   var cantidad = combo.length;
   for (var i = 1; i < cantidad; i++) {
      var toks=combo[i].text.split("-");
      if (toks[0] == elemento) {
         combo[i].selected = true;
		 break;
      }
   }
   
	if(etapa == 5){
		document.getElementById("centro_de_costos").setAttribute("disabled","disabled");
		$("#devuelto").val(elemento);
		$("#devuelto").attr("name", "centro_de_costos");
		$("#devuelto").attr("id", "centro_de_costos");
	}
}
//Cargar invitados en la tabla
function cargarInvitados(valor, carga){
	var frm=document.invitacion_comp;
	if(valor != "" && valor!="-1"){
		if(carga === "cargaSeleccionLista"){
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
		}
		
		$.ajax({
			type: "POST",
			url: "services/Ajax_comprobacion.php",
			data: "t_id2="+valor,
			dataType: "json",
			timeout: 10000, 
			success: function(json){			
				if(json==null){
					VaciarTabla();
					document.getElementById("numInvitadosDisabled").value = 0;
					document.getElementById("numInvitados").value = 0;
				}else{
					VaciarTabla();
					LlenarTabla(json, frm.invitado_table);
				}
			},
			complete: function(json){
				activaIva();
				getTotal();
				recalculaMontos();
				if(carga === "cargaSeleccionLista"){
					$.unblockUI();
				}
			},
			error: function(x, t, m) {
				if(t==="timeout") {
					location.reload();
					abort();
				} 
			}
		});
	}
}

function cargarCiudad(valor){
	if(valor!="" && valor!="-1"){
		$.ajax({
			type: "POST",
			url: "services/Ajax_comprobacion.php",
			data: "sol_ciud="+valor,
			success: function(json){			
				if(json==null){
					$("#ciudad_data").html("Datos no encontrados");
				}else{
					$("#ciudad_data").html(json);
					$("#co_ciudad_data").val(json);
				}
			}
		});
		}
}

function cargarLugar(valor){
	var tramite = <?php $tramiteComp = $_GET['edit2'];
	echo $tramiteComp;?>;
	if(valor!="" && valor!="-1"){
		$.ajax({
			type: "POST",
			url: "services/Ajax_comprobacion.php",
			data: "tramSolicitud="+tramite,
			success: function(json){
				if(json == null){
					document.getElementById("lugar_inv").value = "NULL";
				} else {
					document.getElementById("lugar_inv").value = json;
				}
			}
		});
	}
}

function inhabilita_campos(elemento){
	var etapa = $("#etapa").val();

	if(etapa == 5){
		document.getElementById("solicitud_de_invitacion").setAttribute("disabled","disabled");
		$("#dato001").val(elemento);
		$("#dato001").attr("name", "solicitud_de_invitacion");
		$("#dato001").attr("id", "solicitud_de_invitacion");
	}
}

function cargarFecha(valor){
	var finvitacion = "";
	var finvitacionbd = "";
	if(valor!="" && valor!="-1"){
		$.ajax({
			type: "POST",
			url: "services/Ajax_comprobacion.php",
			data: "tramiteid="+valor,
			success: function(json){
				if(json==null){
					$("#invitacion_dato").html("Datos no encontrados");
				} else {
					finvitacion = fecha_to_mysql_normal(json);
					$("#invitacion_dato").html(finvitacion);
					finvitacionbd = fecha_to_mysql(json);
					document.getElementById("fechainvitacion").value = finvitacionbd;
					inhabilita_campos(valor);
				}
			}
		});
	}
}

function cargarMontoPesos(valor){
	if(valor!=""){
		$.ajax({
			type: "POST",
			url: "services/Ajax_comprobacion.php",
			data: "idsolic="+valor,
			success: function(json){
				if(json == null){
					document.getElementById("monto_pesosDisabled").value = "0";
					document.getElementById("monto_pesos").value = "NULL";
				} else {
					document.getElementById("monto_pesosDisabled").value = number_format(json,0,".",",");
					document.getElementById("monto_pesos").value = number_format(json,0,".",",");
				}
			}
		});
	}
}

function cambiaTitulo(){
	var divEuro = parseFloat($("#valorDivisaEUR").val());
	var total = parseFloat(($("#monto_pesos").val()).replace(/,/g,""));
	var invitados = parseFloat($("#numInvitados").val());

	//Variable para guardar 
    var monto2 = 0;
    monto2 = ((total / divEuro) / invitados );
    
    if(monto2 > 50){
    	mensajeExcedePoliticas = "<strong>Esta rebasando la pol&iacute;tica del concepto. <br>El monto m&aacute;ximo es de 50.00 Euros por persona.</strong>";
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
    	$("#capaWarning").html("");
    	document.getElementById("banderavalida").value = 0;
    }
}

function LlenarTabla(json, tabla){	
	var frm=document.invitacion_comp;
	for(var i=0;i<json.length;i++){
		
		var toks=json[i].split(":");
		
		//Creamos la nueva fila y sus respectivas columnas
		var nuevaFila='<tr>';
		nuevaFila+="<td>"+"<div id='renglonI"+(i+1)+"' name='renglonI"+(i+1)+"'>"+(i+1)+"</div>"+"<input type='hidden' name='row2"+(i+1)+"' id='row2"+(i+1)+"' value='"+(i+1)+"' readonly='readonly' /></td>";
		nuevaFila+="<td><input type='hidden' name='nombre"+(i+1)+"' id='nombre"+(i+1)+"' value='"+toks[0]+"' readonly='readonly' />"+toks[0]+"</td>";
		nuevaFila+="<td><input type='hidden' name='puesto"+(i+1)+"' id='puesto"+(i+1)+"' value='"+toks[1]+"' readonly='readonly' />"+toks[1]+"</td>";
		nuevaFila+="<td><input type='hidden' name='empresa"+(i+1)+"' id='empresa"+(i+1)+"' value='"+toks[2]+"' readonly='readonly' />"+toks[2]+"</td>";
		nuevaFila+="<td ><input type='hidden' name='tipoinv"+(i+1)+"' id='tipoinv"+(i+1)+"' value='"+toks[3]+"' readonly='readonly' />"+toks[3]+"</td>";
		nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+(i+1)+"del' id='"+(i+1)+"del' onmousedown='eliminarInvitado(this.id);' style='cursor:pointer;' /></div><div align='center'>Eliminar Partida</div></td>";
		nuevaFila+= '</tr>';
		frm.rowCount2.value=parseInt(frm.rowCount2.value)+parseInt(1);
		$("#invitado_table").append(nuevaFila);
	}
	document.getElementById("numInvitadosDisabled").value = json.length;
	document.getElementById("numInvitados").value = json.length;
}

function VaciarTabla() {
	var TABLE = document.getElementById("invitado_table");
	for(var i=TABLE.rows.length-1;i>=1;i--){
		TABLE.deleteRow(i);
	}
}
//Agregar nuevo invitado
function agregarNuevoInvitado(){
	if(document.getElementById("agregarInv").value == "Agregar Invitado"){
		document.getElementById("agregarInv").value = "Guardar";
		document.getElementById("agregarInvitado").style.display = "";
		//document.getElementById("registrar_comp").setAttribute("disabled","disabled");
	}else{ //en caso de que se oprima el boton Guardar		
		document.getElementById("agregarInv").value = "Agregar Invitado";
		document.getElementById("agregarInvitado").style.display = "none";
		insertarInvitadoTabla();
		//document.getElementById("registrar_comp").removeAttribute("disabled");
	}
}
// FIN Agregar nuevo invitado
// Agregar partida
function insertarInvitadoTabla(){
	var nuevaFila='<tr>';
	var frm=document.invitacion_comp;
	var invitados=parseFloat($("#numInvitados").val());
	id=parseInt($("#invitado_table").find("tr:last").find("div").eq(0).html());

	if($("#nombre_invitado").val()==""){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
		document.getElementById("agregarInv").click();
		$("#nombre_invitado").focus();
	}else if($("#puesto_invitado").val()==""){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
		document.getElementById("agregarInv").click();
		$("#puesto_invitado").focus();
	}else if($("#empresa_invitado").val()==""){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
		document.getElementById("agregarInv").click();
		$("#empresa_invitado").focus();
	}else if($("#tipo_invitado").val()==-1){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");		
		document.getElementById("agregarInv").click();
		$("#tipo_invitado").focus();
	}else{
		if(isNaN(id)){
			id=1;
		}else{
			id+=parseInt(1);
		}
		nuevaFila+="<td>"+"<div id='renglonI"+id+"' name='renglonI"+id+"'>"+id+"</div>"+"<input type='hidden' name='row2"+id+"' id='row2"+id+"' value='"+id+"' readonly='readonly' /></td>";
		nuevaFila+="<td><input type='hidden' name='nombre"+id+"' id='nombre"+id+"' value='"+$("#nombre_invitado").val()+"' readonly='readonly' />"+$("#nombre_invitado").val()+"</td>";
		nuevaFila+="<td><input type='hidden' name='puesto"+id+"' id='puesto"+id+"' value='"+$("#puesto_invitado").val()+"' readonly='readonly' />"+$("#puesto_invitado").val()+"</td>";
		nuevaFila+="<td><input type='hidden' name='empresa"+id+"' id='empresa"+id+"' value='"+$("#empresa_invitado").val()+"' readonly='readonly' />"+$("#empresa_invitado").val()+"</td>";
		nuevaFila+="<td ><input type='hidden' name='tipoinv"+id+"' id='tipoinv"+id+"' value='"+$("#tipo_invitado").val()+"' readonly='readonly' />"+$("#tipo_invitado").val()+"</td>";
		nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+id+"del' id='"+id+"del' onmousedown='eliminarInvitado(this.id);' style='cursor:pointer;' /></div><div align='center'>Eliminar Partida</div></td>";
		nuevaFila+= '</tr>';

		$("#invitado_table").append(nuevaFila);
		invitados=invitados+1;
		$("#numInvitadosDisabled").val(invitados);
		$("#numInvitados").val(invitados);
		frm.rowCount2.value=parseInt(frm.rowCount2.value)+parseInt(1);
		$("#nombre_invitado").val("");
		$("#puesto_invitado").val("");
		$("#empresa_invitado").val("");
		$("#tipo_invitado").val("-1");
		// Verificamos que el monto no exceda con el npumero de invitados que resten.
		evaluaMonto($("#monto_pesos").val().replace(/,/g,""));
	}
}
// FIN Agregar partida
// Guardar comprobacion
function guardaComprobacion(){
	var frm=document.invitacion_comp;
	if(parseInt(frm.no_Comprobaciones_parciales.value)>=1){
		if($("#delegado").val() != 0){
        	$("#enviaDirector").removeAttr("disabled");
        }else{
        	$("#guardarCompedit").removeAttr("disabled");
        }		
	}else{
		if($("#delegado").val() != 0){
			$("#enviaDirector").attr("disabled", "disabled");
        }else{
        	$("#guardarCompedit").attr("disabled", "disabled");
        }		
	}
}
// FIN Guardar comprobacion
// Verificar tipo de invitado
function verificar_tipo_invitado(){
	if($("#tipo_invitado").val()=="BMW"){
		$("#empresa_invitado").val("BMW DE MEXICO SA DE CV.");
		$("#empresa_invitado").attr("disabled", "disable");
	}else{
		$("#empresa_invitado").val("");
		$("#empresa_invitado").removeAttr("disabled");

	}
}

//Borrar invitado
function eliminarInvitado(id){
	var no_partidas = parseInt($("#invitado_table>tbody>tr").length);
	// Quitamos el registro de Invitado
	borrarRenglon4(id, "invitado_table", "rowCount2", 0,"renglonI", "edit", "del", "");
	$("#numInvitadosDisabled").val(parseInt(no_partidas - 1));
	$("#numInvitados").val(parseInt(no_partidas - 1));
	$("#rowCount2").val(parseInt(no_partidas - 1));
	evaluaMonto($("#monto_pesos").val().replace(/,/g,""));
}// Borrar invitado
// FIN Borrar partida

function cambiaNombreBtn(obj){
	if(obj.value=='     Agregar Nuevo Proveedor'){
		limpiarCamposDeProveedor(1);
		$("#msg_div").removeAttr('style');
		$('#add_rem_prv').attr("style","background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;");
		obj.value='     Cerrar Panel de Nuevo Proveedor' ;
	}else{
		limpiarCamposDeProveedor(1);
		$("#msg_div").removeAttr('style');
		$('#add_rem_prv').attr("style","background:url(../../images/add.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;");
		obj.value='     Agregar Nuevo Proveedor' ;
	}
}

//Agrega Nuevo Proveedor al catálogo
function nuevoProveedor(nombreProveedor,rfcProveedor,dirFiscal){
	if(nombreProveedor == "" ){
		alert("Debe ingresar la Razón Social del proveedor.");
		$("#new_proveedor").focus();
		return false;
	}else if(rfcProveedor == ""){
		alert("Debe ingresar el RFC del proveedor.");
		$("#new_p_rfc").focus();
		return false;
	}else if(dirFiscal == ""){
		alert("Debe ingresar el Domicilio Fiscal del proveedor.");
		$("#new_p_addr").focus();
		return false;
	}else{
		if(!valida_formatoRFC(rfcProveedor)){
			$("#new_p_rfc").focus();
			return false;
		}else{
			$.ajax({
				url: 'services/catalogo_proveedores.php',
				type: "POST",
				data: "submit=&nameprov="+nombreProveedor+"&rfcprov="+rfcProveedor+"&dirf="+dirFiscal,
				success: function(datos){
					if (datos==""){
						$("#proveedor").val("");
						$("#rfc").val("");
						$('#new_proveedor').val("");
						$('#new_p_rfc').val("");
						$('#new_p_addr').val("");
						$("#proveedor").focus();
					}else{
						alert(datos);
						$("#new_proveedor").focus();
					}
				}
			});//fin ajax
			return false;
		}
	}//fin if rfc
}
//fin nuevoProveedor

function validaInvitados(){
	var numInv = parseInt($("#numInvitados").val());
	if(numInv < 2){
		alert("Favor de ingresar por lo menos dos invitados.");
		return false;
	}else{
		return true;
	}
}

//Verificar estructura de RFC
function valida_formatoRFC(campo){
	var resultado = campo.match(/[A-Z]{3,4}[0-9]{6}((([A-Z]|[a-z]|[0-9]){3}))/);
	var resultado2 = campo.match(/^[a-zA-Z]{4,4}[0-9]{6,6}[a-zA-Z0-9]*$/) ;
	
	if(resultado == null && resultado2 == null){
		alert("El RFC que intenta ingresar es incorrecto. Favor de verificarlo.");
		return false;
	}else{
		return true;
	}	
}

//VALIDA TODOS LOS CAMPOS Y PARAMETROS marca
function validarcampos(){
	var frm= document.invitacion_comp;

	// Desactivar las listas Tipo de Tarjeta y Lista de Cargos en el caso 
	// que la Comprobación sea Devuelta con Observaciones por Finanzas 
	if($("#etapaComprobacion").val() == 5){
		$("#select_tipo_tarjeta").removeAttr("disabled");
		$("#select_tarjeta_cargo").removeAttr("disabled");
	}
	
	var sol_inv=$("#solicitud_de_invitacion").val();
	var tipo_comp=$("#tipo option:selected").val();
	var cc_costos=$("#centro_de_costos").val();

	if(validaInvitados() && solicitarConfirmarGuardado()){
		if(parseInt(sol_inv)==-1){
			alert("Debe seleccionar una solicitud de invitacion para comprobar. Favor de llenar los datos faltantes.");
			return false;
		}else if(parseInt(tipo_comp)==-1){
			alert("Debe seleccionar un tipo de comprobación. Favor de llenar los datos faltantes.");
			return false;
		}else{
			if(tipo_comp=="amex"){
					var lugar_amex=$("#lugar_inv").val();
					var proveedor_amex=($("#fact_chk").is(':checked'))?true:false;
					var monto_amex=$("#monto").val();
					var monto_pesos_amex=($("#monto_pesos").val()).replace(/,/g,"");
					var moneda_amex=$("#moneda").val();

					var total_comp = ($("#total").val()).replace(/,/g,"");
					var monto_amex_comp = ($("#amex_dolar_val").val()).replace(/,/g,"");
					total_comp = parseFloat(total_comp);
					monto_amex_comp = parseFloat(monto_amex_comp);
					//var dat_fact= checafecha(frm.fecha.value, 60);
					var dat_fact= true;
					var total_amex=$("#total").val();
					var observaciones_amex=$("#banderavalida").val();
					//alert(parseFloat(frm.total.value)+"-"+parseFloat(frm.monto_cargo_val.value));
					var aux_fecha_cargo = frm.fecha_cargo_val.value.split(" ");
					aux_fecha_cargo = aux_fecha_cargo[0];
					aux_fecha_cargo = aux_fecha_cargo.split("-").reverse().join("/");
					if(frm.select_tipo_tarjeta.value==-1){ //tipo de tarjeta
						alert("Debe seleccionar un tipo de cargo para comprobar. Favor de llenar los datos faltantes.");
						return false;
					}else if(frm.select_tarjeta_cargo.value==-1 || frm.select_tarjeta_cargo.value==""){ //lista de cargos
						alert("Debe seleccionar un cargo para comprobar. Favor de llenar los datos faltantes.");
						return false;
					}else{
						if(lugar_amex==""){
							alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
							$("#lugar_inv").focus();
							return false;
						}else if(proveedor_amex){ // Verfificar parte de la edición
							if(monto_amex == "" || monto_amex == "0.00" || monto_amex == 0){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#monto").focus();
								return false;
							}else if(frm.impuesto.value==""){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								frm.impuesto.focus();
								return false;
							}else if (parseFloat((frm.impuesto.value).replace(/,/g,"")) < 0 || frm.impuesto.value == ""){
								alert("El IVA introducido es inválido. Favor de verificarlo.");
								frm.impuesto.focus();
								return false;
							}else if($("#d_folio").val()==""){
								alert("Debe ingresar un folio de factura. Favor de llenar los datos faltantes.");
								$("#d_folio").focus();
								return false;
							}else if((frm.rfc.value.length < 12) || (frm.rfc.value.length > 13)){
								alert("El RFC que intenta ingresar es incorrecto. Favor de verificarlo.");
								frm.rfc.focus();
								return false;
							}else if(frm.proveedor.value==""){
								alert("Los datos del proveedor estan incompletos. Favor de llenar los datos faltantes.");
								frm.rfc.focus();
								return false;
							}else if(!valida_formatoRFC($("#rfc").val()) || (frm.rfc_cargo_val.value.toUpperCase() != frm.rfc.value && ($("#rfc_cargo_val").val()).length > 1)){
								alert("El RFC ingresado difiere del RFC registrado al cargo.");
								frm.rfc.focus();
								return false;
							}else if(($("#moneda option:selected").text()) != ($("#moneda_fact_val").val())){
								alert("La moneda de facturación difiere de la divisa ingresada.");
								return false;
							}else if(total_comp != monto_amex_comp){
								alert("El monto ingresado difiere del Total Amex.");
								return false;
							}else if(observaciones_amex == 1 && $("#observ").val() == "" ){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#observ").focus();
								return false;
							}else if(!validaProveedor($("#rfc").val(),$("#proveedor").val())){
								return false;
							}
						}else if(!proveedor_amex){
							if(monto_amex == "" || monto_amex == "0.00" || monto_amex == 0){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#monto").focus();
								return false;
							}else if(parseFloat(monto_amex)<=0){
								alert("El monto introducido no es válido. Favor de verificarlo.");
								$("#monto").focus();
								return false;
							}else if(monto_pesos_amex==""){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#monto").focus();
								return false;
							}else if(parseFloat(monto_pesos_amex)<=0){
								alert("El monto en pesos introducido no es válido. Favor de verificarlo.");
								$("#monto").focus();
								return false;
							}else if(moneda_amex==-1){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#moneda").focus();
								return false;
							}else if(moneda_amex == 1){
								if (parseFloat((frm.impuesto.value).replace(/,/g,"")) < 0 || frm.impuesto.value == ""){
									alert("El IVA introducido es inválido. Favor de verificarlo.");
									frm.impuesto.focus();
									return false;
								}else if(($("#moneda option:selected").text()) != ($("#moneda_fact_val").val())){
									alert("La moneda de facturación difiere de la divisa ingresada.");
									return false;
								}else if(total_comp != monto_amex_comp){
									alert("El monto ingresado difiere del Total Amex.");
									return false;
								}else if(observaciones_amex==1 && $("#observ").val().length == 0){
									alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
									$("#observ").focus();
									return false;
								}
							}else if(dat_fact==false){
								alert("Tu comprobante excede el plazo permitido de 60 dias.");                      
								return false;
							}else if(total_amex==""){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#total").focus();
								return false;
							}else if(($("#moneda option:selected").text()) != ($("#moneda_fact_val").val())){
								alert("La moneda de facturación difiere de la divisa ingresada.");
								return false;
							}else if(total_comp != monto_amex_comp){
								alert("El monto ingresado difiere del Total Amex.");
								return false;
							}else if(observaciones_amex==1 && $("#observ").val().length == 0){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#observ").focus();
								return false;
							}else{
								return true;
							}
						}else{
							if(monto_amex == "" || monto_amex == "0.00" || monto_amex == 0){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#monto").focus();
								return false;
							}else if(parseFloat(monto_amex)<=0){
								alert("El monto introducido no es válido. Favor de verificarlo.");
								$("#monto").focus();
								return false;
							}else if(monto_pesos_amex==""){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#monto").focus();
								return false;
							}else if(parseFloat(monto_pesos_amex)<=0){
								alert("El monto en pesos introducido no es válido. Favor de verificarlo.");
								$("#monto").focus();
								return false;
							}else if(moneda_amex==-1){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#moneda").focus();
								return false;
							}else if(moneda_amex == 1){
								if (parseFloat((frm.impuesto.value).replace(/,/g,"")) < 0 || frm.impuesto.value == ""){
									alert("El IVA introducido es inválido. Favor de verificarlo.");
									frm.impuesto.focus();
									return false;
								}else if(($("#moneda option:selected").text()) != ($("#moneda_fact_val").val())){
									alert("La moneda de facturación difiere de la divisa ingresada.");
									return false;
								}else if(total_comp != monto_amex_comp){
									alert("El monto ingresado difiere del Total Amex.");
									return false;
								}else if(observaciones_amex==1 && $("#observ").val().length == 0){
									alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
									$("#observ").focus();
									return false;
								}
							}else if(dat_fact==false){
								alert("Tu comprobante excede el plazo permitido de 60 dias.");                      
								return false;
							}else if(total_amex==""){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#total").focus();
								return false;
							}else if(($("#moneda option:selected").text()) != ($("#moneda_fact_val").val())){
								alert("La moneda de facturación difiere de la divisa ingresada.");
								return false;
							}else if(total_comp != monto_amex_comp){
								alert("El monto ingresado difiere del Total Amex.");
								return false;
							}else if(observaciones_amex==1 && $("#observ").val().length == 0){
								alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
								$("#observ").focus();
								return false;
							}else{
								return true;
							}
						} 
					}
			}else if(tipo_comp == "reembolso_para_empleado"){
					var lugar_reembolso=$("#lugar_inv").val();
					var proveedor_reembolso=($("#fact_chk").is(':checked'))?true:false;
					var monto_amex=$("#monto").val();
					var moneda_amex=$("#moneda").val();
					//var dat_fact= checafecha(frm.fecha.value, 60);
					var dat_fact= true;
					var total_amex=$("#total").val();
					var observaciones_amex=$("#banderavalida").val();
					
					if(lugar_reembolso==""){
						alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
						$("#lugar_inv").focus();
						return false;
					}else if(proveedor_reembolso){
						if(monto_amex == "" || monto_amex == "0.00" || monto_amex == 0){
							alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
							$("#monto").focus();
							return false;
						}else if (frm.impuesto.value==""){
							alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
							frm.impuesto.focus();
							return false;
						}else if (parseFloat((frm.impuesto.value).replace(/,/g,"")) < 0  || frm.impuesto.value == ""){
							alert("El IVA introducido es inválido. Favor de verificarlo.");
							frm.impuesto.focus();
							return false;
						} else if($("#d_folio").val()==""){
							alert("Debe ingresar un folio de factura. Favor de llenar los datos faltantes.");
							$("#d_folio").focus();
							return false;
						}else if((frm.rfc.value.length<12)||(frm.rfc.value.length>13)){
							alert("El RFC que intenta ingresar es incorrecto. Favor de verificarlo e intente nuevamente.");
							frm.rfc.focus();
							return false;
						}else if(frm.proveedor.value==""){
							alert("Los datos del proveedor estan incompletos. Favor de llenar los datos faltantes.");
							frm.rfc.focus();
							return false;
						}else if(!validaProveedor($("#rfc").val(), $("#proveedor").val())){
							return false;
						}						
					}else if(monto_amex==""){
						alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
						$("#monto").focus();
						return false;
					}else if(parseFloat(monto_amex)<=0){
						alert("El monto introducido no es válido. Favor de verificarlo.");
						$("#monto").focus();
						return false;
					}else if(monto_pesos_amex==""){
						alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
						$("#monto").focus();
						return false;
					}else if(parseFloat(monto_pesos_amex)<=0){
						alert("El monto en pesos introducido no es válido. Favor de verificarlo.");
						$("#monto").focus();
						return false;
					}else if(moneda_amex==-1){
						alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
						$("#moneda").focus();
						return false;
					}else if(moneda_amex == 1){
						if(parseFloat((frm.impuesto.value).replace(/,/g,"")) < 0 || frm.impuesto.value == ""){
							alert("El IVA introducido es inválido. Favor de verificarlo.");
							frm.impuesto.focus();
							return false;
						}else if(observaciones_amex == 1 && $("#observ").val() == "" ){
							alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
							$("#observ").focus();
							return false;
						}
					}else if(dat_fact==false){
						alert("Tu comprobante excede el plazo permitido de 60 dias.");                      
						return false;
					}else if(total_amex==""){
						alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
						$("#total").focus();
						return false;
					}else if(parseFloat(total_amex)<=0){
						alert("El Total introducido no es válido. Favor de verificarlo.");
						$("#total").focus();
						return false;
					}else if(observaciones_amex==1 && $("#observ").val() == "" ){
						alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
						$("#observ").focus();
						return false;
					}else{
						return true;
					}
			}
		}
	}else{
		return false;
	}
}
// FIN de Validar campos

function cambiaMotivo(){
	var frm=document.invitacion_comp;

	if(frm.solicitud_de_invitacion.value!="-1" && frm.solicitud_de_invitacion.value!="n"){                                                                                                                                        	
		var tramite=frm.solicitud_de_invitacion.options[frm.solicitud_de_invitacion.selectedIndex].value;			
		frm.motive.value=$("#solicitud_de_invitacion option:selected").text();
		//frm.typedoc.value="1|"+tramite;			
		//getSaldoAnticipo(tramite);		
	}else{
		frm.motive.value="";		
		//frm.typedoc.value="";			
		//$("#g_saldo").html("");
	}
}

function cargar_solicitud(valor){
	guardaComprobacionprev();
	clean_comprobacion();
	cambiaMotivo();
	seleccionar_centro_de_costos(valor);
	cargarInvitados(valor, "cargaSeleccionLista");
	cargarCiudad(valor);
	cargarLugar(valor);
	cargarFecha(valor);
	//cargarMontoPesos(valor);
	cambiaTitulo();
}

function cambiar_divisa(valor){
	$("#divisa_sol").val(valor);
}

function validaZeroIzquierda(monto,campo){			
	if( monto.substring(monto.length-1,monto.length) === "." || monto.substring(monto.length-2,monto.length) === ".0" ){
		$("#"+campo).val(monto);
	}else if(monto == 0 || monto == "" || monto == "NaN"){
		$("#"+campo).val(0);
	}else{
		$("#"+campo).val(parseFloat(monto));
	}
}
</script>
<link rel="stylesheet" type="text/css" href="../../css/jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="../../css/date_input.css" />
<link rel="stylesheet" type="text/css" href="../../css/table_style.css" />
<style type="text/css">
.style1 {
	color: #FF0000
}

.fader {
	opacity: 0;
	display: none;
}

.trans {
	background-color: #D7D7D7;
	color: #0000FF;
	position: absolute;
	vertical-align: middle;
	width: 690px;
	height: 200px;
	padding: 65px;
	font-size: 15px;
	font-weight: bold;
	top: 26%;
	left: 18%;
}

.boton {
	background: #666666;
	color: #FFFFFF;
	border-color: #CCCCCC;
}
</style>

<form action="comprobacion_invitacion.php?save" method="post" name="invitacion_comp" id="invitacion_comp">
	<table width="785" border="0" align="center" cellspacing="1" style="border: 1px #CCCCCC solid; margin: auto; margin-top: 5px; text-align: left;" onmouseover="cambiaTitulo();" onblur="cambiaTitulo();" onmouseout="cambiaTitulo();">
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<?
			$cnn = new conexion();
			if(isset($_SESSION['iddelegado'])){ 
				$iduser = $_SESSION['iddelegado']; 
			}else{
				$iduser = $_SESSION["idusuario"];
			}
			//$idusuario=$_SESSION["idusuario"];
			$query = sprintf("SELECT DISTINCT si.si_id AS id, si.si_tramite AS t, t.t_etiqueta AS e
					FROM solicitud_invitacion AS si
					INNER JOIN tramites AS t ON si.si_tramite = t.t_id
					INNER JOIN usuario AS u ON t.t_iniciador = '%s' 
					LEFT JOIN comprobacion_invitacion ON co_tramite = si_tramite
					WHERE t.t_comprobado = '0' AND t.t_etapa_actual = '3' 
					AND co_mi_tramite = '%s'", $iduser, $_GET["edit2"]);
			/*$query = sprintf("select distinct si.si_id as 'id',
					si.si_tramite as 't',
					t.t_etiqueta as 'e'
					from solicitud_invitacion as si
					inner join tramites as t
					on si.si_tramite = t.t_id
					inner join usuario as u
					on t.t_iniciador = $idusuario
					where t.t_comprobado = 0
					and t.t_etapa_actual = 3 or t_etapa_actual=4");*/
					//error_log($query);
			$rst = $cnn->consultar($query);
			$fila = mysql_num_rows($rst);
			?>
			<td colspan="3">Solicitud de invitaci&oacute;n<span class="style1">*</span>:
				<select name="solicitud_de_invitacion" id="solicitud_de_invitacion" onchange="cargar_solicitud(this.value);">
					<option id="-1" value="-1">Seleccione...</option>
					<?php
						if($fila>0){
							while($fila=mysql_fetch_assoc($rst)){
								echo "<option id=".$fila["id"]." value=".$fila["t"].">".$fila["t"]." - ".$fila["e"]."</option>";
							}
						}else{
							echo "<option id='-1' value='-1'>No hay Solicitudes Pendientes</option>";
						}
					?>
				</select>
				<input type="hidden" id="dato001" name="dato001" size="5" readonly="readonly" value="0" />
			</td>
			<td colspan="3">Tipo de comprobaci&oacute;n<span class="style1">*</span>:
				<select name="tipo" id="tipo" onChange="tipo_de_comprobacion(value);"
				disabled="disabled">
					<option id="-1" value="-1">Seleccione...</option>
					<?php if($tipoUsuario != 3){?>
						<option name="amex" id="amex" value="amex">Amex</option>
					<?php }?>
					<option name="reembolso_para_empleado" id="reembolso_para_empleado" value="reembolso_para_empleado">Reembolso</option>
			</select>
			<input type="hidden" id="dato002" name="dato002" size="5" readonly="readonly" value="" />
			</td>
			<?
			$cnn = new conexion();
			$iduser=$_SESSION["idusuario"];
			$query = sprintf("SELECT CC.CC_ID AS 'ID',
				CC.CC_CENTROCOSTOS AS 'CC',
				CC.CC_NOMBRE AS 'NOMBRE'
				FROM cat_cecos AS CC
				WHERE cc_estatus = '1' AND cc_empresa_id = '".$_SESSION["empresa"]."' 
				ORDER BY CC.CC_CENTROCOSTOS");
			$rst = $cnn->consultar($query);
			$fila = mysql_num_rows($rst);
			?>
			<td colspan="3">Centro de costos<span class="style1">*</span>: <select
				name="centro_de_costos" id="centro_de_costos" onchange="" disabled="disabled">
					<option id="-1" value="-1">Seleccione...</option>
					<?php
						if($fila>0){
							while($fila=mysql_fetch_assoc($rst)){
								echo "<option id=".$fila["ID"]." value=".$fila["CC"].">".$fila["CC"]."-".$fila["NOMBRE"]."</option>";
							}
						}else{
							echo "<option id='n' value='n'>No hay centro de costos</option>";
						}
					?>
				</select>
			</td>
			<td><input type="hidden" id="devuelto" name="devuelto" readonly="readonly" value="0" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<br />
	<!-- Seccion Amex -->
	<!--<div id="seccion_amex" align="right" style="display: none;" onmouseover="obtener_tasa_USD();" onblur="obtener_tasa_USD();" onmouseout="obtener_tasa_USD();">-->
	<div id="seccion_amex" align="right" style="display: none;" >
		<table width="785" border="0" align="center" cellspacing="1" style="border: 1px #CCCCCC solid; margin: auto; margin-top: 5px; text-align: left;">
			<tr>
				<td colspan="3"><h3 align="center">Amex</h3></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="3">Tipo de cargo: <select name="select_tipo_tarjeta" id="select_tipo_tarjeta" onchange="verificar_tipo_tarjeta();">
						<option id="-1" value="-1">Seleccione...</option>
						<option id="1" value="1">Amex corporativa gastos</option>
						<!-- option id="2" value="2">Amex corporativa Gasolina</option -->
				</select>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="3">Tarjeta de Cr&eacute;dito:&nbsp;
					<div name="no_tarjeta_credito" id="no_tarjeta_credito" style="width: 70%"></div>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<!--<td colspan="3">Lista de Cargos: <select name="select_tarjeta_cargo" id="select_tarjeta_cargo" onchange="cargar_detalles();obtener_tasa_USD();" onmouseover="obtener_tasa_USD();" onblur="obtener_tasa_USD();" onmouseout="obtener_tasa_USD();">-->
				<td colspan="3">Lista de Cargos: <select name="select_tarjeta_cargo" id="select_tarjeta_cargo" onchange="cargar_detalles();">
				</select>
				</td>
			</tr>
			<tr>
				<td colspan="3"><h3 align="center">Detalle del cargo</h3></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td width="50%">Establecimiento: <input type="hidden" id="establecimiento_cargo_val" name="establecimiento_cargo_val" value="0" readonly="readonly" />
					<div id="establecimiento_cargo" name="establecimiento_cargo" align="left"></div>
				</td>
				<td width="50%">Total Factura: <input type="hidden" id="monto_cargo_val" name="monto_cargo_val" value="0" readonly="readonly" />
					<div id="monto_cargo" name="monto_cargo" align="left"></div>
					<input type="hidden" id="moneda_fact_val" name="moneda_fact_val" readonly="readonly" />
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td width="50%">Fecha: <input type="hidden" id="fecha_cargo_val" name="fecha_cargo_val" value="0" readonly="readonly" />
					<div id="fecha_cargo" name="fecha_cargo"></div>
				</td>
				<td width="50%">Total Amex: <input type="hidden" id="amex_dolar_val" name="amex_dolar_val" value="0" readonly="readonly" /> 
					<div id="amex_dolar" name="amex_dolar" align="left"></div>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td width="50%">RFC: <input type="hidden" id="rfc_cargo_val" name="rfc_cargo_val" value="0" readonly="readonly" /><div id="rfc_cargo" name="rfc_cargo" align="left"></div></td>
				<td width="50%">Total MXN: <input type="hidden" id="amex_pesos_val" name="amex_pesos_val" value="0" readonly="readonly" /><div id="amex_pesos" name="amex_pesos" align="left"></div></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td colspan="4" width="50%">Tipo de cambio: <input type="hidden" id="tipo_cambio" name="tipo_cambio" value="0.00" readonly="readonly" /><div id="div_tipo_cambio" name="div_tipo_cambio" align="left"></div></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td width="50%">&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<br />
	</div>
	<!-- FIN Seccion Amex -->

	<!-- Conceptos a comprobar -->
	<table width="785" border="0" align="center" cellspacing="1" style="border: 1px #CCCCCC solid; margin: auto; margin-top: 5px; text-align: left;" onmouseover="cambiaTitulo();" onblur="cambiaTitulo();" onmouseout="cambiaTitulo();">
		<tr>
			<td colspan="2"></td>
		</tr>
		<tr>
			<td width="2">&nbsp;</td>
			<td colspan="12"><h3 align="center">Conceptos a comprobar</h3></td>
			<td width="2">&nbsp;</td>
		</tr>
		<tr>
			<td width="2">&nbsp;</td>
			<td ><div id="ciudad_label">Ciudad: </div></td>
			<td colspan="3" align="left">
				<div id="ciudad_data" name="ciudad_data"></div>
				<input type="hidden" id="co_ciudad_data" name="co_ciudad_data" value="" readonly="readonly"/>
			</td>
			<td >&nbsp;</td>
			<td align="left" colspan="2">
				<table>
					<tr>
						<td align="left"><div id="fecha_inv">Fecha de invitaci&oacute;n: </div></td>
						<td align="left"><div id="invitacion_dato" name="invitacion_dato" align="left"></div><input type="hidden" name="fechainvitacion" id="fechainvitacion"/></td>
					</tr>
				</table>
			</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="1" width="2">&nbsp;</td>
			<td colspan="12">
			<table>
				<tr>
					<td>Lugar invitaci&oacute;n/Restaurante<span class="style1">*</span>:&nbsp;<input type="text" name="lugar_inv" id="lugar_inv" /></td>
				</tr>
			</table>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="2">Proveedor Nacional: <input type="checkbox" name="fact_chk" id="fact_chk" onclick="verDatosProveedor();" /></td>
			<td colspan="2"><a href="http://www.oanda.com/lang/es/currency/converter/" target="_black">Realiza la conversi&oacute;n a tu divisa</a></td>
			<td>&nbsp;</td>
			<td colspan="7">
				<table>
					<tr>
						<td><div align="left">Fecha Comprobante<span class="style1">*</span>:&nbsp;</div></td>
						<td><div align="left"><input name="fecha" id="fecha" value="<?php echo date('d/m/Y'); ?>" size="10" onfocus="return checafecha(document.invitacion_comp.fecha.value, 60);" /></div></td>
					</tr>
				</table>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="2" height="26">&nbsp;</td>
			<td width="70"><div align="left">Subtotal/Monto<span class="style1">*</span>: </div></td>
			<!--<td width="83"><div align="left"><input name="monto" id="monto" value="0" size="10" onkeyup="getTotal();recalculaMontos(); this.value = format_input(this.value);" onchange="getTotal();recalculaMontos();" onkeypress="return validaNum(event)" size="10" autocomplete="off" /></div></td>-->
			<!--<td width="83"><div align="left"><input name="monto" id="monto" value="0" size="10" onkeyup="getTotal();recalculaMontos();" onchange="getTotal();recalculaMontos();" onkeydown="" onkeypress="return NumCheck(event, this);" size="10" autocomplete="off" /></div></td>-->
			<td width="83"><div align="left"><input name="monto" id="monto" value="0" size="10" onkeyup="revisaCadena(this); getTotal();recalculaMontos(); validaZeroIzquierda(this.value,this.id);" onchange="getTotal(); recalculaMontos(); validaZeroIzquierda(this.value,this.id);" onkeydown="validaZeroIzquierda(this.value,this.id);" onkeypress="validaZeroIzquierda(this.value,this.id);" size="10" autocomplete="off" /></div></td>
			<td width="55" colspan="2">
				<table>
				<tr>
					<td><div align="left">Divisa<span class="style1">*</span>:</div></td>
					<td><div align="left">
					<select name="moneda" id="moneda" onchange="cambiaTasa(this.value);recalculaMontos();cambiar_divisa(this.value);activaIva();">
						<option value="-1">Seleccione...</option>
						<option value="1">MXN</option>
						<option value="2">USD</option>
						<option value="3">EUR</option>
					</select>
					</div></td>
				</tr>
				</table>
				<input type="hidden" name="tasa" id="tasa" value="1" />
			</td>
			<td width="4">&nbsp;</td>
			<td width="150">&nbsp;</td>
			<td width="4">&nbsp;</td>
			<td colspan="5" rowspan="8" style="vertical-align: top">&nbsp;</td>
			<td width="4">&nbsp;</td>
		</tr>
		<tr>
			<td width="2">&nbsp;</td>
			<td width="70"><div align="left" id="iva_label" style ="display:none;">IVA<span class="style1">*</span>: </div></td>
			<!--<td width="83"><div align="left"><input name="impuesto" id="impuesto" value="0" size="10" onkeyup="getTotal();recalculaMontos(); this.value = format_input(this.value);" onchange="getTotal();recalculaMontos();" onkeypress="return validaNum(event);" autocomplete="off" style="display:none;"/></div></td>			<td width="55" colspan="2"><input type="hidden" name="bandera_iva" id="bandera_iva" size="15" /></td>-->
			<!--<td width="83"><div align="left"><input name="impuesto" id="impuesto" value="0" size="10" onkeyup="getTotal();recalculaMontos();" onchange="getTotal();recalculaMontos();" onkeydown="" onkeypress="return NumCheck(event, this);" autocomplete="off" style="display:none;"/></div></td>			<td width="55" colspan="2"><input type="hidden" name="bandera_iva" id="bandera_iva" size="15" /></td>-->
			<td width="83"><div align="left"><input name="impuesto" id="impuesto" size="10" onkeyup="revisaCadena(this); getTotal(); recalculaMontos(); validaZeroIzquierda(this.value,this.id);" onchange="getTotal(); recalculaMontos(); validaZeroIzquierda(this.value,this.id);" onkeydown="validaZeroIzquierda(this.value,this.id);" onkeypress="validaZeroIzquierda(this.value,this.id);" autocomplete="off" style="display:none;"/></div></td>
			<td width="55" colspan="2"><input type="hidden" name="bandera_iva" id="bandera_iva" size="15" /></td>
			<td width="4">&nbsp;</td>
			<td colspan="1" rowspan="3">
				<div align="left" id="datosProveedor" style="display: none;">
					<div align="left" id="div_folio">
						Folio Factura<span class="style1">*</span>:
					</div>
					<div align="left">
						<input name="d_folio" id="d_folio" size="15" onkeypress="return validaNum(event)" />
					</div>
					<div id="rfc_prov_busq_div" align="left">
						RFC<span class="style1">*</span>:
					</div>
					<div align="left">
						<input name="rfc" type="text" id="rfc" value="" size="30" maxlength="13" onkeyup="this.value = this.value.toUpperCase();"/>
					</div>
					<div align="left">
						Raz&oacute;n Social<span class="style1">*</span>:
					</div>
					<div align="left">
						<input name="proveedor" type="text" id="proveedor" value="" size="30" disabled/>
					</div>
					<br />
					<input type="button" class="fadeNext" style='background: url(../../images/add.png); background-position: left; background-repeat: no-repeat; background-color: #E1E4EC;' name="add_rem_prv" id="add_rem_prv" onclick="cambiaNombreBtn(this);" value="     Agregar Nuevo Proveedor" /><div class="fader" align="right">
					<table width="295" border="0" align="center" cellspacing="1" style="border: 1px #CCCCCC solid; margin: auto; margin-top: 5px; padding-left: 5px; text-align: left;">
							<tr>
								<td><div align="left">
										<h3>Agregar nuevo proveedor</h3>
									</div></td>
							</tr>
							<tr>
								<td><div align="left">
										<em><strong>Raz&oacute;n Social<span class="style1">*</span>: </strong></em>
									</div>
									<div align="left">
										<input name="new_proveedor" type="text" id="new_proveedor" value="" size="45" />
									</div></td>
							</tr>
							<tr>
								<td><div align="left">
										<em><strong>RFC<span class="style1">*</span>: </strong></em>
									</div>
									<div align="left">
										<input name="new_p_rfc" type="text" id="new_p_rfc" value="" size="30" maxlength="13" onkeyup="this.value = this.value.toUpperCase();"  onblur="valida_formatoRFC(this.value);" />
									</div></td>
							</tr>
							<tr>
								<td><div align="left">
										<em><strong>Domicilio Fiscal<span class="style1">*</span>:</strong></em>
									</div> <input name="new_p_addr" id="new_p_addr" value="" size="45" /></td>
							</tr>
							<tr>
								<td>
									<div align="left">
										<input type="button" name="agregar" value="    Agregar" onclick="nuevoProveedor(new_proveedor.value,new_p_rfc.value,new_p_addr.value);" style='background: url(../../images/add.png); background-position: left; background-repeat: no-repeat; background-color: #E1E4EC;' />
									</div>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</td>
			<td colspan="1">&nbsp;</td>
			<td >&nbsp;</td>
		</tr>
		<tr>
			<td width="2">&nbsp;</td>
			<td width="70"><div name="propina" id="propina" align="left">Propina<span class="style1"></span>: </div></td>
			<!--<td width="83"><div align="left"><input name="propina_dato" value="0" id="propina_dato" size="10" onkeyup="getTotal();recalculaMontos(); this.value = format_input(this.value);" onchange="getTotal();recalculaMontos();" onkeypress="return validaNum(event);" /></div></td>-->
			<!--<td width="83"><div align="left"><input name="propina_dato" value="0" id="propina_dato" size="10" onkeyup="getTotal();recalculaMontos();" onchange="getTotal();recalculaMontos();" onkeydown="" onkeypress="return NumCheck(event, this);" /></div></td>-->
			<td width="83"><div align="left"><input name="propina_dato" value="0" id="propina_dato" size="10" onkeyup="revisaCadena(this); getTotal(); recalculaMontos(); validaZeroIzquierda(this.value,this.id);" onchange="getTotal(); recalculaMontos(); validaZeroIzquierda(this.value,this.id);" onkeydown="validaZeroIzquierda(this.value,this.id);" onkeypress="validaZeroIzquierda(this.value,this.id);" /></div></td>
			<td width="55">&nbsp;</td>
			<td width="83" colspan="1">&nbsp;</td>
			<td width="4">&nbsp;</td>
			<td colspan="1">&nbsp;</td>
			<td colspan="1">&nbsp;</td>
		</tr>
		<tr>
			<td width="2">&nbsp;</td>
			<td width="70"><div align="left">Total<span class="style1">*</span>: </div></td>
			<td ><div align="left"><input type="text" name="totalDisabled" id="totalDisabled" value="0.00" size="10" disabled="disabled" />
				<input type="hidden" name="total" id="total" value="0.00" size="10" readonly="readonly" /></div></td>
			<td colspan="3" align="center">Monto Total en pesos: &nbsp;<input type="text" name="monto_pesosDisabled" id="monto_pesosDisabled" value="0.00" size="12" disabled="disabled"/>
			<input type="hidden" name="monto_pesos" id="monto_pesos" value="0.00" size="12" readonly="readonly"/>&nbsp;MXN</td>
			<td >&nbsp;</td>
			<td >&nbsp;</td>
		</tr>
		<tr>
			<td width="2">&nbsp;</td>
			<td colspan="6"><div id="capaWarning"></div><input type="hidden" name="banderavalida" id="banderavalida" readonly="readonly" /></td>
			<td width="2">&nbsp;</td>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr>
			<td width="2">&nbsp;</td>
			<td colspan="2" valign="top"><div id="comment" align="right">Comentarios: </div></td>
			<td colspan="10"><textarea maxlength="36" name="comentarios" id="comentarios" cols="80" rows="5"></textarea></td>
			<td >&nbsp;</td>
		</tr>
		<tr>
			<td width="2">&nbsp;</td>
			<td colspan="6">&nbsp;</td>
			<td width="2">&nbsp;</td>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr>
			<td width="2">&nbsp;</td>
			<td colspan="6">
				<h3>Personas que asistieron:</h3>
				<table id="invitado_table" class="tablesorter" cellspacing="1">
					<thead>
						<tr>
							<th width="5%">No.</th>
							<th width="35%">Nombre</th>
							<th width="25%">Puesto</th>
							<th width="20%">Empresa</th>
							<th width="15%">Tipo de Invitado</th>
							<th width="5%">Eliminar</th>
						</tr>
					</thead>
					<tbody>
						<!-- cuerpo tabla-->
					</tbody>
				</table>
			</td>
			<td colspan="5" rowspan="2">
			<div name="agregarInvitado" id="agregarInvitado" align="left" style="display: none;">
					Agregar un Invitado<br /> 
					Nombre:<span class="style1">*</span><br> <input name="nombre_invitado" type="text" id="nombre_invitado" size="50" maxlength="100" /><br> Puesto:&nbsp;<span class="style1">*</span><br>
					<input name="puesto_invitado" type="text" id="puesto_invitado" size="50" maxlength="100" /><br> 
					Tipo de Invitado:&nbsp;<span class="style1">*</span><br> 
					<select name="tipo_invitado" id="tipo_invitado" onchange="verificar_tipo_invitado();">
						<option value="-1">Seleccione...</option>
						<option value="BMW">Empleado BMW de M&eacute;xico</option>
						<option value="Externo">Externo</option>
						<option value="Gobierno">Gobierno</option>
					</select><br> 
					Empresa:&nbsp;<span class="style1">*</span><br> 
					<input name="empresa_invitado" type="text" id="empresa_invitado" size="50" maxlength="100" disabled="disable" />
				</div></td>
			<td colspan="1" rowspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="4"><div id="asistentes" align="left">Total de Asistentes:&nbsp; <input type="text" name="numInvitadosDisabled" id="numInvitadosDisabled" value="0" size="2" disabled="disabled" />
			<input type="hidden" name="numInvitados" id="numInvitados" value="0" size="2" readonly="readonly" /><br></div></td>
			<td colspan="2" align="center" valign="middle"><input type="button" id="agregarInv" name="agregarInv" value="Agregar Invitado" onclick="agregarNuevoInvitado();" /></td>
		</tr>
		<tr>
			<td width="2">&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>
			</td>
			<td colspan="6"><div align="center" class="style1" id="load_div"></div>
			</td>
			<td width="2">&nbsp;</td>
			<td >&nbsp;</td>
		</tr>
	</table>
	<!-- FIN Conceptos a comprobar -->

	<div id="msg_div" align="center"></div>
	<br />
	
	<!--FIN DE: function forma_comprobacion() -->
	<?php
            }
            ?> 

            <center><h3>Comprobaci&oacute;n de solicitud de invitaci&oacute;n</h3></center>
            <?php
            forma_comprobacion();
            ?>
            <div align="right" style="display: none;">
    <!--                <div id="g_saldo">&nbsp;</div><input type="hidden" readonly="readonly" name="t_saldo" id="t_saldo" value="0.00" /><br />-->
    <!--                <div id="g_sbt">Total: 0.00</div><input type="hidden" readonly="readonly" name="t_subtotal" id="t_subtotal" value="0.00" /><br />-->
    <!--                <div id="g_iva">IVA: 0.00</div><input type="hidden" readonly="readonly" name="t_iva" id="t_iva" value="0.00" /><br />-->
    <!--                <div id="g_tot">Saldo a reembolsar: 0.00</div><input type="hidden" readonly="readonly" name="t_total" id="t_total" value="0.00" />-->
            </div>
            <div id="ficha_deposito" style="display: none;">
                <table width="550" align="center" border="0" cellspacing="1" style="border: 1px #CCCCCC solid; margin: auto; margin-top: 5px; text-align: left;">
                    <tr>
                        <td colspan="4" align="center" style="color: #DF0101">
                            <b>El monto a comprobar es menor al anticipo, la comprobaci&oacute;n no se puede guardar.</b><br>
                        </td>
                    </tr>
                </table>
            </div>
            <div align="center">
                <table border="0" width='785'>
                <?php 
				$tramite = $_GET['edit2'];
				$FTramite =  new Tramite();
				$FTramite->Load_Tramite($tramite);
				$tEtapa = $FTramite->Get_dato("t_etapa_actual");
				$t_historial_autorizaciones = $FTramite->Get_dato("t_autorizaciones_historial");
//				error_log("Tramite: ".$tramite);
//				error_log("Etapa del tramite: ".$tEtapa);
				if($tEtapa == COMPROBACION_INVITACION_ETAPA_RECHAZADA || $tEtapa == COMPROBACION_INVITACION_ETAPA_RECHAZADA_POR_DIRECTOR || $t_historial_autorizaciones != ""  || $tEtapa == COMPROBACION_INVITACION_ETAPA_DEVUELTA_CON_OBSERVACIONES){?>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td align="right" valign="top">Historial de Observaciones:</td>
						<td colspan="2" rowspan="1" class="alignLeft" >
						<textarea name="historial_observaciones" id="historial_observaciones" cols="60" rows="5" readonly="readonly" onkeypress="confirmaRegreso('historial_observaciones');" onkeydown="confirmaRegreso('historial_observaciones');" ></textarea>
						</td>
						<td>&nbsp;</td>
					</tr>
				<?php }?>
                        <tr>
                            <td><input type="hidden" name="etapa" id="etapa" value="<?php echo $tEtapa;?>" /></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    <tr>
                        <td width="20%"rowspan="4" align='right' valign="top" width="5"><div id="obsjus">Observaciones:</div></td>
                        <td width="40%" colspan="2" rowspan="4" class="alignLeft" valign="top">
                            <textarea name="observ" id="observ" cols="60" rows="5" ></textarea>
                        </td>
                        <td width="40%" align="right">
                            <table style="border: 1px #CCCCCC solid; margin: auto; margin-top: 5px; width:280 px">
                                <tr>
                                    <td colspan="2" style="color: #000000; text-align: center;"><h3>Resumen</h3></td>
                                </tr>
                                <tr>
                                    <td><div id="g_saldo"></div><input type="hidden" readonly="readonly" name="t_saldo" id="t_saldo" value="0.00" /></td>
                                </tr>
                                <tr>
                                    <td><div id="g_sbt"></div><input type="hidden" readonly="readonly" name="t_subtotal" id="t_subtotal" value="0.00" /></td>
                                </tr><tr>
                                    <td>Total Amex comprobado:</td>
                                    <td><div id="g_amex_comprobado" align="right">0.00 MXN</div><input type="hidden" readonly="readonly" name="t_amex_comprobado" id="t_amex_comprobado" value="0.00" /></td>
                                </tr>
                                <tr>
                                    <td style="color: #DF0101">Monto a reembolsar:</td>
                                    <td style="color: #DF0101" align="right"><div id="g_reembolso">0.00 MXN</div><input type="hidden" readonly="readonly" name="t_reembolso" id="t_reembolso" value="0.00" /></td>
                                </tr>
                                <tr>
                                    <td style="color: #DF0101"></td>
                                    <td style="color: #DF0101"><div id="g_nocomprobado"></div><input type="hidden" readonly="readonly" name="t_nocomprobado" id="t_nocomprobado" value="0.00" /></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Total comprobaci&oacute;n:</td>
                                    <td><div id="g_comprobado" align="right">0.00 MXN</div><input type="hidden" readonly="readonly" name="t_comprobado" id="t_comprobado" value="0.00" /></td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                </table>
            </div>

	<div align="center">
		<br />
		<div align="center">
			<input type="submit" id="guardarCompprevedit" name="guardarCompprevedit" value="     Guardar Previo"  onclick="return solicitarConfirmPrevio();" style="background: url('../../images/save.gif'); background-position: left; background-repeat: no-repeat; background-color: #E1E4EC;" readonly="readonly" disabled="disabled"/>
			<?php if(isset($_SESSION['iddelegado']) && $tEtapa != COMPROBACION_INVITACION_ETAPA_DEVUELTA_CON_OBSERVACIONES){ ?>
                <input type="submit" id="enviaDirector" name="enviaDirector" value="     Enviar a Director"  onclick="return validarcampos();" style='background: url("../../images/save.gif"); background-position: left; background-repeat: no-repeat; background-color: #E1E4EC;' readonly="readonly" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php }else{ ?>
				<input type="submit" id="guardarCompedit" name="guardarCompedit" value="     Enviar Comprobaci&oacute;n"  onclick="return validarcampos();" style='background: url("../../images/save.gif"); background-position: left; background-repeat: no-repeat; background-color: #E1E4EC;' readonly="readonly" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<?php }?>
		</div>
		<?php
		$query_centrocostos = sprintf("SELECT idcentrocosto FROM empleado INNER JOIN cat_cecos ON empleado.idcentrocosto = cat_cecos.cc_id WHERE empleado.idempleado = %s", $_SESSION["idusuario"]);
		$rst_centrocostos = mysql_query($query_centrocostos);
		$fila_idcentro_costos1 = mysql_fetch_assoc($rst_centrocostos);
		$idcentrocosto_original = $fila_idcentro_costos1['idcentrocosto'];
		//Obtener Divisa Euro
		$_divisas = new Divisa();
		$_divisas->Load_data(3); //busca Id de divisa
		$divisaEUR = $_divisas->Get_dato("div_tasa");
		//Obtener Divisa Dólar
		$_divisas1 = new Divisa();
		$_divisas1->Load_data(2);
		$divisaUSD = $_divisas1->Get_dato("div_tasa");
		?>
		<input type="hidden" name="typedoc" id="typedoc" readonly="readonly"  value="" />
		<input type="hidden" name="tramite_id" id="tramite_id" readonly="readonly"  value="" />
		<input type="hidden" name="iu" id="iu" readonly="readonly" value='<? echo $_SESSION["idusuario"];?>' />
		<input type="hidden" name="ne" id="ne" readonly="readonly" value='<? echo $_SESSION["idusuario"];?>' />
		<input type="hidden" name="empresa" id="empresa" value='<?php echo $_SESSION["empresa"]; ?>' readonly="readonly" />
		<input type="hidden" name="guarda" id="guarda" value="" readonly="readonly" />
		<input type="hidden" id="rowCount" name="rowCount" value="0" readonly="readonly"/>
		<input type="hidden" id="rowCount2" name="rowCount2" value="0" readonly="readonly"/>
		<input type="hidden" id="rowDel" name="rowDel" value="0" readonly="readonly"/>
		<input type="hidden" name="datos_empleado2" id="datos_empleado2" value="" readonly="readonly" />
		<input type="hidden" id="sol_id" name="sol_id" value="0" readonly="readonly"/>
		<input type="hidden" id="divisa_sol" name="divisa_sol" value="0" readonly="readonly"/>
		<input type="hidden" id="rowCountCecos" name="rowCountCecos" value="0" readonly="readonly"/>
		<input type="hidden" id="Cecos" name="Cecos" value="0" readonly="readonly"/>
		<input type="hidden" id="Cecos_usuario" name="Cecos_usuario" value="<?php echo $idcentrocosto_original; ?>"readonly="readonly" />
		<input type="hidden" id="Cecos_refacturado" name="Cecos_refacturado" value="0" readonly="readonly"/>
		<input type="hidden" id="concepto_alim_hot" name="concepto_alim_hot" value="0" readonly="readonly"/>
		<input type="hidden" id="porcentA" name="porcentA" value="0" readonly="readonly"/>
		<input type="hidden" id="no_Comprobaciones_parciales" name="no_Comprobaciones_parciales" value="0" readonly="readonly"/>
		<input type="hidden" name="fact_chk1" id="fact_chk1" value="" readonly="readonly" />
		<input type="hidden" name="motive" id="motive" value="" readonly="readonly" />
		<input type="hidden" name="co_subtotal" id="co_subtotal" value="0.00" readonly="readonly" />
		<input type="hidden" name="co_iva" id="co_iva" value="0.00" readonly="readonly" />
		<input type="hidden" name="rfcComprobacion" id="rfcComprobacion" value="" readonly="readonly" />
		<!-- Divisa Euro -->
		<input type='hidden' id='valorDivisaEUR' name='valorDivisaEUR' value="<?php echo $divisaEUR; ?>">
		<!-- Divisa Dólar -->
		<input type='hidden' id='valorDivisaUSD' name='valorDivisaUSD' value="<?php echo $divisaUSD; ?>">
		<!-- aqui se carga el id del cargo amex seleccionado cuando edita un previo de tipo amex -->
		<input type='hidden' id='id_cargo_amex_seleccionado' name='id_cargo_amex_seleccionado' value="">
		<input type="hidden" name="delegado" id="delegado" readonly="readonly" value="<?php if(isset($_SESSION['iddelegado'])){ echo $_SESSION['iddelegado']; }else{echo 0;}?>" />
		<input type="hidden" name="tramiteID" id="tramiteID" readonly="readonly" value="<?php if(isset($_GET['edit2'])){ echo $_GET['edit2']; }else{ echo 0;}?>" />
		<input type="hidden" name="cargaEdicion" id="cargaEdicion" readonly="readonly"  value="1" />
	</div>

</form>
<!-- FIN DE: if (isset($_GET['comp_solicitud'])) -->

<?php
}
?>
