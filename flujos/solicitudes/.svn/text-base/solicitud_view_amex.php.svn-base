<?php
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
require_once("$RUTA_A/flujos/solicitudes/services/C_SV.php");
   
//
//     Autoriza la solicitud (ANTICIPO_ETAPA_APROBADA)
//
 if(isset($_POST['autorizar']) && isset($_POST['idT']) && $_POST['idT']!="" && isset($_POST['iu']) && $_POST['iu']!=""){

    // Datos del tramite
    $sObser=$_POST['observ'];
    $idTramite=$_POST['idT'];
    
    //Datos usuario
    $iduser=$_POST['iu'];
    $numempleado=$_POST['ne'];
        
    // Seleccionar el monto a autorizar y lo guarda...
    $cnn = new conexion();
    
    // Carga los datos de la solicitud
    $Csv=new C_SV();
    $Csv->Load_Solicitud_Amex_Tramite($idTramite);
    $Csv->Modifica_Observaciones($idTramite, $sObser, FLUJO_AMEX);    
    $idceco=$Csv->Get_dato("sa_ceco_paga");
    $sv_fecha_viaje=$Csv->Get_dato("sa_fecha_viaje");
    $sa_anticipo=$Csv->Get_dato("sa_anticipo");
    
    // Envia el tramite a aprobacion
    $tramite = new Tramite();
    $tramite->Load_Tramite($idTramite);
    $iniciador = $tramite->Get_dato("t_iniciador");
    $aprobador = $tramite->Get_dato("t_dueno");
    $t_flujo   = $tramite->Get_dato("t_flujo");
    $siguiente_autorizador = $tramite->Get_Siguiente_Autorizador($idTramite);   
    
    // Busca el usuario agencia para que le podamos notificar
    $Us=new Usuario();
    $agencia = $Us->buscaAgenciaViajesParaSolicitud($idceco); 
    
    error_log("siguiente_autorizador=".$siguiente_autorizador);
    error_log("agencia=".$agencia);
        
    if($siguiente_autorizador!="-1"){
                
        $usuarioAprobador = new Usuario();
        $usuarioAprobador->Load_Usuario_By_ID($aprobador); 
        $usuarioAprobadorAdicional = new Usuario();
        $usuarioAprobadorAdicional->Load_Usuario_By_ID($siguiente_autorizador); 

        $mensaje = sprintf("La solicitud <strong>%05s</strong> ha sido <strong>APROBADA</strong> por <strong>%s</strong> y enviada a <strong>%s</strong> para su aprobaci&oacute;n adicional.",
                $idTramite, $usuarioAprobador->Get_dato('nombre'), $usuarioAprobadorAdicional->Get_dato('nombre'));            
        $tramite->Modifica_Etapa($idTramite, ANTICIPO_AMEX_ETAPA_APROBACION, FLUJO_AMEX, $siguiente_autorizador);
        $tramite->AgregaSiguienteAutorizador($idTramite, $aprobador);
        
        // Manda el mensaje a las 3 partes de la transaccion
        $tramite->EnviaNotificacion($idTramite, $mensaje, $iniciador, $aprobador); 
        $tramite->EnviaNotificacion($idTramite, $mensaje, $iniciador, $siguiente_autorizador); 
        $tramite->EnviaNotificacion($idTramite, $mensaje, $iniciador, $iniciador);
                        
    } else {
        
        // Actualiza el presupuesto
        $Cc=new CentroCosto();
        $Cc->resta_presupuesto($idceco, (float)$sa_anticipo, $sv_fecha_viaje);        
        
        // Este es el caso cuando no hay aprobador adicional
        $usuarioAprobador = new Usuario();
        $usuarioAprobador->Load_Usuario_By_ID($aprobador);   
        $mensaje = sprintf("La solicitud <strong>%05s</strong> ha sido <strong>APROBADA</strong> por <strong>%s</strong>.",
                                $idTramite, $usuarioAprobador->Get_dato('nombre'));
        //$tramite->EnviaMensaje($idTramite, $mensaje); // Notese que el mensaje se envia antes que se cambia la etapa
        
        // Manda el mensaje a las 3 partes de la transaccion
        $tramite->EnviaNotificacion($idTramite, $mensaje, $iniciador, $aprobador); 
        $tramite->EnviaNotificacion($idTramite, $mensaje, $iniciador, $agencia); 
        $tramite->EnviaNotificacion($idTramite, $mensaje, $iniciador, $iniciador);
        
        $tramite->Modifica_Etapa($idTramite, ANTICIPO_AMEX_ETAPA_APROBADA, FLUJO_AMEX, $iniciador);       
        $tramite->AgregaSiguienteAutorizador($idTramite, $aprobador);        
    }
    
    // Se cambia el estatus de ERP a autorizado = 1
    $VSql = "UPDATE solicitud_amex SET sa_status_erp = $ESTATUS_ERP_AUTORIZADO WHERE sa_tramite = '$idTramite';";
    $Rvsql = $cnn->insertar($VSql);
    
    $VSql = "UPDATE tramites SET t_fecha_cierre = NOW();";
    $Rvsql = $cnn->insertar($VSql);

    // Regresa a la pagina de solicitudes
    header("Location: ./index.php?action=autorizar");
    
//
//     Rechaza la solicitud (ANTICIPO_ETAPA_RECHAZADA)
//    
} else if(isset($_POST['rechazar']) && isset($_POST['idT']) && $_POST['idT']!=""){
		
    // Datos del tramite
    $sObser=$_POST['observ'];
    $idTramite=$_POST['idT'];
    
    // Actualiza el campo de observaciones
    $Csv=new C_SV();    
    $Csv->Load_Solicitud_Amex_Tramite($idTramite);
    $Csv->Modifica_Observaciones($idTramite, $sObser, FLUJO_AMEX);    
    
    // Regresa el monto apartado al ceco
    $idceco = $Csv->Get_dato("sa_ceco_paga");
    $monto = $Csv->Get_dato("sa_anticipo");
    $fecha = $Csv->Get_dato("sa_fecha_viaje");
    $Cc=new CentroCosto();
    $Cc->regresa_monto($idceco, $monto, $fecha);

    // Envia el tramite a cancelacion
    $tramite = new Tramite();
    $tramite->Load_Tramite($idTramite);    
    $iniciador = $tramite->Get_dato("t_iniciador");
    $aprobador = $tramite->Get_dato("t_dueno");
    $usuarioAprobador = new Usuario();
    $usuarioAprobador->Load_Usuario_By_ID($aprobador);   
    
    // Busca el usuario agencia para que le podamos notificar
    $Us=new Usuario();
    $agencia = $Us->buscaAgenciaViajesParaSolicitud($idceco);     
    
    $mensaje = sprintf("La solicitud <strong>AMEX %05s</strong> ha sido <strong>CANCELADA</strong> por <strong>%s</strong>.",
                            $idTramite, $usuarioAprobador->Get_dato('nombre'));
                            
    // Manda el mensaje a las 3 partes de la transaccion
    $tramite->EnviaNotificacion($idTramite, $mensaje, $iniciador, $aprobador); 
    $tramite->EnviaNotificacion($idTramite, $mensaje, $iniciador, $agencia); 
    $tramite->EnviaNotificacion($idTramite, $mensaje, $iniciador, $iniciador);                            
                            
    //$tramite->EnviaMensaje($idTramite, $mensaje); // Notese que el mensaje se envia antes que se cambia la etapa                            
    $tramite->Modifica_Etapa($idTramite, ANTICIPO_AMEX_ETAPA_RECHAZADA, FLUJO_AMEX, $iniciador);
    
    
    // Regresa a la pagina de solicitudes    
    header("Location: ./index.php?action=rechazar");

//
//     Muestra la pantalla de Autorizacion
// 
} else if(isset($_GET['id']) && $_GET['id']!="" && isset( $_GET['view']) || isset( $_GET['edit_view'] )){
				
    $empleado = $_SESSION["idusuario"];
    $cnn = new conexion();
    $aux = array();
    $query="SELECT t_flujo from tramites where t_id={$_GET['id']} ";
    $rst=$cnn->consultar($query);
    $datos=mysql_fetch_assoc($rst);
    if($datos["t_flujo"]==FLUJO_AMEX) {
        require_once("solicitud_view_edit_amex.php");            
    } elseif($datos["t_flujo"]==FLUJO_ANTICIPO){
        require_once("solicitud_view_edit_viaje.php");
    }

?>
	</html>
<?php		
	}
	else{
		echo "<font color='#FF0000'><b>Se encontr&oacute; error en el tr&aacute;mite. Verifique e intente de nuevo.</b></font>";
	}	

?>
