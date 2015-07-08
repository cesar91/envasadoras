//Habilitar botón de Autorizar y ocultar botón de Enviar al Supervisor de Finanzas
// CHIPOTLE'S SOLUTION
arrayAcentos = new Array();
arrayAcentos["a"] ="\u00e1";
arrayAcentos["e"] ="\u00e9";
arrayAcentos["i"] ="\u00ed";
arrayAcentos["o"] ="\u00f3";
arrayAcentos["u"] ="\u00fa";
arrayAcentos["n"] ="\u00f1";
arrayAcentos["Int"] ="\u00BF";

 
function activaAutorizar(){
//	$("#envia_supervisor").attr("style", "");
//	$("#envia_supervisor").attr("value", "    Autorizar");
//	$("#envia_supervisor").attr("id", "autorizar_comp_inv");
//	$("#envia_supervisor").attr("name", "autorizar_comp_inv");
	$("#envia_supervisor").css("display", "none");
	$("#autorizar_comp_inv").attr("style", "background:url(../../images/ok.png); background-position:left; background-repeat:no-repeat; background-color:#E1E4EC;");
	$("#devolver_observ").css("display", "none");	
}

function redondea(valor){
	return (Math.round(valor * Math.pow(10, 2)) / Math.pow(10, 2));
}

// Esta función revisa que todo lo que se escriba este en orden 
function revisaCadena(textItem){
	// Si comienza con un punto, le agregamos un cero
	if(textItem.value.substring(0,1) == '.') 
		textItem.value = '0' + textItem.value;

	// Si no cumples las reglas, no te dejo escribir
	if(!cumpleReglas(textItem.value))
		textItem.value = textoAnterior;
	else // Todo en orden
		textoAnterior = textItem.value;
} // End function revisaCadena

var textoAnterior = '';

//ESTA FUNCIÓN DEFINE LAS REGLAS DEL JUEGO
function cumpleReglas(simpleTexto){
	// La pasamos por una poderosa expresión regular
	// Var expresion = new RegExp("^(|([0-9]{1,2}(\\.([0-9]{1,2})?)?))$");
	var expresion = new RegExp("^(|([0-9]{1,30}(\\.([0-9]{1,2})?)?))$");

	// Si pasa la prueba, es válida
	if(expresion.test(simpleTexto))
		return true;
	return false;
} // End function checaReglas 

//==================================================================== Comprobacion de invitacion (RESUMEN) ================================================
// Función para obtener Anticipo comprobado autorizado por BMW
function Anticipo_comprobado_autorizado_X_BMW(id){
	//id = parseInt(id);
	var i =0;
	var no_conceptos = parseInt($("#descrip_comprobacion_table>tbody >tr").length);
	//alert(no_conceptos);
	var amex_comprobado_autorizado = 0;
	var personal_amex = 0;
	var amex_comprobado_autorizado_BMW = 0;
	for(i=1; i<=no_conceptos; i++){
		var div = $("#divisa"+i).val();
		//alert(div);
		if(id != i){
			if($("#tipo_comp"+i).val()=="Amex"){
				amex_comprobado_autorizado = amex_comprobado_autorizado + parseFloat($("#total"+i).val());
				
				switch (div){
					case "MXN":
						amex_comprobado_autorizado = amex_comprobado_autorizado;
						break;
					case "USD":
						var tasaUSD = parseFloat($("#tasaUSD").val());
						amex_comprobado_autorizado = amex_comprobado_autorizado * tasaUSD;
						break;
					case "EUR":
						var tasaEUR = parseFloat($("#tasaEUR").val());
						amex_comprobado_autorizado = amex_comprobado_autorizado * tasaEUR;
						break;
				}
			}

			if($("#tipo_comp"+i).val()=="Amex" && $("#concepto"+i).val() == 31){ // Se comparará con 31 debido a que es el ID que corresponde al Concepto de Personal
				personal_amex = personal_amex +  parseFloat($("#total"+i).val());
				
				switch (div){
				case "MXN":
					personal_amex = personal_amex;
					break;
				case "USD":
					var tasaUSD = parseFloat($("#tasaUSD").val());
					personal_amex = personal_amex * tasaUSD;
					break;
				case "EUR":
					var tasaEUR = parseFloat($("#tasaEUR").val());
					personal_amex = personal_amex * tasaEUR;
					break;
				}
			}
			
		}	
	}
	//alert(amex_comprobado_autorizado_BMW);
	amex_comprobado_autorizado_BMW = amex_comprobado_autorizado - personal_amex;
	$("#g_amex_comprobado").html(number_format(amex_comprobado_autorizado_BMW,2,".",",")+" MXN");
	$("#t_amex_comprobado").val(amex_comprobado_autorizado_BMW);
}

function personal_descontar_BMW(id){
	var frm = document.comp_inv;
	id = parseInt(id);
	//var frm=document.;
	var i =0;
	var no_conceptos = parseInt($("#descrip_comprobacion_table>tbody >tr").length);
	//alert("conceptos en C"+ no_conceptos);
	var anticipo_solicitado = $("#anticipo_solicitado2").val();
	//alert("anticipo"+anticipo_solicitado);
	var anticipo_comp_anticipo_per =0;
	
	for(i=1; i<=no_conceptos; i++){
		var div = $("#divisa"+i).val();
		if(id != i){
			if($("#concepto"+i).val() != 31){ // Se comparará con 31 debido a que es el ID que corresponde al Concepto de Personal
				anticipo_comp_anticipo_per = anticipo_comp_anticipo_per + parseFloat($("#total"+i).val());
				
				switch (div){
				case "MXN":
					anticipo_comp_anticipo_per = anticipo_comp_anticipo_per;
					break;
				case "USD":
					var tasaUSD = parseFloat($("#tasaUSD").val());
					anticipo_comp_anticipo_per = anticipo_comp_anticipo_per * tasaUSD;
					break;
				case "EUR":
					var tasaEUR = parseFloat($("#tasaEUR").val());
					anticipo_comp_anticipo_per = anticipo_comp_anticipo_per * tasaEUR;
					break;
				}
			}
			
		}	
	}
	//alert("A sol."+anticipo_comp_anticipo_per);
	//alert("Personal anticipo"+personal_anticipo);
	//alert("amex personal"+personal_amex);
	var total_personal_descontar = anticipo_solicitado - anticipo_comp_anticipo_per + personal_anticipo;
	//console.log(anticipo_solicitado +"-"+ anticipo_comp_anticipo_per +"+"+ personal_anticipo);
	$("#g_personal").html(number_format(total_personal_descontar,2,".",",")+" MXN");
	$("#t_personal").val(total_personal_descontar);
}

function amex_comprobado_autorizado_X_BMW(id){
	id = parseInt(id);
	var i =0;
	var no_conceptos = parseInt($("#descrip_comprobacion_table>tbody >tr").length);
	var amex_comprobado_autorizado =0;
	personal_amex =0;
	var amex_comprobado_autorizado_BMW=0;
	for(i=1; i<=no_conceptos; i++){
		if(id != i){
			if($("#tipo_comp"+i).val()=="Amex"){
				amex_comprobado_autorizado = amex_comprobado_autorizado + parseFloat($("#total"+i).val());
			}

			if($("#tipo_comp"+i).val()=="Amex" && $("#concepto"+i).val() == 31){ // Se comparará con 31 debido a que es el ID que corresponde al Concepto de Personal
				personal_amex = personal_amex +  parseFloat($("#total"+i).val());
			}
			
		}	
	}
	
	amex_comprobado_autorizado_BMW = amex_comprobado_autorizado - personal_amex;
	$("#g_amex_comprobado").html(number_format(amex_comprobado_autorizado_BMW,2,".",",")+" MXN");
	$("#t_amex_comprobado").val(amex_comprobado_autorizado_BMW);
}

