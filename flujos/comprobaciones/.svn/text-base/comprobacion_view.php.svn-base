<?php
require_once("$RUTA_A/lib/php/constantes.php");
require_once("../solicitudes/services/C_SV.php");
require_once("services/func_comprobacion.php");
require_once "$RUTA_A/functions/Notificacion.php";
require_once("$RUTA_A/functions/utils.php");
require_once("$RUTA_A/lib/php/mobile_device_detect.php");

$idusuario = $_SESSION["idusuario"];
$tipoUsuario = $_SESSION["perfil"];

$empleado		= $_SESSION["idusuario"];
$cnn			= new conexion();
$aux= array();
$ruta_nueva=0;
$t_sigAprobador="";
$mobile = false;

	//Se obtienen los ids de Controlling y de Finanzas
	$agrup_usu = new AgrupacionUsuarios();
	$agrup_usu->Load_Grupo_de_Usuario_By_Nombre("Controlling");
	$idControlling = $agrup_usu->Get_dato("au_id");
	$agrup_usu->Load_Grupo_de_Usuario_By_Nombre("Finanzas");
	$idFinanzas = $agrup_usu->Get_dato("au_id");
	//error_log($idControlling);
	//error_log($idFinanzas);

/********************************************************
*      		   T&E Vista Comprobaci�n  		            *
* Creado por:	  Jorge Usigli Huerta 16-Feb-2010		*
* Modificado por: Jorge Usigli Huerta 16-Feb-2010		*
* PHP, jQuery, JavaScript, CSS                          *
*********************************************************/
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
	function get_siguiente_autorizador2($ruta_autorizacion,$autorizaciones,$dueno){
		$ruta_aut_array = array();
		$aut_array      = array();
		$ruta_aut_array = explode("|",$ruta_autorizacion);
		$aut_array      = explode("|",$autorizaciones);
		$size_ruta_aut  = sizeof($ruta_aut_array);
		$size_aut       = sizeof($aut_array);
		//error_log("tama�o ruta aut       = ".$size_ruta_aut);
		//error_log("tama�o autorizaciones = ".$size_aut);
		if($aut_array[0] == "")
			$size_aut = 0;
		if(($size_aut+1)<$size_ruta_aut)
			return $ruta_aut_array[$size_aut+1];
		else
			return "";
	}

	// Detecta si es un dispositivo movil (IPhone, Android, Blackberry)
	$mobile_type = null;
	$mobile = mobile_device_detect(true,true,true,true,true,true,false,false,&$mobile_type);
	//$mobile = false;
	
// Autoriza la COMPROBACION DE INVITACION
if(isset($_POST['autorizar_comp_inv']) && isset($_POST['idT']) && $_POST['idT']!="" && isset($_POST['iu']) && $_POST['iu']!=""){
	$fin_de_ruta_autorizacion = false;
	$es_controlling_o_finanzas = 0;
	$cambio_cc = 0;
	$cambio_cpto = 0;
	$cambio_tot_aprob = 0;
	//Recibimos campo Observaciones y el ID del Tramite
	$HObser=$_POST['historial_observaciones'];
	$sObser = $_POST['observ_up'];
	$idTramite = $_POST['idT'];
	$delegado = $_POST['delegado'];
	$delegadoNombre = $_POST['delegadoNombre'];
	
	$rutaAuto=new RutaAutorizacion();
	$t_dueno=$rutaAuto->getDueno($idTramite);
	$tramite=new Tramite();
	$tramite->Load_Tramite($idTramite);
    $t_ruta_autorizacion = $tramite->Get_dato("t_ruta_autorizacion");
    $t_delegado = $tramite->Get_dato("t_delegado");
	//error_log("Due�o: ".$t_dueno);
	
	//Actualizamos el campos de observaciones
	if($sObser != ""){
		$notificacion = new Notificacion();
		$observaciones = $notificacion->anotaObservacion($t_dueno, $HObser, $sObser, FLUJO_COMPROBACION_INVITACION, COMPROBACION_INVITACION_ETAPA_APROBACION);
		//$observaciones = anotaObservacion($t_dueno,$HObser,$sObser);
		$query = sprintf("UPDATE comprobacion_invitacion SET co_observaciones = '%s' WHERE co_mi_tramite = '%s'", $observaciones, $idTramite);
		$cnn->ejecutar($query);
		//error_log($query);
	}
	
	//Si es controlling se guarda el nuevo centro de costos y las observaciones si es que fueron cambiado
	if($t_dueno == $idControlling){
		//error_log("Centro de Costos: ".$_POST['centro_de_costos_new']);
		if($_POST['centro_de_costos_new'] != $_POST['centro_de_costos_old']){
			$t_sigAprobador=$rutaAuto->AutorizarControlling($idTramite, $_POST['centro_de_costos_new'], 1);
			
			// Actualizar el CECO de la comprobaci�n
			$query = sprintf("UPDATE comprobacion_invitacion SET co_cc_clave = '%s' WHERE co_mi_tramite = '%s'", $_POST['centro_de_costos_new'], $idTramite);
			$cnn->ejecutar($query);
				
			$agrup_usu = new AgrupacionUsuarios();
			$agrup_usu->Load_Grupo_de_Usuario_By_ID($t_dueno);
			$controlling = $agrup_usu->Get_dato("au_nombre");
	
			$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>MODIFICADA</strong> por <strong>%05s</strong>.",$idTramite,$controlling);
			$remitente = $t_dueno;//por este momento es controlling o puede ser GA/DA
			$destinatario = $tramite->Get_dato("t_iniciador");
			// Notificaci�n para el iniciador/empleado
			$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "0", ""); //"0" para no enviar email y "1" para enviarlo
				
			$iniciador=new Usuario();
			$iniciador->Load_Usuario_By_ID($tramite->Get_dato("t_iniciador"));
			$nombreIniciador = $iniciador->Get_dato('nombre');
			$destinatario = $t_sigAprobador;
			
			if($t_delegado != 0){
				$iniciador->Load_Usuario_By_ID($t_delegado);
				$nombreDelegado = $iniciador->Get_dato('nombre');
				$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> creada por el usuario: <strong>%s</strong> en nombre de: <strong>%s</strong> ha sido <strong>AUTORIZADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.",$idTramite, $nombreDelegado, $nombreIniciador, $controlling);
			}else{
				$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> creada por el usuario: <strong>%s</strong> ha sido <strong>AUTORIZADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.",$idTramite, $nombreIniciador, $controlling);
			}
			// Notificaci�n para el autorizador
			$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "1", ""); //"0" para no enviar email y "1" para enviarlo
			$tramite->Modifica_Etapa($idTramite, COMPROBACION_INVITACION_ETAPA_APROBACION, FLUJO_COMPROBACION_INVITACION, $t_sigAprobador, "");
			// Asigno el valor que trae del campo de texto, debido a que en el index, la variable de sesi�n, se convertia en un entero(ID del usuario delegado).
			$_SESSION['delegado'] = $delegadoNombre;
			
			// Regresa a la pagina de solicitudes de invitaci�n
			if($mobile){
				echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?docs=docs&type=4&action=autorizar'>";
			}else{
				echo ("<script language='Javascript'> location.href='http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?docs=docs&type=4&action=autorizar';</script>");
			}	
		}else{
			error_log("No cambio");
			$t_sigAprobador=$rutaAuto->AutorizarControlling($idTramite,"", 0);
	
			$agrup_usu = new AgrupacionUsuarios();
			$agrup_usu->Load_Grupo_de_Usuario_By_ID($t_dueno);
			$controlling = $agrup_usu->Get_dato("au_nombre");
	
			$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>AUTORIZADA</strong> por <strong>%05s</strong>.",$idTramite,$controlling);
			$remitente = $t_dueno;//por este momento es controlling o puede ser GA/DA
			$destinatario = $tramite->Get_dato("t_iniciador");
			$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "0", ""); //"0" para no enviar email y "1" para enviarlo
	
			$iniciador=new Usuario();
			$iniciador->Load_Usuario_By_ID($tramite->Get_dato("t_iniciador"));
			$nombreIniciador = $iniciador->Get_dato('nombre');
			$destinatario = $t_sigAprobador;
			
			if($t_delegado != 0){
				$iniciador->Load_Usuario_By_ID($t_delegado);
				$nombreDelegado = $iniciador->Get_dato('nombre');
				$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> creada por el usuario: <strong>%s</strong> en nombre de: <strong>%s</strong> ha sido <strong>AUTORIZADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.",$idTramite, $nombreDelegado, $nombreIniciador, $controlling);
			}else{
				$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> creada por el usuario: <strong>%s</strong> ha sido <strong>AUTORIZADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.",$idTramite, $nombreIniciador, $controlling);
			}
			
			$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "0", ""); //"0" para no enviar email y "1" para enviarlo
			$tramite->Modifica_Etapa($idTramite, COMPROBACION_INVITACION_ETAPA_APROBACION, FLUJO_COMPROBACION_INVITACION, $t_sigAprobador, "");
			// Asigno el valor que trae del campo de texto, debido a que en el index, la variable de sesi�n, se convertia en un entero(ID del usuario delegado).
			$_SESSION['delegado'] = $delegadoNombre;			
			
			// Regresa a la pagina de solicitudes de invitaci�n
			if($mobile){
				echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?docs=docs&type=4&action=autorizar'>";
			}else{
				echo ("<script language='Javascript'> location.href='http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?docs=docs&type=4&action=autorizar';</script>");
			}
		}
	}else if($t_dueno==$idFinanzas){
		if($_POST['centro_de_costos_new'] != $_POST['centro_de_costos_old']){
			$tramite=new Tramite();
			$tramite->Load_Tramite($idTramite);
			$realizo=$tramite->Get_dato("t_iniciador");
			$fin_de_ruta=false;
			$t_sigAprobador=$rutaAuto->AutorizarFinanzas($idTramite, $_POST['centro_de_costos_new'], 1);
			if($t_sigAprobador==""){
				$fin_de_ruta=true;
				$t_sigAprobador=$tramite->Get_dato("t_iniciador");
			}
			
			// Actualizar el CECO de la comprobaci�n
			$query = sprintf("UPDATE comprobacion_invitacion SET co_cc_clave = '%s' WHERE co_mi_tramite = '%s'", $_POST['centro_de_costos_new'], $idTramite);
			$cnn->ejecutar($query);
	
			$agrup_usu = new AgrupacionUsuarios();
			$agrup_usu->Load_Grupo_de_Usuario_By_ID($t_dueno);
			$finanzas = $agrup_usu->Get_dato("au_nombre");
	
			$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>MODIFICADA</strong> por <strong>%s</strong>.",$idTramite,$finanzas);
			$remitente = $t_dueno;//por este momento es controlling o puede ser GA/DA
			$destinatario = $tramite->Get_dato("t_iniciador");
			// Notificaci�n para el iniciador/empleado
			$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "0", ""); //"0" para no enviar email y "1" para enviarlo
	
			if($fin_de_ruta==false){
				$iniciador=new Usuario();
				$iniciador->Load_Usuario_By_ID($tramite->Get_dato("t_iniciador"));
				$nombreIniciador = $iniciador->Get_dato('nombre');
				$destinatario = $t_sigAprobador;
				
				if($t_delegado != 0){
					$iniciador->Load_Usuario_By_ID($t_delegado);
					$nombreDelegado = $iniciador->Get_dato('nombre');
					$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> creada por el usuario: <strong>%s</strong> en nombre de: <strong>%s</strong> ha sido <strong>AUTORIZADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.",$idTramite, $nombreDelegado, $nombreIniciador, $finanzas);
				}else{
					$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> creada por el usuario: <strong>%s</strong> ha sido <strong>AUTORIZADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.",$idTramite, $nombreIniciador, $finanzas);
				}
				// Notificaci�n para el Autorizador
				$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "1", ""); //"0" para no enviar email y "1" para enviarlo
				$tramite->Modifica_Etapa($idTramite, COMPROBACION_INVITACION_ETAPA_APROBACION, FLUJO_COMPROBACION_INVITACION, $t_sigAprobador, "");
			}else{
				$iniciador=new Usuario();
				$iniciador->Load_Usuario_By_ID($tramite->Get_dato("t_iniciador"));
				$destinatario = $t_sigAprobador;
				$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>APROBADA</strong> por completo.",$idTramite);
				$tramite->Modifica_Etapa($idTramite, COMPROBACION_INVITACION_ETAPA_APROBADA, FLUJO_COMPROBACION_INVITACION, $t_sigAprobador, "");
				$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "1", "", 0); //"0" para no enviar email y "1" para enviarlo, el �ltimo argumento indicar� si se coloca el link de ingreso a la aplicaci�n (1) y (0) si no es requerido.
				$tramite->setCierreFecha($idTramite);
			}
			// Asigno el valor que trae del campo de texto, debido a que en el index, la variable de sesi�n, se convertia en un entero(ID del usuario delegado).
			$_SESSION['delegado'] = $delegadoNombre;
			
			// Regresa a la pagina de solicitudes de invitaci�n
			if($mobile){
				echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?docs=docs&type=4&action=autorizar'>";
			}else{
				echo ("<script language='Javascript'> location.href='http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?docs=docs&type=4&action=autorizar';</script>");
			}
			
		}else{
			$tramite=new Tramite();
			$tramite->Load_Tramite($idTramite);
			$realizo=$tramite->Get_dato("t_iniciador");
			//$etapa  = $tramite->Get_dato("t_etapa_actual");
			
			error_log("No cambio");
			$fin_de_ruta=false;
			$t_sigAprobador=$rutaAuto->AutorizarFinanzas($idTramite,"", 0);
			error_log("--------->>>>>>>>>>>>>>Aprobador: ".$t_sigAprobador);
			if($t_sigAprobador==""){
				$fin_de_ruta=true;
				$t_sigAprobador=$tramite->Get_dato("t_iniciador");
			}
				
			$agrup_usu = new AgrupacionUsuarios();
			$agrup_usu->Load_Grupo_de_Usuario_By_ID($t_dueno);
			$finanzas = $agrup_usu->Get_dato("au_nombre");
	
			$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>AUTORIZADA</strong> por <strong>%s</strong>.",$idTramite,$finanzas);
			$remitente = $t_dueno;//por este momento es controlling o puede ser GA/DA
			$destinatario = $tramite->Get_dato("t_iniciador");
			//$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "0", ""); //"0" para no enviar email y "1" para enviarlo
			
			if($fin_de_ruta==false){
				$iniciador=new Usuario();
				$iniciador->Load_Usuario_By_ID($tramite->Get_dato("t_iniciador"));
				$nombreIniciador = $iniciador->Get_dato('nombre');
				$destinatario = $t_sigAprobador;
				
				if($t_delegado != 0){
					$iniciador->Load_Usuario_By_ID($t_delegado);
					$nombreDelegado = $iniciador->Get_dato('nombre');
					$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> creada por el usuario: <strong>%s</strong> en nombre de: <strong>%s</strong> ha sido <strong>AUTORIZADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.",$idTramite, $nombreDelegado, $nombreIniciador, $finanzas);
				}else{
					$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> creada por el usuario: <strong>%s</strong> ha sido <strong>AUTORIZADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.",$idTramite, $nombreIniciador, $finanzas);
				}
				
				$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "0", ""); //"0" para no enviar email y "1" para enviarlo
				$tramite->Modifica_Etapa($idTramite, COMPROBACION_INVITACION_ETAPA_APROBACION, FLUJO_COMPROBACION_INVITACION, $t_sigAprobador, "");
			}else{
				$iniciador=new Usuario();
				$iniciador->Load_Usuario_By_ID($tramite->Get_dato("t_iniciador"));
				$destinatario = $t_sigAprobador;
				$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>APROBADA</strong> por completo.",$idTramite);
				$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "1", "", 0); //"0" para no enviar email y "1" para enviarlo, el �ltimo argumento indicar� si se coloca el link de ingreso a la aplicaci�n (1) y (0) si no es requerido.
				$tramite->Modifica_Etapa($idTramite, COMPROBACION_INVITACION_ETAPA_APROBADA, FLUJO_COMPROBACION_INVITACION, $t_sigAprobador, "");
				$tramite->setCierreFecha($idTramite);
			}
			// Asigno el valor que trae del campo de texto, debido a que en el index, la variable de sesi�n, se convertia en un entero(ID del usuario delegado).
			$_SESSION['delegado'] = $delegadoNombre;
			
			// Regresa a la pagina de solicitudes de invitaci�n
			if($mobile){
				echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?docs=docs&type=4&action=autorizar'>";
			}else{
				echo ("<script language='Javascript'> location.href='http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?docs=docs&type=4&action=autorizar';</script>");
			}
		}
	}else{
		//$aprobador=$rutaAuto->agregarAutorizacion($t_dueno);
		$duenoActual = new Usuario();
		$duenoActual->Load_Usuario_By_ID($t_dueno);
		$aprobadorNombre = $duenoActual->Get_dato('nombre');
		$siguienteAprobador=new Usuario();
		
		if($delegado != 0){
			$duenoActual_delegado = new Usuario();
			$duenoActual_delegado->Load_Usuario_By_ID($_POST['iu']);
			$delegado_act_nombre = $duenoActual_delegado->Get_dato('nombre');
			$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>AUTORIZADA</strong> por: <strong>%05s</strong> en nombre de: <strong>%05s</strong>.",$idTramite,$delegado_act_nombre,$aprobadorNombre);
		}else{
			$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>AUTORIZADA</strong> por: <strong>%05s</strong>.",$idTramite,$aprobadorNombre);
		}
		
		$remitente = $t_dueno;//por este momento es controlling o puede ser GA/DA
		$destinatario = $tramite->Get_dato("t_iniciador");
		$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "0", ""); //"0" para no enviar email y "1" para enviarlo

		$aprobador=$rutaAuto->agregarAutorizacion($t_dueno,$idTramite);
		
		$iniciador=new Usuario();
		$iniciador->Load_Usuario_By_ID($tramite->Get_dato("t_iniciador"));
		$nombreIniciador = $iniciador->Get_dato('nombre');
	
		$destinatario = $aprobador;
		
		if($delegado != 0){
			$duenoActual_delegado = new Usuario();
			$duenoActual_delegado->Load_Usuario_By_ID($_POST['iu']);
			$delegado_act_nombre = $duenoActual_delegado->Get_dato('nombre');
			
			if($t_delegado != 0){
				$iniciador->Load_Usuario_By_ID($t_delegado);
				$nombreDelegado = $iniciador->Get_dato('nombre');
				$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> creada por el usuario: <strong>%s</strong> en nombre de: <strong>%s</strong> ha sido <strong>AUTORIZADA</strong> por: <strong>%s</strong> en nombre de: <strong>%s</strong> y requiere de su autorizaci&oacute;n.",$idTramite, $nombreDelegado, $nombreIniciador,$delegado_act_nombre,$aprobadorNombre);
			}else{
				$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> creada por el usuario: <strong>%s</strong> ha sido <strong>AUTORIZADA</strong> por: <strong>%s</strong> en nombre de: <strong>%s</strong> y requiere de su autorizaci&oacute;n.",$idTramite,$nombreIniciador,$delegado_act_nombre,$aprobadorNombre);
			}			
			
			$usuario_delegado = $_POST['iu'];
			$t_ruta_autorizacion = agregarDelegadoRuta($delegado, $usuario_delegado, $t_ruta_autorizacion);
			//error_log("---------->>>>>>>>>>Nueva ruta: ".$t_ruta_autorizacion."<<<<<<<<<<<----------");
		}else{
			if($t_delegado != 0){
				$iniciador->Load_Usuario_By_ID($t_delegado);
				$nombreDelegado = $iniciador->Get_dato('nombre');
				$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> creada por el usuario: <strong>%s</strong> en nombre de: <strong>%s</strong> ha sido <strong>AUTORIZADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.",$idTramite, $nombreDelegado, $nombreIniciador,$aprobadorNombre);
			}else{
				$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> creada por el usuario: <strong>%s</strong> ha sido <strong>AUTORIZADA</strong> por: <strong>%s</strong> y requiere de su autorizaci&oacute;n.",$idTramite,$nombreIniciador,$aprobadorNombre);
			}			
		}
		
		if($aprobador != 1000 || $aprobador != '1000'){
			$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "1", ""); //"0" para no enviar email y "1" para enviarlo
		}else{
			$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "0", ""); //"0" para no enviar email y "1" para enviarlo
		}

		$tramite->Modifica_Etapa($idTramite, COMPROBACION_INVITACION_ETAPA_APROBACION, FLUJO_COMPROBACION_INVITACION, $aprobador, $t_ruta_autorizacion);
		// Asigno el valor que trae del campo de texto, debido a que en el index, la variable de sesi�n, se convertia en un entero(ID del usuario delegado).
		$_SESSION['delegado'] = $delegadoNombre;
		
		// Regresa a la pagina de solicitudes de invitaci�n
		if($mobile){
			echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?docs=docs&type=4&action=autorizar'>";
		}else{
			echo ("<script language='Javascript'> location.href='http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?docs=docs&type=4&action=autorizar';</script>");
		}
	}
}

