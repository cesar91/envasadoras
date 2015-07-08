var regionNacional = 1;

// Obtención de Políticas cotización de Hotel por Agencia
function validarPoliticaHotelAgencia(concepto){
	var no_itinerario = parseInt($("#itinerarioActualOPosible").val());	
	var idUsuario = $("#t_iniciador").val();
	var region = $("#region"+no_itinerario).val();
	var flujo = $("#flujoId").val();
	var montoPesos = parseFloat($('#montoP').val().replace(/,/g,""));
	var montoLimitePolitica = 0;
	var dias = parseInt($("#noches").val());
	var montoLimitePesos = 0;
	
	var politica = {
		'idUsuario' : idUsuario,
		'idConcepto' : concepto,
		'flujo': flujo,
		'region' : region
	};
	
	montoLimitePolitica = obtenerPolitica(politica);
	
	montoLimitePesos = (montoLimitePolitica * dias);
	//alert("Monto en Pesos: "+montoPesos+" \n Monto Límite por día: "+montoLimitePolitica+" \n Monto limite Politica: "+ montoLimitePesos +" \n"+montoPesos+" : "+montoLimitePesos);
	if(montoLimitePesos < montoPesos){
		$("#warning_msg").html("<strong>NOTA: Esta rebasando la pol&iacute;tica del concepto Hotel, el monto m&aacute;ximo es de: " + number_format(montoLimitePesos,2,".",",") + " MXN.</strong>");
		$("#warning_msg").fadeIn();
		$("#obsjus").fadeIn();
		$("#warning_msg").css("display", "block");
		$("#obsjus").html("Comentarios<span class='style1'>*</span>:&nbsp;");
		$("#excedeMontoHospedaje").val("1");
	}else{
		$("#warning_msg").slideUp(1000).hide().html("");
		$("#obsjus").html("Comentarios:&nbsp;");
		$("#excedeMontoHospedaje").val("0");
	}
} // Fin de validarPoliticaHotelAgencia

//Obtención de Políticas cotización de Renta de Auto por Agencia
function validarPoliticaAutoAgencia(concepto){	
	var no_itinerario = parseInt($("#itinerarioActualOPosible").val());	
	var idUsuario = $("#t_iniciador").val();
	var region = $("#region"+no_itinerario).val();
	var flujo = $("#flujoId").val();
	var montoPesos = parseFloat($('#montoPesos').val().replace(/,/g,""));
	var montoLimitePolitica = 0;
	var dias = parseInt($("#diasRenta").val());
	
	var politica = {
		'idUsuario' : idUsuario,
		'idConcepto' : concepto,
		'flujo': flujo,
		'region' : region
	};
		
	montoLimitePolitica = obtenerPolitica(politica);
	
	montoLimitePesos = (montoLimitePolitica * dias);
	//alert("Monto en Pesos: "+montoPesos+" \n Monto Límite por día: "+montoLimitePolitica+" \n Monto limite Politica: "+ montoLimitePesos +" \n"+montoPesos+" : "+montoLimitePesos);
	if(montoLimitePesos < montoPesos){
		$("#warning_msg_auto").html("<strong>NOTA: Esta rebasando la pol&iacute;tica del concepto Auto, el monto m&aacute;ximo es de: " + number_format(montoLimitePesos,2,".",",") + " MXN.</strong>");
		$("#warning_msg_auto").fadeIn();
		$("#obsjus").fadeIn();
		$("#warning_msg_auto").css("display", "block");
		$("#obsjus").html("Comentarios<span class='style1'>*</span>:&nbsp;");
		$("#excedeMontoAuto").val("1");
	}else{
		$("#warning_msg_auto").slideUp(1000).hide().html("");
		$("#obsjus").html("Comentarios:&nbsp;");
		$("#excedeMontoAuto").val("0");
	}
} // Fin de validarPoliticaAutoAgencia

// Obtención de Políticas cotización de Hotel por Empleado
function validarPoliticaHotelEmpleado(concepto){
	var idUsuario = $("#idusuario").val();
	var flujo = $("#flujoId").val();
	var region;
	
	// Obtener Región
	if($("#select_tipo_viaje option:selected").val() == -1){
		region = 0;
	}else if($("#select_tipo_viaje option:selected").val() != 1){
		region = $("#select_region_viaje option:selected").val();
	}else{
		region = regionNacional;
	}
	
	var montoPesos = parseFloat($('#montoP').val().replace(/,/g,""));
	var dias = parseInt($("#noches").val());
	var montoLimitePesos = 0;
	var montoLimitePolitica = 0;
	
	if(region != 0){
		var politica = {
			'idUsuario' : idUsuario,
			'idConcepto' : concepto,
			'flujo': flujo,
			'region' : region
		};
		
		montoLimitePolitica = obtenerPolitica(politica);
		
		montoLimitePesos = (montoLimitePolitica * dias);
		//alert("Monto en Pesos: "+montoPesos+" \n Monto Límite por día: "+montoLimitePolitica+" \n Monto limite Politica: "+ montoLimitePesos +" \n"+montoPesos+" : "+montoLimitePesos);
		if(montoLimitePesos < montoPesos){
			$("#warning_msg").html("<strong>NOTA: Esta rebasando la pol&iacute;tica del concepto Hotel, el monto m&aacute;ximo es de: " + number_format(montoLimitePesos,2,".",",") + " MXN.</strong>");
			$("#warning_msg").fadeIn();
			$("#obsjus").fadeIn();
			$("#warning_msg").css("display", "block");
			$("#obsjus").html("Comentarios<span class='style1'>*</span>:&nbsp;");
			$("#excedeMontoHospedaje").val("1");
		}else{
			$("#warning_msg").slideUp(1000).hide().html("");
			$("#obsjus").html("Comentarios:&nbsp;");
			$("#excedeMontoHospedaje").val("0");
		}
	}else{
		$("#warning_msg").html("<strong>Por favor seleccione la Regi&oacute;n a la que desea viajar.</strong>");
		$("#warning_msg").fadeIn();
		$("#warning_msg").css("display", "block");
		$("#agregar_Hotel").attr("disabled", "disabled");
		$("#aceptarHotel").attr("disabled", "disabled");
		$("#cancelarHotel").attr("disabled", "disabled");
	}
} // Fin de validarPoliticaHotelEmpleado