function efectivo_comprobado_autorizado_X_BMW(id){
	id = parseInt(id);
	var i =0;
	var no_conceptos = parseInt($("#descrip_comprobacion_table>tbody >tr").length);
	//alert(no_conceptos);
	var efectivo_comprobado_autorizado =0;
	var personal_efectivo =0;
	var efectivo_comprobado_autorizado_BMW=0;
	for(i=1; i<=no_conceptos; i++){
		var div = $("#divisa"+i).val();
		if(id != i){
			if($("#tipo_comp"+i).val() == "Reembolso"){
				efectivo_comprobado_autorizado = efectivo_comprobado_autorizado + parseFloat($("#total"+i).val());
				
				switch (div){
				case "MXN":
					efectivo_comprobado_autorizado = efectivo_comprobado_autorizado;
					break;
				case "USD":
					var tasaUSD = parseFloat($("#tasaUSD").val());
					efectivo_comprobado_autorizado = efectivo_comprobado_autorizado * tasaUSD;
					break;
				case "EUR":
					var tasaEUR = parseFloat($("#tasaEUR").val());
					efectivo_comprobado_autorizado = efectivo_comprobado_autorizado * tasaEUR;
					break;
				}
			}
			if($("#tipo_comp"+i).val() == "Reembolso" && $("#concepto"+i).val() == 31){ // Se comparará con 31 debido a que es el ID que corresponde al Concepto de Personal
				personal_efectivo = personal_efectivo + parseFloat($("#total"+i).val());
				
				switch (div){
				case "MXN":
					personal_efectivo = personal_efectivo;
					break;
				case "USD":
					var tasaUSD = parseFloat($("#tasaUSD").val());
					personal_efectivo = personal_efectivo * tasaUSD;
					break;
				case "EUR":
					var tasaEUR = parseFloat($("#tasaEUR").val());
					personal_efectivo = personal_efectivo * tasaEUR;
					break;
				}
			}
		}	
	}
	efectivo_comprobado_autorizado_BMW=efectivo_comprobado_autorizado - personal_efectivo;
	$("#g_efectivo_comprobado").html(number_format(efectivo_comprobado_autorizado_BMW,2,".",",")+" MXN");
	$("#t_efectivo_comprobado").val(efectivo_comprobado_autorizado_BMW);
}

function monto_a_descontar(){
	$("#g_descuento").html($("#g_personal").html());
	$("#t_descuento").val($("#t_personal").val());
}

function monto_a_reembolsar(){
	$("#g_reembolso").html($("#g_efectivo_comprobado").html());
	$("#t_reembolso").val($("#t_efectivo_comprobado").val());
}

function limpiaComas(id){
	var montosComa = $("#monto"+id).val().replace(/,/g,"");
	$("#dc_monto"+id).val(montosComa);
	
	var ivasComa = $("#iva"+id).val().replace(/,/g,"");
	$("#dc_iva"+id).val(ivasComa);
	
	var propinasComa = $("#propina"+id).val().replace(/,/g,"");
	$("#dc_propinas"+id).val(propinasComa);
	
}
//================================================================ END Comprobacion de invitacion (RESUMEN) ======================================


// Función que permitira activar el botón recalculo
function activarRecalculo(nombre,id,comprobacion){
	$("#recalculo"+id).css("display", "block");
	$("#recall"+id).css("display", "none");	
	// Comprobacion Viaje: comprobacion = 1  ---- Comprobacion Inivtación: comprobacion = 0
	if(comprobacion == 1){
		$('#recalculo_aux'+id).val(1);
	}
} // End función que permitira activar el botón recalculo

// Funcion que permite el recalculo del total ... Comprobacion invitacion
function recalcularTotal(){		
		//se realiza la suma de las filas que se hayan ingresado
		var id = parseInt($("#descrip_comprobacion_table>tbody >tr").length);
		//alert("longitud para el recalculo: "+id);

		var totalComprobado=0;
		var i=0;
		
		for(i=1;i<=id;i++){
			//alert($("#total"+i).val());
			var TotalCalculado=$("#total"+i).val();
			TotalCalculado=TotalCalculado.indexOf(",");
			if(TotalCalculado == -1){
				var TotalCalculado=parseFloat($("#total"+i).val());
			}else{
				var TotalCalculado=$("#total"+i).val().replace(',','');			
			}
			totalComprobado=totalComprobado+parseFloat(TotalCalculado);					
			//alert("TOTAL CALCULADO"+totalComprobado);				 
			}	
		//alert("total a calcular"+totalComprobado);
		$("#total_pesos").html(number_format(totalComprobado,2,".",","));
		$("#total_rows").val(id);
	
		//recalculo del resumen
		//pendiente
		Anticipo_comprobado_autorizado_X_BMW(0);
		personal_descontar_BMW(0);
		//amex_comprobado_autorizado_X_BMW(0);
		efectivo_comprobado_autorizado_X_BMW(0);
		monto_a_descontar();
		monto_a_reembolsar();
	
} // End funcion que permite el recalculo del total



//======================================COMPROBACION DE INVITACIONES(Finanzas) =========================

