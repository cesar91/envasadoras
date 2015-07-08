	<!-- Inicia forma para comprobación -->
	<script type="text/javascript" src="../../lib/js/jquery/jquery.blockUI.js"></script> 
	<script type="text/javascript" src="../../lib/js/formatNumber.js"></script>
	<script type="text/javascript" src="../../lib/js/jqueryui/jquery-ui.min.js"></script>	
	<script type="text/javascript" src="../../lib/js/jquery/jquery.autocomplete.js"></script>
	<script type="text/javascript" src="../../lib/js/jquery/jquery.price_format.1.6.min.js"></script>		
	<script type="text/javascript" src="js/backspaceGeneral.js"></script>	
	<script type="text/javascript" src="js/configuracionGts.js"></script>
	<script type="text/javascript" src="js/cargaDatos.js"></script>
	
	<!-- Estilos -->
	<link rel="stylesheet" type="text/css" href="../../css/jquery.autocomplete.css"/>
	<link rel="stylesheet" type="text/css" href="../../css/smoothness/jquery-ui-1.10.3.custom.min.css"/>
	<link rel="stylesheet" type="text/css" href="../../css/table_style.css"/>
	<link rel="stylesheet" type="text/css" href="css/estilos_comprobacion.css"/>
	
		<script src="../../lib/js/jquery-ui-1.10.4/js/jquery-1.10.2.js"></script>
	<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.core.js"></script>
	<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.widget.js"></script>
	<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.mouse.js"></script>
	<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.draggable.js"></script>
	<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.position.js"></script>
	<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.resizable.js"></script>
	<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.button.js"></script>
	<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.dialog.js"></script>
	<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.effect.js"></script>
	<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.effect-blind.js"></script>
	<script src="../../lib/js/jquery-ui-1.10.4/development-bundle/ui/jquery.ui.effect-explode.js"></script>
	<script src="../../lib/js/jquery/jquery.json-2.4.min.js"></script>
	<script src="../../lib/js/jquery.uploadfile.min.js"></script>
	
		<script type="text/javascript">
		var j = jQuery.noConflict();
		var id = 0;
