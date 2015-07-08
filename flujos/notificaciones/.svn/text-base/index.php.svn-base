<?php
session_start();
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/functions/utils.php";
require_once "$RUTA_A/Connections/fwk_db.php";

	
	$usuario ="";       
       if(isset($_SESSION['iddelegado'])){
               $usuario=$_SESSION['iddelegado'];
       }else{
               $usuario=$_SESSION["idusuario"];
       }
       
 
function imprime_mensajes(){
    //Mensajes 
	if(isset($_GET["okdevuelta"])){
		echo "<h3><img src='../../images/ok.png' width='20' height='20' />  La solicitud se ha devuelto al empleado.</h3>";
	}
	if(isset($_GET["montoexcedido"])){
		echo "<h3><font color='#FF0000'>Se ha excedido el monto del Boleto de Avión.</font></h3>";
	}
	if(isset($_GET["confirmacionCompra"])){
		echo "<h3><img src='../../images/ok.png' width='20' height='20' />  La compra de la solicitud se ha realizado exitosamente.</h3>";
	}
}
	
    
    // Muestra la pantalla de agencia de viajes
	if(isset($_GET['id']) && $_SESSION["perfil"]==5){
		$I  = new Interfaz("Aprobación de Solicitud de Viaje",true);
        
        
        
        // TODO: Esta clase deberia de estar dentro del directorio de flujo/solicitudes
		require_once("travel_pass.php");
		$I->Footer();
        
    // Muestra la lista de solicitudes asignadas a la agencia de viajes
	} else{
		
		 $agrup_usu = new AgrupacionUsuarios();
         $agrup_usu->Load_Grupo_de_Usuario_By_Nombre("Agencia");
         $agencia = $agrup_usu->Get_dato("au_id");
               
		$I	= new Interfaz("Notificaciones",true);

		if(isset($_GET['action']) && $_GET['action']=="devolver")
			echo "<font color='#FF0000'><b> La solicitud pasa al autorizador.</b></font>";
		else if(isset($_GET['error']))
			echo "<font color='#FF0000'><b> Error: No se localiz&oacute; destinatario, intente de nuevo.</b></font>";
		else if(isset($_GET['action'])&& $_GET['action']=="cancelada")
			echo "<font color='#FF0000'><b> El tr&aacute;mite ha sido cancelado.</b></font>";
		else if(isset($_GET['action'])&& $_GET['action']=="comprar")
			echo "<font color='#FF0000'><b> El boleto del tr&aacute;mite ha sido comprado.</b></font>";
			
		if($_SESSION["perfil"]==4){
			imprime_mensajes();
		}
		
        echo "<h1>Solicitudes por cotizar </h1>";   
                 
		$L4	= new Lista();
        $L4->Cabeceras("Folio");        
        $L4->Cabeceras("Fecha Registro");
        $L4->Cabeceras("Motivo de Viaje");
        $L4->Cabeceras("Solicitante");
        $L4->Cabeceras("Fecha de Salida");
        $L4->Cabeceras("Consultar");
        
        
        //query para solicitudes por cotizar
        $query_cotizar="SELECT tramites.t_id,
        DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'),
        tramites.t_etiqueta,
        (IF(t_delegado = 0, (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id=t_iniciador), (CONCAT((SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_delegado), ' EN NOMBRE DE: ', (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_iniciador))))) AS usuario,
        MIN(DATE_FORMAT(sv_itinerario.svi_fecha_salida,'%d/%m/%Y')),
 		(CONCAT('<p align=center><a href=$RUTA_CONSULTA_NOTIFICACIONES',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a></p>')) AS 'Consultar'
 		FROM tramites
		INNER JOIN solicitud_viaje 
		ON tramites.t_id=solicitud_viaje.sv_tramite
		INNER JOIN sv_itinerario 
		ON solicitud_viaje.sv_id=sv_itinerario.svi_solicitud
		INNER JOIN empleado 
		ON tramites.t_dueno=".$agencia."
		INNER JOIN usuario 
		ON tramites.t_iniciador=usuario.u_id		
		INNER JOIN etapas 
		ON tramites.t_flujo = etapas.et_flujo_id AND tramites.t_etapa_actual= etapas.et_etapa_id		 
		WHERE tramites.t_etapa_actual=".SOLICITUD_ETAPA_AGENCIA."
		AND ((sv_itinerario.svi_tipo_transporte='Terrestre')OR(sv_itinerario.svi_tipo_transporte='Aéreo'))
		AND (((sv_itinerario.svi_renta_auto_agencia = 1)OR(sv_itinerario.svi_hotel_agencia = 1))OR((sv_itinerario.svi_renta_auto_agencia = 0)AND(sv_itinerario.svi_hotel_agencia = 0))) 		
		GROUP BY tramites.t_id";
        
        
        if ($DEBUG) error_log($query);           
        $L4->muestra_lista($query_cotizar,0,false,-1,"",10,"svCotizar");;
                
        echo "<br><br>";
        echo "<h1>Solicitudes en revisi&oacute;n con empleado </h1>";
        
        $L5	= new Lista();
        $L5->Cabeceras("Folio");        
        $L5->Cabeceras("Fecha Registro");
        $L5->Cabeceras("Motivo de Viaje");
        $L5->Cabeceras("Solicitante");
        $L5->Cabeceras("Fecha de Salida");
        $L5->Cabeceras("Consultar");
        
        
       //query para solicitudes en revision con empleado 
       $query_conempleado="SELECT tramites.t_id,
       DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'),
       tramites.t_etiqueta,
       (IF(t_delegado = 0, (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id=t_iniciador), (CONCAT((SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_delegado), ' EN NOMBRE DE: ', (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_iniciador))))) AS usuario,
       MIN(DATE_FORMAT(sv_itinerario.svi_fecha_salida,'%d/%m/%Y')),
       (CONCAT('<p align=center><a href=$RUTA_CONSULTA_NOTIFICACIONES',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a></p>')) AS 'Consultar'  
       FROM tramites
	   INNER JOIN solicitud_viaje 
	   ON tramites.t_id=solicitud_viaje.sv_tramite
	   INNER JOIN sv_itinerario 
	   ON solicitud_viaje.sv_id=sv_itinerario.svi_solicitud	   
	   INNER JOIN empleado 
	   ON tramites.t_dueno=".$agencia." 
	   INNER JOIN usuario 
	   ON tramites.t_iniciador=usuario.u_id
	   INNER JOIN etapas 
	   ON tramites.t_flujo = etapas.et_flujo_id AND tramites.t_etapa_actual= etapas.et_etapa_id
	   WHERE ((tramites.t_etapa_actual=".SOLICITUD_ETAPA_COTIZADA.") OR (tramites.t_etapa_actual=".SOLICITUD_ETAPA_EN_COTIZACION."))
	  	AND ((sv_itinerario.svi_tipo_transporte='Terrestre')OR(sv_itinerario.svi_tipo_transporte='Aéreo'))
		AND (((sv_itinerario.svi_renta_auto_agencia = 1)OR(sv_itinerario.svi_hotel_agencia = 1))OR((sv_itinerario.svi_renta_auto_agencia = 0)AND(sv_itinerario.svi_hotel_agencia = 0))) 		
		GROUP BY tramites.t_id";
        
       	
        if ($DEBUG) error_log($query);             
        $L5->muestra_lista($query_conempleado,0,false,-1,"",10,"svRevision");
       	
       echo "<br><br>";
       echo "<h1>Solicitudes de compra pendiente </h1>";
       
       $L6	= new Lista();
        $L6->Cabeceras("Folio");        
        $L6->Cabeceras("Fecha Registro");
        $L6->Cabeceras("Motivo de Viaje");
        $L6->Cabeceras("Solicitante");
        $L6->Cabeceras("Fecha de Salida");
        $L6->Cabeceras("Consultar");
        
        
        //query para solicitudes de compra pendiente
        /*$query_compra_pendiente="SELECT tramites.t_id,
        tramites.t_fecha_registro,
        tramites.t_etiqueta,
        CONCAT(usuario.u_nombre,' ',usuario.u_paterno,' ',usuario.u_materno) AS usuario,
        MIN(sv_itinerario.svi_fecha_salida),
        (CONCAT('<p align=center><a href=$RUTA_CONSULTA_NOTIFICACIONES',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a></p>')) AS 'Consultar' 
        FROM tramites
		INNER JOIN solicitud_viaje 
		ON tramites.t_id=solicitud_viaje.sv_tramite 
		INNER JOIN etapas 
		ON tramites.t_flujo=etapas.et_flujo_id AND tramites.t_etapa_actual=etapas.et_etapa_id
		INNER JOIN flujos 
		ON etapas.et_flujo_id=flujos.f_id 
		INNER JOIN sv_itinerario 
		ON solicitud_viaje.sv_id=sv_itinerario.svi_solicitud
		INNER JOIN empleado 
		ON tramites.t_dueno=empleado.idempleado 
		INNER JOIN usuario 
		ON empleado.idfwk_usuario=usuario.u_id
		WHERE (etapas.et_id=".SOLICITUD_ETAPA_APROBADA." || etapas.et_id=".SOLICITUD_ETAPA_SEGUNDA_APROBACION.")  AND sv_itinerario.svi_medio='Aéreo'
		group by tramites.t_id";*/
        
        $query_compra_pendiente="SELECT tramites.t_id,
        DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'),
        tramites.t_etiqueta,
        (IF(t_delegado = 0, (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id=t_iniciador), (CONCAT((SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_delegado), ' EN NOMBRE DE: ', (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_iniciador))))) AS usuario,
        MIN(DATE_FORMAT(sv_itinerario.svi_fecha_salida,'%d/%m/%Y')),
        (CONCAT('<p align=center><a href=$RUTA_CONSULTA_NOTIFICACIONES',t_id,'&etapa=',t_etapa_actual,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a></p>')) AS 'Consultar' 
        FROM tramites
		INNER JOIN solicitud_viaje 
		ON tramites.t_id = solicitud_viaje.sv_tramite 
		INNER JOIN sv_itinerario 
		ON solicitud_viaje.sv_id = sv_itinerario.svi_solicitud
		INNER JOIN empleado 
	    ON tramites.t_dueno=".$agencia." 
	    INNER JOIN usuario 
	    ON tramites.t_iniciador=usuario.u_id 	
		INNER JOIN etapas 
		ON tramites.t_flujo = etapas.et_flujo_id AND tramites.t_etapa_actual= etapas.et_etapa_id		
		WHERE tramites.t_flujo = '" . FLUJO_SOLICITUD . "'
		AND ((tramites.t_etapa_actual ='".SOLICITUD_ETAPA_APROBADA."')  OR (tramites.t_etapa_actual = '".SOLICITUD_ETAPA_SEGUNDA_APROBACION."'))
		AND sv_itinerario.svi_tipo_transporte='Aéreo'		 		
		GROUP BY tramites.t_id";        
        
      
        if ($DEBUG) error_log($query);    
        $L6->muestra_lista($query_compra_pendiente,0,false,-1,"",10,"svCompraPendiente");
 

		$I->Footer();
	}
?>