//Rechaza la COMPROBACION DE INVITACION
else if(isset($_POST['rechazar_comp_inv']) && isset($_POST['idT']) && $_POST['idT']!=""){
	//Recibimos campo Observaciones
	$HObser=$_POST['historial_observaciones'];
	$sObser = $_POST['observ_up'];
	$idTramite = $_POST['idT'];
	$delegado = $_POST['delegado'];
	$delegadoNombre = $_POST['delegadoNombre'];
	
	$cnn = new conexion();
	
	$tramite = new Tramite();
	$tramite->Load_Tramite($_POST['idT']);
	
	$t_dueno = $tramite->Get_dato("t_dueno");
	$duenoActual = new Usuario();
	$duenoActual->Load_Usuario_By_ID($t_dueno);
    
	// Modifica la etapa y el dueno
	$tramite->Modifica_Dueno($idTramite, COMPROBACION_INVITACION_ETAPA_RECHAZADA, FLUJO_COMPROBACION_INVITACION, $t_dueno, $tramite->Get_dato('t_iniciador'));
	
	$compInv = new Comprobacion();
	$compInv->Load_Comprobacion_Invitacion_By_co_mi_tramite($_POST['idT']);
	
	//error_log("--->>>>>>>>>> Co_id: ".$compInv->Get_dato("co_id"));
	//Se checa si es comprobacion amex
	$cnn3 = new conexion();
	$query3 = sprintf("UPDATE amex SET estatus='0' WHERE comprobacion_id = '%s'", $compInv->Get_dato("co_id"));
	//error_log($query3);
	$cnn3->ejecutar($query3);	
	
	$dueno_act_nombre = $duenoActual->Get_dato('nombre');
	if($dueno_act_nombre == ""){
		$agrup_usu = new AgrupacionUsuarios();
		$agrup_usu->Load_Grupo_de_Usuario_By_ID($t_dueno);
		$dueno_act_nombre = $agrup_usu->Get_dato("au_nombre");
	}
	
	if($sObser != ""){
		//Actualizamos el campos de observaciones
		$notificacion = new Notificacion();
		$observaciones = $notificacion->anotaObservacion($t_dueno, $HObser, $sObser, FLUJO_COMPROBACION_INVITACION, "");
		//$observaciones = anotaObservacion($t_dueno,$HObser,$sObser);
		$query = sprintf("UPDATE comprobacion_invitacion SET co_observaciones = '%s' WHERE co_mi_tramite = '%s'", $observaciones, $idTramite);
		$cnn->ejecutar($query);
		//error_log($query);
	}
	
	//Envia notificacion al iniciador de la solicitud de invitacion ----------------------------------
	if($delegado != 0){
		$duenoActual_delegado = new Usuario();
		$duenoActual_delegado->Load_Usuario_By_ID($_POST['iu']);
		$delegado_act_nombre = $duenoActual_delegado->Get_dato('nombre');
		$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>RECHAZADA</strong> por: <strong>%s</strong> en nombre de: <strong>%s</strong>.",$idTramite,$delegado_act_nombre,$dueno_act_nombre);
	}else{
		$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>RECHAZADA</strong> por: <strong>%s</strong>.", $_POST['idT'], $dueno_act_nombre);
	}
	
	$remitente = $t_dueno;
	$destinatario = $tramite->Get_dato("t_iniciador");
	$tramite->EnviaNotificacion($_POST['idT'], $mensaje, $remitente, $destinatario, "0", ""); //false para no enviar email
	// Asigno el valor que trae del campo de texto, debido a que en el index, la variable de sesi�n, se convertia en un entero(ID del usuario delegado).
	$_SESSION['delegado'] = $delegadoNombre;
	
	// Regresa a la pagina de solicitudes de invitaci�n
    if($mobile){
    	echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?docs=docs&type=4&action=rechazar'>";
    }else{
    	echo ("<script language='Javascript'> location.href='http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?docs=docs&type=4&action=rechazar';</script>");
    }
}

