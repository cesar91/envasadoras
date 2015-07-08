var cc_centrocostos;
var cc_codigo_new=0;
var valConcepto="";
var registrosOriginales=0;

// Variables editables
var montoOriginal=0;
var ivaOriginal=0;
var propinaOriginal=0;
var imphospedajeOriginal=0;
var totalOriginal=0;

// Variable para agregar nuevas filas
var i=0;

// Variables para el calculo del resumen
var personal_amex =0;
var personal_anticipo=0;

// Variables para la actualizacion de datos necesarios (Pediente redaccion)
var cont=0;

// Total
var dc_total=0;
var frm = document.comp_inv;

function Location(){
	location.href='index.php?docs=docs&type=4';
}

// Función que permitirá desactivar los botones (Eliminar/recalculo)	
function desactivarBoton(){
	var filas = parseInt($("#descrip_comprobacion_table>tbody >tr").length);
	for(var i=1;i<=filas;i++){
		$("#recalculo"+i).css("display", "none");
		$("#eliminar"+i).css("display", "none");
	}
}

// Imprimir Reporte en PDF
function imprimir_pdf(){
	var tramite = $("#idT").val();
	window.open("generador_pdf_comp_gts.php?id="+tramite,"imprimir");
}

// Función para el calculo del resumen 
function recalcula(){
	var no_partidas = parseInt($("#descrip_comprobacion_table>tbody >tr").length);
	//var anticipo_solicitado = parseFloat($("#anticipo_solicitado2").val().replace(/,/g,""));
	var anticipo_solicitado = parseFloat(0);
	var anticipo_comprobado = 0;
	var personal_anticipo = 0;
	var personal_amex = 0;
	var personal_amex_externo = 0;
	var comprobado_amex = 0;
	var personal_efectivo = 0;		
	var efectivo_comprobado = 0;		
	var amex_externo = 0;		
	var div = "";
	var tasaUSD = 0;
	var tasaEUR = 0;
	var totalComprobacion = 0;

	// Toma los valores de las divisas correspondientes.
	tasaUSD = parseFloat($("#valorDivisaUSD").val());		
	tasaEUR = parseFloat($("#valorDivisaEUR").val());
	
	for(var i = 1; i <= no_partidas; i++){
		// Suma el personal y el total por anticipo			
		div = $("#dc_divisa"+i).val();
		
		if ($("#tipo"+i).val() == "Anticipo"){											
			switch (div){
				case "MXN":
					anticipo_comprobado += parseFloat($("#total"+i).val().replace(/,/g,""));
					break;
				case "USD":						
					anticipo_comprobado += parseFloat($("#total"+i).val().replace(/,/g,"")) * tasaUSD;
					break;
				case "EUR":						
					anticipo_comprobado += parseFloat($("#total"+i).val().replace(/,/g,"")) * tasaEUR;
					break;
			}
			    
			if($("#concepto"+i).val() == "Personal"){
				switch (div){
					case "MXN":
						personal_anticipo += parseFloat($("#total"+i).val().replace(/,/g,""));
						break;
					case "USD":						
						personal_anticipo += parseFloat($("#total"+i).val().replace(/,/g,"")) * tasaUSD;
						break;
					case "EUR":						
						personal_anticipo += parseFloat($("#total"+i).val().replace(/,/g,"")) * tasaEUR;
						break;
				}
			}
		}
		
		// Suma el personal y el total por amex
		if($("#tipo"+i).val() == "AMEX"  || $("#tipo"+i).val() == "Amex"){
			switch (div){
				case "MXN":
					comprobado_amex += parseFloat($("#total"+i).val().replace(/,/g,""));
					break;
				case "USD":						
					comprobado_amex += parseFloat($("#total"+i).val().replace(/,/g,"")) * tasaUSD;
					break;
				case "EUR":						
					comprobado_amex += parseFloat($("#total"+i).val().replace(/,/g,"")) * tasaEUR;
					break;
			}
			   
			if($("#concepto"+i).val() == "Personal"){
				switch (div){
					case "MXN":
						personal_amex += parseFloat($("#total"+i).val().replace(/,/g,""));
						break;
					case "USD":						
						personal_amex += parseFloat($("#total"+i).val().replace(/,/g,"")) * tasaUSD;
						break;
					case "EUR":						
						personal_amex += parseFloat($("#total"+i).val().replace(/,/g,"")) * tasaEUR;
						break;
				}					
			}
		}
		
		// Suma el personal y el total por efectivo Reembolo para empleado
		if($("#tipo"+i).val() == "Reembolso" || $("#tipo"+i).val() == "Reembolso"){
			switch (div){
				case "MXN":
					efectivo_comprobado += parseFloat($("#total"+i).val().replace(/,/g,""));
					break;
				case "USD":						
					efectivo_comprobado += parseFloat($("#total"+i).val().replace(/,/g,"")) * tasaUSD;						
					break;
				case "EUR":						
					efectivo_comprobado += parseFloat($("#total"+i).val().replace(/,/g,"")) * tasaEUR;
					break;
			} 				
		}
		
		// Suma el personal y el total por efectivo Reembolo para empleado
		if($("#tipo"+i).val() == "Amex externo"){								
			switch (div){
			case "MXN":
				amex_externo += parseFloat($("#total"+i).val().replace(/,/g,""));
				break;
			case "USD":						
				amex_externo += parseFloat($("#total"+i).val().replace(/,/g,"")) * tasaUSD;					
				break;
			case "EUR":						
				amex_externo += parseFloat($("#total"+i).val().replace(/,/g,"")) * tasaEUR;
				break;
			}
			
			if($("#concepto"+i).val() == "Personal"){					
				switch (div){
					case "MXN":
						personal_amex_externo += parseFloat($("#total"+i).val().replace(/,/g,""));
						break;
					case "USD":						
						personal_amex_externo += parseFloat($("#total"+i).val().replace(/,/g,"")) * tasaUSD;
						break;
					case "EUR":						
						personal_amex_externo += parseFloat($("#total"+i).val().replace(/,/g,"")) * tasaEUR;
						break;
				}
			}    			
		}
			
		// Sumaremos el total en pesos de cada concepto comprobado segï¿½n la Divisa del concepto
		switch (div){
			case "MXN":
				totalComprobacion += parseFloat($("#total"+i).val().replace(/,/g,""));
				break;
			case "USD":						
				totalComprobacion += parseFloat($("#total"+i).val().replace(/,/g,"")) * tasaUSD;
				break;
			case "EUR":						
				totalComprobacion += parseFloat($("#total"+i).val().replace(/,/g,"")) * tasaEUR;
				break;
			}
	}
	
	var tipoEmpleado = compruebaTipoUsuario(no_partidas);
	
	$("#anticipo_comprobado_autorizado_BMW2").val(anticipo_comprobado);
	$("#anticipo_comprobado_autorizado_BMW1").html(number_format(anticipo_comprobado,2,".",",") + " MXN");		

	if(tipoEmpleado == 1){
		$("#personal_descontar2").val(personal_amex_externo);		
		$("#personal_descontar1").html(number_format(personal_amex_externo,2,".",",")+ " MXN");
	}else{
		$("#personal_descontar2").val(anticipo_solicitado - anticipo_comprobado + (personal_anticipo + personal_amex + personal_amex_externo));		
		$("#personal_descontar1").html(number_format(anticipo_solicitado - anticipo_comprobado + (personal_anticipo + personal_amex + personal_amex_externo),2,".",",")+ " MXN");
	}
	
	$("#amex_comprobado_autorizado_BMW2").val(comprobado_amex);
	$("#amex_comprobado_autorizado_BMW1").html(number_format(comprobado_amex,2,".",",") + " MXN");
	$("#efectivo_comprobado_autorizado_BMW2").val(efectivo_comprobado);
	$("#efectivo_comprobado_autorizado_BMW1").html(number_format(efectivo_comprobado,2,".",",") + " MXN");

	if(tipoEmpleado == 1){
		$("#monto_a_descontar2").val(personal_amex_externo);
		$("#monto_a_descontar1").html(number_format(personal_amex_externo,2,".",",") + " MXN");
	}else{
		$("#monto_a_descontar2").val(anticipo_solicitado - anticipo_comprobado + (personal_anticipo + personal_amex + personal_amex_externo));
		$("#monto_a_descontar1").html(number_format(anticipo_solicitado - anticipo_comprobado + (personal_anticipo + personal_amex + personal_amex_externo),2,".",",") + " MXN");
	}
	
	$("#monto_a_reembolsar2").val(efectivo_comprobado);
	$("#monto_a_reembolsar1").html(number_format(efectivo_comprobado,2,".",",") + " MXN");
	$("#amex_externo2").val(amex_externo);
	$("#amex_externo1").html(number_format(amex_externo,2,".",",") + " MXN");

	// Actualizar el monto de la comprobaciï¿½n
	$("#total_pesosDiv").html(number_format(totalComprobacion,2,".",","));
	$("#total_pesos").val(totalComprobacion);
}