//Función que nos permitira validar un monto ingresado
function validarMonto(id, detalleComprobacion){
	if(verificarTipoYConceptos("descrip_comprobacion_table", 31, 0, 0,0)){
		alert("Para ingresar un nuevo concepto personal, favor de eliminar el concepto existente.");
		return false;
	}else if(verificarTipoYConceptos("descrip_comprobacion_table", "Reembolso", 1, 0, 0)){
		id = parseInt(id);
		if(verificaMontosIngresados("descrip_comprobacion_table", 0, id)){ // Parametros ( tabla , TipoComprobacion[1:Viaje, 0:Invitación])
			if(confirm("Las cantidades de este concepto han sido modificadas, no podrán volver a su valor original.\n ¿Esta seguro que desea guardar los cambios?")){
				actializacionMontos("descrip_comprobacion_table", 0, id);
				recalculoReembolso(id, 0);
				
				var montoAprobado = $('#dc_monto'+id).val();
				var ivaAprobado = $('#dc_iva'+id).val();				
				var propinaAprobada = $('#dc_propinas'+id).val();
				var totalAprobado = $('#total'+id).val();
				var conceptoReasignado = $('#concepto'+id).val();
				
				var url = "services/Ajax_comprobacion.php";
				var valor = true;
				$.ajaxSetup({async:false});				
				$.post(url,{monto:montoAprobado, iva:ivaAprobado, propina:propinaAprobada, total:totalAprobado, concepto:conceptoReasignado, detalle:detalleComprobacion, comprobacion:0 },function(data){
					//console.log(data);
					valor = (data == 0) ? false : true;					
				});
			}else{
				restaurarMontos("descrip_comprobacion_table", 0, id);
				return false;
			}
		}else{			
			return false;
		}				
	}else{
		//checara si se elimina los registros de la tabla temporal para el calculo virtual
		//=================================================
		var registros=parseInt($("#descrip_comprobacion_table>tbody >tr").length);
		//alert("TOTAL DE REGISTROS ACTUALES: "+registros);
		/*if(registros == registrosOriginales ){
			borrarMonto();
		}*/
		//=================================================
		
		//variables que nos permitiran insertar los datos para el calculo
		//=========================================================
		var montoOriginal=0;
		var idOriginal=0;
		var idNext=0;
		var diferenciaVirtual=0;
		var ivaOriginal=0;
		var propinaOriginal =0;
		var imphospedajeOriginal =0;
		var tramite = 0;
		//==========================================================
		
		var frm = document.comp_inv;
		id=parseFloat(id);
		// ValoresOriginales(id);	
		
		// Tomamos el valor que el usuario ingreso en los campos editables		
		var dc_total = $("#total"+id).val().replace(/,/g,"");
		var tramite = $("#idT").val();
		var dc_id = $("#dc_id"+id).val();
		var fila_original = $("#fila_original"+id).val();
		
		// Cambiamos el formato de la cantidad total que hemos obtenido
		var compruebaTotal = dc_total.indexOf(",");
		if(compruebaTotal == -1){
			// La busqueda de aquel caracter fallo
			//alert("No se transformara");
			dc_total=parseFloat($("#total"+id).val());
		}else{
			//alert("Se transformara la cifra.");
			dc_total= dc_total.replace(',','');
			dc_total=parseFloat(dc_total);
		}
		
		//alert("TOTAL: "+dc_total);	
		var monto=$("#monto"+id).val().replace(/,/g,"");	
		var monto_old = $("#dc_monto"+id).val().replace(/,/g,"");
		//alert("Agrega fila: "+monto);
		
		if(monto != monto_old){
			if(monto == 0.00){
				monto = monto_old;
			}else{
				monto = monto;
			}
		}else{
			monto = 0.00;
		}		
		//alert("MONTO: "+monto);
		
		var iva=$("#iva"+id).val().replace(/,/g,"");
		var iva_old = $("#dc_iva"+id).val().replace(/,/g,"");
		
		if(iva != iva_old){
			if(iva == 0.00){
				iva = iva_old;
			}else{
				iva = iva;
			}
		}else{
			iva = 0.00;
		}
		//alert("IVA: "+iva);
		
		var propina=$("#propina"+id).val().replace(/,/g,"");
		var propina_old = $("#dc_propinas"+id).val().replace(/,/g,"");
		
		if(propina != propina_old){
			if(propina == 0.00){
				propina = propina_old;
			}else{
				propina = propina;
			}
		}else{
			propina = 0.00;
		}
		//alert("PROPINA: "+propina);
//		var imphospedaje=$("#imphospedaje"+id).val();
//		alert("IMPUESTO: "+imphospedaje);
		
		// Tomamos valores de la comprobacion en cuestion
		var dc_divisa=$("#divisa"+id).val();
//		var ex_mensaje=$("#ex_mensaje"+id).val();
		var dc_tipo=$("#tipo_comp"+id).val();
//		var notransaccion=$("#notransaccion"+id).val();
		var dc_fecha=$("#f_factura"+id).val();
		
		// Pasamos a entero el valor que fue ingresado y el total para poder saber si es menor o mayor
		//alert("montito"+monto);
		monto = parseFloat(monto);
		monto_old = parseFloat(monto_old);
		
		iva = parseFloat(iva);
		iva_old = parseFloat(iva_old);
		
		propina = parseFloat(propina);
		propina_old = parseFloat(propina_old);
		
		if(monto > monto_old){
			alert("No puede ingresar un monto mayor al original.");
			$("#monto"+id).focus();
		}else if(iva > iva_old){
			alert("No puede ingresar un monto de IVA mayor al original.");
			$("#iva"+id).focus();
		}else if(propina > propina_old){
			alert("No puede ingresar un monto de Propina mayor al original.");
			$("#propina"+id).focus();
		}else{
			var diferencia = 0.00;
			var diferencia_iva = 0.00;
			var diferencia_propina = 0.00;
			
			if(monto != 0.00){
				diferencia = parseFloat(monto_old) - number_format(redondea(monto),2,".","");
				if(diferencia == 0.00){
					diferencia = monto_old;
				}
			}else{
				diferencia = 0;
			}
			
			if(iva != 0.00){
				diferencia_iva = parseFloat(iva_old) - number_format(redondea(iva),2,".","");
				if(diferencia_iva == 0.00){
					diferencia_iva = iva_old;
				}
			}else{
				diferencia_iva = 0;
			}
			
			if(propina != 0.00){
				diferencia_propina = parseFloat(propina_old) - number_format(redondea(propina),2,".","");
				if(diferencia_propina == 0.00){
					diferencia_propina = propina_old;
				}
			}else{
				diferencia_propina = 0;
			}
			//realizamos la diferencia del monto ingresado por finanzas y el monto original
			var sumaCalculada = parseFloat(diferencia)+parseFloat(diferencia_iva)+parseFloat(diferencia_propina);
			var sumaCalculada2 = parseFloat($("#monto"+id).val().replace(/,/g,"")) + parseFloat($("#iva"+id).val().replace(/,/g,"")) + parseFloat($("#propina"+id).val().replace(/,/g,""));
			//alert("diferencia XD"+diferencia);
			
			//tomamos los vaores para el calculo virtual
			//================================
			montoOriginal=monto;
			ivaOriginal=iva;
			propinaOriginal=propina;
			imphospedajeOriginal=0;
			idOriginal=id;
			diferenciaVirtual=diferencia;
			//================================
			
			
			$("#tota"+id).html(number_format(sumaCalculada2,2,".",","));
			$("#total"+id).val(number_format(sumaCalculada2,2,".",""));
			
			//realizamos la operacion para poder tener el total con los valores que fueron ingresados por elusuario
			//var totalCalculadoFinanzas=diferencia+parseFloat(iva)+parseFloat(propina)+parseFloat(imphospedaje);
			
			//se validara el concepto que fue seleccionado
			if($("#concepto_new"+id+" option:selected").val() == -1){
				var concepto = $("#concepto_old"+id).val();
				var conceptoTxt = "N/A";
			}else{
				var concepto = $("#concepto_new"+id+" option:selected").val();
				var conceptoTxt = $("#concepto_new_txt"+id).val();
			}		
			
			//Busca el ultimo ID de las partidas y le sumamos uno para el siguiente
			id = parseInt($("#descrip_comprobacion_table>tbody>tr").length);		
			//alert("numero de filas actuales"+id);
			if(isNaN(id)){
				
				id=1;
			}else{
				//alert("suma");
				id+=parseInt(1);
			}
			//frm.rowCount.value=parseInt(id);
			 var lastRow = parseInt($("#descrip_comprobacion_table").find("tr:last").find("td").eq(0).html()); 
			 var nuevaFila="<tr>";
				nuevaFila+="<td align='center' valign='middle'><div id='renglonS" + id + "'>" + id + "</div><input type='hidden' name='rows" + id + "' id='rows" + id + "' value='" + id + "' readonly='readonly' /><input type='hidden' id='fila_original" + id + "' name='fila_original" + id + "' value='" + fila_original + "' /></td>";
		        nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='dc_id" + id + "' id='dc_id" + id + "' value='" + dc_id + "' readonly='readonly' /><input type='hidden' name='tipo" + id + "' id='tipo" + id + "' value='" + dc_tipo + "' readonly='readonly' />" + dc_tipo + "</td>";
				nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='dc_fecha" + id + "' id='dc_fecha" + id + "' value='" + dc_fecha + "' readonly='readonly' />" + dc_fecha + "</td>";
				nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='concepto" + id + "' id='concepto" + id + "' value='31' readonly='readonly' />Personal</td>";
				nuevaFila+="<td align='center' valign='middle'><input type='text' name='comentario" + id + "' id='comentario" + id + "' style='border-color: #FFFFFF; text-align: left; font-size:11px;' onmouseover='changedFields(this.id);' onblur='changedFieldsOutText(this.id);' /></td>";
				nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='asistentes" + id + "' id='asistentes" + id + "' value='0' readonly='readonly' />N/A</td>";
				nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='proveedor" + id + "' id='proveedor" + id + "' value='0' readonly='readonly' />N/A</td>";
				nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='rfc" + id + "' id='rfc" + id + "' value='N/A' readonly='readonly' />" + "N/A" + "</td>";
				nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='dc_monto" + id + "' id='dc_monto" + id + "' value='" + diferencia + "' readonly='readonly' />" + number_format(diferencia, 2, '.', ',') + "</td>";
				nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='divisa" + id + "' id='divisa" + id + "' value='" + dc_divisa + "' readonly='readonly' />" + dc_divisa + "</td>";
				nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='dc_iva" + id + "' id='dc_iva" + id + "' value='" + diferencia_iva + "' readonly='readonly' />" + number_format(diferencia_iva, 2, '.', ',') + "</td>";
				nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='dc_propinas" + id + "' id='dc_propinas" + id + "' value='" + diferencia_propina + "' readonly='readonly' />" + number_format(diferencia_propina, 2, '.', ',') + "</td>";
				nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='total" + id + "' id='total" + id + "' value='" + sumaCalculada + "' readonly='readonly' />" + number_format(sumaCalculada, 2, '.', ',') + "</td>";
				nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='select_concepto" + id + "' id='select_concepto" + id + "' value='N/A' readonly='readonly' />N/A</td>";
				nuevaFila+="<td align='center' valign='middle'><input type='hidden' name='" + id + "edit' id='" + id + "edit' value='N/A' size='10' disabled='disabled' style='text-align:center; border-color:#FFFFFF;' />N/A</td>";
				nuevaFila+="<td align='center' valign='middle'><input type='button' name='" + id + "del' id='" + id + "del' style='width:30px; height:30px; background:url(../../images/action_cancel.gif); background-position:center; background-repeat:no-repeat; border-style:none; cursor:pointer;' onClick='eliminaConceptoSI(this.id);' /></td>";
				nuevaFila+="</tr>";

			if(dc_total == sumaCalculada2){
				alert("Los montos no han sido modificados, para recalcular un concepto personal modifique un monto.");
			}else{
				$("#descrip_comprobacion_table").append(nuevaFila);
				Anticipo_comprobado_autorizado_X_BMW(0);
				personal_descontar_BMW(0);
				efectivo_comprobado_autorizado_X_BMW(0);
				monto_a_descontar();
				monto_a_reembolsar();
				//recalcula();
				limpiaComas(idOriginal);
				
				$("#total_rows").val(id);
				// Desactivar el botón de recalculo
				$("#recalculo"+idOriginal).attr("disabled", "disabled");
				$("#recalculo"+idOriginal).attr("style", "width: 30px; height:30px; background:url(../../images/fwk/action_reorder.gif); background-position:center; background-repeat:no-repeat; border-style:none; cursor:default;");
			}
		}
	}
	
} // End función que nos permitira validar un monto ingresado Comprobacion de invitaciones

