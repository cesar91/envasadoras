<?php
	session_start();
	require_once("../../lib/php/constantes.php");
	require_once "$RUTA_A/Connections/fwk_db.php";
	require_once "$RUTA_A/functions/utils.php";
	
	$idUser = $_SESSION["idusuario"];
	$noEmpleado = $_SESSION["idusuario"];
	
	function eliminar(){
	?><!-- CHIPOTLE'S SOLUTION  -->
		<script language="JavaScript" src="js/utilities.js" type="text/javascript"></script> 
		<script>
			
			// Comprobaciones de Viaje
			function confirm_del(){
				var valor = (confirm(arrayAcentos["Int"]+"Realmente desea eliminar la comprobaci"+arrayAcentos["o"]+"n de viaje?")) ? true : false ;
				return  valor;
			}

			// Comprobaciones de Invitaciï¿½n (exclusivas)
			function confirm_del_inv(){
				var valor = (confirm(arrayAcentos["Int"]+"Realmente desea eliminar la comprobación de gastos?")) ? true : false ;
				return  valor;
			}
		</script>
	<?php
	}
	
	function imprime_mensajes(){
		//Mensajes 
		if(isset($_GET["oksaveP"])){
			echo "<h3><img src='../../images/ok.png' width='20' height='20' />El previo de la solicitud se guard&oacute; correctamente.</h3>";
		}elseif(isset($_GET["errsaveP"])){
			echo "<font color='#FF0000'>
			<img src='../../images/action_cancel.gif' width='22' height='22' /><b>Se encontraron errores, el previo de la solicitud no se ha guardado. Verifique e intente de nuevo.</b></font>";
		}elseif(isset($_GET["oksave"])){ 
			echo "<h3><img src='../../images/ok.png' width='20' height='20' />La comprobaci&oacute;n se guard&oacute; correctamente, pasa a la siguiente etapa para su aprobaci&oacute;n.</h3>";
		}elseif(isset($_GET["oksaveprev"])){ 
			echo "<h3><img src='../../images/ok.png' width='20' height='20' />El previo de la comprobaci&oacute;n se guard&oacute; correctamente.</h3>";
		}elseif(isset($_GET["okAut"])){ 
			echo "<h3><img src='../../images/ok.png' width='20' height='20' />La comprobaci&oacute;n se ha aprobado.</h3>";
		}elseif(isset($_GET["okSup"])){ 
			echo "<h3><img src='../../images/ok.png' width='20' height='20' />La comprobaci&oacute;n ha sido enviada al supervisor de finanzas.</h3>";
		}elseif (isset($_GET["regresar"])) {
			echo "<font color='#FF0000'><b>La comprobaci&oacute;n se guard&oacute; correctamente.</b></font>";
		} elseif (isset($_GET["errsave"])) {
			echo "<font color='#FF0000'><b>La comprobaci&oacute;n se regreso correctamente.</b></font>";
		} elseif (isset($_GET["errsaveAut"])) {
			echo "<font color='#FF0000'><b>Se encontraron errores, la comprobaci&oacute;n no se ha guardado. Verifique e intente de nuevo. " . base64_decode($_GET['msg']) . "</b></font>";
		} elseif (isset($_GET["oksaveEx"])) {
			echo "<font color='#FF0000'><b>Se ha autorizado el excedente de la comprobaci&oacute;n.</b></font>";
		} elseif (isset($_GET["errjefe"])) {
			echo "<font color='#FF0000'><b>No tiene asignado un grupo de aprobador. ";
			if (isset($_GET['msg']))
				echo base64_decode($_GET['msg']);
			echo "</b></font>";
		}elseif (isset($_GET["erramnt"])) {
			echo "<font color='#FF0000'><b>Se encontraron errores en los montos, la comprobaci&oacute;n no se ha guardado. Verifique e intente de nuevo. " . base64_decode($_GET['msg']) . "</b></font>";
		}elseif (isset($_GET["errAut"])){
			echo "<font color='#FF0000'><b>Se encontr&oacute; un error al Reasignar el CECO de la Comprobaci&oacute;n. Verifique e intente de nuevo. </b></font>";;
			if (isset($_GET['msg']))
				echo base64_decode($_GET['msg']);
			echo "</b></font>";
		}elseif (isset($_GET["action"])) {
			if ($_GET["action"] == "autorizar"){
				echo "<font color='#00B000'><b>La comprobaci&oacute;n se ha aprobado.</b></font>";
			}elseif ($_GET["action"] == "aprobar"){
				echo "<font color='#00B000'><b>Las comprobaciones se han validado.</b></font>";
			}elseif ($_GET["action"] == "devolver"){
				echo "<font color='#00B000'><b>La comprobaci&oacute;n ha sido devuelta con observaciones al empleado.";
				if (isset($_GET['msg']))
					echo base64_decode($_GET['msg']);
				echo "</b></font>";
			}elseif ($_GET["action"] == "rechazar"){
				echo "<font color='#FF0000'><b>La comprobaci&oacute;n fue rechazada. ";
				if (isset($_GET['msg']))
					echo base64_decode($_GET['msg']);
				echo "</b></font>";
			}elseif ($_GET["action"] == "envia") {
				echo "<font color='#00B000'><b>La comprobaci&oacute;n ha sido enviada al Supervisor/Gerente de Finanzas.";
				if (isset($_GET['msg']))
					echo base64_decode($_GET['msg']);
				echo "</b></font>";
			}
		}
	}

	// Permite ver el detalle de una comprobacion sin edicion
	if (isset($_GET["view"])){
		$I = new Interfaz("Comprobaciones", true);
		require_once("comprobacion_gastos_view.php");
		$I->Footer();
	}elseif(isset($_GET["view_travel"])){
		$I = new Interfaz("Comprobaciones", true);
		//ventana para Controlling,Finanzas y Gerente/Director de area
		require_once("comprobacion_travel_view.php");
		$I->Footer();
	}elseif(isset($_GET["view_n"])){
		 $I = new Interfaz("Comprobaciones", true);
		//ventana para consulta normal de comprobacion
		require_once("comprobacion_travel_viewN.php");
		$I->Footer();
	}elseif(isset($_GET['edit_view'])){
		// Autorizacion de la Comprobacion
		$I = new Interfaz("Comprobaciones",true);
		$perfil = $_SESSION['perfil'];
		if($perfil == FINANZAS){
			require_once("comprobacion_gastos_view_finanzas.php");
		}else{
			require_once("comprobacion_gastos_view.php");
		}
		$I->Footer();
	}elseif(isset($_GET["id"]) ){
		// Carga los datos del tramite para saber a que etapa nos debemos de diriguir
		$Tramite = new Tramite();
		$idTramite = $_GET['id'];
		$Tramite->Load_Tramite($idTramite);
		$t_flujo = $Tramite->Get_dato("t_flujo");
		$t_etapa_actual = $Tramite->Get_dato("t_etapa_actual");
		if($t_etapa_actual==COMPROBACION_ETAPA_APROBACION){
			// TODO: Se tienen que indexar por flujo			
			$I = new Interfaz("Comprobaciones", true);
			require_once("comprobacion_action.php");
			$I->Footer();				
		}
	// Comprobación de Solicitud de Gstos
	}elseif(isset($_GET['comp_solicitud'])){
		$I  = new Interfaz("Comprobaciones",true);
		require_once("comprobacion_gastos.php");
		$I->Footer();
	} else if (isset($_GET["new"])) {
		$I = new Interfaz("Comprobaciones", true);
		require_once("comprobacion_travel.php");
		$I->Footer();
	} else if (isset($_GET["test"])) {
		$I = new Interfaz("Comprobaciones", true);
		require_once("comprobacion_travel2.php");
		$I->Footer();
	}elseif (isset($_POST['enviarcinv'])){
		// Validar Comprobaciones de Invitaciï¿½n por el Supervisor/Gerente de Finanzas
		$tramites_aceptados = $_POST['tramites_aceptados'];
		$tramites_rechazados = $_POST['tramites_rechazados'];
		$perfil = $_POST['perfil'];
		$tramites_a_aprobar = array();
		$tramites_a_rechazar = array();
		$no_tramites_a_aprobar = 0;
		$no_tramites_a_rechazar = 0;
		
		$cnn = new conexion();
		$tramite = new Tramite();
		$rutaAuto = new RutaAutorizacion();
		
		if($perfil == GERENTE_FINANZAS){
			$validador = "Gerente";
		}else{
			$validador = "Supervisor";
		}
			
		// Tramites para aprobar 
		//error_log("-------------->>>>>>>>>>>>>>>tramites a aprobar: ".$tramites_aceptados);
		if($tramites_aceptados != 0){
			$tramites_a_aprobar = explode('|', $tramites_aceptados);
			$no_tramites_a_aprobar = count($tramites_a_aprobar);
			
			for($i=0; $i < (int)$no_tramites_a_aprobar; $i++){
				if($tramites_a_aprobar[$i] != ""){
					//error_log($tramites_a_aprobar[$i]);
					$idT = $tramites_a_aprobar[$i];
					$t_dueno = $rutaAuto->getDueno($idT);
					$tramite->Modifica_Etapa($idT, COMPROBACION_GASTOS_ETAPA_APROBADA_POR_SUPERVISOR_FINANZAS, FLUJO_COMPROBACION_GASTOS, "2000", "");
					
					//Envia notificacion al Supervisor/Gerente de Finanzas de la solicitud de invitaciï¿½n ----------------------------------
					$mensaje = sprintf("La Comprobaci&oacute;n de Gastos <strong>%05s</strong> ha sido <strong>APROBADA</strong> por el %s de Finanzas.", $idT, $validador);
					//error_log($mensaje);
					$remitente = $t_dueno;
					$destinatario = "2000";
					$tramite->EnviaNotificacion($idT, $mensaje, $remitente, $destinatario, "0", ""); //false para no enviar email
				}
			}
		}
		
		// Tramites a rechazar	
		//error_log("-------------->>>>>>>>>>>>>>>tramites a rechazar: ".$tramites_rechazados);
		if($tramites_rechazados != 0){
			$tramites_a_rechazar = explode('|', $tramites_rechazados);
			$no_tramites_a_rechazar = count($tramites_a_rechazar);
			
			for($i=0; $i < (int)$no_tramites_a_rechazar; $i++){
				if($tramites_a_rechazar[$i] != ""){
					//error_log($tramites_a_rechazar[$i]);
					$idT = $tramites_a_rechazar[$i];
					$t_dueno = $rutaAuto->getDueno($idT);
					$tramite->Modifica_Etapa($idT, COMPROBACION_GASTOS_ETAPA_RECHAZADA_POR_SUPERVISOR_FINANZAS, FLUJO_COMPROBACION_GASTOS, "2000", "");
						
					//Envia notificacion al Supervisor/Gerente de Finanzas de la solicitud de invitaciï¿½n ----------------------------------
					$mensaje = sprintf("La Comprobaci&oacute;n de Gastos <strong>%05s</strong> ha sido <strong>RECHAZADA</strong> por el %s de Finanzas.", $idT, $validador);
					//error_log($mensaje);
					$remitente = $t_dueno;
					$destinatario = "2000";
					$tramite->EnviaNotificacion($idT, $mensaje, $remitente, $destinatario, "0", ""); //false para no enviar email
				}
			}
		}
		
		header("Location: ./index.php?action=aprobar");
		
	}elseif(isset($_POST['enviarCV'])){
		// Validar Comprobaciones de Viaje por el Supervisor/Gerente de Finanzas
		$tramites_aceptados = $_POST['tramites_aceptadosCV'];
		$tramites_rechazados = $_POST['tramites_rechazadosCV'];
		$perfil = $_POST['perfil'];
		$tramites_a_aprobar = array();
		$tramites_a_rechazar = array();
		$no_tramites_a_aprobar = 0;
		$no_tramites_a_rechazar = 0;
		
		$cnn = new conexion();
		$tramite = new Tramite();
		$rutaAuto = new RutaAutorizacion();
		
		if($perfil == GERENTE_FINANZAS){
			$validador = "Gerente";
		}else{
			$validador = "Supervisor";
		}
		
		// Tramites para aprobar
		//error_log("-------------->>>>>>>>>>>>>>>tramites a aprobar: ".$tramites_aceptados);
		if($tramites_aceptados != 0){
			$tramites_a_aprobar = explode('|', $tramites_aceptados);
			$no_tramites_a_aprobar = count($tramites_a_aprobar);
			
			for($i=0; $i < (int)$no_tramites_a_aprobar; $i++){
				if($tramites_a_aprobar[$i] != ""){
					//error_log($tramites_a_aprobar[$i]);
					$idT = $tramites_a_aprobar[$i];
					$t_dueno = $rutaAuto->getDueno($idT);
					$tramite->Modifica_Etapa($idT, COMPROBACION_ETAPA_APROBADA_POR_SF, FLUJO_COMPROBACION, "2000", "");
		
					//Envia notificacion al Supervisor/Gerente de Finanzas de la solicitud de invitaciï¿½n ----------------------------------
					$mensaje = sprintf("La Comprobaci&oacute;n de Viaje <strong>%05s</strong> ha sido <strong>APROBADA</strong> por el %s de Finanzas.", $idT, $validador);
					//error_log($mensaje);
					$remitente = $t_dueno;
					$destinatario = "2000";
					$tramite->EnviaNotificacion($idT, $mensaje, $remitente, $destinatario, "0", ""); //false para no enviar email
				}
			}
		}
		
		// Tramites a rechazar
		//error_log("-------------->>>>>>>>>>>>>>>tramites a rechazar: ".$tramites_rechazados);
		if($tramites_rechazados != 0){
			$tramites_a_rechazar = explode('|', $tramites_rechazados);
			$no_tramites_a_rechazar = count($tramites_a_rechazar);

			for($i=0; $i < (int)$no_tramites_a_rechazar; $i++){
				if($tramites_a_rechazar[$i] != ""){
					//error_log($tramites_a_rechazar[$i]);
					$idT = $tramites_a_rechazar[$i];
					$t_dueno = $rutaAuto->getDueno($idT);
					$tramite->Modifica_Etapa($idT, COMPROBACION_ETAPA_RECHAZADA_POR_SF, FLUJO_COMPROBACION, "2000", "");
						
					//Envia notificacion al Supervisor/Gerente de Finanzas de la solicitud de invitaciï¿½n ----------------------------------
					$mensaje = sprintf("La Comprobaci&oacute;n de Viaje <strong>%05s</strong> ha sido <strong>RECHAZADA</strong> por el %s de Finanzas.", $idT, $validador);
					//error_log($mensaje);
					$remitente = $t_dueno;
					$destinatario = "2000";
					$tramite->EnviaNotificacion($idT, $mensaje, $remitente, $destinatario, "0", ""); //false para no enviar email
				}
			}
		}
		
		header("Location: ./index.php?action=aprobar");
	}
	
	//
	//      Comprobaciones de Gastos
	//	
	elseif (isset($_GET['type']) && $_GET['type']=='4' || isset($_POST['type']) && $_POST['type']=='4'){
		if(isset($_GET['delsol'])||isset($_POST['delsol'])){
			$id_solicitud=$_GET['delsol'];
			$cnn = new conexion();
			//query que tomara el id de la factura
			$query_obtiene_idFact="SELECT DISTINCT(id_factura) as id_factura FROM detalle_comprobacion_gastos WHERE dc_comprobacion= (select co_id from comprobacion_gastos where co_mi_tramite = ".$id_solicitud.")";
			$resFact = $cnn->consultar($query_obtiene_idFact);
			while($rowFact = mysql_fetch_array($resFact)){
				$id_fact[] = $rowFact['id_factura'];
			}
			foreach($id_fact as $idf){
				if($idf != ""){
					$query_elimina_factura="DELETE FROM factura where id_factura=".$idf."";
					$cnn->ejecutar($query_elimina_factura);
				}
			}
			$query_desmarcar_cargos_amex="update amex set estatus = '0', comprobacion_id = '0' where comprobacion_id = (select co_id from comprobacion_gastos where co_mi_tramite = '".$id_solicitud."')";
			$query_detalle_solicitud_invitacion="delete from detalle_comprobacion_gastos where dc_comprobacion= (select co_id from comprobacion_gastos where co_mi_tramite = ".$id_solicitud.")";
			$query_solicitud_invitacion="delete from comprobacion_gastos where co_mi_tramite=".$id_solicitud;
			$query_tramite="delete from tramites where t_id=".$id_solicitud;
			//error_log("----".$query_desmarcar_cargos_amex);
			$cnn->ejecutar($query_desmarcar_cargos_amex);
			$cnn->ejecutar($query_detalle_solicitud_invitacion);
			$cnn->ejecutar($query_solicitud_invitacion);
			$cnn->ejecutar($query_tramite);
		}
		
		if(isset($_REQUEST["buscar"])){
			//
			//     busqueda
			//   
		$busqueda_valueCG = "";
		$etapa = "";
		
			if (isset($_REQUEST['et_etapa_id']))
				$etapa = $_REQUEST['et_etapa_id'];

			$parametros = "";
			if (isset($_REQUEST["noComp"]) && !empty($_REQUEST["noComp"])) {
				$parametros .= " and t_id=" . $_REQUEST["noComp"];
				$busqueda_valueCG .= "&noComp=$noComp";
			}

			// Etapa
			if ($etapa >= 0 && $etapa != '') {
				$parametros.=" AND t_etapa_actual = ".$etapa;
				$busqueda_valueCG .= "&et_etapa_id=$etapa";
			}

			// fecha inicial
			if (isset($_REQUEST["finicial"]) && !empty($_REQUEST["finicial"])) {
				$date = explode("/", $_REQUEST["finicial"]);
				if(count($date)!=3){
					$date_db = $_REQUEST["finicial"];
				}else{
					$date_db = $date[2]."-".$date[1]."-".$date[0];
				}            
				$parametros .= " and t_fecha_registro >= '" . $date_db . " 00:00:00'";
				$busqueda_valueCG .= "&finicial=$date_db";
			}

			// fecha final
			if (isset($_REQUEST["ffinal"]) && !empty($_REQUEST["ffinal"])) {
				$date = explode("/", $_REQUEST["ffinal"]);
				if(count($date)!=3){
					$date_db = $_REQUEST["ffinal"];
				}else{
					$date_db = $date[2]."-".$date[1]."-".$date[0];
				}            
				$parametros .= " and t_fecha_registro <= '" . $date_db . " 23:59:59'";
				$busqueda_valueCG .= "&ffinal=$date_db";
			}
			
			//error_log(">>>>>>>>>>>>>>>>>>>>>>URL: ".$busqueda_valueCG);
			
			$I = new Interfaz("Comprobaciones", true);
			$L2 = new Lista("buscar&docs=docs&type=4".$busqueda_valueCG);
			//$L2->type = "&type=2";
			$L2->Cabeceras("Folio");
			$L2->Cabeceras("Motivo");
			$L2->Cabeceras("Fecha Registro");
			$L2->Cabeceras("Etapa");
			$L2->Cabeceras("Solicitante");
			$L2->Cabeceras("Responsable");
			$L2->Cabeceras("Total Comprobado", "", "number", "R");
			$L2->Cabeceras("Divisa");
			$L2->Cabeceras("Consultar","","text","C");
			$L2->Cabeceras("Editar","","text","C");
			$L2->Cabeceras("Eliminar","","text","C");
			//$L2->Herramientas("S", "./index.php?view=view&id=");
			eliminar();
			
			if ($_SESSION["perfil"] == 2) {
				$query = "SELECT t_id, t_etiqueta, DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'), et_etapa_nombre, 
					IF(t_dueno = t_iniciador && t_etapa_actual = ".COMPROBACION_GASTOS_ETAPA_SIN_ENVIAR.",'',IF(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_SIN_ENVIAR."' OR t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_APROBADA."' OR t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_RECHAZADA."' OR t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_DEVUELTA_CON_OBSERVACIONES."' OR t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_RECHAZADA_POR_DIRECTOR."','',(
					CASE t_dueno 
					WHEN '1000' THEN 'CONTROLLING'
					WHEN '2000' THEN 'FINANZAS'
					ELSE (SELECT nombre FROM empleado WHERE idfwk_usuario = t_dueno)
					END))) AS atiende, 
					co_total,
					(SELECT div_nombre FROM divisa WHERE div_id = dc_divisa) AS divisa, 
					CONCAT('<a href=./index.php?view=view&id=',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a>') AS consultar, 
					IF(((t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_APROBACION."')||(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_APROBADA."')||(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_APROBACION_SUPERVISOR_FINANZAS."')||(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_APROBADA_POR_SUPERVISOR_FINANZAS."')||(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_RECHAZADA_POR_SUPERVISOR_FINANZAS."')||(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_EN_APROBACION_POR_DIRECTOR."')),'',(CONCAT('<a href=./index.php?docs=docs&type=2&comp_solicitud=comp_solicitud&idcg=',t_id,'><img border=0 title=Editar src=".$RUTA_R."images/addedit.png></a>'))) AS editar,
					IF((t_etapa_actual = '".COMPROBACION_GASTOS_ETAPA_SIN_ENVIAR."' || t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_RECHAZADA."' || t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_RECHAZADA_POR_DIRECTOR."'),(CONCAT('<a href=./index.php?docs=docs&type=4&delsol=',t_id,' onclick=\'return confirm_del_inv(); \'><img border=0 title=Eliminar src=".$RUTA_R."images/delete.png></a>')),'') AS eliminar  
					FROM tramites AS t
					INNER JOIN comprobacion_gastos AS ci ON ci.co_mi_tramite=t.t_id
					INNER JOIN etapas AS et ON (et.et_etapa_id = t.t_etapa_actual AND et.et_flujo_id = t.t_flujo)
					INNER JOIN detalle_comprobacion_gastos ON dc_comprobacion = ci.co_id 
					INNER JOIN divisa ON dc_divisa = div_id 
					WHERE t_flujo = '" . FLUJO_COMPROBACION_GASTOS . "' ".$parametros." 
					GROUP BY t_id 
					ORDER BY t_id desc";
			} else {
				//Se inicializa la busqueda
				$query = "SELECT t_id, t_etiqueta, DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'), et_etapa_nombre, (SELECT nombre FROM empleado WHERE idfwk_usuario = t_iniciador) AS solicitante, 
					IF(t_dueno = t_iniciador && t_etapa_actual = ".COMPROBACION_GASTOS_ETAPA_SIN_ENVIAR.",'',IF(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_SIN_ENVIAR."' OR t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_APROBADA."' OR t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_RECHAZADA."' OR t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_DEVUELTA_CON_OBSERVACIONES."' OR t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_RECHAZADA_POR_DIRECTOR."','',(
					CASE t_dueno 
					WHEN '1000' THEN 'CONTROLLING'
					WHEN '2000' THEN 'FINANZAS'
					ELSE (SELECT nombre FROM empleado WHERE idfwk_usuario = t_dueno)
					END))) AS atiende,  
					co_total,
					(SELECT div_nombre FROM divisa WHERE div_id = dc_divisa) AS divisa, 
					CONCAT('<a href=./index.php?view=view&id=',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a>') AS consultar, 
					IF(((t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_APROBACION."')||(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_APROBADA."')||(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_APROBACION_SUPERVISOR_FINANZAS."')||(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_APROBADA_POR_SUPERVISOR_FINANZAS."')||(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_RECHAZADA_POR_SUPERVISOR_FINANZAS."')||(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_EN_APROBACION_POR_DIRECTOR."')),'',(CONCAT('<a href=./index.php?docs=docs&type=2&comp_solicitud=comp_solicitud&idcg=',t_id,'><img border=0 title=Editar src=".$RUTA_R."images/addedit.png></a>'))) AS editar,
					IF((t_etapa_actual = '".COMPROBACION_GASTOS_ETAPA_SIN_ENVIAR."' || t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_RECHAZADA."' || t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_RECHAZADA_POR_DIRECTOR."'),(CONCAT('<a href=./index.php?docs=docs&type=4&delsol=',t_id,' onclick=\'return confirm_del_inv(); \'><img border=0 title=Eliminar src=".$RUTA_R."images/delete.png></a>')),'') AS eliminar  
					FROM tramites AS t
					INNER JOIN comprobacion_gastos AS ci ON ci.co_mi_tramite=t.t_id
					INNER JOIN etapas AS et ON (et.et_etapa_id = t.t_etapa_actual AND et.et_flujo_id = t.t_flujo)
					INNER JOIN detalle_comprobacion_gastos ON dc_comprobacion = ci.co_id 
					INNER JOIN divisa ON dc_divisa = div_id 
					WHERE t_iniciador = '".$idUser."' 
					AND t_flujo = '".FLUJO_COMPROBACION_GASTOS."' ". $parametros." 
					GROUP BY t_id 
					ORDER BY t_id DESC";
			}// si el tipo de usuario es administrador(busqueda)
			//error_log($query);
			include("../../lib/php/mnu_toolbar.php");
			comprobacion_toolbar($noEmpleado);
			busca_comprobacion(FLUJO_COMPROBACION_GASTOS);
			?>
			<h1>Resultado b&uacute;squeda de Comprobaciones</h1> 
			<?php
			$L2->muestra_lista($query,0,false,-1,"",10,"compgtsBusq");
			//$L2->muestra_lista($query, 0);
			$I->Footer();
		} else { // termina la busqueda 

			//
			//     Mis comprobaciones de viajes
			//
			$I = new Interfaz("Comprobaciones", true);
			$L2 = new Lista("docs=docs&type=4");
			$L2->Cabeceras("Folio");
			$L2->Cabeceras("Motivo");
			$L2->Cabeceras("Fecha Registro");
			$L2->Cabeceras("Etapa");
			$L2->Cabeceras("Autorizador");
			$L2->Cabeceras("Total Comprobado", "", "number", "R");
			$L2->Cabeceras("Divisa");
			$L2->Cabeceras("Consultar","","text","C");
			$L2->Cabeceras("Editar","","text","C");
			$L2->Cabeceras("Eliminar","","text","C");
			//$L2->Herramientas("S", "./index.php?view=view&id=");
			eliminar();
			include("../../lib/php/mnu_toolbar.php");
			comprobacion_toolbar($noEmpleado);
			
			if($_SESSION["perfil"]==5 || $_SESSION["perfil"]==6){
				imprime_mensajes();	
			}
			
			if($_SESSION["perfil"]!=5 && $_SESSION["perfil"]!=6){
				busca_comprobacion(FLUJO_COMPROBACION_GASTOS);
				imprime_mensajes();
				echo "</div>";
				echo "<h1>Mis solicitudes de gastos comprobadas</h1>";
				if ($_SESSION["perfil"] == 2) {
						$query = "SELECT t_id, t_etiqueta, DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'), et_etapa_nombre,
								IF(t_dueno = t_iniciador && t_etapa_actual = ".COMPROBACION_GASTOS_ETAPA_SIN_ENVIAR.",'',IF(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_SIN_ENVIAR."' OR t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_APROBADA."' OR t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_RECHAZADA."' OR t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_DEVUELTA_CON_OBSERVACIONES."' OR t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_RECHAZADA_POR_DIRECTOR."','',(
								CASE t_dueno 
								WHEN '1000' THEN 'CONTROLLING'
								WHEN '2000' THEN 'FINANZAS'
								ELSE (SELECT nombre FROM empleado WHERE idfwk_usuario = t_dueno)
								END))) AS atiende,
								co_total,
								(SELECT div_nombre FROM divisa WHERE div_id = dc_divisa) AS divisa, 
								CONCAT('<a href=./index.php?view=view&id=',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a>') AS consultar, 
								IF(((t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_APROBACION."')||(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_APROBADA."')||(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_APROBACION_SUPERVISOR_FINANZAS."')||(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_APROBADA_POR_SUPERVISOR_FINANZAS."')||(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_RECHAZADA_POR_SUPERVISOR_FINANZAS."')||(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_EN_APROBACION_POR_DIRECTOR."')),'',(CONCAT('<a href=./index.php?docs=docs&type=2&comp_solicitud=comp_solicitud&idcg=',t_id,'><img border=0 title=Editar src=".$RUTA_R."images/addedit.png></a>'))) AS editar,
								IF((t_etapa_actual = '".COMPROBACION_GASTOS_ETAPA_SIN_ENVIAR."' || t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_RECHAZADA."' || t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_RECHAZADA_POR_DIRECTOR."'),(CONCAT('<a href=./index.php?docs=docs&type=4&delsol=',t_id,' onclick=\'return confirm_del_inv(); \'><img border=0 title=Eliminar src=".$RUTA_R."images/delete.png></a>')),'') AS eliminar  
								FROM tramites AS t
								INNER JOIN comprobacion_gastos AS ci ON ci.co_mi_tramite=t.t_id 
								INNER JOIN empleado AS e ON t.t_iniciador = e.idfwk_usuario 
								LEFT JOIN empleado AS ee 
								/*inner join agrupacion_usuarios as au
								on (t.t_dueno = ee.idfwk_usuario or t.t_dueno = au.au_id)*/
								ON t.t_dueno = ee.idfwk_usuario
								INNER JOIN etapas AS et ON (et.et_etapa_id = t.t_etapa_actual AND et.et_flujo_id = t.t_flujo)
								INNER JOIN detalle_comprobacion_gastos ON dc_comprobacion = ci.co_id 
								INNER JOIN divisa ON dc_divisa = div_id 
								WHERE t_flujo = '".FLUJO_COMPROBACION_GASTOS."' 
								GROUP BY t_id 
								ORDER BY t_id DESC";
				} else {
					$parametrobusqueda = "";
					//Se inicializa la busqueda
					
					if(!isset($_SESSION['idrepresentante'])){
						$parametrobusqueda = " AND t_etapa_actual != '".COMPROBACION_GASTOS_ETAPA_RECHAZADA_POR_DIRECTOR."' ";
					}
					
					$query = "SELECT DISTINCT t_id, t_etiqueta, DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'), et_etapa_nombre,
							IF(t_dueno = t_iniciador && t_etapa_actual = ".COMPROBACION_GASTOS_ETAPA_SIN_ENVIAR.",'',IF(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_SIN_ENVIAR."' OR t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_APROBADA."' OR t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_RECHAZADA."' OR t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_DEVUELTA_CON_OBSERVACIONES."' OR t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_RECHAZADA_POR_DIRECTOR."','',(
							CASE t_dueno 
							WHEN '1000' THEN 'CONTROLLING'
							WHEN '2000' THEN 'FINANZAS'
							WHEN '6000' THEN 'AUTORIZADOR GENERAL'
							ELSE (SELECT nombre FROM empleado WHERE idfwk_usuario = t_dueno)
							END))) AS atiende,
							co_anticipo_comprobado,
							(SELECT div_nombre FROM divisa WHERE div_id = dc_divisa) AS divisa,
							CONCAT('<a href=./index.php?view=view&id=',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a>') AS consultar, 
							IF(((t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_APROBACION."')||(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_APROBADA."')||(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_APROBACION_SUPERVISOR_FINANZAS."')||(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_APROBADA_POR_SUPERVISOR_FINANZAS."')||(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_RECHAZADA_POR_SUPERVISOR_FINANZAS."')||(t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_EN_APROBACION_POR_DIRECTOR."')),'',(CONCAT('<a href=./index.php?docs=docs&type=2&comp_solicitud=comp_solicitud&idcg=',t_id,'><img border=0 title=Editar src=".$RUTA_R."images/addedit.png></a>'))) AS editar,
							IF((t_etapa_actual = '".COMPROBACION_GASTOS_ETAPA_SIN_ENVIAR."' || t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_RECHAZADA."' || t_etapa_actual='".COMPROBACION_GASTOS_ETAPA_RECHAZADA_POR_DIRECTOR."'),(CONCAT('<a href=./index.php?docs=docs&type=4&delsol=',t_id,' onclick=\'return confirm_del_inv(); \'><img border=0 title=Eliminar src=".$RUTA_R."images/delete.png></a>')),'') AS eliminar  
							FROM tramites AS t
							INNER JOIN comprobacion_gastos AS ci ON ci.co_mi_tramite=t.t_id
							#inner join empleado as e on t.t_iniciador = e.idfwk_usuario
							#inner join empleado as ee on t.t_dueno = ee.idfwk_usuario or t.t_dueno = '1000' or t.t_dueno = '2000'
							INNER JOIN etapas AS et ON (et.et_etapa_id = t.t_etapa_actual AND et.et_flujo_id = t.t_flujo)
							INNER JOIN detalle_comprobacion_gastos ON dc_comprobacion = ci.co_id 
							LEFT JOIN divisa ON dc_divisa = div_id 
							WHERE t_iniciador = '".$idUser."'  
							".$parametrobusqueda." 
							AND t_flujo = '".FLUJO_COMPROBACION_GASTOS."'  
							GROUP BY t_id 
							ORDER BY t_id DESC";
				}
				$L2->muestra_lista($query,0,false,-1,"",10,"compgts");
				//$L2->muestra_lista($query, 0);
			}

			//
			//     Comprobaciones pendientes de Aprobar
			//        
			
			if($_SESSION["perfil"]!=3){
				//Se checa si el usuario pertenece a Controlling o Finanzas
				$parametros = "";
				$agrup_usu = new AgrupacionUsuarios();
				if($agrup_usu->Load_Homologacion_Dueno_By_u_ID($_SESSION["idusuario"])){
					$idAgrupacion = $agrup_usu->Get_dato("hd_au_id");
					if($idAgrupacion != ""){
						$parametros = ", ".$idAgrupacion;
					}
				}
				
			$tipo_usuario = "";
			if($_SESSION["perfil"] == 6){
				$tipo_usuario = "t_dueno = '2000' AND (t_etapa_actual = '".COMPROBACION_GASTOS_ETAPA_APROBACION."' OR t_etapa_actual = '".COMPROBACION_GASTOS_ETAPA_APROBADA_POR_SUPERVISOR_FINANZAS."' OR t_etapa_actual = '".COMPROBACION_GASTOS_ETAPA_RECHAZADA_POR_SUPERVISOR_FINANZAS."') ";
			}else if($_SESSION["perfil"] == 5){
				$tipo_usuario = "t_dueno = '1000' AND (t_etapa_actual = '".COMPROBACION_GASTOS_ETAPA_APROBACION."') ";
			}elseif($_SESSION["perfil"] == 10){
				$tipo_usuario = "t_dueno = '4000' AND (tramites.t_etapa_actual = '".COMPROBACION_GASTOS_ETAPA_APROBACION."') ";
			}elseif($_SESSION["perfil"] == 9){
				$tipo_usuario = "t_dueno = '6000' AND (tramites.t_etapa_actual = '".COMPROBACION_GASTOS_ETAPA_APROBACION."') ";				
			}else if($_SESSION["perfil"] == SUPERVISOR_FINANZAS){
				$usuario=new Usuario();
				$tu_id=$usuario->getidtipo(SUPERVISOR_FINANZAS);
				$SupervisorFinanzas=$usuario->getGerenteSFinanzas($tu_id);
				// Se colocarï¿½ la etapa en duro, debido a que segï¿½n el flujo las constantes estan definidas de diferente forma.
				$tipo_usuario = "t_dueno =  '{$SupervisorFinanzas}' AND t_etapa_actual = '".COMPROBACION_GASTOS_ETAPA_APROBACION_SUPERVISOR_FINANZAS."' ";
			}else if($_SESSION["perfil"] == GERENTE_FINANZAS){
				$usuario=new Usuario();
				$tu_id=$usuario->getidtipo(GERENTE_FINANZAS);
				$GerenteFinanzas=$usuario->getGerenteSFinanzas($tu_id);
				// Se colocarï¿½ la etapa en duro, debido a que segï¿½n el flujo las constantes estan definidas de diferente forma.
				$tipo_usuario = "t_dueno = '{$GerenteFinanzas}' AND t_etapa_actual = '".COMPROBACION_INVITACION_ETAPA_APROBACION_SUPERVISOR_FINANZAS."' ";
			}else{
				$tipo_usuario = "t_dueno = '{$idUser}' AND (t_etapa_actual = '".COMPROBACION_GASTOS_ETAPA_APROBACION."') ";
			}
		
				$L = new Lista("docs=docs&type=4");
				$L->Cabeceras("Folio");
				$L->Cabeceras("Motivo");
				$L->Cabeceras("Fecha Registro");
				$L->Cabeceras("Etapa");
				$L->Cabeceras("Solicitante");
				$L->Cabeceras("Total Comprobado", "", "number", "R");
				$L->Cabeceras("Divisa");
				$L->Cabeceras("Consultar","","text","C");
						
				$query = "SELECT t_id, t_etiqueta, DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'),
					(SELECT et_etapa_nombre FROM etapas WHERE et_etapa_id = t_etapa_actual AND et_flujo_id = t_flujo),
					(IF(t_delegado = 0, (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id=t_iniciador), (CONCAT((SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_delegado), ' EN NOMBRE DE: ', (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_iniciador))))) AS atiende,
					co_anticipo_comprobado, 
					(SELECT div_nombre FROM divisa WHERE div_id = dc_divisa) AS divisa,
					CONCAT('<a href=./index.php?edit_view=edit_view&id=',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a>') AS consultar
					FROM tramites
					INNER JOIN comprobacion_gastos as ci ON ci.co_mi_tramite=tramites.t_id
					INNER JOIN detalle_comprobacion_gastos ON dc_comprobacion = co_id 
					INNER JOIN divisa ON dc_divisa = div_id 
					WHERE $tipo_usuario 
					/*WHERE t_dueno in ($idUser $parametros)*/
					AND t_flujo = '".FLUJO_COMPROBACION_GASTOS."'
					AND t_iniciador != '".$_SESSION["idusuario"]."' 
					GROUP BY t_id 
					ORDER BY t_id DESC";
				//error_log($query);
				?>
				<br><br><h1>Comprobaciones de gastos pendientes de mi aprobaci&oacute;n</h1>
				<!-- a href="http://localhost/eexpensesv2_bmw/flujos/comprobaciones/services/tye_amex_upload.php">Test</a> -->
				<form action="index.php" method="post" name="comprobacionesaprobs">
				<?php
				$L->muestra_lista($query,0,false,-1,"",10,"compgtsAut");
				//$L->muestra_lista($query, 0);
				?>
				</form>
				<?php
			}
				$I->Footer();
		}

	//
	//      Comprobaciones de Caja Chica
	//	
	}else{
		$busqueda_value = "";

		if(isset($_REQUEST["buscar"])){
			$etapa = "";
			$parametros = "";
			
			if(isset($_REQUEST['et_etapa_id']))
				$etapa = $_REQUEST['et_etapa_id'];
			
			if(isset($_REQUEST["noComp"]) && !empty($_REQUEST["noComp"])){
				$parametros = " AND tramites.t_id=" . $_REQUEST["noComp"];
				$busqueda_value .= "&noComp=".$noComp;
			}
						
			if($etapa >= 0 && $etapa != ""){
				$parametros.="AND etapas.et_etapa_id =".$etapa;
				$busqueda_value .= "&et_etapa_id=".$etapa;
			}
						
			if (isset($_REQUEST["finicial"]) && !empty($_REQUEST["finicial"])){
				$date = explode("/",$_REQUEST["finicial"]);
				if(count($date)!=3){
					$date_db = $_REQUEST["finicial"];
				}else{
					$date_db = $date[2]."-".$date[1]."-".$date[0];
				}
				$parametros.= " AND t_fecha_registro >= '".$date_db." 00:00:00'";
				$busqueda_value.= "&finicial=".$date_db;
			}
			
			// Fecha Final
			if (isset($_REQUEST["ffinal"]) && !empty ($_REQUEST["ffinal"])){
				$date = explode("/",$_REQUEST["ffinal"]);
				if(count($date)!=3){
					$date_db = $_REQUEST["ffinal"];
				}else{
					$date_db = $date[2]."-".$date[1]."-".$date[0];
				}
				$parametros.= " AND t_fecha_registro <= '".$date_db." 23:59:59'";
				$busqueda_value.= "&ffinal=".$date_db;
			}
			
			$I = new Interfaz("Comprobaciones", true);
			$L2 = new Lista("buscar&docs=docs&type=3".$busqueda_value);
			$L2->Cabeceras("Folio");
			$L2->Cabeceras("Motivo del viaje");
			$L2->Cabeceras("Fecha Registro");
			$L2->Cabeceras("Etapa");
			$L2->Cabeceras("Autorizador");
			$L2->Cabeceras("Total Comprobado");
			$L2->Cabeceras("Consultar");
			$L2->Cabeceras("Editar");
			$L2->Cabeceras("Eliminar");
			eliminar();
			if($_SESSION["perfil"] == 2){
				$query = "SELECT tramites.t_id, tramites.t_etiqueta, DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'), etapas.et_etapa_nombre,
							CASE t_dueno WHEN '1000' THEN 'CONTROLLING' WHEN '2000' THEN 'FINANZAS' ELSE  (IF(t_dueno = t_iniciador && (t_etapa_actual = 1 OR t_etapa_actual = 4 OR t_etapa_actual = 5 OR t_etapa_actual = 3 OR t_etapa_actual = 10),'',(SELECT CONCAT( u_nombre,' ', u_paterno,' ', u_materno) FROM usuario WHERE u_id = t_dueno))) END AS usuario,
							CONCAT(FORMAT(comprobaciones.co_total, 2),' MXN') AS 'comprobacion', CONCAT('<p align=center><a href=./index.php?edit_view=edit_view&id=',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a></p>'),
							IF(((etapas.et_etapa_id =".COMPROBACION_ETAPA_SIN_ENVIAR." ) || (etapas.et_etapa_id =".COMPROBACION_ETAPA_RECHAZADA." ) || (etapas.et_etapa_id =".COMPROBACION_ETAPA_DEVUELTO_CON_OBSERVACIONES.")), (CONCAT('<p align=center><a href=./index.php?new=new&edit=',t_id,'><img border=0 title=Editar src=".$RUTA_R."images/addedit.png></a></p>')),'') AS 'Editar',
							IF((((etapas.et_etapa_id =".COMPROBACION_ETAPA_SIN_ENVIAR.")  || (etapas.et_etapa_id =".COMPROBACION_ETAPA_RECHAZADA."))),(CONCAT('<p align=center><a href=./index.php?elimina=',t_id,' onclick=\'return confirm_del(); \'><img border=0 title=Eliminar src=".$RUTA_R."images/delete.png></a></p>')),'') AS 'Eliminar'
							FROM tramites
							INNER JOIN etapas ON tramites.t_flujo = etapas.et_flujo_id AND tramites.t_etapa_actual= etapas.et_etapa_id
							INNER JOIN usuario ON tramites.t_iniciador = usuario.u_id
							INNER JOIN empleado ON usuario.u_id = empleado.idfwk_usuario
							INNER JOIN comprobaciones ON tramites.t_id=comprobaciones.co_mi_tramite
							WHERE tramites.t_flujo=".FLUJO_COMPROBACION." ".$parametros."
							GROUP BY tramites.t_id
							ORDER BY t_id DESC";
			}else{
				$query = "SELECT tramites.t_id, tramites.t_etiqueta, DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'), etapas.et_etapa_nombre,
							CASE t_dueno WHEN '1000' THEN 'CONTROLLING' WHEN '2000' THEN 'FINANZAS' ELSE (IF(t_dueno = t_iniciador && (t_etapa_actual = 1 OR t_etapa_actual = 4 OR t_etapa_actual = 5 OR t_etapa_actual = 3 OR t_etapa_actual = 10),'',(SELECT CONCAT( u_nombre,' ', u_paterno,' ', u_materno) FROM usuario WHERE u_id = t_dueno))) END AS usuario,
							CONCAT(FORMAT(comprobaciones.co_total, 2),' MXN') AS 'comprobacion', CONCAT('<p align=center><a href=./index.php?view_n=view_n&id=',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a></p>'),
							IF(((etapas.et_etapa_id =".COMPROBACION_ETAPA_SIN_ENVIAR." ) || (etapas.et_etapa_id =".COMPROBACION_ETAPA_RECHAZADA." ) || (etapas.et_etapa_id =".COMPROBACION_ETAPA_DEVUELTO_CON_OBSERVACIONES.") || (etapas.et_etapa_id =".COMPROBACION_ETAPA_RECHAZADA_POR_DIRECTOR.")),(CONCAT('<p align=center><a href=./index.php?new=new&edit=',t_id,'><img border=0 title=Editar src=".$RUTA_R."images/addedit.png></a></p>')),'') AS 'Editar',
							IF((((etapas.et_etapa_id =".COMPROBACION_ETAPA_SIN_ENVIAR.") || (etapas.et_etapa_id =".COMPROBACION_ETAPA_RECHAZADA.") || (etapas.et_etapa_id =".COMPROBACION_ETAPA_RECHAZADA_POR_DIRECTOR."))),(CONCAT('<p align=center><a href=./index.php?elimina=',t_id,' onclick=\'return confirm_del(); \'><img border=0 title=Eliminar src=".$RUTA_R."images/delete.png></a></p>')),'') AS 'Eliminar'
							FROM tramites
							INNER JOIN etapas ON tramites.t_flujo = etapas.et_flujo_id AND tramites.t_etapa_actual= etapas.et_etapa_id
							INNER JOIN usuario ON tramites.t_iniciador = usuario.u_id
							INNER JOIN empleado ON usuario.u_id = empleado.idfwk_usuario
							INNER JOIN comprobaciones ON tramites.t_id=comprobaciones.co_mi_tramite
							WHERE tramites.t_iniciador=".$idUser."
							AND tramites.t_flujo=".FLUJO_COMPROBACION." ".$parametros."
							GROUP BY tramites.t_id
							ORDER BY t_id DESC";    		 
			}//tipo de usuario administrador
			
			include("../../lib/php/mnu_toolbar.php");
			comprobacion_toolbar($noEmpleado);
			busca_comprobacion(FLUJO_COMPROBACION);
			 
			echo "<h1> Comprobaciones en Etapa: ";
			if($etapa == '-1' || $etapa < 0 || $etapa == ""){
					echo "Todas</h1>"; 
			}else{
				$t = new Tramite();
				echo $t->Get_EtapaNombre($etapa,FLUJO_COMPROBACION);
				echo "</h1>";
			}       
			//filtro de busquedas
			$L2->muestra_lista($query,0,false,-1,"",10,"compviajeBusq");
			//$L2->muestra_lista($query, 0);
			$I->Footer();
			
		}else{
			// Mostraremos solo la lista de checkbox para Supervisor/Gerente de Finanzas
			if($_SESSION["perfil"] == SUPERVISOR_FINANZAS || $_SESSION["perfil"] == GERENTE_FINANZAS){?>
				<script language="JavaScript" src="../../lib/js/jquery/jquery-1.3.2.js" type="text/javascript"></script>
				<script language="JavaScript" src="js/checksIndex.js" type="text/javascript"></script>
				<script src="js/communication_ajax.js" type="text/javascript"></script>
				<script>
					var doc = $(document);
					doc.ready(inicializarEventos);
					
					function inicializarEventos(){
						//ocultamos el boton enviar al iniciar la pantalla
						$("#enviarCV").css("display", "none");
						$("#enviarcinv").css("display", "none");
					}				
				</script>
				<?php 
				// Encabezados
				$I = new Interfaz("Comprobaciones", true);
				echo "</div>";
				imprime_mensajes();
				echo "</ br><h1>Comprobaciones de viajes pendientes de verificar</h1>";
				echo "<form action='index.php' method='post' name='solViajeComp'>";
				
				// Listado de Comprobaciones de Viaje Pendientes de Verificar    	
				$L1 = new Lista();
				$L1->Cabeceras("Folio");
				$L1->Cabeceras("Motivo de Viaje");
				$L1->Cabeceras(utf8_decode("Fecha de Comprobaci&oacute;n"));
				$L1->Cabeceras("Periodo de Viaje");
				$L1->Cabeceras("Solicitante");
				$L1->Cabeceras("Monto Comprobado MXN");
				$L1->Cabeceras("<INPUT TYPE=checkbox NAME='aprobarCV' onclick='seleccionarTodoApruebCV();' id='apruebCV0'>Aprobar");
				$L1->Cabeceras("<INPUT TYPE=checkbox NAME='rechazarCV' onclick='seleccionarTodoRechazCV();' id='rechazCV0'>Rechazar");
				
				($_SESSION["perfil"] == SUPERVISOR_FINANZAS) ? $signo = "<=": $signo = ">" ;    			
				$query = "SELECT t_id, t_etiqueta, DATE_FORMAT(co_fecha_registro,'%d/%m/%Y'), 
							(SELECT DATE_FORMAT(MAX(svi_fecha_llegada),'%d/%m/%Y') FROM sv_itinerario, solicitud_viaje WHERE sv_id = svi_solicitud AND sv_tramite = co_tramite),
							(IF(t_delegado = 0, (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id=t_iniciador), (CONCAT((SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_delegado), ' EN NOMBRE DE: ', (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_iniciador))))), 
							FORMAT(co_total, 2),
							CONCAT('<p align=center><a><input type=checkbox name=apruebCV',t_id,' class=apruebCV id=apruebCV',t_id,' value=',t_id,' onclick=activarBotonInvCV(this.value,this.id); ></a></p>') AS 'Aprobar',
							CONCAT('<p align=center><a><input type=checkbox name=rechazCV',t_id,' class=rechazCV id=rechazCV',t_id,' value=',t_id,' onclick=activarBotonInvCV(this.value,this.id); ></a></p>') AS 'Rechazar'
							FROM tramites
							JOIN comprobaciones ON co_mi_tramite = t_id
							JOIN detalle_comprobacion ON dc_comprobacion = co_id
							WHERE t_dueno = $idUser
							AND t_etapa_actual = ".COMPROBACION_ETAPA_EN_APROBACION_POR_SF." 					
							AND co_total $signo ".ULT_APROBACION."
							GROUP BY t_id
							ORDER BY t_id DESC";
				//error_log($query);
				$L1->muestra_lista($query,0,false,-1,"",10,"compviajeSGFinanzas");
				//$L1->muestra_lista($query, 0); 
				echo "<br /> <br />"; ?>
				<input type="hidden" id="tramites_aceptadosCV" name="tramites_aceptadosCV" size="5" readonly="readonly" value="0" />
				<input type="hidden" id="tramites_rechazadosCV" name="tramites_rechazadosCV" size="5" readonly="readonly" value="0" />
				<input type="submit" value=" Enviar" id="enviarCV" name="enviarCV" style=" margin-left:1040px;" onclick="enviarNotificacionCV();" />
				<?php     	
				// Listado de Comprobaciones de Invitaciï¿½n Pendientes de Verificar    	
				echo "<br /> <br />";
				echo "<h1>Comprobaciones de Gsatos pendientes de verificar</h1>";
				$L2 = new Lista();
				$L2->Cabeceras("Folio");
				$L2->Cabeceras("Motivo Gasto");
				$L2->Cabeceras("Fecha Registro");
				$L2->Cabeceras("Solicitante");
				$L2->Cabeceras("Monto Comprobado MXN");
				$L2->Cabeceras("<input type=checkbox name='aprueb0Inv' onclick='seleccionarTodoApruebInv();' id='aprueb0Inv'>Aprobar");
				$L2->Cabeceras("<input type=checkbox name='rechaz0Inv' onclick='seleccionarTodoRechazInv();' id='rechaz0Inv'>Rechazar");
				
				($_SESSION["perfil"] == SUPERVISOR_FINANZAS) ? $signoSGF = "<=": $signoSGF = ">";
					$query = "SELECT t_id, t_etiqueta, DATE_FORMAT(co_fecha_registro,'%d/%m/%Y') AS co_fecha_registro, 
						(IF(t_delegado = 0, (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id=t_iniciador), (CONCAT((SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_delegado), ' EN NOMBRE DE: ', (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_iniciador))))) AS atiende,
						FORMAT((SUM(dc_total) * (SELECT div_tasa FROM divisa WHERE dc_divisa = div_id)), 2) AS monto_pesos, 
						CONCAT('<p align=center><a><input type=checkbox name=apruebInv',t_id,' class=apruebInv id=apruebInv',t_id,' value=',t_id,' onclick=activarBotonInv(this.value,this.id); ></a></p>') AS 'Aprobar',
						CONCAT('<p align=center><a><input type=checkbox name=rechazInv',t_id,' class=rechazInv id=rechazInv',t_id,' value=',t_id,' onclick=activarBotonInv(this.value,this.id); ></a></p>') AS 'Rechazar'									
						FROM tramites 
						INNER JOIN comprobacion_gastos ON co_mi_tramite = t_id
						INNER JOIN detalle_comprobacion_gastos ON dc_comprobacion = co_id
						WHERE t_dueno= '{$idUser}' 
						AND t_etapa_actual = '".COMPROBACION_GASTOS_ETAPA_APROBACION_SUPERVISOR_FINANZAS."' 
						#AND (SELECT (SUM(dc_total) * (SELECT div_tasa FROM divisa WHERE dc_divisa = div_id)) FROM detalle_comprobacion_gastos) ".$signoSGF."'".ULT_APROBACION."'
						GROUP BY t_id 
						ORDER BY t_id DESC";
				//error_log($query);
				$L2->muestra_lista($query,0,false,-1,"",10,"compgtsSGFinanzas");
				echo "</ br> </ br>"; ?>
				<input type="hidden" id="tramites_aceptados" name="tramites_aceptados" size="5" readonly="readonly" value="0" />
				<input type="hidden" id="tramites_rechazados" name="tramites_rechazados" size="5" readonly="readonly" value="0" />
				<input type="hidden" id="perfil" name="perfil" size="5" readonly="readonly" value="<?php echo $_SESSION["perfil"];?>" />
				<input type="submit" value=" Enviar" id="enviarcinv" name="enviarcinv" style=" margin-left:1040px;" onclick="enviarNotificacionInv();" />
				<?php echo "</form>"; 
				$I->Footer();
							
			}else{ // Pantalla visible para usuario diferente de Supervisor/Gerente de Finanzas 
				//Muestra al inicio de la pantalla (sin busqueda) -> Mis solicitudes de viaje comprobadas 
				// y comprobaciones de viaje pendientes de mi aprobacion
				//comprobaciones de viajes, eliminar todo el codigo que no sirve y modificarlo, agregar
				//
				//     Mis comprobaciones de viajes
				//
				$I = new Interfaz("Comprobaciones", true);
				$L2 = new Lista();
				$L2->Cabeceras("Folio");
				$L2->Cabeceras("Motivo del viaje");   
				$L2->Cabeceras("Fecha Registro");
				$L2->Cabeceras("Etapa");       
				$L2->Cabeceras("Autorizador");
				$L2->Cabeceras("Total Comprobado"); 
				$L2->Cabeceras("Consultar");
				$L2->Cabeceras("Editar");
				$L2->Cabeceras("Eliminar");
			
				include("../../lib/php/mnu_toolbar.php");        
				if($_SESSION["perfil"] == 6 || $_SESSION["perfil"] == 5){            	
					comprobacion_toolbar($noEmpleado);        
				}else{
					comprobacion_toolbar($noEmpleado);
					busca_comprobacion(FLUJO_COMPROBACION);
				}		
				imprime_mensajes();
				
				$parametrobusqueda = "";
				if(!isset($_SESSION['idrepresentante'])){
					$parametrobusqueda = " AND tramites.t_etapa_actual !=  '".COMPROBACION_ETAPA_RECHAZADA_POR_DIRECTOR."' ";
				}
			 
				if(($_SESSION["perfil"] != 6 && $_SESSION["perfil"] != 5 )){
					if($_SESSION["perfil"] == 2){
						echo "</div>";
						echo "<h1>Mis solicitudes de viaje comprobadas (Administrador)</h1>";
						echo "<form action='index.php' method='post' name='solicitudesComprobadas'>";
						echo "</form>";
					}else{
						echo "</div>";
						echo "<h1>Mis solicitudes de viaje comprobadas</h1>";
						echo "<form action='index.php' method='post' name='solViajeComp'>";
					}						
					
					//encabezados
					$L1 = new Lista();
					$L1->Cabeceras("Folio");
					$L1->Cabeceras("Motivo del viaje");   
					$L1->Cabeceras("Fecha Registro");
					$L1->Cabeceras("Etapa");       
					$L1->Cabeceras("Autorizador");
					$L1->Cabeceras("Total Comprobado"); 
					$L1->Cabeceras("Consultar");
					$L1->Cabeceras("Editar");
					$L1->Cabeceras("Eliminar");
					eliminar();

					$query = "SELECT tramites.t_id, tramites.t_etiqueta, DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'), etapas.et_etapa_nombre,
							IF(t_dueno = t_iniciador && (t_etapa_actual = ".COMPROBACION_ETAPA_SIN_ENVIAR." OR t_etapa_actual = ".COMPROBACION_ETAPA_RECHAZADA." OR t_etapa_actual = ".COMPROBACION_ETAPA_DEVUELTO_CON_OBSERVACIONES." OR t_etapa_actual = ".COMPROBACION_ETAPA_APROBADA." OR t_etapa_actual = ".COMPROBACION_ETAPA_RECHAZADA_POR_DIRECTOR."),'', IF(tramites.t_dueno IN (SELECT au_id FROM agrupacion_usuarios),(SELECT au_nombre FROM agrupacion_usuarios AS au WHERE au.au_id=tramites.t_dueno),(SELECT CONCAT(usuario.u_nombre,' ',usuario.u_paterno,' ',usuario.u_materno) FROM usuario WHERE usuario.u_id=tramites.t_dueno))) AS usuario,
							CONCAT(FORMAT(comprobaciones.co_anticipo_comprobado, 2),' MXN') AS 'comprobacion',
							CONCAT('<p align=center><a href=./index.php?view_n=view_n&id=',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a></p>'),
							IF(((etapas.et_etapa_id =".COMPROBACION_ETAPA_SIN_ENVIAR." ) || (etapas.et_etapa_id =".COMPROBACION_ETAPA_RECHAZADA." ) || (etapas.et_etapa_id =".COMPROBACION_ETAPA_DEVUELTO_CON_OBSERVACIONES.") || (etapas.et_etapa_id =".COMPROBACION_ETAPA_RECHAZADA_POR_DIRECTOR.")),
							(CONCAT('<p align=center><a href=./index.php?new=new&edit=',t_id,'><img border=0 title=Editar src=".$RUTA_R."images/addedit.png></a></p>')),'') AS 'Editar',  
							IF(((etapas.et_etapa_id =".COMPROBACION_ETAPA_SIN_ENVIAR.")  || (etapas.et_etapa_id =".COMPROBACION_ETAPA_RECHAZADA.") || (etapas.et_etapa_id =".COMPROBACION_ETAPA_RECHAZADA_POR_DIRECTOR.")),(CONCAT('<p align=center><a href=./index.php?elimina=',t_id,' onclick=\'return confirm_del(); \'><img border=0 title=Eliminar src=".$RUTA_R."images/delete.png></a></p>')),'') AS 'Eliminar'
							FROM tramites
							INNER JOIN etapas ON tramites.t_flujo = etapas.et_flujo_id AND tramites.t_etapa_actual= etapas.et_etapa_id
							INNER JOIN usuario ON tramites.t_iniciador = usuario.u_id
							INNER JOIN empleado ON usuario.u_id = empleado.idfwk_usuario
							INNER JOIN comprobaciones ON tramites.t_id=comprobaciones.co_mi_tramite
							WHERE tramites.t_iniciador= '".$idUser."' 
							".$parametrobusqueda."
							AND tramites.t_flujo=".FLUJO_COMPROBACION." 
							GROUP BY tramites.t_id
							ORDER BY t_id DESC";
							//AND tramites.t_etapa_actual=".SOLICITUD_ETAPA_SIN_ENVIAR." ".FLUJO_COMPROBACION."".$idUser."
					//funcion para eliminar
					if(isset($_GET['elimina'])||isset($_POST['elimina'])){
						$elimina=$_GET['elimina'];
						$id_com = 0;
						$cnn = new conexion();						
						//query que eliminara tramites dependiendo al tramite seleccionado
						$query_elimina_tramites="DELETE FROM tramites where t_id=".$elimina.""; 
						$cnn->ejecutar($query_elimina_tramites);
						
						//query que tomara el id de la comprobacion dependiendo del tramite creado
						$query_obtiene_idcom="SELECT co_id FROM comprobaciones WHERE co_mi_tramite=".$elimina."";
						$res = mysql_query($query_obtiene_idcom);						
						if($row=mysql_fetch_array($res))
							$id_com = $row['co_id'];
							
						//query que tomara el id de la factura
						$query_obtiene_idFact="SELECT DISTINCT(id_factura) as id_factura FROM detalle_comprobacion WHERE dc_comprobacion=".$id_com."";
						$resFact = mysql_query($query_obtiene_idFact);

							while($rowFact=mysql_fetch_array($resFact)){
								$id_fact[] = $rowFact['id_factura'];
							}
							foreach($id_fact as $idf){
								if($idf != ""){
									$query_elimina_factura="DELETE FROM factura where id_factura=".$idf."";		        							
									$cnn->ejecutar($query_elimina_factura);
								}	
							}							

					
						//Regresa las transacciones relacionadas con la comprobacion a su estatus 0
						$query_amex = "UPDATE amex 
									JOIN detalle_comprobacion ON idamex = dc_idamex_comprobado
									JOIN comprobaciones ON co_id = dc_comprobacion
									SET estatus = 0, 
									comprobacion_id = '0' 
									WHERE co_id = ".$id_com;
						mysql_query($query_amex);						
							
						//query que eliminara el detalle de la comprobacion
						$query_elimina_detalle="DELETE FROM detalle_comprobacion where dc_comprobacion=".$id_com."";		        	
						$cnn->ejecutar($query_elimina_detalle);
						
						//query que eliminara la comprobacion
						$query_elimina_comprobacion="DELETE FROM comprobaciones where co_mi_tramite=".$elimina."";		        							
						$cnn->ejecutar($query_elimina_comprobacion);
					}
					$L1->muestra_lista($query,0,false,-1,"",10,"compviaje");
				
				}		   
				//usuario  tipo administrador
				if($_SESSION["perfil"] == 2){    
				
				   echo "</div><br><br><h1>Comprobaciones de viaje pendientes de mi aprobaci&oacuten (Administracion)</h1> ";
				   //echo"<form action='index.php' method='post' name='comprobacionesaprobs'>";        
				   //echo "</form>"; 
				   
				   //encabezados
				   if($_SESSION["perfil"] == SUPERVISOR_FINANZAS || $_SESSION["perfil"] == GERENTE_FINANZAS){				
						$L = new Lista();
						$L->Cabeceras("Folio");
						$L->Cabeceras("Motivo de invitaciï¿½n");   
						$L->Cabeceras("Fecha Registro");			        
						$L->Cabeceras("Solicitante");
						$L->Cabeceras("Monto comprobado MXN"); 
						$L->Cabeceras("Aprobar");
						$L->Cabeceras("Rechazar");
				   }else{
						$L = new Lista();
						$L->Cabeceras("Folio");
						$L->Cabeceras("Motivo del viaje");   
						$L->Cabeceras("Fecha Registro");
						$L->Cabeceras("Etapa");       
						$L->Cabeceras("Solicitante");
						$L->Cabeceras("Total Comprobado"); 
						$L->Cabeceras("Consultar");
				   }
					
					$query = "SELECT tramites.t_id, tramites.t_etiqueta, DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'), etapas.et_etapa_nombre,
							IF(tramites.t_dueno IN (SELECT au_id FROM agrupacion_usuarios),(SELECT au_nombre FROM agrupacion_usuarios AS au WHERE au.au_id=tramites.t_dueno),(SELECT CONCAT(usuario.u_nombre,' ',usuario.u_paterno,' ',usuario.u_materno) FROM usuario WHERE usuario.u_id=tramites.t_dueno)) AS usuario,
							CONCAT(FORMAT(comprobaciones.co_total, 2),' MXN')AS 'comprobacion',
							CONCAT('<p align=center><a href=./index.php?edit_view=edit_view&id=',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a></p>')
							FROM tramites
							INNER JOIN etapas ON tramites.t_flujo = etapas.et_flujo_id AND tramites.t_etapa_actual= etapas.et_etapa_id
							INNER JOIN usuario ON tramites.t_iniciador = usuario.u_id
							INNER JOIN empleado ON usuario.u_id = empleado.idfwk_usuario
							INNER JOIN comprobaciones ON tramites.t_id=comprobaciones.co_mi_tramite
							WHERE tramites.t_flujo=".FLUJO_COMPROBACION."
							AND t_iniciador != '".$_SESSION["idusuario"]."'  
							GROUP BY tramites.t_id
							ORDER BY t_id DESC";
					
				    $L->muestra_lista($query,0,false,-1,"",10,"compviajeAut");
				   //$L->muestra_lista($query, 0);				   
				}else{
					if($_SESSION["perfil"]!=3){
						echo "</div><br><br><h1>Comprobaciones de viaje pendientes de mi aprobaci&oacute;n</h1> ";

						//Se checa si el usuario pertenece a Controlling o Finanzas
						$parametros = "";
						$agrup_usu = new AgrupacionUsuarios();
							
						if($agrup_usu->Load_Homologacion_Dueno_By_u_ID($_SESSION["idusuario"])){
							$idAgrupacion = $agrup_usu->Get_dato("hd_au_id");
							if($idAgrupacion != ""){
								$parametros = ", ".$idAgrupacion;
							}
						}
					
					$tipo_usuario = "";
					if($_SESSION["perfil"] == 6){
						$tipo_usuario = "t_dueno = '2000' AND (tramites.t_etapa_actual = '".COMPROBACION_ETAPA_EN_APROBACION."' OR tramites.t_etapa_actual = '".COMPROBACION_ETAPA_APROBADA_POR_SF."' OR tramites.t_etapa_actual = '".COMPROBACION_ETAPA_RECHAZADA_POR_SF."') ";
					}elseif($_SESSION["perfil"] == 5){
							$tipo_usuario = "t_dueno = '1000' AND (tramites.t_etapa_actual = '".COMPROBACION_ETAPA_EN_APROBACION."') ";
					}elseif($_SESSION["perfil"] == 10){
							$tipo_usuario = "t_dueno = '4000' AND (tramites.t_etapa_actual = '".COMPROBACION_ETAPA_EN_APROBACION."') ";
					}elseif($_SESSION["perfil"] == 9){
							$tipo_usuario = "t_dueno = '6000' AND (tramites.t_etapa_actual = '".COMPROBACION_ETAPA_EN_APROBACION."') ";							
					}else if($_SESSION["perfil"] == SUPERVISOR_FINANZAS){
						$usuario=new Usuario();
						$tu_id=$usuario->getidtipo(SUPERVISOR_FINANZAS);
						$SupervisorFinanzas=$usuario->getGerenteSFinanzas($tu_id);
						$tipo_usuario = "t_dueno =  '{$SupervisorFinanzas}' AND (tramites.t_etapa_actual = '".COMPROBACION_ETAPA_EN_APROBACION_POR_SF."') ";
					}else if($_SESSION["perfil"] == GERENTE_FINANZAS){
						$usuario=new Usuario();
						$tu_id=$usuario->getidtipo(GERENTE_FINANZAS);
						$GerenteFinanzas=$usuario->getGerenteSFinanzas($tu_id);
						$tipo_usuario = "t_dueno = '{$GerenteFinanzas}' AND (tramites.t_etapa_actual = '".COMPROBACION_ETAPA_EN_APROBACION_POR_SF."') ";
					}else{
						$tipo_usuario = "t_dueno = '{$idUser}' AND (tramites.t_etapa_actual = '".COMPROBACION_ETAPA_EN_APROBACION."') ";
					}
						
						//encabezados
						
						$L = new Lista();
						$L->Cabeceras("Folio");
						$L->Cabeceras("Motivo del viaje");
						$L->Cabeceras("Fecha Registro");
						$L->Cabeceras("Etapa");
						$L->Cabeceras("Solicitante");
						$L->Cabeceras("Total Comprobado");
						$L->Cabeceras("Consultar");
						
						$query = "SELECT tramites.t_id, tramites.t_etiqueta, DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'), etapas.et_etapa_nombre,
									(IF(t_delegado = 0, (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id=t_iniciador), (CONCAT((SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_delegado), ' EN NOMBRE DE: ', (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_iniciador))))) AS usuario,
									CONCAT(FORMAT(comprobaciones.co_anticipo_comprobado, 2),' MXN')AS 'comprobacion',
									CONCAT('<p align=center><a href=./index.php?view_travel=view_travel&id=',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a></p>')
									FROM tramites
									INNER JOIN etapas ON tramites.t_flujo = etapas.et_flujo_id AND tramites.t_etapa_actual= etapas.et_etapa_id
									INNER JOIN usuario ON tramites.t_iniciador = usuario.u_id
									INNER JOIN empleado ON usuario.u_id = empleado.idfwk_usuario
									INNER JOIN comprobaciones ON tramites.t_id=comprobaciones.co_mi_tramite
									WHERE {$tipo_usuario} 
									AND tramites.t_flujo=".FLUJO_COMPROBACION."
									AND t_iniciador != '".$_SESSION["idusuario"]."' 
									GROUP BY tramites.t_id
									ORDER BY t_id DESC";
						
						$L->muestra_lista($query,0,false,-1,"",10,"compviajeAut");					
					} 
				}       
			$I->Footer();
			}
		}
	}
?>
