<?php
session_start();
require_once("../../lib/php/constantes.php");
require_once "$RUTA_A/Connections/fwk_db.php";
require_once "$RUTA_A/functions/utils.php";


$idUser = $_SESSION["idusuario"];  
$noEmpleado = $_SESSION["idusuario"];

//Se realiza la pregunta de eliminacion
function eliminar(){
	?>
	<script type="text/javascript">
	function confirm_del(){
		if(confirm("¿Realmente desea eliminar la solicitud de Viaje?")){
			return true;
		}else{
			return false;
			}
		}

	function confirm_del_inv(){
		if(confirm("¿Realmente desea eliminar la solicitud de Invitación?")){
			return true;
		}else{
			return false;
		}
	}
	</script>
	<?php
}		        

function imprime_mensajes(){
    //Mensajes 
	if(isset($_GET["oksaveP"])){
		echo "<h3><img src='../../images/ok.png' width='20' height='20' />El previo de la solicitud se guard&oacute; correctamente.</h3>";
	}
    if(isset($_GET["oksave"])){ 
        echo "<h3><img src='../../images/ok.png' width='20' height='20' />La solicitud se guard&oacute; correctamente, pasa a la siguiente etapa para su aprobaci&oacute;n.</h3>";
    }     
    if(isset($_GET["oksaveE"])){
        echo "<h3><img src='../../images/ok.png' width='20' height='20' />La solicitud se envi&oacute; correctamente para su aprobaci&oacute;n.</h3>";
    }
    if(isset($_GET["okdirector"])){
    	echo "<h3><img src='../../images/ok.png' width='20' height='20' />La solicitud se envi&oacute; correctamente al Director para su aprobaci&oacute;n.</h3>";
    }
    if(isset($_GET["okcotizacion"])){
    	echo "<h3><img src='../../images/ok.png' width='20' height='20' />La cotizaci&oacute;n se ha aprobado correctamente, pasa a la siguiente etapa para su aprobaci&oacute;n.</h3>";
    }
    if(isset($_GET["oksaveG"])){
    	echo "<h3><img src='../../images/ok.png' width='20' height='20' />La solicitud se guard&oacute; correctamente, pasa a la siguiente etapa para su aprobaci&oacute;n.</h3>";
    }
    if(isset($_GET["errsave"])){ 
        echo "<font color='#FF0000'>
		<img src='../../images/action_cancel.gif' width='22' height='22' /><b>Se encontraron errores, la solicitud no se ha guardado. Verifique e intente de nuevo.</b></font>";
    }     
    if(isset($_GET["errsaveP"])){
    	echo "<font color='#FF0000'>
		<img src='../../images/action_cancel.gif' width='22' height='22' /><b>Se encontraron errores, el previo de la solicitud no se ha guardado. Verifique e intente de nuevo.</b></font>";
    }    
    if(isset($_GET["erramnt"]) ){ 
        echo "<font color='#FF0000'>
		<img src='../../images/action_cancel.gif' width='22' height='22' /><b>Se encontraron errores en los montos, la solicitud no se ha guardado. Verifique e intente de nuevo.</b></font>";
    }     
    if(isset($_GET["ccNegative"])){ 
        echo "<font color='#FF0000'>
		<img src='../../images/action_cancel.gif' width='22' height='22' /><b>No se guardo la solicitud ya que no se localiza presupuesto del periodo actual para el departamento seleccionado, verifique con su administrador</b></font>";		
    }     
    if(isset($_GET["action"]) ){         
        if($_GET["action"]=="autorizar" ){ 
            echo "<font color='#00B000'><b>La solicitud se ha aprobado.</b></font>";
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
}

//generar_csv
if(isset($_GET['generar_csv'])){
	$I  = new Interfaz("generar scv",true);
	require_once("generar_csv_anticipos.php");
	require_once("generar_csv_vuelos.php");
	require_once("generar_csv_comprobaciones.php");
	$I->Footer();
}

// Alta de nueva Solicitud de Viaje
if(isset($_REQUEST['new'])){
	echo"<script type=\"text\/javascript\">alert(\"Registrar nueva solicitud de Viaje.\")</script>";
	$I  = new Interfaz("Solicitud de Viaje",true);
	require_once("solicitud_viaje_new.php");
	$I->Footer();
}
// Alta de nueva Solicitud Gastos
else if(isset($_GET['new2'])){
	$I  = new Interfaz("Solicitud de Gastos",true);
	require_once("solicitud_gastos.php");
	$I->Footer();
}
// Detalles de Solicitud 
else if(isset($_GET['view'])){
	$I  = new Interfaz("Solicitud de Viaje",true);
	require_once("solicitud_view.php");
	$I->Footer();
}
// Autorizacion de Solicitud
else if(isset($_GET['edit_view'])){
    $I  = new Interfaz("Solicitud de Viaje",true);
    require_once("solicitud_view.php");
    $I->Footer();        
}

else
// Muestra pantalla de Amex
 if(isset($_GET['type']) && $_GET['type']=='2' || isset($_POST['type']) && $_POST['type']=='2'){
 	
	if(isset($_GET['delsol'])||isset($_POST['delsol'])){
		$id_solicitud=$_GET['delsol'];
		$cnn = new conexion();		
		$query_solicitud_gastos = "DELETE FROM solicitud_gastos WHERE sg_tramite=".$id_solicitud;		
		$query_comensales = "DELETE FROM comensales WHERE c_solicitud = ".$id_solicitud;
		$query_tramite = "DELETE FROM tramites WHERE t_id=".$id_solicitud;
		$cnn->ejecutar($query_solicitud_gastos);
		$cnn->ejecutar($query_comensales);
		$cnn->ejecutar($query_tramite);
	}
	
    // Procesa busqueda de solicitudes
    if(isset($_REQUEST["busca"])){
    	
        @$noComp = $_REQUEST["noComp"];       
        $parametros="";
        $cabecera="";
        $etapa="";
        $busqueda_valueSI = "";	
	
		if(isset($_REQUEST['et_etapa_id']))
			$etapa = $_REQUEST['et_etapa_id'];
		
		// Numero de comprobacion
		if(isset($noComp) && $noComp != ''){                     
			$parametros.="AND t_id = '".$noComp."'";     
			$busqueda_valueSI .= "&noComp=$noComp";				
		}
		
		// Etapa
		if($etapa>=0 && $etapa!=''){
			$parametros.=" AND t_etapa_actual = ".$etapa;
			$busqueda_valueSI .= "&et_etapa_id=$etapa";
		}
		
		// Fecha Inicial
		if (isset($_REQUEST["finicial"])&& !empty($_REQUEST["finicial"])){
			$date=explode("/",$_REQUEST["finicial"]);
			if(count($date)!=3){
				$date_db = $_REQUEST["finicial"];
			}else{
				$date_db=$date[2]."-".$date[1]."-".$date[0];
			}
			$parametros.=" AND t_fecha_registro >= '".$date_db." 00:00:00'";
			$busqueda_valueSI .= "&finicial=$date_db";
		}
		
		// Fecha Final
		if (isset($_REQUEST["ffinal"])&& !empty ($_REQUEST["ffinal"])){
			$date=explode("/",$_REQUEST["ffinal"]);
			if(count($date)!=3){
				$date_db = $_REQUEST["ffinal"];
			}else{
				$date_db=$date[2]."-".$date[1]."-".$date[0];
			}
			$parametros.=" AND t_fecha_registro <= '".$date_db." 23:59:59'";
			$busqueda_valueSI .= "&ffinal=$date_db";
		}
		
		//error_log(">>>>>>>>>>>>>>>>>>>>>>URL: ".$busqueda_valueSI);
		$I  = new Interfaz("Solicitudes",true);
		$L2	= new Lista("busca&docs=docs&type=2$busqueda_valueSI");
		$L2->Cabeceras("Folio");
		$L2->Cabeceras("Motivo");
		$L2->Cabeceras("Fecha Registro");
		$L2->Cabeceras("Etapa");
		$L2->Cabeceras("Autorizador");
		$L2->Cabeceras("$ Solc.","","number","R");
		$L2->Cabeceras("Divisa");
		$L2->Cabeceras("Consultar","","text","C");
		$L2->Cabeceras("Editar","","text","C");
		$L2->Cabeceras("Eliminar","","text","C");
		
	   if($_SESSION["perfil"]==2){              
			$query="SELECT t_id, t_etiqueta, DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'), (SELECT et_etapa_nombre FROM etapas WHERE et_etapa_id = t_etapa_actual AND et_flujo_id = t_flujo), 
				IF(t_etapa_actual='".SOLICITUD_GASTOS_ETAPA_SIN_ENVIAR."' OR t_etapa_actual='".SOLICITUD_GASTOS_ETAPA_APROBADA."' OR t_etapa_actual='".SOLICITUD_GASTOS_ETAPA_RECHAZADA."', '', 
				CASE t_dueno
				WHEN 2000 THEN 'FINANZAS'
				ELSE (SELECT nombre FROM empleado WHERE idfwk_usuario = t_dueno)
				END) AS autorizador,   
				sg_monto, div_nombre,
				CONCAT('<a href=./index.php?view=view&id=',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a>') AS consultar, 
				IF((t_etapa_actual = '".SOLICITUD_GASTOS_ETAPA_APROBACION."' || t_etapa_actual = '".SOLICITUD_GASTOS_ETAPA_APROBADA."' || t_etapa_actual = '".SOLICITUD_GASTOS_ETAPA_EN_APROBACION_POR_DIRECTOR."'),'',(CONCAT('<a href=./index.php?docs=docs&type=2&new2=new2&id=',t_id,'><img border=0 title=Editar src=".$RUTA_R."images/addedit.png></a>'))) AS editar,
				IF((t_etapa_actual = '".SOLICITUD_GASTOS_ETAPA_APROBACION."' || t_etapa_actual = '".SOLICITUD_GASTOS_ETAPA_APROBADA."' || t_etapa_actual = '".SOLICITUD_GASTOS_ETAPA_EN_APROBACION_POR_DIRECTOR."'),'',(CONCAT('<a href=./index.php?docs=docs&type=2&delsol=',t_id,' onclick=\'return confirm_del_inv(); \' ><img border=0 title=Eliminar src=".$RUTA_R."images/delete.png></a>'))) AS eliminar
				FROM tramites INNER JOIN solicitud_gastos ON (sg_tramite=tramites.t_id)
				LEFT JOIN divisa ON (sg_divisa = div_id) 
				WHERE t_flujo=".FLUJO_SOLICITUD_GASTOS." ".$parametros." ORDER BY t_id DESC";
		} else {
			$query="SELECT t_id, t_etiqueta, DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'), (SELECT et_etapa_nombre FROM etapas WHERE et_etapa_id = t_etapa_actual AND et_flujo_id = t_flujo), 
				IF(t_etapa_actual='".SOLICITUD_GASTOS_ETAPA_SIN_ENVIAR."' OR t_etapa_actual='".SOLICITUD_GASTOS_ETAPA_APROBADA."' OR t_etapa_actual='".SOLICITUD_GASTOS_ETAPA_RECHAZADA."' OR t_etapa_actual='".SOLICITUD_GASTOS_ETAPA_RECHAZADA_POR_DIRECTOR."', '', 
				CASE t_dueno
				WHEN 2000 THEN 'FINANZAS'
				ELSE (SELECT nombre FROM empleado WHERE idfwk_usuario = t_dueno)
				END) AS autorizador,   
				sg_monto, div_nombre,
				CONCAT('<a href=./index.php?view=view&id=',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a>') AS consultar, 
				IF((t_etapa_actual = '".SOLICITUD_GASTOS_ETAPA_APROBACION."' || t_etapa_actual = '".SOLICITUD_GASTOS_ETAPA_APROBADA."' || t_etapa_actual = '".SOLICITUD_GASTOS_ETAPA_EN_APROBACION_POR_DIRECTOR."'),'',(CONCAT('<a href=./index.php?docs=docs&type=2&new2=new2&id=',t_id,'><img border=0 title=Editar src=".$RUTA_R."images/addedit.png></a>'))) AS editar,
				IF((t_etapa_actual = '".SOLICITUD_GASTOS_ETAPA_APROBACION."' || t_etapa_actual = '".SOLICITUD_GASTOS_ETAPA_APROBADA."' || t_etapa_actual = '".SOLICITUD_GASTOS_ETAPA_EN_APROBACION_POR_DIRECTOR."'),'',(CONCAT('<a href=./index.php?docs=docs&type=2&delsol=',t_id,' onclick=\'return confirm_del_inv(); \' ><img border=0 title=Eliminar src=".$RUTA_R."images/delete.png></a>'))) AS eliminar
				FROM tramites INNER JOIN solicitud_gastos ON (sg_tramite=tramites.t_id)
				LEFT JOIN divisa ON (sg_divisa = div_id) 
				WHERE tramites.t_iniciador =".$idUser." AND t_flujo=".FLUJO_SOLICITUD_GASTOS." ".$parametros." ORDER BY t_id DESC";
		}			
		
		include("../../lib/php/mnu_toolbar.php"); 
		solicitud_toolbar($noEmpleado);
		busca_solicitud(FLUJO_AMEX);

		echo "<h1>Solicitudes de gastos en Etapa: ";
		if($etapa == '-1' || $etapa < 0 || $etapa == ''){
			echo "Todas</h1>"; 
		} else {
			$t = new Tramite();
			echo $t->Get_EtapaNombre($etapa, FLUJO_AMEX);
			echo "</h1>"; 
		}
		$L2->muestra_lista($query,0,false,-1,"",10,"solgtsBusq");
		$I->Footer();
			 
    // Muestra lista de solicitudes sin filtrar
    } else {
        
        $I	= new Interfaz("Solicitudes de Gastos",true);
        include("../../lib/php/mnu_toolbar.php"); 
        solicitud_toolbar($noEmpleado);
        if($_SESSION["perfil"]==5 || $_SESSION["perfil"]==6)
        	imprime_mensajes();	
        

		if(!isset($_SESSION["idrepresentante"]))
        	$parametrobusqueda = " AND t_etapa_actual != '".SOLICITUD_GASTOS_ETAPA_RECHAZADA_POR_DIRECTOR."' ";        
        
        eliminar();
        
        $L2	= new Lista("docs=docs&type=2");
        $L2->Cabeceras("Folio");
        $L2->Cabeceras("Motivo");
        $L2->Cabeceras("Fecha Registro");
        $L2->Cabeceras("Etapa");
        $L2->Cabeceras("Autorizador");
        $L2->Cabeceras("$ Solc.","","number","R");
        $L2->Cabeceras("Divisa");
        $L2->Cabeceras("Consultar","","text","C");
        $L2->Cabeceras("Editar","","text","C");
        $L2->Cabeceras("Eliminar","","text","C");
        
        if($_SESSION["perfil"]!=5 && $_SESSION["perfil"]!=6){
        	busca_solicitud(FLUJO_SOLICITUD_GASTOS);
        	imprime_mensajes();
        	echo "</div><h1>Mis Solicitudes de Gastos Creadas</h1>";
	        if($_SESSION["perfil"]==2){        
	            $query="SELECT t_id, t_etiqueta, DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'), (SELECT et_etapa_nombre FROM etapas WHERE et_etapa_id = t_etapa_actual AND et_flujo_id = t_flujo), 
					IF(t_etapa_actual='".SOLICITUD_GASTOS_ETAPA_SIN_ENVIAR."' OR t_etapa_actual='".SOLICITUD_GASTOS_ETAPA_APROBADA."' OR t_etapa_actual='".SOLICITUD_GASTOS_ETAPA_RECHAZADA."' OR t_etapa_actual='".SOLICITUD_GASTOS_ETAPA_RECHAZADA_POR_DIRECTOR."', '',
					CASE t_dueno
					WHEN 6000 THEN 'AUTORIZADOR'
					ELSE (SELECT nombre FROM empleado WHERE idfwk_usuario = t_dueno)
					END) AS autorizador, 
					sg_monto, div_nombre, 
					CONCAT('<a href=./index.php?view=view&id=',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a>') AS consultar, 
					IF(((t_etapa_actual='".SOLICITUD_GASTOS_ETAPA_APROBACION."')||(t_etapa_actual='".SOLICITUD_GASTOS_ETAPA_APROBADA."')||(t_etapa_actual='".SOLICITUD_GASTOS_ETAPA_EN_APROBACION_POR_DIRECTOR."')),'',(CONCAT('<a href=./index.php?docs=docs&type=2&new2=new2&id=',t_id,'><img border=0 title=Editar src=".$RUTA_R."images/addedit.png></a>'))) AS editar,
					IF((t_etapa_actual = '".SOLICITUD_GASTOS_ETAPA_APROBACION."' || t_etapa_actual = '".SOLICITUD_GASTOS_ETAPA_APROBADA."' || t_etapa_actual = '".SOLICITUD_GASTOS_ETAPA_EN_APROBACION_POR_DIRECTOR."'),'',(CONCAT('<a href=./index.php?docs=docs&type=2&delsol=',t_id,' onclick=\'return confirm_del_inv(); \' ><img border=0 title=Eliminar src=".$RUTA_R."images/delete.png></a>'))) AS eliminar
					FROM tramites INNER JOIN solicitud_gastos ON (sg_tramite=tramites.t_id)
					LEFT JOIN divisa ON (sg_divisa = div_id) 
					WHERE t_flujo=".FLUJO_SOLICITUD_GASTOS." ORDER BY t_id DESC";
	        } else {
				$query="SELECT t_id, t_etiqueta, DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'), (SELECT et_etapa_nombre FROM etapas WHERE et_etapa_id = t_etapa_actual AND et_flujo_id = t_flujo), 
					IF(t_etapa_actual='".SOLICITUD_GASTOS_ETAPA_SIN_ENVIAR."' OR t_etapa_actual='".SOLICITUD_GASTOS_ETAPA_APROBADA."' OR t_etapa_actual='".SOLICITUD_GASTOS_ETAPA_RECHAZADA."' OR t_etapa_actual='".SOLICITUD_GASTOS_ETAPA_RECHAZADA_POR_DIRECTOR."', '',
					CASE t_dueno
					WHEN 6000 THEN 'AUTORIZADOR'
					ELSE (SELECT nombre FROM empleado WHERE idfwk_usuario = t_dueno)
					END) AS autorizador,   
					sg_monto, div_nombre,
					CONCAT('<a href=./index.php?view=view&id=',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a>') AS consultar, 
					IF(((t_etapa_actual='".SOLICITUD_GASTOS_ETAPA_APROBACION."')||(t_etapa_actual='".SOLICITUD_GASTOS_ETAPA_APROBADA."')||(t_etapa_actual='".SOLICITUD_GASTOS_ETAPA_EN_APROBACION_POR_DIRECTOR."')),'',(CONCAT('<a href=./index.php?docs=docs&type=2&new2=new2&id=',t_id,'><img border=0 title=Editar src=".$RUTA_R."images/addedit.png></a>'))) AS editar,
					IF((t_etapa_actual = '".SOLICITUD_GASTOS_ETAPA_APROBACION."' || t_etapa_actual = '".SOLICITUD_GASTOS_ETAPA_APROBADA."')||(t_etapa_actual='".SOLICITUD_GASTOS_ETAPA_EN_APROBACION_POR_DIRECTOR."'),'',(CONCAT('<a href=./index.php?docs=docs&type=2&delsol=',t_id,' onclick=\'return confirm_del_inv(); \'><img border=0 title=Eliminar src=".$RUTA_R."images/delete.png></a>'))) AS eliminar
					FROM tramites INNER JOIN solicitud_gastos ON (sg_tramite=tramites.t_id)
					LEFT JOIN divisa ON (sg_divisa = div_id)  
					WHERE tramites.t_iniciador =".$idUser." AND t_flujo=".FLUJO_SOLICITUD_GASTOS." ".$parametrobusqueda." ORDER BY t_id DESC";
	        }
			$L2->muestra_lista($query,0,false,-1,"",10,"solgts");			
        } 
        
        echo "<br><br>";
        if($_SESSION["perfil"]!=3){
	        echo "<h1>Solicitudes de Gastos Pendientes de mi Aprobaci&oacute;n </h1>";
	        echo "<form action='index.php' method='post' name='comprobacionesaprobs'>";
	        
	        $user = "";
	        if($_SESSION["perfil"] == 6){
	        	$user = "2000";
	        }else if($_SESSION["perfil"] == 5){
	        	$user = "1000";
			}else if($_SESSION["perfil"] == 10){
	        	$user = "4000";
			}else if($_SESSION["perfil"] == 9){
	        	$user = "6000";
	        }else{			
	        	$user = $idUser;
	        }
	
	        $L	= new Lista("docs=docs&type=2");
	        $L->Cabeceras("Folio");
	        $L->Cabeceras("Motivo");
	        $L->Cabeceras("Fecha Registro");
	        $L->Cabeceras("Etapa");
	        $L->Cabeceras("Solicitante");
	        $L->Cabeceras("$ Solc.","","number","R");
	        $L->Cabeceras("Divisa");
	        $L->Cabeceras("Consultar","","text","C");
	        $query="SELECT t_id, t_etiqueta, DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'), (SELECT et_etapa_nombre FROM etapas WHERE et_etapa_id = t_etapa_actual AND et_flujo_id = t_flujo), 
	        		(IF(t_delegado = 0, (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id=t_iniciador), (CONCAT((SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_delegado), ' EN NOMBRE DE: ', (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_iniciador))))) AS solicitante, 
	        		sg_monto, div_nombre, 
					CONCAT('<a href=./index.php?edit_view=edit_view&id=',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a>') AS consultar 
					FROM tramites INNER JOIN solicitud_gastos ON (sg_tramite=tramites.t_id)
					INNER JOIN divisa ON (sg_divisa = div_id) 
					WHERE t_dueno = '".$user."' AND t_flujo = '".FLUJO_SOLICITUD_GASTOS."' AND t_etapa_actual = '".SOLICITUD_GASTOS_ETAPA_APROBACION."' 
	        		AND t_iniciador != '".$idUser."' ORDER BY t_id DESC";
	        //if ($DEBUG) //error_log($query);
			$L->muestra_lista($query,0,false,-1,"",10,"solgtsAut");
	        //$L->muestra_lista($query,0);
	        echo "</form>";
        }        	
        $I->Footer();

    }

//busqueda a realizar
// Muestra pantalla por defecto: Solicitudes
//amex
} else {
   
    // Procesa busqueda de solicitudes
    if(isset($_REQUEST["busca"])){
		$parametros="";
        $cabecera="";
        $etapa="";
        $busqueda_value = "";        
		$noComp = $_POST["noComp"] ;
		
        if(isset($_REQUEST['et_etapa_id']))
        	$etapa = $_REQUEST['et_etapa_id'];
        
        // Numero de comprobacion
        if(isset($noComp) && $noComp != ''){
        	$parametros.="AND t_id = '".$noComp."'";
        	$busqueda_value .= "&noComp=$noComp";
        }
         
        // Etapa
        if($etapa>=0 && $etapa!='' ){
        $parametros.=" AND t_etapa_actual = ".$etapa;
        	$busqueda_value .= "&et_etapa_id=$etapa";
        }
        
        // Fecha Inicial
        if (isset($_REQUEST["finicial"])&& !empty($_REQUEST["finicial"])){
        	$date=explode("/",$_REQUEST["finicial"]);
        	if(count($date)!=3){
        		$date_db = $_REQUEST["finicial"];
        	}else{
        		$date_db=$date[2]."-".$date[1]."-".$date[0];
        	}
        	$parametros.=" AND t_fecha_registro >= '".$date_db." 00:00:00'";
        	$busqueda_value .= "&finicial=$date_db";
        }
        
        // Fecha Final
        if (isset($_REQUEST["ffinal"]) && !empty ($_REQUEST["ffinal"])){
        	$date=explode("/",$_REQUEST["ffinal"]);
        	if(count($date)!=3){
        		$date_db = $_REQUEST["ffinal"];
        	}else{
        		$date_db=$date[2]."-".$date[1]."-".$date[0];
        	}
        	$parametros.=" AND t_fecha_registro <= '".$date_db." 23:59:59'";
        	$busqueda_value .= "&ffinal=$date_db";
        }
        			       
            
		$I  = new Interfaz("Solicitudes",true);
        $L2	= new Lista("busca&docs=docs&type=1".$busqueda_value);	
		$L2->Cabeceras("Folio");
		$L2->Cabeceras("Motivo del viaje");
		$L2->Cabeceras("Fecha Registro");
		$L2->Cabeceras("Etapa");
		$L2->Cabeceras("Autorizador");
		$L2->Cabeceras("$ Solc.");
		$L2->Cabeceras("Consultar");   
		$L2->Cabeceras("Editar");
		$L2->Cabeceras("Eliminar");
		// Se  hace llamado a la funcion eliminar
		eliminar();
		
		if($_SESSION["perfil"]==2){
			$query="SELECT  tramites.t_id,
					tramites.t_etiqueta,
					DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'),etapas.et_etapa_nombre,
					IF(etapas.et_etapa_id=".SOLICITUD_ETAPA_APROBADA." AND ((sv_viaje='Sencillo') OR (sv_viaje='Redondo')),IF(sv_itinerario.svi_tipo_transporte='Aéreo','AGENCIA',''),IF(etapas.et_etapa_id=".SOLICITUD_ETAPA_APROBADA." 
					AND((SELECT COUNT(DISTINCT(svi_tipo_transporte)) FROM sv_itinerario WHERE svi_solicitud = (SELECT sv_id FROM solicitud_viaje WHERE sv_tramite = tramites.t_id)) = 1),
					IF((SELECT DISTINCT(svi_tipo_transporte) FROM sv_itinerario WHERE svi_solicitud = (SELECT sv_id FROM solicitud_viaje WHERE sv_tramite = tramites.t_id)) = 'Terrestre','','AGENCIA'),
					IF(etapas.et_etapa_id=".SOLICITUD_ETAPA_APROBADA." AND (sv_itinerario.svi_tipo_transporte='Terrestre' OR sv_itinerario.svi_tipo_transporte='Aéreo'),'AGENCIA',
					IF((etapas.et_etapa_id=".SOLICITUD_ETAPA_RECHAZADA.")OR (etapas.et_etapa_id=".SOLICITUD_ETAPA_SIN_ENVIAR."),'',
					IF(tramites.t_dueno IN (SELECT au_id FROM agrupacion_usuarios),(SELECT au_nombre FROM agrupacion_usuarios AS au WHERE au.au_id=tramites.t_dueno),
					IF(tramites.t_dueno = tramites.t_iniciador AND etapas.et_etapa_id !=". SOLICITUD_ETAPA_EN_APROBACION_POR_DIRECTOR.",'',(SELECT CONCAT(usuario.u_nombre,' ',usuario.u_paterno,' ',usuario.u_materno) FROM usuario WHERE usuario.u_id=tramites.t_dueno))))))) AS 'autorizador',
					CONCAT(FORMAT(solicitud_viaje.sv_total, 2),' MXN') AS monto, 
					(CONCAT('<p align=center><a href=./index.php?edit_view=edit_view&id=',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a></p>')) AS 'Consultar',
					IF(((etapas.et_etapa_id=".SOLICITUD_ETAPA_RECHAZADA.") || (etapas.et_etapa_id=".SOLICITUD_ETAPA_SIN_ENVIAR.") || (etapas.et_etapa_id=".SOLICITUD_ETAPA_RECHAZADA_POR_DIRECTOR.")),
					(CONCAT('<p align=center><a href=./index.php?new=new&id=',t_id,'><img border=0 title=Editar src=".$RUTA_R."images/addedit.png></a></p>')),'') AS 'Editar' ,  
					IF(((etapas.et_etapa_id=".SOLICITUD_ETAPA_CANCELADA.")  || (etapas.et_etapa_id=".SOLICITUD_ETAPA_RECHAZADA.") || (etapas.et_etapa_id=".SOLICITUD_ETAPA_SIN_ENVIAR.") || (etapas.et_etapa_id=".SOLICITUD_ETAPA_RECHAZADA_POR_DIRECTOR.")),(CONCAT('<p align=center><a href=./index.php?elimina=',t_id,' onclick=\'return confirm_del(); \'><img border=0 title=Eliminar src=".$RUTA_R."images/delete.png></a></p>')),'') AS 'Eliminar'		            
					FROM tramites
					INNER JOIN etapas
					ON tramites.t_flujo= etapas.et_flujo_id AND tramites.t_etapa_actual = etapas.et_etapa_id
					INNER JOIN usuario 	ON tramites.t_iniciador = usuario.u_id
					INNER JOIN solicitud_viaje ON tramites.t_id = solicitud_viaje.sv_tramite
					INNER JOIN sv_itinerario ON sv_itinerario.svi_solicitud = solicitud_viaje.sv_id					
					WHERE t_flujo = '" . FLUJO_SOLICITUD . "'".$parametros." GROUP BY t_id ORDER BY t_id DESC";
		}else{
			$query="SELECT  tramites.t_id,
					tramites.t_etiqueta,
					DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'),etapas.et_etapa_nombre,
					IF(etapas.et_etapa_id=".SOLICITUD_ETAPA_APROBADA." AND ((sv_viaje='Sencillo') OR (sv_viaje='Redondo')),IF(sv_itinerario.svi_tipo_transporte='Aéreo','AGENCIA',''),IF(etapas.et_etapa_id=".SOLICITUD_ETAPA_APROBADA." 
					AND((SELECT COUNT(DISTINCT(svi_tipo_transporte)) FROM sv_itinerario WHERE svi_solicitud = (SELECT sv_id FROM solicitud_viaje WHERE sv_tramite = tramites.t_id)) = 1),
					IF((SELECT DISTINCT(svi_tipo_transporte) FROM sv_itinerario WHERE svi_solicitud = (SELECT sv_id FROM solicitud_viaje WHERE sv_tramite = tramites.t_id)) = 'Terrestre','','AGENCIA'),
					IF(etapas.et_etapa_id=".SOLICITUD_ETAPA_APROBADA." AND (sv_itinerario.svi_tipo_transporte='Terrestre' OR sv_itinerario.svi_tipo_transporte='Aéreo'),'AGENCIA',
					IF((etapas.et_etapa_id=".SOLICITUD_ETAPA_RECHAZADA.")OR (etapas.et_etapa_id=".SOLICITUD_ETAPA_SIN_ENVIAR."),'',
					IF(tramites.t_dueno IN (SELECT au_id FROM agrupacion_usuarios),(SELECT au_nombre FROM agrupacion_usuarios AS au WHERE au.au_id=tramites.t_dueno),
					IF(tramites.t_dueno = tramites.t_iniciador AND etapas.et_etapa_id !=". SOLICITUD_ETAPA_EN_APROBACION_POR_DIRECTOR.",'',(SELECT CONCAT(usuario.u_nombre,' ',usuario.u_paterno,' ',usuario.u_materno) FROM usuario WHERE usuario.u_id=tramites.t_dueno))))))) AS 'autorizador',
					CONCAT(FORMAT(solicitud_viaje.sv_total, 2),' MXN') AS monto, 
					(CONCAT('<p align=center><a href=./index.php?edit_view=edit_view&id=',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a></p>')) AS 'Consultar',
					IF(((etapas.et_etapa_id=".SOLICITUD_ETAPA_RECHAZADA.") || (etapas.et_etapa_id=".SOLICITUD_ETAPA_SIN_ENVIAR.") || (etapas.et_etapa_id=".SOLICITUD_ETAPA_RECHAZADA_POR_DIRECTOR.")),
					(CONCAT('<p align=center><a href=./index.php?new=new&id=',t_id,'><img border=0 title=Editar src=".$RUTA_R."images/addedit.png></a></p>')),'') AS 'Editar' ,  
					IF(((etapas.et_etapa_id=".SOLICITUD_ETAPA_CANCELADA.")  || (etapas.et_etapa_id=".SOLICITUD_ETAPA_RECHAZADA.") || (etapas.et_etapa_id=".SOLICITUD_ETAPA_SIN_ENVIAR.") || (etapas.et_etapa_id=".SOLICITUD_ETAPA_RECHAZADA_POR_DIRECTOR.")),(CONCAT('<p align=center><a href=./index.php?elimina=',t_id,' onclick=\'return confirm_del(); \'><img border=0 title=Eliminar src=".$RUTA_R."images/delete.png></a></p>')),'') AS 'Eliminar'		            
					FROM tramites
					INNER JOIN etapas
					ON tramites.t_flujo= etapas.et_flujo_id AND tramites.t_etapa_actual = etapas.et_etapa_id
					INNER JOIN usuario 	ON tramites.t_iniciador = usuario.u_id
					INNER JOIN solicitud_viaje ON tramites.t_id = solicitud_viaje.sv_tramite
					INNER JOIN sv_itinerario ON sv_itinerario.svi_solicitud = solicitud_viaje.sv_id	 
					WHERE tramites.t_iniciador=".$idUser."
					AND t_flujo = '" . FLUJO_SOLICITUD . "'".$parametros." GROUP BY t_id ORDER BY t_id DESC";
            }
            
			include("../../lib/php/mnu_toolbar.php"); 
            solicitud_toolbar($noEmpleado);
            busca_solicitud(FLUJO_SOLICITUD);

            echo "<h1>Solicitudes en Etapa: ";
            if($etapa == '-1' || $etapa < 0 || $etapa == ""){
                echo "Todas</h1>"; 
            } else {
                $t = new Tramite();
                echo $t->Get_EtapaNombre($etapa, FLUJO_SOLICITUD);
                echo "</h1>"; 
            }
            $L2->muestra_lista($query,0,false,-1,"",10,"solviajeBusq");
            $I->Footer();
	// Muestra lista de solicitudes sin filtrar   
    } else {
        
        $I	= new Interfaz("Solicitudes de Viajes",true);
        include("../../lib/php/mnu_toolbar.php");         
 		solicitud_toolbar($noEmpleado);
       
        
        if($_SESSION["perfil"]==5 || $_SESSION["perfil"]==6){        	
   		}else{
       		busca_solicitud(FLUJO_SOLICITUD);        
			echo "</div><h1>Mis Solicitudes de Viaje Creadas  </h1>";
   		}
   		
   		if(!isset($_SESSION["idrepresentante"]))
   			$parametrobusqueda = " AND t_etapa_actual != '".SOLICITUD_ETAPA_RECHAZADA_POR_DIRECTOR."' ";
   		
   		
   		imprime_mensajes();
		//Cuando el usuario sea diferente de tipo 5 y 6  se mostraran Mis solicitudes de viaje creadas y las pendientes
		//cuando sea de tipo 5 y 6 solo se mostrara las solicitudes pendientes a su aprobacion
   		if(($_SESSION["perfil"] != 6 && $_SESSION["perfil"] != 5 )){
   			if($_SESSION["perfil"]==2){
   				//query de  Mis solicitudes de viaje creadas (administrador)
   				//Linea 546 (etapas.et_etapa_id=".SOLICITUD_ETAPA_EN_COTIZACION.") || (etapas.et_etapa_id!=".SOLICITUD_ETAPA_EN_APROBACION.") ||
   				$query="SELECT  tramites.t_id,
					tramites.t_etiqueta,
					DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'),etapas.et_etapa_nombre,
					IF(etapas.et_etapa_id=".SOLICITUD_ETAPA_APROBADA." AND ((sv_viaje='Sencillo') OR (sv_viaje='Redondo')),IF(sv_itinerario.svi_tipo_transporte='Aéreo','AGENCIA',''),IF(etapas.et_etapa_id=".SOLICITUD_ETAPA_APROBADA." 
					AND((SELECT COUNT(DISTINCT(svi_tipo_transporte)) FROM sv_itinerario WHERE svi_solicitud = (SELECT sv_id FROM solicitud_viaje WHERE sv_tramite = tramites.t_id)) = 1),
					IF((SELECT DISTINCT(svi_tipo_transporte) FROM sv_itinerario WHERE svi_solicitud = (SELECT sv_id FROM solicitud_viaje WHERE sv_tramite = tramites.t_id)) = 'Terrestre','','AGENCIA'),
					IF(etapas.et_etapa_id=".SOLICITUD_ETAPA_APROBADA." AND (sv_itinerario.svi_tipo_transporte='Terrestre' OR sv_itinerario.svi_tipo_transporte='Aéreo'),'AGENCIA',
					IF((etapas.et_etapa_id=".SOLICITUD_ETAPA_RECHAZADA.")OR (etapas.et_etapa_id=".SOLICITUD_ETAPA_SIN_ENVIAR."),'',
					IF(tramites.t_dueno IN (SELECT au_id FROM agrupacion_usuarios),(SELECT au_nombre FROM agrupacion_usuarios AS au WHERE au.au_id=tramites.t_dueno),
					IF(tramites.t_dueno = tramites.t_iniciador AND etapas.et_etapa_id !=". SOLICITUD_ETAPA_EN_APROBACION_POR_DIRECTOR.",'',(SELECT CONCAT(usuario.u_nombre,' ',usuario.u_paterno,' ',usuario.u_materno) FROM usuario WHERE usuario.u_id=tramites.t_dueno))))))) AS 'autorizador',
					CONCAT(FORMAT(solicitud_viaje.sv_total, 2),' MXN') AS monto, 
					(CONCAT('<p align=center><a href=./index.php?edit_view=edit_view&id=',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a></p>')) AS 'Consultar',
					IF(((etapas.et_etapa_id=".SOLICITUD_ETAPA_RECHAZADA.") || (etapas.et_etapa_id=".SOLICITUD_ETAPA_SIN_ENVIAR.") || (etapas.et_etapa_id=".SOLICITUD_ETAPA_RECHAZADA_POR_DIRECTOR.")),
					(CONCAT('<p align=center><a href=./index.php?new=new&id=',t_id,'><img border=0 title=Editar src=".$RUTA_R."images/addedit.png></a></p>')),'') AS 'Editar' ,  
					IF(((etapas.et_etapa_id=".SOLICITUD_ETAPA_CANCELADA.")  || (etapas.et_etapa_id=".SOLICITUD_ETAPA_RECHAZADA.") || (etapas.et_etapa_id=".SOLICITUD_ETAPA_SIN_ENVIAR.") || (etapas.et_etapa_id=".SOLICITUD_ETAPA_RECHAZADA_POR_DIRECTOR.")),(CONCAT('<p align=center><a href=./index.php?elimina=',t_id,' onclick=\'return confirm_del(); \'><img border=0 title=Eliminar src=".$RUTA_R."images/delete.png></a></p>')),'') AS 'Eliminar'		            
					FROM tramites
					INNER JOIN etapas
					ON tramites.t_flujo= etapas.et_flujo_id AND tramites.t_etapa_actual = etapas.et_etapa_id
					INNER JOIN usuario
					ON tramites.t_iniciador = usuario.u_id
					INNER JOIN solicitud_viaje
					ON tramites.t_id = solicitud_viaje.sv_tramite
					INNER JOIN sv_itinerario
                    ON sv_itinerario.svi_solicitud = solicitud_viaje.sv_id					
					WHERE t_flujo = '" . FLUJO_SOLICITUD . "' 
					GROUP BY t_id
					ORDER BY t_id DESC";	        	
   			}else{
   				//query de Mis solicitudes de viaje creadas (usuario normal)
   				//tramites.t_iniciador mis solicitudes de viaje creadas 
   				//Linea 568 (etapas.et_etapa_id=".SOLICITUD_ETAPA_EN_COTIZACION.") || (etapas.et_etapa_id!=".SOLICITUD_ETAPA_EN_APROBACION.") || 
   			$query="SELECT  tramites.t_id,
					tramites.t_etiqueta,
					DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'),etapas.et_etapa_nombre,
					IF(etapas.et_etapa_id=".SOLICITUD_ETAPA_APROBADA." AND ((sv_viaje='Sencillo') OR (sv_viaje='Redondo')),IF(sv_itinerario.svi_tipo_transporte='Aéreo','AGENCIA',''),IF(etapas.et_etapa_id=".SOLICITUD_ETAPA_APROBADA." 
					AND((SELECT COUNT(DISTINCT(svi_tipo_transporte)) FROM sv_itinerario WHERE svi_solicitud = (SELECT sv_id FROM solicitud_viaje WHERE sv_tramite = tramites.t_id)) = 1),
					IF((SELECT DISTINCT(svi_tipo_transporte) FROM sv_itinerario WHERE svi_solicitud = (SELECT sv_id FROM solicitud_viaje WHERE sv_tramite = tramites.t_id)) = 'Terrestre','','AGENCIA'),
					IF(etapas.et_etapa_id=".SOLICITUD_ETAPA_APROBADA." AND (sv_itinerario.svi_tipo_transporte='Terrestre' OR sv_itinerario.svi_tipo_transporte='Aéreo'),'AGENCIA',
					IF((etapas.et_etapa_id=".SOLICITUD_ETAPA_RECHAZADA.")OR (etapas.et_etapa_id=".SOLICITUD_ETAPA_SIN_ENVIAR."),'',
					IF(tramites.t_dueno IN (SELECT au_id FROM agrupacion_usuarios),(SELECT au_nombre FROM agrupacion_usuarios AS au WHERE au.au_id=tramites.t_dueno),
					IF(tramites.t_dueno = tramites.t_iniciador AND etapas.et_etapa_id !=". SOLICITUD_ETAPA_EN_APROBACION_POR_DIRECTOR." ,'',(SELECT CONCAT(usuario.u_nombre,' ',usuario.u_paterno,' ',usuario.u_materno) FROM usuario WHERE usuario.u_id=tramites.t_dueno))))))) AS 'autorizador',
					CONCAT(FORMAT(solicitud_viaje.sv_total, 2),' MXN') AS monto, 
					(CONCAT('<p align=center><a href=./index.php?edit_view=edit_view&id=',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a></p>')) AS 'Consultar',
					IF(((etapas.et_etapa_id=".SOLICITUD_ETAPA_RECHAZADA.") || (etapas.et_etapa_id=".SOLICITUD_ETAPA_SIN_ENVIAR.") || (etapas.et_etapa_id=".SOLICITUD_ETAPA_RECHAZADA_POR_DIRECTOR.")),
					(CONCAT('<p align=center><a href=./index.php?new=new&id=',t_id,'><img border=0 title=Editar src=".$RUTA_R."images/addedit.png></a></p>')),'') AS 'Editar' ,  
					IF(((etapas.et_etapa_id=".SOLICITUD_ETAPA_CANCELADA.")  || (etapas.et_etapa_id=".SOLICITUD_ETAPA_RECHAZADA.") || (etapas.et_etapa_id=".SOLICITUD_ETAPA_SIN_ENVIAR.") || (etapas.et_etapa_id=".SOLICITUD_ETAPA_RECHAZADA_POR_DIRECTOR.")),(CONCAT('<p align=center><a href=./index.php?elimina=',t_id,' onclick=\'return confirm_del(); \'><img border=0 title=Eliminar src=".$RUTA_R."images/delete.png></a></p>')),'') AS 'Eliminar'		            
					FROM tramites
					INNER JOIN etapas
					ON tramites.t_flujo= etapas.et_flujo_id AND tramites.t_etapa_actual = etapas.et_etapa_id
					INNER JOIN usuario
					ON tramites.t_iniciador = usuario.u_id
					INNER JOIN solicitud_viaje
					ON tramites.t_id = solicitud_viaje.sv_tramite
					INNER JOIN sv_itinerario 
					ON sv_itinerario.svi_solicitud = solicitud_viaje.sv_id	
					WHERE tramites.t_iniciador=".$idUser."
					".$parametrobusqueda."  
					AND t_flujo = '" . FLUJO_SOLICITUD . "'
					GROUP BY t_id	
					ORDER BY t_id DESC";
   			}
   			
   				//funcion para eliminar
		        	if(isset($_GET['elimina'])||isset($_POST['elimina'])){
		        		$elimina=$_GET['elimina'];
		        		
		        		$cnn = new conexion();
		        		
		        		//query que eliminara tramites dependiendo al tramite seleccionado
		        		$query_elimina_tramites="DELETE FROM tramites where t_id=".$elimina."";        		
		        		//query que eliminara las observaciones de el tramite seleccionado
		        		$query_elimina_obsv="DELETE  FROM observaciones WHERE ob_tramite=".$elimina."";
		        		//query obtiene id de la solicitud de viaje respecto al tramite en cuestion*/
		        		
		        		$cnn->ejecutar($query_elimina_tramites);
		    			
		    			$cnn->ejecutar($query_elimina_obsv);
		    			
		        		//query que obtendra el id de la solicitud de viajes dependiendo a el tramite seleccionado
		        		$query_obtiene_idsv="SELECT sv_id FROM solicitud_viaje WHERE sv_tramite=".$elimina."";
		        		$res=mysql_query($query_obtiene_idsv);
		        		
		        		if($row=mysql_fetch_array($res)){
		        			$id_sv=$row[0];
		        		} 
						
		        		//query que obtendra el id de itinerarios dependiendo al id de solicitud_viaje
		        		$query_obtiene_idsvi="SELECT svi_id FROM sv_itinerario WHERE svi_solicitud=".$id_sv."";
		        		$res2=mysql_query($query_obtiene_idsvi);
		        		
		        		if($row2=mysql_fetch_array($res2)){
		        			$id_svi=$row2[0];
		        		}       		        		
		        		
		        		//query que eliminara la solicitud de viaje con la que este ligado a el tramite seleccionado
		        		$query_elimina_sv="DELETE  FROM solicitud_viaje WHERE sv_id=".$id_sv."";
		        		
		        		$cnn->ejecutar($query_elimina_sv);        		
		        		
		        		//query que eliminara el itinerario al cual este ligado a una  solicitud de viaje
		        		$query_elimina_itinerario="DELETE FROM sv_itinerario WHERE svi_id =".$id_svi."";
		        		//query que elimina la excepcion dependiendo de la solicitud de viaje elegida
		    			$query_elimina_excepcion="DELETE FROM excepciones WHERE ex_solicitud=".$id_sv."";
		    			
		    			$cnn->ejecutar($query_elimina_itinerario);
		    			$cnn->ejecutar($query_elimina_excepcion);
		    			
		    			//query que elimina el hotel dependiendo a un itinerario seleccionado*/
		    			$query_elimina_hotel="DELETE FROM hotel WHERE svi_id=".$id_svi."";
		    			
		    			$cnn->ejecutar($query_elimina_hotel);
		        	}
		        	
   				//cabeceras para las sesiones normal y administrador
		        //error_log($query);
		        $L2	= new Lista();
		        $L2->Cabeceras("Folio");
		        $L2->Cabeceras("Motivo de Viaje");
		        $L2->Cabeceras("Fecha Registro");
		        $L2->Cabeceras("Etapa");
		        $L2->Cabeceras("Autorizador");
		        $L2->Cabeceras("$ Solc.");         
		        $L2->Cabeceras("Consultar");   
		        $L2->Cabeceras("Editar");
		        $L2->Cabeceras("Eliminar");    
		        //$L2->Herramientas("D","./index.php?edit_view=edit_view&id=");
		        //se  hace llamado a la funcion eliminar
		   		eliminar();		  
		        $L2->muestra_lista($query,0,false,-1,"",10,"solviaje");		        
   		}       
        
		//
		        //
		        //     Solicitudes pendientes de Aprobar
		        //        
				
				//Se checa si el usuario pertenece a Controlling o Finanzas
				$parametros = "";
				$agrup_usu = new AgrupacionUsuarios();
				if($agrup_usu->Load_Homologacion_Dueno_By_u_ID($_SESSION["idusuario"])){
					$idAgrupacion = $agrup_usu->Get_dato("hd_au_id");
					if($idAgrupacion != ""){
						$parametros = ", ".$idAgrupacion;
					}
				}
		//        
		        echo "<br><br>";
		        if($_SESSION["perfil"]!=3){
			        echo "<h1>Solicitudes Pendientes de mi Aprobaci&oacute;n </h1>";
			        echo "<form action='index.php' method='post' name='comprobacionesaprobs'>";
			
			        $L	= new Lista();
			        $L->Cabeceras("Folio");
			        $L->Cabeceras("Motivo de Viaje");
			        $L->Cabeceras("Fecha Registro");
			        $L->Cabeceras("Etapa");
			        $L->Cabeceras("Solicitante");
			        $L->Cabeceras("$ Solc.");             
			        $L->Herramientas("S","./index.php?edit_view=edit_view&id=");
			        
			       if($_SESSION["perfil"]==2){
			       	
				       	$query="SELECT t.t_id,
				        t.t_etiqueta,
				        DATE_FORMAT(t.t_fecha_registro,'%d/%m/%Y'),
				        e.et_etapa_nombre,
				        (IF(t_delegado = 0, (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id=t_iniciador), (CONCAT((SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_delegado), ' EN NOMBRE DE: ', (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_iniciador))))) AS solicitante,
				        CONCAT(FORMAT(solicitud_viaje.sv_total, 2),' MXN')
						FROM tramites as t
						INNER JOIN solicitud_viaje as sv
						ON t.t_id = sv.sv_tramite 
						INNER JOIN usuario as u
						ON t.t_iniciador = u.u_id
						INNER JOIN empleado as em
						ON u.u_id = em.idempleado
						INNER JOIN etapas as e
						ON t.t_flujo = e.et_flujo_id and t.t_etapa_actual = e.et_etapa_id					
						WHERE t_flujo = '" . FLUJO_SOLICITUD . "' 
						AND t_iniciador != '".$_SESSION["idusuario"]."' 
						AND t_etapa_actual = '" . SOLICITUD_ETAPA_EN_APROBACION . "'
						ORDER BY t_id desc";		       	
				       	
			       }else{
			      
						$user = "";
						if($_SESSION["perfil"] == 6){
							$user = "2000";
						}else if($_SESSION["perfil"] == 5){
							$user = "1000";
						}else if($_SESSION["perfil"] == 10){
							$user = "4000";
						}else if($_SESSION["perfil"] == 9){
							$user = "6000";
						}else{
							$user = $idUser;
						}
						
						$query="SELECT t.t_id,
						t.t_etiqueta,
						DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'),
						e.et_etapa_nombre,
						(IF(t_delegado = 0, (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id=t_iniciador), (CONCAT((SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_delegado), ' EN NOMBRE DE: ', (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_iniciador))))) AS solicitante,
						CONCAT(FORMAT(sv.sv_total, 2),' MXN')
						FROM tramites as t
						INNER JOIN solicitud_viaje as sv
						ON t.t_id = sv.sv_tramite 
						INNER JOIN usuario as u
						ON t.t_iniciador = u.u_id
						INNER JOIN empleado as em
						ON u.u_id = em.idempleado
						INNER JOIN etapas as e
						ON t.t_flujo = e.et_flujo_id and t.t_etapa_actual = e.et_etapa_id
						WHERE t_dueno = '{$user}' 
						/*WHERE t_dueno in ($idUser $parametros)*/
						AND t_flujo = '" . FLUJO_SOLICITUD . "' 
						AND((t_etapa_actual = '" . SOLICITUD_ETAPA_EN_APROBACION . "') OR (t_etapa_actual = '" . SOLICITUD_ETAPA_SEGUNDA_APROBACION . "'))
						AND t_iniciador != '".$idUser."' 
						ORDER BY t_id desc";
			       } 
					$L->muestra_lista($query,0,false,-1,"",10,"solviajeAut");    
					echo "</form>";
			}
        $I->Footer();

    }//fin del else de  busqueda
    
}//else de amex.