// Función que permitirá realizar la eliminación del concepto registrado
function eliminaConceptoSI(id){
	var tramite = 0;
	tramite = $("#idT").val();
	var t_etapa = $("#t_etapa_actual").val();
	
	if(confirm("¿Esta seguro de que desea eliminar el registro? Se recalculará al monto original.")){
		if(t_etapa == 8){
			var idOriginal = $("#rows"+parseInt(id)).val() - 1;
			
			if(isNaN(idOriginal)){
				idOriginal = 1;
			}
			
			var fila_origen = $("#fila_original"+idOriginal).val();
			var totalRows = parseInt($("#total_rows").val()) - 1;
			
			$.ajax({
				type: "POST",
				url: "services/Ajax_comprobacion.php",
				data: "id_next="+idOriginal+"&tramite="+tramite+"&comp=0", 
				dataType: "json",				
				success: function(json){
					var monto_old = $("#monto_old"+fila_origen).val(json[0].monto);
					var iva_old = $("#iva_old"+fila_origen).val(json[0].iva);
					var propina_old = $("#propina_old"+fila_origen).val(json[0].propinas);
					var sumaTotal = parseFloat(json[0].monto) + parseFloat(json[0].iva) + parseFloat(json[0].propinas);
					
					// Se le asigna el valor al monto original
					$("#monto"+fila_origen).val(number_format(json[0].monto,2,".",","));
					// Se le asigna el valor al iva original
					$("#iva"+fila_origen).val(number_format(json[0].iva,2,".",","));
					// Se le asigna el valor a la propina original
					$("#propina"+fila_origen).val(number_format(json[0].propinas,2,".",","));
					// Se le asigna el valor a el total
					$("#total"+fila_origen).val(number_format(json[0].total,2,".",","));
					$("#tota"+fila_origen).html(number_format(json[0].total,2,".",","));
					
					// Habilitamos el botón
					$("#recalculo"+fila_origen).removeAttr('disabled');
					$("#recalculo"+fila_origen).attr("style", "");
					$("#recalculo"+fila_origen).attr("style", "width: 30px; height:30px; background:url(../../images/fwk/action_reorder.gif); background-position:center; background-repeat:no-repeat; border-style:none; cursor:pointer;");
					$("#recalculo"+fila_origen).css("display", "none");
					
					// Mostramos el input de Texto
					$("#recall"+fila_origen).css("display", "block");
					$("#dc_monto"+fila_origen).val(json[0].monto);
					$("#dc_iva"+fila_origen).val(json[0].iva);
					$("#dc_propinas"+fila_origen).val(json[0].propinas);
					//console.log("Valor de json.total: "+json[0].total)
					//console.log("Valor del campo total: "+$("#total"+fila_origen).val());
					$("#total"+fila_origen).val(number_format(json[0].total,2,".",",").replace(/,/g,""));
					
					borrarRenglon4(id,"descrip_comprobacion_table","rowCount","rowDel","renglonS","edit","del","");
					
					Anticipo_comprobado_autorizado_X_BMW(0);
					personal_descontar_BMW(0);
					efectivo_comprobado_autorizado_X_BMW(0);
					monto_a_descontar();
					monto_a_reembolsar();
					$("#total_rows").val(totalRows);
					//recalcula();
					limpiaComas(idOriginal);
				},			
		        error: function(x, t, m) {
					if(t==="timeout") {
						eliminaConceptoSI(id);
					}
				}
			});
			
		}else{
			var idOriginal = $("#rows"+parseInt(id)).val() - 1;
			
			var fila_origen = $("#fila_original"+idOriginal).val();
			var monto_old = $("#monto_old"+fila_origen).val();
			var iva_old = $("#iva_old"+fila_origen).val();
			var propina_old = $("#propina_old"+fila_origen).val();
			var totalRows = parseInt($("#total_rows").val()) - 1;
			
			var sumaCompleta = parseFloat(monto_old) + parseFloat(iva_old) + parseFloat(propina_old);
			
			// Se le asigna el valor al monto original
			$("#monto"+fila_origen).val(number_format(monto_old,2,".",","));
			// Se le asigna el valor al iva original
			$("#iva"+fila_origen).val(number_format(iva_old,2,".",","));
			// Se le asigna el valor a la propina original
			$("#propina"+fila_origen).val(number_format(propina_old,2,".",","));
			// Se le asigna el valor a el total
			$("#total"+fila_origen).val(number_format(sumaCompleta,2,".",""));
			$("#tota"+fila_origen).html(number_format(sumaCompleta,2,".",","));
			
			// Habilitamos el botón
			$("#recalculo"+fila_origen).removeAttr('disabled');
			$("#recalculo"+fila_origen).attr("style", "");
			$("#recalculo"+fila_origen).attr("style", "width: 30px; height:30px; background:url(../../images/fwk/action_reorder.gif); background-position:center; background-repeat:no-repeat; border-style:none; cursor:pointer;");
			$("#recalculo"+fila_origen).css("display", "none");
			
			// Mostramos el input de Texto
			$("#recall"+fila_origen).css("display", "block");
			//se reinicia el valor de el campo del monto
			
			$("#dc_monto"+fila_origen).val(number_format(monto_old,2,".",","));
			$("#dc_iva"+fila_origen).val(number_format(iva_old,2,".",","));
			$("#dc_propinas"+fila_origen).val(number_format(propina_old,2,".",","));
			$("#total"+fila_origen).val(number_format(sumaCompleta,2,".",",").replace(/,/g,""));
			
			borrarRenglon4(id,"descrip_comprobacion_table","rowCount","rowDel","renglonS","edit","del","");
			Anticipo_comprobado_autorizado_X_BMW(0);
			personal_descontar_BMW(0);
			efectivo_comprobado_autorizado_X_BMW(0);
			monto_a_descontar();
			monto_a_reembolsar();
			$("#total_rows").val(totalRows);
			//recalcula();
		}
	}else{
		return false;
	}
}

// Validar que el campo de Observaciones no este vacío
function validaObservaciones(){
	if($("#observ_up").val() == ""){
		alert("Para devolver al empleado, ingrese sus observaciones.");
		$("#observ_up").focus();
		return false;
	}else{
		$("#devolver_observ").fadeOut("slow");
		$("#autorizar_comp_inv").fadeOut("slow");
		$("#envia_supervisor").fadeOut("slow");
		$("#Volver").fadeOut("slow");
		$("#rechazar_comp_inv").fadeOut("slow");
		return true;
	}
}