j(document).ready(function(){
	ocultarElemento("tr_kilometraje");
		j('#divisa').prop('selectedIndex',1);
		var divisa = j("#divisa").val();
		jQuery.fn.center = function(){
		this.css("position","absolute");
		this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) + $(window).scrollTop()) + "px");
		this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + $(window).scrollLeft()) + "px");
		return this;
		}
		
		var ant = $( "#anticipo").val();
		var rowCount = j('#comprobacion_table tr').length;
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
				rowTipoComp = j( "#div_row_tipoComprobacion"+i).html();
				rowTipoComp = j.trim(rowTipoComp);
				rowConcepto = j( "#div_row_conceptoTexto"+i).html();
					switch(rowConcepto) {
						case "Impuesto de IVA":
							rowIVA += parseFloat(j( "#div_row_total"+i).html().replace(/,/g, ""));
							rowIVARbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowIVAAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowIVAAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							break;
						case "Retenciones IVA":
							rowRetIVA += parseFloat(j( "#div_row_total"+i).html().replace(/,/g, ""));
							rowRetIVARbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowRetIVAAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowRetIVAAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							break;
						case "Retenciones ISR":
							rowRetISR += parseFloat(j( "#div_row_total"+i).html().replace(/,/g, ""));
							rowRetISRRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowRetISRAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowRetISRAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							break;													
						case "Descuento":
							rowDesc += parseFloat(j( "#div_row_total"+i).html().replace(/,/g, ""));
							rowDescRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowDescAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowDescAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							break;
						case "IEPS":
							rowIeps += parseFloat(j( "#div_row_total"+i).html().replace(/,/g, ""));
							rowIepsRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowIepsAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowIepsAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							break;							
						default:
							rowTotal += parseFloat(j( "#div_row_total"+i).html().replace(/,/g, ""));
							rowTotalRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowTotalAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowTotalAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							}
			}
			j( "#div_res_subtotal").html("$"+rowTotal.toFixed(2));
			j( "#div_res_desc").html("$"+rowDesc.toFixed(2));
			j( "#div_res_iva").html("$"+rowIVA.toFixed(2));
			j( "#div_res_ret_iva").html("$"+rowRetIVA.toFixed(2));
			j( "#div_res_ret_isr").html("$"+rowRetISR.toFixed(2));
			j( "#div_res_ieps").html("$"+rowIeps.toFixed(2));
			
			j( "#div_res_subtotal_ant").html("$"+rowTotalAnt.toFixed(2));
			j( "#div_res_desc_ant").html("$"+rowDescAnt.toFixed(2));
			j( "#div_res_iva_ant").html("$"+rowIVAAnt.toFixed(2));
			j( "#div_res_ret_iva_ant").html("$"+rowRetIVAAnt.toFixed(2));
			j( "#div_res_ret_isr_ant").html("$"+rowRetISRAnt.toFixed(2));
			j( "#div_res_ieps_ant").html("$"+rowIepsAnt.toFixed(2));
			
			j( "#div_res_subtotal_amex").html("$"+rowTotalAMEX.toFixed(2));
			j( "#div_res_desc_amex").html("$"+rowDescAMEX.toFixed(2));
			j( "#div_res_iva_amex").html("$"+rowIVAAMEX.toFixed(2));
			j( "#div_res_ret_iva_amex").html("$"+rowRetIVAAMEX.toFixed(2));
			j( "#div_res_ret_isr_amex").html("$"+rowRetISRAMEX.toFixed(2));
			j( "#div_res_ieps_amex").html("$"+rowIepsAMEX.toFixed(2));									
				var TotalReembolso = rowTotalRbl - rowDescRbl + rowIVARbl - rowRetIVARbl - rowRetISRRbl - rowIepsRbl;
				j( "#div_total_reembolso").html("$"+TotalReembolso.toFixed(2));
				var TotalAnticipo = rowTotalAnt - rowDescAnt + rowIVAAnt - rowRetIVAAnt - rowRetISRAnt - rowIepsAnt;
				j( "#div_comp_anticipo").html("$"+TotalAnticipo.toFixed(2));
				var TotalAMEX = rowTotalAMEX - rowDescAMEX + rowIVAAMEX - rowRetIVAAMEX - rowRetISRAMEX - rowIepsAMEX;
				j( "#div_comp_amex").html("$"+TotalAMEX.toFixed(2));
				var TotalPendiente = ant - TotalAnticipo;
				j( "#div_pendiente").html("$"+TotalPendiente.toFixed(2));
				var div_totalSuma = TotalReembolso + TotalAnticipo +TotalAMEX;
				$( "#div_totalSuma").html("$"+div_totalSuma.toFixed(2));
				$( "#anticipoComprobado").val(div_totalSuma.toFixed(2));				
		j('#CargaArchivo').click(function(event){
			  $.blockUI({ message: '<h5 align="center">Espere un momento...</h5><img align="center" src="../../imgs/loading.png"/>', theme: true, fadeIn: 0  });
			  j('html').append('<iframe style="display:none;" id="myiframe" name="myiframe" src=""></iframe>');
			  j("#cargaXML").submit();
			  $("#myiframe").load(function(e){
			   var objUploadBody = window.frames[$(this).attr("id")].document.getElementsByTagName("body")[0];
			   var jBody = $(objUploadBody);
			   var response = jBody.html().replace(/(<([^>]+)>)/ig,"");
			   var response2 = jBody.html();
			   try{
				returnedData = j.evalJSON(response);
					if(returnedData.msj != "Exito"){
						alert(returnedData.msj);
						j('#dialog').dialog("close");
						j("#concepto").val(0);
						j("#concepto").trigger("change");
						j("#myfile").val('');
						j("#myiframe").remove();						
						return false;
					}				
					var rowCount = j('#comprobacion_table tr').length;
					var banderaFactura = false;
					for (i = 1; i <rowCount; i++) {
						var validauuid = j("#uuid"+i).val();
						if(validauuid == returnedData.Timbre.uuid){
							banderaFactura = true;
						}
					}
						if(banderaFactura == false){
							if(returnedData.msj == "Exito"){
							j( "#dialog" ).dialog( "close" );
							var emisorDomicilio = returnedData.Emisor.emisorCalle+" "+
												  returnedData.Emisor.emisorNoExt+" "+
												  returnedData.Emisor.emisorNoInt+" "+
												  returnedData.Emisor.emisorCol+" "+
												  returnedData.Emisor.emisorLocalidad+" "+
												  returnedData.Emisor.emisorMunicipio+" "+
												  returnedData.Emisor.emisorCodigoPostal;
								////////////////////////////////////////////////
								////////////////Creo los inputs/////////////////
								////////////////////////////////////////////////
								id++;
							var res = '';
								//-----------Contenido del Archivo------------//
								res += '<textarea name="contenidoXML" id="contenidoXML" rows="4" cols="50">'+returnedData.contenidoXml+'</textarea>';
								//-----------Contenido del Timbre------------//
								res += '<input name="uuid" id="uuid" value="'+returnedData.Timbre.uuid+'"/>';
								res += '<input name="fechaTimbrado" id="fechaTimbrado" value="'+returnedData.Timbre.fechaTimbrado+'"/>';
								res += '<input name="selloCFD" id="selloCFD" value="'+returnedData.Timbre.selloCFD+'"/>';
								res += '<input name="noCertificadoSat" id="noCertificadoSat" value="'+returnedData.Timbre.noCertificadoSat+'"/>';
								res += '<input name="selloSat" id="selloSat" value="'+returnedData.Timbre.selloSat+'"/>';
								//-----------Contenido del Emisor------------//
								res += '<input name="emisorNombre" id="emisorNombre" value="'+returnedData.Emisor.emisorNombre+'"/>';
								res += '<input name="emisorRFC" id="emisorRFC" value="'+returnedData.Emisor.emisorRFC+'"/>';
								res += '<input name="emisorDomicilio" id="emisorDomicilio" value="'+emisorDomicilio+'"/>';
								res += '<input name="emisorEstado" id="emisorEstado" value="'+returnedData.Emisor.emisorEstado+'"/>';
								res += '<input name="emisorPais" id="emisorPais" value="'+returnedData.Emisor.emisorPais+'"/>';
								//------Contenido del Comprobante------------//
								res += '<input name="comprobanteVersion" id="comprobanteVersion" value="'+returnedData.Comprobante.comprobanteVersion+'"/>';
								res += '<input name="comprobanteSerie" id="comprobanteSerie" value="'+returnedData.Comprobante.comprobanteSerie+'"/>';
								res += '<input name="comprobanteFolio" id="comprobanteFolio" value="'+returnedData.Comprobante.comprobanteFolio+'"/>';
								res += '<input name="comprobanteFecha" id="comprobanteFecha" value="'+returnedData.Comprobante.comprobanteFecha+'"/>';
								res += '<input name="comprobanteSello" id="comprobanteSello" value="'+returnedData.Comprobante.comprobanteSello+'"/>';
								res += '<input name="comprobanteFPago" id="comprobanteFPago" value="'+returnedData.Comprobante.comprobanteFPago+'"/>';
								res += '<input name="comprobanteNoCer" id="comprobanteNoCer" value="'+returnedData.Comprobante.comprobanteNoCer+'"/>';
								res += '<input name="comprobanteCertificado" id="comprobanteCertificado" value="'+returnedData.Comprobante.comprobanteCertificado+'"/>';
								res += '<input name="comprobanteSubtotal" id="comprobanteSubtotal" value="'+returnedData.Comprobante.comprobanteSubtotal+'"/>';
								res += '<input name="comprobanteTipoCambio" id="comprobanteTipoCambio" value="'+returnedData.Comprobante.comprobanteTipoCambio+'"/>';
								res += '<input name="comprobanteMoneda" id="comprobanteMoneda" value="'+returnedData.Comprobante.comprobanteMoneda+'"/>';
								res += '<input name="comprobanteTotal" id="comprobanteTotal" value="'+returnedData.Comprobante.comprobanteTotal+'"/>';
								res += '<input name="comprobanteTipoComp" id="comprobanteTipoComp" value="'+returnedData.Comprobante.comprobanteTipoComp+'"/>';
								res += '<input name="comprobanteMetodoPago" id="comprobanteMetodoPago" value="'+returnedData.Comprobante.comprobanteMetodoPago+'"/>';
								res += '<input name="comprobanteExpedicion" id="comprobanteExpedicion" value="'+returnedData.Comprobante.comprobanteExpedicion+'"/>';
								j('#tablaXML').append('<table id="tabla"><tr><td>'+res+'<td></tr></table>');
								j("#folio").val(returnedData.Comprobante.comprobanteFolio);
								j("#folio").attr("readonly", "readonly");
								j("#folio").attr("disabled", "disabled");
								j("#proveedor").val(returnedData.Emisor.emisorNombre);
								j("#proveedor").attr("readonly", "readonly");
								j("#proveedor").attr("disabled", "disabled");
								j("#rfc").val(returnedData.Emisor.emisorRFC);
								j("#rfc").attr("readonly", "readonly");
								j("#rfc").attr("disabled", "disabled");
								j("#monto").val(returnedData.Comprobante.comprobanteSubtotal);
								j("#monto").attr("readonly", "readonly");
								j("#monto").attr("disabled", "disabled");
								j("#total").val(returnedData.Comprobante.comprobanteTotal);
								j("#total").attr("readonly", "readonly");
								j("#total").attr("disabled", "disabled");
								var ImpuestosTraslado = returnedData.ImpuestosTraslado.importe;
								var ImpuestosTrasladoTipo = returnedData.ImpuestosTraslado.impuesto;
								var k=0;
								var totalIVA = 0;
								if(ImpuestosTraslado != ""){
									var ik = "";
										for(objetoTraslado in ImpuestosTraslado){
										totalIVA += parseFloat(ImpuestosTraslado[objetoTraslado]);
											k++;
											ik += "<input type='text' name='impuestoTraslado"+objetoTraslado+"' id='impuestoTraslado"+objetoTraslado+"' tipo='"+ImpuestosTrasladoTipo[k-1]+"' value='"+ImpuestosTraslado[objetoTraslado]+"'>";
										}
									j("#impuestoTraslado").html(ik);
								}
								j("#iva").val(totalIVA.toFixed(2));
								var ImpuestosRetencion = returnedData.ImpuestosRetencion.importe;
								var impuestoRetencionTipo = returnedData.ImpuestosRetencion.impuesto;
								var i=0;
								if(ImpuestosRetencion != ""){
									var ir = "";
										for(objeto in ImpuestosRetencion){
											i++;
											ir += "<input type='text' name='impuestoRetencion"+objeto+"' id='impuestoRetencion"+objeto+"' tipo='"+impuestoRetencionTipo[i-1]+"' value='"+ImpuestosRetencion[objeto]+"'>";
										}
									j("#impuestoRetencion").html(ir);	
								}
								var descuento = returnedData.Comprobante.comprobanteDescuento;
								if(descuento > ""){
									var desc = "";
									desc += "<input type='text' name='valDesc' id='valDesc' value='"+descuento+"'>";
									j("#divDescuento").html(desc);
								}
								j("#impuestoTraslado").val(returnedData.ImpuestosTraslado.importe);
								j("#impuestoRetencion").val(returnedData.ImpuestosRetencion.importe);
								j("#iva").attr("readonly", "readonly");
								j("#iva").attr("disabled", "disabled");
								j("#muestraAgregarProveedor").hide();
								j("#table_proveedor").fadeIn(3000);
								j( "#proveedorNacional" ).prop( "checked", true );
								j( "#proveedorNacional" ).prop( "disabled", true );
								j( "#factura" ).prop( "disabled", true );
								validaIva();
								recalcularTotales();
								j( "#iva" ).removeClass( "req" );
							}else{
								alert(returnedData.msj)
							}
						}else{
							alert("Esta factura ya se encuentra registrada dentro del Detalle de Comprobacion");
						}							
			   }catch(ex){
			   alert("Error de conexion");
				j('#dialog').dialog("close");
				j("#concepto").val(0);
				j("#concepto").trigger("change");			   
				console.log(ex);
			   } 
			   $.unblockUI();        
			   $(this).remove();
			   e.preventDefault();
			   e.stopPropagation();
			   event.stopPropagation();
			   event.preventDefault();         
			  });
			});
			j('#dialog').dialog({
				modal: true,
				autoOpen: false,
				height: 'auto',
				width: 'auto',
				open: function(event, ui) {
					$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
				},
				buttons: { "Cancelar": function() { 
								j(this).dialog("close");
								j("#concepto").val(0);
								j("#concepto").trigger("change");
								} 
							}, 				
				show: {
					effect: "blind",
					duration: 1000
				},
				hide: {
					effect: "explode",
					duration: 1000
				},
					position: ['center', 'top']
			});
			j("#factura").click(function(){
				if(j("#factura").is(':checked')){
					j( "#dialog" ).dialog( "open" );
				}
			});
			j("#concepto").change(function(){
				if(j("#concepto").val() == 42){
					alert("Recuerda que tus propinas no deben ser mayores al 10% del monto de los alimentos");
				}
								if(j("#factura").is(':checked')){
					j( "#dialog" ).dialog( "open" );
				}
			});
			j("#concepto").change(function(){
			ocultarElemento("tr_kilometraje");
			j( "#kilometros" ).val('');			
				if(j("#concepto").val() == 42){
					alert("Recuerda que tus propinas no deben ser mayores al 10% del monto de los alimentos");
				}
				if(j("#concepto").val() == 33){
					j( "#monto" ).prop( "disbled", true );
					j( "#monto" ).prop( "readonly", true );
					mostrarElemento("tr_kilometraje");
					j( "#kilometros" ).keyup(function() {
					var kilometros = j("#kilometros").val();
						var totKil = kilometros * parseFloat('2.4');
						j("#monto").val(totKil.toFixed(2));
						recalcularTotales(); 
					});					
				}else{
					j( "#monto" ).val('');
					j( "#monto" ).prop( "disabled", false );
					j( "#monto" ).prop( "readonly", false );				
				}
				j( "#factura" ).prop( "checked", false );
				j( "#proveedorNacional" ).prop( "checked", false );
				j( "#folio" ).val('');
				j( "#rfc" ).val('');
				j( "#proveedor" ).val('');
				j( "#total" ).val('');
				j( "#totalPartida" ).val('');
				j( "#iva" ).val('');
				j( "#impuestoTraslado" ).html('');
				j( "#divDescuento" ).html('');
				j( "#tablaXML" ).html('');			
				j( "#divisa" ).prop( "disabled", true );
				j('#divisa').prop('selectedIndex',1);
				validaIva();
				recalcularTotales(); 
				var concepto = j("#concepto").val();
				var param = "conceptoXML=conceptoXML&concepto="+concepto;
				var json = obtenJson(param);
				var deducible = json["cp_deducible"];
				var motivo = j("#motivo").val();
				var tipoComprobacion = j("#tipoComprobacion").val();
				var solicitud = j("#solicitud").val();
					if(solicitud == "-1"){
						if(motivo > ""){
							if(tipoComprobacion != 0){
								if(deducible == 1){
									j( "#trFact" ).show();
									j( "#trProveedor" ).show();
									j( "#factura" ).prop( "checked", true );
									j( "#dialog" ).dialog( "open" );
									j( "#myfile" ).prop( "disabled", false );
									j( "#CargaArchivo" ).prop( "disabled", false );
								}else{
									j( "#iva" ).prop( "disabled", false );
									j( "#iva" ).prop( "readonly", false );
									$( "#iva" ).removeClass( "req" );
									j( "#trFact" ).hide();
									j( "#trProveedor" ).hide();
									j( "#td_iva1" ).hide();
									j( "#td_iva2" ).hide();
								}
							}else{
								alert("Debes especificar el Tipo de Comprobacion");
								j('#concepto').prop('selectedIndex',0);
								return false;							
							}
						}else{
							alert("Debes especificar el campo motivo");
							j('#concepto').prop('selectedIndex',0);
							return false;
						}
					}else{
						if(tipoComprobacion != 0){
							if(deducible == 1){
								j( "#trFact" ).show();
								j( "#trProveedor" ).show();
								j( "#factura" ).prop( "checked", true );
								j( "#dialog" ).dialog( "open" );
								j( "#myfile" ).prop( "disabled", false );
								j( "#CargaArchivo" ).prop( "disabled", false );
							}else{
								j( "#iva" ).prop( "disabled", false );
								j( "#iva" ).prop( "readonly", false );
								$( "#iva" ).removeClass( "req" );
								j( "#trFact" ).hide();
								j( "#trProveedor" ).hide();
								j( "#td_iva1" ).hide();
								j( "#td_iva2" ).hide();
							}
						}else{
							alert("Debes especificar el Tipo de Comprobacion");
							j('#concepto').prop('selectedIndex',0);
							return false;							
						}				
					}
			});
			var tablaLength = obtenTablaLength("comprobacion_table");
				for(var i = 1; i <= tablaLength; i++){
						$("#comprobacion_table>thead>tr>th").eq(15).hide();
						$("#tr_"+i+" td").eq(15).hide();						
				}			
});		
		</script>
	
	<div id="Layer1">
		<div style="display:none;" id="tablaXML">
	</div>
		<center><h2>Comprobaci&oacute;n Gastos</h2></center>
		<center><h3>Detalle de la Comprobaci&oacute;n</h3></center>
        <form action="controller_comprobacion_gastos.php" method="post" name="form_comprobacion" id="form_comprobacion">		
			<!-- Inicia Datos Generales de la Comprobaciones -->
			<table id="table_infoGeneral" class="tablaDibujada">
				<tr>
					<td rowspan="5" width="70px">&nbsp;</td>
					<td colspan="4">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3">Solicitud<span class="style1">*</span>:
						<select name="solicitud" id="solicitud"></select>
						<input type="text" name="motivoSolicitud" id="motivoSolicitud" value="" />
					</td>
					<td><div id="consulta_solicitud">&nbsp;</div></td>
				</tr>					
				<tr id="tr_gasolina">
					<td>
						Motivo<span class="style1">*</span>: 
						<input name="motivo" id="motivo" size="25" />						
					</td>
					<td id="td_fechas" colspan="3">
						Fecha Inicial<span class="style1">*</span>: 
						<input name="fechaInicial" id="fechaInicial" size="12" value="" readonly="readonly" />
						Fecha Final<span class="style1">*</span>: 
						<input name="fechaFinal" id="fechaFinal" size="12" value="" readonly="readonly" />						
					</td>
				</tr>					
				<tr>
					<td colspan="4">
						Seleccione el tipo de comprobaci&oacute;n que desea realizar<span class="style1">*</span>:                            
						<select name="tipoComprobacion" id="tipoComprobacion"></select>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						Centro de Costos al que se cargar&aacute;<span class="style1">*</span>:
						<select name="centroCostos" id="centroCostos" ></select>
					</td>
				</tr>
				<tr><td colspan="5">&nbsp;</td></tr>
			</table>
			<!-- Fin Datos Generales de la Comprobaciones -->				
			<br />
			<!-- Inicia Datos AMEX -->
			<div id="div_amex" >
				<table id="table_Amex" class="tablaDibujada">
					<tr>
						<td rowspan="10">&nbsp;</td>
						<td colspan="2"><h3 align="center">Amex</h3></td>
						<td rowspan="10">&nbsp;</td>
					</tr>
					<tr>
						<td>
							Tipo de tarjeta<span class="style1">*</span>: 
							<select name="tipoTarjeta" id="tipoTarjeta"></select>
						</td>
						<td>
							Tarjeta de Cr&eacute;dito:&nbsp;
							<div name="noTarjeta" id="noTarjeta"></div>
						</td>							
					</tr>
					<tr>
						<td colspan="2">
							Lista de Cargos<span class="style1">*</span>: 
							<select name="cargoTarjeta" id="cargoTarjeta">
							<option id="0" value="0">- Seleccione -</option>
							</select>
						</td>							
					</tr>
					<tr>
						<td colspan="2">
							No. Transacci&oacute;n:
							<div id="div_noTransaccion"></div>
						</td>							
					</tr>
					<tr>
						<td colspan="2"><h3 align="center">Detalle del cargo</h3></td>
					</tr>
					<tr>
						<td>
							Establecimiento:
							<div id="div_establecimiento" ></div>
						</td>
						<td>
							Total origen factura: 
							<div id="div_montoCargo"></div>
							<input type="hidden" id="montoCargo" name="montoCargo" />
							<input type="hidden" id="monedaCargo" name="monedaCargo" />							
						</td>
					</tr>
					<tr>
						<td>
							Fecha: 
							<div id="div_fechaCargo" ></div>
						</td>
						<td>
							Total Amex: 
							<div id="div_totalAmex" ></div>
							<input type="hidden" id="totalAmex"/>							
						</td>                            
					</tr>
					<tr>
						<td>
							RFC: 							
							<div id="div_rfc"></div>
						</td>
						<td>
							Total MXN: 
							<div id="div_totalPesos" ></div>
							<input type="hidden" id="totalPesos" name="totalPesos"/>							
						</td>							
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>
							Tipo de cambio: 
							<div id="div_tipoCambio"></div>
							<input type="hidden" id="tipoCambio" name="tipoCambio"/>							
						</td>							
					</tr>                       
					<tr><td colspan="2">&nbsp;</td></tr>
				</table>     
			</div>              
			<!-- Fin Datos AMEX -->
			<!-- Inicioa captura de Partidas -->
			<table id="table_regisrtoPartidas" class="tablaDibujada">
				<tr><td colspan="8">&nbsp;</td></tr>
				<tr>
					<td rowspan="14">&nbsp;</td>
					<td colspan="6"><h3 align="center">Registro de Partidas</h3></td>
					<td rowspan="14">&nbsp;</td>
				</tr>
				<tr>
					<td>Concepto<span class="style1">*</span>:</td>
					<td colspan="3"><select name="concepto" id="concepto" ></select></td>
					<td>Fecha Comprobante<span class="style1">*</span>:</td>
					<td><input name="fecha" id="fecha" size="12" value="" readonly /></td>                        
				</tr>
				<tr id="tr_tipoComida">
					<td>Tipo<span class="style1">*</span>:</td>
					<td><select name="tipoComida" id="tipoComida" ></select></td>
					<td colspan="4"></td>
				</tr>
				<tr id="trFact" style = "display:none;">
					<td>Requiere Factura:</td>
					<td><input type="checkbox" name="factura" id="factura"  /></td>
				</tr>
				<tr id="trProveedor" style = "display:none;"> 
					<td>Proveedor Nacional:</td>
					<td><input type="checkbox" name="proveedorNacional" id="proveedorNacional"  /></td>
					<td colspan="2"><a href="http://www.oanda.com/lang/es/currency/converter/" target="_black">Realiza la conversi&oacute;n a tu divisa</a></td>
					<td colspan="2" rowspan="7" valign="top">
						<!-- Inicio Pantalla de Proveedores -->						
						<table id="table_proveedor" class="tablaDibujada" style="width:250px">
							<tr>
								<td rowspan="6">&nbsp;</td>
								<td colspan="4" ><h3 align="center">Datos de proveedor</h3></td>
								<td rowspan="6">&nbsp;</td>
							</tr>
							<tr>
								<td>Folio Factura<span class="style1">*</span>:</td>
								<td><input type="text" name="folio" id="folio" size="8"/></td>										
							</tr>
							<tr>
								<td>RFC<span class="style1">*</span>:</td>
								<td><input type="text" name="rfc" id="rfc" value="" size="13" maxlength="13" /></td>										
							</tr>	
							<tr>
								<td>Raz&oacute;n Social<span class="style1">*</span>:</td>
								<td><input name="proveedor" type="text" id="proveedor" value="" size="24" /></td>										
							</tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr>
								<td colspan="2" align="center">
									<input class="button_agregar" type="button" name="muestraAgregarProveedor" id="muestraAgregarProveedor" value="     Agregar Nuevo Proveedor" />											
								</td>										
							</tr>
						</table>						
						<!-- Fin Pantalla de Proveedores -->							
					</td>                        
				</tr>
				<tr>             
					<td>Subtotal/Monto<span class="style1">*</span>:</td>
					<td><input name="monto" id="monto" value="0" size="12"/></td>                       
					<td>Divisa<span class="style1">*</span>:</td>
					<td><select name="divisa" id="divisa"></select></td>
				</tr>
				<tr>                    
					<td>Total<span class="style1">*</span>: </td>
					<td><input name="total"  id="total" value="0.00" size="12" /></td>
					<td id="td_iva1">IVA<span class="style1">*</span>: $</td>
					<td id="td_iva2"><input name="iva" id="iva" value="0" size="8" /></td>
				</tr>                   
				<tr>
					<td>Total en pesos:</td>
					<td colspan="3"><input name="totalPartida" value="0.00" id="totalPartida" size="12" /></td>                        
				</tr>
				<tr>
					<td>Comentario<label id="label_comentarioReq"></label>:</td>
					<td colspan="3"><textarea name="comentario" id="comentario" cols="50" rows="3" maxlength="36" ></textarea></td>                        
				</tr>
				<tr id="tr_asistentes">
					<td>No. Asistentes<span class="style1">*</span>:</td>
					<td colspan="3"><input name="asistentes" id="asistentes" value="1" size="10"/></td>						
				</tr>
				<tr id="tr_kilometraje">
					<td>Kilometros<span class="style1">*</span>:</td>
					<td colspan="3"><input name="kilometros" id="kilometros" value="1" size="10"/></td>						
				</tr>					
				<tr id="tr_propina">
					<td>Propina:</td>
					<td colspan="3"><input name="propina" value="0" id="propina" size="10" /></td>					
				</tr>
				<tr id="tr_impuestoHospedaje">                        
					<td>Impuesto hospedaje<span class="style1">*</span>:</td>
					<td colspan="5"><input name="impuestoHospedaje" value="0" id="impuestoHospedaje" size="10" /></td>						
				</tr>
				<tr id="tr_lugarRestaurante">                        
					<td>Lugar/Restaurante<span class="style1">*</span>:</td>
					<td colspan="5"><input type="text" name="lugar" id="lugar" value="" size="50" maxlength="100" /></td>						
				</tr>
				<tr id="tr_ciudad">                        
					<td>Ciudad<span class="style1">*</span>:</td>
					<td colspan="5"><input name="ciudad" type="text" id="ciudad" value="" size="50" maxlength="100" /></td>						
				</tr>
				<tr><td colspan="6">&nbsp;</td></tr>
				<tr><td colspan="6">&nbsp;</td></tr>        
			</table>
			<!-- Fin captura de Partidas -->
			<br/>
			<!-- Inicio de Div Comidas de Representacion -->
			<table id="invitados_table" border="0" cellspacing="1" class="tablaDibujada">
				<tr>
					<td colspan="4" align="center" valign="middle"><h3>Invitados</h3></td>
				</tr>
				<tr>
					<td colspan="4">
					<table id="infoComidaRepresentacion" border="0">
						<tr>
							<td>&nbsp;</td>
							<td width="50%">Nombre<span class="style1">*</span>:&nbsp;&nbsp;<input name="nombre" type="text" id="nombre" size=50 maxlength="100" /></td>
							<td width="50%">Tipo de Invitado<span class="style1">*</span>:&nbsp;&nbsp;<select name="tipoInvitado" id="tipoInvitado"></select>
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td width="50%">Puesto<span class="style1">*</span>:&nbsp;&nbsp;&nbsp;&nbsp;<input name="puesto" type="text" id="puesto" size=50 maxlength="100" /></td>
							<td width="50%">Empresa<span class="style1">*</span>:&nbsp;&nbsp;<input name="empresaInvitado" type="text" id="empresaInvitado" size=50 maxlength="100" /></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><div id="capaDirector">&nbsp;</div></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td colspan="4">&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan="2" align="center"><input class="button_ok" name="agregar_invitado" type="button" id="agregar_invitado" value="     Agregar Invitado" /></td>
	                <td>&nbsp;</td>
	            </tr>
	            <tr>
	            	<td colspan="4">&nbsp;</td>
	            </tr>
	            <tr>
	            	<td>&nbsp;</td>
	            	<td id="td_Invitados" colspan="2" align="center" valign="middle">
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
	            			<tbody></tbody>
						</table>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="4">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="4">N&uacute;mero de invitados: <span id="span_totalInvitados"></span>
						<input type="hidden" name="numInvitados" id="numInvitados" value="0" readonly="readonly" />
					</td>
				</tr>
			</table>
			<!-- Fin de Comidas de Representacion -->
			<br />
			<!-- Inicio Div Gasolina -->				
			<div id="div_gasolina" class="div_flotante" >
				<table class="div_close">
					<tr>
						<td>Comprobaci&oacute;n de Gasolina</td>
						<td width="16px"><img src="../../images/close2.ico" id="cerrar_div_gasolina" alt="Cerrar" align="right" style="cursor:pointer"/></td>
					</tr>
				</table>
				
				<table class="tablaDibujada" style="width:445px">
					<th colspan="3"><h3 align="center">Comprobaci&oacute;n de Gasolina</h3></th>
					<tr>
						<td>Modelo de Auto<span class="style1">*</span>:</td>
						<td><select name="modeloAuto" id="modeloAuto">
							<option id="0" value="0">- Seleccione -</option>
							</select></td>
						<td><div id="div_factor" name="div_factor"></div></td>
					</tr>
					<tr>
						<td>Kilometraje<span class="style1">*</span>:</td>
						<td><input name="kilometraje" id="kilometraje"  value="0.00"></td>
						<td><a target="_blank" href="https://maps.google.com/">Buscar Kilometraje</a></td>
					</tr>
					<tr>
						<td>Monto de gasolina<span class="style1">*</span>:</td>
						<td><input name="monto_gasolina" value="0.00" id="monto_gasolina"></td>
						<td>MXN</td>
					</tr>
					<tr>
						<td>Ruta detallada<span class="style1">*</span>:</td>
						<td colspan="2"><textarea name="ruta_detallada" id ="ruta_detallada" cols="40" rows="5"></textarea></td>
					</tr>
					<tr>
						<td colspan="3" align="center">
							<input class="button_enviar" type="button" value="     Aceptar" name="enviarComprobacionGasolina" id="enviarComprobacionGasolina" />
							<input class="button_cancelar" type="button" value="     Cancelar" name="cancelarGasolina" id="cancelarGasolina" />
						</td>
					</tr>
				</table>
			</div>
			<!-- Fin Div Gasolina -->
			<br />
			<!-- Inicio Div Proveedores -->                
			<div id="div_nuevoProveedor" class="div_flotante">
				<table class="div_close">
					<tr>
						<td>Pantalla proveedores</td>						
						<td><img src="../../images/close2.ico" id="cerrar_div_nuevoProveedor" alt="Cerrar" align="right" style="cursor:pointer;" /></td>
					</tr>
				</table>
				<br />
				<table class="tablaDibujada" style="width:300px">
					<tr><td colspan="2" align="center"><h3>Agregar nuevo proveedor</h3></td></tr>
					<tr>
						<td align="right">Raz&oacute;n Social<span class="style1">*</span>:</td>
						<td><input name="nuevoProveedor" type="text" id="nuevoProveedor" value="" size="50" /></td>
					</tr>
					<tr>
						<td align="right">RFC<span class="style1">*</span>:</td>
						<td><input name="nuevoRfc" type="text" id="nuevoRfc" value="" size="30" maxlength="13"></td>
					</tr>
					<tr>
						<td align="right">Domicilio Fiscal<span class="style1">*</span>:</td>
						<td><input name="nuevoDomicilio" id="nuevoDomicilio" value="" size="80" /></td>
					</tr>
					<tr>
						<td colspan="2" align="center">
							<input class="button_ok" type="button" name="agregarProveedor" id="agregarProveedor" value="     Agregar"  />
							<input class="button_cancelar" type="button" name="cancelarAgregarProveedor" id="cancelarAgregarProveedor" value="      Cerrar Panel de Nuevo Proveedor"/>
						</td>
					</tr>
				</table>
			</div>
			<!-- Fin Div Proveedores -->
			<br />
			<div id="div_mensajes" align="center" ></div>
			<br />
			<div align="center"> 
				<input class="button_ok" name="registrarPartida" type="button" id="registrarPartida" value="     Registrar gasto" />					
			</div>
            <center><h3>Detalle de la Comprobaci&oacute;n</h3></center>             
			<!-- Inicio Tabla de Partidas -->
            <table id="comprobacion_table" class="tablesorter" cellspacing="1"> 
                <thead> 
                    <tr> 
                        <th>No.</th>
                        <th>Tipo</th>
						<th>No. Trasacci&oacute;n</th>
                        <th>Fecha</th>
                        <th>Concepto</th>
                        <th>Comentario</th> 
                        <th>No. Asistentes</th> 
                        <th>RFC</th>
                        <th>Proveedor</th>
                        <th>Factura</th>
                        <th>Monto </th>
                        <th>Total</th>
						<th>Divisa</th>
						<th>Editar</th>
                        <th>Eliminar</th>
                    </tr> 
                </thead> 
                <tbody>                     
                </tbody> 
            </table>  
			<!-- Fin Tabla de Partidas -->
			<!-- Inicio Tabla de Excepciones -->
            <table id="excepcion_table" class="tablesorter" cellspacing="1"> 
				<thead> 									
                    <tr> 
                        <th>No.</th>
                        <th>Concepto</th>
						<th>Mensaje</th>
						<th>Fecha</th>
						<th>Referencia</th>
						<th>Partidas</th>
                        <th>Diferencia</th>
                    </tr> 
                </thead> 
                <tbody>                     
                </tbody> 
            </table>  
			<!-- Fin Tabla de Excepciones -->
            <br />
            <br />
			<!-- Iniciao Observaciones y Resumen -->
            <div align="center">            	
                <table>
                   	<tr id="tr_historialObservaciones">
                   	 	<td colspan="2" align="center">
							<table>
								<tr>
									<td>Historial de observaciones: </td>
									<td>
										<textarea name="historialObervaciones" id="historialObervaciones" cols="70" rows="6" ></textarea>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table align="center">
								<tr>
									<td>Observaciones:</td>
									<td>
										<textarea name="observaciones" cols="70" rows="6" id="observaciones"></textarea>
									</td>                        
								</tr>
							</table>
                        </td>				
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
										<input type="hidden" name="anticipoComprobado" id="anticipoComprobado" value="" />
									</td>
                                </tr>								
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
			<!-- Fin Observaciones y Resumen -->
            <br />
			<br />                
			<table id="table_botones" align="center">
				<tr align="center">
					<td>
						<input class="button_enviar" type="button" id="guardarPrevio" name="guardarPrevio" value="      Guardar Previo"  />							
					</td>
					<td>&nbsp;</td>
					<td>
						<input class="button_enviar" type="button" id="enviarComprobacion" name="enviarComprobacion" value="      Enviar Comprobacion" /> 
					</td>
				</tr>
			</table>
			<!-- Respaldo de Informacion de Comidas de representacion -->
			<div id="respaldo_invitado_table">
				<input type="text" id="lugarComida" name="lugarComida" readonly="readonly" />
				<input type="text" id="ciudadComida" name="ciudadComida" readonly="readonly" />
				<input type="text" id="noInvitados" name="noInvitados" readonly="readonly" />
			</div>
			<!-- Fin de respaldo de Informacion de Comidas de representacion -->
			<input type="text" name="idusuario" id="idusuario" value="<?PHP echo $_SESSION["idusuario"];?>" />
			<input type="text" name="empresa" id="empresa" value="<?PHP echo $_SESSION["empresa"];?>" />
			<input type="text" name="perfil" id="perfil" value="<?PHP echo $_SESSION["perfil"];?>" />
			<input type="hidden" name="delegado" id="delegado" value="<?PHP echo $_SESSION["idrepresentante"];?>" />			
			<input type="text" name="tipoViaje" id="tipoViaje" value="" />
			<input type="text" name="diasViaje" id="diasViaje" value="" />
			<input type="text" name="region" id="region" value="1" />
			<input type="text" name="tramiteEdit" id="tramiteEdit" value="" />
			<input type="text" name="totalPartidas" id="totalPartidas" value="" />
			<input type="text" name="totalExcepciones" id="totalExcepciones" value="" />
			
			<div id="impuestoTraslado" style="display:none">
			</div>
			<div id="impuestoRetencion" style="display:none">
			</div>
			<div id="divDescuento" style="display:none">
			</div>			
		</form>
			<div id="dialog" title="Carga Factura">
				<form name = "cargaXML" id = "cargaXML" method="post" target="myiframe" enctype="multipart/form-data" action="validaXML.php">
					<input type="file" name ="myfile" id="myfile" />
					<input type="submit" name="CargaArchivo" id="CargaArchivo" value="Cargar" />
				</form>

			</div>
	</div>