// Guardar las tasas introducidas por Finanzas
function guardaTasas(tramite){
	var estatusJson = "";
	var tasaDollar = $("#tasaUSDeditable").val();
	var tasaEURO = $("#tasaEUReditable").val();
	
	$.ajax({
		type: "POST",
		url: "services/Ajax_comprobacion_gastos.php",
		data: "tasaUSD="+tasaDollar+"&tasaEUR="+tasaEURO+"&tramite="+tramite,		
		dataType: "html",
		async: false, 
		timeout: 10000,
		success: function(json){},
		complete: function(json){
			// Mostramos la referencia de los valores capturados
			$("#tasaDollar").html("<strong>$ " + tasaDollar + "</strong>");
			$("#tasaEuro").html("<strong>$ " + tasaEURO + "</strong>");
			
			// Asignamos el valor capturado para los calculos 
			$("#valorDivisaUSD").val(tasaDollar);
			$("#valorDivisaEUR").val(tasaEURO);
			// Recalculamos el Resumen
			recalcula();
			
			bloquearcamposPantalla(0);
		},
		error: function(x, t, m){
			if(t==="timeout") {
				guardarTasas(tramite);
			}
		}
	});
}

//Asignamos el valor de los montos recalculados a campos ocultos
function limpiarComas(id){
	var montosComa = 0;
	var ivasComa = 0;
	var propinasComa = 0;
	var hospedajeComa = 0;
	
	montosComa = $("#monto"+id).val().replace(/,/g,"");
	$("#dc_monto_aux"+id).val(montosComa);
	
	ivasComa = $("#iva"+id).val().replace(/,/g,"");
	$("#dc_iva_aux"+id).val(ivasComa);
	
	propinasComa = $("#propina"+id).val().replace(/,/g,"");
	$("#dc_propinas_aux"+id).val(propinasComa);

	hospedajeComa = $("#imphospedaje"+id).val().replace(/,/g,"");
	$("#dc_hospedajeImpuesto_aux"+id).val(hospedajeComa);
}