function sumaTotalConceptos(tipoConcepto){
	var no_partidas = parseInt($("#comprobacion_table>tbody >tr").length);
	var sumaTotalConcepto = 0;
	
	for(var i = 1; i<=no_partidas; i++){
		if(($('#concepto'+i).val() == tipoConcepto)){
			sumaTotalConcepto += parseFloat($('#totalMxn'+i).val().replace(/,/g,""));
		}
	}	
	return sumaTotalConcepto;
}

function ExcepcionViaje(tramiteId,nombreConcepto,contador,IdConcepto){
	var sumaTotalConcepto = 0;
	var totalMXNConcepto = 0;
	var TotalMXNConceptoViaje = 0;
	var montoOriginal = 0;
	var montoPermitido = 0;
	var divisa = 0;
	var diasViaje = 0;
	var diasViajeHotel = 0;
	
	
	$.ajax({
        type: "POST",
        url: "services/Ajax_comprobacion.php",
        data: "tramiteComentario="+tramiteId+"&conceptoIdComentario="+IdConcepto,
        dataType: "json",
        async : false,
        timeout: 10000,
        success: function(json){
        	montoPermitido = json[0].pr_cantidad;
            divisa = json[0].div_id;
            diasViaje = json[0].dias_viaje;
            
            totalMXNConcepto=calculoDivisa(montoPermitido,divisa);                
            
            sumaTotalConcepto = sumaTotalConceptos(nombreConcepto);            
            if(nombreConcepto == "Alimentos"){
            	TotalMXNConceptoViaje = totalMXNConcepto * diasViaje;
            	montoOriginal = montoPermitido * diasViaje;
            }else if(nombreConcepto == "Hotel"){
    			if(diasViaje == 1){
    				diasViajeHotel = 1;
    			}else{
    				diasViajeHotel = (diasViaje - 1);
    			}    			
    			TotalMXNConceptoViaje = totalMXNConcepto * diasViajeHotel;
    			montoOriginal = montoPermitido * diasViajeHotel;
    		}else if(nombreConcepto == "Lavandería"){    			
    			TotalMXNConceptoViaje = totalMXNConcepto * (diasViaje - 6);
    			montoOriginal = montoPermitido * (diasViaje - 6);    			
    		}
    		
    		if(sumaTotalConcepto > TotalMXNConceptoViaje){    			
    			alert(mensajeExcepcion(nombreConcepto,montoOriginal,divisa));
    			//insertamos la excepcion
    			var diferenciaExcepcion = parseFloat(sumaTotalConcepto) - parseFloat(TotalMXNConceptoViaje);		
    			insertaMensajeExc(nombreConcepto,mensajeExcepcion(nombreConcepto,montoOriginal,divisa),diferenciaExcepcion);
    		}
        },
        complete: function(json){	    			
			$("#validacionRealizada").val(contador);
		},
		error: function(x, t, m) {
			if(t==="timeout") {
				location.reload();
					 abort();
			}
		}
	});
}

function mensajeExcepcion(nombreConcepto,montoOriginal,divisa){
	var mensaje = "";
	if(nombreConcepto == "Alimentos"){
		mensaje ="Se excede el monto máximo permitido del viaje para el concepto de tipo Alimento que es de:"+" "+" "+number_format((montoOriginal),2,".",",")+" "+nombreDivisaTotal(divisa)+".";
	}else if(nombreConcepto == "Hotel"){
		mensaje ="Se excede el monto máximo permitido del viaje para el concepto de tipo Hospedaje que es de:"+" "+" "+number_format((montoOriginal),2,".",",")+" "+nombreDivisaTotal(divisa)+".";
	}else if(nombreConcepto == "Lavandería" ){
		mensaje ="Se excede el monto máximo permitido del viaje para el concepto de tipo Lavandería que es de:"+" "+" "+number_format((montoOriginal),2,".",",")+" "+nombreDivisaTotal(divisa)+".";
	}
	
	return mensaje;
}

function insertaMensajeExc(nombreConcepto,mensaje,diferenciaExcepcion){
	if(nombreConcepto == "Alimentos"){
		$("#mensaje_excepcion_ViajeA").val(mensaje);
		$("#dif_ViajeA").val(diferenciaExcepcion);
	}else if(nombreConcepto == "Hotel"){
		$("#mensaje_excepcion_ViajeH").val(mensaje);
		$("#dif_ViajeH").val(diferenciaExcepcion);
	}else if(nombreConcepto == "Lavandería" ){
		$("#mensaje_excepcion_ViajeL").val(mensaje);
		$("#dif_ViajeL").val(diferenciaExcepcion);
	}
}


function calculoDivisa(total,divisa){
	var totalMXN = 0;	
	//Valores de tasas
	var valorDivisaEUR = $('#valorDivisaEUR').val();
	var valorDivisaUSA = $('#valorDivisaUSD').val();
	var valorDivisaMNX = $('#valorDivisaMEX').val();
	
	if(divisa == 1){
		totalMXN = parseFloat(total) * valorDivisaMNX;		
	}else if(divisa == 2){
		totalMXN = parseFloat(total) * valorDivisaUSA;		
	}else if(divisa == 3){
		totalMXN = parseFloat(total) * valorDivisaEUR;		
	}	
	return totalMXN;
}

function nombreDivisaTotal(divisa){
	var nombreDivisa="";
	if(divisa == 1){
		nombreDivisa="MXN";	
	}else if(divisa == 2){
		nombreDivisa="USD";			
	}else if(divisa == 3){
		nombreDivisa="EUR";			
	}
	return nombreDivisa;
}

function recalculoReembolso(id, comprobacion){
	var difMonto = 0;
	var sumaTotal = 0;
	var impuestoHospedaje = 0;	
	
	// Obtenemos la suma total de (Monto,iva, propina, imp.hospedaje)
	if(comprobacion == 1){
		impuestoHospedaje = parseFloat($('#imphospedaje'+id).val().replace(/,/g,""));
	}		
	//$('#'+nombreCampo+id).val(difMonto);
	sumaTotal = parseFloat($('#monto'+id).val().replace(/,/g,""))+parseFloat($('#iva'+id).val().replace(/,/g,""))+parseFloat($('#propina'+id).val().replace(/,/g,""))+impuestoHospedaje;
	
	//if(comprobacion == 1){
		$('#tota'+id).html(number_format(sumaTotal,2,".",","));
		$('#total'+id).val(sumaTotal);
	//}else{			
		//$('#tota'+id).html(number_format(sumaTotal,2,".",","));
		//$('#total'+id).val(sumaTotal);
	//}
	//recalculo del resumen	
	
	if(comprobacion == 1){
		// Sumamos todos los de tipo reembolso y sera visualizado en el efectivo comprobado y a el monto a reembolsar.
		recalculaCF();
	}else{
		recalcula();
	}
}

//funcion que nos permitira reconocer si el empleado que envio la comprobacion es de tipo EXterno ó de tipo normal
//un empleado sera externo cuando solo tenga gastos comprobados de tipo amex externo y reembolso
//de lo contrario sera un empleado de tipo normal
function compruebaTipoUsuario(no_partidas){
	// banderaUsuarioExterno = 0 : cuando sea un empleado de tipo normal
	// banderaUsuarioExterno = 1 : cuando sea un empleado de tipo externo
	var banderaUsuarioExterno = 0;
	for(var i = 1; i<= no_partidas ; i++){
		if (($("#tipo"+i).val() == "Amex externo") || ($("#tipo"+i).val() == "Reembolso")){
			banderaUsuarioExterno = 1;
		}else{
			banderaUsuarioExterno = 0;
		}		
	}
	
	return banderaUsuarioExterno;
}


//Funcion que permitira obtener el monto permitivo para el parametro en cuestion : Validacion Comentarios Obligatorios
function validacionParametro(concepto,tramiteId,conceptoId){
	$.ajax({
        type: "POST",
        url: "services/Ajax_comprobacion.php",
        data: "tramiteComentario="+tramiteId+"&conceptoIdComentario="+conceptoId,
        dataType: "json",
        async : false,
        timeout: 10000,
        success: function(json){
        	var nombreDivisa="";
            var montoPermitido = json[0].pr_cantidad;
            var divisa = json[0].div_id;
           getParametroDivisa(montoPermitido,divisa,concepto);
        },
        error: function(x, t, m) {
			if(t==="timeout") {
				location.reload();
					 abort();
			}
		}
     });
}

