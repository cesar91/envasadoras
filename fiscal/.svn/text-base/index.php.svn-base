<?php
	session_start();
	require_once("../lib/php/constantes.php");
	require_once "$RUTA_A/Connections/fwk_db.php";
	require_once "$RUTA_A/functions/utils.php";
	require_once('../lib/php/utils.php');
	
	$I = new Interfaz("Reportes",true);
	$cnn = new conexion();
	
	if(isset($_POST['buscar'])){
		$parametros = "WHERE 1 = 1 ";
		if(!empty($_POST['EmpresaOpciones'])){
			$empresa = new Empresa();
			$empresa->Load_EmpresaporID($_POST['EmpresaOpciones']);
			$idEmpresa = $empresa->Get_Dato("e_id");
			$parametros .= sprintf(" AND emp.e_id = '%s'", $idEmpresa);
		}
		
		$parametros .= (!empty($_POST['rfc'])) ? sprintf(" AND fact.e_rfc = %s ", $_POST['rfc']) : "";
		$parametros .= (!empty($_POST['fechaInicio'])) ? sprintf(" AND fact.f_timbrado >= '%s 00:00:00' ", fecha_to_mysql($_POST['fechaInicio'])) : "";
		$parametros .= (!empty($_POST['fechaFin'])) ? sprintf(" AND fact.f_timbrado <= '%s 00:00:00' ", fecha_to_mysql($_POST['fechaFin'])) : "";
		$parametros .= (!empty($_POST['empleados'])) ? sprintf(" AND t.t_iniciador  = '%s' ", $_POST['empleados']) : "";
		
		$queryDropTables = "DROP TABLE IF EXISTS resultado";
		$cnn->ejecutar($queryDropTables);
		
		$sql = "CREATE TABLE resultado AS
				SELECT	t.t_id AS 'FOLIO',
					dc_tipo_comprobacion AS 'TIPO DE COMPROBACION',
					DATE_FORMAT(t.t_fecha_registro, '%d/%m/%Y') AS 'FECHA DE COMPROBACION',	
					LEFT(u_usuario, 8) AS 'NUMERO DE EMPLEADO',
					nombre AS 'NOMBRE DEL EMPLEADO',
					emp.e_nombre AS 'EMPRESA',
					f_nombre AS 'DOCUMENTO',
					fact.id_factura AS 'FACTURA ID',
					DATE_FORMAT(fact.f_timbrado, '%d/%m/%Y') AS 'FECHA EMISION FACTURA',
					fact.e_rfc AS 'RFC EMISOR',
					fact.uuid AS 'FOLIO FISCAL - UUID', 
					fact.e_nombre AS 'APELLIDO PATERNO MATERNO NOMBRE(S) O DENOMINACION O RAZON SOCIAL',
					CONCAT(fact.e_domicilio, ', ', fact.e_estado, ', ', fact.e_pais) AS 'DIRECCION FISCAL',
					(IF(co_tramite = '-1', 'N/A', (SELECT DATE_FORMAT(t_fecha_registro, '%d/%m/%Y') FROM tramites WHERE t_id = co_tramite))) AS 'FECHA DE SOLICITUD',
					(IF(co_tramite = '-1', 'N/A', (SELECT t_id FROM tramites WHERE t_id = co_tramite))) AS 'FOLIO DE SOLICITUD',
					DATE_FORMAT(t.t_fecha_cierre, '%d/%m/%Y') AS 'FECHA DE AUTORIZACIÓN DE COMPROBACION',
					(IF(co_tramite = '-1', co_motivo_gasolina, t.t_etiqueta)) AS 'MOTIVO', 
					(IF(co_tramite = '-1', 'N/A', co_anticipo_comprobado)) AS 'ANTICIPO COMPROBADO',
					CONCAT(cc_centrocostos,' - ',cc_nombre) AS 'CENTRO DE COSTOS',
					cp_concepto AS 'CONCEPTO',
					cp_cuenta AS 'CUENTA CONTABLE',
					dc_monto AS 'SUBTOTAL',
					dc_total AS 'MONTO TOTAL',
					div_nombre AS 'MONEDA',
					IFNULL(SUBSTRING(dc_notransaccion, 29, 9), 'N/A') AS 'TRANSACCION',
					IFNULL(CONVERT(notarjetacredito USING UTF8),'N/A') AS 'NÚMERO DE TARJETA',
					et_etapa_nombre AS 'ETAPA',
					rut_ruta AS 'RUTA DE AUTORIZACION'
				FROM tramites t

				JOIN empleado ON idfwk_usuario = t.t_iniciador
				JOIN usuario ON idfwk_usuario = u_id

				JOIN comprobaciones ON co_mi_tramite = t.t_id
				JOIN detalle_comprobacion ON dc_comprobacion = co_id
				LEFT JOIN factura fact ON detalle_comprobacion.id_factura = fact.id_factura

				JOIN cat_cecos ON cc_id = co_cc_clave
				JOIN flujos ON f_id = t.t_flujo
				JOIN etapas ON et_etapa_id = t.t_etapa_actual AND et_flujo_id = t.t_flujo
				JOIN divisa ON div_id = dc_divisa
				JOIN empresas emp ON e_codigo = cc_empresa_id
				JOIN cat_conceptos ON cp_id = dc_concepto

				LEFT JOIN rutatransformacion ON rut_id = t.t_id
				$parametros
				AND t.t_flujo = 3
			
			UNION

				SELECT	t.t_id AS 'FOLIO',
					dc_tipo AS 'TIPO DE COMPROBACION',
					DATE_FORMAT(t.t_fecha_registro, '%d/%m/%Y') AS 'FECHA DE COMPROBACION',
					LEFT(u_usuario, 8) AS 'NUMERO DE EMPLEADO',
					nombre AS 'NOMBRE DEL EMPLEADO',
					emp.e_nombre AS 'EMPRESA',
					f_nombre AS 'DOCUMENTO',
					fact.id_factura AS 'FACTURA ID',
					DATE_FORMAT(fact.f_timbrado, '%d/%m/%Y') AS 'FECHA EMISION FACTURA',
					fact.e_rfc AS 'RFC EMISOR',
					fact.uuid AS 'FOLIO FISCAL - UUID', 
					fact.e_nombre AS 'APELLIDO PATERNO MATERNO NOMBRE(S) O DENOMINACION O RAZON SOCIAL',
					CONCAT(fact.e_domicilio, ', ', fact.e_estado, ', ', fact.e_pais) AS 'DIRECCION FISCAL',
					(IF(co_tramite = '-1', 'N/A', (SELECT DATE_FORMAT(t_fecha_registro, '%d/%m/%Y') FROM tramites WHERE t_id = co_tramite))) AS 'FECHA DE SOLICITUD',
					(IF(co_tramite = '-1', 'N/A', (SELECT t_id FROM tramites WHERE t_id = co_tramite))) AS 'FOLIO DE SOLICITUD',
					DATE_FORMAT(t.t_fecha_cierre, '%d/%m/%Y') AS 'FECHA DE AUTORIZACIÓN DE COMPROBACION',
					(IF(co_tramite = '-1', co_motivo_gasolina, t.t_etiqueta)) AS 'MOTIVO', 
					(IF(co_tramite = '-1', 'N/A', co_anticipo_comprobado)) AS 'ANTICIPO COMPROBADO',
					CONCAT(cc_centrocostos,' - ',cc_nombre) AS 'CENTRO DE COSTOS',
					cp_concepto AS 'CONCEPTO',
					cp_cuenta AS 'CUENTA CONTABLE',
					dc_monto AS 'SUBTOTAL',
					dc_total AS 'MONTO TOTAL',
					div_nombre AS 'MONEDA',
					IFNULL(SUBSTRING(dc_notransaccion, 29, 9), 'N/A') AS 'TRANSACCION',
					IFNULL(CONVERT(notarjetacredito USING UTF8),'N/A') AS 'NÚMERO DE TARJETA',
					et_etapa_nombre AS 'ETAPA',
					rut_ruta AS 'RUTA DE AUTORIZACION'
				FROM tramites t

				JOIN empleado ON idfwk_usuario = t.t_iniciador
				JOIN usuario ON idfwk_usuario = u_id

				JOIN comprobacion_gastos ON co_mi_tramite = t.t_id
				JOIN detalle_comprobacion_gastos ON dc_comprobacion = co_id
				LEFT JOIN factura fact ON detalle_comprobacion_gastos.id_factura = fact.id_factura

				JOIN cat_cecos ON cc_id = co_cc_clave
				JOIN flujos ON f_id = t.t_flujo
				JOIN etapas ON et_etapa_id = t.t_etapa_actual AND et_flujo_id = t.t_flujo
				JOIN divisa ON div_id = dc_divisa
				JOIN empresas emp ON e_codigo = cc_empresa_id
				JOIN cat_conceptos ON cp_id = dc_concepto

				LEFT JOIN rutatransformacion ON rut_id = t.t_id
				$parametros
				AND t.t_flujo = 4
			ORDER BY folio";
		
		$_SESSION['query'] = $sql;
		$_SESSION['nombreReporte'] = "Reporte Fiscal";
		$_SESSION['head'] = "Reporte Fiscal";
		
		$res = mysql_query($sql);
		$sql = "SELECT * FROM resultado";
		$res = mysql_query($sql);
		$count = mysql_num_rows($res);
		
		if($count > 0){
			$msg1 = ($count == 1) ? "registro fue encontrado" : "registros fueron encontrados";
			$msg2 = ($count == 1) ? "registro encontrado" : "registros encontrados";
			
			if(($_POST["fechaFin"] == "") && ($_POST["fechaInicio"] == ""))
				$mensaje = "<center><font color=blue> $count $msg1.</font></center>";
			else
				$mensaje = "<center><font color=blue>Reporte entre ".fecha_to_mysql($_POST['fechaInicio'])." y ".fecha_to_mysql($_POST['fechaFin']).", $count $msg2.</font></center>";
		}else{
			$mensaje = "<center><font color=red>Los par&aacute;metros de b&uacute;squeda no generaron resultados.</font></center>";
		}
	}
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
	<div align="center">
		<h3>Reporte Fiscal</h3>
	</div>
	<form action="index.php" method="post" name="formReporte">
		<table align='center'>
			<tr>
				<td align="right" valign="middle"><span class="classFiltros">Empresa:</span></td>
				<td>
					<?php $empresa = new Empresa(); ?>
					<select name="EmpresaOpciones" id="EmpresaOpciones">
						<option value=""></option>
						<?php foreach($empresa->Load_all() as $arrE){ ?>
							<option name="EmpresaOpciones" id="EmpresaOpciones" value="<?php echo $arrE['e_id'];?>"><?php echo $arrE['e_nombre'];?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td  colspan='1' align='right'><span class="classFiltros">RFC:</span></td>
				<td colspan="2"><input type="text" name="rfc" id="rfc" /></td>
			</tr>
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
			<tr>
				<td colspan='2'>&nbsp;</td>
			</tr>
			<tr>
				<td colspan='2' align='center'>
					<input type="submit" name='buscar' id='buscar' value="Buscar">
				</td>
			</tr>
		</table>
	</form>
	<div align="center"><?php echo $mensaje;?></div>
	<?php if(!empty($count)){ ?>
	<div id="div_exportar">
		<form action="../reportes/exportar.php" method="get" name="rep1" target="_blank" id="rep1">
			<table align='center', width='30%'>
				<tr>
					<td colspan='2' align='center'>&nbsp;</td>
				<tr>
				<tr>
					<td colspan='2' align='center'><input TYPE="submit" name='exportar' id='exportar' value="Exportar a excel"></td>
				</tr>
		</table>
		</form>
	</div>
	<?php } ?>
	<?php $I->footer(); ?>