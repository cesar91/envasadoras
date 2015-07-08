<?php 
	require_once("../../lib/php/constantes.php");
	require_once "../../Connections/fwk_db.php";
	require_once("$RUTA_A/functions/utils.php");
	require_once("$RUTA_A/lib/php/mobile_device_detect.php");
	
	$tipoUsuario    = $_SESSION["perfil"];
	
	if(isset($_REQUEST["modo"])){
		$cnn = new conexion();
		// Direccionara a index en Rechazo
		$redirect = "";
		$siguienteEtapa = COMPROBACION_GASTOS_ETAPA_APROBACION;
		$finRuta = false;
		$mail = 1;
		
		$modo = $_REQUEST["modo"];
		$idTramite = $_POST["idT"];
		$delegado = $_POST['delegado'];
		$idusuario = $_POST['iu'];
		$perfil = $_POST['perfil'];
		$privilegios = $_POST['privilegios'];
		$etapa = $_POST['t_etapa_actual'];
		$rows = $_POST['total_rows'];
		$observaciones = $_POST['campo_observaciones'];
		$historialObservaciones = $_POST["campo_historial"];
		$cecoNuevo = $_POST['centro_de_costos_new'];		
		$cecoOriginal = $_POST['centro_de_costos_old'];		
		$divisaEuro = $_POST["valorDivisaEUR"];
		$divisaDolar = $_POST["valorDivisaUSD"];
		
		$tramite = new Tramite();
		$rutaAutorizacion = new RutaAutorizacion();
		$notificacion = new Notificacion();
		$comprobaciones = new ComprobacionGastos();
		$detalleComprobacion = new DetalleComprobacionGastos();
		$concepto = new Concepto();
		$usuario = new Usuario();
		
		// Iniciar transaccion en caso de que existir un error
		$tramite->ejecutar("SET AUTOCOMMIT = 0");
		$tramite->ejecutar("BEGIN");
		
		// Informacion del Tramite
		$tramite->Load_Tramite($idTramite);
		$t_ruta_autorizacion = $tramite->Get_dato("t_ruta_autorizacion");
		$t_delegado = $tramite->Get_dato("t_delegado");
		$t_dueno = $tramite->Get_dato("t_dueno");
		$t_iniciador = $tramite->Get_dato("t_iniciador");
		
		// Guardado de Observaciones
		if($observaciones != ""){
			$notificacion = new Notificacion();
			$observaciones = $notificacion->anotaObservacion($t_dueno, $historialObservaciones, $observaciones, FLUJO_COMPROBACION_GASTOS, COMPROBACION_ETAPA_EN_APROBACION);
			$comprobaciones->actualizaObservaciones($observaciones, "", $idTramite);
		}
		
		//Informacion de la Comprobacion
		$comprobaciones->cargaComprobacionGastosporTramite($idTramite);
		 
		// Obtener ID de la Comprobacion de Gastos
		$co_id = $comprobaciones->Get_dato('co_id');
		
		// Definicion de mensajes Autorizadores/Usuario
		$mensajeAutorizadores = $tramite->crearMensaje($idTramite, COMPROBACION_GASTOS_ETAPA_APROBACION, true, true, $delegado);
		//error_log("--->>".$mensajeAutorizadores."<br />");
		
		if($modo == "reasignar"){
			$redirect = "errAut";
			
			// El centro de Costos tiene siempre que ser diferente
			if($cecoNuevo != $cecoOriginal){
				// Obtener aprobador
				$aprobador = $rutaAutorizacion->AutorizarFinanzas($idTramite, $cecoNuevo, 1);
				
				// Guardar el CECO de la Comprobacion
				$comprobaciones->actualizarCECO($cecoNuevo, $idTramite);
				
				$agrup_usu = new AgrupacionUsuarios();
				$agrup_usu->Load_Grupo_de_Usuario_By_ID($t_dueno);
				$finanzas = $agrup_usu->Get_dato("au_nombre");
				$mensajeUsuario = sprintf("La Comprobaci&oacute;n de Gastos <strong>%05s</strong> ha sido <strong>MODIFICADA</strong> por <strong>%s</strong>.", $idTramite, $finanzas);
				
				$redirect = "action=autorizar";
				
				$tramite->Load_Tramite($idTramite);
				$t_ruta_autorizacion = $tramite->Get_dato('t_ruta_autorizacion');
			}
		}
		
		if($modo == "autorizar"){
			// Obtener los datos del Resumen
			$anticipoComprobado = $_POST["anticipoComprobado"];
			$personalComprobado = $_POST["personalComprobado"];
			$amexComprobado = $_POST["amexComprobado"];
			$efectivoComprobado = $_POST["efectivoComprobado"];
			$amexExternoComprobado = $_POST["amexExterno"];
			$montoDescontar = $_POST["montoDescontar"];
			$montoReembolsar = $_POST["montoReembolsar"];
			$totalComprobado = $_POST['totalComprobacion'];
			
			// Actualizar los montos del Resumen en la pantalla de Finanzas
			$comprobaciones->actualizarResumen($idTramite, $anticipoComprobado, $personalComprobado, $amexComprobado, $efectivoComprobado, $montoDescontar, $montoReembolsar, $amexExternoComprobado, $totalComprobado);
			
			// Borramos todos los Registros Personales de la Comprobación
			$detalleComprobacion->limpiar_detalles($idTramite);
			
			for($i = 1; $i <= $rows; $i++){
				$tipoComprobacion = $_POST['row_tipoComprobacion'.$i];
				$noTransaccion = ($_POST['row_noTransaccion'.$i] == "N/A") ? 0 : $_POST['row_noTransaccion'.$i] ;
				$cargoTarjeta = $_POST['row_cargoTarjeta'.$i];
				$concepto = $_POST['row_concepto'.$i];
				$tipoComida = ($_POST['row_tipoComida'.$i] == "N/A") ? 0 : $_POST['row_tipoComida'.$i];
				$comentario = $_POST['row_comentario'.$i];
				$asistentes = $_POST['row_asistentes'.$i];
				$fecha = $_POST['row_fecha'.$i];
				$rfc = ($_POST['row_rfc'.$i] == "N/A") ? 0 : $_POST['row_rfc'.$i];
				$folio = $_POST['row_folio'.$i];
				$monto = $_POST['row_monto'.$i];
				$iva = $_POST['row_iva'.$i];
				$propina = $_POST['row_propina'.$i];
				$impuestoHospedaje = $_POST['row_impuestoHospedaje'.$i];
				$total = $_POST['row_total'.$i];
				$divisa = $_POST['row_divisa'.$i];
				$totalPartida = $_POST['row_totalPartida'.$i];
				$dc_origen_personal = $_POST["row_origenPersonal".$i];
				$dc_id = $_POST['row_id'.$i];
				$default = ($dc_id != "") ? $dc_id : "DEFAULT";
				$fecha = fecha_to_mysql($fecha);
				
				$idDetalle = $detalleComprobacion->agregaDetalle($co_id, $tipoComprobacion, $cargoTarjeta, $noTransaccion, $concepto, $tipoComida, $monto, $divisa, $iva, $total, $totalPartida, $comentario, $asistentes, $fecha, "", 0, 0, $rfc, $propina, $impuestoHospedaje, $totalPartida);
				if($idDetalle == 0 || $idDetalle == null)
					exit(header("Location: ./index.php?docs=docs&type=4&errsaveAut"));
			}
			
			// Obtener aprobador
			$aprobador = $rutaAutorizacion->AutorizarFinanzas($idTramite, "", 0);
				
			if($aprobador == ""){
				$finRuta = true;
				$aprobador = $t_iniciador;
			}
			
			$agrup_usu = new AgrupacionUsuarios();
			$agrup_usu->Load_Grupo_de_Usuario_By_ID($t_dueno);
			$finanzas = $agrup_usu->Get_dato("au_nombre");
			$mensajeAutorizadores = sprintf("La Comprobaci&oacute;n de Gastos <strong>%05s</strong> ha sido <strong>AUTORIZADA</strong> por <strong>%s</strong>.", $idTramite, $finanzas);
			
			if($finRuta){
				$siguienteEtapa = COMPROBACION_GASTOS_ETAPA_APROBADA;
				$mensajeAutorizadores = sprintf("La Comprobaci&oacute;n de Gastos <strong>%05s</strong> ha sido <strong>AUTORIZADA</strong> por completo.",$idTramite);
				$tramite->setCierreFecha($idTramite);
				$tramite->set_t_comprobado($idTramite, 1);
			}
			
			$redirect = "action=autorizar";
		}
		
		if($modo == "rechazar"){
			$finRuta = true;
			$tramite->limpiarAutorizaciones($idTramite);
			$aprobador = $t_iniciador;
			$siguienteEtapa = COMPROBACION_GASTOS_ETAPA_RECHAZADA;
			$t_ruta_autorizacion = "";
			$mail = 0;
			$redirect = "action=rechazar";
		}
		
		if($modo != "reasignar"){
			// Crear mensaje para el usuario
			$mensajeUsuario = $tramite->crearMensaje($idTramite, $siguienteEtapa, false, true, $delegado);
			//error_log("--->>".$mensajeUsuario."<br />");
		}
		
		//Modificar la Etapa de la Solicitud
		$tramite->Modifica_Etapa($idTramite, $siguienteEtapa, FLUJO_COMPROBACION_GASTOS, $aprobador, $t_ruta_autorizacion, $delegado);
		
		// Enviar Notificacion para Aprobador
		$tramite->EnviaNotificacion($idTramite, $mensajeAutorizadores, $t_dueno, $aprobador, $mail, "");
		
		// Enviar notificacion a Usuario
		if(!$finRuta)
			$tramite->EnviaNotificacion($idTramite, $mensajeUsuario, $t_dueno, $t_iniciador, $mail, "");
		
		// Guardamos todos los cambios en la Base de Datos
		$tramite->ejecutar("COMMIT");
		
		// Regresar a la pantalla de Cmprobaciones de Viaje 
		if($mobile){
    		echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?docs=docs&type=4$redirect'>";
    	}else{
    		// Regresa a la pagina de Comprobaciones de Gastos
    		if($modo == "autorizar"){
	    		echo("<script language='javascript' type='text/javascript'>
	    				if(confirm('¿Desea imprimir la comprobación actual?')){
	    				window.open('generador_pdf_comp_gts.php?id=".$idTramite."', 'imprimir');
	    				location.href='./index.php?docs=docs&type=4&action=autorizar';
	    		}else{
	    				location.href='./index.php?docs=docs&type=4&action=autorizar';
	    		} </script>");
    		}else{
    			exit(header("location: index.php??docs=docs&type=4&$redirect"));
    		}
    	}// Fin de regreso
	}else{ ?>
	<script type="text/javascript" src="../../lib/js/formatNumber.js"></script>
	<script type="text/javascript" src="../../lib/js/jqueryui/jquery-ui.min.js"></script>
	<script type="text/javascript" src="../../lib/js/jquery/jquery.blockUI.js"></script> 
	<script type="text/javascript" src="../../lib/js/jquery/jquery.price_format.1.6.min.js"></script>	
	<script language="JavaScript" src="../../lib/js/jquery/jquery.bgiframe.js" type="text/javascript"></script>
	<script type="text/javascript" src="js/cargaPantallaCG.js"></script>
	<script type="text/javascript" src="js/cargaDatos.js"></script>
	<script type="text/javascript" src="js/backspaceGeneral.js"></script>
	<link rel="stylesheet" type="text/css" href="../../css/table_style.css"/>
	<link rel="stylesheet" type="text/css" href="css/estilos_comprobacion.css"/>
		
	<div id="Layer1" align="center">
		<form name="comprobacion_form" id="comprobacion_form" action="" method="post">
		<table id="comprobacion_table1" border="0" cellspacing="3" width="75%" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">
			<tr>
				<td colspan="5">
					<div align="center" style="color:#003366"><strong>Informaci&oacute;n General</strong></div>
				</td>
			</tr>
			<tr>
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr>
				<td width="6%"><div align="right">No. de Folio:</div></td>
				<td width="18%" align="left"><span id="span_noFolio"></span></td>
				<td width="2%">&nbsp;</td>
				<td width="12%"><div align="right">Fecha de Creaci&oacute;n:</div></td>
				<td width="14%" align="left"><span id="span_fechaRegistro"></span></td>
			</tr>		   
			<tr>
				<td ><div align="right">Solicitante:</div></td>
				<td ><span id="span_nombreEmpleado"></span></td>
				<td width="2%">&nbsp;</td>
				<td align="right">Centro de Costos:</td>
				<td align="left"><span id="span_ceco"></span></td>
			</tr>			
			<tr>
				<td ><div align="right">Motivo:</div></td>
				<td align="left"><span id="span_motivo"></span></td>
				<td >&nbsp;</td>
				<td width="12%"><div align="right">Etapa:</div></td>
				<td width="14%" align="left"><span id="span_etapa"></span></td>
			</tr>
			<tr>
				<td ><div align="right">Autorizador(es):</div></td>
			    <td colspan="5" align="left"><span id="span_autorizadores"></span></td>          
			</tr>
			<tr>
				<td colspan="5">&nbsp;</td>
			</tr>
		</table>
		<br />
		<table id="table_divisas" width="30%" align="center">
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		    <tr id="tr_divisas">
		    	<td align="right"><span>Tasa USD:</span><span id="span_tasaDollar"></span></td>
		    	<td align="right"><span>Tasa EUR:</span><span id="span_tasaEuro"></span></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</table>
		<div align="center"><h1>Gastos Comprobados</h1></div>
		<table id="comprobacion_table" class="tablesorter" cellspacing="1">
			<thead>
				<tr>
					<th>No.</th>
					<th>Tipo</th>
					<th>No. Transacci&oacute;n</th>
					<th>Fecha</th>
					<th>Concepto</th>
					<th>Concepto AMEX</th>
					<th>Comentario</th>
					<th>No. Asistentes</th>
					<th>RFC</th>
					<th>Proveedor</th>
					<th>Factura</th>
					<th>Monto</th> 
					<th>IVA</th>
					<th>Impuesto <br/>Hospedaje</th>			
					<th>Total</th>
					<th>Divisa</th>
					<th>Total MXN</th>
					<th>Excepci&oacute;n</th>		  
					<th>Reasignaci&oacute;n de Concepto</th>
					<th>Recalcular</th>
					<th>Eliminar</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
		<table id="gasto_comp" border="0" align="right">
			<tr>
				<td><span id="span_totalComprobacionText"></span></td>
				<td><span id="span_totalComprobacion"></span><input type="hidden" name="totalComprobacion" id="totalComprobacion" value="0" /></td>
				<td><div>MXN</div></td>
			</tr>
		</table>
		<div id="div_comensales">
			<h1 align="center">Lista de Invitados</h1>
			<table align="center" id="invitado_table" class="tablesorter" cellspacing="1" style="width:65%">
				<thead>
					<tr>
		                <th width="4%">No.</th>
		                <th width="28%">Nombre</th>
		                <th width="28%">Puesto</th>
		                <th width="28%">Empresa</th>
		                <th width="12%">Tipo</th>
					</tr>
				</thead>
	            <tbody></tbody>
			</table>
			<table align="center" id="info_comidasRepresentacion" style="width:65%">
				<tr>
		    		<td width="4%" align="right">&nbsp;</td>
		    		<td width="28%">Ciudad: <span id="span_ciudadComprobacion"></span></td>
		    		<td width="28%">Lugar/Restaurante: <span id="span_lugarComprobacion"></span></td>
		    		<td width="28%">Total de Invitados: <span id="span_totalInvitados"></span></td>
		    		<td width="4%" align="center" valign="middle">&nbsp;</td>
	    		</tr>
	    		<tr>
					<td colspan="5">&nbsp;</td>
				</tr>
	    	</table>
    	</div>
    	<div id="div_excepcion_table">
	    	<h1 align="center">Excepciones</h1>
	    	<table class="tablesorter" style="position: relative; text-align: left; width: 580px;" id="excepcion_table">
				<thead>
					<tr>
						<th>Concepto</th>
						<th>Mensaje</th>
						<th>Excedente</th>
						<th>Tipo de Excepci&oacute;n</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
    	<div id="div_refrescar">
    		<input class="button_refresh" type="button" name="actualizaResumen" id="actualizaResumen" value="       Actualizar Resumen"  onclick="actualizarResumen();"/>
    	</div>
		<table>
			<tr>
    			<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td>	
					<table id="table_historialObservaciones">
						<tr>
							<td width="10px">Historial de Observaciones: </td>
							<td><textarea name="campo_historial" id="campo_historial" cols="90" rows="5" readonly="readonly" ></textarea></td>
						</tr>
					</table>   
					<table id="table_Observaciones">
						<tr>
							<td width="10px">Observaciones: </td>
							<td><textarea name="campo_observaciones" id="campo_observaciones" rows="5" cols="90"></textarea></td>								
						</tr>
					</table>													
				</td>
				<td width="110px">&nbsp;</td>		
				<td>
					<div align="center">
						<table class="tablaDibujada" style="width:300px; text-align:right;" >
							<tr>
								<td colspan="2" style="text-align: center;"><h3>Resumen</h3></td>
							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td width="50%">Anticipo Solicitado:</td>
								<td> 
									<div id="div_anticipo">$ 0.00</div>
									<input type="hidden" name="anticipo" id="anticipo" value="0" />
								</td>
							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td>Anticipo Comprobado: </td>
									<td>
									<div id="div_anticipoComprobado">$ 0.00</div>
									<input type="hidden" name="anticipoComprobado" id="anticipoComprobado" value="0" />
								</td>
							</tr>
								<tr>
								<td>Personal a Descontar: </td>
								<td>
									<div id="div_personalComprobado" >$ 0.00</div>
									<input type="hidden" name="personalComprobado" id="personalComprobado" value="0" />
								</td>
							</tr>
							<tr>
								<td>Amex Comprobado: </td>
								<td>
									<div id="div_amexComprobado" >$ 0.00</div>
									<input type="hidden" name="amexComprobado" id="amexComprobado" value="0" />
								</td>
							</tr>
							<tr>
								<td>Efectivo Comprobado: </td>
								<td>
									<div id="div_efectivoComprobado">$ 0.00</div>
									<input type="hidden" name="efectivoComprobado" id="efectivoComprobado" value="0" />
								</td>
							</tr>                                
							<tr>
								<td>Amex Externo: </td>
								<td>
									<div id="div_amexExterno">$ 0.00</div>
									<input type="hidden" name="amexExterno" id="amexExterno" value="0" />
								</td>
							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr class="montos_rojos">
								<td>Monto a Descontar:</td>
								<td>
									<div id="div_montoDescontar">$ 0.00</div>
									<input type="hidden" name="montoDescontar" id="montoDescontar" value="0" />
								</td>
							</tr>
							<tr class="montos_rojos"> 
								<td>Monto a Reembolsar:</td>
								<td>
									<div id="div_montoReembolsar">$ 0.00</div>
									<input type="hidden" name="montoReembolsar" id="montoReembolsar" value="0" />
								</td>
							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
						</table>
						<table id="table_gasolina" class="tablaDibujada" style="width:300px; text-align:right;">
							<tr>
								<td colspan="2" align="center"><h3>Detalle de Gasolina en M&eacute;xico</h3></td>
							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td>Modelo Auto: </td>
								<td><span id="span_modeloAtuo"></span></td>
							</tr>
							<tr>
								<td>Factor Gasolina: </td>
								<td><span id="span_factorGasolina"></span></td>
							</tr>
							<tr>
								<td>Kilometraje: </td>
								<td><span id="span_kilometraje"></span></td>
							</tr>
							<tr>
								<td>Monto por Factor: </td>
								<td><span id="span_montoGasolina"></span></td>
							</tr>
							<tr style="color:blue">
								<td>Total Gasolina: </td>
								<td><span id="span_totalGasolina"></span></td>
							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td>Ruta detallada: </td>
								<td><textarea name="rutaDetalladaTextArea" id="rutaDetalladaTextArea" readonly="readonly" onkeypress="confirmaRegreso(this.id);" rows="3" ></textarea></td>
							</tr>			
						</table>							
					</div>	
				</td>		
			</tr>
			<tr>
    			<td colspan="3">&nbsp;</td>
			</tr>
		</table>
		<!--<<<<TABLE QUE FUNCIONA COMO FLOTANTE DIVISA >>>>>>>>>>-->
		<div class="div_flotante" id="ventanaDivisa">
			<table class="div_close">
				<tr>
					<td>Captura de tasa</td>
				</tr>
			</table>
			<table width="400" align="center"  class="tablaDibujada" style="width:400px;">
				<tr >
					<td rowspan="5">&nbsp;</td>
					<td align="right">Tasa USD:</td>
					<td rowspan="2">&nbsp;</td>
					<td align="left"><input type="text" name="tasaUSDeditable" id="tasaUSDeditable" value="<?PHP echo $divisaUSD;?>" size="10" maxlength="13" /></td>					
				</tr>
				<tr >
					<td align="right">Tasa EUR:</td>					
					<td align="left"><input type="text" name="tasaEUReditable" id="tasaEUReditable" value="<?PHP echo $divisaEUR;?>" size="10" maxlength="13" /></td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" align="center"><strong>Nota: Para desbloquear la pantalla, de click en 'Aceptar'.</strong></td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
			</table>
			<br />
				<input class="button_ok" type="button" name="aceptarDivisa" id="aceptarDivisa" value="     Aceptar"/>
			<br />
		</div>
		<div class="div_flotante" id="ventanaCeco" >
			<table class="div_close"><tr><td>Reasginaci&oacute;n de CECO</td></tr></table>		
			<div width="400" align="center">
				<p>S&iacute; se es reasignado el CECO, no se permitir&aacute; modificar los montos registrados</p>	
				Centro de costos:<select name='centro_de_costos_new' id="centro_de_costos_new"></select><br/><br/>
				<input type="hidden" id="centro_de_costos_old" name="centro_de_costos_old" />				
				<input class="button_ok" type="button"  value="     Continuar"  name="continuar2" id="continuar2"/>				
			</div>
		</div>
		<table id="botones_table" align="center" valign="middle">
			<tr>
				<td>
					<input class="button_ok" type="button"  value="     Autorizar"  name="autorizar" id="autorizar" />
					<input class="button_volver" type="button" value="     Volver"  name="Volver" id="Volver" />
					<input class="button_ok" type="button" value="     Continuar" id="continuar" name="continuar"/>
					<input class="button_rechazar" type="button" value="     Rechazar" id="rechazar" name="rechazar" />
				</td>
			</tr>
		</table>
		<table>
			<tr>
				<td>
					<!-- Divisa MEX -->
					<input type='hidden' id='valorDivisaMEX' name='valorDivisaMEX' value="1">
					<!-- Divisa Euro -->
					<input type='hidden' id='valorDivisaEUR' name='valorDivisaEUR' value="0">
					<!-- Divisa Dollar -->
					<input type='hidden' id='valorDivisaUSD' name='valorDivisaUSD' value="0">
					
					<input type="hidden" name="total_rows" id="total_rows" value="" readonly="readonly"/>
					<input type="hidden" name="idT" id="idT" value="" />
					<input type="hidden" name="iu" id="iu" value="<?PHP echo $_SESSION["idusuario"]; ?>" />
					<input type="hidden" name="perfil" id="perfil" value="<?PHP echo $tipoUsuario; ?>" />
					<input type="hidden" name="delegado" id="delegado" value="<?PHP echo $_SESSION['idrepresentante'];?>" />
					<input type="hidden" name="privilegios" id="privilegios" value="<?PHP echo $_SESSION['privilegios'];?>" />
					<input type="hidden" name="t_etapa_actual" id="t_etapa_actual" value=""/>
				</td>
			</tr>
		</table>
		</form>
	</div>
<?php } ?>