function validacionViaje(tramiteId,A,H,L){
	var idConceptos = new Array($("#id_alimentos").val(),$("#id_hotel").val(),$("#id_lavanderia").val());
	var nomConceptos = new Array("Alimentos","Hotel","Lavandería");
	var miConceptos = new Array(A,H,L);
	for(var i=0;i<miConceptos.length;i++){		
		if(miConceptos[i] == true){			
			ExcepcionViaje(tramiteId,nomConceptos[i],(i+1),idConceptos[i]);
		}
	}
	if($("#validacionRealizada").val() <= 3){		
		$("#validacionCompleta").val(1);
		$("#sol_select").removeAttr("disabled");
	}else{
		$("#validacionCompleta").val(1);
		$("#sol_select").removeAttr("disabled");		
	}
}

function getParametroDivisa(montoPermitido,divisa,conceptoNombre){	
	 if(conceptoNombre == "Alimentos"){
		
     	$("#parametroAlimento").val(montoPermitido);
     	$("#divisaAlimento").val(divisa);
     }else if(conceptoNombre == "Hotel"){
    	
     	$("#parametroHospedaje").val(montoPermitido);
     	$("#divisaHospedaje").val(divisa);
     }else if(conceptoNombre == "Lavandería"){
    	
     	$("#parametroLavanderia").val(montoPermitido);
     	$("#divisaLavanderia").val(divisa);
     }	 
}

function recalculaConceptoDetalle(conceptoEliminar){
	if(conceptoEliminar == "Alimentos"){
		var AlimentosId = 0;
		var no_partidas = parseInt($("#comprobacion_table>tbody >tr").length);
		/*for(var i = 1; i<=no_partidas; i++){
			if($('#concepto'+i).val() == "Alimentos"){
				AlimentosId = parseInt($('#numConcepto'+i).val());
			}
		}*/
		tramiteId = $('#sol_select').val();		
		//Solo se realizara la validación diaria para el concepto de tipo alimento		
		conceptoId = $("#id_alimentos").val();		
		calculoParametros();
	}	
}

function conceptoElimina(id){
	var idElimina=parseInt(id);
	conceptoAEliminar = $('#concepto'+idElimina).val();	
}

function reiniciaComentarioObligatorio(){
	if($("#select_concepto").val() == 25){
		/* Validará que si el concepto es Otros Gastos, colocará el asterisco para indicar que el campo 
		 * de Comentarios es obligatorio.
		 */ 
		$("#comentarioReq").html("Comentario<span class='style1' >*</span>:");					
	}else{
		$("#comentarioReq").html("Comentario:&nbsp;");
		$("#excedeMontoComentarios").val("0");
	}
}

function ComentariosObligatorios(){
	//alert("opcion"+$("#select_concepto option:selected").html());
	var numeroDias = 0;
	numeroDias = $("#diasViaje").val();	
	var total=parseFloat($("#total_dolares").val().replace(/,/g,""));	
	
	if($("#select_concepto option:selected").html() == "Alimentos"){
		var TotalMXNAlimento = calculoDivisa($('#parametroAlimento').val(),$('#divisaAlimento').val());		
		//alert(sumaComentariosObligatorios(TotalMXNAlimento,conceptoNombre,total));
		if((sumaComentariosObligatorios(TotalMXNAlimento,$("#select_concepto option:selected").html(),total) > TotalMXNAlimento) && (sumaComentariosObligatorios(TotalMXNAlimento,$("#select_concepto option:selected").html(),total) > 0)){
			$("#comentarioReq").html("Comentario<span class='style1'>*</span>:&nbsp;"); 
			$("#excedeMontoComentarios").val("1");
		}else{
			if($("#no_asistentes").val() > 1){
				$("#comentarioReq").html("Comentario<span class='style1' >*</span>:");	
			}else{
				reiniciaComentarioObligatorio();
			}
		}
	}else if($("#select_concepto option:selected").html() == "Hotel"){
		if(numeroDias > 1){
			numeroDias = parseInt(numeroDias - 1);
		}
		//alert("Numero de dias"+numeroDias);
		var TotalMXNHospedajeViaje = 0;			
		var TotalMXNHospedaje = calculoDivisa($('#parametroHospedaje').val(),$('#divisaHospedaje').val());
		
		TotalMXNHospedajeViaje = TotalMXNHospedaje * numeroDias;		
		//alert("veremos!!!!"+TotalMXNHospedajeViaje);
		//alert(sumaComentariosObligatorios(TotalMXNHospedajeViaje,$("#select_concepto option:selected").html(),total));
		if((sumaComentariosObligatorios(TotalMXNHospedajeViaje,$("#select_concepto option:selected").html(),total) > TotalMXNHospedajeViaje) && (sumaComentariosObligatorios(TotalMXNHospedajeViaje,$("#select_concepto option:selected").html(),total) > 0)){
			$("#comentarioReq").html("Comentario<span class='style1'>*</span>:&nbsp;"); 
			$("#excedeMontoComentarios").val("1");
		}else{
			reiniciaComentarioObligatorio();
		}
	}else if($("#select_concepto option:selected").html() == "Lavandería"){
		var diasViajeLavanderia = 0;
		var TotalMXNLavanderiaViaje = 0;
		
		diasViajeLavanderia = numeroDias - 6;		
		var TotalMXNLavanderia = calculoDivisa($('#parametroLavanderia').val(),$('#divisaLavanderia').val());
		
		//validacion por viaje.
		TotalMXNLavanderiaViaje = TotalMXNLavanderia * diasViajeLavanderia;		
		if((sumaComentariosObligatorios(TotalMXNLavanderiaViaje,$("#select_concepto option:selected").html(),total) > TotalMXNLavanderiaViaje) && (sumaComentariosObligatorios(TotalMXNLavanderiaViaje,$("#select_concepto option:selected").html(),total) > 0)){
			$("#comentarioReq").html("Comentario<span class='style1'>*</span>:&nbsp;"); 
			$("#excedeMontoComentarios").val("1");
		}else{
			reiniciaComentarioObligatorio();
		}
	}	
}

function sumaComentariosObligatorios(TotalMXN,concepto,total){
	var sumConcepto = 0;
	var sumaAlimentos = 0;
	if((cCObligatorio != 0)){ //Se realizo una edicion
		//alert("aqui edicion");
		//sumConcepto = sumaGastos(total);
		if(total != parseFloat($("#totalMxn"+cCObligatorio).val())){
			//alert("Diferente");
			if($("#fecha"+cCObligatorio).val() == $("#fecha").val()){
				sumConcepto = total + (sumaGastosEdit(0,concepto) - parseFloat($("#totalMxn"+cCObligatorio).val()));
			}else{
				sumConcepto = total + sumaGastosEdit(0,concepto);
			}
			//alert("sum concepto"+sumConcepto);
		}else{			
			if($("#fecha"+cCObligatorio).val() == $("#fecha").val()){
				sumConcepto = sumaGastosEdit(total,concepto) - parseFloat($("#totalMxn"+cCObligatorio).val());
			}else{
				sumConcepto = sumaGastosEdit(total,concepto);
			}			
			//alert("...."+sumConcepto);			
		}		
	}else{
		//alert("comentario Obligatorio.");
		sumConcepto = sumaGastos(total,concepto);
	}		
	return sumConcepto;
}

//function sumaComentariosObligatoriosHotel(TotalMXN,concepto,total){
//	var sumConcepto = 0;
//	
//}
//
//function sumaComentariosObligatoriosLavanderia(TotalMXN,concepto,total){
//	var sumConcepto = 0;
//}

