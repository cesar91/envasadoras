<?php
	session_start();
	require_once("../../lib/php/constantes.php");
	require_once "$RUTA_A/Connections/fwk_db.php";
	require_once "$RUTA_A/functions/utils.php";
	require_once("$RUTA_A/lib/php/mobile_device_detect.php");

	function imprime_mensajes(){
		if(isset($_GET["oksave"])){ 
			echo "<h3><img src='../../images/ok.png' width='20' height='20' />La solicitud se guard&oacute; correctamente, pasa a la siguiente etapa para su aprobaci&oacute;n.</h3>";
		} 
		if(isset($_GET["oksaveG"])){
			echo "<h3><img src='../../images/ok.png' width='20' height='20' />La solicitud se guard&oacute; correctamente, pasa a la siguiente etapa para su aprobaci&oacute;n.</h3>";
		} 
		if(isset($_GET["errsave"])){ 
			echo "<font color='#FF0000'><img src='../../images/action_cancel.gif' width='22' height='22' /><b>Se encontraron errores, la solicitud no se ha guardado. Verifique e intente de nuevo.</b></font>";
		} 		
		if(isset($_GET["erramnt"]) ){ 
			echo "<font color='#FF0000'><img src='../../images/action_cancel.gif' width='22' height='22' /><b>Se encontraron errores en los montos, la solicitud no se ha guardado. Verifique e intente de nuevo.</b></font>";
		} 		
		if(isset($_GET["ccNegative"])){ 
			echo "<font color='#FF0000'><img src='../../images/action_cancel.gif' width='22' height='22' /><b>No se guardo la solicitud ya que no se localiza presupuesto del periodo actual para el departamento seleccionado, verifique con su administrador</b></font>";		
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
	}
	
	if(isset($_GET['view'])){
		$I = new Interfaz("Solicitud de Viaje",true);
		require_once("solicitud_view.php");
	}elseif($_SESSION["perfil"] == 2 || $_SESSION["perfil"] == 4 || $_SESSION["perfil"] == 9 || $_SESSION["perfil"] == 10){
		$parametros = " ";
		$url = "";
		
		$url.= (!empty($_REQUEST['noComp'])) ? "&noComp=".$_REQUEST['noComp'] : '' ;
		$parametros .= (!empty($_REQUEST['noComp'])) ? " AND t_id = ".$_REQUEST['noComp'] : '' ;
		
		$url.= (!empty($_REQUEST['solicitante'])) ? "&solicitante=".$_REQUEST['solicitante'] : '' ;
		$parametros .= (!empty($_REQUEST['solicitante'])) ? " AND nombre LIKE '%".$_REQUEST['solicitante']."%'" : '' ;
		$url .= ( @$_REQUEST['et_etapa_id'] != "-1" && @$_REQUEST['et_etapa_id'] != "") ? "&et_etapa_id=".$_REQUEST['et_etapa_id'] : '' ;
		if( @$_REQUEST['et_etapa_id'] != "-1" && @$_REQUEST['et_etapa_id'] != ""){
			$prmts = explode("|", $_REQUEST['et_etapa_id']);
			$flujo = $prmts[0];
			$etapaid = $prmts[1];
			$parametros .= " AND et_etapa_id = '".$etapaid."' ";
			$parametros .= " AND et_flujo_id = '".$flujo."' ";
		}			
		if ( isset($_REQUEST["finicial"]) && !empty($_REQUEST["finicial"]) && isset($_REQUEST["ffinal"])&& !empty($_REQUEST["ffinal"]) ){
			$date1 = explode("/",$_REQUEST["finicial"]);
			$date2 = explode("/",$_REQUEST["ffinal"]);
			$date_db_inicial = $date1[2]."/".$date1[1]."/".$date1[0];
			$date_db_final = $date2[2]."/".$date2[1]."/".$date2[0];
			$url .= "&finicial=".$date1[0]."/".$date1[1]."/".$date1[2]."&ffinal=".$date2[0]."/".$date2[1]."/".$date2[2];
			$parametros .= " AND t_fecha_registro BETWEEN '".$date_db_inicial." 00:00:00' AND '".$date_db_final." 23:59:59'";        
		}

		if(isset($_REQUEST['buscar'])) $url = "buscar=busca".$url;
		
		$I  = new Interfaz("Solicitudes",true);
		
		$L1	= new Lista($url);
		$L1->Cabeceras("Folio");
		$L1->Cabeceras("Tipo");
		$L1->Cabeceras("Motivo del viaje");
		$L1->Cabeceras("Fecha Registro");
		$L1->Cabeceras("Etapa");
		$L1->Cabeceras("Solicitante");
		$L1->Cabeceras("Monto Total");
		$L1->Cabeceras("Consultar");
		
		$sqlBusqueda = "SELECT t_id, 
						(CASE t_flujo WHEN '1' THEN
								'SOLICITUD DE VIAJE'
							WHEN '2' THEN
								'SOLICITUD DE GASTOS'
							WHEN '3' THEN
								'COMPROBACION DE VIAJE'
							WHEN '4' THEN
								'COMPROBACION DE GASTOS'
						END) AS tipoTramite,
						t_etiqueta, DATE_FORMAT(t_fecha_registro, '%d/%m/%Y'), et_etapa_nombre,
						nombre,
						(CASE t_flujo 
							WHEN '1' THEN
								CONCAT(CONVERT(FORMAT(sv_total, 2) USING utf8),' MXN')
							WHEN '2' THEN
								CONCAT(CONVERT(FORMAT(sg_monto, 2) USING utf8),' MXN')
							WHEN '3' THEN
								CONCAT(CONVERT(FORMAT(comprobaciones.co_total, 2) USING utf8),' MXN')
							WHEN '4' THEN
								CONCAT(CONVERT(cg.co_total USING utf8), ' ', (SELECT div_nombre FROM divisa WHERE div_id = dc_divisa))
						END) AS total,
						(CASE t_flujo 
							WHEN '1' THEN
								CONCAT('<p align=center><a href=./index.php?view=view&hist=hist&id=', CONVERT(t_id USING utf8), '><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a></p>')
							WHEN '2' THEN
								CONCAT('<p align=center><a href=./index.php?view=view&hist=hist&id=', CONVERT(t_id USING utf8), '><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a></p>')
							WHEN '3' THEN
								CONCAT('<p align=center><a href=../../flujos/comprobaciones/index.php?view_n=view_n&hist=hist&id=', CONVERT(t_id USING utf8), '><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a></p>')
							WHEN '4' THEN
								CONCAT('<p align=center><a href=../../flujos/comprobaciones/index.php?view=view&hist=hist&id=', CONVERT(t_id USING utf8), '><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a></p>')
						END) AS consultar
				FROM tramites
				LEFT JOIN solicitud_viaje ON t_id = sv_tramite
				LEFT JOIN solicitud_gastos ON t_id = sg_tramite
				LEFT JOIN comprobaciones ON t_id = comprobaciones.co_mi_tramite 
				LEFT JOIN comprobacion_gastos AS cg ON cg.co_mi_tramite = t_id
				LEFT JOIN detalle_comprobacion_gastos ON dc_comprobacion = cg.co_id 
				INNER JOIN etapas ON (et_flujo_id = t_flujo AND et_etapa_id = t_etapa_actual)
				INNER JOIN empleado ON idfwk_usuario = t_iniciador
				WHERE 1 = 1 
				$parametros
				GROUP BY t_id
				ORDER BY t_id DESC";
		$L2	= new Lista($url);
		$L2->Cabeceras("Folio");
		$L2->Cabeceras("Motivo del viaje");
		$L2->Cabeceras("Fecha Registro");
		$L2->Cabeceras("Etapa");
		$L2->Cabeceras("Solicitante");
		$L2->Cabeceras("Destino");
		$L2->Cabeceras("Monto de la Solicitud");
		$L2->Cabeceras("Consultar");

		$sql = "SELECT t_id, t_etiqueta, DATE_FORMAT(t_fecha_registro, '%d/%m/%Y'), et_etapa_nombre, nombre,
						sv_viaje,
						CONCAT(FORMAT(sv_total,2),' MXN') AS monto,
						CONCAT('<p align=center><a href=./index.php?view=view&hist=hist&id=', CONVERT(t_id USING utf8), '><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a></p>')
				FROM tramites
				JOIN etapas ON t_flujo = et_flujo_id AND t_etapa_actual = et_etapa_id
				JOIN empleado ON idfwk_usuario = t_iniciador
				JOIN solicitud_viaje ON t_id = sv_tramite 			
				JOIN sv_itinerario  ON sv_id = svi_solicitud
				WHERE t_flujo = " . FLUJO_SOLICITUD . "
				$parametros
				GROUP BY t_id
				ORDER BY t_id DESC";
		
		$L3	= new Lista($url);
		$L3->Cabeceras("Folio");
		$L3->Cabeceras("Motivo");
		$L3->Cabeceras("Fecha Registro");
		$L3->Cabeceras("Etapa");
		$L3->Cabeceras("Solicitante");
		$L3->Cabeceras("Monto de la Solicitud");
		$L3->Herramientas("S","./index.php?view=view&hist=hist&id=");

		$sqlSolicitudGastos = "SELECT t_id, t_etiqueta, DATE_FORMAT(t_fecha_registro, '%d/%m/%Y'), et_etapa_nombre, nombre,
						CONCAT(FORMAT(sg_monto, 2),' MXN') AS monto 
				FROM tramites
				JOIN etapas ON t_flujo = et_flujo_id AND t_etapa_actual = et_etapa_id
				JOIN empleado ON idfwk_usuario = t_iniciador
				JOIN solicitud_gastos ON t_id = sg_tramite
				WHERE t_flujo = " . FLUJO_SOLICITUD_GASTOS . "
				$parametros
				GROUP BY t_id
				ORDER BY t_id DESC";
		
		$L4	= new Lista($url);
		$L4->Cabeceras("Folio");
		$L4->Cabeceras("Motivo");
		$L4->Cabeceras("Fecha Registro");
		$L4->Cabeceras("Etapa");
		$L4->Cabeceras("Solicitante");
		$L4->Cabeceras("Monto de la Comprobaci&oacute;n");
		$L4->Cabeceras("Consultar");
		
		$sqlComprobaciones = "SELECT tramites.t_id, tramites.t_etiqueta, DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'), etapas.et_etapa_nombre,
							(IF(t_delegado = 0, (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id=t_iniciador), (CONCAT((SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_delegado), ' EN NOMBRE DE: ', (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_iniciador))))) AS usuario,
							CONCAT(FORMAT(comprobaciones.co_total, 2),' MXN') AS 'comprobacion',
							CONCAT('<p align=center><a href=../../flujos/comprobaciones/index.php?view_n=view_n&hist=hist&id=',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a></p>')
					FROM tramites
					INNER JOIN etapas ON tramites.t_flujo = etapas.et_flujo_id AND tramites.t_etapa_actual= etapas.et_etapa_id
					INNER JOIN usuario ON tramites.t_iniciador = usuario.u_id
					INNER JOIN empleado ON usuario.u_id = empleado.idfwk_usuario
					INNER JOIN comprobaciones ON tramites.t_id=comprobaciones.co_mi_tramite
					WHERE tramites.t_flujo=".FLUJO_COMPROBACION."
					GROUP BY tramites.t_id
					ORDER BY t_id DESC";
		
		$L5	= new Lista($url);
		$L5->Cabeceras("Folio");
		$L5->Cabeceras("Motivo");
		$L5->Cabeceras("Fecha Registro");
		$L5->Cabeceras("Etapa");
		$L5->Cabeceras("Solicitante");
		$L5->Cabeceras("Monto de la Comprobaci&oacute;n");
		$L5->Cabeceras("Consultar");
		
		$sqlComprobacionesGastos = "SELECT t_id, t_etiqueta, DATE_FORMAT(t_fecha_registro,'%d/%m/%Y'),
							(SELECT et_etapa_nombre FROM etapas WHERE et_etapa_id = t_etapa_actual AND et_flujo_id = t_flujo),
							(IF(t_delegado = 0, (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id=t_iniciador), (CONCAT((SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_delegado), ' EN NOMBRE DE: ', (SELECT CONCAT(u_nombre,' ',u_paterno,' ',u_materno) FROM usuario WHERE u_id = t_iniciador))))) AS atiende,
							CONCAT(co_total, ' ', (SELECT div_nombre FROM divisa WHERE div_id = dc_divisa)) AS totalComprobacion,
							CONCAT('<p align=center><a href=../../flujos/comprobaciones/index.php?view=view&hist=hist&id=',t_id,'><img border=0 title=Consultar src=".$RUTA_R."images/btn-search.gif></a></p>') AS consultar
					FROM tramites
					INNER JOIN comprobacion_gastos AS cg ON cg.co_mi_tramite = tramites.t_id
					INNER JOIN detalle_comprobacion_gastos ON dc_comprobacion = co_id 
					INNER JOIN divisa ON dc_divisa = div_id 
					WHERE t_flujo = '" . FLUJO_COMPROBACION_GASTOS . "'
					GROUP BY t_id 
					ORDER BY t_id DESC";
		
		include("../../lib/php/mnu_toolbar.php"); 
		busca_solicitud_historial(FLUJO_SOLICITUD); 
		echo "<br /><br />";?>
		
		<meta http-equiv="Pragma" content="no-cache">
		<script type="text/javascript" src="../../lib/js/jquery/jquery-1.9.1.js"></script>
		<script type="text/javascript" src="../../lib/js/jqueryui/jquery-ui-1.10.3.custom.min.js"></script>
		<script type="text/javascript" src="js/configuracionHistorial.js"></script>
		<script type="text/javascript" src="js/functionsHistorial.js"></script>
		<link rel="stylesheet" href="../../css/jquery-ui-1.10.3.custom.min.css" type="text/css" media="screen" />
		<?php
		if (isset($_REQUEST['buscar'])){
			echo "</div><h1>Resultado de la b&uacute;squeda</h1>";
			$L1->muestra_lista($sqlBusqueda,0);
		}else{ ?>
			</div>
			<div id='tabs' style="width: 97%;" align="center">
				<ul class="pestanas">
					<li><a id="a_solicitud_viaje" href='#div_solicitud_viaje'>Historial de Solicitudes de Viajes</a></li>
					<li><a id="a_solicitud_gastos" href='#div_solicitud_gastos'>Historial de Solicitudes de Gastos</a></li>
					<li><a id="a_comprobaciones" href='#div_comprobaciones'>Historial de Comprobaciones de Viajes</a></li>
					<li><a id="a_comprobacion_gastos" href='#div_comprobacion_gastos'>Historial de Comprobaciones de Gastos</a></li>
				</ul>
				<div id='div_solicitud_viaje'>
					<?php $L2->muestra_lista($sql, 0); ?>
				</div>
				<div id='div_solicitud_gastos'>
					<?php $L3->muestra_lista($sqlSolicitudGastos, 0, false, -1, "", 10, "solicitudGastos"); ?>
				</div>
				<div id='div_comprobaciones'>
					<?php $L4->muestra_lista($sqlComprobaciones, 0, false, -1, "", 10, "comprobacionesViaje"); ?>
				</div>
				<div id='div_comprobacion_gastos'>
					<?php $L5->muestra_lista($sqlComprobacionesGastos, 0, false, -1, "", 10, "comprobacionesGastos"); ?>
				</div>
			</div>
		<?php 
		}
	}else{
		$I = new Interfaz("Solicitud de Viaje",true);
		echo ("<div align='center'><br /><h1>Estimado usuario, Ud. no cuenta con los privilegios <br />necesarios para visualizar esta secci&oacute;n.</h1></div>");
	}
	$I->Footer();		
?>