// Envia a Supervisor de Finanzas
else if(isset($_POST['envia_supervisor']) && isset($_POST['idT']) && $_POST['idT']!=""){
	//Recibimos campo Observaciones
	$HObser=$_POST['historial_observaciones'];
	$sObser = $_POST['observ_up'];
	$idTramite = $_POST['idT'];
	//$monto_aprobado_finanzas = $_POST['total_comprobado'];
	$filas = $_POST['total_rows'];
	$amex_comprobado = $_POST['t_amex_comprobado'];
	$efectivo_comprobado = $_POST['t_efectivo_comprobado'];
	$personal_a_descontar = $_POST['t_personal'];
	$descuento = $_POST['t_descuento'];
	$reembolso = $_POST['t_reembolso'];
	$comprobacionID = $_POST['comprobacionID'];
	$cDate = date('d/m/Y');
	$divisaComp = $_POST['divisaComp'];
	
	error_log(">>>>>>>>>>> Amex_comprobado: ".$_POST['t_amex_comprobado']);
	error_log(">>>>>>>>>>> Personal_a_descontar: ".$_POST['t_efectivo_comprobado']);
	
	switch ($divisaComp){
		case 1:
			$tasaComp = "1.00";
			break;
		case 2:
			$tasaComp = $_POST['tasaUSD'];
			break;
		case 3:
			$tasaComp = $_POST['tasaEUR'];
			break;
		default:
			$tasaComp = "1.00";
			break;		
	}

	$cnn = new conexion();

	$tramite = new Tramite();
	$tramite->Load_Tramite($idTramite);
	$t_dueno = $tramite->Get_dato("t_dueno");
	
	// Verfificamos si la comprobac�on fue de tipo AMEX � REEMBOLSO
	if($_POST['t_amex_comprobado'] != 0.00 || $_POST['t_amex_comprobado'] != "0.00"){ // Si el monto de AMEX es direfente de Cero, asignaremos al monto de aprobaci�n a la variable.
		$monto_aprobado_finanzas = $_POST['t_amex_comprobado'];
	}else{
		$monto_aprobado_finanzas = $_POST['t_efectivo_comprobado'];
	}
	
	if($monto_aprobado_finanzas <= ULT_APROBACION){//eamg
		// Supervisor de finanzas
		$usuario=new Usuario();
		$tu_id=$usuario->getidtipo(SUPERVISOR_FINANZAS);
		$SupervisorFinanzas=$usuario->getGerenteSFinanzas($tu_id);
		$t_sig_dueno = $SupervisorFinanzas;
		$destinatario = $SupervisorFinanzas;
	}else{
		// Gerente de finanzas
		$usuario=new Usuario();
		$tu_id=$usuario->getidtipo(GERENTE_FINANZAS);
		$GerenteFinanzas=$usuario->getGerenteSFinanzas($tu_id);
		$t_sig_dueno = $GerenteFinanzas;
		$destinatario = $GerenteFinanzas;
	}

	// Modifica la etapa y el dueno
	$tramite->Modifica_Dueno_Etapa($idTramite, COMPROBACION_INVITACION_ETAPA_APROBACION_SUPERVISOR_FINANZAS, FLUJO_COMPROBACION_INVITACION, $t_sig_dueno);
	
	if($sObser != ""){
		//Actualizamos el campos de observaciones
		$notificacion = new Notificacion();
		$observaciones = $notificacion->anotaObservacion($t_dueno, $HObser, $sObser, FLUJO_COMPROBACION_INVITACION, "");
		//$observaciones = anotaObservacion($t_dueno,$HObser,$sObser);
		$query = sprintf("UPDATE comprobacion_invitacion SET co_observaciones = '%s' WHERE co_mi_tramite = '%s'", $observaciones, $idTramite);
		$cnn->ejecutar($query);
		//error_log($query);
	}
	
	// Eliminaremos el concepto de personal, por si el usuario ha decidido modificar o eliminar el concepto asociado a la comprobaci�n.
	$detalleConceptoPersonal = 0;
	$query = sprintf("SELECT dc_id FROM detalle_comprobacion_invitacion INNER JOIN comprobacion_invitacion ON co_id = dc_comprobacion WHERE dc_concepto = '31' AND co_mi_tramite = '%s'", $idTramite);
	//error_log($query);
	$rst = $cnn->consultar($query);
	$fila = mysql_fetch_assoc($rst);
	$detalleConceptoPersonal = $fila['dc_id'];
	
	$query = sprintf("DELETE FROM detalle_comprobacion_invitacion WHERE dc_id = '%s'", $detalleConceptoPersonal);
	//error_log($query);
	$cnn->ejecutar($query);
	
	// Actualiza los montos del detalle de la comprobaci�n
	for($i=1; $i<=$filas; $i++){
		$concepto = $_POST['concepto'.$i];
		$monto = $_POST['dc_monto'.$i];
		$iva = $_POST['dc_iva'.$i];
		$propina = $_POST['dc_propinas'.$i];
		$id_dci = $_POST['dc_id'.$i];
		$new_concept = $_POST['concepto'.$i];
		$total = $_POST['total'.$i];
		$comentario = $_POST['comentario'.$i];
		
		$monto = str_replace(',', '', $monto);
		$iva = str_replace(',', '', $iva);
		$propina = str_replace(',', '', $propina);
		$total = str_replace(',', '', $total);
		
		$cImp_porc = 0;
		if($iva != 0 || $iva != 0.00){
			$cImp_porc = "16";
		}
		//error_log(">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Concepto: ".$concepto);
		if($concepto != 31){ // Si en la lista de conceptos comprobados existe un 35, indicar� que corresponde a un concepto "Personal" y que deber� ser tratado como una inserci�n nueva.
			$query = sprintf("UPDATE detalle_comprobacion_invitacion SET dc_concepto = '%s', dc_monto = '%s', dc_iva = '%s', dc_propinas = '%s', dc_total = '%s' WHERE dc_id = '%s'", $new_concept, $monto, $iva, $propina, $total, $id_dci);
			//error_log($query);
			$cnn->ejecutar($query);	
		}else{
			$comprobacion = new Comprobacion();
			$comprobacion->Agrega_Detalle_Comp_Invitacion2($comprobacionID, $concepto, "", $monto, $cImp_porc, $iva, $total, "0", $cDate, $divisaComp, $tasaComp, "0", $propina, "0", $total, "0", $comentario);
			//$comprobacion->Agrega_Detalle_Comp_Invitacion2($comprobacionID,$concepto,"",$monto,$cImp_porc,$iva,$total,"0",$cDate,"MXN","1.00","0",$propina,"0",$total,"0",$comentario);
		}
	}
	
	$query = sprintf("UPDATE comprobacion_invitacion SET 
			co_amex_comprobado = '%s', 
			co_efectivo_comprobado = '%s', 
			co_personal_descuento = '%s', 
			co_mnt_descuento = '%s', 
			co_mnt_reembolso = '%s' 
			WHERE co_mi_tramite = '%s'", 
			$amex_comprobado, 
			$efectivo_comprobado, 
			$personal_a_descontar, 
			$descuento, 
			$reembolso, 
			$idTramite);
	//error_log($query);
	$cnn->ejecutar($query);
	
	//Envia notificacion al Supervisor/Gerente de Finanzas de la solicitud de invitaci�n ----------------------------------
	$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> te ha sido <strong>ASIGNADA</strong> para su Aprobaci&oacute;n.", $idTramite);
	$remitente = $t_sig_dueno;
	$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "0", ""); //false para no enviar email

	if($mobile){
		header("Location: ".$RUTA_R."inicial.php?action=envia");
	} else {
		// Regresa a la pagina de solicitudes de invitacion
		echo("<script language='javascript' type='text/javascript'> 
		if(confirm('�Desea imprimir la comprobaci�n actual?')){ 
			window.open('generador_pdf_comp_inv.php?id=".$idTramite."', 'imprimir');
			location.href='./index.php?docs=docs&type=4&action=envia';
		}else{
			location.href='./index.php?docs=docs&type=4&action=envia';
		}
		</script>");
		//echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?docs=docs&type=4&action=envia'>";
	}
}

// Devolver con Observaciones al empleado
else if(isset($_POST['devolver_observ']) && isset($_POST['idT']) && $_POST['idT']!=""){
	//Recibimos campo Observaciones
	$HObser=$_POST['historial_observaciones'];
	$sObser = $_POST['observ_up'];
	$idTramite = $_POST['idT'];

	$cnn = new conexion();

	$tramite = new Tramite();
	$tramite->Load_Tramite($idTramite);
	
	$compInv = new Comprobacion();
	$compInv->Load_Comprobacion_Invitacion_By_co_mi_tramite($idTramite);
	//Se checa si es comprobacion amex
// 	$cnn3 = new conexion();
// 	$query3 = sprintf("UPDATE amex SET estatus='0' WHERE comprobacion_id = '%s'", $compInv->Get_dato("co_id"));
// 	//echo $query3;
// 	$cnn3->ejecutar($query3);

	$rutaAuto=new RutaAutorizacion();
	$t_dueno=$rutaAuto->getDueno($idTramite);
	$duenoActual = new Usuario();
	$duenoActual->Load_Usuario_By_ID($t_dueno);
	
	// Extraemos el siguente aprobador
	// Modifica la etapa y el dueno
	$tramite->Modifica_Etapa($idTramite, COMPROBACION_INVITACION_ETAPA_DEVUELTA_CON_OBSERVACIONES, FLUJO_COMPROBACION_INVITACION, $t_dueno, "");
 	//$tramite->Modifica_Dueno($idTramite, COMPROBACION_INVITACION_ETAPA_DEVUELTA_CON_OBSERVACIONES, FLUJO_COMPROBACION_INVITACION, $t_dueno, $tramite->Get_dato('t_iniciador'));
	$dueno_act_nombre = $duenoActual->Get_dato('nombre');
	
	if($dueno_act_nombre == ""){
		$agrup_usu = new AgrupacionUsuarios();
		$agrup_usu->Load_Grupo_de_Usuario_By_ID($t_dueno);
		$dueno_act_nombre = $agrup_usu->Get_dato("au_nombre");
	}

	if($sObser != ""){
		//Actualizamos el campos de observaciones
		$notificacion = new Notificacion();
		$observaciones = $notificacion->anotaObservacion($t_dueno, $HObser, $sObser, FLUJO_COMPROBACION_INVITACION, COMPROBACION_INVITACION_ETAPA_DEVUELTA_CON_OBSERVACIONES);
		//$observaciones = anotaObservacion($t_dueno,$HObser,$sObser);
		$query = sprintf("UPDATE comprobacion_invitacion SET co_observaciones = '%s' WHERE co_mi_tramite = '%s'", $observaciones, $idTramite);
		$cnn->ejecutar($query);
		//error_log($query);
	}

	//Envia notificacion al iniciador de la solicitud de invitacion ----------------------------------
	$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>DEVUELTA</strong> con <strong>OBSERVACIONES</strong> por <strong>%s</strong>.", $idTramite, $dueno_act_nombre);
	$remitente = $t_dueno;
	$destinatario = $tramite->Get_dato("t_iniciador");
	$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "0", ""); //false para no enviar email
	
	// Regresa a la pagina de solicitudes de invitaci�n
	if($mobile){
		echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?docs=docs&type=4&action=devolver'>";
	}else{
		echo ("<script language='Javascript'> location.href='http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?docs=docs&type=4&action=devolver';</script>");
	}
}