function sumaGastos(total,concepto){
	var sumGasto = 0;
	sumGasto = total;
	var no_conceptos = parseInt($("#comprobacion_table>tbody >tr").length);
	if(concepto == "Alimentos"){
		for(var i=1;i<=no_conceptos;i++){
			if(concepto == $("#concepto"+i).val()){
				if($("#fecha").val() == $("#fecha"+i).val()){
					//alert("Es igual");
					sumGasto+= parseFloat($("#totalMxn"+i).val());
				}else{
					//alert("No es igual");
				}
			}				
		}		
	}else{
		for(var i=1;i<=no_conceptos;i++){
			if(concepto == $("#concepto"+i).val()){ //Hotel : Lavanderia
				sumGasto+= parseFloat($("#totalMxn"+i).val());
			}
		}
	}
	//alert(sumGasto);
	return sumGasto;
}

function sumaGastosEdit(total,concepto){
	var sumGasto = 0;
	sumGasto = total;
	var no_conceptos = parseInt($("#comprobacion_table>tbody >tr").length);
	
	if(concepto == "Alimentos"){
		for(var i=1;i<=no_conceptos;i++){
			if($("#concepto"+i).val() == concepto){
				if($("#fecha"+i).val() == $("#fecha").val()){
					//alert("Es igual");
					sumGasto+= parseFloat($("#totalMxn"+i).val());
				}		
			}						
		}
	}else{
		for(var i=1;i<=no_conceptos;i++){
			if($("#concepto"+i).val() == concepto){
				sumGasto+= parseFloat($("#totalMxn"+i).val());
			}
		}
	}

	return sumGasto;
}

function fechaAlimentos(){
	var numeroDias = 0;
	numeroDias = $("#diasViaje").val();	
	var total=parseFloat($("#total_dolares").val().replace(/,/g,""));
	
	if($('#select_concepto option:selected').html() == "Alimentos"){		
		var TotalMXNAlimento = calculoDivisa($('#parametroAlimento').val(),$('#divisaAlimento').val());
		var sumaAlimentos = 0;	
		if((cCObligatorio != 0)){ //Se realizo una edicion
			sumaAlimentos = sumFechaAlimentosEdicion($('#select_concepto option:selected').html());		
			sumaAlimentos = sumaAlimentos + total;		
			if(sumaAlimentos > TotalMXNAlimento){
				$("#comentarioReq").html("Comentario<span class='style1'>*</span>:&nbsp;"); 
				$("#excedeMontoComentarios").val("1");
			}else{
				if($("#no_asistentes").val() > 1){
					$("#comentarioReq").html("Comentario<span class='style1' >*</span>:");	
				}else{
					reiniciaComentarioObligatorio();
				}
			}
		}else{
			//alert("Comentarios.");
			ComentariosObligatorios();
		}
	}else if($('#select_concepto option:selected').html() == "Hotel"){
		//alert("Cambio de fecha en hotel");
		numeroDias = parseInt(numeroDias - 1);
		var TotalMXNHospedajeViaje = 0;			
		var TotalMXNHospedaje = calculoDivisa($('#parametroHospedaje').val(),$('#divisaHospedaje').val());		
		TotalMXNHospedajeViaje = TotalMXNHospedaje * numeroDias;
		if((sumaComentariosObligatorios(TotalMXNHospedajeViaje,$("#select_concepto option:selected").html(),total) > TotalMXNHospedajeViaje) && (sumaComentariosObligatorios(TotalMXNHospedajeViaje,$("#select_concepto option:selected").html(),total) > 0)){
			$("#comentarioReq").html("Comentario<span class='style1'>*</span>:&nbsp;"); 
			$("#excedeMontoComentarios").val("1");
		}else{
			reiniciaComentarioObligatorio();
		}		
	}else if($('#select_concepto option:selected').html() == "Lavandería"){
		var diasViajeLavanderia = 0;		
		diasViajeLavanderia = numeroDias - 6;		
		var TotalMXNLavanderia = calculoDivisa($('#parametroLavanderia').val(),$('#divisaLavanderia').val());
		TotalMXNLavanderiaViaje = TotalMXNLavanderia * diasViajeLavanderia;
		
		if((sumaComentariosObligatorios(TotalMXNLavanderiaViaje,$("#select_concepto option:selected").html(),total) > TotalMXNLavanderiaViaje) && (sumaComentariosObligatorios(TotalMXNLavanderiaViaje,$("#select_concepto option:selected").html(),total) > 0)){
			$("#comentarioReq").html("Comentario<span class='style1'>*</span>:&nbsp;"); 
			$("#excedeMontoComentarios").val("1");
		}else{
			reiniciaComentarioObligatorio();
		}
	}
}


function sumFechaAlimentosEdicion(concepto){
	var suma = 0;	
	var no_conceptos = parseInt($("#comprobacion_table>tbody >tr").length);
	for(var i=1;i<=no_conceptos;i++){
		if(concepto == $("#concepto"+i).val()){
			if(($("#fecha").val() == $("#fecha"+i).val())){
				suma += parseFloat($("#totalMxn"+i).val()); 
			}
		}		
	}
	
	return suma;
}

function verificarTipoYConceptos(tabla, parametro, buscaTipo, comprobacion, iddetalle){
	parametro = new String(parametro);
	var i = 1;
	var no_partidas = parseInt($("#" + tabla + ">tbody>tr").length);
	var encontradoConcepto = false;
	var encontradoTipo = false;
	for(i = 1; i <= no_partidas; i++){
		if(buscaTipo == 0){ // Se buscará por concepto
			var concepto = $('#concepto'+i).val();
			
			if(comprobacion == 1){ // Comprobación de viaje
				var detalleOriginal = $('#concepto_original'+i).val();
								
				if(concepto == parametro && parseInt(detalleOriginal) == parseInt(iddetalle)){
					encontradoConcepto = true;
					break;
				}else{
					encontradoConcepto = false;
				}
			}else{
				if(concepto == parametro){
					encontradoConcepto = true;
				}else{
					encontradoConcepto = false;
				}
			}
			
		}else{ // Se buscará por tipo
			var tipo = "";
			if(comprobacion == 0){
				tipo = $('#tipo_comp'+i).val();
				if(tipo == parametro){
					encontradoTipo = true;
				}else{
					encontradoTipo = false;
				}
			}else{
				tipo = new String($("#tipo"+i).val());
				if(tipo == parametro){
					encontradoTipo = true;
				}else{
					encontradoTipo = false;
				}
			}
		} // Enf If
	} // End For
	
	if(encontradoConcepto == true || encontradoTipo == true){
		return true;
	}else{
		return false;
	}
}

function verificaMontosIngresados(tabla, tipoComp, id){
	var i = 0;
	var no_partidas = parseInt($("#" + tabla + ">tbody>tr").length);
	var excedeMonto = false;
	var excedeIVA = false;
	var excedePropinas = false;
	var excedeHospedaje = false;
	var monto = 0;
	var iva = 0; 
	var propina = 0;
	var hospedaje = 0;
	
	var montoBD = 0;
	var ivaBD = 0;
	var propinaBD = 0;
	var hospedajeBD = 0;
	
	
		// Variables de montos ingresados por el usuario
		monto = $('#monto'+id).val().replace(/,/g,"");
		iva = $('#iva'+id).val().replace(/,/g,"");
		propina = $('#propina'+id).val().replace(/,/g,"");
		
		if(tipoComp == 1){
			hospedaje = $('#imphospedaje'+id).val().replace(/,/g,"");
		}
		
		// Montos extraidos de la BD
		montoBD = $('#dc_monto'+id).val().replace(/,/g,"");
		ivaBD = $('#dc_iva'+id).val().replace(/,/g,"");
		propinaBD = $('#dc_propinas'+id).val().replace(/,/g,"");
		
		if(tipoComp == 1){
			hospedajeBD = $('#dc_imphospedaje'+id).val().replace(/,/g,"");
		}
		
		if(parseFloat(monto) > parseFloat(montoBD)){
			alert("No puede ingresar un monto mayor al original.");
			$('#monto'+id).css({'background-color' : '#E1E4EC'});
			$('#monto'+id).val(number_format(montoBD,2,".",","));
			$('#monto'+id).focus();
			excedeMonto = true;
		}else if(parseFloat(iva) > parseFloat(ivaBD)){
			alert("No puede ingresar un monto de iva mayor al original.");
			$('#iva'+id).css({'background-color' : '#E1E4EC'});
			$('#iva'+id).val(number_format(ivaBD,2,".",","));
			$('#iva'+id).focus;
			excedeIVA = true;
		}else if(parseFloat(propina) > parseFloat(propinaBD)){
			alert("No puede ingresar un monto de propina mayor al original.");
			$('#propina'+id).css({'background-color' : '#E1E4EC'});
			$('#propina'+id).val(number_format(propinaBD,2,".",","));
			$('#propina'+id).focus();
			excedePropinas = true;
		}else if(parseFloat(hospedaje) > parseFloat(hospedajeBD) && tipoComp == 1){
			alert("No puede ingresar un monto de hospedaje mayor al original.");
			$('#imphospedaje'+id).css({'background-color' : '#E1E4EC'});
			$('#imphospedaje'+id).val(number_format(hospedajeBD,2,".",","));
			$('#imphospedaje'+id).focus;
			excedeHospedaje = true;
		}

	
	if(excedeMonto || excedeIVA || excedePropinas || excedeHospedaje){ // Si se excede, devolveremos un false para detener la ejecución del script
		return false;
	}else{
		return true;
	}
}

