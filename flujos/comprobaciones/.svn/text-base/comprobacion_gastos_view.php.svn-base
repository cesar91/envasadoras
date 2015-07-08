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
// 		$rows = $_POST['total_rows'];
		$observaciones = $_POST['campo_observaciones'];
		$historialObservaciones = $_POST["campo_historial"];
// 		$cecoNuevo = $_POST['centro_de_costos_new'];		
// 		$cecoOriginal = $_POST['centro_de_costos_old'];		
// 		$divisaEuro = $_POST["valorDivisaEUR"];
// 		$divisaDolar = $_POST["valorDivisaUSD"];
		
		$tramite = new Tramite();
		$rutaAutorizacion = new RutaAutorizacion();
		$notificacion = new Notificacion();
		$comprobaciones = new ComprobacionesGastos();
		$detalleComprobacion = new DetalleComprobacionGastos();
		$concepto = new Concepto();
		$usuario = new Usuario();
		
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

		if($modo == "autorizar"){
			$aprobador = $rutaAutorizacion->getSiguienteAprobador($idTramite,$t_dueno);
			//echo $aprobador;
			if($aprobador == ""){
				$finRuta = true;
				$aprobador = $t_iniciador;
			}
			if($finRuta){
				$siguienteEtapa = COMPROBACION_GASTOS_ETAPA_APROBADA;
				$mensajeAutorizadores = sprintf("La Comprobaci&oacute;n de Gastos <strong>%05s</strong> ha sido <strong>AUTORIZADA</strong> por completo.",$idTramite);
				$tramite->setCierreFecha($idTramite);
				$tramite->set_t_comprobado($idTramite, 1);
			}			
			if($aprobador == "1000" || $aprobador == "2000")
				$mail = 0;
			$redirect = "action=autorizar";
		}
		
		if($modo == "rechazar"){
			$tramite->limpiarAutorizaciones($idTramite);
			$aprobador = $t_iniciador;
			$siguienteEtapa = COMPROBACION_GASTOS_ETAPA_RECHAZADA;
			$t_ruta_autorizacion = "";
			$mail = 0;
			$redirect = "action=rechazar";
		}
		$duenoActual01 = new Usuario();
			if($duenoActual01->Load_Usuario_By_ID($t_dueno))
				$dueno_act_nombre = $duenoActual01->Get_dato('nombre');
			else{
				$agrup_usu = new AgrupacionUsuarios();
				$agrup_usu->Load_Grupo_de_Usuario_By_ID($t_dueno);
				$dueno_act_nombre = $agrup_usu->Get_dato("au_nombre");
			}
			$remitente = $t_dueno;
			$destinatario = $tramite->Get_dato("t_iniciador");
		// Crear mensaje para el usuario
			if(!$finRuta){
				$mensajeAutorizadores = $tramite->crearMensaje($idTramite, $siguienteEtapa, false, true, $delegado);	
				$mensaje2 = sprintf("La Comprobaci&oacute;n de Gastos <strong>%05s</strong> ha sido <strong>AUTORIZADA</strong> por <strong>%05s</strong> y requiere de su autorizaci&oacute;n", $idTramite, $dueno_act_nombre);
				$tramite->EnviaNotificacion($idTramite, $mensaje2, $remitente, $aprobador, 1, "");
				$t_dueno = $t_dueno;			
			}		
		//Modificar la Etapa de la Solicitud
		$tramite->Modifica_Etapa($idTramite, $siguienteEtapa, FLUJO_COMPROBACION_GASTOS, $aprobador, $t_ruta_autorizacion, $delegado);
			if(!$finRuta){
				$aprobador = $t_dueno;				
			}	
		// Enviar Notificacion para Aprobador
		$tramite->EnviaNotificacion($idTramite, $mensajeAutorizadores, $t_dueno, $aprobador, $mail, "");
		
		// Regresar a la pantalla de Cmprobaciones de Viaje 
		if($mobile){
    		echo "<meta http-equiv='Refresh' content='0; URL=http://".$SERVER.$RUTA_R."flujos/comprobaciones/index.php?docs=docs&type=4$redirect'>";
    	}else{
    		exit(header("location: index.php??docs=docs&type=4&$redirect"));
    	}// Fin de regreso
	}else{ ?>
	<script type="text/javascript" src="../../lib/js/formatNumber.js"></script>
	<script type="text/javascript" src="../../lib/js/jqueryui/jquery-ui.min.js"></script>
	<script type="text/javascript" src="../../lib/js/jquery/jquery.blockUI.js"></script> 
	<script type="text/javascript" src="../../lib/js/jquery/jquery.price_format.1.6.min.js"></script>	
	<script language="JavaScript" src="../../lib/js/jquery/jquery.bgiframe.js" type="text/javascript"></script>
	<script type="text/javascript" src="js/cargaDatos.js"></script>
	<script type="text/javascript" src="js/backspaceGeneral.js"></script>
	<link rel="stylesheet" type="text/css" href="../../css/table_style.css"/>
	<link rel="stylesheet" type="text/css" href="css/estilos_comprobacion.css"/>
	
	<script type="text/javascript">
	/**
	* Inicializa los Eventos una vez que se ha ter minado de Cargar el DOM (Document Object Model)
	*/
	var doc = $(document);
	doc.ready(init);
	
	function init(){
		blockUI(true);
		/**
		* Funcion Global, permite obtener el valor de un parametro enviado por URL 
		*
		* @param name string 	=> Nombre de la variable a obtener
		* @return string 		=> El valor del parametro si existe este, de lo contrario devuelve 0
		*/
		
		$._GET = function(name){
			var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(top.window.location.href);
			return (results !== null) ? results[1] : 0;
		}

		/**
		* Funcion Global, permite centrar un div en pantalla
		*/
		jQuery.fn.center = function(){
			this.css("position","absolute");
			this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) + $(window).scrollTop()) + "px");
			this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + $(window).scrollLeft()) + "px");
			return this;
		}
		
		/**
		* Configuracion Default del Objeto AJAX de JQuery
		*
		* @attr url string				=> Indicamos la ruta al servidor que se encarga de atender las peticiones
		* @attr timeout int				=> Indicamos el tiempo de espera de respuesta del servidor
		* @attr async string			=> Indicamos el modo de peticion sera sincrono o asincrono
		* @attr cache boolean			=> Indicamos si guardaremos en la cache las respuestas del servidor
		* @attr type string				=> Indicamos por que metodo se enviara la informacion al servidor
		* @attr dataType string			=> Indicamos el tipo de codificacion de los datos que esperamos como respuesta
		* @attr contentType string 		=> Indicamos el tipo de codificacion de los datos enviados
		*/
		var urlServices = "services/servicesCompGts.php";
		
		$.ajaxSetup({
			'url':			urlServices,
			'timeout':		5000,
			'async':		false,
			'cache':		false,
			'type':			'post',
			'dataType':		'json',			
			'contentType':	'application/x-www-form-urlencoded; charset=utf-8'		
		});
		
		if($._GET("id") != 0){
			var usuario = $("#iu").val();
			var tramite = $._GET("id");
			var json = obtenInformacionGeneral(tramite);
			obtenInformacionGasolina(tramite);
			
			asignaVal("t_etapa_actual", json["t_etapa_actual"]);
			asignaVal("centro_de_costos_new", json["co_cc_clave"]);
			asignaVal("centro_de_costos_old", json["co_cc_clave"]);
			
			asignaVal("idT", tramite);			
			
			var anticipoCeco = obtenAnticipoCeco(0, tramite);	
			var anticipo = anticipoCeco["sv_total_anticipo"];
			anticipo = (!anticipoCeco ) ? "0.00" : anticipo;
			asignaVal("anticipo", anticipo);	
			asignaText("div_anticipo", anticipo, "number");
			
			var banderaComidasRepresentacion = false;
			ocultarElemento("div_comensales");
			var objetoPartidas = obtenDatosPartida(tramite);
			for(var prop in objetoPartidas){
				var partida = objetoPartidas[prop];
				
				if(partida.concepto == 1){
					banderaComidasRepresentacion = true;
				}
				
				for(var propPar in partida)
					partida[propPar] = (partida[propPar] == "" || partida[propPar] == null) ? "N/A" :  partida[propPar];
				
				var id = obtenTablaLength("comprobacion_table")+1;
				var	renglon = creaRenglonFI(objetoPartidas[prop], id);
				agregaRenglon(renglon, "comprobacion_table");
				obtenConceptosPartida(id);
			}
			
			asignaVal("total_rows",obtenTablaLength("comprobacion_table"));
								var ant = $( "#anticipo").val();
								var rowCount = $('#comprobacion_table tr').length;
								var rowTotal = 0;
								var rowIVA = 0;
								var rowRetIVA = 0;
								var rowRetISR = 0;
								var rowDesc = 0;
								var rowIeps = 0;

								var rowTotalRbl = 0;
								var rowIVARbl = 0;
								var rowRetIVARbl = 0;
								var rowRetISRRbl = 0;
								var rowDescRbl = 0;
								var rowIepsRbl = 0;
								
								var rowTotalAnt = 0;
								var rowIVAAnt = 0;
								var rowRetIVAAnt = 0;
								var rowRetISRAnt = 0;
								var rowDescAnt = 0;
								var rowIepsAnt = 0;
								
								var rowTotalAMEX = 0;
								var rowIVAAMEX = 0;
								var rowRetIVAAMEX = 0;
								var rowRetISRAMEX = 0;
								var rowDescAMEX = 0;
								var rowIepsAMEX = 0;
								
								var rowConceptoAnt = '';
									for (i = 1; i < rowCount; i++) {
										rowTipoComp = $( "#div_row_tipoComprobacion"+i).html();
										rowTipoComp = $.trim(rowTipoComp);
										rowConcepto = $( "#div_row_conceptoTexto"+i).html();
											switch(rowConcepto) {
												case "Impuesto de IVA":
													rowIVA += parseFloat($( "#div_row_total"+i).val().replace(/,/g, ""));
													rowIVARbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
													rowIVAAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
													rowIVAAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
													break;
												case "Retenciones IVA":
													rowRetIVA += parseFloat($( "#div_row_total"+i).val().replace(/,/g, ""));
													rowRetIVARbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
													rowRetIVAAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
													rowRetIVAAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
													break;
												case "Retenciones ISR":
													rowRetISR += parseFloat($( "#div_row_total"+i).val().replace(/,/g, ""));
													rowRetISRRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
													rowRetISRAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
													rowRetISRAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
													break;													
												case "Descuento":
													rowDesc += parseFloat($( "#div_row_total"+i).val().replace(/,/g, ""));
													rowDescRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
													rowDescAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
													rowDescAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
													break;
												case "IEPS":
													rowIeps += parseFloat($( "#div_row_total"+i).val().replace(/,/g, ""));
													rowIepsRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
													rowIepsAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
													rowIepsAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
													break;							
												default:
													rowTotal += parseFloat($( "#div_row_total"+i).val().replace(/,/g, ""));
													rowTotalRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
													rowTotalAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
													rowTotalAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat($( "#div_row_total"+i).val().replace(/,/g, "")) : 0;
													}
									}
									$( "#div_res_subtotal").html("$"+rowTotal.toFixed(2));
									$( "#div_res_desc").html("$"+rowDesc.toFixed(2));
									$( "#div_res_iva").html("$"+rowIVA.toFixed(2));
									$( "#div_res_ret_iva").html("$"+rowRetIVA.toFixed(2));
									$( "#div_res_ret_isr").html("$"+rowRetISR.toFixed(2));
									$( "#div_res_ieps").html("$"+rowIeps.toFixed(2));
									
									$( "#div_res_subtotal_ant").html("$"+rowTotalAnt.toFixed(2));
									$( "#div_res_desc_ant").html("$"+rowDescAnt.toFixed(2));
									$( "#div_res_iva_ant").html("$"+rowIVAAnt.toFixed(2));
									$( "#div_res_ret_iva_ant").html("$"+rowRetIVAAnt.toFixed(2));
									$( "#div_res_ret_isr_ant").html("$"+rowRetISRAnt.toFixed(2));
									$( "#div_res_ieps_ant").html("$"+rowIepsAnt.toFixed(2));
									
									$( "#div_res_subtotal_amex").html("$"+rowTotalAMEX.toFixed(2));
									$( "#div_res_desc_amex").html("$"+rowDescAMEX.toFixed(2));
									$( "#div_res_iva_amex").html("$"+rowIVAAMEX.toFixed(2));
									$( "#div_res_ret_iva_amex").html("$"+rowRetIVAAMEX.toFixed(2));
									$( "#div_res_ret_isr_amex").html("$"+rowRetISRAMEX.toFixed(2));
									$( "#div_res_ieps_amex").html("$"+rowIepsAMEX.toFixed(2));									
										var TotalReembolso = rowTotalRbl - rowDescRbl + rowIVARbl - rowRetIVARbl - rowRetISRRbl - rowIepsRbl;
										$( "#div_total_reembolso").html("$"+TotalReembolso.toFixed(2));
										var TotalAnticipo = rowTotalAnt - rowDescAnt + rowIVAAnt - rowRetIVAAnt - rowRetISRAnt - rowIepsAnt;
										$( "#div_comp_anticipo").html("$"+TotalAnticipo.toFixed(2));
										var TotalAMEX = rowTotalAMEX - rowDescAMEX + rowIVAAMEX - rowRetIVAAMEX - rowRetISRAMEX - rowIepsAMEX;
										$( "#div_comp_amex").html("$"+TotalAMEX.toFixed(2));
										var TotalPendiente = ant - TotalAnticipo;
										$( "#div_pendiente").html("$"+TotalPendiente.toFixed(2));
										var div_totalSuma = TotalReembolso + TotalAnticipo +TotalAMEX;
										$( "#div_totalSuma").html("$"+div_totalSuma.toFixed(2));										
			if(banderaComidasRepresentacion){
				mostrarElemento("div_comensales");
				obtenInvitados(tramite);
				obtenInformacionComidasRepresentacion(tramite);
				remuevecolumnaEliminar();
			}
		}
		
		ocultarElemento("table_divisas");
		
		var etapa = $("#t_etapa_actual").val();
		var perfil = $("#perfil").val();
		var registrosOriginales = obtenTablaLength("comprobacion_table");
		var delegado = $("#delegado").val();
		var privilegios = $("#privilegios").val();
		var tramite = $._GET("id");
		
		deshabilitaElemento();
		habilitaElemento("Volver");
		habilitaElemento("imprimir");
		habilitaElemento("campo_historial");
		
		validaRefrescarInit(perfil);
		validaHistorialInit(perfil);
		validaColumnasInit(perfil, etapa);
		validaLecturaColumnasInit(perfil);
		validaCecoInit(etapa, perfil);
		validaBotonesInit(etapa, perfil, delegado, privilegios);

		calcularResumen();
		calcularTotalComprobacion();

		if($._GET("edit_view") == "edit_view"){
			habilitaElemento("autorizar");
			habilitaElemento("rechazar");
			habilitaElemento("campo_observaciones");
		}else{
			ocultarElemento("autorizar", "hide");
			ocultarElemento("table_Observaciones");
			ocultarElemento("rechazar", "hide");
			ocultarElemento("table_gasolina");
		}
		
		obtenExcepciones($._GET("id"));
		obtenExcepcionesPresupuesto($._GET("id"));
			
		$("#actualizaResumen").click(function(){
			calcularResumen();
			calcularTotalComprobacion();
			asignaVal("total_rows", obtenTablaLength("comprobacion_table"));
		});
		
		$("#autorizar").click(function(){
			ocultarElemento("botones_table");
			habilitaElemento();
			$("#comprobacion_form").attr("action", "comprobacion_gastos_view.php?modo=autorizar");
			$("#comprobacion_form").submit();
		});

		$("#Volver").click(function(){
			ocultarElemento("botones_table");
			var url = ($("#hist").val() == 'hist') ? '../solicitudes/historial.php' : "index.php?docs=docs&type=4";
			location.href = url;
		});
		
		/**
		 * Evento, click en el <button enviarComprobacionGasolina>
		 *
		 * 1 Confirma ejecuccion de la ccion 
		 * 2 Valida requeridos de la pantalla de gasolina
		 */
		$("#imprimir").click(function(){
					var subtotal = $("#div_res_subtotal").html();
					var descuento = $("#div_res_desc").html();
					var iva = $("#div_res_iva").html();
					var retisr = $("#div_res_ret_isr").html();
					var retiva = $("#div_res_ret_iva").html();
					var ieps = $("#div_res_ieps").html();
					var anticipo = $("#div_comp_anticipo").html();
					var amex = $("#div_comp_amex").html();
					var reembolso = $("#div_total_reembolso").html();
					var pendiente = $("#div_pendiente").html();		
			window.open("generador_pdf_comp_gts.php?id="+$._GET("id")+"&subtotal="+subtotal+"&descuento="+descuento+"&iva="+iva+"&retisr="+retisr+"&retiva="+retiva+"&ieps="+ieps+"&anticipo="+anticipo+"&amex="+amex+"&reembolso="+reembolso+"&pendiente="+pendiente,"imprimir");
		});

		$("#rechazar").click(function(){
			ocultarElemento("botones_table");
			habilitaElemento();
			$("#comprobacion_form").attr("action", "comprobacion_gastos_view.php?modo=rechazar");
			$("#comprobacion_form").submit();
		});

		blockUI(false);
	}
	</script>
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
					<th>Total</th>
					<th>Divisa</th>
					<th>Excepci&oacute;n</th>		  
					<th>Reasignaci&oacute;n de Concepto</th>
					<th>Recalcular</th>
					<th>Eliminar</th>
					<th>---</th>
					<th>XML</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
		<table style="display:none" id="gasto_comp" border="0" align="right">
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
                                    <td>Subtotal: </td>
                                    <td>
										<div id="div_res_subtotal">$ 0.00</div>
										<div style="display:none;" id="div_res_subtotal_ant">$ 0.00</div>
										<div style="display:none;" id="div_res_subtotal_amex">$ 0.00</div>
									</td>
                                </tr>
                                <tr>
                                    <td>Descuento: </td>
                                    <td>
										<div id="div_res_desc">$ 0.00</div>
										<div style="display:none;" id="div_res_desc_ant">$ 0.00</div>
										<div style="display:none;" id="div_res_desc_amex">$ 0.00</div>
									</td>
                                </tr>
                                <tr>
                                    <td>IVA: </td>
                                    <td>
										<div id="div_res_iva">$ 0.00</div>
										<div style="display:none;" id="div_res_iva_ant">$ 0.00</div>
										<div style="display:none;" id="div_res_iva_amex">$ 0.00</div>
									</td>
                                </tr>
								<tr>
                                    <td>Retencion de ISR: </td>
                                    <td>
										<div id="div_res_ret_isr">$ 0.00</div>
										<div style="display:none;" id="div_res_ret_isr_ant">$ 0.00</div>
										<div style="display:none;" id="div_res_ret_isr_amex">$ 0.00</div>
									</td>
                                </tr>
                                <tr>
                                    <td>Retencion de IVA: </td>
                                    <td>
										<div id="div_res_ret_iva">$ 0.00</div>
										<div style="display:none;" id="div_res_ret_iva_ant">$ 0.00</div>
										<div style="display:none;" id="div_res_ret_iva_amex">$ 0.00</div>
									</td>
                                </tr>
								 <tr>
                                    <td>IEPS: </td>
                                    <td>
										<div id="div_res_ieps">$ 0.00</div>
										<div style="display:none;" id="div_res_ieps_ant">$ 0.00</div>
										<div style="display:none;" id="div_res_ieps_amex">$ 0.00</div>
									</td>
                                </tr>
                                <tr class="montos_rojos">
                                    <td>Total comprobacion Anticipo:</td>
                                    <td>
										<div id="div_comp_anticipo">$ 0.00</div>
										<input type="hidden" name="anticipo" id="anticipo" value="0" />
									</td>
                                </tr>
								 <tr class="montos_rojos">
                                    <td>Total comprobacion AMEX:</td>
                                    <td>
										<div id="div_comp_amex">$ 0.00</div>
										<input type="hidden" name="montoDescontar" id="montoDescontar" value="" />
									</td>
                                </tr>
								 <tr class="montos_rojos">
                                    <td>Total de Reembolso:</td>
                                    <td>
										<div id="div_total_reembolso">$ 0.00</div>
										<input type="hidden" name="montoDescontar" id="montoDescontar" value="" />
									</td>
                                </tr>
								 <tr class="montos_rojos">
                                    <td>Pendiente de comprobar:</td>
                                    <td>
										<div id="div_pendiente">$ 0.00</div>
										<input type="hidden" name="montoDescontar" id="montoDescontar" value="" />
									</td>
                                </tr>
								 <tr class="montos_rojos">
                                    <td>Total:</td>
                                    <td>
										<div id="div_totalSuma">$ 0.00</div>
										<input type="hidden" name="montoSuma" id="montoSuma" value="" />
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
		<table id="botones_table" align="center" valign="middle">
			<tr>
				<td>
					<input class="button_ok" type="button"  value="     Autorizar"  name="autorizar" id="autorizar" />
					<input class="button_volver" type="button" value="     Volver"  name="Volver" id="Volver" />
					<input class="button_rechazar" type="button" value="     Rechazar" id="rechazar" name="rechazar" />
					<input type="button" value="     Imprimir" id="imprimir"    name="imprimir"    class="button_imprimir" />
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
					<input type="hidden" name="hist" id="hist" readonly="readonly" value="<? echo $_GET['hist']; ?>" />
				</td>
			</tr>
		</table>
		</form>
	</div>
<?php } ?>