// Aprobaci�n de la comprobaci�n por el Director
else if(isset($_POST['aprobar_ci']) && isset($_POST['idT']) && $_POST['idT']!=""){
	$idTramite = $_POST['idT'];
	$iduser = $_POST['iu'];
	$observaciones = $_POST['observ_up'];
	
	$cnn = new conexion();
	$tramite=new Tramite();
	$tramite->Load_Tramite($idTramite);
	$t_dueno = $tramite->Get_dato('t_dueno');
	$t_delegado = $tramite->Get_dato('t_delegado');
	$iniciador = $tramite->Get_dato('t_iniciador');
	
	if($observaciones != ""){
		$query = sprintf("SELECT co_observaciones FROM comprobacion_invitacion WHERE co_mi_tramite = '%s'",$idTramite);
		$rst = $cnn->consultar($query);
		$fila = mysql_fetch_assoc($rst);
		$HObser = $fila['co_observaciones'];
		
		$notificacion = new Notificacion();
		$observaciones = $notificacion->anotaObservacion($iduser, $HObser, $observaciones, FLUJO_COMPROBACION_INVITACION, COMPROBACION_ETAPA_EN_APROBACION);
	
		$queryInsertaObs=sprintf("UPDATE comprobacion_invitacion SET co_observaciones = '%s' WHERE co_mi_tramite = '%s'", $observaciones, $idTramite);
		$cnn->ejecutar($queryInsertaObs);
	}
	
	// Buscamos quien debe aprobar esta solicitud
	$ruta_autorizacion = new RutaAutorizacion();
	$ruta_autorizacion->generRutaAutorizacionComprobacionInvitacion($idTramite, $iduser);
	$aprobador = $ruta_autorizacion->getAprobador($idTramite, $iduser);
	
	// Envia el tramite a aprobacion
	$duenoActual = new Usuario();
	$duenoActual->Load_Usuario_By_ID($iduser);
	$nombreUsuario = $duenoActual->Get_dato('nombre');
	
	$duenoActual->Load_Usuario_By_ID($t_delegado);
	$nombreDelegado = $duenoActual->Get_dato('nombre');
	$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>CREADA</strong> por: <strong>%s</strong> en nombre de: <strong>%s</strong> y requiere de su autorizaci&oacute;n.", $idTramite, $nombreDelegado, $nombreUsuario);
	
	$remitente = $iduser;
	$destinatario = $aprobador;
	$tramite->Modifica_Etapa($idTramite, COMPROBACION_INVITACION_ETAPA_APROBACION, FLUJO_COMPROBACION_INVITACION, $aprobador, "");
	$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "1", ""); //"0" para no enviar email y "1" para enviarlo
	
	// Regresa a la pagina de solicitudes de invitaci�n
	if($mobile){
		echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?docs=docs&type=4&action=autorizar'>";
	}else{
		echo ("<script language='Javascript'> location.href='http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?docs=docs&type=4&action=autorizar';</script>");
	}
}

// Rechazo de la comprobaci�n por el Director
else if(isset($_POST['rechazar_ci']) && isset($_POST['idT']) && $_POST['idT']!=""){
	$idTramite = $_POST['idT'];
	$iduser = $_POST['iu'];
	$observaciones = $_POST['observ_up'];
	
	$cnn = new conexion();
	$tramite=new Tramite();
	$tramite->Load_Tramite($idTramite);
	$t_dueno = $tramite->Get_dato('t_dueno');
	$t_delegado = $tramite->Get_dato('t_delegado');
	$iniciador = $tramite->Get_dato('t_iniciador');
	
	$compInv = new Comprobacion();
	$compInv->Load_Comprobacion_Invitacion_By_co_mi_tramite($idTramite);
	$query3 = sprintf("UPDATE amex SET estatus='0' WHERE comprobacion_id = '%s'", $compInv->Get_dato("co_id"));
	//error_log($query3);
	$cnn->ejecutar($query3);
	
	if($observaciones != ""){
		$query = sprintf("SELECT co_observaciones FROM comprobacion_invitacion WHERE co_mi_tramite = '%s'",$idTramite);
		$rst = $cnn->consultar($query);
		$fila = mysql_fetch_assoc($rst);
		$HObser = $fila['co_observaciones'];
	
		$notificacion = new Notificacion();
		$observaciones = $notificacion->anotaObservacion($iduser, $HObser, $observaciones, FLUJO_COMPROBACION_INVITACION, COMPROBACION_ETAPA_EN_APROBACION);	
		$queryInsertaObs=sprintf("UPDATE comprobacion_invitacion SET co_observaciones = '%s' WHERE co_mi_tramite = '%s'", $observaciones, $idTramite);
		$cnn->ejecutar($queryInsertaObs);
	}
	
	$duenoActual = new Usuario();
	$duenoActual->Load_Usuario_By_ID($iduser);
	$nombreUsuario = $duenoActual->Get_dato('nombre');
	$mensaje = sprintf("La Comprobaci&oacute;n de Invitaci&oacute;n <strong>%05s</strong> ha sido <strong>RECHAZADA</strong> por: <strong>%s</strong>.", $idTramite, $nombreUsuario);
	
	$remitente = $iduser;
	$destinatario = $iniciador;
	$tramite->Modifica_Dueno($idTramite, COMPROBACION_INVITACION_ETAPA_RECHAZADA_POR_DIRECTOR, FLUJO_COMPROBACION_INVITACION, $t_dueno, $iniciador);
	//$tramite->Modifica_Etapa($idTramite, COMPROBACION_INVITACION_ETAPA_RECHAZADA_POR_DIRECTOR, FLUJO_COMPROBACION_INVITACION, $iniciador, "");
	$tramite->EnviaNotificacion($idTramite, $mensaje, $remitente, $destinatario, "0", ""); //"0" para no enviar email y "1" para enviarlo
	
	// Regresa a la pagina de solicitudes de invitaci�n
	if($mobile){
		echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?docs=docs&type=4&action=rechazar'>";
	}else{
		echo ("<script language='Javascript'> location.href='http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?docs=docs&type=4&action=rechazar';</script>");
	}
}

