<?php
	session_start();

	require_once("../lib/php/constantes.php");
	require_once "$RUTA_A/Connections/fwk_db.php";
	require_once "$RUTA_A/functions/utils.php";
	require_once('../lib/php/utils.php');
	
	function insertarRuta($rutaNombre, $t_id, $autorizaciones=false, $rutaActualNombres=''){
		$cnn = new conexion();
		$sql = "INSERT INTO rutatransformacion (rut_id, rut_ruta, rut_actual) VALUES ";
		
		if($autorizaciones){
			for($i=0; $i<count($rutaNombre); $i++)
				$sql.= "('".$t_id[$i]."','".$rutaNombre[$i]."','".$rutaActualNombres[$i]."'),";
		}else{
			for($i=0; $i<count($rutaNombre); $i++)
				$sql.= "('".$t_id[$i]."','".$rutaNombre[$i]."', ''),";		
		}		
		$sql = substr($sql,0,-1);
		
		if(count($t_id) > 0)
			$cnn->consultar($sql);
	}

	function getNombreAutorizadoresReporte($tramiteId, $ruta=true){
		$ruta_del = array();
		$usuarioAprobador = new Usuario();
		$tramite = new Tramite();
		$tramite->Load_Tramite($tramiteId);
		$tramite_ruta = ($ruta) ?  $tramite->Get_dato("t_ruta_autorizacion") : $tramite->Get_dato("t_autorizaciones"); 
		
		//Obtener los nombres de los Autorizadores
		$token = strtok($tramite_ruta,"|");
		$autorizadores = "";
		$encontrado = false;
		if($token != false){
			if($usuarioAprobador->Load_Usuario_By_ID($token)){
				$usuarioAprobador->Load_Usuario_By_ID($token);
				$nombre_autorizadores = $usuarioAprobador->Get_dato("u_nombre")." ".$usuarioAprobador->Get_dato("u_paterno");
			}else{
				$agrup_usu = new AgrupacionUsuarios();
				$agrup_usu->Load_Grupo_de_Usuario_By_ID($token);
				$nombre_autorizadores = $agrup_usu->Get_dato("au_nombre");
			}

			$encontrado = strpos($token, "*");
			if($encontrado){
				$ruta_del = explode("*", $token);
				$usuarioAprobador->Load_Usuario_By_ID($ruta_del[0]);
				$nombre_autorizadores = $usuarioAprobador->Get_dato("u_nombre")." ".$usuarioAprobador->Get_dato("u_paterno");
				$autorizadores .= " ".$nombre_autorizadores." AUTORIZO EN NOMBRE DE: ";
				$usuarioAprobador->Load_Usuario_By_ID($ruta_del[1]);
				$nombre_autorizadores1 = $usuarioAprobador->Get_dato("u_nombre")." ".$usuarioAprobador->Get_dato("u_paterno");
				$autorizadores .= $nombre_autorizadores1;
			}else{
				$autorizadores .= $nombre_autorizadores;
			}
			$token = strtok("|");
			while($token != false){
				$autorizadores .= ", ";
				if($usuarioAprobador->Load_Usuario_By_ID($token)){
					$usuarioAprobador->Load_Usuario_By_ID($token);
					$nombre_autorizadores = $usuarioAprobador->Get_dato("u_nombre")." ".$usuarioAprobador->Get_dato("u_paterno");
				}else{
					$agrup_usu = new AgrupacionUsuarios();
					$agrup_usu->Load_Grupo_de_Usuario_By_ID($token);
					$nombre_autorizadores = $agrup_usu->Get_dato("au_nombre");
				}
				$encontrado = strpos($token, "*");
				if($encontrado){
					$ruta_del = explode("*", $token);
					$usuarioAprobador->Load_Usuario_By_ID($ruta_del[0]);
					$nombre_autorizadores = $usuarioAprobador->Get_dato("u_nombre")." ".$usuarioAprobador->Get_dato("u_paterno");
					$autorizadores .= " ".$nombre_autorizadores."AUTORIZO EN NOMBRE DE: ";
					$usuarioAprobador->Load_Usuario_By_ID($ruta_del[1]);
					$nombre_autorizadores1 = $usuarioAprobador->Get_dato("u_nombre")." ".$usuarioAprobador->Get_dato("u_paterno");
					$autorizadores .= $nombre_autorizadores1;
				}else{
					$autorizadores .= $nombre_autorizadores;
				}
				$token = strtok("|");
			}
		}

		return $autorizadores;
	}
	
	function obtenerRutaNombresAutorizacion(){
		//===============================Construccion de la ruta en nombres	
		//Crearemos la tabla que nos permitira alojar las rutas transformadas
		$cnn = new Conexion();
		$borra_tablas = "DROP TABLE IF EXISTS rutatransformacion";			
		$cnn->ejecutar($borra_tablas);
		
		$creaTabla = "CREATE TABLE rutatransformacion(
				rut_id BIGINT(20) NOT NULL ,
				rut_ruta VARCHAR(256) NULL ,
				rut_actual VARCHAR(256) NULL ,
				INDEX t_ruta_tramite (rut_id ASC) )";
		$cnn->ejecutar($creaTabla);
		
		$arregloRuta = "SELECT t_id FROM tramites";
		$rst2 = $cnn->consultar($arregloRuta);
		$rutaNombres = array();
		$t_id = array();
		$i = 0;
		
		while ($filaa = mysql_fetch_assoc($rst2)){
			$rutaNombres[$i] = getNombreAutorizadoresReporte($filaa['t_id']);
			$t_id[$i] = $filaa['t_id'];
			$i++;
		}
		
		insertarRuta($rutaNombres, $t_id);
	}
	
	function obtenerAnticiposNoComprobados(){
		$cnn = new conexion();
		$sql = "CREATE TABLE resultadoAnticiposnoComprobados AS
					SELECT t.t_id,
						CONCAT(cc.cc_centrocostos, ' - ', cc.cc_nombre) AS Ceco,
						emp.nombre,
						f.f_nombre,
						DATE_FORMAT(t.t_fecha_registro, '%d/%m/%Y') AS FechaRegistro,
						sv.sv_total_anticipo,
						'0.00' AS anticipoComprobado,
						(sv.sv_total_anticipo) AS anticipoNoComprobado,
						'N/A' AS etapaComprobacion,
						sv.sv_viaje,
						DATE_FORMAT(svi.svi_fecha_salida,'%d/%m/%Y') AS svi_fecha_salida,
						DATE_FORMAT((SELECT MAX(svi_fecha_llegada) FROM sv_itinerario WHERE svi_solicitud = sv_id) ,'%d/%m/%Y') AS svi_fecha_llegada,
						rut_ruta, 
						cc_id,
						t.t_iniciador,
						t.t_fecha_registro
					FROM tramites AS t
					INNER JOIN flujos f ON f.f_id = t.t_flujo
					INNER JOIN etapas et ON et.et_etapa_id = t.t_etapa_actual AND et.et_flujo_id = t.t_flujo
					INNER JOIN solicitud_viaje sv ON sv.sv_tramite = t.t_id
					LEFT  JOIN sv_itinerario svi ON sv.sv_id = svi.svi_solicitud
					INNER JOIN cat_cecos cc ON cc.cc_id = sv.sv_ceco_paga
					INNER JOIN empleado emp ON emp.idempleado = t.t_iniciador
					INNER JOIN rutatransformacion rt ON rt.rut_id = t.t_id
					JOIN empresas ON cc.cc_empresa_id = e_codigo
					WHERE sv.sv_anticipo = 1
					AND (SELECT svc_enviado_sap 
						FROM sv_conceptos_detalle 
						JOIN sv_itinerario ON svc_itinerario = svi_id 
						WHERE svi_solicitud = sv_id 
						AND svc_enviado_sap = 1 LIMIT 1) = 1
					AND t.t_flujo = 1
					AND t.t_etapa_actual = 7 OR t.t_etapa_actual = 9
					AND NOT EXISTS (SELECT * FROM comprobaciones WHERE co_tramite = sv_tramite)

				UNION 

					SELECT t.t_id,
						CONCAT(cc.cc_centrocostos, ' - ', cc.cc_nombre) AS Ceco,
						emp.nombre,
						f.f_nombre,
						DATE_FORMAT(t.t_fecha_registro, '%d/%m/%Y') AS FechaRegistro,
						sg_monto,
						'0.00',
						sg_monto AS anticipoNoComprobado,
						'N/A' AS etapaComprobacion,
						'N/A',
						'N/A',
						'N/A',
						rut_ruta, 
						cc_id,
						t.t_iniciador,
						t.t_fecha_registro
						
					FROM tramites AS t
					INNER JOIN flujos f ON f.f_id = t.t_flujo
					INNER JOIN etapas et ON et.et_etapa_id = t.t_etapa_actual AND et.et_flujo_id = t.t_flujo
					JOIN solicitud_gastos ON sg_tramite = t.t_id
					JOIN cat_cecos cc ON cc.cc_id = sg_ceco
					JOIN empleado emp ON emp.idempleado = t.t_iniciador
					JOIN rutatransformacion rt ON rt.rut_id = t.t_id
					JOIN empresas ON cc.cc_empresa_id = e_codigo
					WHERE sg_requiere_anticipo = 1
					AND sg_enviado_sap = 1
					AND t.t_flujo = 2
					AND t.t_etapa_actual = 3
					AND NOT EXISTS (SELECT * FROM comprobacion_gastos WHERE co_tramite = sg_tramite)";
		
		$cnn->ejecutar($sql);
	}
	
	function obtenerAnticiposenComprobacion(){
		$cnn = new conexion();
		$sql = "CREATE TABLE resultadoAnticiposenComprobacion
				SELECT t.t_id,
						CONCAT(cc.cc_centrocostos, ' - ', cc.cc_nombre) AS Ceco,
						emp.nombre,
						f.f_nombre,
						DATE_FORMAT(t.t_fecha_registro, '%d/%m/%Y') AS FechaRegistro,
						sv_total_anticipo,
						co_anticipo_comprobado,
						(sv_total_anticipo - co_anticipo_comprobado) AS anticipoNoComprobado,
						et.et_etapa_nombre AS etapaComprobacion,
						sv_viaje,
						DATE_FORMAT(svi.svi_fecha_salida,'%d/%m/%Y') AS svi_fecha_salida,
						DATE_FORMAT((SELECT MAX(svi_fecha_llegada) FROM sv_itinerario WHERE svi_solicitud = sv_id) ,'%d/%m/%Y') AS svi_fecha_llegada,
						rut_ruta, 
						cc_id,
						t.t_iniciador,
						t.t_fecha_registro
					
					FROM comprobaciones 
					JOIN tramites t ON co_mi_tramite = t.t_id

					JOIN solicitud_viaje ON sv_tramite = co_tramite
					LEFT JOIN sv_itinerario svi ON sv_id = svi.svi_solicitud
					JOIN tramites tramitePadre ON sv_tramite = tramitePadre.t_id

					INNER JOIN flujos f ON f.f_id = t.t_flujo
					INNER JOIN etapas et ON et.et_etapa_id = t.t_etapa_actual AND et.et_flujo_id = t.t_flujo

					INNER JOIN cat_cecos cc ON cc.cc_id = sv_ceco_paga
					INNER JOIN empleado emp ON emp.idempleado = t.t_iniciador
					INNER JOIN rutatransformacion rt ON rt.rut_id = t.t_id
					JOIN empresas ON cc.cc_empresa_id = e_codigo
					WHERE t.t_flujo = 3
					AND t.t_etapa_actual != 3
					AND sv_anticipo = 1 
					AND (SELECT svc_enviado_sap 
						FROM sv_conceptos_detalle 
						JOIN sv_itinerario ON svc_itinerario = svi_id 
						WHERE svi_solicitud = sv_id 
						AND svc_enviado_sap = 1 LIMIT 1) = 1
					AND tramitePadre.t_flujo = 1
					AND tramitePadre.t_etapa_actual = 7 OR tramitePadre.t_etapa_actual = 9

				UNION
				
					SELECT t.t_id,
						CONCAT(cc.cc_centrocostos, ' - ', cc.cc_nombre) AS Ceco,
						emp.nombre,
						f.f_nombre,
						DATE_FORMAT(t.t_fecha_registro, '%d/%m/%Y') AS FechaRegistro,
						sg_monto,
						co_anticipo_comprobado,
						(sg_monto - co_anticipo_comprobado) AS anticipoNoComprobado,
						et.et_etapa_nombre AS etapaComprobacion,
						'N/A',
						sg_fecha_gasto,
						'N/A',
						rut_ruta, 
						cc_id,
						t.t_iniciador,
						t.t_fecha_registro

					FROM comprobacion_gastos
					JOIN tramites t ON co_mi_tramite = t.t_id

					JOIN solicitud_gastos ON sg_tramite = co_tramite
					JOIN tramites tramitePadre ON sg_tramite = tramitePadre.t_id

					INNER JOIN flujos f ON f.f_id = t.t_flujo
					INNER JOIN etapas et ON et.et_etapa_id = t.t_etapa_actual AND et.et_flujo_id = t.t_flujo

					INNER JOIN cat_cecos cc ON cc.cc_id = sg_ceco
					INNER JOIN empleado emp ON emp.idempleado = t.t_iniciador
					INNER JOIN rutatransformacion rt ON rt.rut_id = t.t_id
					JOIN empresas ON cc.cc_empresa_id = e_codigo
					WHERE t.t_flujo = 4
					AND t.t_etapa_actual != 3
					AND sg_requiere_anticipo = 1 
					AND sg_enviado_sap = 1
					AND tramitePadre.t_flujo = 2
					AND tramitePadre.t_etapa_actual = 3";
		$cnn->ejecutar($sql);
	}

	$I = new Interfaz("Reportes",true);
	$cnn = new conexion();
	
	//Borrar Vistas y tablas
	$borra_vistas = "DROP VIEW IF EXISTS resultado, resultado2";
	$borra_tablas = "DROP TABLE IF EXISTS resultado, resultado2";
	$cnn->ejecutar($borra_vistas);
	$cnn->ejecutar($borra_tablas);
	?>
	<link rel="stylesheet" type="text/css" href="../lib/js/jquery-ui-1.10.4/development-bundle/themes/smoothness/jquery.ui.all.css"/>
	<script type="text/javascript" src="../lib/js/jquery-ui-1.10.4/development-bundle/jquery-1.10.2.js"></script>
	<script type="text/javascript" src="../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.core.js"></script>
	<script type="text/javascript" src="../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.button.js"></script>
	<script type="text/javascript" src="../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.position.js"></script>
	<script type="text/javascript" src="../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.menu.js"></script>
	<script type="text/javascript" src="../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.autocomplete.js"></script>
	<script type="text/javascript" src="../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.tooltip.js"></script>
	<script type="text/javascript" src="../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.datepicker.js"></script>
	<script type="text/javascript" src="js/configuracion.js"></script>
	
	<style>
	.custom-combobox {
		position: relative;
		display: inline-block;
	}
	.custom-combobox-toggle {
		position: absolute;
		top: 0;
		bottom: 0;
		margin-left: -1px;
		padding: 0;
		/* support: IE7 */
		*height: 1.7em;
		*top: 0.1em;
	}
	.custom-combobox-input {
		margin: 0;
		padding: 0.3em;
	}
	.ui-autocomplete {
		max-height: 180px;
		overflow-y: auto;
		/* prevent horizontal scrollbar */
		overflow-x: hidden;
	}
	/* IE 6 doesn't support max-height
	 * we use height instead, but this forces the menu to always be this tall
	 */
	* html .ui-autocomplete {
		height: 180px;
	}
	
	.classFiltros{
		color: 0000FF
	}
	</style>
	<?php
		if(isset ($_GET['reporte']))
			$_SESSION['reporte'] = $_GET['reporte'];
					
		echo "<center><h3>".$_SESSION['reporte']."</h3></center>";
	?>
	<form action="reporte.php" method="post" name="formReporte">
		<table align='center' width='30%'>
		<?php if(($_SESSION['reporte']== "ReporteComprobaciones")||($_SESSION['reporte']== "Reporte de Anticipos no comprobados")){?>
			<?php if($_SESSION['reporte']== "Reporte de Gastos"){?>
			<!-- Tipo Empleado -->
			<tr>
				<td  colspan='1' align='right' >Tipo de Empleado:</td>
				<td colspan='2' align="left" style="top:100px;">
					<select name="tipoemp" id="tipoemp">
						<option value="-1">Seleccione Tipo de empleado</option>
						<option value="1">BMW</option>
						<option value="3">EXTERNOS</option>						
					</select>
				</td>
			</tr>
			<?php }?>
			<!-- Empresa -->
			<?php if($_SESSION['reporte'] != "ReporteComprobaciones" && $_SESSION['reporte'] != "Reporte de Anticipos no comprobados"){?>
			<tr>
				<td colspan='1' align='right' style="left:20px;">Empresa:</td>
				<td colspan='2' align="left" style="top:100px;">
					<?php $empresa = new Empresa(); ?>
					<select name="EmpresaOpciones" id="EmpresaOpciones" onChange="obtener_cecos(this.value)">
						<option name="EmpresaOpciones" id="EmpresaOpciones" value="-1">Seleccione una Empresa</option>
						<?php foreach($empresa->Load_all() as $arrE){ ?>
						<option name="EmpresaOpciones" id="EmpresaOpciones" value="<?php echo $arrE['e_id'];?>"><?php echo $arrE['e_nombre'];?></option>
						<?php } ?>		
					</select>
				</td>								
			</tr>
			<? } ?>
			<!-- CECOS -->
			<tr>
				<td  colspan='1' align='right' ><span class="classFiltros">CECO:</span></td>
				<td colspan="2">
				<?php if($_SESSION['reporte'] == "ReporteComprobaciones" || $_SESSION['reporte'] == "Reporte de Anticipos no comprobados"){?>
					<select name="CecoOpciones" id="CecoOpciones">
					<?php $cnn = new conexion();
							$query = '';
							$query = sprintf("SELECT cc_id, cc_centrocostos, cc_nombre FROM cat_cecos WHERE cc_estatus = 1 ORDER BY cc_nombre ASC");
							$rst = $cnn->consultar($query); ?>
							<option value=""></option>
							<?php while($fila = mysql_fetch_assoc($rst)){ ?>
								<option value="<?php echo $fila['cc_id'];?>"><?php echo $fila['cc_centrocostos'].' - '.strtoupper(utf8_decode($fila['cc_nombre']));?></option>
							<?php } ?>
				<?php }?>
					</select>					
				</td>											
			</tr>	
			<?php if($_SESSION['reporte']== "Reporte de Anticipos no comprobados" || $_SESSION['reporte'] == "ReporteComprobaciones"){?>
			<!-- EMPLEADO -->
			<tr>
				<td  colspan='1' align='right' ><span class="classFiltros">Empleado:</span></td>
				<td colspan="2">
					<?php $cnn = new conexion();
					$query = sprintf("SELECT idempleado, nombre, u_usuario AS 'numEmpleado'
									FROM empleado 
									JOIN usuario on idfwk_usuario = u_id 
									WHERE u_activo = 1 
									ORDER BY nombre ASC");
					$rst_empleado = $cnn->consultar($query); ?>
					<select name="empleados" id="empleados">
						<option value=""></option>
						<?php while($rst = mysql_fetch_assoc($rst_empleado)){ ?>
						<option value="<?php echo $rst['idempleado'];?>"><?php echo $rst['numEmpleado']." - ".strtoupper(utf8_decode($rst['nombre']));?></option>
						<?php } ?>		
					</select>				
				</td>											
			</tr>
			<?php }?>
		<?php }?>
		</table>		
		<br>
		<!-- Calendario -->			
		<table align='center' width='30%'>
			<tr>
				<td colspan="2" align="center" valign="middle"><strong><span class="classFiltros">Periodo:</span></strong></td>
			</tr>
			<tr>
				<td width="91px" align="right" valign="middle"><span class="classFiltros">Fecha Inicial:</span></td>
				<td align="left" valign="middle"><input name="fechaInicio" id="fechaInicio" readonly="true" /></td>
			</tr>
			<tr>
				<td align="right" valign="middle"><span class="classFiltros">Fecha Final:</span></td>
				<td align="left" valign="middle"><input name="fechaFin" id="fechaFin" readonly="true" /></td>
			</tr>				
			<tr><td colspan='2'>&nbsp;</td></tr>    
			<tr>
				<td colspan='2' align='center'>
				   <input type="submit" name='buscar' id='buscar' value="Buscar">
				</td>
			</tr>     
		</table>
	</form>
	<?php
	
	if(isset ($_POST['buscar'])){
		$count = 0;		
		//Variables necesarias  para la busqueda
		$tEmpleado = isset($_POST['tipoemp']) ?  $_POST['tipoemp'] : "";
		
		$empresaId = isset($_POST['EmpresaOpciones']) ?  $_POST['EmpresaOpciones'] : "";
		$ceco = isset($_POST['CecoOpciones']) ?  $_POST['CecoOpciones'] : "";
		$nomEmpleado = isset($_POST['EmpleadoOpciones']) ?  $_POST['EmpleadoOpciones'] : "";
		$idEmpleado = isset($_POST['empleados']) ?  $_POST['empleados'] : "";
		$fechaInicio = $_POST["fechaInicio"];
		$fechaFin = $_POST["fechaFin"];			
		$parametros = " WHERE 1 = 1";
		
		//====================== Validacion de busqueda (parametros)======================
		//Tipo de Empleado		
		if($tEmpleado > -1 && $_SESSION['reporte'] == "Reporte de Gastos" )
				$parametros.= " AND tipo_empleado = '$tEmpleado'";		
		
		//Empresa
		if($empresaId > 0 && ($_SESSION['reporte'] == "Reporte de Gastos" || $_SESSION['reporte'] == "Reporte de Anticipos no comprobados" ) ){
				$empresa = new Empresa();
				$empresa->Load_Empresa($empresaId);
				$empresaNom = $empresa->Get_Dato("e_nombre");				
				$parametros.= " AND empresa LIKE '%$empresaNom%'"; 				
		}
		
		// Empleado
		if($idEmpleado > 0 && ($_SESSION['reporte'] == "ReporteComprobaciones") ){
			$parametros.= " AND t.t_iniciador  = '$idEmpleado'";
		}
		
		//Centro de costos 
		if($ceco > 0 ){
			if($_SESSION['reporte'] == "ReporteComprobaciones"){							
				$parametros.= " AND cc_id = '$ceco'";
			}elseif($_SESSION['reporte'] == "Reporte de Anticipos no comprobados"){							
				$parametros.= " AND cc_id = '$ceco'";
			}else{
				$ceco = new CentroCosto();
				$ceco->Busca_CeCo($_POST['CecoOpciones']);
				$Ceco = $ceco->Get_Dato('cc_centrocostos');		
				$nombre = $ceco->Get_Dato('cc_nombre');
					
				if($_SESSION['reporte'] == "Reporte de Gastos"){							
					$parametros.= " AND centro_de_costos = '$Ceco'";
				}else{
					$parametros.= " AND cc_id = '$ceco'";
				}
			}
		}			
		
		//Nombre de empleado
		if($idEmpleado > 0 && $_SESSION['reporte'] == "Reporte de Anticipos no comprobados")
			$parametros.= " AND t_iniciador  = '$idEmpleado'";
		
		//validacion para fechas
		if($fechaInicio != ""){			
			$date = explode("/",$fechaInicio);
			if(count($date)!=3)
				return "";				
			$date_db = $date[2]."-".$date[1]."-".$date[0];	
			
			if($_SESSION['reporte'] == "ReporteComprobaciones"){					
				$parametros.= " AND t.t_fecha_registro >= '$date_db 00:00:00'";					
			}elseif(($_SESSION['reporte']== "Reporte de Anticipos no comprobados")){
				$parametros.= " AND t_fecha_registro >= '$date_db 00:00:00'";									
			}elseif(($_SESSION['reporte'] == "Reporte de Anticipos generados y registrados en SAP")){
				$parametros.= " AND t.t_fecha_registro >= '$date_db 00:00:00'";
			}elseif($_SESSION['reporte'] == "Reporte de Gastos AMEX no comprobados"){
				$parametros.= " AND fecha >= '$date_db 00:00:00'";
			}elseif($_SESSION['reporte'] == "Reporte de Viajes en Avion"){
				$parametros.= " AND sv_fecha_registro >= '$date_db 00:00:00'";
			}elseif($_SESSION['reporte'] == "Reporte de Descuentos"){
				$parametros.= " AND co_fecha_registro >= '$date_db 00:00:00'";
			}elseif($_SESSION['reporte'] == "Reporte de Solicitudes"){
				$parametros.= " AND t_fecha_registro >= '$date_db 00:00:00'";
			}elseif($_SESSION['reporte'] == "Reporte de Comprobaciones de Gasolina fuera de politica"){
				$parametros.= " AND t_fecha_registro >='$date_db 00:00:00'";
			}							
		} 
		
		if($fechaFin != ""){
			$date = explode("/",$fechaFin);
			if(count($date) != 3)
				return "";
			$date_db = $date[2]."-".$date[1]."-".$date[0];
			
			if($_SESSION['reporte']== "ReporteComprobaciones"){
				$parametros.= " AND t.t_fecha_registro <= '$date_db 23:59:59'";
			}elseif(($_SESSION['reporte']== "Reporte de Anticipos no comprobados")){
				$parametros.= " AND t_fecha_registro <= '$date_db 23:59:59'";
			}elseif($_SESSION['reporte']== "Reporte de Anticipos generados y registrados en SAP"){
				$parametros.= " AND t.t_fecha_registro <= '$date_db 23:59:59'";
			}elseif($_SESSION['reporte']== "Reporte de Gastos AMEX no comprobados"){
				$parametros.= " AND fecha <= '$date_db 23:59:59'";
			}elseif($_SESSION['reporte']== "Reporte de Viajes en Avion"){
				$parametros.= " AND sv_fecha_registro <= '$date_db 23:59:59'";
			}elseif($_SESSION['reporte']== "Reporte de Descuentos"){
				$parametros.= " AND co_fecha_registro <= '$date_db 23:59:59'";
			}elseif($_SESSION['reporte']== "Reporte de Solicitudes"){
				$parametros.= " AND t_fecha_registro <= '$date_db 23:59:59'";
			}elseif($_SESSION['reporte']== "Reporte de Comprobaciones de Gasolina fuera de politica"){
				$parametros.= " AND t_fecha_registro <='$date_db 23:59:59'";
			}		
		}				

	
		//Empieza la validacion del tipo de reporte
		if($_SESSION['reporte']== "Reporte de Gastos"){
				
			//se realizara el drop de las vistas y tablas si es que existen
			$borra_vistas = "DROP VIEW IF EXISTS VistaFiltrado, V_Gastos1, V_Gastos2, VistaGastosRuta1, VistaGastosRuta2";
			$borra_tablas = "DROP TABLE IF EXISTS rutatransformacion";			
			$cnn->ejecutar($borra_vistas);			
			$cnn->ejecutar($borra_tablas);
			
			//ejecutamos las vistas relacionadas con la ruta
			$vistaRuta1 = "CALL VistaGastosRuta1();";
			$vistaRuta2 = "CALL VistaGastosRuta2();";
			mysql_query($vistaRuta1);		
			mysql_query($vistaRuta2);
			
			//===============================Construccion de la ruta en nombres			
			//Crearemos la tabla que nos permitira alojar las rutas transformadas			
			$creaTabla = "CREATE TABLE rutatransformacion(rut_id VARCHAR(45), rut_ruta VARCHAR(256))";
			$cnn->consultar($creaTabla);
			
			$arregloRuta = "SELECT folio, ruta_de_aprobacion 
			FROM VistaGastosRuta1 $parametros
			UNION
			SELECT folio,ruta_de_aprobacion 
			FROM VistaGastosRuta2 $parametros";
			$rst2 = $cnn->consultar($arregloRuta);			 
			$rutaNombres = array();
			$t_id = array();
			$i = 0;			
			while ($filaa = mysql_fetch_assoc($rst2)){							
				$rutaNombres[$i] = getNombreAutorizadoresReporte($filaa['folio']);					
				$t_id[$i] = $filaa['folio'];					
				$i++;
			}
			insertarRuta($rutaNombres,$t_id);
			//=======================================Fin
			
			//ejecutamos las vistas relacionadas con los datos generados
			$Vista1 = "CALL VistaGastos1();";
			$Vista2 = "CALL VistaGastos2();";
			mysql_query($Vista1);
			mysql_query($Vista2);

			//se creara una vista en la cual genere el filtrado por la fecha 
			$VistaFiltrado = "CREATE VIEW VistaFiltrado AS
			SELECT * FROM V_Gastos1 $parametros
			UNION
			SELECT * FROM V_Gastos2 $parametros";			
			$cnn->ejecutar($VistaFiltrado);
			
			//seleccionamos lo que hay en las vistas y realizamos una union.	 	
			$sql = "CREATE TABLE resultado AS
			SELECT no_de_solicitud, tipo_de_comprobacion, DATE(fecha_de_comprobacion) AS fecha_de_comprobacion, numero_de_empleado, nombre_del_empleado, empresa, documento, 
				IFNULL(DATE(fecha_de_solicitud),'N/A') AS fecha_de_solicitud, folio_de_solicitud, DATE(fecha_de_autorizacion) AS fecha_de_autorizacion, motivo, 
				IF(anticipo='0.00','N/A',anticipo) AS anticipo, centro_de_costos, concepto, cuenta_contable, importe_sin_iva, propina, impuesto_sobre_hospedaje, iva, monto_total, 
				moneda, rfc, transaccion, numero_de_tarjeta, ruta_de_aprobacion 
			FROM VistaFiltrado
			ORDER BY no_de_solicitud, tipo_de_comprobacion"; 	
			
			$_SESSION['query'] = $sql;
			$_SESSION['nombreReporte'] = "reporteDeGastos";
			$_SESSION['head'] = "REPORTE DE GASTOS";
			
		}elseif($_SESSION['reporte']== "Reporte de Anticipos no comprobados"){
			$cnn = new conexion();
			$queryDropTables = "DROP TABLE IF EXISTS resultado, resultadoAnticiposnoComprobados, resultadoAnticiposenComprobacion";			
			$cnn->ejecutar($queryDropTables);
			
			obtenerRutaNombresAutorizacion();
			obtenerAnticiposNoComprobados();
			obtenerAnticiposenComprobacion();
			
			$sql = "CREATE TABLE resultado AS
				SELECT t_id AS 'FOLIO',
						Ceco AS 'CENTRO DE COSTOS',
						nombre AS 'EMPLEADO',
						f_nombre AS 'DOCUMENTO',
						FechaRegistro AS 'FECHA DE SOLICITUD',
						sv_total_anticipo AS 'ANTICIPO SOLICITADO',
						anticipoComprobado AS 'ANTICIPO COMPROBADO',
						anticipoNoComprobado AS 'ANTICIPO NO COMPROBADO',
						etapaComprobacion AS 'ETAPA DE COMPROBACIÓN',
						sv_viaje AS 'TIPO DE VIAJE',
						svi_fecha_salida AS 'FECHA DE SALIDA',
						svi_fecha_llegada AS 'FECHA DE REGRESO',
						rut_ruta AS 'RUTA'
					FROM resultadoAnticiposnoComprobados $parametros
				UNION
					SELECT t_id AS 'FOLIO',
						Ceco AS 'CENTRO DE COSTOS',
						nombre AS 'EMPLEADO',
						f_nombre AS 'DOCUMENTO',
						FechaRegistro AS 'FECHA DE SOLICITUD',
						sv_total_anticipo AS 'ANTICIPO SOLICITADO',
						co_anticipo_comprobado AS 'ANTICIPO COMPROBADO',
						anticipoNoComprobado AS 'ANTICIPO NO COMPROBADO',
						etapaComprobacion AS 'ETAPA DE COMPROBACIÓN',
						sv_viaje AS 'TIPO DE VIAJE',
						svi_fecha_salida AS 'FECHA DE SALIDA',
						svi_fecha_llegada AS 'FECHA DE REGRESO',
						rut_ruta AS 'RUTA' 
					FROM resultadoAnticiposenComprobacion $parametros
					ORDER BY folio";
			//error_log($sql);
			$_SESSION['query'] = $sql;
			$_SESSION['nombreReporte'] = "ReportedeAnticiposnoComprobados";
			$_SESSION['head'] = "REPORTE DE ANTICIPOS NO COMPROBADOS";
		}elseif($_SESSION['reporte']== "Reporte de Viajes en Avion"){
			
			$sql="CREATE TABLE resultado as
			
			SELECT 
			sv.sv_tramite  AS 'FOLIO DE SOLICITUD',
			emp.nombre AS 'NOMBRE',
			svi.svi_origen AS 'ORIGEN',
			svi.svi_destino AS 'DESTINO',
			
			DATE_FORMAT(sv.sv_fecha_registro,'%d/%m/%Y') AS 'FECHA SOLICITUD',
			DATE_FORMAT(svi.svi_fecha_salida_avion,'%d/%m/%Y') AS 'FECHA SALIDA',
			DATE_FORMAT(MAX(svi.svi_fecha_salida),'%d/%m/%Y')  AS 'FECHA REGRESO',
			IFNULL(svi.svi_aerolinea,'') AS 'AEROLINEA',
			CONCAT(cc.cc_centrocostos, '-' ,cc.cc_nombre) AS 'CENTRO DE COSTOS',
			IFNULL(svi.svi_tipo_aerolinea, 'N/A') AS 'DESCRIPCIÓN DE CLASE DE TARIFA',
			DATE_FORMAT(t.t_fecha_cierre,'%d/%m/%Y') AS 'FECHA DE COMPRA',
			svi.svi_monto_vuelo_total  AS 'TOTAL COTIZADO',
			IFNULL(svi.svi_monto_vuelo, '') AS 'MONTO AUTORIZADO',
			IFNULL(svi.svi_tua, '') AS 'TUA AUTORIZADO',
			IFNULL(svi.svi_iva, '') AS 'IVA AUTORIZADO',
			IFNULL((svi.svi_monto_vuelo + svi.svi_tua + svi.svi_iva), '') AS 'TOTAL AUTORIZADO'			
			
			FROM solicitud_viaje AS sv
			INNER JOIN tramites t ON t.t_id = sv.sv_tramite
			INNER JOIN flujos f ON f.f_id = t.t_flujo AND t.t_flujo = '1'
			INNER JOIN etapas et ON et.et_etapa_id = t.t_etapa_actual AND et.et_flujo_id = '1' AND et.et_etapa_id = '9'
			INNER JOIN empleado emp ON emp.idempleado = t.t_iniciador		
			INNER JOIN cat_cecos cc ON cc.cc_id = sv.sv_ceco_paga
			INNER JOIN sv_itinerario svi ON svi.svi_solicitud = sv.sv_id
			$parametros
			AND svi.svi_id IS NOT NULL
			AND svi.svi_enviado_sap = 1
			AND svi.svi_tipo_transporte = 'Aereo' 
			GROUP BY sv.sv_tramite			
			ORDER BY t.t_id, svi.svi_id"; 	
							
			
			$_SESSION['query'] = $sql;
			$_SESSION['nombreReporte'] = "reporteDeViajesEnAvion";
			$_SESSION['head'] = "REPORTE DE VIAJES EN AVION";
			
		 }elseif($_SESSION['reporte']== "Reporte de Anticipos generados y registrados en SAP"){
			obtenerRutaNombresAutorizacion();
			
			$sql = "CREATE TABLE resultado AS
					SELECT t.t_id AS 'FOLIO',
						DATE_FORMAT(t.t_fecha_registro, '%d/%m/%Y') AS 'FECHA DE SOLICITUD',
						LEFT(u_usuario, 8) AS 'NÚMERO DE EMPLEADO',
						emp.nombre AS 'EMPLEADO',
						CONCAT(cc.cc_centrocostos, ' - ', cc.cc_nombre) AS 'CENTRO DE COSTOS',
						f.f_nombre AS 'DOCUMENTO',
						sv.sv_total_anticipo AS 'ANTICIPO SOLICITADO',
						sv_motivo AS 'MOTIVO',
						sv.sv_viaje AS 'TIPO DE VIAJE',
						DATE_FORMAT(svi.svi_fecha_salida,'%d/%m/%Y') AS 'FECHA DE SALIDA',
						DATE_FORMAT((SELECT MAX(svi_fecha_llegada) FROM sv_itinerario WHERE svi_solicitud = sv_id) ,'%d/%m/%Y') AS 'FECHA DE REGRESO',
						rut_ruta AS 'RUTA DE APROBACIÓN'
					FROM tramites AS t
					INNER JOIN flujos f ON f.f_id = t.t_flujo
					INNER JOIN etapas et ON et.et_etapa_id = t.t_etapa_actual AND et.et_flujo_id = t.t_flujo
					INNER JOIN solicitud_viaje sv ON sv.sv_tramite = t.t_id
					LEFT  JOIN sv_itinerario svi ON sv.sv_id = svi.svi_solicitud
					INNER JOIN cat_cecos cc ON cc.cc_id = sv.sv_ceco_paga
					INNER JOIN empleado emp ON emp.idempleado = t.t_iniciador
					JOIN usuario ON idfwk_usuario = u_id
					INNER JOIN rutatransformacion rt ON rt.rut_id = t.t_id
					JOIN empresas ON cc.cc_empresa_id = e_codigo
					$parametros
					AND sv.sv_anticipo = 1
					AND t.t_flujo = 1
					AND t.t_etapa_actual = 7 OR t.t_etapa_actual = 9
					AND (SELECT svc_enviado_sap 
						FROM sv_conceptos_detalle 
						JOIN sv_itinerario ON svc_itinerario = svi_id 
						WHERE svi_solicitud = sv_id 
						AND svc_enviado_sap = 1 LIMIT 1) = 1

				UNION 

					SELECT t.t_id AS 'FOLIO',
						DATE_FORMAT(t.t_fecha_registro, '%d/%m/%Y') AS 'FECHA DE SOLICITUD',
						LEFT(u_usuario, 8) AS 'NÚMERO DE EMPLEADO',
						emp.nombre AS 'EMPLEADO',
						CONCAT(cc.cc_centrocostos, ' - ', cc.cc_nombre) AS 'CENTRO DE COSTOS',
						f.f_nombre AS 'DOCUMENTO',
						sg_monto AS 'ANTICIPO SOLICITADO',
						sg_motivo AS 'MOTIVO',
						'N/A' AS 'TIPO DE VIAJE',
						'N/A' AS 'FECHA DE SALIDA',
						'N/A' AS 'FECHA DE REGRESO',
						rut_ruta AS 'RUTA DE APROBACIÓN'
					FROM tramites AS t
					INNER JOIN flujos f ON f.f_id = t.t_flujo
					INNER JOIN etapas et ON et.et_etapa_id = t.t_etapa_actual AND et.et_flujo_id = t.t_flujo
					JOIN solicitud_gastos ON sg_tramite = t.t_id
					JOIN cat_cecos cc ON cc.cc_id = sg_ceco
					JOIN empleado emp ON emp.idempleado = t.t_iniciador
					JOIN usuario ON idfwk_usuario = u_id
					JOIN rutatransformacion rt ON rt.rut_id = t.t_id
					JOIN empresas ON cc.cc_empresa_id = e_codigo
					$parametros
					AND sg_requiere_anticipo = 1
					AND sg_enviado_sap = 1
					AND t.t_flujo = '2'
					AND t.t_etapa_actual = 3
				ORDER BY folio";
			
			$_SESSION['query'] = $sql;
			$_SESSION['nombreReporte'] = "reporteDeAnticiposGeneradosYRegistradosEnSAP";
			$_SESSION['head'] = "REPORTE DE ANTICIPOS GENERADOS Y REGISTRADOS EN SAP";	 	
		
		}elseif($_SESSION['reporte']== "Reporte de Gastos AMEX no comprobados" ){	 	
			$sql = "SELECT dc_idamex, et_etapa_nombre
				FROM detalle_comprobacion, comprobaciones, tramites, etapas
				WHERE dc_comprobacion = co_id
				AND co_mi_tramite = t_id
				AND t_flujo = et_flujo_id
				AND t_etapa_actual = et_etapa_id
				AND dc_idamex != 0
				AND (et_etapa_nombre = 'Sin Enviar' OR et_etapa_nombre = 'Rechazada por Director' OR et_etapa_nombre = 'Rechazada')
				GROUP BY dc_idamex
			
			UNION ALL
			
			SELECT dc_idamex_comprobado as dc_idamex, et_etapa_nombre
				FROM detalle_comprobacion_gastos, comprobacion_gastos, tramites, etapas
				WHERE dc_comprobacion = co_id
				AND co_mi_tramite = t_id
				AND t_flujo = et_flujo_id
				AND t_etapa_actual = et_etapa_id
				AND dc_idamex_comprobado != 0
				AND (et_etapa_nombre = 'Sin Enviar' OR et_etapa_nombre = 'Rechazada por Director' OR et_etapa_nombre = 'Rechazada')
				GROUP BY dc_idamex_comprobado";
			$res = mysql_query($sql);
			$array = array();
			$cont = 0;
			$var = "";
			while($row = mysql_fetch_assoc($res)){
				$array[$cont] = array($row["dc_idamex"],$row["et_etapa_nombre"]);
				$var.= " OR idamex = ".$row["dc_idamex"];
				$cont++;
			}
			
			$sql = "CREATE TABLE resultado AS
					SELECT DATE_FORMAT(fecha_cargo,'%d/%m/%Y') as FECHA, nombre AS EMPLEADO, SUBSTRING(notransaccion,29,9)  AS 'NÚMERO DE TRANSACCIÓN', 
					tarjeta AS 'NÚMERO DE TARJETA', IF(notarjetacredito_gas = tarjeta, 'Amex corporativa gasolina',IF(notarjetacredito = tarjeta,'Amex corporativa gastos','N/A')) AS 'TIPO DE TARJETA',
					concepto AS 'CONCEPTO', FORMAT(montoAmex,2) AS 'MONTO', monedaAmex AS 'MONEDA', IFNULL(rfc_establecimiento, '') AS 'RFC',
					IF((amex.estatus = 0 AND amex.comprobacion_id = 0),'PENDIENTE', 'SIN ENVIAR') AS estado
					FROM amex
					JOIN empleado ON tarjeta = notarjetacredito OR tarjeta =  notarjetacredito_gas 
					$parametros
					AND (
						(amex.estatus = 0 AND amex.comprobacion_id=0)
						OR 
						(
							(amex.estatus = 0 AND amex.comprobacion_id > 0) 
							OR 
							(amex.estatus > 0 AND amex.comprobacion_id = 0 AND amex.estatus != 2) 
							$var
						)
					)					
					GROUP BY idamex
					ORDER BY amex.fecha_cargo ASC	
					";
			
			$_SESSION['query'] = $sql;
			$_SESSION['nombreReporte'] = "reporteGastosAMEXnoComprobados";
			$_SESSION['head'] = "REPORTE GASTOS AMEX NO COMPROBADOS";
			
		 }elseif($_SESSION['reporte']== "Reporte de Descuentos" ){	
			
			$drop = "DROP TABLE  IF EXISTS resultado1";
			$res = $cnn->consultar($drop);									
		 
			$sql = "CREATE TABLE resultado1 as 
			SELECT
				t_id AS 'fc',
				IFNULL(t_etiqueta,cmp.co_motivo_gasolina) AS 'mc',
				DATE_FORMAT((SELECT MIN(svi_fecha_salida) FROM sv_itinerario WHERE svi_solicitud = sv_id),'%d/%m/%Y') AS 'fi',
				DATE_FORMAT((SELECT MAX(svi_fecha_llegada) FROM sv_itinerario WHERE svi_solicitud = sv_id),'%d/%m/%Y') AS 'ff',
				emp.idempleado AS 'nue',
				emp.nombre AS 'noe',
				CONCAT(cc.cc_centrocostos, '-', cc.cc_nombre) AS 'cc',
				ccp.cp_concepto AS 'co',
				dc_total AS 'md',
				IFNULL(dc_comentario,'') AS 'c', 
				'0' AS 'b'
			FROM tramites 			
			JOIN flujos  ON f_id = t_flujo AND t_flujo = '3'
			JOIN etapas et ON et.et_etapa_id = t_etapa_actual AND et.et_flujo_id = '3' AND t_etapa_actual = '3'
			JOIN comprobaciones cmp ON t_id = cmp.co_mi_tramite
			JOIN solicitud_viaje ON sv_tramite = co_tramite
			JOIN detalle_comprobacion dc ON dc.dc_comprobacion = cmp.co_id 			
			JOIN cat_conceptosbmw ccp ON ccp.dc_id = dc.dc_concepto
			JOIN empleado emp ON emp.idempleado = t_iniciador
			JOIN cat_cecos cc ON cc.cc_id = cmp.co_cc_clave		
			$parametros
			AND dc_concepto = '31'
			GROUP BY cmp.co_id, dc.dc_id
			
			UNION
			
			SELECT
				t_id AS 'fc',
				t_etiqueta AS 'mc',
				DATE_FORMAT(si_fecha_invitacion,'%d/%m/%Y') AS 'fi',
				'N/A' AS 'ff',
				idempleado AS 'nue',
				nombre AS 'noe',
				CONCAT(cc_centrocostos, '-', cc_nombre) AS 'cc',
				ccb.cp_concepto AS 'co',
				dc_total AS 'md',				
				IFNULL(dc_comentarios,'') AS 'c',
				'1' AS 'b'
			FROM tramites 						
			JOIN comprobacion_invitacion ON t_id = co_mi_tramite
			JOIN solicitud_invitacion ON si_tramite = co_tramite
			JOIN empleado ON idfwk_usuario = t_iniciador
			JOIN detalle_comprobacion_invitacion dc ON dc.dc_comprobacion = co_id 			
			JOIN cat_conceptosbmw ccb ON ccb.dc_id = dc.dc_concepto			
			JOIN cat_cecos ON cc_id = co_cc_clave		
			$parametros
			AND dc_concepto = '31'

			GROUP BY co_id, dc.dc_id";
			$res = $cnn->consultar($sql);	
			
			
			$sql = "SELECT * FROM resultado1";
			$res = $cnn->consultar($sql);
			$numregs = mysql_num_rows($res);
			
			$row = array();
			$insertSql = "INSERT INTO resultado1 VALUES";
			$aux = 0;
			while($row = mysql_fetch_assoc($res)){				
				$insertSql.= "('".$row['fc']."','".$row['mc']."','".$row['fi']."','".$row['ff']."','".$row['nue']."','".$row['noe']."','".$row['cc']."','".$row['co']."','".$row['md']."','".$row['c']."','".$row['b']."'),";
				
				if($aux != $row['fc'] && $row['b'] == 0){
					$aux = $row['fc'];
					$sqls = "SELECT (co_anticipo_viaje-co_anticipo_comprobado) as total FROM comprobaciones WHERE co_mi_tramite = $aux";
					$ress = $cnn->consultar($sqls);			
					$rows = mysql_fetch_assoc($ress);
					$insertSql.= "('".$row['fc']."','".$row['mc']."','".$row['fi']."','".$row['ff']."','".$row['nue']."','".$row['noe']."','".$row['cc']."','DIFERENCIA DE ANTICIPO','".$rows['total']."','','".$row['b']."'),";				
				}								
			}
			
			$truncateSql = "TRUNCATE resultado1";
			$res = $cnn->consultar($truncateSql);			
			
			if(count($row) > 0 && $numregs > 0){
				$insertSql = substr($insertSql,0,-1);
				$res = $cnn->consultar($insertSql);						
			}
			
			$sql = "CREATE TABLE resultado as	 			 
			SELECT fc AS 'FOLIO COMPROBACIÓN', IF(b=0,'COMPROBACION DE VIAJE','COMPROBACION DE INVITACION') AS 'TIPO DE COMPROBACION', mc AS 'MOTIVO DE COMPROBACIÓN', fi as 'FECHA SALIDA',
					ff AS 'FECHA LLEGADA', nue AS 'NÚMERO DE EMPLEADO', noe AS 'NOMBRE DE EMPLEADO', cc AS 'CENTRO DE COSTOS', co AS 'CONCEPTO', md as 'MONTO A DESCONTAR',
					c AS 'COMENTARIO'
			FROM resultado1
			ORDER BY fc, co";			
			
			$_SESSION['query'] = $sql;
			$_SESSION['nombreReporte'] = "reporteDeDescuentos";
			$_SESSION['head'] = "REPORTE DE DESCUENTOS";
			
		 }elseif($_SESSION['reporte']== "Reporte de Solicitudes" ){
			obtenerRutaNombresAutorizacion();
			$sql="CREATE TABLE resultado as
				SELECT t.t_id AS 'FOLIO',
					DATE_FORMAT(t.t_fecha_registro,'%d/%m/%Y') AS 'FECHA SOLICITUD',
					LEFT(u_usuario, 8) AS 'NÚMERO DE EMPLEADO',
					nombre AS 'EMPLEADO',
					CONCAT(cc_centrocostos,' - ',cc_nombre) AS 'CENTRO DE COSTOS',
					f_nombre AS 'DOCUMENTO',
					sv_motivo AS 'MOTIVO',
					sv_viaje AS 'TIPO DE VIAJE',
					(SELECT MIN(svi_fecha_salida) FROM sv_itinerario WHERE svi_solicitud = sv_id) AS 'FECHA DE SALIDA',
					IF(sv_viaje = 'Sencillo','N/A',(SELECT MAX(svi_fecha_llegada) FROM sv_itinerario WHERE svi_solicitud = sv_id)) AS 'FECHA DE REGRESO',	
					IF(sv_viaje = 'Multidestinos', 'Multidestinos', (SELECT svi_destino FROM sv_itinerario WHERE svi_solicitud = sv_id LIMIT 1) ) AS 'DESTINO',
					IF(IFNULL((SELECT SUM(svi_monto_vuelo_cotizacion) FROM sv_itinerario WHERE svi_solicitud = sv_id AND svi_monto_vuelo_cotizacion IS NOT NULL ),0)!=0,(SELECT SUM(svi_monto_vuelo_cotizacion) FROM sv_itinerario WHERE svi_solicitud = sv_id AND svi_monto_vuelo_cotizacion IS NOT NULL ),'N/A') AS 'TOTAL VUELOS',
					IF(IFNULL((SELECT SUM(h_total_pesos) FROM sv_itinerario svi JOIN hotel AS ho ON ho.svi_id = svi.svi_id WHERE svi.svi_solicitud = sv_id AND h_total_pesos IS NOT NULL),0)!=0,(SELECT SUM(h_total_pesos) FROM sv_itinerario svi JOIN hotel AS ho ON ho.svi_id = svi.svi_id WHERE svi.svi_solicitud = sv_id AND h_total_pesos IS NOT NULL),'N/A') AS 'TOTAL HOSPEDAJE',
					IF(IFNULL((SELECT SUM(svi_total_pesos_auto) FROM sv_itinerario WHERE svi_solicitud = sv_id AND svi_total_pesos_auto IS NOT NULL),0)!=0,(SELECT SUM(svi_total_pesos_auto) FROM sv_itinerario WHERE svi_solicitud = sv_id AND svi_total_pesos_auto IS NOT NULL),'N/A') AS 'TOTAL AUTO',				
					IF(sv_total_anticipo!=0,sv_total_anticipo,'N/A') AS 'ANTICIPO SOLICITADO',
					IF(sv_total!=0,sv_total,'N/A') AS 'TOTAL SOLICITUD',				
					et_etapa_nombre AS 'ETAPA',
					rut_ruta AS 'RUTA ESPERADA'
				FROM tramites t
				JOIN empleado ON idfwk_usuario = t.t_iniciador
				JOIN usuario ON idfwk_usuario = u_id
				JOIN solicitud_viaje ON sv_tramite = t.t_id
				JOIN cat_cecos ON sv_ceco_paga = cc_id
				JOIN flujos ON f_id = t.t_flujo
				JOIN etapas ON t.t_etapa_actual = et_etapa_id AND t.t_flujo = et_flujo_id
				JOIN rutatransformacion ON rut_id = t.t_id
				$parametros
				AND t.t_flujo = 1
				
			UNION
			
				SELECT t.t_id AS 'FOLIO',
					DATE_FORMAT(t.t_fecha_registro,'%d/%m/%Y') AS 'FECHA SOLICITUD',
					LEFT(u_usuario, 8) AS 'NÚMERO DE EMPLEADO',
					nombre AS 'EMPLEADO',
					CONCAT(cc_centrocostos,' - ',cc_nombre) AS 'CENTRO DE COSTOS',
					f_nombre AS 'DOCUMENTO',
					sg_motivo AS 'MOTIVO',
					'N/A' AS 'TIPO DE VIAJE',
					'N/A' AS 'FECHA DE SALIDA',
					'N/A' AS 'FECHA DE REGRESO',
					'N/A' AS 'DESTINO',
					'N/A' AS 'TOTAL VUELOS',
					'N/A' AS 'TOTAL HOSPEDAJE',
					'N/A' AS 'TOTAL AUTO',
					sg_monto AS 'ANTICIPO SOLICITADO',
					sg_monto AS 'TOTAL SOLICITUD',
					et_etapa_nombre AS 'ETAPA',
					rut_ruta AS 'RUTA ESPERADA'
				FROM tramites t
				JOIN empleado ON idfwk_usuario = t.t_iniciador
				JOIN usuario ON idfwk_usuario = u_id
				JOIN solicitud_gastos ON sg_tramite = t.t_id
				JOIN cat_cecos ON sg_ceco = cc_id
				JOIN flujos ON f_id = t.t_flujo
				JOIN etapas ON t.t_etapa_actual = et_etapa_id AND t.t_flujo = et_flujo_id
				JOIN rutatransformacion ON rut_id = t.t_id
				$parametros
				AND t.t_flujo = 2
			ORDER BY folio";
			
			$_SESSION['query'] = $sql;
			$_SESSION['nombreReporte'] = "reporteDeSolicitudes";
			$_SESSION['head'] = "REPORTE DE SOLICITUDES";			
			
		 }elseif($_SESSION['reporte']== "Reporte de Comprobaciones de Gasolina fuera de politica" ){
			
			$sql = "CREATE TABLE resultado AS
			SELECT t.t_id AS 'FOLIO', DATE_FORMAT(t.t_fecha_registro,'%d/%m/%Y') AS 'FECHA DE CREACIÓN', e.nombre AS 'EMPLEADO', CONCAT (cc.cc_centrocostos, ' - ' , cc.cc_nombre) AS 'CENTRO DE COSTOS',
				IFNULL(t.t_etiqueta, 'N/A')AS 'MOTIVO',	IFNULL(sv.sv_viaje, 'N/A') AS 'TIPO DE VIAJE', 
				IFNULL(svi.svi_tipo_viaje, 'N/A') AS 'ZONA DE VIAJE',				
				IFNULL(DATE_FORMAT(svi.svi_fecha_salida,'%d/%m/%Y'), DATE_FORMAT(cmp.co_fecha_inicial_gasolina,'%d/%m/%Y')) AS 'FECHA DE SALIDA',
				IF(sv_viaje = 'Redondo' OR sv_viaje = 'Sencillo', DATE_FORMAT(svi.svi_fecha_llegada,'%d/%m/%Y'),
					IF(sv_viaje = 'Multidestinos', DATE_FORMAT(MAX(svi.svi_fecha_llegada),'%d/%m/%Y'), DATE_FORMAT(cmp.co_fecha_final_gasolina,'%d/%m/%Y'))
				) AS 'FECHA DE LLEGADA',
				(SELECT SUM(dcs.dc_total)
					FROM detalle_comprobacion AS dcs
					WHERE dc_comprobacion = ex_comprobacion 
					AND dc_concepto = 12
					AND ((SELECT idamex FROM amex WHERE idamex = dcs.dc_idamex AND moneda_local = 'MXN' LIMIT 1) = dc_idamex_comprobado
						OR (dc_idamex_comprobado = 0  AND dc_divisa = 1)
						)
				) AS 'MONTO AUTORIZADO',
				d.div_nombre AS 'DIVISA',
				IFNULL((SELECT SUM(dc_total)
					FROM detalle_comprobacion dcs
					WHERE dc_origen_personal != 0 
					AND ((SELECT idamex FROM amex WHERE idamex = dcs.dc_idamex AND moneda_local = 'MXN' LIMIT 1) = dc_idamex_comprobado
						OR (dc_idamex_comprobado = 0  AND dc_divisa = 1)
						)
					AND dc_origen_personal = (SELECT dc_id FROM detalle_comprobacion  WHERE dc_id = dcs.dc_origen_personal AND dc_concepto = 12)
					AND dc_comprobacion = co_id
					),'N/A'
				) AS 'MONTO PERSONAL',						
				IFNULL(preg.pr_cantidad, '') AS 'TOLERANCIA', 
				IFNULL(di.div_nombre, 'N/A')AS 'DIVISA P.',		
				IFNULL((SELECT CONVERT(SUM(dc_total),DECIMAL(10,2))		
					FROM detalle_comprobacion AS dcs
					WHERE dc_comprobacion = ex_comprobacion 					
					AND dc_concepto = 12
					AND ((SELECT idamex FROM amex WHERE idamex = dcs.dc_idamex AND moneda_local = 'MXN' LIMIT 1) = dc_idamex
						OR (dc_idamex = 0  AND dc_divisa = 1)
						)
					) 					
					-					
					(pr_cantidad+(ma_factor * co_kilometraje)), ''
				) AS 'VALOR QUE ROMPE LA REGLA',
				((ma_factor * co_kilometraje)+pr_cantidad) AS 'MONTO LIMITE'
			
			FROM excepciones AS ex
			JOIN comprobaciones AS cmp ON cmp.co_id = ex.ex_comprobacion 
			JOIN detalle_comprobacion AS dc ON dc.dc_comprobacion = cmp.co_id
			JOIN cat_cecos AS cc ON cc.cc_id = cmp.co_cc_clave
			JOIN divisa AS d ON d.div_id = dc.dc_divisa
			JOIN tramites AS t ON t.t_id = cmp.co_mi_tramite
			JOIN empleado AS e ON e.idempleado = t.t_iniciador
			LEFT JOIN solicitud_viaje AS sv ON sv.sv_tramite = cmp.co_tramite
			LEFT JOIN sv_itinerario AS svi ON svi.svi_solicitud = sv.sv_id
			LEFT JOIN modelo_auto ON ma_id = co_modelo_auto
			JOIN cat_conceptosbmw ccp ON ccp.dc_id = dc.dc_concepto AND ccp.dc_id = '12'
			JOIN parametrosbmw pbmw ON pbmw.p_concepto = dc.dc_concepto
			JOIN parametro_regionbmw preg ON preg.p_id = pbmw.p_id
			JOIN cat_regionesbmw AS cr ON cr.re_id = preg.re_id AND preg.re_id = '1'
			JOIN divisa AS di ON di.div_id = preg.div_id
			$parametros
			AND ex.ex_comprobacion != 0
			AND ex.ex_comprobacion_detalle = 0 
			AND t.t_etapa_actual = 3
			AND d.div_id = 1
			AND ex_mensaje like '%monto por factor%'
			AND 1 = (SELECT dc_enviado_sap FROM detalle_comprobacion WHERE dc_enviado_sap = 1 AND dc_comprobacion = co_id LIMIT 1)
			GROUP BY cmp.co_id, ccp.cp_concepto";

			$_SESSION['query'] = $sql;
			$_SESSION['nombreReporte'] = "reporteDeComprobacionesDeGasolinaFueraDePolitica";
			$_SESSION['head'] = "REPORTE DE COMPROBACIONES DE GASOLINA FUERA DE POLITICA";	 	
		}elseif($_SESSION['reporte']== "ReporteComprobaciones" ){
			obtenerRutaNombresAutorizacion();
			$sql = "CREATE TABLE resultado AS
				SELECT	t.t_id AS 'FOLIO',
					dc_tipo_comprobacion AS 'TIPO DE COMPROBACIÓN',
					DATE_FORMAT(t.t_fecha_registro, '%d/%m/%Y') AS 'FECHA DE COMPROBACIÓN',	
					LEFT(u_usuario, 8) AS 'NÚMERO DE EMPLEADO',
					nombre AS 'NOMBRE DEL EMPLEADO',
					e_nombre AS 'EMPRESA',
					f_nombre AS 'DOCUMENTO',
					(IF(co_tramite = '-1', 'N/A', (SELECT DATE_FORMAT(t_fecha_registro, '%d/%m/%Y') FROM tramites WHERE t_id = co_tramite))) AS 'FECHA DE SOLICITUD',
					(IF(co_tramite = '-1', 'N/A', (SELECT t_id FROM tramites WHERE t_id = co_tramite))) AS 'FOLIO DE SOLICITUD',
					DATE_FORMAT(t.t_fecha_cierre, '%d/%m/%Y') AS 'FECHA DE AUTORIZACIÓN DE COMPROBACIÓN',
					(IF(co_tramite = '-1', co_motivo_gasolina, t.t_etiqueta)) AS 'MOTIVO', 
					(IF(co_tramite = '-1', 'N/A', co_anticipo_comprobado)) AS 'ANTICIPO COMPROBADO',
					CONCAT(cc_centrocostos,' - ',cc_nombre) AS 'CENTRO DE COSTOS',
					cp_concepto AS 'CONCEPTO',
					cp_cuenta AS 'CUENTA CONTABLE',
					dc_monto AS 'SUBTOTAL',
					dc_total AS 'MONTO TOTAL',
					div_nombre AS 'MONEDA',
					dc_rfc AS 'RFC',
					IFNULL(SUBSTRING(dc_notransaccion, 29, 9), 'N/A') AS 'TRANSACCION',
					IFNULL(CONVERT(notarjetacredito USING UTF8),'N/A') AS 'NÚMERO DE TARJETA',
					et_etapa_nombre AS 'ETAPA',
					rut_ruta AS 'RUTA DE AUTORIZACIÓN'
				FROM tramites t
				JOIN empleado ON idfwk_usuario = t.t_iniciador
				JOIN usuario ON idfwk_usuario = u_id
				JOIN comprobaciones ON co_mi_tramite = t.t_id
				JOIN detalle_comprobacion ON dc_comprobacion = co_id
				JOIN cat_cecos ON cc_id = co_cc_clave
				JOIN flujos ON f_id = t.t_flujo
				JOIN etapas ON et_etapa_id = t.t_etapa_actual AND et_flujo_id = t.t_flujo
				JOIN divisa ON div_id = dc_divisa
				JOIN empresas ON e_codigo = cc_empresa_id
				JOIN cat_conceptos ON cp_id = dc_concepto
				JOIN rutatransformacion ON rut_id = t.t_id
				$parametros
				AND t.t_flujo = 3
			
			UNION ALL

				SELECT	t.t_id AS 'FOLIO',
					dc_tipo AS 'TIPO DE COMPROBACIÓN',
					DATE_FORMAT(t.t_fecha_registro, '%d/%m/%Y') AS 'FECHA DE COMPROBACIÓN',	
					LEFT(u_usuario, 8) AS 'NÚMERO DE EMPLEADO',
					nombre AS 'NOMBRE DEL EMPLEADO',
					e_nombre AS 'EMPRESA',
					f_nombre AS 'DOCUMENTO',
					(IF(co_tramite = '-1', 'N/A', (SELECT DATE_FORMAT(t_fecha_registro, '%d/%m/%Y') FROM tramites WHERE t_id = co_tramite))) AS 'FECHA DE SOLICITUD',
					(IF(co_tramite = '-1', 'N/A', (SELECT t_id FROM tramites WHERE t_id = co_tramite))) AS 'FOLIO DE SOLICITUD',
					DATE_FORMAT(t.t_fecha_cierre, '%d/%m/%Y') AS 'FECHA DE AUTORIZACIÓN DE COMPROBACIÓN',
					(IF(co_tramite = '-1', co_motivo_gasolina, t.t_etiqueta)) AS 'MOTIVO', 
					(IF(co_tramite = '-1', 'N/A', co_anticipo_comprobado)) AS 'ANTICIPO COMPROBADO',
					CONCAT(cc_centrocostos,' - ',cc_nombre) AS 'CENTRO DE COSTOS',
					cp_concepto AS 'CONCEPTO',
					cp_cuenta AS 'CUENTA CONTABLE',
					dc_monto AS 'SUBTOTAL',
					dc_total AS 'MONTO TOTAL',
					div_nombre AS 'MONEDA',
					dc_rfc AS 'RFC',
					IFNULL(SUBSTRING(dc_notransaccion, 29, 9), 'N/A') AS 'TRANSACCION',
					IFNULL(CONVERT(notarjetacredito USING UTF8),'N/A') AS 'NÚMERO DE TARJETA',
					et_etapa_nombre AS 'ETAPA',
					rut_ruta AS 'RUTA DE AUTORIZACIÓN'
				FROM tramites t
				JOIN empleado ON idfwk_usuario = t.t_iniciador
				JOIN usuario ON idfwk_usuario = u_id
				JOIN comprobacion_gastos ON co_mi_tramite = t.t_id
				JOIN detalle_comprobacion_gastos ON dc_comprobacion = co_id
				JOIN cat_cecos ON cc_id = co_cc_clave
				JOIN flujos ON f_id = t.t_flujo
				JOIN etapas ON et_etapa_id = t.t_etapa_actual AND et_flujo_id = t.t_flujo
				JOIN divisa ON div_id = dc_divisa
				JOIN empresas ON e_codigo = cc_empresa_id
				JOIN cat_conceptos ON cp_id = dc_concepto
				JOIN rutatransformacion ON rut_id = t.t_id
				$parametros
				AND t.t_flujo = 4
			ORDER BY folio";
			
			$_SESSION['query'] = $sql;
			$_SESSION['nombreReporte'] = "ReporteComprobaciones";
			$_SESSION['head'] = "REPORTE DE COMPROBACIONES";	 	
		}
		///////////////////////////////////////////////////////////////////////////////////////////
		$res = mysql_query($sql);
		$sql = "SELECT * FROM resultado;";
		$res = mysql_query($sql);
		$count = mysql_num_rows($res);		

		if($count > 0){
			$msg1 = ($count == 1) ? "registro fue encontrado" : "registros fueron encontrados";
			$msg2 = ($count == 1) ? "registro encontrado" : "registros encontrados";
			if(($_POST["fechaFin"] == "") && ($_POST["fechaInicio"] == ""))
				echo "<center><font color=blue> $count $msg1.</font></center>";						
			else
				echo "<center><font color=blue>Reporte entre $fechaInicio y $fechaFin, $count $msg2.</font></center>";					
	?>
			<form action="exportar.php" method="get" name="rep1" target="_blank" id="rep1">
				<table align='center', width='30%'>    
					<tr><td colspan='2' align='center'>&nbsp;</td><tr>    
					<tr>
						<td colspan='2' align='center'>
							<INPUT TYPE="submit" name='exportar' id='exportar' value="Exportar a excel">
						</td>
					</tr>     
				</table>
			</form>
	<?php
		}else{
			echo "<center><font color=red>Sus par&aacute;metros de b&uacute;squeda no generaron resultados.</font></center>";
		}
	}
	$I->footer();
?>