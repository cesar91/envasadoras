// Tomamos el valor seleccionado del concepto y lo enviamos en un ajax
function actualizarConcepto(valor,id, comp){
	var idConceptNew = 0;
	var conceptNew = "";
	
	if($("#concepto_new"+id+" option:selected").val() != -1){
		$.ajax({
			type: "POST",
			url: "services/Ajax_comprobacion.php",
			data: "conceptoNew="+valor,
			dataType: "json",
			async: false, 
			timeout: 10000,
			success: function(json){
				valConcepto=json;
				idConceptNew = json[0].NewId;
				conceptNew = json[0].conceptoNewId;	
			},
			complete: function(json){
				$("#concepto_txt"+id).html(conceptNew);
				if(comp == 1){
					$("#concepto"+id).val(conceptNew);
				}else{
					$("#concepto"+id).val(idConceptNew);
				}
//				$("#concepto_txt"+id).val(json);
//				$("#c_concepto"+id).val(valConcepto);
				getConcepto(conceptNew,id);
				
				if(comp == 1){
					recalculaCF();
				}else{
					recalcula();
				}
			},
			error: function(x, t, m) {
				if(t==="timeout") {
					actualizarConcepto(valor,id, comp);
				} 
			}
		});	
		
		cont ++;
		var total= number_format(parseFloat($("#total"+id).val()),2,".",",");
		var tramite = $("#idT").val();
		$.ajax({
			type: "POST",
			url: "services/Ajax_comprobacion.php",
			data: "idConcepto="+valor+"&total="+total+"&idSelectConcepto="+id+"&visitas="+cont+"&tramite="+tramite,
			success: function(json){			
				}
		});
	}
}

//funcion para asociar el nombre del concepto nuevo seleccionado
function getConcepto(conceptoNuevo,id){
	$("#concepto_new_txt"+id).val(conceptoNuevo);	
}



// Función que permitira crear una tabla temporal para los montos
function montosTabla(){
	var crear = "tablatemp";
	$.ajax({
		type: "POST",
		url: "services/Ajax_comprobacion.php",
		data: "idtotaltemp="+crear,
		success: function(json){			
		}
	});
}

// Función que me permitira crear una tabla temporal para poder guardar los conceptos
function conceptosTabla(){
	var temp=1;
	$.ajax({
		type: "POST",
		url: "services/Ajax_comprobacion.php",
		data: "tablaConceptostemp="+temp,
		success: function(json){			
		}
	});
}

function borrarMonto(idOriginal){	
	tramite = $("#idT").val();	
	
	$.ajax({
		type: "POST",
		url: "services/Ajax_comprobacion.php",
		data: "eliminarID="+idOriginal+"&tramite="+tramite,
		dataType: "json",
		success: function(json){
		}
	});
}




function insertaTTemp(id_t,monto,diferencia,iva,propina,imphospedaje,id_next,id_tramite){	
	$.ajax({
		type: "POST",
		url: "services/Ajax_comprobacion.php",
		data: "idTTemp="+id_t+"&monto="+monto+"&diferencia="+diferencia+"&iva="+iva+"&propina="+propina+"&imphospedaje="+imphospedaje+"&id="+id_next+"&tramite="+id_tramite,
		success: function(json){
			$("#recalculo"+id_t).attr("disabled", "disabled");
			$("#recalculo"+id_t).attr("style", "width: 30px; height:30px; background:url(../../images/fwk/action_reorder.gif); background-position:center; background-repeat:no-repeat; border-style:none; cursor:default;");
		}
	});
}

//Está función se utilizará para aprobar las comprobaciones de invitación por el Supervisor de finanzas
function apruebaComprobacionInv(tramite){
	$.ajax({
		type: "POST",
		url: "services/Ajax_comprobacion.php",
		data: "tramiteComp="+tramite,
		success: function(json){
			if(json == "realizado"){
				return true;
			}else{
				return false;
			}
		}
	});
}

// Verificara si se ha ingresado previamente el RFC
function validaProveedor(rfc,nombre){
	//console.debug(rfc,nombre);
	var url = "services/Ajax_comprobacion.php";
	var valor = true;
	$.ajaxSetup({async:false});				
	$.post(url,{proveedorRFC:rfc,proveedorNombre:nombre},function(data){
		//console.log(data);
		valor = (data == 0) ? false : true;
	});
	//console.debug("---->>>>>>"+valor);
	if(!valor) alert("El proveedor ingresado no se encuetra registrado, favor de dar de alta al proveedor");
	return valor;			
}

// Guardara las tasas introducidas por Finanzas
function guardarTasas(tramite){
	var estatusJson = "";
	var tasaDollar = $("#tasaUSDeditable").val();
	var tasaEURO = $("#tasaEUReditable").val();
	
	$.ajax({
		type: "POST",
		url: "services/Ajax_comprobacion.php",
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
			recalculaCF()
			
			bloquearcamposPantalla(0);
		},
		error: function(x, t, m){
			if(t==="timeout") {
				guardarTasas(tramite);
			}
		}
	});
}

function redondea(valor){
	return (Math.round(valor * Math.pow(10, 2)) / Math.pow(10, 2));
}

function aplicaFormato(){
	var tasaUS = parseFloat($("#tasaUSDeditable").val().replace(/,/g,""));
	var tasaER = parseFloat($("#tasaEUReditable").val().replace(/,/g,""));
	
	$("#tasaUSDeditable").val(number_format(redondea(tasaUS),2,".",","));
	$("#tasaEUReditable").val(number_format(redondea(tasaER),2,".",","));
	
}

// Verificar ei el concepto ha sido recalculado
function verificaConcepto(idDetalle, id){
	var encontrado = 0;
	$.ajax({
		type: "POST",
		url: "services/Ajax_comprobacion.php",
		data: "conceptoComprobacionAMEX="+idDetalle,		
		dataType: "html",
		async: false, 
		timeout: 10000,
		success: function(json){
			encontrado = json;
		},
		complete: function(json){
			$("#aux"+id).val(encontrado);
		},
		error: function(x, t, m){
			if(t==="timeout") {
				verificaConcepto(idDetalle);
			}
		}
	});
}

// Cargar cargo Amex seleccionado
function cargoAmex(cargoAmex){
	var frm= document.invitacion_comp;
	$.ajax({
		type: "POST",
		url: "services/Ajax_comprobacion.php",
		data: "cargoAmexseleccionado="+cargoAmex,
		dataType: "json",
		async: false, 
		timeout: 10000,
		success: function(json){
			if(json==null){
				$("#select_tarjeta_cargo").append(new Option("Sin Datos"));
			}else{
				arreglovalores.splice(0,arreglovalores.length);
				arregloestatus.splice(0,arregloestatus.length);
				arreglodescripcion.splice(0,arreglodescripcion.length);
				LlenarCombo(json, frm.select_tarjeta_cargo);
				LlenarCombo2(arreglovalores,arreglodescripcion,arregloestatus,frm.select_tarjeta_cargo);
			}
		},
		error: function(x, t, m) {
			if(t==="timeout") {
				cargoAmex(cargoAmex);
			} 
		}
	});
}