else if((isset($_GET['view']) && isset($_GET['id'])) || (isset($_GET['edit_view']) && isset($_GET['id'])) || (isset($_GET['VIEW']) && isset($_GET['ID'])) || (isset($_GET['EDIT_VIEW']) && isset($_GET['ID']))){
	if(isset($_GET['id'])){
		$idTramite=$_GET['id'];
	}else{
		$idTramite=$_GET['ID'];
	}
	$idUser=$_SESSION["idusuario"];  
	$noEmpleado=$_SESSION["idusuario"];
    $invitados= array();
	
	//Checa si el usuario autorizador es de Controlling o Finanzas,
	//si es asi podra cambiar el centro de costos y el concepto
    $filas = 0;
	$es_controlling_o_finanzas = 0;
	$cnn = new conexion();
	$query = sprintf("SELECT * FROM homologacion_dueno WHERE hd_u_id = '%s' AND hd_au_id != '3000'", $idUser);
	//error_log($query);
	$rst = $cnn->consultar($query);
	$filas = mysql_num_rows($rst);
	if($_SESSION["perfil"] == 5 || $_SESSION["perfil"] == 6){
		$es_controlling_o_finanzas = 1;
	}
	
	//error_log("es_controlling_o_finanzas: ".$es_controlling_o_finanzas);

    $usu = new Usuario();
	$usu->Load_Usuario_By_ID($idUser);
	$nombreEmpleado=$usu->Get_dato("nombre");
    
	//Se carga el tramite de la comprobacion
	$tramite = new Tramite();
	$tramite->Load_Tramite($idTramite);
	
	//Se carga la comprobacion
	$comp_inv = new Comprobacion();
	$comp_inv->Load_Comprobacion_Invitacion_By_co_mi_tramite($idTramite);
	
	//Se obtiene el id del tramite de la solicitud de invitacion
	$id_tramite_sol_inv = $comp_inv->Get_dato("co_tramite");

	//Se carga la solicitud de invitacion a la que hace referencia la comprobacion
	$sol_inv = new C_SV();
	$sol_inv->Load_Solicitud_Invitacion_Tramite($id_tramite_sol_inv);
	
	//Datos de la comprobacion
	$idComprobacion    = $comp_inv->Get_dato("co_id");
    $co_fecha_registro = $comp_inv->Get_dato("co_fecha_registro");
    $co_tipo    = $comp_inv->Get_dato("co_tipo");
    $motivo     = $comp_inv->Get_dato("co_motivo");
    $ciudad     = $comp_inv->Get_dato("co_ciudad");
    $co_tramite = $comp_inv->Get_dato("co_tramite");
    $co_cc_clave = $comp_inv->Get_dato("co_cc_clave");
    $subtotal_comp	= number_format($comp_inv->Get_dato("co_subtotal"),2,".",",");
    $iva_comp	= number_format($comp_inv->Get_dato("co_iva"),2,".",",");
    $total_comp	= number_format($comp_inv->Get_dato("co_total"),2,".",",");
    $co_total_aprobado = number_format($comp_inv->Get_dato("co_total_aprobado"),2,".",",");
    $co_pendiente = number_format($comp_inv->Get_dato("co_total") - $comp_inv->Get_dato("co_total_aprobado"),2,".",",");
    $observaciones = $comp_inv->Get_dato("co_observaciones");
    $co_num_invitados = $comp_inv->Get_dato("co_num_invitado");
    $co_lugar = $comp_inv->Get_dato("co_lugar");
    $co_fecha_invitacion = darFormatoFecha($comp_inv->Get_dato("co_fecha_invitacion"));
    $co_hubo_excedente = $comp_inv->Get_dato("co_hubo_exedente");
    $co_amex_comprobado = number_format($comp_inv->Get_dato("co_amex_comprobado"),2,".",",");
    $co_monto_a_reembolsar = number_format($comp_inv->Get_dato("co_mnt_reembolso"),2,".",",");
    $co_efectivo_comprobado = number_format($comp_inv->Get_dato("co_efectivo_comprobado"),2,".",",");
    $co_personal_a_descontar = number_format($comp_inv->Get_dato("co_personal_descuento"),2,".",",");
    $co_descuento = number_format($comp_inv->Get_dato("co_mnt_descuento"),2,".",",");
	
    $referencia = $tramite->Get_dato("t_etiqueta");
    $t_owner    = $tramite->Get_dato("t_dueno");
	$comprobacion_etapa = $tramite->Get_dato("t_etapa_actual");
	$comprobacion_flujo = $tramite->Get_dato("t_flujo");
	
	$co_fecha_registro =  darFormatoFecha($co_fecha_registro);

	$t_iniciador = $tramite->Get_dato("t_iniciador");
	$usuarioIniciador = new Usuario();
	$usuarioIniciador->Load_Usuario_By_ID($t_iniciador);
	$nombre_del_solicitante_comp = $usuarioIniciador->Get_dato('nombre');

	//Checa si el usuario dueno es de Controlling o Finanzas,
	//si es asi aparecera el boton de imprimir
    $filas = 0;
	$dueno_es_controlling_o_finanzas = 0;
	$query = "select * from agrupacion_usuarios where au_id = $t_owner";
	//error_log($query);
	$rst = $cnn->consultar($query);
	$filas = mysql_num_rows($rst);
	if( $filas != 0 ){
		$dueno_es_controlling_o_finanzas = 1;
	}

	$query = "select dc_monto, dc_iva, dc_propinas, dc_divisa, dc_proveedor,dci_monto_total_pesos, dc_concepto, 
			dc_total_aprobado,dc_factura,dc_comentarios, dc_rfc, dc_folio_factura 
			from detalle_comprobacion_invitacion where dc_comprobacion = $idComprobacion";
	$rst = $cnn->consultar($query);
	$fila = mysql_fetch_assoc($rst);
	$dci_monto = $fila['dc_monto'];
	$dci_iva = $fila['dc_iva'];
	$dci_divisa = $fila['dc_divisa'];
	$dci_propina = $fila['dc_propinas'];
	$dci_proveedor = $fila['dc_proveedor'];
	$dci_concepto = $fila['dc_concepto'];
	$dc_total_aprobado = $fila['dc_total_aprobado'];
	$co_total = $total_comp;
	$dci_monto_total_pesos = $fila['dci_monto_total_pesos'];
	$dc_factura = $fila['dc_factura'];
	$dc_comentarios = $fila['dc_comentarios'];
	$dci_rfc = $fila['dc_rfc'];
	$dci_folio_factura = $fila['dc_folio_factura'];

	$query = "select cp_concepto from cat_conceptosbmw where dc_id = $dci_concepto";
	$rst = $cnn->consultar($query);
	$fila = mysql_fetch_assoc($rst);
	$dci_concepto_nombre = $fila['cp_concepto'];
	
	
	//Se obtiene el nombre del proveedor
	$query = "select pro_proveedor from proveedores where pro_id = $dci_proveedor";
	$rst = $cnn->consultar($query);
	$fila = mysql_fetch_assoc($rst);
	$dci_proveedor = $fila['pro_proveedor'];
	
	//Carga el nombre de la etapa en que esta la comprobacion de invitacion
	$etapa = new Etapa();
	$etapa->Load_Etapa_by_etapa_y_flujo($comprobacion_etapa,$comprobacion_flujo);
	$comprobacion_etapa_nombre = $etapa->Get_dato("et_etapa_nombre");
	
    $divisa	= $sol_inv->Get_dato("si_divisa");
    $total_solicitado = number_format($sol_inv->Get_dato("si_monto"),2,".",",");
    $monto_pesos = number_format($sol_inv->Get_dato("si_monto_pesos"),2,".",",");   

	//Se cargan los comensales de la comprobacion de invitacion
	$comensales = new Comensales();
	$invitados = $comensales->Load_comensales_by_tramite($idTramite);
	
    // datos del centro de costos
	$cc = new CentroCosto();
	$cc->Load_CeCo($co_cc_clave);
    $cc_centrocostos=$cc->Get_Dato("cc_centrocostos");
    $cc_nombre=$cc->Get_Dato("cc_nombre");
    $cc->Busca_CeCoXCodigo($cc_centrocostos);
    $cc_id = $cc->Get_Dato("cc_id");

    // Nombre del autorizador
	$usuAutorizador = new Usuario();
	if($usuAutorizador->Load_Usuario_By_ID($t_owner)){
		$aprobador = $usuAutorizador->Get_dato('u_nombre');
	}else{
		$agrup_usu2 = new AgrupacionUsuarios();
		$agrup_usu2->Load_Grupo_de_Usuario_By_ID($t_owner);
		$aprobador = $agrup_usu2->Get_dato("au_nombre");
	}

    // Carga datos de autorizadores
    $ruta_autorizadores = $tramite->Get_dato("t_ruta_autorizacion");
    $autorizaciones     = $tramite->Get_dato("t_autorizaciones");
	
    //Traer� la ruta de autorizaci�n de la solicitud correspondiente
    $rutaAutorizacion = new RutaAutorizacion();
    $autorizadores = $rutaAutorizacion->getNombreAutorizadores($idTramite);
	
	//Obtener Divisa Euro
	$_divisas = new Divisa();
	$_divisas->Load_data(3); //busca Id de divisa
	$divisaEUR = $_divisas->Get_dato("div_tasa");
	//Obtener Divisa D�lar
	$_divisas1 = new Divisa();
	$_divisas1->Load_data(2);
	$divisaUSD = $_divisas1->Get_dato("div_tasa");
	
	$t_delegado = $tramite->Get_dato("t_delegado");
	// Verificamos si la solicitud fue realizada por un delegado; de ser as� imprimiremos el nombre de los involucrados
	if($t_delegado != 0){
		$duenoActual = new Usuario();
		$duenoActual->Load_Usuario_By_ID($t_delegado);
		$nombredelegado = $duenoActual->Get_dato('nombre');
		$nombre_del_solicitante_comp = "<font color='#0000CA'>".$nombredelegado."</font>".strtoupper(" en nombre de: ").$nombre_del_solicitante_comp;
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.date_input.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.tablesorter.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.autocomplete.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.bgiframe.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/formatNumber.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/dom-drag.js" type="text/javascript"></script>
<script language="JavaScript" src="../../lib/js/jquery/jquery.blockUI.js" type="text/javascript"></script> 
<script language="JavaScript" src="../solicitudes/js/solicitud_viaje.js" type="text/javascript"></script> 
<script language="JavaScript" src="js/utilities.js" type="text/javascript"></script>
<script language="JavaScript" src="js/backspaceGeneral.js" type="text/javascript"></script>
<script language="JavaScript" src="js/communication_ajax.js" type="text/javascript"></script>		      
<script language="JavaScript" src="../../lib/js/withoutReloading.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="../../css/date_input.css"/>
<link rel="stylesheet" type="text/css" href="../../css/table_style.css"/>
<script language="javascript" type="text/javascript">
var doc;
var cc_centrocostos;
var cc_codigo_new=0;
var valConcepto="";
var registrosOriginales=0;
//variables editables
var montoOriginal=0;
var ivaOriginal=0;
var propinaOriginal=0;
var imphospedajeOriginal=0;
var totalOriginal=0;
// variable para agregar nuevas filas
var i=0;
//variables para el calculo del resumen
var personal_amex =0;
var personal_anticipo=0;
//variables para la actualizacion de datos necesarios (Pediente redaccion)
var cont=0;
//total
var dc_total=0;
doc = $(document);
doc.ready(inicializarEventos);
var frm = document.comp_inv;
function inicializarEventos(){
	// Realizamos la validacion de la etapa correspondiente
	validaEtapa();
	// Creamos la tabla temporal de montos
	montosTabla();
	// Creamos la tabla temporal que permitira registrar los conceptos
	conceptosTabla();
	// Ocultamos los botones (Eliminar /recalcular)	
	desactivarBoton();

	// Obtendremos el valor de los registros originales que existan al principio
	registrosOriginales = parseInt($("#descrip_comprobacion_table>tbody >tr").length);
	// alert("Inicio de aplicacion (registros)"+ registrosOriginales);

	//Seleccionar el centro de costos del usuario actual
	//select idcentrocosto from empleado where idempleado = "110"
	var id_centro_de_costos = "<? echo $cc_centrocostos; ?>";
	seleccionar(id_centro_de_costos);
	
	//Seleccionar el concepto
	var id_concepto = "<? echo $dci_concepto; ?>";
	seleccionar_concepto(id_concepto);
	
	// Asignamos los valores extraidos de la BD hacia las capas	
	var tipo_comp = "<?php echo $co_tipo;?>";

	if(tipo_comp == 1){
		Anticipo_comprobado_autorizado_X_BMW(0);
	}else{
		efectivo_comprobado_autorizado_X_BMW(0);
	}
	personal_descontar_BMW(0);
	monto_a_descontar();
	monto_a_reembolsar();

	/* 
	 * Impedimos que salga de la pantalla al teclear el backspace en alg�n bot�n
	 */

	// Botones de Acci�n
	$('#autorizar_comp_inv').focus(function() {
		confirmaRegreso('autorizar_comp_inv');
	});
	
	$('#envia_supervisor').focus(function() {
		confirmaRegreso('envia_supervisor');
	});
	
	$('#rechazar_comp_inv').focus(function() {
		confirmaRegreso('rechazar_comp_inv');
	});

	$('#Volver').focus(function() {
		confirmaRegreso('Volver');
	});

	$('#devolver_observ').focus(function() {
		confirmaRegreso('devolver_observ');
	});

	$('#aprobar_ci').focus(function() {
		confirmaRegreso('aprobar_ci');
	});

	$('#rechazar_ci').focus(function() {
		confirmaRegreso('rechazar_ci');
	});
	
	$('#imprimir').focus(function() {
		confirmaRegreso('imprimir');
	});

	// Ocultar botones para evitar que el usuario de doble click en alguno de estos
	$("#autorizar_comp_inv").click(function(e){
		$(this).fadeOut("slow");
		$("#envia_supervisor").fadeOut("slow");
		$("#Volver").fadeOut("slow");
		$("#rechazar_comp_inv").fadeOut("slow");
		$("#devolver_observ").fadeOut("slow");
	});

	$("#envia_supervisor").click(function(e){
		$(this).fadeOut("slow");
		$("#autorizar_comp_inv").fadeOut("slow");
		$("#Volver").fadeOut("slow");
		$("#rechazar_comp_inv").fadeOut("slow");
		$("#devolver_observ").fadeOut("slow");
	});

	$("#Volver").click(function(e){
		$(this).fadeOut("slow");
		$("#autorizar_comp_inv").fadeOut("slow");
		$("#envia_supervisor").fadeOut("slow");
		$("#rechazar_comp_inv").fadeOut("slow");
		$("#devolver_observ").fadeOut("slow");
		$("#aprobar_ci").fadeOut("slow");
		$("#rechazar_ci").fadeOut("slow");
	});

	$("#rechazar_comp_inv").click(function(e){
		$(this).fadeOut("slow");
		$("#autorizar_comp_inv").fadeOut("slow");
		$("#envia_supervisor").fadeOut("slow");
		$("#Volver").fadeOut("slow");
		$("#devolver_observ").fadeOut("slow");
	});
	
	$("#aprobar_ci").click(function(e){
		$(this).fadeOut("slow");
		$("#rechazar_ci").fadeOut("slow");
		$("#Volver").fadeOut("slow");
	});

	$("#rechazar_ci").click(function(e){
		$(this).fadeOut("slow");
		$("#aprobar_ci").fadeOut("slow");
		$("#Volver").fadeOut("slow");
	});
} // Fin ready � inicializarEventos

//Funci�n que permitira desactival los botones (Eliminar/recalculo)	
function desactivarBoton(){
	var filas = parseInt($("#descrip_comprobacion_table>tbody >tr").length);
	for(var i=1;i<=filas;i++){
		$("#recalculo"+i).css("display", "none");
		$("#eliminar"+i).css("display", "none");
	}
}

// Seleccionar elemento del combo de centro_de_costos_new
function seleccionar(elemento) {
   var combo = document.comp_inv.centro_de_costos_new;
   if(combo != undefined){
	   var cantidad = combo.length;

	   for (var i = 0; i < cantidad; i++) {
		  var toks=combo[i].text.split("-");
		  if (toks[0] == elemento) {
			 combo[i].selected = true;
			 break;
		  }
	   }
   }
}

// Seleccionar elemento del combo de concepto_new
function seleccionar_concepto(elemento) {
   var combo = document.comp_inv.concepto_new;
   if(combo != undefined){
	   var cantidad = combo.length;

	   for (var i = 0; i < cantidad; i++) {
		  if (combo[i].value == elemento) {
			 combo[i].selected = true;
			 break;
		  }
	   }
	}
}

function Location(){
    //location.href='./index.php';
	location.href='./index.php?docs=docs&type=4';
	//location.href='../notificaciones/index.php';
}

// Validaci�n campos numericos
function validaNum(valor){
	cTecla=(document.all)?valor.keyCode:valor.which;
	if(cTecla==8) return true;
	patron=/^([0-9.]{1,2})?$/;
	cTecla= String.fromCharCode(cTecla);
	return patron.test(cTecla);
}

function imprimir_pdf(id_tramite){
	window.open("generador_pdf_comp_inv.php?id="+id_tramite,"imprimir")
}

//realizamos la validacion de la etapa correspondiente al tramite
function validaEtapa(){
<?php 
	// Si la etapa esta en una etapa 7 (COMPROBACION_ETAPA_VALIDADO_POR_SF)
	if($comprobacion_etapa == COMPROBACION_INVITACION_ETAPA_APROBADA_POR_SUPERVISOR_FINANZAS){
	?>
	$("#autorizar").attr("disabled",false);
	$("#rechazar").attr('disabled',true);
	$("#devolverObservaciones").attr('disabled',true);							
	<?php 	
	}
?>
 } 
</script>
<div align="center">
<form name="comp_inv" id="comp_inv" action="" method="post">

    <table id="comprobacion_table" border="0" cellspacing="3" width="75%" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">
        <tr>
			<td colspan="5"><div align="center" style="color:#003366"><strong>Informaci&oacute;n de la comprobaci&oacute;n de la solicitud de invitaci&oacute;n</strong></div></td>
        </tr>
        <tr>
            <td colspan="5">&nbsp;</td>
        </tr>
        
        <tr>
          	<td width="6%"><div align="right">Tr&aacute;mite:</div></td>
            <td width="18%"><strong><?php echo $idTramite ?></strong></td>
            <td width="2%">&nbsp;</td>
            <td width="12%"><div align="right">Fecha de creaci&oacute;n:</div></td>
            <td width="14%"><div align="left"><strong><?php echo $co_fecha_registro ?></strong></div></td>
        </tr>
        
        <tr>
            <td ><div align="right">Solicitante:</div></td>
            <td ><div align="left"><strong><?php echo $nombre_del_solicitante_comp;?></strong></div></td>
            <td >&nbsp;</td>
			<?php if($es_controlling_o_finanzas == 1){
			if ($tipoUsuario == 5 || $tipoUsuario == 6){?>
				<td ><div align="right">Etapa de la comprobaci&oacute;n:</div></td>
				<td ><div align="left"><strong><?php echo $comprobacion_etapa_nombre;?></strong></div></td>
			<? }
			}else{?>
            <td ><div align="right">Centro de costos:</div></td>
			<td ><div align="left"><strong><? echo $cc_centrocostos.' - '.$cc_nombre ?></strong></div>
			<?}?>
			</td>
        </tr>
        <tr>
            <td ><div align="right">Lugar de invitaci&oacute;n/Restaurante:</div></td>
            <td ><div align="left"><strong><?php echo $co_lugar;?></strong></div></td>
            <td >&nbsp;</td>
			<?php if($es_controlling_o_finanzas == 0){?>
				<td ><div align="right">Etapa de la comprobaci&oacute;n:</div></td>
				<td ><div align="left"><strong><?php echo $comprobacion_etapa_nombre;?></strong></div></td>
			<?}else{?>
				<td >&nbsp;</td>
				<td >&nbsp;</td>
			<?}?>
        </tr>
        <tr>
            <td ><div align="right">Fecha de invitaci&oacute;n:</div></td>
            <td ><div align="left"><strong><?php echo $co_fecha_invitacion ?></strong></div></td>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
        </tr>
        <tr>
            <td ><div align="right">Autorizador(es):</div></td>
            <td colspan="5"><div align="left"><strong><?php echo $autorizadores; ?></strong></div></td>
        </tr>
        <tr>
            <td colspan="5">&nbsp;</td>
        </tr>                
    </table>
<br />
<br />
<?php if($es_controlling_o_finanzas == 1 && $comprobacion_etapa != 7){?>
    <table id="" border="0" cellspacing="3" width="75%" style="border:0px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left;">
        <tr>
			<!--Se permite cambiar el centro de costoso cuando el usuario es de controlling o finanzas-->
			<?php 
			$cnn = new conexion();
			$idusuario=$_SESSION["idusuario"];
			$query = sprintf("SELECT cc.cc_id AS 'id',
					cc.cc_centrocostos AS 'cc',
					cc.cc_nombre AS 'nombre'
					FROM cat_cecos AS cc 
					WHERE cc_estatus = '1'
					AND cc_empresa_id = '".$_SESSION["empresa"]."' 
					ORDER BY cc.cc_centrocostos");
			$rst = $cnn->consultar($query);
			$fila = mysql_num_rows($rst);
			?>
			<td align="right">
				Centro de costos:
				<select name="centro_de_costos_new" id="centro_de_costos_new" onchange="activaAutorizar();">
					<option id="-1" value="-1">Seleccione...</option>
					<?php
						if($fila>0){
							while($fila=mysql_fetch_assoc($rst)){
								echo "<option id=".$fila["cc"]." value=".$fila["id"].">".$fila["cc"]."-".$fila["nombre"]."</option>";
							}
						}else{
							echo "<option id='n' value='n'>No hay centro de costos</option>";
						}
					?>
				</select>
				<input type="hidden" id="centro_de_costos_old" name="centro_de_costos_old" value="<?php echo $cc_id; ?>"/>
			</td>
        </tr>
	</table>	
<br />
<?}?>
<div align="center" style="color:#003366"><strong>Conceptos comprobados</strong></div>
<table id="descrip_comprobacion_table" class="tablesorter" cellspacing="1">
  <thead>
    <tr>
      <th width="3%">No.</th>
      <th width="10%">Tipo</th>
      <th width="10%">Fecha</th>
      <th width="10%">Concepto</th>
      <th width="10%">Comentario</th>
      <th width="5%">Asistentes</th>
	  <th>RFC</th>
      <th width="10%">Proveedor</th>
      <?php //if($tipoUsuario == 5){?>
      <!--<th width="7%">RFC</th>
      <th width="5%">Factura</th>-->
      <?php //}?>
      <th width="7%">Monto</th>
      <th width="5%">Divisa</th>
      <th width="7%">IVA</th>
      <th width="7%">Propina</th>
      <th width="7%">Total</th>   
	  <?if($tipoUsuario == 6 && $comprobacion_etapa != 7){?>
		<th width="10%">Reasignaci�n de concepto</th>
		<th width="28%">Recalcular</th>
		<th width="28%">Eliminar</th>   
	  <?}?>
    </tr>
  </thead>
  <tbody>
	<?php 
	$query = "SELECT co_tipo, dc_monto, dc_iva, dc_propinas, dc_proveedor, (SELECT pro_rfc FROM proveedores WHERE pro_id = dc_proveedor) AS rfc, 
			dci_monto_total_pesos, dc_concepto, dc_total_aprobado, dc_factura, dc_comentarios, 
			dc_rfc, dc_folio_factura, co_num_invitado, dc_total, dc_fecha, dc_id, 
			DATE_FORMAT(dc_fecha,'%d/%m/%Y') AS fecha_factura,
			DATE_FORMAT(dc_factura,'%d/%m/%Y') AS fecha_comp,
			(SELECT cp_concepto FROM cat_conceptosbmw WHERE dc_id = dc_concepto) AS concepto,
			(SELECT pro_proveedor FROM proveedores WHERE pro_id = dc_proveedor) AS proveedor, 
			(SELECT div_nombre FROM divisa WHERE div_id = dc_divisa) AS dc_divisa 
			FROM detalle_comprobacion_invitacion 
			INNER JOIN comprobacion_invitacion ON (co_id = dc_comprobacion)
			WHERE dc_comprobacion = '{$idComprobacion}'";
	//error_log($query);
	$rst = $cnn->consultar($query);
	$aux = array();
	while($datos=mysql_fetch_assoc($rst)){
		array_push($aux,$datos);
	}
	$i = 0;
	$Total = 0;
	foreach($aux as $datosAux){
		$i++;
		$iva = $datosAux['dc_iva'];
		$propina = $datosAux['dc_propinas'];
		?>
		<tr>
			<td align="center" valign="middle"><?php echo $i;?><input type="hidden" id="rows<?php echo $i;?>" name="rows<?php echo $i;?>" value="<?php echo $i;?>" readonly="readonly" size="5" />
			<input type="hidden" id="dc_id<?php echo $i;?>" name="dc_id<?php echo $i;?>" value="<?php echo $datosAux['dc_id'];?>" readonly="readonly" size="5" /></td>
			<td align="center" valign="middle"><?php if($datosAux['co_tipo'] == 1){echo "Amex";}else if($datosAux['co_tipo'] == 3){ echo "Reembolso";}else{ echo "";}?><input type="hidden" id="tipo_comp<?php echo $i;?>" name="tipo_comp<?php echo $i;?>" value="<?php if($datosAux['co_tipo'] == 1){echo "Amex";}else if($datosAux['co_tipo'] == 3){ echo "Reembolso";}else{ echo "";}?>" readonly="readonly" /></td>
			<td align="center" valign="middle"><?php echo $datosAux['fecha_comp'];?><input type="hidden" id="f_factura<?php echo $i;?>" name="f_factura<?php echo $i;?>" value="<?php echo $datosAux['fecha_comp'];?>" readonly="readonly" /></td>
			<td align="center" valign="middle"><div id="concepto_txt<?php echo $i; ?>" align="center"><?php echo $datosAux['concepto'];?></div><input type="hidden" id="concepto<?php echo $i;?>" name="concepto<?php echo $i;?>" value="<?php echo $datosAux['dc_concepto'];?>" readonly="readonly" /></td>
			<td align="center" valign="middle"><?php echo $datosAux['dc_comentarios'];?><input type="hidden" id="comentario<?php echo $i;?>" name="comentario<?php echo $i;?>" value="<?php echo $datosAux['dc_comentarios'];?>" readonly="readonly" /></td>
			<td align="center" valign="middle"><?php echo $datosAux['co_num_invitado'];?><input type="hidden" id="no_invitados<?php echo $i;?>" name="no_invitados<?php echo $i;?>" value="<?php echo $datosAux['co_num_invitado'];?>" /></td>
			<td><?php if($datosAux['rfc'] == ""){echo "N/A";}else{ echo $datosAux['rfc'];}?></td>
			<td align="center" valign="middle"><?php if($datosAux['proveedor'] == ""){echo "Sin proveedor";}else{ echo $datosAux['proveedor'];}?><input type="hidden" id="prov<?php echo $i;?>" name="prov<?php echo $i;?>" value="<?php echo $datosAux['proveedor'];?>" readonly="readonly" /></td>
			
			<td align="center" valign="middle"><?php if($tipoUsuario == 6 && $comprobacion_etapa != 7 && $datosAux['dc_concepto'] != 31){?>
			<input type="text" name="monto<?php echo $i;?>" id="monto<?php echo $i; ?>" value="<?php echo $datosAux["dc_monto"]; ?>"  size="10" onmouseover='changedFields(this.id);' onkeypress='activarRecalculo("monto",<?php echo $i;?>,0);' style='border-color:#FFFFFF; text-align:center; font-size:11px;' onkeyup='revisaCadena(this);' onchange='activarRecalculo("monto",<?php echo $i;?>,0);' onblur='changedFieldsOut(this.id);' onmouseout='mouseOut(this.id);' />
			<input type="hidden" id="dc_monto<?php echo $i;?>" name="dc_monto<?php echo $i;?>" value="<?php echo $datosAux['dc_monto'];?>" />
			<?php }else{ echo number_format($datosAux['dc_monto'],2,".",",");?><input type="hidden" id="dc_monto<?php echo $i;?>" name="dc_monto<?php echo $i;?>" value="<?php echo $datosAux['dc_monto'];?>" />
			<?php }?></td>
			
			<td align="center" valign="middle"><?php echo $datosAux['dc_divisa'];?><input type="hidden" id="divisa<?php echo $i;?>" name="divisa<?php echo $i;?>" value="<?php echo $datosAux['dc_divisa'];?>" /></td>
			
			<td align="center" valign="middle"><?php if($tipoUsuario == 6 && $comprobacion_etapa != 7 && $datosAux['dc_concepto'] != 31){?>
			<input type='text' name='iva<?php echo $i; ?>' id='iva<?php echo $i; ?>' value='<?php echo $iva; ?>'  size='10' onmouseover='changedFields(this.id);' onkeypress='activarRecalculo("iva",<?php echo $i;?>,0);' style='border-color:#FFFFFF; text-align:center; font-size:11px;' onkeyup='revisaCadena(this);' onchange='activarRecalculo("iva",<?php echo $i;?>,0);' onblur='changedFieldsOut(this.id);' onmouseout='mouseOut(this.id);' />
			<input type="hidden" id="dc_iva<?php echo $i;?>" name="dc_iva<?php echo $i;?>" value="<?php echo $datosAux['dc_iva'];?>" />
			<?php }else{ echo number_format($datosAux['dc_iva'],2,".",",");?><input type="hidden" id="dc_iva<?php echo $i;?>" name="dc_iva<?php echo $i;?>" value="<?php echo $datosAux['dc_iva'];?>" />
			<?php }?></td>
			
			<td align="center" valign="middle"><?php if($tipoUsuario == 6 && $comprobacion_etapa != 7 && $datosAux['dc_concepto'] != 31){?>
			<input type='text' name='propina<?php echo $i; ?>' id='propina<?php echo $i; ?>' value='<?php echo $propina; ?>'  size='10' onmouseover='changedFields(this.id);' onkeypress='activarRecalculo("propina",<?php echo $i;?>,0);' style='border-color:#FFFFFF; text-align:center; font-size:11px;' onkeyup='revisaCadena(this);' onchange='activarRecalculo("propina",<?php echo $i;?>,0);' onblur='changedFieldsOut(this.id);' onmouseout='mouseOut(this.id);' />
			<input type="hidden" id="dc_propinas<?php echo $i;?>" name="dc_propinas<?php echo $i;?>" value="<?php echo $datosAux['dc_propinas'];?>" />
			<?php }else{ echo number_format($datosAux['dc_propinas'],2,".",",");?><input type="hidden" id="dc_propinas<?php echo $i;?>" name="dc_propinas<?php echo $i;?>" value="<?php echo $datosAux['dc_propinas'];?>" />
			<?php }?></td>
			
			<td align="center" valign="middle"><div id="tota<?php echo $i; ?>" ><?php echo number_format($datosAux['dc_total'],2,".",",");?></div><input type="hidden" id="total<?php echo $i;?>" name="total<?php echo $i;?>" value="<?php echo $datosAux['dc_total'];?>" /></td>
			<?if($tipoUsuario == 6 && $comprobacion_etapa != 7){?>
			<!--<td><input name="co_total_aprob_x_fin_new" id="co_total_aprob_x_fin_new" value="<? //echo $dc_total_aprobado?>" size="10" onkeypress="return validaNum(event)" size="10" /></td>-->
			<td>
				<div id="div_conceptos">
					<select name="concepto_new<?php echo $i;?>" id="concepto_new<?php echo $i;?>" onchange="actualizarConcepto(this.value,<?php echo $i; ?>, 0);">
					<option value="-1">Sin reasignar</option>  
						<?php
						$query1 = sprintf("SELECT dc_id,cp_concepto FROM cat_conceptosbmw ORDER BY cp_concepto");
						$var1 = mysql_query($query1);

						while ($arr1 = mysql_fetch_assoc($var1)) {
							echo sprintf("<option value='%s'>%s</option>",$arr1['dc_id'],$arr1['cp_concepto']);

						}
						?>  
					</select>
					<input type="hidden" id="concepto_new_txt<?php echo $i;?>" name="concepto_new_txt<?php echo $i;?>" value="" />
				</div>
				<input type="hidden" id="concepto_old<?php echo $i;?>" name="concepto_old<?php echo $i;?>" value="<?php echo $datosAux['dc_concepto']; ?>"/>
				<input type="hidden" name="monto_old<?php echo $i;?>" id="monto_old<?php echo $i;?>" value="<?php echo $datosAux['dc_monto'];?>" />
				<input type="hidden" id="iva_old<?php echo $i;?>" name="iva_old<?php echo $i;?>" value="<?php echo $datosAux['dc_iva']; ?>"/>
				<input type="hidden" id="propina_old<?php echo $i;?>" name="propina_old<?php echo $i;?>" value="<?php echo $datosAux['dc_propinas']; ?>"/>
				<input type="hidden" id="fila_original<?php echo $i;?>" name="fila_original<?php echo $i;?>" value="<?php echo $i;?>"/>
				<input type="hidden" id="co_total_aprob_x_fin_old<?php echo $i;?>" name="co_total_aprob_x_fin_old<?php echo $i;?>" value="<?php echo $datosAux['dc_total_aprobado']; ?>"/>
			</td>
			<td align="center" valign="middle"><input  name='recall<?php echo $i; ?>' id='recall<?php echo $i; ?>'  value="N/A" size=10  disabled="disabled" style="text-align:center; border-color:#FFFFFF; "/><input type="button" name='recalculo<?php echo $i; ?>' id='recalculo<?php echo $i; ?>' style="width: 30px; height:30px; background:url(../../images/fwk/action_reorder.gif); background-position:center; background-repeat:no-repeat; border-style:none; cursor:pointer;" onclick="validarMonto(<?php echo $i;?>, <?php echo $datosAux['dc_id'];?>);"/></td>
			<td align="center" valign="middle">
			<?php if($datosAux["dc_concepto"] != 31){?>
				<input name='elimin<?php echo $i; ?>' id='elimin<?php echo $i; ?>'  value="N/A" size=10  disabled="disabled" style="text-align:center; border-color:#FFFFFF; "/><input type="button" name='eliminar<?php echo $i; ?>' id='eliminar<?php echo $i; ?>' style="width: 30px; height:30px; background:url(../../images/action_cancel.gif); background-position:center;  background-repeat:no-repeat; border-style:none; cursor:pointer;"/>
			<?php }else{?>
				<input type="button" name="<?php echo $i;?>del" id="<?php echo $i;?>del" style="width:30px; height:30px; background:url(../../images/action_cancel.gif); background-position:center; background-repeat:no-repeat; border-style:none; cursor:pointer;" onClick="eliminaConceptoSI(this.id);" />
			<?php }?>
			</td>
		  <?}?>
		</tr>
	<?php 
		$Total += $datosAux['dc_total'];
	}?>
  </tbody>
</table>
<input type="hidden" name="total_rows" id="total_rows" value="<?php echo ($i);?>" readonly="readonly"/>
<br />
<div align="center" style="color:#003366"><strong>Lista de invitados</strong></div>
<table id="invitados_table" class="tablesorter" cellspacing="1" style="width:75%">
	<thead> 
		<tr>
			<th width="7%">No.</th>
			<th width="28%">Nombre</th>
			<th width="28%">Puesto</th> 					
			<th width="28%">Empresa</th>
			<th width="9%">Tipo</th>
		</tr> 

		<?php 
			$cont=1;
			foreach($invitados as $datosAux){
		?>
			<tr>
				<td><?php echo $cont ?></td>
				<td><?php echo $datosAux["dci_nombre_invitado"] ?></td>
				<td><?php echo $datosAux["dci_puesto_invitado"] ?></td>
				<td><?php echo $datosAux["dci_empresa_invitado"]?></td>
				<td><?php echo $datosAux["dci_tipo_invitado"]?></td>
			</tr>
		<?php
			 $cont++;
			} 
		?>
	</thead> 
	<tbody>
		<!-- cuerpo tabla-->
	</tbody>
</table>
<br/>
<br/>
<table id="datosgrales" cellspacing="1" width="75%" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">
	<tr>
		<td colspan="5"><div align="center" style="color:#003366; display:none"><strong>Solicitud de invitaci&oacute;n</strong></div></td>
	</tr>
	<tr>
		<td colspan="5">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="5">
		<table width="100%">
		<tr>
			<td width="20%" align="right"><div align="right">Total de invitados: </div></td>
			<td width="20%"><div align="left"><strong><?php echo $co_num_invitados ?></strong></div></td>
			<td width="20%">&nbsp;</td>
			<td width="20%" align="right"><div align="right">Total monto solicitado:</div></td>
			<td width="20%"><div align="left"><strong><?php echo $total_solicitado." ".$divisa; ?></strong></div>
			<input type="hidden" readonly="readonly" name="anticipo_solicitado2" id="anticipo_solicitado2" value="<?php echo $dci_monto_total_pesos;?>" /></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td colspan="5">
		<table width="100%">
		<tr>
			<td width="20%" align="right"><div align="right">Ciudad:</div></td>
			<td width="20%"><div align="left"><strong><?php echo $ciudad ?></strong></div></td>
			<td width="20%">&nbsp;</td>
			<td width="20%" align="right"><div align="right">Total comprobado:</div></td>
			<td width="20%"><div align="left"><strong>
			<?php
			switch ($dci_divisa){
				case 1:
					$Total = number_format($Total,2,".",",");
					echo $Total." MXN";
					break;
				case 2:
					$Total = ($Total * $divisaUSD);
					$Total = number_format($Total,2,".",",");
					echo $Total." MXN";
					break;
				case 3:
					$Total = ($Total * $divisaEUR);
					$Total = number_format($Total,2,".",",");
					echo $Total." MXN";
					break;
			}?></strong></div>
			<input type='hidden' name='total_pesos' id='total_pesos' value='<?php echo $Total; ?>'/>
			<input type="hidden" name="total_comprobado" id="total_comprobado" value="<?php echo $Total;?>"/></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td >&nbsp;</td>
		<td colspan="3" align="left" valign="middle"><div><?php if(isset($_GET['edit_view']) && $co_hubo_excedente == 1){ echo "<font color='#FF0000'><b>Excede el monto l&iacute;mite de 50.00 EUR por persona.</b></font>"; }?></div></td>
		<td >&nbsp;</td>
	</tr>
	<tr>
		<td align="right" valign="top" width="20%"><div align="right">Historial de Observaciones:</div></td>
		<td colspan="2" rowspan="1" class="alignLeft" >
		<textarea name="historial_observaciones" id="historial_observaciones" <?php if($tipoUsuario != 6){ echo "cols='80'";}else{echo "cols='60'";}?> rows="5" readonly="readonly" onkeypress="confirmaRegreso('historial_observaciones');" onkeydown="confirmaRegreso('historial_observaciones');"><? echo $observaciones;?></textarea></td>
		<?php if($tipoUsuario != 6){?>
		<td >&nbsp;</td>
		<td >&nbsp;</td>
		<?php }else{?><!-- Colocamos la tabla flotante del resumen de los montos autorizados -->
		<td rowspan="4" align="center" valign="middle">
		<table style="border: 1px #CCCCCC solid; margin: auto;" cellspacing="1">
			<tr>
				<td colspan="2" style="color: #000000; text-align: center;"><h3>Resumen</h3></td>
 			</tr>
			<tr>
				<td><div id="g_saldo"></div><input type="hidden" readonly="readonly" name="t_saldo" id="t_saldo" value="0.00" /></td>
				<td><div id="g_sbt"></div><input type="hidden" readonly="readonly" name="t_subtotal" id="t_subtotal" value="0.00" /></td>
			</tr>
			<tr>
				<td>Amex comprobado:</td>
				<td><div id="g_amex_comprobado" align="right"><?php echo $co_amex_comprobado;?> MXN</div><input type="hidden" readonly="readonly" name="t_amex_comprobado" id="t_amex_comprobado" value="<?php echo $co_amex_comprobado;?>" /></td>
			</tr>
			<tr>
				<td>Efectivo comprobado:</td>
				<td><div id="g_efectivo_comprobado" align="right"><?php echo $co_efectivo_comprobado;?> MXN</div><input type="hidden" readonly="readonly" name="t_efectivo_comprobado" id="t_efectivo_comprobado" value="<?php echo $co_efectivo_comprobado;?>" /></td>
			</tr>
			<tr>
				<td>Personal a descontar:</td>
				<td><div id="g_personal" align="right"><?php echo $co_personal_a_descontar;?> MXN</div><input type="hidden" readonly="readonly" name="t_personal" id="t_personal" value="<?php echo $co_personal_a_descontar;?>" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="color: #DF0101">Descuento:</td>
				<td style="color: #DF0101"><div id="g_descuento" align="right"><?php echo $co_descuento;?> MXN</div><input type="hidden" readonly="readonly" name="t_descuento" id="t_descuento" value="<?php echo $co_descuento;?>" /></td>
			</tr>
			<tr>
				<td style="color: #DF0101">Reembolso:</td>
				<td style="color: #DF0101"><div id="g_reembolso" align="right"><?php echo $co_monto_a_reembolsar;?> MXN</div><input type="hidden" readonly="readonly" name="t_reembolso" id="t_reembolso" value="<?php echo $co_monto_a_reembolsar;?>" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		</td>
		<?php }?>
	</tr>
	<?if((isset($_GET['edit_view']) && ($comprobacion_etapa == COMPROBACION_INVITACION_ETAPA_APROBACION || $comprobacion_etapa == COMPROBACION_INVITACION_ETAPA_RECHAZADA_POR_SUPERVISOR_FINANZAS || $comprobacion_etapa == COMPROBACION_INVITACION_ETAPA_APROBADA_POR_SUPERVISOR_FINANZAS)) || isset($_GET['view']) && $comprobacion_etapa == COMPROBACION_INVITACION_ETAPA_EN_APROBACION_POR_DIRECTOR && !isset($_SESSION['iddelegado'])){?>
	<tr>
		<?php if($tipoUsuario != 6){?>
		<td colspan="5" align="center" valign="middle">&nbsp;</td>
		<?php }else{?>
			<td colspan="4" align="center" valign="middle">&nbsp;</td>
		<?php }?>
	</tr>
	<tr>
		<td valign="top"><div align="right" >Observaciones:</div></td>
		<td colspan="2" class="alignLeft" valign="top"> <textarea name="observ_up" <?php if($tipoUsuario != 6){ echo "cols='80'";}else{echo "cols='60'";}?> rows="5" id="observ_up" ></textarea></td>
		<?php if($tipoUsuario != 6){?>
		<td align="center" valign="middle">&nbsp;</td>
		<td align="center" valign="middle">&nbsp;</td>
		<?php }?>
	</tr>
	<tr>
		<?php if($tipoUsuario != 6){?>
		<td colspan="5" align="center" valign="middle">&nbsp;</td>
		<?php }else{?>
			<td colspan="4" align="center" valign="middle">&nbsp;</td>
		<?php }?>
	</tr>
	<?}?>
	<tr>
		<td colspan="5">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="5"><br />
		  <div align="center">
		  <?php 
		  $idTramite=$_GET['id'];
		  $tramites = new Tramite();
		  $tramites->Load_Tramite($idTramite);
		  $t_iniciador=$tramites->Get_dato("t_iniciador");
		  $t_delegado = $tramites->Get_dato("t_delegado");
		  $t_dueno=$tramites->Get_dato("t_dueno");
		  $t_ruta_autorizacion = $tramites->Get_dato("t_ruta_autorizacion");
		  if(isset($_SESSION['iddelegado'])){
		  	$usuario = $_SESSION['iddelegado']; 
		  }else{
		  	$usuario = $_SESSION["idusuario"];
		  }
		  
		  $encontrado = encuentraAutorizador($t_ruta_autorizacion, $usuario);
		  
		  $privilegios = 0;
		  if(isset($_SESSION['iddelegado'])){
		  	$query = sprintf("SELECT privilegios FROM usuarios_asignados WHERE id_asignador = '%s' AND id_delegado = '%s'", $_SESSION["idusuario"], $_SESSION['iddelegado']);
		  	//echo $query."<br />";
		  	$rst = $cnn->consultar($query);
		  	$fila = mysql_fetch_assoc($rst);
		  	$privilegios = $fila["privilegios"];
		  	//error_log($query);
		  }
		  
// 		  echo "Pintando encontrado: ".$encontrado."<br />";
// 		  echo "Pintando privilegios: ".$privilegios."<br />";
// 		  echo "Pintando delegado: ".$_SESSION['iddelegado']."<br />";
		  
		  if(isset($_GET['edit_view'])){?>
		  <?php if($tipoUsuario != 6 || $tipoUsuario != 5){
				if((isset($_SESSION['iddelegado']) && $privilegios == 1 && $encontrado == 1)  || (!isset($_SESSION['iddelegado']) && $privilegios == 0)){ // Si la Sesi�n del delegado se ha iniciado, y el asignador tiene privilegios de autorizar, se deber�n mostrar los botones de Autorizar y Rechazar ?>
					<input type="submit" value="<?php if($comprobacion_etapa == COMPROBACION_INVITACION_ETAPA_APROBADA_POR_SUPERVISOR_FINANZAS){ echo "    Contabilizar";}else{ echo "    Autorizar";}?>" id="autorizar_comp_inv" name="autorizar_comp_inv"  style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; visibility:hidden" />
			<?php }?>			
		<?php }?>
		  <? if($tipoUsuario == 6 && $comprobacion_etapa != COMPROBACION_INVITACION_ETAPA_APROBADA_POR_SUPERVISOR_FINANZAS){?>
			<input type="submit" value="    Enviar a supervisor" id="envia_supervisor" name="envia_supervisor"  style="background:url(../../images/Arrow_Right.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
		  <?php }?>
			<input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>
		<?php if((isset($_SESSION['iddelegado']) && $privilegios == 1 && $encontrado == 1) || (!isset($_SESSION['iddelegado']) && $privilegios == 0)){ // Si la Sesi�n del delegado se ha iniciado, y el asignador tiene privilegios de autorizar, se deber�n mostrar los botones de Autorizar y Rechazar ?>
			<input type="submit" value="    Rechazar" id="rechazar_comp_inv" name="rechazar_comp_inv" style="background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC; visibility:hidden"/>
		<?php }?>
		  <?if($tipoUsuario == 6 && $comprobacion_etapa != COMPROBACION_INVITACION_ETAPA_APROBADA_POR_SUPERVISOR_FINANZAS){?>
		  <input type="submit" value="    Devolver con observaciones" id="devolver_observ" name="devolver_observ"  style="background:url(../../images/Arrow_Left.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="return validaObservaciones();" />
		  <?php }
			}else if(isset($_GET['view'])&& $comprobacion_etapa == COMPROBACION_INVITACION_ETAPA_EN_APROBACION_POR_DIRECTOR && !isset($_SESSION['iddelegado'])){?>
				<input type="submit" value="    Aprobar" id="aprobar_ci" name="aprobar_ci"  style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" />
				<input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>
				<input type="submit" value="    Rechazar" id="rechazar_ci" name="rechazar_ci" style="background:url(../../images/reject.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>
			<?php }else{?>
				<input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>
			<?php } ?>
			<input type="hidden" id="idT" name="idT" value="<?php if(isset($_GET['id'])){echo $_GET['id'];}else{echo $_GET['ID'];} ?>" />
			<input type="hidden" name="iu" id="iu" readonly="readonly" value="<? echo $_SESSION["idusuario"];  ?>" />
			<input type="hidden" name="ne" id="ne" readonly="readonly" value="<? //echo $_SESSION["idusuario"];  ?>" />
			<input type="hidden" id="cont_invitados" name="cont_invitados" value="<?php echo $cont?>"/>
			<input type="hidden" id="comprobacionID" name="comprobacionID" value="<?php echo $idComprobacion;?>"/>
			<input type="hidden" name="delegado" id="delegado" readonly="readonly" value="<?php if(isset($_SESSION['iddelegado'])){ echo $_SESSION['iddelegado']; }else{echo 0;}?>" />
			<input type="hidden" name="delegadoNombre" id="delegadoNombre" readonly="readonly" value="<?php if(isset($_SESSION['iddelegado'])){ echo $_SESSION['delegado']; }else{echo 0;}?>" />
			<input type="hidden" id="tasaUSD" name="tasaUSD" value="<?php echo $divisaUSD?>" readonly="readonly" />
			<input type="hidden" id="tasaEUR" name="tasaEUR" value="<?php echo $divisaEUR?>" readonly="readonly"/>
			<input type="hidden" id="divisaComp" name="divisaComp" value="<?php echo $dci_divisa?>" readonly="readonly"/>
			<input type="hidden" id="tipo_comp" name="tipo_comp" value="<?php echo $co_tipo?>" readonly="readonly" />
			<input type="hidden" id="t_etapa_actual" name="t_etapa_actual" value="<?php echo $comprobacion_etapa;?>" readonly="readonly" />
		  </div>
	  </td>
	</tr>
	<tr>
		<td colspan="5">&nbsp;</td>
	</tr>
</table>

<?if($t_iniciador == $_SESSION["idusuario"] || (isset($_SESSION['iddelegado']))){?>
<?if(($t_owner == "1000" || $t_owner == "2000") || ($comprobacion_etapa == COMPROBACION_INVITACION_ETAPA_APROBADA || $comprobacion_etapa >= COMPROBACION_INVITACION_ETAPA_DEVUELTA_CON_OBSERVACIONES && $comprobacion_etapa < COMPROBACION_INVITACION_ETAPA_EN_APROBACION_POR_DIRECTOR)){?>
	<table border="0">
		<tr>
			<td colspan="10"><br />
			  <div align="center"> 
				<input type="button" value="    Imprimir"  name="imprimir" id="imprimir" style="background:url(../../images/icon_Imprimir.gif); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="imprimir_pdf(<?php if(isset($_GET['id'])){echo $_GET['id'];}else{echo $_GET['ID'];} ?>)" />
			  </div>
			</td>				 
		</tr>
	</table>
<?}
	}?>

<table id="totales" width="90%" border="0" style="display:none">
    <tr>
        <td width=85%>&nbsp;</td>
        <td><div align="right">Subtotal:</div></td>
        <td><div align="right">$<?php  echo $subtotal_comp;?></div></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><div align="right">IVA:</div></td>        
        <td><div align="right">$<?php  echo $iva_comp;?></div></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><div align="right">Total:</div></td>        
        <td><div align="right">$<?php  echo $total_comp;?></div></td>
    </tr>    
    <tr>
        <td>&nbsp;</td>
        <td><div align="right">Aprobado:</div></td>        
        <td><div align="right">$<?php  echo $co_total_aprobado;?></div></td>
    </tr>    
    <tr>
        <td>&nbsp;</td>
        <td><div align="right">Pendiente:</div></td>        
        <td><div align="right">$<?php  echo $co_pendiente;?></div></td>
    </tr>    
</table>

<table border="0" width='60%' style="display:none">
    <tr>
        <td rowspan="4" align='right' valign="top" width="5">Observaciones:</td>
        <td width="30%" colspan="2" rowspan="4" class="alignLeft" valign="top">
           <textarea name="observ" cols="70" rows="5" id="observ" readonly="readonly"><?echo $observaciones?></textarea>
        </td>
        <td width="15%"></td>
    </tr>
</table>

 <table border="0" style="display:none">
    <tr>
        <td colspan="10"><br />
          <div align="center"> 
            <input type="button" value="    Volver"  name="Volver" id="Volver" style="background:url(../../images/back.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" onclick="Location()"/>
          </div>
      </td>				 
    </tr>
</table>
</form>
</div>
   <?php
    //echo ('<script language="JavaScript" type="text/javascript">searchSolicitud_amex();</script>');
   $tipoUsuario = $_SESSION["perfil"];
		if(isset($_GET['edit_view'])){
			if($tipoUsuario == 6 || $tipoUsuario == 5){
				if($comprobacion_etapa == 7 && $tipoUsuario == 6){
		            echo "<script language='javascript'>
					document.getElementById('autorizar_comp_inv').style.visibility = 'visible';
					document.getElementById('rechazar_comp_inv').style.visibility = 'visible';
					</script>";
				}else if($comprobacion_etapa != 7 && $tipoUsuario == 5){
		            echo "<script language='javascript'>
					document.getElementById('autorizar_comp_inv').style.visibility = 'visible';
					document.getElementById('rechazar_comp_inv').style.visibility = 'visible';
					</script>";
				}else{
					echo "<script language='javascript'>
					document.getElementById('rechazar_comp_inv').style.visibility = 'visible';
					</script>";
				}	            
			}else{
				echo "<script language='javascript'>
				document.getElementById('autorizar_comp_inv').style.visibility = 'visible';
				document.getElementById('rechazar_comp_inv').style.visibility = 'visible';
				document.getElementById('imprimir').style.display = 'none';
				</script>";				
			}
		}
}else{
	echo "Error en parametros.";
}
   ?>