<?php

session_start();
require_once("./lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";

	$usuario	= $_POST["user"];
	$passwd		= $_POST["passwd"];
	$hidTramite = $_POST["hidTramite"];
	$notification_by_email = $_POST['mail'];
	
	$Empresa	= new Empresa();
	$U			= new Usuario();
	$T			= new Tramite();

	$tramite_aux = new Tramite();
	
	if($U->Valida($usuario,$passwd)==false){
		if($hidTramite != 0){
			header("Location: ./index.php?error&id=".$hidTramite);
		}else{
			header("Location: ./index.php?error");
		}
		return	(false);
		
	}else{
		
		$id_empresa	=$U->Get_dato("u_empresa");
		$Empresa->Load_Empresa($id_empresa);
		
		$_SESSION["idusuario"]		= $U->Get_dato("u_id");
		$_SESSION["usuario"]		= $U->Get_dato("u_paterno") . " " . $U->Get_dato("u_materno") . " " . $U->Get_dato("u_nombre");
		$_SESSION["empresa"]		= $id_empresa;
        $_SESSION["nombreempresa"]	= $Empresa->Get_dato("e_codigo") . " - " . $Empresa->Get_dato("e_nombre");
		
		if($U->find_tipos($U->Get_dato("u_id")) && $notification_by_email == 0){
			header("Location: accesotipo.php");
		}else if($U->find_delegaciones($U->Get_dato("u_id")) && $notification_by_email == 0){
			header("Location: ./flujos/delegacion/delegaciones.php");
		}else if ($hidTramite!=null && $notification_by_email != 0){
			
			$tramite_aux->Load_Tramite($hidTramite);
			$t_flujo = $tramite_aux->Get_dato("t_flujo");
			$etapa_tramite = $tramite_aux->Get_dato("t_etapa_actual");
			error_log("Flujo del tramite: ".$t_flujo);
			error_log("Etapa del tramite: ".$etapa_tramite);
			
			if($t_flujo == ""){
				header("Location: inicial.php?error");
			}
			
			// Perfil de Agencia para Solicitudes de Viaje
			if($t_flujo == FLUJO_SOLICITUD && $etapa_tramite == SOLICITUD_ETAPA_AGENCIA){
				$_SESSION["perfil"] = "4";
			} // Perfil de Autorizador para Solicitudes de Viaje
			else if($t_flujo == FLUJO_SOLICITUD && $etapa_tramite == SOLICITUD_ETAPA_EN_APROBACION){
				$_SESSION["perfil"] = "1";
			} // Perfil de Autorizador para Comprobaciones de Viaje, Solicitudes y comprobaciones de Invitación
			else{
				$_SESSION["perfil"] = "1";
			}
			//Inicio de variable de perfil si es que el usuario no tiene más de una sola asignación de tipo de usuario.
			//error_log("UT_TIPO???".$_SESSION["perfil"]);
			
			$query = "";
			if($t_flujo == FLUJO_SOLICITUD_GASTOS){
				$query="SELECT t_id, t_etiqueta, DATE(t_fecha_registro), (SELECT et_etapa_nombre FROM etapas WHERE et_etapa_id = t_etapa_actual AND et_flujo_id = t_flujo ),  
					sg_monto, sg_divisa FROM tramites INNER JOIN solicitud_gastos ON (sg_tramite = tramites.t_id) 
					WHERE t_dueno = '".$_SESSION["idusuario"]."' AND t_flujo = '".FLUJO_SOLICITUD_GASTOS."' AND (t_etapa_actual = '".SOLICITUD_GASTOS_ETAPA_APROBACION."' OR t_etapa_actual = '".SOLICITUD_GASTOS_ETAPA_EN_APROBACION_POR_DIRECTOR."') AND sg_tramite = '".$hidTramite."' ORDER BY t_id DESC";
			}else if($t_flujo == FLUJO_SOLICITUD){
				$t_dueno = $_SESSION["idusuario"];
				if($etapa_tramite == SOLICITUD_ETAPA_AGENCIA){
					$agrup_usu = new AgrupacionUsuarios();
					$agrup_usu->Load_Homologacion_Dueno_By_u_ID($t_dueno);
					$t_dueno = $agrup_usu->Get_dato("hd_au_id");
				}
				$query="SELECT t_id, t_etiqueta, DATE(t_fecha_registro), (SELECT et_etapa_nombre FROM etapas WHERE et_etapa_id = t_etapa_actual AND et_flujo_id = t_flujo ),  
					sv_total FROM tramites INNER JOIN solicitud_viaje ON (solicitud_viaje.sv_tramite=tramites.t_id) 
					WHERE t_dueno = '".$t_dueno."' AND t_flujo = '".FLUJO_SOLICITUD."' AND (t_etapa_actual = '".SOLICITUD_ETAPA_AGENCIA."' OR t_etapa_actual = '".SOLICITUD_ETAPA_EN_APROBACION."' OR t_etapa_actual = '".SOLICITUD_ETAPA_EN_APROBACION_POR_DIRECTOR."') AND sv_tramite = '".$hidTramite." ' ORDER BY t_id DESC";
			}else if($t_flujo == FLUJO_COMPROBACION){
				$query="SELECT t_id, t_etiqueta, DATE(t_fecha_registro), (SELECT et_etapa_nombre FROM etapas WHERE et_etapa_id = t_etapa_actual AND et_flujo_id = t_flujo ) 
					FROM tramites INNER JOIN comprobaciones ON (comprobaciones.co_mi_tramite = tramites.t_id)
					WHERE t_dueno = '".$_SESSION["idusuario"]."' AND t_flujo = '".FLUJO_COMPROBACION."' AND (t_etapa_actual = '".COMPROBACION_ETAPA_EN_APROBACION."' OR t_etapa_actual = '".COMPROBACION_ETAPA_EN_APROBACION_POR_DIRECTOR."') AND co_mi_tramite = '".$hidTramite."' ORDER BY t_id DESC";
			}else if($t_flujo == FLUJO_COMPROBACION_INVITACION){
				$query="SELECT t_id, t_etiqueta, DATE(t_fecha_registro), (SELECT et_etapa_nombre FROM etapas WHERE et_etapa_id = t_etapa_actual AND et_flujo_id = t_flujo )  
					FROM tramites INNER JOIN comprobacion_invitacion ON (comprobacion_invitacion.co_mi_tramite=tramites.t_id) 
					WHERE t_dueno = '".$_SESSION["idusuario"]."' AND t_flujo = '".FLUJO_COMPROBACION_INVITACION."' AND (t_etapa_actual = '".COMPROBACION_INVITACION_ETAPA_APROBACION."' OR t_etapa_actual = '".COMPROBACION_INVITACION_ETAPA_EN_APROBACION_POR_DIRECTOR."') AND co_mi_tramite = '".$hidTramite."' ORDER BY t_id DESC";
			}
			//error_log("Query: ".$query);			
			error_log("tr ".$T->validaTramiteActivo($query)." hid: ".$hidTramite);
			
			if ($T->validaTramiteActivo($query)!= ($hidTramite) ){
				if($t_flujo == FLUJO_SOLICITUD_GASTOS){
					header("Location: ./flujos/solicitudes/index.php?docs=docs&type=2");
				}else if($t_flujo == FLUJO_SOLICITUD){
					if($etapa_tramite == SOLICITUD_ETAPA_AGENCIA){
						header("Location: ./flujos/notificaciones/index.php");
					}else{
						header("Location: ./flujos/solicitudes/index.php?docs=docs&type=1");
					}
				}else if($t_flujo == FLUJO_COMPROBACION_INVITACION){
					header("Location: ./flujos/comprobaciones/index.php?docs=docs&type=4");
				}else if($t_flujo == FLUJO_COMPROBACION){
					header("Location: ./flujos/comprobaciones/index.php?docs=docs&type=1");
				}
			}else{
				if ($hidTramite!=""){
					if($t_flujo == FLUJO_SOLICITUD_GASTOS || $t_flujo == FLUJO_SOLICITUD){
						if($etapa_tramite == SOLICITUD_GASTOS_ETAPA_EN_APROBACION_POR_DIRECTOR){
							header("Location: ./flujos/solicitudes/index.php?view=view&id=".$hidTramite);
						}else{
							header("Location: ./flujos/solicitudes/index.php?edit_view=edit_view&id=".$hidTramite);
						}
					}else if($t_flujo == FLUJO_COMPROBACION_INVITACION){
						if($etapa_tramite == COMPROBACION_INVITACION_ETAPA_EN_APROBACION_POR_DIRECTOR){
							header("Location: ./flujos/comprobaciones/index.php?view=view&id=".$hidTramite);
						}else{
							header("Location: ./flujos/comprobaciones/index.php?edit_view=edit_view&id=".$hidTramite);
						}
					}else if($t_flujo == FLUJO_COMPROBACION){
						if($etapa_tramite == COMPROBACION_ETAPA_EN_APROBACION_POR_DIRECTOR){
							header("Location: ./flujos/comprobaciones/index.php?view_n=view_n&id=".$hidTramite);
						}else{
							header("Location: ./flujos/comprobaciones/index.php?view_travel=view_travel&id=".$hidTramite);
						}
					}
				}else{
					header("Location: ./inicial.php");
				}
			}
		}
		else{
			//error_log("Usuario ID: ".$_SESSION["idusuario"]);
			$_SESSION['perfil'] = $U->Load_tipo_usuario_unico_id($_SESSION["idusuario"]);
			//error_log("PERFIL!: ".$_SESSION['perfil']);
			header("Location: ./inicial.php");
			//echo "1";	
		}
	}