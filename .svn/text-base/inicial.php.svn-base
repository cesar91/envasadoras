<?php
session_start();
require_once("./lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";
//require_once('./lib/php/mobile_device_detect.php');

// Detecta si es un dispositivo movil (IPhone, Android, Blackberry)
// $mobile_type = null;
// $mobile = mobile_device_detect(true,true,true,true,true,true,false,false,&$mobile_type);
$mobile=false;

// Se usa en el cliente movil asi como en solicitudes/index.php (se deberia mover a una lib)
function imprime_mensajes(){
	require_once("./lib/php/constantes.php");
	
    //Mensajes 
    if(isset($_GET["oksave"])){ 
        echo "<h3>La solicitud se guard&oacute; correctamente, pasa a la siguiente etapa para su aprobaci&oacute;n.</h3>";
    } 
    
    if(isset($_GET["oksaveG"])){
        echo "<h3>La solicitud se guard&oacute; correctamente, pasa a la siguiente etapa para su aprobaci&oacute;n.</h3>";
    } 
    
    if(isset($_GET["errsave"])){ 
        echo "<font color='#FF0000'><b>Se encontraron errores, la solicitud no se ha guardado. Verifique e intente de nuevo.</b></font>";
    } 
    
    if(isset($_GET["erramnt"]) ){ 
        echo "<font color='#FF0000'><b>Se encontraron errores en los montos, la solicitud no se ha guardado. Verifique e intente de nuevo.</b></font>";
    } 
    
    if(isset($_GET["ccNegative"])){ 
        echo "<font color='#FF0000'><b>No se guardo la solicitud ya que no se localiza presupuesto del periodo actual para el centro de costo seleccionado, verifique con su administrador</b></font>";		
    } 
    
    if(isset($_GET["action"]) ){ 
        
        if($_GET["action"]=="autorizar" ){ 
            echo "<font color='#FF0000'><b>La solicitud se ha aprobado.</b></font>";
        }
        
        if($_GET["action"]=="devolver" ){ 
            echo "<font color='#FF0000'><b>La solicitud ha sido devuelta al empleado.</b></font>";
        } 
        
        if($_GET["action"]=="cancel" ){ 
            echo "<font color='#FF0000'><b>La solicitud ha sido cancelada.</b></font>";
        } 
        
        if($_GET["action"]=="rechazar" ){ 
            echo "<font color='#FF0000'><b>La solicitud fue rechazada.</b></font>";
        } 
    }
        
    if(isset($_GET["erramnt"]) ){ 
        echo "<font color='#FF0000'><b>Se encontraron errores en los montos, la solicitud no se ha guardado. Verifique e intente de nuevo.</b></font>";
    }
    
    if(isset($_GET["errjefe"])){ 
        echo "<font color='#FF0000'><b>No tiene asignado autorizador para su solicitud.</b></font>";
    }
    
    if(isset($_GET["error"]) ){
    	echo "<img src='./images/error.png' width='22' height='22' /> <font color='#FF0000'><b>La Solicitud/Comprobaci&oacute;n no existe &oacute; ha sido eliminada por el usuario.</b></font><br /><br />";
    }
    
}

if($mobile==true){
    
    //
    //  Muestra la pantalla de bienvenida al sistema (MOVIL)
    //
    $U	= new Usuario();
    $U->Load_Usuario_By_ID($_SESSION["idusuario"]);
    $I			= new InterfazMovil("Inicial");
    $usuario	= $_SESSION["usuario"];
    $empleado	= $_SESSION["idusuario"];
    
    imprime_mensajes();
        
    //
    //  Muestra las solicitudes pendientes de mi autorizacion
    //       
    echo "<br><br>";
    echo "<h1>Solicitudes a Aprobar</h1>";
    echo "<form action='index.php' method='post' name='comprobacionesaprobs'>";

    $L	= new Lista();
    $L->Cabeceras("Folio");
    $L->Cabeceras("Referencia");
    $L->Cabeceras("Fecha");
    $L->Cabeceras("Solicitante");
    $L->Cabeceras("$ Solc.","","number","R");
    $L->Cabeceras("Divisa");
    $L->Herramientas("E","./flujos/solicitudes/index.php?edit_view=edit_view&id=");
    $query="SELECT t_id, t_etiqueta, date(t_fecha_registro), 
			(SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id=t_iniciador) as Solicitante, 
			sv_anticipo, sv_divisa FROM tramites INNER JOIN solicitud_viaje ON (solicitud_viaje.sv_tramite=tramites.t_id) 
			WHERE t_dueno=" . $_SESSION["idusuario"] . " and t_flujo=".FLUJO_ANTICIPO." AND t_etapa_actual = ".ANTICIPO_ETAPA_APROBACION." ORDER BY t_id DESC";
    $L->muestra_lista($query,0,false,-1,"",0,"inicialMobile");
    //$L->muestra_lista($query,0);

    echo "</form>";	    
    echo "</td></tr>"; 
    
    $I->Footer();     

} else {

    //
    //  Muestra la pantalla de bienvenida al sistema.
    //
    $U	= new Usuario();
    $U->Load_Usuario_By_ID($_SESSION["idusuario"]);
    //error_log("--------------->>>>>".$_SESSION["idusuario"]);
	$usuario	= $_SESSION["usuario"];
    //$idusuario	= $_SESSION["idusuario"];
    if(isset($_SESSION['iddelegado'])){
    	$iduser = $_SESSION['iddelegado'];
    }else{
    	$iduser	= $_SESSION["idusuario"];
    }
    
    $empleado	= $_SESSION["idusuario"];
    //error_log("--------------->>>>>".$_SESSION["usuario"]);
    //error_log("--------------->>>>>".$_SESSION["usuario"]);
    //$_SESSION["usuario"] = $_SESSION["usuario"];
	if(isset($_SESSION['iddelegado'])){
		error_log("El id del usuario delegante es el siguiente: ".$_SESSION["idusuario"]);
		error_log("El usuario que se esta delegando tiene el siguiente ID: ".$_SESSION['iddelegado']);
		error_log("El usuario que se esta delegando tiene el siguiente Nombre: ".$_SESSION['delegado']);
		$_SESSION['nombreDelegado'] = $_SESSION['delegado'];
	}
    
    $I			= new Interfaz("Inicial");
    imprime_mensajes();
	//Se checa si el usuario pertenece a Controlling o Finanzas
	error_reporting(0);
	$parametros = "";
	$agrup_usu = new AgrupacionUsuarios();
	$agrup_usu->Load_Homologacion_Dueno_By_u_ID($iduser);
	$idAgrupacion = $agrup_usu->Get_dato("hd_au_id");
	if($idAgrupacion != ""){
		$parametros = ", ".$idAgrupacion;
	}
	
	$tipo_usuario = "";
	$auxiliar = "";
	if($_SESSION["perfil"] == 6){
		$tipo_usuario = "nt_asignado_a = '2000'";
	}else if($_SESSION["perfil"] == 5){
		$tipo_usuario = "nt_asignado_a = '1000'";
	}else if($_SESSION["perfil"] == 10){
		$tipo_usuario = "nt_asignado_a = '4000'";		
	}else if($_SESSION["perfil"] == 9){
		$tipo_usuario = "nt_asignado_a = '6000'";			
	}else if($_SESSION["perfil"] == SUPERVISOR_FINANZAS){
		$usuario=new Usuario();
		$tu_id=$usuario->getidtipo(SUPERVISOR_FINANZAS);
		$SupervisorFinanzas=$usuario->getGerenteSFinanzas($tu_id);
		// Se colocará la etapa en duro, debido a que según el flujo las constantes estan definidas de diferente forma.
		$tipo_usuario = "nt_asignado_a = '{$SupervisorFinanzas}' AND (t_etapa_actual >= '6' || t_etapa_actual = '2') AND (t_flujo = '".FLUJO_COMPROBACION."' || t_flujo = '".FLUJO_COMPROBACION_INVITACION."')";
	}else if($_SESSION["perfil"] == GERENTE_FINANZAS){
		$usuario=new Usuario();
		$tu_id=$usuario->getidtipo(GERENTE_FINANZAS);
		$GerenteFinanzas=$usuario->getGerenteSFinanzas($tu_id);
		// Se colocará la etapa en duro, debido a que según el flujo las constantes estan definidas de diferente forma.
		$tipo_usuario = "nt_asignado_a = '{$GerenteFinanzas}' AND (t_etapa_actual >= '6' || t_etapa_actual = '2') AND (t_flujo = '".FLUJO_COMPROBACION."' || t_flujo = '".FLUJO_COMPROBACION_INVITACION."')";
	}else{
		if(isset($_SESSION['iddelegado'])){
			$tipo_usuario = "nt_asignado_a = '{$iduser}'";
		}else{
			$tipo_usuario = "nt_asignado_a = '{$iduser}'";
			$auxiliar = "AND nt_activo = '1' ";
		}
	}
	
    $Lista		= new Lista();
    $query		=   "SELECT nt_descripcion, t_etiqueta, DATE_FORMAT(nt_fecha,'%d/%m/%Y') FROM notificaciones LEFT JOIN 
					tramites ON (notificaciones.nt_tramite=tramites.t_id) 
					WHERE {$tipo_usuario} ".$auxiliar."ORDER BY nt_id DESC LIMIT 20";
    //error_log($query);
    $Lista->Cabeceras("&Uacute;ltimas Notificaciones");
    $Lista->Cabeceras("Referencia");
    $Lista->Cabeceras("Fecha");
    $Lista->muestra_lista($query,0,false,-1,"",0,"inicial");
    //$Lista->muestra_lista($query,0,false,-1,"",0);
    $I->Footer();	

} ?>