function restaurarMontos(tabla, tipoComp, id){
	var i = 1;
	var no_partidas = parseInt($("#" + tabla + ">tbody>tr").length);
	var monto = 0;
	var iva = 0; 
	var propina = 0;
	var hospedaje = 0;
	
	var montoBD = 0;
	var ivaBD = 0;
	var propinaBD = 0;
	var hospedajeBD = 0;
	
	//for(i = 1; i <= no_partidas; i++){
		// Variables de montos ingresados por el usuario
		monto = $('#monto'+id).val().replace(/,/g,"");
		iva = $('#iva'+id).val().replace(/,/g,"");
		propina = $('#propina'+id).val().replace(/,/g,"");
		
		if(tipoComp == 1){
			hospedaje = $('#imphospedaje'+id).val().replace(/,/g,"");
		}
		
		// Montos extraidos de la BD
		montoBD = $('#dc_monto'+id).val().replace(/,/g,"");
		ivaBD = $('#dc_iva'+id).val().replace(/,/g,"");
		propinaBD = $('#dc_propinas'+id).val().replace(/,/g,"");
		
		if(tipoComp == 1){
			hospedajeBD = $('#dc_imphospedaje'+id).val().replace(/,/g,"");
		}
		
		$('#monto'+id).val(number_format(montoBD,2,".",","));
		$('#iva'+id).val(number_format(ivaBD,2,".",","));
		$('#propina'+id).val(number_format(propinaBD,2,".",","));
		
		if(tipoComp == 1){
			$('#imphospedaje'+id).val(number_format(hospedajeBD,2,".",","));
		}
	//}
}

function obtenerFiladeDetalle(tabla, dcidBuscado){
	var i = 1;
	var no_partidas = parseInt($("#" + tabla + ">tbody>tr").length);
	
	for(i = 1; i <= no_partidas; i++){
		if($('#dc_id'+i).val() == dcidBuscado){
			return i;
			break;
		}
	}
}

function actualizarResumen(){
	if(verificaMontosIngresados("descrip_comprobacion_table")){
		recalcularTotal_CV();
	}
}

function actializacionMontos(tabla, tipoComp, id){
	var i = 1;
	var no_partidas = parseInt($("#" + tabla + ">tbody>tr").length);
	var monto = 0;
	var iva = 0; 
	var propina = 0;
	var hospedaje = 0;
	
	var montoBD = 0;
	var ivaBD = 0;
	var propinaBD = 0;
	var hospedajeBD = 0;
	
	// Variables de montos ingresados por el usuario
	monto = $('#monto'+id).val().replace(/,/g,"");
	iva = $('#iva'+id).val().replace(/,/g,"");
	propina = $('#propina'+id).val().replace(/,/g,"");
	
	if(tipoComp == 1){
		hospedaje = $('#imphospedaje'+id).val().replace(/,/g,"");
	}
	
	// Montos extraidos de la BD
	montoBD = $('#dc_monto'+id).val().replace(/,/g,"");
	ivaBD = $('#dc_iva'+id).val().replace(/,/g,"");
	propinaBD = $('#dc_propinas'+id).val().replace(/,/g,"");
	
	if(tipoComp == 1){
		hospedajeBD = $('#dc_imphospedaje'+id).val().replace(/,/g,"");
	}
	
	if(tipoComp == 1){
		$('#dc_monto_aux'+id).val(number_format(monto,2,".",","));
		$('#dc_iva_aux'+id).val(number_format(iva,2,".",","));
		$('#dc_propinas_aux'+id).val(number_format(propina,2,".",","));		
		$('#dc_imphospedaje_aux'+i).val(number_format(hospedaje,2,".",","));
	}else{
		$('#dc_monto'+id).val(number_format(monto,2,".",","));
		$('#dc_iva'+id).val(number_format(iva,2,".",","));
		$('#dc_propinas'+id).val(number_format(propina,2,".",","));
	}
}

function ocultaAmex(){
	var frm=document.detallecomp;
	if(frm.tipo.value == 2){
		$("#amex_form").slideDown(1000);            
        $("#amex_form").css("display", "block");
        $("#amex_form").addClass("visible");				
	}else{					
		if($("#amex_form").hasClass("visible")){
            $("#amex_form").slideUp(1000);
            $("#amex_form").removeClass("visible");
		}
	}	
}

function verificaBotonesEncendidos(registros){
	var numBtnsencendidos = 0;
	
	for(var i=1; i<=registros; i++){
		if($('#recalculo_aux'+i).val() == 1){
			numBtnsencendidos += 1;
		}
	}
	
	return numBtnsencendidos;
}

function restaurarMontosOriginales(id){
	var montoBD = 0;
	var ivaBD = 0;
	var propinaBD = 0;
	var hospedajeBD = 0;
	
	// Montos extraidos de la BD
	montoBD = $('#dc_monto'+id).val().replace(/,/g,"");
	ivaBD = $('#dc_iva'+id).val().replace(/,/g,"");
	propinaBD = $('#dc_propinas'+id).val().replace(/,/g,"");
	hospedajeBD = $('#dc_imphospedaje'+id).val().replace(/,/g,"");
	
	$('#monto'+id).val(number_format(montoBD,2,".",","));
	$('#iva'+id).val(number_format(ivaBD,2,".",","));
	$('#propina'+id).val(number_format(propinaBD,2,".",","));
	$('#imphospedaje'+id).val(number_format(hospedajeBD,2,".",","));
}

// Función para verificar si el monto del cargo AMEX es igual a la suma de los montos de los gastos comprobados
function verificarEstatusAmex(id_tramite){
	$.ajax({
        url:  "services/Ajax_comprobacion.php",
        type: "POST",
        data: "comprobacionTram="+id_tramite,
        dataType: "json",
        async: false,
        timeout: 10000, 
        success: function(json){},
        complete: function(json){},
		error: function(x, t, m){
			if(t==="timeout"){
				verificarEstatusAmex(id_tramite);
			}
		}
      });//fin ajax
}

function validaZeroIzquierda(monto,campo){			
	if( monto.substring(monto.length-1,monto.length) === "." || monto.substring(monto.length-2,monto.length) === ".0" ){
		$("#"+campo).val(monto);
	}else if(monto == 0 || monto == "" || monto == "NaN"){
		$("#"+campo).val(0);
	}else{
		$("#"+campo).val(parseFloat(monto));
	}
}

//validación campos númericos
function validaNum(valor){
    cTecla=(document.all)?valor.keyCode:valor.which;
    if(cTecla==8) return true;
    patron=/^([0-9.]{1,2})?$/;
    cTecla= String.fromCharCode(cTecla);
    return patron.test(cTecla);
}