//Ocultar botón de Recalcular 
function ocultaRecalculo(id){
	// Habilitamos el botón
	$("#recalculo"+id).removeAttr('disabled');
	$("#recalculo"+id).attr("style", "");
	$("#recalculo"+id).attr("style", "width: 30px; height:30px; background:url(../../images/fwk/action_reorder.gif); background-position:center; background-repeat:no-repeat; border-style:none; cursor:pointer;");
	$("#recalculo"+id).css("display", "none");
	$("#recall"+id).css("display", "block");

	// Apagamos la bandera del botï¿½n del recalculo
	$('#recalculo_aux'+id).val(0);
}

function agregaRegistro(id, detalleComprobacion){
	var registros=parseInt($("#descrip_comprobacion_table>tbody >tr").length);
	
	// Variables que nos permitiran insertar los datos para el calculo	
	var montoOriginal=0;
	var idOriginal=0;
	var idNext=0;
	var diferenciaVirtual=0;
	var ivaOriginal=0;
	var propinaOriginal =0;
	var imphospedajeOriginal =0;
	var tramite = 0;
	var idAmexCargo = 0;
	
	var frm = document.comp_inv;
	id=parseInt(id);
	// Tomamos el valor que el usuario ingreso en los campos editables		
	dc_total=$("#total"+id).val();
	tramite = $("#idT").val();
	dc_id = $("#dc_id"+id).val();
	idAmexCargo = $("#idCargoAMEX"+id).val();
	
	// Cambiamos el formato de la cantidad total que hemos obtenido
	var compruebaTotal = dc_total.indexOf(",");
	if(compruebaTotal == -1){
		// La busqueda de aquel caracter fallo
		dc_total=parseFloat($("#total"+id).val());
	}else{
		dc_total= dc_total.replace(',','');
		dc_total=parseFloat(dc_total);
	}
	
	var monto=parseFloat(($("#monto"+id).val()).replace(/,/g,""));
	var monto_old =parseFloat($("#dc_monto"+id).val().replace(/,/g,"")); 
	monto=number_format(monto,2,".",",").replace(/,/g,"");
	monto_old=number_format(monto_old,2,".",",").replace(/,/g,"");
	
	if(monto != monto_old){
		if(monto == 0.00){
			monto = monto_old;
		}else{
			monto = monto;
		}
	}else{
		monto = 0.00;
	}
	
	var iva=parseFloat($("#iva"+id).val().replace(/,/g,""));
	var iva_old =parseFloat($("#dc_iva"+id).val().replace(/,/g,""));
	iva=number_format(iva,2,".",",").replace(/,/g,"");	
	iva_old=number_format(iva_old,2,".",",").replace(/,/g,"");

	if(iva != iva_old){
		if(iva == 0.00){
			iva = iva_old;
		}else{
			iva = iva;
		}
	}else{
		iva = 0.00;
	}
	
	
	var propina=parseFloat($("#propina"+id).val().replace(/,/g,""));
	var propina_old =parseFloat($("#dc_propinas"+id).val().replace(/,/g,"")); 
	propina=number_format(propina,2,".",",").replace(/,/g,"");	
	propina_old=number_format(propina_old,2,".",",").replace(/,/g,"");	
	
	
	if(propina != propina_old){
		if(propina == 0.00){
			propina = propina_old;
		}else{
			propina = propina;
		}
	}else{
		propina = 0.00;
	}
	
	var imphosp=parseFloat($("#imphospedaje"+id).val().replace(/,/g,""));
	var imphosp_old =parseFloat($("#dc_imphospedaje"+id).val().replace(/,/g,"")); 
	imphosp=number_format(imphosp,2,".",",").replace(/,/g,"");
	imphosp_old=number_format(imphosp_old,2,".",",").replace(/,/g,"");
	
	if(imphosp != imphosp_old){
		if(imphosp == 0.00){
			imphosp = imphosp_old;
		}else{
			imphosp = imphosp;
		}
	}else{
		imphosp = 0.00;
	}
	
	// Tomamos valores de la comprobacion en cuestion
	var dc_divisa=$("#dc_divisa"+id).val();
	var dc_tipo=$("#tipo"+id).val();
	var notransaccion=$("#notransaccion"+id).val();
	var dc_fecha=$("#dc_factura"+id).val();
	
	// Pasamos a entero el valor que fue ingresado y el total para poder saber si es menor o mayor
	monto = parseFloat(monto);
	monto_old = parseFloat(monto_old);
	
	iva = parseFloat(iva);
	iva_old = parseFloat(iva_old);
	
	propina = parseFloat(propina);
	propina_old = parseFloat(propina_old);
	
	imphosp = parseFloat(imphosp);
	imphosp_old = parseFloat(imphosp_old);

	if(monto > monto_old){
		alert("No puede ingresar un monto mayor al original.");
		restaurarMontosOriginales(id);
		ocultaRecalculo(id);
		$("#monto"+id).focus();
	}else if(iva > iva_old){
		alert("No puede ingresar un monto de IVA mayor al original.");
		restaurarMontosOriginales(id);
		ocultaRecalculo(id);
		$("#iva"+id).focus();
	}else if(propina > propina_old){
		alert("No puede ingresar un monto de Propina mayor al original.");
		restaurarMontosOriginales(id);
		ocultaRecalculo(id);
		$("#propina"+id).focus();
	}else if(imphosp > imphosp_old){
		alert("No puede ingresar un impuesto hospedaje mayor al original.");
		restaurarMontosOriginales(id);
		ocultaRecalculo(id);
		$("#imphospedaje"+id).focus();
	}else{
		var diferencia = 0.00;
		var diferencia_iva = 0.00;
		var diferencia_propina = 0.00;
		var diferencia_imphosp = 0.00;
		
		if(monto != 0.00){
			diferencia = monto_old - monto;
			diferencia=number_format(diferencia,2,".",",").replace(/,/g,"");
			
			if(diferencia == 0.00){
				diferencia = monto_old;
			}
		}else{
			diferencia = 0;
		}
		
		if(iva != 0.00){
			diferencia_iva = iva_old - iva;
			diferencia_iva=number_format(diferencia_iva,2,".",",").replace(/,/g,"");
			if(diferencia_iva == 0.00){
				diferencia_iva = iva_old;
			}
		}else{
			diferencia_iva = 0;
		}
		
		if(propina != 0.00){
			diferencia_propina = parseFloat(propina_old) - propina;
			if(diferencia_propina == 0.00){
				diferencia_propina = propina_old;
			}
		}else{
			diferencia_propina = 0;
		}
		
		if(imphosp != 0.00){
			diferencia_imphosp = parseFloat(imphosp_old) - imphosp;
			if(diferencia_imphosp == 0.00){
				diferencia_imphosp = imphosp_old;
			}
		}else{
			diferencia_imphosp = 0;
		}
		
		// Realizamos la diferencia del monto ingresado por finanzas y el monto original
		var sumaCalculada = parseFloat(diferencia)+parseFloat(diferencia_iva)+parseFloat(diferencia_propina)+parseFloat(diferencia_imphosp);
		var sumaCalculada2 = parseFloat(($("#monto"+id).val()).replace(/,/g,""))+parseFloat(($("#iva"+id).val()).replace(/,/g,""))+parseFloat(($("#propina"+id).val()).replace(/,/g,""))+parseFloat(($("#imphospedaje"+id).val()).replace(/,/g,""));
		
		// Tomamos los vaores para el calculo virtual
		montoOriginal=monto;
		ivaOriginal=iva;
		propinaOriginal=propina;
		imphospedajeOriginal=0;
		idOriginal=id;
		diferenciaVirtual=diferencia;
		
		$("#tota"+id).html(number_format(sumaCalculada2,2,".",","));
		$("#total"+id).val(number_format(sumaCalculada2,2,".",","));
		
		//realizamos la operacion para poder tener el total con los valores que fueron ingresados por elusuario
		//var totalCalculadoFinanzas=diferencia+parseFloat(iva)+parseFloat(propina)+parseFloat(imphospedaje);
		
		// Se validará el concepto que fue seleccionado
		if(($("#concepto_new"+id+" option:selected").val() == -1) || ($("#conceptoOld"+id).val() == $("#concepto_new_txt"+id).val()) || ($("#concepto_new_txt"+id).val() == "Personal")){
			var concepto = $("#concepto_old"+id).val();
			var conceptoTxt = "N/A";
		}else{
			var concepto = $("#concepto_new"+id+" option:selected").val();
			var conceptoTxt = $("#concepto_new_txt"+id).val();
		}
		// Se tomará el valor del concepto para realizar la actualizacion
		var conceptoOriginal=$("#conceptoOld"+id).val();

		// Se tomará el valor del concepto AMEX para el personal 
		var conceptoAMEXOriginal=$("#conceptoAMEX"+id).val();
		
		//Busca el ultimo ID de las partidas y le sumamos uno para el siguiente
		id = parseInt($("#descrip_comprobacion_table>tbody >tr").length);		
		//alert("numero de filas actuales"+id);
		
		if(isNaN(id)){
			id=1;
		}else{
			id+=parseInt(1);
		}
		
		var lastRow = parseInt($("#descrip_comprobacion_table").find("tr:last").find("td").eq(0).html()); 
	
		var nuevaFila="<tr>";
		nuevaFila+="<td align='center' valign='middle'>"+"<div id='renglonS"+id+"'>"+id+"</div>"+"<input type='hidden' name='row"+id+"' id='row"+id+"' value='"+id+"' readonly='readonly' /></td>";
		nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='dc_id"+id+"' id='dc_id"+id+"' value='"+dc_id+"' readonly='readonly' /><input type='hidden' name='tipo"+id+"'id='tipo"+id+"' value='"+dc_tipo+"'readonly='readonly' />"+dc_tipo+"</td>";
		nuevaFila+="<td align='center'><input type='hidden' name='notransaccion"+id+"'id='notransaccion"+id+"' value='"+notransaccion+"'readonly='readonly' />"+notransaccion+"</td>";
		nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='dc_fecha"+id+"'id='dc_fecha"+id+"' value='"+dc_fecha+"'readonly='readonly' />"+dc_fecha+"</td>";
		nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='conceptoOld"+id+"'id='conceptoOld"+id+"' value='Personal' readonly='readonly' /><input type='hidden' name='concepto"+id+"'id='concepto"+id+"' value='Personal' readonly='readonly' />"+"Personal"+"</td>";
		nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='conceptoAMEX"+id+"'id='conceptoAMEX"+id+"' value='" + conceptoAMEXOriginal + "' readonly='readonly' />" + conceptoAMEXOriginal + "</td>";
		nuevaFila+="<td align='center' valign='middle'><input type='text' name='comentario"+id+"' id='comentario"+id+"' style='border-color:#FFFFFF; text-align:right' value='' onmouseover='changedFields(this.id);' onblur='changedFieldsOutText(this.id);' /></td>";
		nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='asistentes"+id+"' id='asistentes"+id+"' value='N/A' readonly='readonly' />"+"N/A"+"</td>";
		nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='proveedor"+id+"' id='proveedor"+id+"' value='N/A' readonly='readonly' />"+"N/A"+"</td>";
		nuevaFila+="<td align='center'><input type='hidden' name='rfc"+id+"' id='rfc"+id+"' value='N/A' readonly='readonly' />"+"N/A"+"</td>";
		nuevaFila+="<td align='center'><input type='hidden' name='factura"+id+"' id='factura"+id+"' value='N/A' readonly='readonly' />"+"N/A"+"</td>";
		nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='idOrig"+id+"' id='idOrig"+id+"' value='"+idOriginal+"' readonly='readonly' /><input type='hidden' name='montoR"+id+"' id='montoR"+id+"' value='"+monto_old+"' readonly='readonly' /><input type='hidden' name='dc_monto_aux"+id+"' id='dc_monto_aux"+id+"' value='"+number_format(diferencia,2,".",",")+"' readonly='readonly' />"+number_format(diferencia,2,".",",")+"</td>";
		nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='ivaR"+id+"' id='ivaR"+id+"' value='"+iva_old+"' readonly='readonly' /><input type='hidden' name='dc_iva_aux"+id+"' id='dc_iva_aux"+id+"' value='"+number_format(diferencia_iva,2,".",",")+"' readonly='readonly' />"+number_format(diferencia_iva,2,".",",")+"</td>";
		nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='propinaR"+id+"' id='propinaR"+id+"' value='"+propina_old+"' readonly='readonly' /><input type='hidden' name='dc_propinas_aux"+id+"' id='dc_propinas_aux"+id+"' value='"+number_format(diferencia_propina,2,".",",")+"' readonly='readonly' />"+number_format(diferencia_propina,2,".",",")+"</td>";
		nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='impuhospedajeR"+id+"' id='impuhospedajeR"+id+"' value='"+imphosp_old+"' readonly='readonly' /><input type='hidden' name='dc_hospedajeImpuesto_aux"+id+"' id='dc_hospedajeImpuesto_aux"+id+"' value='"+number_format(diferencia_imphosp,2,".",",")+"' readonly='readonly' />"+number_format(diferencia_imphosp,2,".",",")+"</td>";
		nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='total"+id+"' id='total"+id+"' value='"+sumaCalculada+"' readonly='readonly' />"+number_format(sumaCalculada,2,".",",")+"</td>";
		nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='divisa"+id+"' id='divisa"+id+"' value='"+dc_divisa+"' readonly='readonly' /><input type='hidden' name='dc_divisa"+id+"' id='dc_divisa"+id+"' value='"+dc_divisa+"' readonly='readonly' />"+dc_divisa+"</td>";
		nuevaFila+="<td align='justify'><input type='hidden' name='excepcion"+id+"' id='excepcion"+id+"' value='Sin excepcion' readonly='readonly' />"+"Sin excepcion"+"</td>";
		nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='select_concepto"+id+"' id='select_concepto"+id+"' value='"+conceptoTxt+"' readonly='readonly' />"+conceptoTxt+"</td>";
		nuevaFila+="<input type='hidden' name='concepto_original"+id+"' id='concepto_original"+id+"' value='"+conceptoOriginal+"' readonly='readonly' /> <input type='hidden' name='dc_monto"+id+"' id='dc_monto"+id+"' value='"+number_format(diferencia,2,".",",")+"' readonly='readonly' /><input type='hidden' name='dc_iva"+id+"' id='dc_iva"+id+"' value='"+number_format(diferencia_iva,2,".",",")+"' readonly='readonly' /><input type='hidden' name='dc_propinas"+id+"' id='dc_propinas"+id+"' value='"+number_format(diferencia_propina,2,".",",")+"' readonly='readonly' /><input type='hidden' name='dc_imphospedaje"+id+"' id='dc_imphospedaje"+id+"' value='"+number_format(diferencia_imphosp,2,".",",")+"' readonly='readonly' />";
		nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='cinsertado"+id+"' id='cinsertado"+id+"' value='0' readonly='readonly' /><input type='hidden' name='detalleOrigen"+id+"' id='detalleOrigen"+id+"' value='"+detalleComprobacion+"' readonly='readonly' /><input type='hidden' value='N/A' name='"+id+"edit' id='"+id+"edit'  size=10  disabled='disabled' style='text-align:center; border-color:#FFFFFF; '/>N/A</td>"
		nuevaFila+="<td align='center' valign='middle'><input type='button' name='"+id+"del' id='"+id+"del' style='width: 30px; height:30px;  background:url(../../images/action_cancel.gif); background-position:center; background-repeat:no-repeat; border-style:none; cursor:pointer;'  onClick='eliminaConcepto(this.id, "+dc_id+");'/>";
		nuevaFila+="<input type='hidden' name='idNext"+id+"' id='idNext"+id+"' value='"+id+"' readonly='readonly' />";
		nuevaFila+="<input type='hidden' name='idCargoAMEX"+id+"' id='idCargoAMEX"+id+"' value='"+idAmexCargo+"' readonly='readonly' />";
		nuevaFila+="<input type='hidden' name='tramite"+id+"' id='tramite"+id+"' value='"+tramite+"' readonly='readonly' /></td>";
		nuevaFila+="</tr>";

		if(dc_total == sumaCalculada2){
			alert("Los montos no han sido modificados, para recalcular un concepto personal modifique un monto.");
			ocultaRecalculo(idOriginal);
			return false;				
		}else{
			$("#descrip_comprobacion_table").append(nuevaFila);			
			recalcula();
			// Guardamos el total de filas generadas
			$("#total_rows").val(id);
			// Asignamos los valores introducidos por el usuario para evitar que se guarden montos sin recalculo
			limpiarComas(idOriginal);
			
			// Apagamos la bandera del botï¿½n del recalculo
			$('#recalculo_aux'+idOriginal).val(0);
			
			// Desactivamos los boteones correspondientes
			$("#recalculo"+idOriginal).attr("disabled", "disabled");
			$("#recalculo"+idOriginal).attr("style", "width: 30px; height:30px; background:url(../../images/fwk/action_reorder.gif); background-position:center; background-repeat:no-repeat; border-style:none; cursor:default;");
			
			// Congelamos los campos para evitar que el usuario modifique los campos y estos sean guardados
			$("#monto"+idOriginal).attr('disabled', 'disabled');
			$("#iva"+idOriginal).attr('disabled', 'disabled');
			$("#propina"+idOriginal).attr('disabled', 'disabled');
			$("#imphospedaje"+idOriginal).attr('disabled', 'disabled');
		}
		//insertaTTemp(idOriginal,monto_old,diferenciaVirtual,iva_old,propina_old,imphospedajeOriginal,id,tramite);
	}
 }

