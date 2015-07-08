<?PHP
	if(isset($_REQUEST["modo"])){
		require_once("../../lib/php/constantes.php");
		require_once("../../functions/utils.php");
		require_once("../../Connections/fwk_db.php");		

		$cnn = new conexion();
		$modo = $_REQUEST["modo"];
			
		$idTramite = $_POST['idT'];
		$iduser = $_POST['iu'];
		$observaciones = $_POST['observ'];
		
		$tramite = new Tramite();
		$tramite->Load_Tramite($idTramite);
		$t_dueno = $tramite->Get_dato('t_dueno');
		$t_delegado = $tramite->Get_dato('t_delegado');
		$iniciador = $tramite->Get_dato('t_iniciador');
		
		if($observaciones != ""){
			$query = sprintf("SELECT co_observaciones FROM comprobaciones WHERE co_mi_tramite = '%s'",$idTramite);
			$rst = $cnn->consultar($query);
			$fila = mysql_fetch_assoc($rst);
			$HObser = $fila['co_observaciones'];			
			$notificacion = new Notificacion();
			$observaciones = $notificacion->anotaObservacion($t_dueno, $HObser, $observaciones, FLUJO_COMPROBACION, COMPROBACION_ETAPA_EN_APROBACION);				
			$queryInsertaObs=sprintf("UPDATE comprobaciones SET co_observaciones = '%s' WHERE co_mi_tramite = '%s'", $observaciones, $idTramite);
			$cnn->ejecutar($queryInsertaObs);
		}		
   
		if($modo == 'aprobar'){
			$rutaautorizacion = new RutaAutorizacion();
			/**
			 * Validacion y guardado de excepcion de presupuesto
			 **/
			$presupuesto = new Presupuesto();
			$objetoPresupuesto = $presupuesto->validarPresupuesto($idTramite);
			$rutaautorizacion->generaExcepcion($idTramite, $objetoPresupuesto);
			$rutaautorizacion->generarRutaAutorizacion($idTramite, $t_delegado);
			$excepciones = $rutaautorizacion->get_Excepciones($idTramite);
			$rutaautorizacion->agregaAutorizadoresExcedentes($idTramite, $excepciones);
			$destinatario = $rutaautorizacion->getAprobador($idTramite, $iduser);
			$etapa = COMPROBACION_ETAPA_EN_APROBACION;			
			$redirect = "okAut";			
		}elseif($modo == 'rechazar'){
			$etapa = COMPROBACION_ETAPA_RECHAZADA_POR_DIRECTOR;
			$destinatario = $iniciador;							
			$redirect = "action=rechazar";			
		}
		
		//$tramite->Modifica_Dueno($idTramite, $etapa, FLUJO_COMPROBACION, $t_dueno, $iniciador);			
		$tramite->Modifica_Etapa($idTramite, $etapa, FLUJO_COMPROBACION, $destinatario, "" , $t_delegado);
		$mensaje = $tramite->crearMensaje($idTramite, $etapa);
		$tramite->EnviaNotificacion($idTramite, $mensaje, $iduser, $destinatario, 1);					
		exit(header("Location: ./index.php?$redirect"));	
			
	}else{	
	?>
		<script type="text/javascript" src="../../lib/js/jquery/jquery.blockUI.js"></script> 
		<script type="text/javascript" src="../../lib/js/formatNumber.js"></script>
		<script type="text/javascript" src="../../lib/js/jqueryui/jquery-ui.min.js"></script>	
		<script type="text/javascript" src="js/backspaceGeneral.js"></script>	
		<script type="text/javascript" src="js/cargaDatos.js"></script>
		<link rel="stylesheet" type="text/css" href="../../css/table_style.css"/>	
		<style>
			span{			
				font-weight: bold;
			}
			.tablaDibujada{			
				border: 1px #CCCCCC solid;
				margin: auto;
				margin-top: 5px;
				text-align:left;
				width: 785px;
			}
			.button_ok{
				background:url(../../images/ok.png);
				background-position:left; 
				background-repeat:no-repeat; 
				background-color:#E1E4EC;
			}
			.button_rechazar{
				background:url(../../images/reject.png);
				background-position:left; 
				background-repeat:no-repeat; 
				background-color:#E1E4EC;
			}		
			.button_volver{
				background:url(../../images/back.png);
				background-position:left; 
				background-repeat:no-repeat; 
				background-color:#E1E4EC;
			}
			.button_imprimir{
				background:url(../../images/icon_Imprimir.gif);
				background-position:left; 
				background-repeat:no-repeat; 
				background-color:#E1E4EC;
			}		
			.montos_rojos{
				color: #DF0101;
			}
		</style>	
		<script language="javascript" type="text/javascript">	  
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
				 * Configuración Default del Objeto AJAX de JQuery
				 *
				 * @attr url string			=> Indicamos la ruta al servidor que se encarga de atender las peticiones
				 * @attr timeout int		=> Indicamos el tiempo de espera de respuesta del servidor
				 * @attr async string		=> Indicamos el modo de peticion será sincrono o asincrono
				 * @attr cache boolean		=> Indicamos si guardaremos en la cache las respuestas del servidor
				 * @attr type string		=> Indicamos por que metodo se enviará la información al servidor
				 * @attr dataType string	=> Indicamos el tipo de codificación de los datos que esperamos como respuesta
				 * @attr contentType string => Indicamos el tipo de codificación de los datos enviados
				 */		 
				var urlServices = "services/services.php";	
				$.ajaxSetup({
					'url':			urlServices,
					'timeout':		5000,
					'async':		true,
					'cache':		false,
					'type':			'post',
					'dataType':		'json',			
					'contentType':	'application/x-www-form-urlencoded; charset=utf-8'		
				});
				
				obtenExcepciones($._GET("id"));
				obtenExcepcionesPresupuesto($._GET("id"));
				
				/**
				 * Evento, click en el <button guardarPrevio>
				 *
				 * 1 Pedimos confirmacion de la accion
				 * 2 Ocultamos los botones de enviarComprobacion y guardarPrevio
				 * 3 Realizamos un submit del formulario
				 */
				$("#aprobar_cv").click(function(){			
					if(confirm("Desea Aprobar la comprobacion?")){
						ocultarElemento("table_botones");
						$("#form_comprobacion").attr("action", "comprobacion_travel_viewN.php?modo=aprobar");				
						$("#form_comprobacion").submit();				
						
					}			
				});	
				
				/**
				 * Evento, click en el <button enviarComprobacion>
				 *
				 * 1 Pedimos confirmacion de la accion
				 * 2 Validamos que los cargos amex sean completamente comprobados
				 * 3 validamos si existe algun concepto de gasolina
				 * 3 a) si existe concepto de gasolina
				 * 3 a)	i)  Cargamos tipos de auto
				 */
				$("#rechazar_cv").click(function(){	
					if(confirm("Desea Rechazar la Comprobacion?")){
						ocultarElemento("table_botones");
						$("#form_comprobacion").attr("action", "comprobacion_travel_viewN.php?modo=rechazar");
						$("#form_comprobacion").submit();
					}
				});		

				/**
				 * Evento, click en el <button enviarComprobacionGasolina>
				 *
				 * 1 Confirma ejecuccion de la ccion 
				 * 2 Valida requeridos de la pantalla de gasolina
				 */
				$("#Volver").click(function(){
					if(confirm("Desea Volver?")){
						var url = ($("#hist").val() == 'hist') ? '../solicitudes/historial.php' : 'index.php';
						location.href = url;
					}
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
					window.open("generador_pdf.php?id="+$._GET("id")+"&subtotal="+subtotal+"&descuento="+descuento+"&iva="+iva+"&retisr="+retisr+"&retiva="+retiva+"&ieps="+ieps+"&anticipo="+anticipo+"&amex="+amex+"&reembolso="+reembolso+"&pendiente="+pendiente,"imprimir");
				});	
				
				/**
				 * Verifica si es una edicion
				 *	
				 * 1 Selecciona la solicitud lanzando un trigger del evento change del select de solicitud
				 * 2 Obtiene un objeto que tiene como atributos objetos del mismo tipo del objeto de un formulario
				 * 3 Para cada objeto del tipo formulario sustituimos los valores "" y NULL por "N/A"
				 * 4 Para cada objeto del tipo formulario agregamos un renglon a la tabla de partidas
				 * 5 Recalculamos el resumen de la comporbacion
				 */
				if($._GET("id") != 0){				
					var json = obtenInformacionGeneral($._GET("id"));					
					activaBotonImprimir(json["t_dueno"], json["t_etapa"]);
					activaBotones(json["t_etapa"]);					
					
					asignaVal("t_etapa_actual", json["t_etapa_actual"]);
					asignaVal("idT", $._GET("id"));
					var etapa = json["t_etapa_actual"];
					var anticipoCeco = obtenAnticipoSol($._GET("id"),0);
					var anticipo = anticipoCeco["sv_total_anticipo"];
					anticipo = (!anticipoCeco ) ? "0.00" : anticipo;
					asignaVal("anticipo", anticipo);	
					asignaText("div_anticipo", anticipo, "number");
						
					var objetoPartidas = obtenDatosPartida($._GET("id"));
					for(var prop in objetoPartidas){
						var partida = objetoPartidas[prop];
						for(var propPar in partida)
							partida[propPar] = (partida[propPar] == "" || partida[propPar] == null) ? "N/A" :  partida[propPar];
						
						var id = obtenTablaLength("comprobacion_table")+1;
						var	renglon = creaRenglon(objetoPartidas[prop], id)
						agregaRenglon(renglon, "comprobacion_table");
					}		
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
										rowConcepto = $( "#div_row_conceptoTexto"+i).html();
											switch(rowConcepto) {
												case "Impuesto de IVA":
													rowIVA += parseFloat($( "#div_row_total"+i).html().replace(/,/g, ""));
													rowIVARbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat($( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowIVAAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat($( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowIVAAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat($( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													break;
												case "Retenciones IVA":
													rowRetIVA += parseFloat($( "#div_row_total"+i).html().replace(/,/g, ""));
													rowRetIVARbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat($( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowRetIVAAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat($( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowRetIVAAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat($( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													break;
												case "Retenciones ISR":
													rowRetISR += parseFloat($( "#div_row_total"+i).html().replace(/,/g, ""));
													rowRetISRRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat($( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowRetISRAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat($( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowRetISRAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat($( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													break;													
												case "Descuento":
													rowDesc += parseFloat($( "#div_row_total"+i).html().replace(/,/g, ""));
													rowDescRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat($( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowDescAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat($( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowDescAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat($( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													break;
												case "IEPS":
													rowIeps += parseFloat($( "#div_row_total"+i).html().replace(/,/g, ""));
													rowIepsRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat($( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowIepsAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat($( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowIepsAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat($( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													break;							
												default:
													rowTotal += parseFloat($( "#div_row_total"+i).html().replace(/,/g, ""));
													rowTotalRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat($( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowTotalAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat($( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowTotalAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat($( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
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
					for(var i = 1; i <= obtenTablaLength("comprobacion_table"); i++){
						$("#comprobacion_table>thead>tr>th").eq(13).hide();
						$("#tr_"+i+" td").eq(13).hide();
						$("#comprobacion_table>thead>tr>th").eq(14).hide();
						$("#tr_"+i+" td").eq(14).hide();						
					}
					
					if(etapa != 9){//COMPROBACION_ETAPA_EN_APROBACION_POR_DIRECTOR
						ocultarElemento("aprobar_cv","hide");
						ocultarElemento("rechazar_cv","hide");
					}
					
					if($("#hist").val() != '')
						ocultarElemento("tr_Observaciones", "hide");
					
					calcularResumen();
				}						
				
				blockUI(false);
			}
		</script>
		
		<div align="center">
			<form name="form_comprobacion" id="form_comprobacion" method="post">
			<table id="comprobacion_informacion_table" cellspacing="3" width="75%" style="border:1px #CCCCCC solid;margin:auto;margin-top:5px;text-align:left; background-color:#F8F8F8">
				<tr>
					<td colspan="5">
						<div align="center" style="color:#003366"><strong>Informaci&oacute;n general</strong></div>
					</td>
				</tr>
				<tr><td colspan="5"></td></tr>
				<tr>
					<td width="6%" align="right">No. de Folio:</td>
					<td width="18%" align="left"><span id="span_noFolio"></td>
					<td width="2%" rowspan="4"></td>
					<td width="12%" align="right">Fecha de creaci&oacute;n:</td>
					<td width="14%"><span id="span_fechaRegistro"></span></td>
				</tr>
				<tr>
					<td align="right">Solicitante:</td>
					<td><span id="span_nombreEmpleado"></td>
					<td align="right">Centro de costos:</td>
					<td><span id="span_ceco"></span></td>
				</tr>						
				<tr>
					<td align="right">Motivo:</td>
					<td><span id="span_motivo"></span></td>				
					<td align="right">Destino:</td>
					<td><span id="span_destino"></span></td>				
				</tr>
				<tr>
					<td align="right">Fecha de Viaje:</td>
					<td><span id="span_fechaViaje"></span></td>				
					<td align="right">Etapa:</td>
					<td><span id="span_etapa"></span></td>
				</tr>       
				<tr>
					<td align="right">Autorizador(es):</td>
					<td colspan="5"><span id="span_autorizadores"></span></td>
				</tr>
				<tr><td colspan="5"></td></tr>							   
			</table>	
			
			<br/>
			<br/>
			<br/>
			
			<div align="center" style="color:#003366"><strong>Gastos comprobados</strong></div>
			
			<table id="comprobacion_table" class="tablesorter" cellspacing="1">
				<thead>
				<tr>
					<th>No.</th>
					<th>Tipo</th>
					<th>No. Transacci&oacute;n</th>
					<th>Fecha</th>
					<th>Concepto</th>
					<th>Comentario</th>
					<th>No. Asistentes</th>
					<th>RFC</th>
					<th>Proveedor</th>
					<th>Folio</th>
					<th>Monto</th> 
					<th>Divisa</th>
					<th>Total</th>
					<th>Editar</th>
					<th>Eliminar</th>
					<th>XML</th>
				</tr>
				</thead>
				<tbody></tbody>
			</table>

			<table style="display:none" id="gasto_comp" border="0" align="right">
				<tr>
					<td>Total:</td>
					<td><span id="span_total"></div></td>
					<td>MXN</td>
				</tr>
			</table>
			<br />
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
			<br />
			<table>
				<tr>
					<td>&nbsp;</td>
					<td>
						<div id="observaciones">
							<table>
								<tr id="tr_historialObservaciones">
									<td width="10px">Historial de observaciones: </td>
									<td><textarea name="campo_historial" id="campo_historial" rows="5" cols="90" readonly="readonly"></textarea></td>						
								</tr>							
								<tr id="tr_Observaciones">
									<td width="10px">Observaciones: </td>
									<td><textarea name="observ" id="observ" rows="5" cols="90" ></textarea></td>						
								</tr>							
							</table>
						</div>
					</td>
					<td width="110px" ></td>		
					<td>
						<table class="tablaDibujada" style="width:300px; text-align:right;" >
							<tr>
								<td colspan="2" style="text-align: center;"><h3>Resumen</h3></td>
							</tr>
							<tr><td colspan="2">&nbsp;</td></tr>
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
                            </table>
                        </td>
                    </tr>	
			</table>	

			<table id="table_botones">
				<tr>
				<td><input type="button" value="     Aprobar"  id="aprobar_cv"  name="aprobar_cv"  class="button_ok" /></td>
				<td><input type="button" value="     Volver"   id="Volver"      name="Volver"      class="button_volver" /></td>
				<td><input type="button" value="     Rechazar" id="rechazar_cv" name="rechazar_cv" class="button_rechazar" /></td>
				<td><input type="button" value="     Imprimir" id="imprimir"    name="imprimir"    class="button_imprimir" /></td>
				</tr>
			</table>
			
			<input type="hidden" id="idT" name ="idT" value="" />
			<input type="hidden" id="iu" name ="iu" value="<?php echo $_SESSION["idusuario"];?>" />
			<input type="hidden" name="delegado" id="delegado" value="<?php echo $_SESSION["idrepresentante"];?>" />		
			<input type="hidden" name="t_etapa_actual" id="t_etapa_actual" value=""/>
			<input type="hidden" name="hist" id="hist" readonly="readonly" value="<? echo $_GET['hist']; ?>" />
			</form>
		</div>
	<?PHP
	}
	?>