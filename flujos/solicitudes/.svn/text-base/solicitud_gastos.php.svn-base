<?PHP
session_start(); 
require_once("../../lib/php/constantes.php");
require_once($RUTA_A."/functions/utils.php");
require_once($RUTA_A."/flujos/comprobaciones/services/func_comprobacion.php");
	
if (isset($_REQUEST['guardarSol']) || isset($_REQUEST['guardarprevSol'])){
	// Conexión a la BD
	$cnn = new conexion();
	
	date_default_timezone_set("America/Mexico_City");
	// Datos del Empleado
	$iniciador = $_POST['idusuario'];
	$empresa = $_POST['empresa'];
	$req_anticipo = 1;
	
	// Variables para registro de Solicitud
	$motivo = $_POST['motive'];
	$fechaGasto = $_POST['fecha_sol'];
	$lugarGasto = $_POST['lugar'];
	$montoSolicitado = $_POST['monto_solicitado'];
	$divisa = $_POST['divisa_solicitud'];
	$totalPesos = $_POST['monto_solicitado'];
	$ciudad = $_POST['ciudad'];
	$ceco = $_POST['ccentro_costos'];
	$observaciones = $_POST['observ'];
	$concepto = $_POST['sg_concepto'];
	
	// Validar si el Check de anticipo esta activo, encenderemos la bandera para indicar que requiere anticipo 
	if(!isset($_POST['reqAnticipo'])){
		$req_anticipo = 1;
	}
	
	// Guardamos la fecha en el formato de Mysql
	$fechaGasto = fecha_to_mysql($fechaGasto);
	
	// Limpiamos las cantidades(eliminamos la ',')
	$montoSolicitado = str_replace(',', '', $montoSolicitado);
	$totalPesos = str_replace(',', '', $totalPesos);
	
	// Bandera de Guardado Previo
	$guardadoPrevio = false;
	
	// Creación de las instancias
	$tramite = new Tramite();
	$solicitud = new SolicitudesGastos();
	$notificacion = new Notificacion();
	$centrocostos = new CentroCosto();
	$duenoActual = new Usuario();
	$comensales = new Comensales();
	
	$tramite->ejecutar("SET AUTOCOMMIT=0");
	$tramite->ejecutar("BEGIN");
	
	// Verificar las observaciones anotadas anteriormente
	if($_POST['tramiteId'] != 0){
		$solicitud->cargaGastoporTramite($_POST['tramiteId']);
		$historialObservaciones = $solicitud->Get_dato('sg_observaciones');
	}
	
	// Verificamos si contamos con un ID de Tramite entonces solo realizaremos actualizaciones, de lo contrario realizaremos las inserciones de los datos.
	if($_POST['tramiteId'] != 0){
		$idTramite = $_POST['tramiteId'];
		
		// Actualizar los datos del tramite
		$tramite->actualizarInfoTramite($idTramite, $motivo, $_SESSION['idrepresentante']);
		
		// Actualizamos la Solicitud de Gastos
		$solicitud->Edita_Gasto($motivo, $montoSolicitado, $totalPesos, $divisa, $ceco, $ciudad, $historialObservaciones, $observaciones, $fechaGasto, $lugarGasto, $req_anticipo, $concepto, $idTramite);
		
	}else{
		// Creamos el tramite
		$idTramite = $tramite->Crea_Tramite($iniciador, $empresa, SOLICITUD_GASTOS_ETAPA_SIN_ENVIAR, FLUJO_SOLICITUD_GASTOS, $motivo, $_SESSION['idrepresentante']);
		
		if($idTramite == 0 || $idTramite == null){
			header("Location: ./index.php?docs=docs&type=2&errsave");
		}
		
		// Creamos la Solicitud de Gastos
		$solicitudGastos = $solicitud->Crea_Gasto($motivo, $montoSolicitado, $totalPesos, $divisa, $ceco, $idTramite, $ciudad, '', $observaciones, $fechaGasto, $lugarGasto, $req_anticipo, $concepto);
		
		if($solicitudGastos == 0 || $solicitudGastos == null){
			header("Location: ./index.php?docs=docs&type=2&errsave");
		}
	}
	
	$tramite->Load_Tramite($idTramite);
	
	// Boraremos los invitados previamente cargados para evitar la inconsistencia de los datos
	$solicitud->cargaGastoporTramite($idTramite);
	$idSolicitud = $solicitud->Get_dato('sg_id');
	$comensales->Eliminar_Comensal_de_Solicitud($idSolicitud);
	
	// Inserción de los invitados 
	if($concepto == COMIDAS_REPRESENTACION){
		for($i = 1; $i <= $_POST['rowCount']; $i++){
			$nombreInvitado = $_POST['nombre'.$i];
			$puestoInvitado = $_POST['puesto'.$i];
			$tipoInvitado = $_POST['tipo'.$i];
			$empresaInvitado = $_POST['empresa'.$i];
			
			$comensales->Crea_Comensal($idSolicitud, $nombreInvitado, $puestoInvitado, $empresaInvitado, $tipoInvitado, 0);
		}
	}
	
	// Limpiar Excepciones
	$cnn = new conexion();
	$query = sprintf("DELETE excepciones FROM excepciones WHERE ex_solicitud = %s", $idSolicitud);
	$cnn->ejecutar($query);
	
	if(!isset($_REQUEST['guardarprevSol'])){
		// Guardado de las Excepciones
		for($j = 1; $j <= $_POST['totalExcepciones']; $j++){
			$e_concepto = $_POST['e_row_concepto'.$j];
			$e_mensaje = $_POST['e_row_mensaje'.$j];
			$e_fecha = $_POST['e_row_fecha'.$j];
			$e_referencia = 0;
			$e_totalPartida = $_POST['e_row_totalPartida'.$j];
			$e_diferencia = $_POST['e_row_diferencia'.$j];
		
			Add_excepcion($e_mensaje, $e_diferencia, $idSolicitud, 0, $e_referencia, $e_concepto, 0, 0);
		}
		
		$rutaAutorizacion = new RutaAutorizacion();
		
		/**
		 * Validacion y guardado de excepcion de presupuesto
		 **/
		$rutaAutorizadores = $rutaAutorizacion->generarRutaAutorizacion($idTramite, $_SESSION['idrepresentante']);
		// Obtener Autorizador
		$aprobador = $rutaAutorizacion->getSiguienteAprobador($idTramite, $iniciador);
		
		// Creamos el texto de las observaciones que serán enviadas
		if($observaciones != ''){
			$observaciones = $notificacion->anotaObservacion($iniciador, $historialObservaciones, $observaciones, FLUJO_SOLICITUD_GASTOS, SOLICITUD_GASTOS_ETAPA_SIN_ENVIAR, $_SESSION['idrepresentante']);
		}else{
			$observaciones = '';
		}
		
		// Actualizar las observaciones de la solicitud de gastos
		$solicitud->actualizaObservaciones($observaciones, '', $idTramite);
		
		$tramite->Modifica_Etapa($idTramite, SOLICITUD_GASTOS_ETAPA_APROBACION, FLUJO_SOLICITUD_GASTOS, $aprobador, $rutaAutorizadores, $_SESSION['idrepresentante']);
		
		// Definición del mensaje
		$mensaje = $tramite->crearMensaje($idTramite, SOLICITUD_GASTOS_ETAPA_APROBACION, true, false, $_SESSION['idrepresentante']);
		$tramite->EnviaNotificacion($idTramite, $mensaje, $iniciador, $aprobador, 1, ""); // "0" para no enviar email y "1" para enviarlo
		
	}else{
		$guardadoPrevio = true;
		// Actualizar las observaciones de la solicitud de gastos
		$solicitud->actualizaObservaciones($historialObservaciones, $observaciones, $idTramite);
		$tramite->Modifica_Etapa($idTramite, SOLICITUD_GASTOS_ETAPA_SIN_ENVIAR, FLUJO_SOLICITUD_GASTOS, $iniciador, "");
	}
	
	// Guardamos todos los cambios en la Base de Datos
	$tramite->ejecutar("COMMIT");
	
	if($guardadoPrevio){
		header("Location: ./index.php?docs=docs&type=2&oksaveP");
	}else{
		header("Location: ./index.php?docs=docs&type=2&oksave");
	}
	
} // End if Guardar Comprobación