//Función que nos permitira validar un monto ingresado
function validarMonto(id, detalleComprobacion, cargo){
	if(verificarTipoYConceptos("descrip_comprobacion_table", "Personal", 0, 1, detalleComprobacion)){
		alert("Para ingresar un nuevo concepto personal, favor de eliminar el concepto existente.");
		recalcula();
		return false;
	}else if(cargo == "Reembolso"){
		id = parseInt(id);
		if(verificaMontosIngresados("descrip_comprobacion_table", 1, id)){ // Parametros ( tabla , TipoComprobacion[1:Viaje, 0:Invitaciï¿½n])
			if(confirm("Las cantidades de este concepto han sido modificadas, no podrán volver a su valor original.\n ¿Esta seguro que desea guardar los cambios?")){
				actializacionMontos("descrip_comprobacion_table", 1, id);
				recalculoReembolso(id, 0, id);
				// Asignamos los valores de los campos para ser guardados en la BD
				limpiarComas(id);
				// Apagamos la bandera del botón del recalculo
				$('#recalculo_aux'+id).val(0);
			}else{
				restaurarMontosOriginales(id);
				ocultaRecalculo(id);
				recalcula();
				return false;
			}
		}else{
			restaurarMontosOriginales(id);
			ocultaRecalculo(id);
			recalcula();
			return false;
		}				
	}else{
		id = parseInt(id);
		var conceptoOriginal = $("#concepto_old"+id).val();
		
		if($("#concepto_new"+id+" option:selected").val() == 31){
			// Si es un concepto Personal, no se debe permitir un recalculo 
			alert("No puede recalcular un concepto Personal.");
			// Seleccionar el concepto anterior
			actualizarConcepto(conceptoOriginal, id, 0);
			$("#concepto_new"+id+" option[value="+conceptoOriginal+"]").attr("selected", true);
			restaurarMontosOriginales(id);
			ocultaRecalculo(id);
			recalcula();
			return false;
		}else{
			// Elinimar el concepto de Personal
			$("#concepto_new"+id+" option[value=31]").remove();
			agregaRegistro(id, detalleComprobacion);
		}
	}
} // Fin validar un monto ingresado para Comprobacion de Gastos.

function verificarBotones(){
	var partidasRecalculadas = parseInt($("#descrip_comprobacion_table>tbody >tr").length);
	var botonesEnccendidos = verificaBotonesEncendidos(partidasRecalculadas);
	
	if(botonesEnccendidos == 0){
		return true;
	}else{
		if(confirm("Tiene recálculos pendientes, dé click en 'Ok' para regresar a la pantalla y concluirlos o 'Cancel' para enviar la comprobación sin aplicarlos.")){
			return false;
		}else{
			return true;
		}
	}
}

// Función que validara el boton volver
function validarVolver(){
	if(confirm("Se perderán todos los cambios. ¿Desea salir de la página?.")){
		Location();
	}else{
		return false;
	}
}