//Obtención de Políticas cotización de Renta de Auto por Empleado
function validarPoliticaAutoEmpleado(concepto){
	var idUsuario = $("#idusuario").val();
	var flujo = $("#flujoId").val();
	var region;
	
	// Obtener Región
	if($("#select_tipo_viaje option:selected").val() == -1){
		region = 0;
	}else if($("#select_tipo_viaje option:selected").val() != 1){
		region = $("#select_region_viaje option:selected").val();
	}else{
		region = regionNacional;
	}
	
	var montoPesos = parseFloat($('#montoPesos').val().replace(/,/g,""));
	var dias = parseInt($("#diasRenta").val());
	var montoLimitePesos = 0;
	var montoLimitePolitica = 0;
	
	if(region != 0){
		var politica = {
			'idUsuario' : idUsuario,
			'idConcepto' : concepto,
			'flujo': flujo,
			'region' : region
		};
		
		montoLimitePolitica = obtenerPolitica(politica);
		
		montoLimitePesos = (montoLimitePolitica * dias);
		//alert("Monto en Pesos: "+montoPesos+" \n Monto Límite por día: "+montoLimitePolitica+" \n Monto limite Politica: "+ montoLimitePesos +" \n"+montoPesos+" : "+montoLimitePesos);
		if(montoLimitePesos < montoPesos){
			$("#warning_msg_auto").html("<strong>NOTA: Esta rebasando la pol&iacute;tica del concepto Auto, el monto m&aacute;ximo es de: " + number_format(montoLimitePesos,2,".",",") + " MXN.</strong>");
			$("#warning_msg_auto").fadeIn();
			$("#obsjus").fadeIn();
			$("#warning_msg_auto").css("display", "block");
			$("#obsjus").html("Comentarios<span class='style1'>*</span>:&nbsp;");
			$("#excedeMontoAuto").val("1");
		}else{
			$("#warning_msg_auto").slideUp(1000).hide().html("");
			$("#obsjus").html("Comentarios:&nbsp;");
			$("#excedeMontoAuto").val("0");
		}
	}else{
		$("#warning_msg_auto").html("<strong>Por favor seleccione la Regi&oacute;n a la que desea viajar.</strong>");
		$("#warning_msg_auto").fadeIn();
		$("#warning_msg_auto").css("display", "block");
		$("#aceptarAuto").attr("disabled", "disabled");
		$("#cancelarAuto").attr("disabled", "disabled");
	}
} // Fin de validarPoliticaAutoEmpleado

// Validación de Politicas para Solicitudes de Gastos
function validarPoliticaSolicitudGastos(){
	var idUsuario = $("#idusuario").val();
	var flujo = $("#flujoId").val();
	var concepto = $("#sg_concepto option:selected").val();
	var conceptoTexto = $("#sg_concepto option:selected").text();
	var montoPesos = parseFloat($('#tpesos').val().replace(/,/g,""));
	var region = regionNacional;
	var mensajeExcedePoliticas = "";
	
	var politica = {
		'idUsuario' : idUsuario, 
		'idConcepto' : concepto, 
		'flujo': flujo, 
		'region' : region
	};
	
	var montoLimitePolitica = obtenerPolitica(politica);
	
	if(montoLimitePolitica == 0){
		$("#capaWarning").slideUp(1000).hide().html("");
	}else{
		if(montoLimitePolitica < montoPesos){
			$("#capaWarning").slideDown(1000);
	        $("#capaWarning").css("display", "block");
			mensajeExcedePoliticas = "<strong>Esta rebasando la pol&iacute;tica del concepto " + conceptoTexto + ". <br>El monto m&aacute;ximo es de " + montoLimitePolitica + " MXN.</strong>";
			$("#capaWarning").html(mensajeExcedePoliticas);
		}else{
			$("#capaWarning").slideUp(1000).hide().html("");
		}
	}
} // Fin de validarPoliticaSolicitudGastos