// Nueva Solicitud
if (isset($_GET['new2'])) {
	// Definimos la Zona Horaria a utilizar
	date_default_timezone_set("America/Mexico_City"); ?>
	
	<!-- Estilos -->
	<link rel="stylesheet" href="../../css/jdpicker.css" type="text/css" media="screen" />
	<link rel="stylesheet" type="text/css" href="../../css/smoothness/jquery-ui-1.10.3.custom.min.css"/>
	<link rel="stylesheet" type="text/css" href="../../css/jquery.autocomplete.css"/>
	<link rel="stylesheet" type="text/css" href="../../css/date_input.css"/>
	<link rel="stylesheet" type="text/css" href="../../css/table_style.css"/>
	<link rel="stylesheet" type="text/css" href="../../css/style_Table_Edit.css"/>
	
	<style type="text/css">
		.style1 {color: #FF0000}
	</style>
	
	<!-- Inicia forma para solicitud -->
	<script type="text/javascript" src="../../lib/js/jquery/jquery-1.3.2.js" ></script>
	<script type="text/javascript" src="../../lib/js/jqueryui/jquery-ui.min.js"></script>
	<script type="text/javascript" src="../../lib/js/jquery/jquery.autocomplete.js" ></script>
	<script type="text/javascript" src="../../lib/js/jquery/jquery.bgiframe.js" ></script>
	<script type="text/javascript" src="../../lib/js/jquery/jquery.blockUI.js" ></script>
	<script type="text/javascript" src="../../lib/js/jquery/jquery.jdpicker.js"></script>
	<script type="text/javascript" src="../comprobaciones/js/backspaceGeneral.js" ></script>
	<script type="text/javascript" src="../../lib/js/formatNumber.js" ></script>
	<script type="text/javascript" src="../../lib/js/jquery/jquery.price_format.1.6.min.js"></script>
	<script type="text/javascript" src="js/cargarDatos.js" ></script>
	<script type="text/javascript" src="js/configuracionSolicitud.js" ></script>
	
	<form action="" method="post" name="sgastos" id="sgastos" >
		<h3 align="center">Solicitud de Gastos</h3>
		<table width="785" border="0" align="center" cellspacing="1" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left;">
			<tr>
				<td colspan="9">&nbsp;</td>
			</tr>
			<tr style="text-align:center;">
				<td colspan="9">Motivo<span class="style1">*</span>: <input type="text" id="motive" name="motive" size=50 maxlength="100" onchange="habilitaGuardado" onkeyup="habilitaGuardado();" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2" align="right">Fecha<span class="style1">*</span>:</td>
				<td align="left"><input name="fecha_sol" id="fecha_sol" value="<?PHP echo date('d/m/Y'); ?>" size="12" readonly="readonly" /></td>
				<td>&nbsp;</td>
				<td colspan="2" align="right"><div name="lugar_solicitud_etiqueta" id="lugar_solicitud_etiqueta" style="display:none;">Lugar/Restaurante<span class="style1">*</span>:</div></td>
				<td align="left"><div id="lugar_solicitud_campo" style="display:none;"><input type="text" name="lugar" id="lugar" value="" size="50" maxlength="100" /></div></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2" align="right">Concepto<span class="style1">*</span>:</td>
				<td align="left">
					<select id="sg_concepto" name="sg_concepto" onchange="verificaConcepto();" >
						<?PHP 
						$query = sprintf("SELECT c.cp_id AS cp_id, cp_concepto, cp_cuenta FROM cat_conceptos AS c 
								INNER JOIN conceptos_flujos AS cf ON (c.cp_id = cf.cp_id) 
								WHERE cf.f_id = %s AND cp_activo = 1 ORDER BY cp_concepto", FLUJO_SOLICITUD_GASTOS);
						$var = mysql_query($query);
						$opciones = "<option value='-1'>Seleccione</option>";
						
						while ($arr = mysql_fetch_assoc($var)){
							$opciones .= sprintf("<option value='%s'>%s</option>", $arr['cp_id'], $arr['cp_concepto']);
						}						echo $opciones; ?>
					</select>
				</td>
				<td>&nbsp;</td>
				<td colspan="2" align="right"><div name="ciudad_solicitud_etiqueta" id="ciudad_solicitud_etiqueta" style="display:none;">Ciudad<span class="style1">*</span>:</div></td>
				<td align="left"><div id="ciudad_solicitud_campo" style="display:none;"><input name="ciudad" type="text" id="ciudad" value="" size="50" maxlength="100" /></div></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="9">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="9"><div id="montoPolitica"></div></td>
			</tr>
			<tr>
				<td align="center" valign="middle" colspan= "9">
					<div id="seccion_inivitados" style="display:none;">
						<table border="0" cellspacing="1" style="border:1px #CCCCCC solid; margin-top:5px; margin-left:10px; margin-right:10px; text-align:left; background-color:#F8F8F8" >
                        <tr style="text-align:center;" >
                            <td colspan="4"><h3>Invitados</h3></td>
                        </tr>
                        <tr>  
                            <td>&nbsp;</td>
                            <td width="50%">Nombre<span class="style1">*</span>:&nbsp;&nbsp;<input name="nombre_invitado" type="text" id="nombre_invitado" size=50 maxlength="100" />
                            </td>
                            <td width="50%">Tipo de Invitado<span class="style1">*</span>:&nbsp;&nbsp;<select name="tipo_invitado" id="tipo_invitado" onchange="verificar_tipo_invitado();">
                                    <option value="-1">Seleccione...</option>
                                    <option value="Interno">Interno</option>
                                    <option value="Externo">Externo</option>
                                    <!-- <option value="Gobierno">Gobierno</option> -->
                                </select>

                            </td>
                            <td>&nbsp;</td>
                        </tr> 
                        <tr>
                            <td>&nbsp;</td>
                            <td width="50%">Puesto<span class="style1">*</span>:&nbsp;&nbsp;&nbsp;<input name="puesto_invitado" type="text" id="puesto_invitado" size=50 maxlength="100" />
                            </td>
                            <td width="50%">Empresa<span class="style1">*</span>:&nbsp;&nbsp;<input name="empresa_invitado" type="text" id="empresa_invitado" size=50 maxlength="100" disabled="disabled" />
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><div id="capaDirector">&nbsp;</div></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="4">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td colspan="2">
                                <div align="center">
                                    <input name="agregar_invitado" type="button" id="agregar_invitado" value="     Agregar Invitado" onclick="agregarInvitado();" style="background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;"/>
                                </div>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="4">&nbsp;</td>
                        </tr>
                        <tr><td>&nbsp;</td><td colspan="2" style="text-align:center">
                                <table id="invitado_table" class="tablesorter" cellspacing="1"> 
                                    <thead> 
                                        <tr> 
                                            <th width="5%" align="center" valign="middle">No.</th>
                                            <th width="30%" align="center" valign="middle">Nombre</th> 
                                            <th width="30%" align="center" valign="middle">Puesto</th>
                                            <th width="30%" align="center" valign="middle">Empresa</th>
                                            <th width="30%" align="center" valign="middle">Tipo</th>
                                            <th width="5%" align="center" valign="middle">Eliminar</th>
                                        </tr>
                                    </thead> 
                                    <tbody> 
                                     <tr><?php 
											// Obtener el puesto del empleado
                                            $idempleado = $_SESSION["idusuario"];
                                            $usuarioEmpleado = new Empleado();
                                            $usuarioEmpleado->cargaDatosEmpleadoUsuario($idempleado);
                                            $nombreUsuario = $usuarioEmpleado->Get_dato('nombre');
                                            $empresaId = $usuarioEmpleado->Get_dato('u_empresa');
                                            $puesto = $usuarioEmpleado->Get_dato('npuesto');
                                            $interno = $usuarioEmpleado->Get_dato('u_interno');
                                            $interno = ($interno) ? "Interno" : "";
                                            
                                            // Obtener el nombre de la empresa
                                            $datosEmpresa = new Empresa();
                                            $datosEmpresa->Load_Empresa($empresaId);
                                            $nombreEmpresa = $datosEmpresa->Get_Dato('e_nombre');
                                            ?>
                                            <td><div id='renglon1'>1</div><input type="hidden" name="row1" id="row1" value="1" readonly='readonly'/></td>
                                            <td><?PHP echo $nombreUsuario; ?><input type="hidden" name="nombre1" id="nombre1" value="<?PHP echo $nombreUsuario; ?>" /></td> 
                                            <td><?PHP echo $puesto; ?><input type="hidden" name="puesto1" id="puesto1" value="<?PHP echo $puesto; ?>" /></td>
                                            <td align="center"><?PHP echo $nombreEmpresa; ?><input type="hidden" name="empresa1" id="empresa1" value="<?PHP echo $nombreEmpresa; ?>" /></td>
                                            <td align="center"><?php echo $interno; ?><input type="hidden" name="tipo1" id="tipo1" value="<?php echo $interno; ?>" /></td>
                                            <td><div align='center'><img id="1del" class="elimina" style="cursor:pointer;" onmousedown="borrarPartida(this.id);" name="1del" alt="Click aquí para Eliminar" src="../../images/delete.gif"></div><div align="center">Eliminar Partida</div></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="4">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2">N&uacute;mero de invitados<span class="style1">*</span>:&nbsp;
                                <input type="text" name="numInvitados" id="numInvitados" value="1" size="15" readonly="readonly" /></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="9"><div>&nbsp;</div></td>
			</tr>
			<tr>
				<td colspan="9">
					<table border="0" cellspacing="6" width="750" align="center" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">
						<tr>
							<td colspan="6">&nbsp;</td>
						</tr>
						<tr>
							<td width="3%">&nbsp;</td>
							<td colspan="4">Centro de Costos<span class="style1">*</span>:&nbsp;
								<select name="ccentro_costos" id="ccentro_costos" onchange="getCentroCostos();">
									<option id="-1" value="-1">Seleccione...</option>
									<?php 
									$iduser = $_SESSION["idusuario"];
									$query = sprintf("SELECT cc_centrocostos FROM cat_cecos INNER JOIN empleado ON (idcentrocosto = cc_id) WHERE idfwk_usuario = '%s'", $iduser);
									$var = mysql_query($query);
									$CECOempleado="";
									while ($arr = mysql_fetch_assoc($var)) {
										$CECOempleado.=$arr['cc_centrocostos'];
									}
									
									$query = sprintf("SELECT c.cc_id, c.cc_centrocostos, c.cc_nombre from cat_cecos c INNER JOIN empleado e ON c.cc_id = e.idcentrocosto WHERE c.cc_empresa_id = '" . $_SESSION["empresa"] . "' AND c.cc_estatus = 1 AND e.idempleado = " . $_SESSION["idusuario"] . " order by c.cc_centrocostos");
									$var = mysql_query($query);
									
									while ($arr = mysql_fetch_assoc($var)){
										if(isset($_GET['id'])){
											echo sprintf("<option value='%s'>%s - %s</option>", $arr['cc_id'], $arr['cc_centrocostos'], $arr['cc_nombre']);
										}else{
											if($arr['cc_centrocostos'] == $CECOempleado){
												echo sprintf("<option value='%s' selected='selected'>%s - %s</option>", $arr['cc_id'], $arr['cc_centrocostos'], $arr['cc_nombre']);
											}else{
												echo sprintf("<option value='%s'>%s - %s</option>", $arr['cc_id'], $arr['cc_centrocostos'], $arr['cc_nombre']);
											}
										}
									} ?>
								</select>
							</td>
							<td>Monto<span class="style1">*</span>:&nbsp;
								<input id="monto_solicitado" name="monto_solicitado" type="text" maxlength="100" value="0"/>&nbsp;<strong>MXN</strong>
							</td>
							<td id="td_Divisa">Divisa<span class="style1">*</span>:&nbsp;
								<select name="divisa_solicitud" id="divisa_solicitud" onchange="recalculaMontos();">
									<option value="-1">Seleccione...</option>
									<option value="1" selected="selected">MXN</option>
									<option value="2">USD</option>
									<option value="3">EUR</option>
								</select>
							</td>
						</tr>
						<tr id="tr_Anticipo">
							<td>&nbsp;</td>
							<td colspan="3">Total en Pesos:&nbsp;
								<input type="text" name="tpesos" id="tpesos" value="0.00" size="15" readonly="readonly" /> MXN
							</td>
							<td><div>Requiere anticipo: <input type="checkbox" name="reqAnticipo" id="reqAnticipo" <?php if(!isset($_GET['id'])){ echo "checked='checked'"; }?> /></div></td>
							<td><input type="hidden" name="banderavalida" id="banderavalida" readonly="readonly" /></td>
						</tr>
						<tr>
							<td colspan="6"><div id="capaWarning"></div></td>
						</tr>
						<?php if(isset($_GET['id'])){ ?>
						<tr>
							<td colspan="6" align="center" valign="middle">
		                        <table border="0">
		                        	<tr>
		                        		<td width="3%">&nbsp;</td>
										<td width="26%" valign="top"><div id="historial_div" align="right">Historial de Observaciones:</div></td>
										<td colspan="3" rowspan="1" class="alignLeft" ><textarea name="historial_observaciones" id="historial_observaciones" cols="80" rows="5" readonly="readonly" onkeypress="confirmaRegreso('historial_observaciones');" onkeydown="confirmaRegreso('historial_observaciones');"></textarea></td>
										<td width="5%">&nbsp;</td>
		                        	</tr>
		                        </table>
	                        </td>
						</tr>
						<tr>
							<td colspan="6">&nbsp;</td>
						</tr>
						<?php } ?>
						<tr>
							<td colspan="6" align="center" valign="middle">
							<table border="0">
								<tr>
									<td width="3%">&nbsp;</td>
									<td width="24%" valign="top"><div id="obsjus" align="right">Observaciones<span class="style1">*</span>:</div></td>
									<td colspan="3" ><div id="areatext"><textarea name="observ" cols="80" rows="5" id="observ"></textarea></div></td>
									<td width="1%">&nbsp;</td>
								</tr>
							</table>
							</td>
						</tr>
						<tr>
							<td colspan="6">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="9"><div>&nbsp;</div></td>
			</tr>
		</table>
		<br />
		<div align="center">
			<input type="button" id="guardarprevSol" name="guardarprevSol" value="     Guardar Previo" onclick="solicitarConfirmPrevio();" style="background:url(../../images/save.gif); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" readonly="readonly" disabled="disabled"/>&nbsp;&nbsp;&nbsp;
			<input type="button" id="guardarSol" name="guardarSol" value="     Enviar Solicitud"  onclick="validaCampos();" style="background:url(../../images/save.gif); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;" readonly="readonly" disabled="disabled"/>
		</div>
		<!-- Inicio Tabla de Partidas de Excepciones -->
		<table id="excepcion_table" class="tablesorter" cellspacing="1">
			<thead>
				<tr>
					<th>No.</th>
					<th>Concepto</th>
					<th>Mensaje</th>
					<th>Fecha</th>
					<th>Partidas</th>
					<th>Total</th>
					<th>Excedente</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
		<!-- Fin Tabla de Partidas -->
		<div>
			<input type="text" id="tasaUSD" name="tasaUSD" value="0" size="10" />
			<input type="text" id="tasaEUR" name="tasaEUR" value="0" size="10" />
			<input type="text" id="rowCount" name="rowCount" value="1" readonly="readonly"/>
			<input type="text" name="idusuario" id="idusuario" value="<?PHP echo $_SESSION["idusuario"]; ?>" readonly="readonly" />
			<input type="text" name="empresa" id="empresa" value="<?PHP echo $_SESSION["empresa"]; ?>" readonly="readonly" />
			<input type="text" id="tramiteId" name="tramiteId" value="<?PHP if(isset($_GET['id'])){ echo $_GET['id']; }else{ echo"0";} ?>" size="5" />
			<input type="text" name="totalExcepciones" id="totalExcepciones" value="0" />
		</div>
	</form>
<?php } ?>