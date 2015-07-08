function crear_combo_itinerarios(){
	var tipo_viaje = $("#select_tipo_viaje_pasaje option:selected").val();
	var_combo_itinerarios = "";
	var count_itinerarios = parseInt($("#solicitud_table>tbody>tr").length);	
	var_combo_itinerarios += "<option value='0'>--</option>|";
	for(var i=1;i<=count_itinerarios;i++)
		var_combo_itinerarios += "<option value='"+i+"'>"+i+"</option>|";
}

function recalculaMontos2(conceptoId, id_fila_concepto){	
	var re = /^[0-9]+|[0-9]+$/;
	id_fila_concepto = (""+id_fila_concepto).match(re);
	
	var Monto = parseFloat(($("#Monto"+id_fila_concepto).val()).replace(/,/g,""));
	var divisa = $("#moneda"+id_fila_concepto).val();
	var no_dias = $("#Days"+id_fila_concepto).val();
	var montoTotal = 0;
	var tasaNueva = 1;
	
	if(divisa != 'MXN'){ //Si la divisa es diferente a MXN
		$.ajax({
			type: "POST",
			url: "services/ajax_solicitudes.php",
			data: "divisa="+divisa,
			dataType: "json",
			success: function(json){
				tasaNueva = json[0];
				montoTotal = Monto * no_dias;
				$("#MontoTotal"+id_fila_concepto).val(number_format((montoTotal).toFixed(2),2,".",","));
				montoTotal = montoTotal * parseFloat(tasaNueva);
				$("#MontoEnPesos"+id_fila_concepto).val(number_format((montoTotal).toFixed(2),2,".",","));
				$("#exedePolitica"+id_fila_concepto).val(isExedePoliticas(id_fila_concepto));
				montoMaxPolitica = getSumaMontoMaxTotalPolitica($("#Concepto"+id_fila_concepto).val(), id_fila_concepto);
				calcular_montos(conceptoId, id_fila_concepto, ($("#MontoTotal"+id_fila_concepto).val()).replace(/,/g,""), $("#moneda"+id_fila_concepto).val(), ($("#MontoEnPesos"+id_fila_concepto).val()).replace(/,/g,""));
				muestraErrores();
				getTotalAnticipo();
			}
		});
	}else{
		montoTotal = Monto * no_dias;		
		$("#MontoTotal"+id_fila_concepto).val(number_format((montoTotal).toFixed(2),2,".",","));
		montoTotal = montoTotal * parseFloat(tasaNueva);
		$("#MontoEnPesos"+id_fila_concepto).val(number_format((montoTotal).toFixed(2),2,".",","));
		$("#exedePolitica"+id_fila_concepto).val(isExedePoliticas(id_fila_concepto));
		montoMaxPolitica = getSumaMontoMaxTotalPolitica($("#Concepto"+id_fila_concepto).val(), id_fila_concepto);
		calcular_montos(conceptoId, id_fila_concepto, ($("#MontoTotal"+id_fila_concepto).val()).replace(/,/g,""), $("#moneda"+id_fila_concepto).val(), ($("#MontoEnPesos"+id_fila_concepto).val()).replace(/,/g,""));
		muestraErrores();
		getTotalAnticipo();
	}
	/**
	 * Excepciones
	 */ 		
	id_fila_concepto = id_fila_concepto[0];
	var objeto = new Object();
	objeto["concepto"] = conceptoId;
	objeto["conceptoTexto"] = $("#select_concepto option[value="+conceptoId+"]").text();
	objeto["totalPartida"] = $("#MontoEnPesos"+id_fila_concepto).val();
	objeto["dias"] = $("#Days"+id_fila_concepto).val();
	objeto["referencia"] = id_fila_concepto;
	
	var usuario = $("#idusuario").val();						
	var Parametros = {
		'idUsuario': 	usuario,
		'idConcepto':	conceptoId,
		'flujo':	 	1,
		'region':	 	$("#regionId"+$("#itinerarios_cbx"+id_fila_concepto).val()).val()
	};				
	var Excepcion = validaPolitica(objeto, Parametros, [0], true);	
	if(Excepcion != false){
		var renglonExcepcion = crearRenglonExcepcion(Excepcion, id_fila_concepto);
		if($("#excepcion_table>tbody>tr#tr_"+id_fila_concepto).length >  0 )
			modificarRenglon(renglonExcepcion, id_fila_concepto, "excepcion_table");
		else{
			agregaRenglon(renglonExcepcion, "excepcion_table");
		}
	}else
		$("#excepcion_table>tbody>tr#tr_"+id_fila_concepto).remove();									
}

function isExedePoliticas(id_fila_concepto){
	montoIngresado = $("#MontoEnPesos"+id_fila_concepto).val();
	montoIngresado = montoIngresado.replace(/,/g,"")
	montoMaxPolitica = getSumaMontoMaxTotalPolitica($("#Concepto"+id_fila_concepto).val(), id_fila_concepto);
	if(parseFloat(montoIngresado) > parseFloat(montoMaxPolitica).toFixed(2) && parseFloat(montoMaxPolitica) != 0){
		return "1";
	}else{
		return "0";
	}
}

function muestraErrores(){	
	$("#capaWarning").html("");
	var mensajeExcedePoliticas = "";    
	var conceptoNombre = "";
	var montoDiarioPolitica = "";
	
	conceptoExcedePoliticas = false;
	var count_renglones = parseInt($("#conceptos_table>tbody>tr").length);	
	for(var i=1;i<=count_renglones;i++){
		if($("#exedePolitica"+i).val() == "1" && $("#montoCotizado"+i).val() == "0"){
			
			conceptoNombre = $("#conceptos_table>tbody").find("tr").eq(i-1).find("td").eq(2).text();
			montoDiarioPolitica = getSumaMontoMaxTotalPolitica($("#Concepto"+i).val(), i);			
			if(mensajeExcedePoliticas == ""){
				mensajeExcedePoliticas = "<div id='"+i+"'><strong><font color='red'>Nota:</font> Esta rebasando la pol&iacute;tica del concepto "+conceptoNombre+", el monto m&aacute;ximo es de "+number_format(montoDiarioPolitica,2,".",",")+" MXN.</strong></div>";
			}else{
				mensajeExcedePoliticas += "<div id='"+i+"'><strong><font color='red'>Nota:</font> Esta rebasando la pol&iacute;tica del concepto "+conceptoNombre+", el monto m&aacute;ximo es de "+number_format(montoDiarioPolitica,2,".",",")+" MXN.</strong></div>";
			}
			conceptoExcedePoliticas = true;
		}else if(($("#exedePolitica"+i).val() == "1" && $("#montoCotizado"+i).val() == "1")){
			
			conceptoNombre = $("#conceptos_table>tbody").find("tr").eq(i-1).find("td").eq(2).text();
			montoDiarioPolitica = getSumaMontoMaxTotalPolitica($("#Concepto"+i).val(), i);			
			if(mensajeExcedePoliticas == ""){
				mensajeExcedePoliticas = "<div id='"+i+"'><strong><font color='red'>Nota:</font> Esta rebasando el monto cotizado del concepto "+conceptoNombre+", el monto m&aacute;ximo es de "+number_format(montoDiarioPolitica,2,".",",")+" MXN.</strong></div>";
			}else{
				mensajeExcedePoliticas += "<div id='"+i+"'><strong><font color='red'>Nota:</font> Esta rebasando el monto cotizado del concepto "+conceptoNombre+", el monto m&aacute;ximo es de "+number_format(montoDiarioPolitica,2,".",",")+" MXN.</strong></div>";
			}
			conceptoExcedePoliticas = true;
		}
	}
	
	if(conceptoExcedePoliticas){
		$("#capaWarning").html(mensajeExcedePoliticas);
	}
}

function eliminateDuplicates(arr) {
	var i,
	len=arr.length,
	out=[],
	obj={};

	for(i=0;i<len;i++){
		obj[arr[i]]=0;
	}
	for(i in obj){
		out.push(i);
	}
	return out;
}

function calculo_de_conceptos(conceptoId, itinerarioId, id_fila_concepto){
	// Variables para la consulta del monto limite de la politica
	var idUsuario = $("#idusuario").val();
	var flujo = $("#flujoId").val();
	
	var cotizado_por_empleado_hosp = 0;
	var cotizado_por_empleado_auto = 0;
	var tipo_viaje_pasaje = $("#select_tipo_viaje_pasaje>option").eq($("#select_tipo_viaje_pasaje").val()).text();
	var re = /^[0-9]+|[0-9]+$/;
	id_fila_concepto = (""+id_fila_concepto).match(re);
	
	//Validaciones para concepto hotel y auto
	
	//HOTEL -----------------------------------------------------------------------------------------
	var error3 = false;
	var mensaje_de_error = "";
	
	//Validacion para cuando se selecciona todos
	if(conceptoId == 5 && itinerarioId == "Todos"){
		var todos = true;
		var count_itinerarios = parseInt($("#solicitud_table>tbody>tr").length);
		for(var i=1;i<=count_itinerarios;i++){
			if($("#CheckHAgencia"+i).val() == "false" || $("#CheckHEnviarAgencia"+i).val() == "false"){
				todos = false;
			}
		}
		if(!todos){
			mensaje_de_error = "El concepto no puede ser asignado a 'Todos' debido a que existe al menos un itinerario sin este concepto.";
			error3 = true;
		}
	}
	//Validacion para cuando se selecciona algun itinerario en especifico
	if(conceptoId == 5 && itinerarioId != "Todos" && itinerarioId != "0"){
		if($("#CheckHAgencia"+itinerarioId).val() == "false"){
			mensaje_de_error = "No puede seleccionar el itinerario "+itinerarioId+" debido a que no se requiere hotel en dicho itinerario.";
			error3 = true;
		}
	}
	if(error3){
		alert(mensaje_de_error);
		itinerarioId = 0;
		calculo_de_conceptos(conceptoId, itinerarioId, id_fila_concepto);
		$("#itinerarios_cbx"+id_fila_concepto).val(itinerarioId);
		return;
	}
	//AUTO -----------------------------------------------------------------------------------------
	var error4 = false;
	var mensaje_de_error2 = "";
	
	//Validacion para cuando se selecciona todos
	if(conceptoId == 10 && itinerarioId == "Todos"){
		var todos = true;
		var count_itinerarios = parseInt($("#solicitud_table>tbody>tr").length);
		for(var i=1;i<=count_itinerarios;i++){
			if($("#CheckTAgencia"+i).val() == "false" || $("#CheckTEnviarAgencia"+i).val() == "false"){
				todos = false;
			}
		}
		if(!todos){
			mensaje_de_error2 = "No puede seleccionar todos debido a que no se requiere auto en algún itinerario.";
			error4 = true;
		}
	}
	//Validacion para cuando se selecciona algun itinerario en especifico
	if(conceptoId == 10 && itinerarioId != "Todos" && itinerarioId != "0"){
		if($("#CheckTAgencia"+itinerarioId).val() == "false"){
			mensaje_de_error2 = "No puede seleccionar el itinerario "+itinerarioId+" debido a que no se requiere auto en dicho itinerario.";
			error4 = true;
		}
	}
	if(error4){
		alert(mensaje_de_error2);
		itinerarioId = 0;
		calculo_de_conceptos(conceptoId, itinerarioId, id_fila_concepto);
		$("#itinerarios_cbx"+id_fila_concepto).val(itinerarioId);
		return;
	}
	//******************************************************************
	
	//Checar si el concepto ya habia sido asignado a ese mismo itinerario
	var count_conceptos = parseInt($("#conceptos_table>tbody>tr").length);
	var count_aux = 0;
	for(var i=1;i<=count_conceptos;i++){
		if(id_fila_concepto != i){
			if($("#itinerarios_cbx"+i).val() == itinerarioId && $("#Concepto"+i).val() == conceptoId){
				if(itinerarioId == 0){					
					alert('No puede tener mas de un concepto sin asignar.');
					//eliminara la fila que se generó
					eliminarConceptos(id_fila_concepto);					
					borrarRenglon4(id_fila_concepto,"conceptos_table","rowCountConceptos","","idRenglonCpto","","dele","");					
				}else{
					alert("El concepto no puede ser asignado al itinerario "+itinerarioId+" debido a que ya cuenta con uno igual.");
					$("#itinerarios_cbx"+id_fila_concepto).val(0);
					limpiaValoresAnticipo(id_fila_concepto);					
				}
				getTotalAnticipo();
				return false;							
			}else if(itinerarioId == "Todos" && $("#Concepto"+i).val() == conceptoId){
				if(itinerarioId == 0){					
					alert('No puede tener mas de un concepto sin asignar.');
					//eliminara la fila que se generó
					eliminarConceptos(id_fila_concepto);					
					borrarRenglon4(id_fila_concepto,"conceptos_table","rowCountConceptos","","idRenglonCpto","","dele","");
				}else{
					alert("El concepto no puede ser asignado a Todos debido a que un itinerario ya tiene un concepto igual.");
					$("#itinerarios_cbx"+id_fila_concepto).val(0);
					limpiaValoresAnticipo(id_fila_concepto);
				}
				getTotalAnticipo();
				return false;
			}
		}		
	}
	//*******************************************************************
	if($("#itinerarios_cbx"+id_fila_concepto).val() == 0){		
		limpiaValoresAnticipo(id_fila_concepto);
		getTotalAnticipo();
	}
	//Validacion de misma fecha Alimentos
	if($("#Concepto"+id_fila_concepto).val() == 6){		
		//Eliminar el concepto para todos los itinerarios con los que se les relacione
		var tramite = $("#noTramite").val();
		var etapaActual = $("#etapaActual").val();
		eliminarConceptos(id_fila_concepto);
		$("#itinerarios_input"+id_fila_concepto).val(itinerarioId);
		//Resetear valores de fila de conceptos
		$("#Monto"+id_fila_concepto).val("0");
		$("#moneda"+id_fila_concepto).val("MXN");
		$("#Days"+id_fila_concepto).val("0");
		$("#MontoTotal"+id_fila_concepto).val("0");	
		$("#MontoEnPesos"+id_fila_concepto).val("0");		
		
		if(parseInt(detectorAlimentos()) > 1){			
			//Realiza la validacion de bloqueo			
			if(fechaConceptos(id_fila_concepto) == true){
				alert("No es posible solicitar anticipo para este concepto debido a que fue solicitado previamente para la misma fecha.");
				limpiaValoresAnticipo(id_fila_concepto);
				$("#itinerarios_cbx"+id_fila_concepto).val(0);				
				return false;
			}
		}
		//validacion del monto 80 USD		
		var politicaMXN = 80 * $("#tasaUSD").val();		
		if(fechaItinerarios(id_fila_concepto) == true){
			limpiaValoresAnticipo(id_fila_concepto);
			$("#itinerarios_input"+id_fila_concepto).val($("#itinerarios_cbx"+id_fila_concepto).val());
			$("#Monto"+id_fila_concepto).val(number_format(politicaMXN,2,".",","));
			diasAnticipos(id_fila_concepto);
			agregarConcepto(conceptoId, itinerarioId,$("#Days"+id_fila_concepto).val(),politicaMXN, 1);
			recalculaMontos2(conceptoId, id_fila_concepto);
			calcular_montos(conceptoId, id_fila_concepto, ($("#MontoTotal"+id_fila_concepto).val()).replace(/,/g,""), $("#moneda"+id_fila_concepto).val(), ($("#MontoEnPesos"+id_fila_concepto).val()).replace(/,/g,""));
			return false;		
		}		
	}			
	//*******************************************************************
	//Eliminar el concepto para todos los itinerarios con los que se les relacione
	var tramite = $("#noTramite").val();
	var etapaActual = $("#etapaActual").val();
	eliminarConceptos(id_fila_concepto);
	$("#itinerarios_input"+id_fila_concepto).val(itinerarioId);
	//Resetear valores de fila de conceptos
	$("#Monto"+id_fila_concepto).val("0.00");
	$("#moneda"+id_fila_concepto).val("MXN");
	$("#Days"+id_fila_concepto).val("0");
	$("#MontoTotal"+id_fila_concepto).val("0.00");
	$("#MontoEnPesos"+id_fila_concepto).val("0.00");

	//Obtener el nombre de las regiones de los itinerarios seleccionados
	var regionNombreArray = new Array();
	if(itinerarioId == "Todos"){
		//Calculo para todos los itinerarios
		var count_renglones = parseInt($("#solicitud_table>tbody>tr").length);
		for(var i=1;i<=count_renglones;i++){
			regionNombreArray[regionNombreArray.length] = $("#region"+i).val();
		}
		regionNombreArray = eliminateDuplicates(regionNombreArray);
	}else if(itinerarioId != "0"){
		//Calculo para un itinerario de una Solicitud de viaje
		regionNombreArray[regionNombreArray.length] = $("#region"+itinerarioId).val();
		regionNombreArray = eliminateDuplicates(regionNombreArray);
	}
	//Se guarda el id del concepto comoun array y en caso de ser comidas se guardan los respectivos ids de desayuno, comida y cena.
	var conceptoIdArray = new Array();
	conceptoIdArray[conceptoIdArray.length] = conceptoId;
	
	var region = $("#regionId"+itinerarioId).val();
	var cantidadEnMXN = 0;
	
	var politica = {
			'idUsuario' : idUsuario,
			'idConcepto' : conceptoId,
			'flujo': flujo,
			'region' : region
		};
	
	var montoLimitePolitica = obtenerPolitica(politica);
	
	var count_renglones = parseInt($("#solicitud_table>tbody>tr").length);
	if(itinerarioId == "Todos"){
		//Calculo para todos los itinerarios MULTIDESTINOS
		for(var i=1;i<=count_renglones;i++){
			var indice = $("#region"+i).val()+""+conceptoIdArray[0];
				//Se identifica las cotizaciones realizadas por el empleado : Hotel / Renta de auto.
				if(($("#CheckHEnviarAgencia"+i).val() == "false")||($("#CheckHEnviarAgencia"+i).val() == false)){
					cotizado_por_empleado_hosp = parseInt($("#costoHosp"+i).val().replace(',',''));							
				}else{
					cotizado_por_empleado_hosp = 0;
				}
				//Se registra que el valor de un hotel fue cotizado por el empleado.
				if(cotizado_por_empleado_hosp != 0 && conceptoId == 5){
					$("#montoCotizado"+id_fila_concepto).val(1);
				}
				
				if(($("#CheckTEnviarAgencia"+i).val() == "false")||($("#CheckTEnviarAgencia"+i).val() == false)){
					cotizado_por_empleado_auto = parseInt($("#costoAuto"+i).val().replace(',',''));
				}else{
					cotizado_por_empleado_auto = 0;
				}
				//Se registra que el valor de un auto fue cotizado por el empleado.						
				if(cotizado_por_empleado_auto != 0 && conceptoId == 10){
					$("#montoCotizado"+id_fila_concepto).val(1);
				}
				
				if( (conceptoId == 5) && (cotizado_por_empleado_hosp > 0) ){//hotel
					var cantidadEnMXN = cotizado_por_empleado_hosp/parseInt($("#noDias"+i).val());
				}else if( (conceptoId == 10) && (cotizado_por_empleado_auto > 0) ){//auto
					var cantidadEnMXN = cotizado_por_empleado_auto/parseInt($("#noDias"+i).val());
				}else{//demas conceptos
					if(montoLimitePolitica != 0)
						var cantidadEnMXN = montoLimitePolitica;
				}
				
			if(cantidadEnMXN == 0 || cantidadEnMXN == '0'){
				//Calculo de un concepto SIN monto maximo
				calculo_de_conceptosTodos1(conceptoId, i, id_fila_concepto);
				calcular_montos(conceptoId, id_fila_concepto, ($("#MontoTotal"+id_fila_concepto).val()).replace(/,/g,""), $("#moneda"+id_fila_concepto).val(), ($("#MontoEnPesos"+id_fila_concepto).val()).replace(/,/g,""));
			}else{
				//Calculo de un concepto CON monto maximo
				calculo_de_conceptosTodos2(conceptoId, i, id_fila_concepto, cantidadEnMXN);
				recalculaMontos2(conceptoId, id_fila_concepto);
				calcular_montos(conceptoId, id_fila_concepto, ($("#MontoTotal"+id_fila_concepto).val()).replace(/,/g,""), $("#moneda"+id_fila_concepto).val(), ($("#MontoEnPesos"+id_fila_concepto).val()).replace(/,/g,""));
			}
		}
	}else if(itinerarioId != "0"){
		//Calculo para un itinerario de una Solicitud de viaje
		var indice = $("#region"+itinerarioId).val()+""+conceptoIdArray[0];
		//Se identifica las cotizaciones realizadas por el empleado : Hotel / Renta de auto.									
			if(($("#CheckHEnviarAgencia"+itinerarioId).val() == "false")||($("#CheckHEnviarAgencia"+itinerarioId).val() == false)){						
				cotizado_por_empleado_hosp = parseInt($("#costoHosp"+itinerarioId).val().replace(',',''));
			}else{
				cotizado_por_empleado_hosp = 0;
			}
			
			//Se registra que el valor de un hotel fue cotizado por el empleado.
			if(cotizado_por_empleado_hosp != 0 && conceptoId == 5){						
				$("#montoCotizado"+id_fila_concepto).val(1);
			}
			
			
			if(($("#CheckTEnviarAgencia"+itinerarioId).val() == "false") || ($("#CheckTEnviarAgencia"+itinerarioId).val() == false)){
				cotizado_por_empleado_auto = parseInt($("#costoAuto"+itinerarioId).val().replace(',',''));
			}else{
				cotizado_por_empleado_auto = 0;
			}
			
			//Se registra que el valor de un auto fue cotizado por el empleado.
			if(cotizado_por_empleado_auto != 0 && conceptoId == 10){						
				$("#montoCotizado"+id_fila_concepto).val(1);
			}
			
			if( (conceptoId == 5) && (cotizado_por_empleado_hosp > 0) ){//hotel
				var cantidadEnMXN = cotizado_por_empleado_hosp/parseInt($("#noDias"+itinerarioId).val());
			}else if( (conceptoId == 10) && (cotizado_por_empleado_auto > 0) ){//auto
				var cantidadEnMXN = cotizado_por_empleado_auto/parseInt($("#noDias"+itinerarioId).val());						
			}else{//demas conceptos
				if(montoLimitePolitica != 0){
					var cantidadEnMXN = montoLimitePolitica;
				}
			}
		
		if(cantidadEnMXN == 0 || cantidadEnMXN == '0'){			
			//Calculo de un concepto SIN monto maximo
			calculo_de_concepto1(conceptoId, itinerarioId, id_fila_concepto);
			calcular_montos(conceptoId, id_fila_concepto, ($("#MontoTotal"+id_fila_concepto).val()).replace(/,/g,""), $("#moneda"+id_fila_concepto).val(), ($("#MontoEnPesos"+id_fila_concepto).val()).replace(/,/g,""));
		}else{
			//Calculo de un concepto CON monto maximo
			calculo_de_concepto2(conceptoId, itinerarioId, id_fila_concepto, cantidadEnMXN);
			recalculaMontos2(conceptoId, id_fila_concepto);
			calcular_montos(conceptoId, id_fila_concepto, ($("#MontoTotal"+id_fila_concepto).val()).replace(/,/g,""), $("#moneda"+id_fila_concepto).val(), ($("#MontoEnPesos"+id_fila_concepto).val()).replace(/,/g,""));
		}
	}
	
}
//------------- Funciones especiales para el calculo de conceptos: Viaje multidestinos = TODOS
function calculo_de_conceptosTodos1(conceptoId,itinerarioId,id_fila_concepto){
	//SIN MAXIMO
	var no_dias = 0;
	no_dias = getDiasMT();
	$("#Days"+id_fila_concepto).val(parseInt(no_dias));	
	monto_max_x_dia = 0;
	monto_max_divisa = 1; //Ninguna divisa
	agregarConcepto(conceptoId, itinerarioId, no_dias, monto_max_x_dia, monto_max_divisa);
}
function calculo_de_conceptosTodos2(conceptoId, itinerarioId, id_fila_concepto, monto_maximo_en_mxn){
	//CON MAXIMO	
	var no_dias = 0;
	var montoTotalValor = 0;
	no_dias = getDiasMT();
	
	$("#Days"+id_fila_concepto).val(parseInt(no_dias));
	
	aux_montoTotal = parseFloat(($("#MontoTotal"+id_fila_concepto).val()).replace(/,/g,""));	
	montoTotalValor = parseFloat(aux_montoTotal)+parseFloat(monto_maximo_en_mxn*no_dias);	
	$("#MontoTotal"+id_fila_concepto).val(montoTotalValor);
	var calculoMonto = parseFloat($("#MontoTotal"+id_fila_concepto).val()/$("#Days"+id_fila_concepto).val());
	monto_max_x_dia_actual = truncaMonto(calculoMonto);
	if(isNaN(monto_max_x_dia_actual))monto_max_x_dia_actual = 0;	
	$("#Monto"+id_fila_concepto).val(number_format((monto_max_x_dia_actual),2,".",","));
	
	monto_max_x_dia = monto_maximo_en_mxn;
	monto_max_divisa = 1; //MXN
	agregarConcepto(conceptoId, itinerarioId, no_dias, monto_max_x_dia, monto_max_divisa);
}
//------------END
function calculo_de_concepto1(conceptoId, itinerarioId, id_fila_concepto){
	//SIN MAXIMO
	
	var no_dias = 0;
	var no_dias_r = 0;
	var etapa_actual = $("#etapaActual").val();
	var tramite = $("#noTramite").val();	
	var tipo_viaje_pasaje = $("#select_tipo_viaje_pasaje>option").eq($("#select_tipo_viaje_pasaje").val()).text();
		
	no_dias = get_dias(itinerarioId);
	
	aux_dias = parseInt($("#Days"+id_fila_concepto).val());			

	if(tipo_viaje_pasaje == "Redondo" && conceptoId != 5 && ($("#salida"+itinerarioId).val() != $("#fechaLlegada"+itinerarioId).val()) ){
		no_dias_r = parseInt(no_dias) + 1;
	}else{
		no_dias_r = no_dias;
	}
	
		$("#Days"+id_fila_concepto).val(parseInt(aux_dias+no_dias_r));
		monto_max_x_dia = 0;
		monto_max_divisa = 1; //Ninguna divisa
		agregarConcepto(conceptoId, itinerarioId, no_dias, monto_max_x_dia, monto_max_divisa);		
	
}
function calculo_de_concepto2(conceptoId, itinerarioId, id_fila_concepto, monto_maximo_en_mxn){
	//CON MAXIMO
	
	var no_dias = 0;
	var no_dias_r = 0;
	var montoTotalValor = 0;
	var tipo_viaje_pasaje = $("#select_tipo_viaje_pasaje>option").eq($("#select_tipo_viaje_pasaje").val()).text();
	
	
	no_dias = get_dias(itinerarioId);
	aux_dias = parseInt($("#Days"+id_fila_concepto).val());
	
	if(tipo_viaje_pasaje == "Redondo" && conceptoId != 5 && ($("#salida"+itinerarioId).val() != $("#fechaLlegada"+itinerarioId).val()) ){
		no_dias_r = parseInt(no_dias) + 1;
	}else{
		no_dias_r = no_dias;
	}
	
	$("#Days"+id_fila_concepto).val(parseInt(aux_dias+no_dias_r));
	
	if(tipo_viaje_pasaje == "Redondo" && conceptoId == 6){
		
		aux_montoTotal = parseFloat(($("#MontoTotal"+id_fila_concepto).val()).replace(/,/g,""));
		
		montoTotalValor = parseFloat(aux_montoTotal)+parseFloat(monto_maximo_en_mxn*no_dias_r);
		$("#MontoTotal"+id_fila_concepto).val(montoTotalValor);
		var calculoMonto = parseFloat($("#MontoTotal"+id_fila_concepto).val()/$("#Days"+id_fila_concepto).val());
		monto_max_x_dia_actual = truncaMonto(calculoMonto);
		if(isNaN(monto_max_x_dia_actual))monto_max_x_dia_actual = 0;	
		$("#Monto"+id_fila_concepto).val(number_format((monto_max_x_dia_actual),2,".",","));
		
		monto_max_x_dia = monto_maximo_en_mxn;
		monto_max_divisa = 1; //MXN
		agregarConcepto(conceptoId, itinerarioId, no_dias_r, monto_max_x_dia, monto_max_divisa);
	}else{
		aux_montoTotal = parseFloat(($("#MontoTotal"+id_fila_concepto).val()).replace(/,/g,""));
		montoTotalValor = parseFloat(aux_montoTotal)+parseFloat(monto_maximo_en_mxn);
		$("#MontoTotal"+id_fila_concepto).val(montoTotalValor);
		
		var calculoMonto = parseFloat($("#MontoTotal"+id_fila_concepto).val());
		monto_max_x_dia_actual = truncaMonto(calculoMonto);
		
		if(isNaN(monto_max_x_dia_actual))monto_max_x_dia_actual = 0;	
		$("#Monto"+id_fila_concepto).val(number_format((monto_max_x_dia_actual),2,".",","));
		if(tipo_viaje_pasaje == "Redondo" ){
			no_dias = parseInt(no_dias) + 1;
		}
		monto_max_x_dia = monto_maximo_en_mxn;
		monto_max_divisa = 1; //MXN
		agregarConcepto(conceptoId, itinerarioId, no_dias, monto_max_x_dia, monto_max_divisa);
	}
	
}
function get_dias(itinerarioId){
	var count_itinerarios = parseInt($("#solicitud_table>tbody>tr").length);
	var tipo_viaje_pasaje = $("#select_tipo_viaje_pasaje>option").eq($("#select_tipo_viaje_pasaje").val()).text();
	if(tipo_viaje_pasaje == "Sencillo"){
		return 1;
	}if(tipo_viaje_pasaje == "Redondo"){
		var dias = 0;
		fecha1 = $("#salida"+itinerarioId).val();
		fecha2 = $("#fechaLlegada"+itinerarioId).val();
		dias = days_between(fecha1,fecha2);
		
		if(dias == 0){
			dias = 1;
		}else{
			dias = dias;
		}
		return dias;
	}if(tipo_viaje_pasaje == "Multidestinos"){
			if(itinerarioId == count_itinerarios){
				//alert("LAST itinerario");
				return 1;
			}else if(itinerarioId >= 1){		
				fecha1 = $("#salida"+itinerarioId).val();
				fecha2 = $("#salida"+parseInt(parseInt(itinerarioId)+1)).val();
				dias = days_between(fecha1,fecha2);	
				
				if(dias == 0){
					dias = 1;
				}else{
					dias = dias;
				}
				return dias;
			}			
	}
}

function getDiasMT(){
	var count_itinerarios = parseInt($("#solicitud_table>tbody>tr").length);
	var dias = 0;
	fecha1 = $("#salida"+1).val();	
	fecha2 = $("#salida"+count_itinerarios).val();	
	dias = days_between(fecha1,fecha2);	
	if(dias == 0){
		dias = 1;
	}else{
		dias = dias + 1;
	}
	
	return dias;
}

function show_msg(){
	alert('El concepto no puede ser agregado ya que todos los itinerarios cuentan con este concepto.');
}

function show_msg2(){
	alert('Existen conceptos sin asignar a un itinerario,\nno puede agregar uno nuevo.');
}

// Da de alta un concepto de gastos
function construyePartidaConcepto2(){
	
	var frm=document.detallesItinerarios;
	var conceptoId=frm.select_concepto.value;
	var id = parseInt($("#conceptos_table>tbody>tr").length)+1;

	//Checar si el concepto se encuentra en todos los itinerarios
	var count_itinerarios = parseInt($("#solicitud_table>tbody>tr").length);
	var count_conceptos = parseInt($("#conceptos_table>tbody>tr").length);
	var count_aux = 0;
	for(var i=1;i<=count_conceptos;i++){
		if($("#Concepto"+i).val() == conceptoId){
			if($("#itinerarios_cbx"+i).val() == "Todos"){
				return show_msg();
			}else if($("#itinerarios_cbx"+i).val() != "0"){
				count_aux++;
			}else if($("#itinerarios_cbx"+i).val() == "0"){
				return show_msg2();
			}
		}
	}
	if(count_aux == count_itinerarios){
		return show_msg();
	}
	//***********************************************************
	
	//Reiniciamos el combo de itinerarios
	var tipo_viaje = $("#select_tipo_viaje_pasaje option:selected").val();	
	if(tipo_viaje == 3 && ($("#select_concepto").val() == 5 || $("#select_concepto").val() == 6  || $("#select_concepto").val() == 10 )){		
		var_combo_itinerarios = retiraConceptoCombo(); 
	}else{
		crear_combo_itinerarios();
	}
		
	
	// Crea la nueva fila con todos los valores en blanco
	var nuevaFila='<tr>';
	nuevaFila+="<td><input type='hidden' name='idsDeFila"+id+"' id=idsDeFila"+id+" value='"+id+"' readonly='readonly'/><div name='idRenglonCpto"+id+"' id=idRenglonCpto"+id+">"+id+"</div></td>";
	nuevaFila+="<td><select name='itinerarios_cbx"+id+"' id='itinerarios_cbx"+id+"' onchange='calculo_de_conceptos("+conceptoId+", this.value, this.id);' style='width:60'>"+var_combo_itinerarios+"</select><input type='hidden' name='itinerarios_input"+id+"' id='itinerarios_input"+id+"' value='0' readonly='readonly'/></td>";
	nuevaFila+="<td>"+frm.select_concepto.options[frm.select_concepto.selectedIndex].text+"<input type='hidden' name='Concepto"+id+"' id='Concepto"+id+"' value='"+conceptoId+"' readonly='readonly' /></td>";
		nuevaFila+="<td align='right'><input type='text' name='Monto"+id+"' id='Monto"+id+"' value='0'  onkeypress='return validaNum (event)' style='border-color:#FFFFFF; text-align:right' onblur='recalculaMontos2("+conceptoId+", this.id); mouseOut(this.id);'/></td>";
	nuevaFila+="<td align='right'><select disabled name='moneda"+id+"' id='moneda"+id+"' onchange='recalculaMontos2("+conceptoId+", this.id);'><option value='MXN' selected='selected'>MXN</option><option value='USD'>USD</option><option value='EUR'>EUR</option></select></td>";
		nuevaFila+="<td align='right'><input type='text' name='Days"+id+"' id='Days"+id+"' value='0' onkeypress='validaCeros(this.value,this.id); return validaNum (event);' onKeyUp='validaDias(this.id,this.value,"+conceptoId+");' onBlur='recalculaMontos2("+conceptoId+", this.id); onBlurDias(this.id,this.value);' style='border-color:#FFFFFF; text-align:center' /></td>";
	nuevaFila+="<td align='right'><input disabled type='text' name='MontoTotal"+id+"' id='MontoTotal"+id+"' value='0' readonly='readonly' style='border-color:#FFFFFF; text-align:right'/></td>";
	nuevaFila+="<td align='right'><input disabled type='text' name='MontoEnPesos"+id+"' id='MontoEnPesos"+id+"' value='0' readonly='readonly' style='border-color:#FFFFFF; text-align:right'/></td>";
	nuevaFila+="<td><div align='center'><img class='eliminar' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+id+"dele' id='"+id+"dele'  onclick='eliminarConceptos(this.id);recalculaTotalAnticipo(this.id); borrarRenglon4(this.id,\"conceptos_table\",\"rowCountConceptos\",\"\",\"idRenglonCpto\",\"\",\"dele\",\"\");getTotalAnticipo();'  style='cursor:pointer' /></div></td>";
	nuevaFila+="<input type='hidden' name='exedePolitica"+id+"' id=exedePolitica"+id+" value='0' readonly='readonly'/>";
	nuevaFila+="<input type='hidden' name='montoCotizado"+id+"' id=montoCotizado"+id+" value='0' readonly='readonly'/>";
	nuevaFila+="</tr>";
	
	$("#conceptos_table").append(nuevaFila);
}

function calcular_porcentajes(conceptoId, id_fila_concepto){
	sumaMontosTotales = getSumaMontoMaxTotalPolitica(conceptoId, id_fila_concepto);
	var count_itinerarios = parseInt($("#solicitud_table>tbody>tr").length);
	var i = $("#itinerarios_input"+id_fila_concepto).val();
	if(i == "Todos"){
		if(parseFloat(sumaMontosTotales) == 0){//SIN MAXIMO
			for(var i=1;i<=count_itinerarios;i++){ //Recorro la tabla: solicitud_table
				var count_renglones = parseInt($("#concepto_table"+i+">tbody>tr").length);
				for(var j=1;j<=count_renglones;j++){
					if($("#id_cpto_"+i+"_"+j).val() == conceptoId){
						$("#porcentaje_"+i+"_"+j).val(parseFloat(1/document.getElementById('area_tablas_conceptos_div').childNodes.length));
					}
				}
			}
		}else{//CON MAXIMO
			for(var i=1;i<=count_itinerarios;i++){ //Recorro la tabla: solicitud_table
				var count_renglones = parseInt($("#concepto_table"+i+">tbody>tr").length);
				for(var j=1;j<=count_renglones;j++){
					if($("#id_cpto_"+i+"_"+j).val() == conceptoId){
						$("#porcentaje_"+i+"_"+j).val(parseFloat($("#mto_max_total_"+i+"_"+j).val()/sumaMontosTotales));
					}
				}
			}
		}
	}else if(i != "0"){
		if(parseFloat(sumaMontosTotales) == 0){//SIN MAXIMO
			var count_renglones = parseInt($("#concepto_table"+i+">tbody>tr").length);
			for(var j=1;j<=count_renglones;j++){
				if($("#id_cpto_"+i+"_"+j).val() == conceptoId){
					$("#porcentaje_"+i+"_"+j).val(1);
				}
			}
		}else{//CON MAXIMO
			var count_renglones = parseInt($("#concepto_table"+i+">tbody>tr").length);
			for(var j=1;j<=count_renglones;j++){
				if($("#id_cpto_"+i+"_"+j).val() == conceptoId){
					$("#porcentaje_"+i+"_"+j).val(parseFloat($("#mto_max_total_"+i+"_"+j).val()/sumaMontosTotales));
				}
			}
		}
	}
}
function calcular_montos(conceptoId, id_fila_concepto, montoTotalMO, moneda, montoTotalEnMXN){
	
	var count_itinerarios = parseInt($("#solicitud_table>tbody>tr").length);
	var i = $("#itinerarios_input"+id_fila_concepto).val();
	var no_dias = $("#Days"+id_fila_concepto).val();
	
	if(i == "Todos"){
		for(var i=1;i<=count_itinerarios;i++){ //Recorro la tabla: solicitud_table
			var count_renglones = parseInt($("#concepto_table"+i+">tbody>tr").length);
			for(var j=1;j<=count_renglones;j++){
				if($("#id_cpto_"+i+"_"+j).val() == conceptoId){
					var calculo1 = parseFloat(montoTotalMO / count_itinerarios);
					$("#montototalmo_u_"+i+"_"+j).val(truncaMonto(calculo1));
					var calculo2 = parseFloat((montoTotalMO / no_dias)) / count_itinerarios;
					$("#monto_u_"+i+"_"+j).val(truncaMonto(calculo2));
					$("#divisa_u_"+i+"_"+j).val(moneda);
					var calculo3 = parseFloat(montoTotalEnMXN / count_itinerarios);
					$("#montomxn_u_"+i+"_"+j).val(truncaMonto(calculo3));
					$("#no_dias_"+i+"_"+j).val(no_dias / count_itinerarios);
				}
			}
		}
	}else if(i != "0"){
			var count_renglones = parseInt($("#concepto_table"+i+">tbody>tr").length);
			for(var j=1;j<=count_renglones;j++){
				if($("#id_cpto_"+i+"_"+j).val() == conceptoId){
					$("#montototalmo_u_"+i+"_"+j).val(parseFloat(montoTotalMO));
					var calculo = parseFloat(montoTotalMO / no_dias);
					$("#monto_u_"+i+"_"+j).val(truncaMonto(calculo));
					$("#divisa_u_"+i+"_"+j).val(moneda);
					$("#montomxn_u_"+i+"_"+j).val(parseFloat(montoTotalEnMXN));
					$("#no_dias_"+i+"_"+j).val(no_dias);
				}
			}
	}
}

/***************************************************************************/
//VARIABLE GLOBAL
var textoAnterior = '';
var conceptoExcedePoliticas = false;

//ESTA FUNCIÓN DEFINE LAS REGLAS DEL JUEGO
function cumpleReglas(simpleTexto){
	//la pasamos por una poderosa expresión regular
	var expresion = new RegExp("^(|([0-9]{1,30}(\\.([0-9]{1,2})?)?))$");

	//si pasa la prueba, es válida
	if(expresion.test(simpleTexto))
		return true;
	return false;
}//end function checaReglas

//ESTA FUNCIÓN REVISA QUE TODO LO QUE SE ESCRIBA ESTÉ EN ORDEN
function revisaCadena(textItem){
	//si comienza con un punto, le agregamos un cero
	if(textItem.value.substring(0,1) == '.') 
		textItem.value = '0' + textItem.value;

	//si no cumples las reglas, no te dejo escribir
	if(!cumpleReglas(textItem.value))
		textItem.value = textoAnterior;
	else //todo en orden
		textoAnterior = textItem.value;
}//end function revisaCadena
/***************************************************************************/

function crear_divs_area_conceptos(){
	//Se cran los divs que contendran la tabla y contadores de los conceptos
	var nuevo_div = document.createElement("div");
	document.getElementById('area_tablas_conceptos_div').appendChild(nuevo_div);
	var nuevo_div2 = document.createElement("div");
	document.getElementById('area_counts_conceptos_div').appendChild(nuevo_div2);
}

function crear_tabla_concepto(itinerarioId){
	var cadena ="<table id='concepto_table"+itinerarioId+"' class='tablesorter' cellspacing='1' style='visibility:hidden;display:none'>";
		cadena += "<thead>"
			cadena += "<tr>"
			cadena += "<th width='2%'>No.</th>";
			cadena += "<th width='3%'>Itinerario</th>";
			cadena += "<th width='3%'>Concepto</th>";
			cadena += "<th width='3%'>Dias</th>";
			cadena += "<th width='5%'>Maximo por dia</th>";
			cadena += "<th width='5%'>Maximo total</th>";
			cadena += "<th width='3%'>Divisa</th>";
			cadena += "<th width='3%'>Porcentaje</th>";
			cadena += "<th width='5%'>Monto_u</th>";
			cadena += "<th width='3%'>Divisa_u</th>";
			cadena += "<th width='5%'>MontoTotalMO_u</th>";
			cadena += "<th width='5%'>MontoMXN_u</th>";
			cadena += "<th width='1%'>Eliminar</th>";
			cadena += "</tr>"; 
		cadena += "</thead>";
		cadena += "<tbody>";
		cadena += "</tbody>";
	cadena += "</table>";
	var num_childNodes_default = 0;
		
	document.getElementById('area_tablas_conceptos_div').childNodes[parseInt(itinerarioId-1+num_childNodes_default)].innerHTML = cadena;

	//Se agregan rowCount_concepto y rowDel_concepto correspondientes al numero de itinerario.
	var cadena1 = "";
	cadena1 += "<input type='hidden' id='rowCount_concepto"+itinerarioId+"' name='rowCount_concepto"+itinerarioId+"' value='0' readonly='readonly'/>";
	cadena1 += "<input type='hidden' id='rowDel_concepto"+itinerarioId+"' name='rowDel_concepto"+itinerarioId+"' value='0' readonly='readonly'/>"

	document.getElementById('area_counts_conceptos_div').childNodes[parseInt(itinerarioId-1+num_childNodes_default)].innerHTML = cadena1;
}

function agregarConcepto(conceptoId, itinerarioId, no_dias, monto_max_x_dia, monto_max_divisa){
	var no_de_itinerario = itinerarioId;
	var id = parseInt($("#concepto_table"+itinerarioId+">tbody >tr").length);
		
	if(isNaN(id)){ 
		id=1; 
	}else{ 
		id+=parseInt(1); 
	}
	var nuevaFila='<tr>';
	nuevaFila+="<td>"+"<div id='no_cpto_"+no_de_itinerario+"_"+id+"' name='no_cpto_"+no_de_itinerario+"_"+id+"'>"+id+"</div>"+"<input type='hidden' name='row_cpto_"+no_de_itinerario+"_"+id+"' id='row_cpto_"+no_de_itinerario+"_"+id+"' value='"+id+"' readonly='readonly' /></td>";
	nuevaFila+="<td><input name='no_iti_"+no_de_itinerario+"_"+id+"' id='no_iti_"+no_de_itinerario+"_"+id+"' value='"+no_de_itinerario+"' readonly='readonly' /></td>"; 
	nuevaFila+="<td><input name='id_cpto_"+no_de_itinerario+"_"+id+"' id='id_cpto_"+no_de_itinerario+"_"+id+"' value='"+conceptoId+"' readonly='readonly' /></td>"; 
	nuevaFila+="<td><input name='no_dias_"+no_de_itinerario+"_"+id+"' id='no_dias_"+no_de_itinerario+"_"+id+"' value='"+no_dias+"' readonly='readonly' /></td>"; 
	nuevaFila+="<td><input name='mto_max_x_dia_"+no_de_itinerario+"_"+id+"' id='mto_max_x_dia_"+no_de_itinerario+"_"+id+"' value='"+monto_max_x_dia+"' readonly='readonly' /></td>"; 
	nuevaFila+="<td><input name='mto_max_total_"+no_de_itinerario+"_"+id+"' id='mto_max_total_"+no_de_itinerario+"_"+id+"' value='"+(no_dias*monto_max_x_dia)+"' readonly='readonly' /></td>"; 
	nuevaFila+="<td><input name='divisa_"+no_de_itinerario+"_"+id+"' id='divisa_"+no_de_itinerario+"_"+id+"' value='"+monto_max_divisa+"' readonly='readonly' /></td>";
	nuevaFila+="<td><input name='porcentaje_"+no_de_itinerario+"_"+id+"' id='porcentaje_"+no_de_itinerario+"_"+id+"' value='0' readonly='readonly' /></td>";

	nuevaFila+="<td><input name='monto_u_"+no_de_itinerario+"_"+id+"' id='monto_u_"+no_de_itinerario+"_"+id+"' value='0' readonly='readonly' /></td>";
	nuevaFila+="<td><input name='divisa_u_"+no_de_itinerario+"_"+id+"' id='divisa_u_"+no_de_itinerario+"_"+id+"' value='0' readonly='readonly' /></td>";
	nuevaFila+="<td><input name='montototalmo_u_"+no_de_itinerario+"_"+id+"' id='montototalmo_u_"+no_de_itinerario+"_"+id+"' value='0' readonly='readonly' /></td>";
	nuevaFila+="<td><input name='montomxn_u_"+no_de_itinerario+"_"+id+"' id='montomxn_u_"+no_de_itinerario+"_"+id+"' value='0' readonly='readonly' /></td>";
	
	nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+id+"_"+no_de_itinerario+"_delCpto' id='"+id+"_"+no_de_itinerario+"_delCpto' onmousedown='borrarRenglon2(this.id,\"concepto_table"+no_de_itinerario+"\",\"rowCount_concepto"+no_de_itinerario+"\",\"rowDel_concepto"+no_de_itinerario+"\",\"no_cpto\",\"\",\"delCpto\",\"\");' style='cursor:pointer;' /></div></td>";
	nuevaFila+= '</tr>';
	
	$("#concepto_table"+no_de_itinerario).append(nuevaFila);
	$("#rowCount_concepto"+no_de_itinerario).val(id);
}

function eliminarConceptos(id_fila_concepto){
	var re = /^[0-9]+|[0-9]+$/;
	id_fila_concepto = (""+id_fila_concepto).match(re);
	
	var conceptoId = $("#Concepto"+id_fila_concepto).val();

	var count_itinerarios = parseInt($("#solicitud_table>tbody>tr").length);
	var i = $("#itinerarios_input"+id_fila_concepto).val();
	if(i == "Todos"){
		for(var i=1;i<=count_itinerarios;i++){ //Recorro las tablas: concepto_table
			var count_renglones = parseInt($("#concepto_table"+i+">tbody>tr").length);
			for(var j=1;j<=count_renglones;j++){
				if($("#id_cpto_"+i+"_"+j).val() == conceptoId){
					borrarRenglon2(j,"concepto_table"+i,"rowCount_concepto"+i,"rowDel_concepto"+i,"no_cpto","","delCpto","");
				}
			}
		}
	}else if(i != "0"){
			var count_renglones = parseInt($("#concepto_table"+i+">tbody>tr").length);
			for(var j=1;j<=count_renglones;j++){
				if($("#id_cpto_"+i+"_"+j).val() == conceptoId){
					borrarRenglon2(j,"concepto_table"+i,"rowCount_concepto"+i,"rowDel_concepto"+i,"no_cpto","","delCpto","");
				}
			}
	}
	
	eliminaMensajeExcede(id_fila_concepto);
}

function eliminaMensajeExcede(id_fila_concepto){
	muestraErrores();	
	$('#capaWarning div').each(
		    function(element){
		    	if($(this).attr('id') == id_fila_concepto){
		    		$(this).html("");
		    	}
		    }
		 );
}

function getSumaMontoMaxTotalPolitica(conceptoId, id_fila_concepto){
	var SumaMontosTotales = 0;
	var count_itinerarios = parseInt($("#solicitud_table>tbody>tr").length);
	var i = $("#itinerarios_input"+id_fila_concepto).val();
	if(i == "Todos"){
		for(var i=1;i<=count_itinerarios;i++){ //Recorro las tablas: concepto_table
			var count_renglones = parseInt($("#concepto_table"+i+">tbody>tr").length);
			for(var j=1;j<=count_renglones;j++){
				if($("#id_cpto_"+i+"_"+j).val() == conceptoId){
					SumaMontosTotales += parseFloat($("#mto_max_total_"+i+"_"+j).val());
				}
			}
		}
	}else if(i != "0"){
			var count_renglones = parseInt($("#concepto_table"+i+">tbody>tr").length);
			for(var j=1;j<=count_renglones;j++){
				if($("#id_cpto_"+i+"_"+j).val() == conceptoId){
					SumaMontosTotales += parseFloat($("#mto_max_total_"+i+"_"+j).val());
				}
			}
	}
	return SumaMontosTotales;
}

function getMontoComidas(horaSalida,horaLlegada,noDias,montosMXNComida){

	var tasaDiaria    = montosMXNComida[0];
	var montoDesayuno = montosMXNComida[1];
	var montoComida   = montosMXNComida[2];
	var montoCena     = montosMXNComida[3];

	var mañana = "Mañana [6:00-12:00]";
	var tarde  = "Tarde [12:00-18:00]";
	var noche  = "Noche [18:00-00:00]";

	var tipoPasaje = $("#select_tipo_viaje_pasaje>option").eq($("#select_tipo_viaje_pasaje").val()).text();

	var DiasV = noDias;

	//Se saca el monto del primer dia, siempre se hace.
	switch(horaSalida){
		case mañana:
			montoPrimerDia = tasaDiaria;
			break;
		case tarde:
			montoPrimerDia = tasaDiaria - montoDesayuno;
			break;
		case noche:
			montoPrimerDia = montoCena;
			break;
	}
	
	//Dependiendo que tipo de pasaje sea, se sacan los montos de los dias intermedios y del dia de llegada.
	if(tipoPasaje == "Sencillo"){
		montoTotal = montoPrimerDia;
	}else if((tipoPasaje == "Redondo" || tipoPasaje == "Multidestinos") && DiasV >= 2){
		//Se saca el monto del ultimo dia, siempre que sea pasaje redondo o con escalas.
		switch(horaLlegada){
			case mañana:
				montoUltimoDia = montoDesayuno;
				break;
			case tarde:
				montoUltimoDia = parseFloat(tasaDiaria) - parseFloat(montoCena);
				break;
			case noche:
				montoUltimoDia = tasaDiaria;
				break;
		}
		
		//Se saca el monto de los dias intermedios
		diasIntermedios = DiasV-2;
		montoDiasIntermedios = parseFloat(tasaDiaria) * parseFloat(diasIntermedios);

		montoTotal = parseFloat(montoPrimerDia) + parseFloat(montoDiasIntermedios) + parseFloat(montoUltimoDia);
	}else if((tipoPasaje == "Redondo" || tipoPasaje == "Multidestinos") && DiasV < 2){
		switch(horaSalida){
			case mañana:
				switch(horaLlegada){
					case mañana:
						montoTotal = montoDesayuno;
						break;
					case tarde:
						montoTotal = parseFloat(tasaDiaria) + parseFloat(montoCena);
						break;
					case noche:
						montoTotal = tasaDiaria;
						break;
				}
				break;
			case tarde:
				switch(horaLlegada){
					case mañana:
						//Error
						break;
					case tarde:
						montoTotal = montoComida;
						break;
					case noche:
						montoTotal = parseFloat(tasaDiaria) - parseFloat(montoDesayuno);
						break;
				}
				break;
			case noche:
				switch(horaLlegada){
					case mañana:
						//Error
						break;
					case tarde:
						//Error
						break;
					case noche:
						montoTotal = montoCena;
						break;
				}
				break;
		}
	}
	return parseFloat(montoTotal);
}

function eliminarCalculosDeConceptos(){
	$("#conceptos_table>tbody>tr").remove();
	var count_itinerarios = parseInt($("#solicitud_table>tbody>tr").length);
	for(var i=1;i<=count_itinerarios;i++){ //Recorro las tablas: concepto_table
		$("#concepto_table"+i+">tbody>tr").remove();
		$("#rowCount_concepto"+i).val(0);
		$("#rowDel_concepto"+i).val(0);
	}	
}

function getTotalAnticipo(){
	var count_conceptos = parseFloat($("#conceptos_table>tbody>tr").length);
	var totalAnticipo = 0;
	for(var i=1;i<=count_conceptos;i++){
		totalAnticipo = totalAnticipo + parseFloat($("#MontoEnPesos"+i).val().replace(/,/g,""));
	}
	$("#anticipoC").val(number_format((totalAnticipo),2,".",","));
	calculoTotal_Anticipo();
}

// Da de alta un concepto de gastos de la DB
function construyePartidaConcepto2FromDB(datos){
	var conceptoId = datos['svc_detalle_concepto'];
	var diasAnticipo = datos['svc_dias_itinerario'];
	var id = parseInt($("#conceptos_table>tbody>tr").length)+1;

	//Checar si el concepto se encuentra en todos los itinerarios
	var count_itinerarios = parseInt($("#solicitud_table>tbody>tr").length);
	var count_conceptos = parseInt($("#conceptos_table>tbody>tr").length);
	var count_aux = 0;
	for(var i=1;i<=count_conceptos;i++){
		if($("#Concepto"+i).val() == conceptoId){
			if($("#itinerarios_cbx"+i).val() == "Todos"){
				return show_msg();
			}else if($("#itinerarios_cbx"+i).val() != "0"){
				count_aux++;
			}else if($("#itinerarios_cbx"+i).val() == "0"){
				return show_msg2();
			}
		}
	}
	if(count_aux == count_itinerarios){
		return show_msg();
	}
	//***********************************************************
	
	//Reiniciamos el combo de itinerarios
	var tipo_viaje = $("#select_tipo_viaje_pasaje option:selected").val();	
	if(tipo_viaje == 3 && (conceptoId == 5 || conceptoId == 6  || conceptoId == 10 )){		
		var_combo_itinerarios = retiraConceptoCombo(); 
	}else{
		crear_combo_itinerarios();
	}
	
	// Crea la nueva fila con todos los valores en blanco
	var nuevaFila='<tr>';
	nuevaFila+="<td><input type='hidden' name='idsDeFila"+id+"' id=idsDeFila"+id+" value='"+id+"' readonly='readonly'/><div name='idRenglonCpto"+id+"' id=idRenglonCpto"+id+">"+id+"</div></td>";
	nuevaFila+="<td><select name='itinerarios_cbx"+id+"' id='itinerarios_cbx"+id+"' onchange='calculo_de_conceptos("+conceptoId+", this.value, this.id);' style='width:60'>"+var_combo_itinerarios+"</select><input type='hidden' name='itinerarios_input"+id+"' id='itinerarios_input"+id+"' value='0' readonly='readonly'/></td>";
	nuevaFila+="<td>"+datos['cp_concepto']+"<input type='hidden' name='Concepto"+id+"' id='Concepto"+id+"' value='"+conceptoId+"' readonly='readonly' /></td>";
	nuevaFila+="<td align='right'><input type='text' name='Monto"+id+"' id='Monto"+id+"' value='0'  onkeypress='return validaNum(event);' style='border-color:#FFFFFF; text-align:right' onblur='recalculaMontos2("+conceptoId+", this.id); mouseOut(this.id);'/></td>";
	nuevaFila+="<td align='right'><select name='moneda"+id+"' id='moneda"+id+"' onchange='recalculaMontos2("+conceptoId+", this.id);'><option value='MXN' selected='selected'>MXN</option><option value='USD'>USD</option><option value='EUR'>EUR</option></select></td>";
	nuevaFila+="<td align='right'><input type='text' name='Days"+id+"' id='Days"+id+"' value='"+datos['svc_dias_itinerario']+"' onkeypress='validaCeros(this.value,this.id); return validaNum(event);' onkeyup='validaDias(this.id,this.value,"+conceptoId+");' onBlur='recalculaMontos2("+conceptoId+", this.id); onBlurDias(this.id,this.value)' style='border-color:#FFFFFF; text-align:center' /></td>";
	nuevaFila+="<td align='right'><input disabled type='text' name='MontoTotal"+id+"' id='MontoTotal"+id+"' value='0' readonly='readonly' style='border-color:#FFFFFF; text-align:right'/></td>";
	nuevaFila+="<td align='right'><input disabled type='text' name='MontoEnPesos"+id+"' id='MontoEnPesos"+id+"' value='0' readonly='readonly' style='border-color:#FFFFFF; text-align:right'/></td>";
	nuevaFila+="<td><div align='center'><img class='eliminar' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+id+"dele' id='"+id+"dele'  onClick='eliminarConceptos(this.id);recalculaTotalAnticipo(this.id); borrarRenglon4(this.id,\"conceptos_table\",\"rowCountConceptos\",\"\",\"idRenglonCpto\",\"\",\"dele\",\"\");'    style='cursor:pointer' /></div></td>";
	nuevaFila+="<input type='hidden' name='exedePolitica"+id+"' id=exedePolitica"+id+" value='0' readonly='readonly'/>";
	nuevaFila+="<input type='hidden' name='montoCotizado"+id+"' id=montoCotizado"+id+" value='0' readonly='readonly'/>";
	nuevaFila+="</tr>";
	$("#conceptos_table").append(nuevaFila);
	
	//Seleccionar itinerario
	var count_itinerarios = parseInt($("#solicitud_table>tbody>tr").length);
	if(datos["recurrencia"] == 1 && count_itinerarios != 1){
		if(datos['svc_divisa'] != 1){			
			$("#Monto"+id).val((parseFloat(datos['svc_monto_divisa'].replace().replace(/,/g,""))/parseFloat(datos['svc_dias_itinerario'])).toFixed(2));
		}else{
			$("#Monto"+id).val((parseFloat(datos['svc_conversion'].replace().replace(/,/g,""))/parseFloat(datos['svc_dias_itinerario'])).toFixed(2));
		}		
	}else{		
		for(var i=1;i<=count_itinerarios;i++){
			if($("#idItinerarioDB"+i).val() == datos['svc_itinerario']){
				$("#itinerarios_cbx"+id).val(i);
				calculo_de_conceptos(conceptoId, i, id);				
			}
		}
		$("#Monto"+id).val(datos['svc_detalle_monto_concepto']);
	}	
	
	$("#moneda"+id).val(datos['div_nombre']);
	$("#Days"+id).val(diasAnticipo);
	recalculaMontos2(conceptoId, id);
}

function verificaMontoCotizadoAgencia(monto,itinerarioSeleccionado){	
	var zona = $('#tipo_viaje'+itinerarioSeleccionado).val();
	var dias = parseInt($("#noches").val());	
	var region = "";
	var regionNombre = "";
	var montoLimite = 0;
	var divisa = "";
	var zonaGeografica = "";
	var tasa = 0;
	var montopesosLimite = 0;	
	if(zona != ""){
		$("#warning_msg").css("display", "none");
		
		if(zona == 'Nacional'){
			region = "0";
			zonaGeografica = '15'; // Zona Geográfica (ID): Nacional
		}else{
			region = $("#regionNombre"+itinerarioSeleccionado).val();
			zonaGeografica = '14'; // Zona Geográfica (ID): Internacional			
		}
		
		if(region != "Seleccione..."){
			$("#warning_msg").css("display", "none");			
			$.ajax({
				type: "POST",
				url: "services/ajax_solicitudes.php",
				data: "regionHospedaje="+region+"&zonaGeografica="+zonaGeografica,
				dataType: "json",		
				success: function(json){
					montoLimite = json[0].montoLim;
					divisa = json[0].moneda;	                
		               
					switch (divisa){
						case "USD":							
							tasa = $("#tasaUSD").val();
							montopesosLimite = 0;
							montopesosLimite = (tasa * montoLimite) * dias;
							break;
						case "EUR":
							tasa = $("#tasaEUR").val();
							montopesosLimite = 0;
							montopesosLimite = (tasa * montoLimite) * dias;
							break;
						default :
							montopesosLimite = montoLimite;
					}
					
					if(montopesosLimite < monto){
						$("#warning_msg").html("<strong>NOTA: Esta rebasando la pol&iacute;tica del concepto Hotel, el monto m&aacute;ximo es de: " + number_format((montoLimite * dias),2,".",",") + " "+divisa+".</strong>");
						$("#warning_msg").fadeIn();
						$("#obsjus").fadeIn();
						$("#warning_msg").css("display", "block");
						$("#obsjus").html("Comentarios<span class='style1'>*</span>:&nbsp;");
						$("#excedeMontoHospedaje").val("1");
					}else{						
						$("#warning_msg").fadeOut();
						$("#obsjus").html("Comentarios:&nbsp;");
						$("#warning_msg").html("");
						$("#warning_msg").css("display", "none");
						$("#excedeMontoHospedaje").val("0");
					}
				}
			});
		}else{
			$("#warning_msg").html("<strong>Por favor seleccione la Regi&oacute;n a la que desea viajar.</strong>");
			$("#warning_msg").fadeIn();
			$("#warning_msg").css("display", "block");
			$("#agregar_Hotel").attr("disabled", "disabled");
			$("#aceptarHotel").attr("disabled", "disabled");
			$("#cancelarHotel").attr("disabled", "disabled");
		}
	}else{
		$("#warning_msg").html("<strong>Por favor seleccione la Zona geogr&aacute;fica a la que desea viajar.</strong>");
		$("#warning_msg").fadeIn();
		$("#warning_msg").css("display", "block");
		$("#agregar_Hotel").attr("disabled", "disabled");
		$("#aceptarHotel").attr("disabled", "disabled");
		$("#cancelarHotel").attr("disabled", "disabled");
	}
}

function verificaMontoCotizado(monto){
	var zona = $("#select_tipo_viaje option:selected").val();
	var dias = parseInt($("#noches").val());
	var region = "";
	var montoLimite = 0;
	var divisa = "";
	var zonaGeografica = "";
	var tasa = 0;
	var montopesosLimite = 0;
	
	if(zona != -1){
		if(zona == 1){
			region = "0";
			zonaGeografica = '15'; // Zona Geográfica (ID): Nacional
		}else{
			region = $("#select_region_viaje option:selected").text();
			zonaGeografica = '14'; // Zona Geográfica (ID): Internacional
		}
		
		if(region != "Seleccione..."){
			$.ajax({
				type: "POST",
				url: "services/ajax_solicitudes.php",
				data: "regionHospedaje="+region+"&zonaGeografica="+zonaGeografica,
				dataType: "json",		
				success: function(json){
					montoLimite = json[0].montoLim;
					divisa = json[0].moneda;
					
					switch (divisa){
						case "USD":
							tasa = $("#tasaUSD").val();
							montopesosLimite = 0;
							montopesosLimite = (tasa * montoLimite) * dias;
							break;
						case "EUR":
							tasa = $("#tasaEUR").val();
							montopesosLimite = 0;
							montopesosLimite = (tasa * montoLimite) * dias;
							break;
						default :
							montopesosLimite = montoLimite;
					}
					
					if(montopesosLimite < monto){
						$("#warning_msg").html("<strong>NOTA: Esta rebasando la pol&iacute;tica del concepto Hotel, el monto m&aacute;ximo es de: " + number_format((montoLimite * dias),2,".",",") + " "+divisa+".</strong>");
						$("#warning_msg").fadeIn();
						$("#obsjus").fadeIn();
						$("#warning_msg").css("display", "block");
						$("#obsjus").html("Comentarios<span class='style1'>*</span>:&nbsp;");
						$("#excedeMontoHospedaje").val("1");
					}else{						
						$("#warning_msg").fadeOut();
						$("#obsjus").html("Comentarios:&nbsp;");
						$("#warning_msg").html("");
						$("#warning_msg").css("display", "none");
						$("#excedeMontoHospedaje").val("0");
					}
				}
			});
		}else{
			$("#warning_msg").html("<strong>Por favor seleccione la Regi&oacute;n a la que desea viajar.</strong>");
			$("#warning_msg").fadeIn();
			$("#warning_msg").css("display", "block");
			$("#agregar_Hotel").attr("disabled", "disabled");
			$("#aceptarHotel").attr("disabled", "disabled");
			$("#cancelarHotel").attr("disabled", "disabled");
		}
	}else{
		$("#warning_msg").html("<strong>Por favor seleccione la Zona geogr&aacute;fica a la que desea viajar.</strong>");
		$("#warning_msg").fadeIn();
		$("#warning_msg").css("display", "block");
		$("#agregar_Hotel").attr("disabled", "disabled");
		$("#aceptarHotel").attr("disabled", "disabled");
		$("#cancelarHotel").attr("disabled", "disabled");
	}
}

function validaDias(diasId,numDias,concepto){
	var re = /^[0-9]+|[0-9]+$/;
	var id = (""+diasId).match(re);	
	
	var itinerarioId = $("#itinerarios_input"+id).val();	
	
	var tipo_viaje_pasaje = $("#select_tipo_viaje_pasaje>option").eq($("#select_tipo_viaje_pasaje").val()).text();
	var maxNumDias = 0;
	var num_dias_redondo = 0;
	
	if(itinerarioId == "Todos"){
		maxNumDias = getDiasMT();
	}else if(itinerarioId != "0"){
		num_dias_redondo = parseInt($("#noDias"+itinerarioId).val());
		
		if(tipo_viaje_pasaje == "Redondo" && concepto != 5 ){
			maxNumDias = num_dias_redondo + 1 ;
		}else if(diasIgualesAlimentos(itinerarioId,concepto) == true){ //validacion de alimentos igual			
			maxNumDias = parseInt($("#noDiasAI"+itinerarioId).val());
		}else{
			maxNumDias = parseInt($("#noDias"+itinerarioId).val());
		}
		
	}
	
	if(parseInt(maxNumDias) == 0){
		alert("Número de días no valido,\nseleccione un Itinerario.");
		$("#Days"+id).val(maxNumDias);
	}else{
		if(parseInt(numDias) > parseInt(maxNumDias)){
			alert("El maximo numero de dias permitidos es "+maxNumDias);
			$("#Days"+id).val(maxNumDias);
		}
	}
	
}

function onBlurDias(diasId,numDias){
	var re = /^[0-9]+|[0-9]+$/;
	var id = (""+diasId).match(re);

	if(numDias == ""){
		$("#Days"+id).val("0");
	}
}

function recalculaTotalAnticipo(id_concepto){	
	var idRentaAuto_concepto=$("#idRentaAutoC").val();
	var idHotel_concepto=$("#idHotelC").val();
	var nuevoTotalAnticipo=0;
	var nuevoTotalSol=0;	
	var count_conceptos = parseInt($("#conceptos_table>tbody>tr").length);
	//Si es un concepto de tipo Renta de auto y hotel se actualizara el tot. anticipos y tot. de la solicitud	
	if(($("#Concepto"+parseInt(id_concepto)).val() != idRentaAuto_concepto)&&($("#Concepto"+parseInt(id_concepto)).val() != idHotel_concepto)){		
		nuevoTotal=parseFloat($("#anticipoC").val().replace(/,/g,""))- parseFloat($("#MontoEnPesos"+parseInt(id_concepto)).val().replace(/,/g,""));
		nuevoTotalSol=parseFloat($("#totalA").val().replace(/,/g,""))- parseFloat($("#MontoEnPesos"+parseInt(id_concepto)).val().replace(/,/g,""));
		//Se afecta el total del anticipo.
		$("#anticipoC").val(number_format(nuevoTotal,2,".",","));
		//se afectara el total de la solicitud		
		$("#totalA").val(number_format(nuevoTotalSol,2,".",","));
		$("#totalSol").val(number_format(nuevoTotalSol,2,".",","));
	}else{			
		nuevoTotal=parseFloat($("#anticipoC").val().replace(/,/g,""))- parseFloat($("#MontoEnPesos"+parseInt(id_concepto)).val().replace(/,/g,""));
		$("#anticipoC").val(number_format(nuevoTotal,2,".",","));
	}
	
}

function rentaAutoId(){
	$.ajax({
		type: "POST",
		url: "services/ajax_solicitudes.php",
		data: "idRentaAuto="+0,
		dataType: "json",
		timeout: 10000,
		success: function(json){							
			$("#idRentaAutoC").val(json);					
		},
		complete: function (json){
			//Obtenemos el valor para el concepto de tipo Hotel 
			HotelId();
		},
		error: function(x, t, m) {
			if(t==="timeout") {
				location.reload();
					 abort();
			}
		}
	});
}

function HotelId(){
	$.ajax({
		type: "POST",
		url: "services/ajax_solicitudes.php",
		data: "idHotel="+0,
		dataType: "json",
		timeout: 10000,
		success: function(json){							
			$("#idHotelC").val(json);							
		},
		complete: function (json){
			//Desbloqueamos la pantalla
			$.unblockUI();
		},
		error: function(x, t, m) {
			if(t==="timeout") {
				location.reload();
					 abort();
			}
		}
	});
}

function montoTotalAvion(id_tramite){
	var totalavion = 0;
	$.ajax({
		type: "POST",
		url: "services/ajax_solicitudes.php",
		data: "totalAvion="+id_tramite,
		dataType: "json",
		async: false, 
		timeout: 10000,
		success: function(json){
			totalavion = json;
		},
		complete : function(json){
			$("#montoAvionSol").val(totalavion);
		},
		error: function(x, t, m) {
			if(t==="timeout") {
				location.reload();
				abort();
			}
		}
	});
}

function truncaMonto(numero){	
	var numString = "";
	var cadena = "";
	var numConcat = "";
	
	numString = numero.toString();
	
	if(numString.indexOf('.') != -1){
		cadena = numString.split(".");
		numConcat = cadena[0] + "." + cadena[1].substring(0,2);
	}else{
		numConcat = numString;
	}
	
	return parseFloat(numConcat);
}

/* Función para alterar la tabla(table_avion) donde se muestra la cotización del boleto de Avión 
 * (Actualizar los datos ingresados para el registro de la compra del Boleto de Avión)
 */
function actualizaDatosAvion(){
	// Eliminamos el registro de la cotización anterior 
	$("#table_avion").find("tr:gt(0)").remove();
	$("#total_pesos_avion").html("0.00");
	
	// Datos generales del viaje
	var solicitud = $("#solicitud").val();
	var tipo_viaje = $("#tipo_viaje").val();
	var tipoViaje = $("#tipoViaje").val();
	
	// Guardamos los datos de la ventanita del Avión
	var val1 = $("#costoAvion").val().replace(/,/g,"");
	var val2 = $("#ivaAvion").val().replace(/,/g,"");
	var val3 = $("#tuaAvion").val().replace(/,/g,"");
	var val4 = $("#ctAvion").val().replace(/,/g,"");
	var val5 = $("#aeroAvion").val();
	var val6 = $("#tipo").val();
	var val7 = $("#salida").val();
	var val8 = $("#llegada").val();
	var val9 = 0;
	var val10 = $("#tipo option:selected").text();
	$('#TotAvion').val(parseFloat(val4));
	
	var montoNuevoTotal = $("#ctAvion").val().replace(',','');
	montoNuevoTotal = parseFloat(montoNuevoTotal);
	
	var montoVueloTotalCotizado = $("#montoVueloTotalCotizado").val().replace(',','');
	montoVueloTotalCotizado = parseFloat(montoVueloTotalCotizado);
	
	var datos = new Array();
	datos['svi_solicitud'] = solicitud;
	datos['svi_tipo_viaje'] = tipoViaje;
	datos['sv_viaje'] = tipo_viaje;
	datos['svi_fecha_regreso_avion'] = val8;
	datos['svi_aerolinea'] = val5;
	datos['svi_fecha_salida_avion'] = val7;
	datos['svi_monto_vuelo'] = val1;
	datos['svi_iva'] = val2;
	datos['svi_tua'] = val3;
	datos['svi_tipo_aerolinea'] = val6;
	datos['svi_monto_vuelo_cotizacion'] = val4;
	datos['se_nombre'] = val10;
	
	// Agregar la fila a la tabla de las cotizaciones de Avión
	agregar_avion_a_tabla(datos);
	
	// Validamos el monto nuevo contra el registrado en la BD
	validaMontoTolerancia("verificando");
	
	// Cerar la ventanita de cotización/compra de Avión
	flotanteActive3(0);
	
	// Mostramos el botón de compra
	document.getElementById('confCompra').style.visibility = 'visible';
	
	// Mostrar el botón de cancelar en caso de que el usuario haya modificado los valores de la cotización
	if(montoVueloTotalCotizado != montoNuevoTotal){
		document.getElementById('cancelarCambios').style.visibility = 'visible';
	}
}

function retiraConceptoCombo(){	
	var elementos = 0;
	var var_combo_itinerarios_AH = "";	
	
	elementos = var_combo_itinerarios.split("|");	
	elementos[elementos.length - 2] = " ";		
	for(var i = 0; i < elementos.length ; i++){			
		var_combo_itinerarios_AH += elementos[i];
	}	
	return var_combo_itinerarios_AH;	
}

function limpiaValoresAnticipo(id){	
	$("#Monto"+id).val("0.00");
	$("#Days"+id).val("0");
	$("#MontoTotal"+id).val("0.00");
	$("#MontoEnPesos"+id).val("0.00");
}

function detectorAlimentos(){
	var contador = 0;
	var count_conceptos = parseInt($("#conceptos_table>tbody>tr").length);
	for(var i=1;i<=count_conceptos;i++){
		if($("#Concepto"+i).val() == 6){
			contador++;
		}
	}
	return contador;
}

function fechaItinerarios(id_fila_concepto){
	var banderaFechaIgual = false;
	var itinerarioSeleccionado = $("#itinerarios_cbx"+id_fila_concepto).val();	
	var longitud = parseInt($("#solicitud_table>tbody >tr").length);
		
	for(var i=1;i<=longitud;i++){ // validacion en itinerarios para verificar que haya un itinerario con misma fecha		
		if($("#itinerarios_cbx"+id_fila_concepto).val() != i){
			if($("#salida"+itinerarioSeleccionado).val() == $("#salida"+i).val() ){				
				banderaFechaIgual = true;				
			}	
		}					
	}
	return banderaFechaIgual;
}

function fechaConceptos(id_fila_concepto){
	var banderaFechaIgual = false;
	var count_conceptos = parseInt($("#conceptos_table>tbody>tr").length);
	var itinerarioSeleccionado = $("#itinerarios_cbx"+id_fila_concepto).val();
	for(var i=1;i<=count_conceptos;i++){
		if(id_fila_concepto != i){
			if($("#Concepto"+i).val() == 6){
				if(($("#salida"+itinerarioSeleccionado).val() == $("#salida"+$("#itinerarios_cbx"+i).val()).val())){				
					banderaFechaIgual = true;			
				}
			}			
		}
	}
	return banderaFechaIgual;
}

function diasAnticipos(id_fila_concepto){
	var longitud = parseInt($("#solicitud_table>tbody >tr").length);
	var itinerarioSeleccionado = $("#itinerarios_cbx"+id_fila_concepto).val();
	var veces = 0;
	var dias = 0;	
	var primerdia =0;
	var operacion = 0;
	var montoNuevo = 0;	
	
	for(var i = parseInt(itinerarioSeleccionado);i<longitud;i++){		
		if($("#salida"+parseInt(itinerarioSeleccionado)).val() != $("#salida"+(i+1)).val() ){			
			dias = days_between($("#salida"+parseInt(itinerarioSeleccionado)).val(),$("#salida"+(i+1)).val());
			veces++;
			if(veces == 1){
				primerdia = dias;
			}
		}					
	}

	if(dias == 0){
		primerdia = 1;
	}
	
	$("#noDiasAI"+itinerarioSeleccionado).val(primerdia);
	$("#Days"+id_fila_concepto).val(primerdia);	
	montoNuevo = parseFloat($("#Monto"+id_fila_concepto).val().replace(/,/g,""));	
	operacion = dias * montoNuevo;	
	$("#MontoTotal"+id_fila_concepto).val(number_format(operacion,2,".",","));
	$("#MontoEnPesos"+id_fila_concepto).val(number_format(operacion,2,".",","));
	getTotalAnticipo();
	
}

function diasIgualesAlimentos(itinerarioId,concepto){	
	var longitud = parseInt($("#solicitud_table>tbody >tr").length);	
	var veces = 0;
	var primerValor = false;
	for(var i = parseInt(itinerarioId);i<longitud;i++){		
		if(($("#salida"+parseInt(itinerarioId)).val() == $("#salida"+(i+1)).val()) && (concepto == 6)){			
			veces++;
			if(veces == 1){
				primerValor = true;
			}
		}					
	}
	return primerValor;
}

	function validaPolitica(ObjetoFormulario, Parametros, conceptosPoliticasFecha, previo){
		Parametros["idConcepto"] = ObjetoFormulario["concepto"];		
		if(Parametros["region"] == undefined)
			Parametros["region"] = $("#regionId"+ObjetoFormulario["referencia"]).val();
		
		var dias = ObjetoFormulario["dias"];
		var noches = dias-1;
		var concepto = new Array();
		var msg = "";
		var excedente = 0;		
		var politica = obtenerPolitica(Parametros);
		concepto[1] = limpiaCantidad(ObjetoFormulario["totalPartida"])/100;
		excedente = concepto[1] - (politica * dias);
		if(politica == 0 || excedente <= 0)
			return false;		
		
		if(!previo)
			alert("Se ha excedido el importe diario permitido por pol&iacute;tica en concepto de: "+ObjetoFormulario["conceptoTexto"]+" para la fecha "+ ObjetoFormulario["fecha"]);						
		
		
		var Excepcion = {
			"concepto":		ObjetoFormulario["concepto"],
			"mensaje":		"Se ha excedido el importe diario permitido por política en concepto de: " + ObjetoFormulario["conceptoTexto"],
			"fecha":		null, 
			"referencia":	ObjetoFormulario["referencia"],
			"totalPartida":	ObjetoFormulario["totalPartida"],
			"excedente":	excedente.toFixed(2)
		}
		
		return Excepcion;		
	}
	
	function crearRenglonExcepcion(Excepcion, id){
		var renglon = "";
		renglon+= '<tr id="tr_'+id+'">'
		renglon+= 	'<td><input id="e_row'+id+'" name="e_row'+id+'" value="'+id+'" /></td>';
		renglon+= 	'<td><input id="e_row_concepto'+id+'" name="e_row_concepto'+id+'" value="'+Excepcion.concepto+'" /></td>';
		renglon+= 	'<td><input id="e_row_mensaje'+id+'" name="e_row_mensaje'+id+'" value="'+Excepcion.mensaje+'" /></td>';
		renglon+= 	'<td><input id="e_row_fecha'+id+'" name="e_row_fecha'+id+'" value="'+Excepcion.fecha+'" /></td>';
		renglon+= 	'<td><input id="e_row_referencia'+id+'" name="e_row_referencia'+id+'" value="'+Excepcion.referencia+'" /></td>';
		renglon+= 	'<td><input id="e_row_totalPartida'+id+'" name="e_row_totalPartida'+id+'" value="'+Excepcion.totalPartida+'"/></td>';
		renglon+= 	'<td><input id="e_row_diferencia'+id+'" name="e_row_diferencia'+id+'" value="'+Excepcion.excedente+'"/></td>';
		renglon+= '</tr>';		
		return renglon;		
	}
	
	function agregaRenglon(renglon, tabla){		
		$("#"+tabla).append(renglon);
	}
	
	function obtenTablaLength(tabla){	
		return parseInt($("#"+tabla+">tbody>tr").length);
	}
	
	function limpiaCantidad(valor){		
		if(valor == "" || valor == undefined)
			valor = 0;
		valor = String(valor).replace(",","");
		valor = String(valor).replace("$","");
		valor = String(valor).replace(/\D/g,"");
		valor = String(valor).replace("_","");
		return parseFloat(valor);		
	}	

	function borrarRenglonj(id, tabla){
		var tablaLength = obtenTablaLength(tabla);
		var fila = $("#"+tabla+">tbody>tr#tr_"+id);
		
		fila.fadeOut(350).remove();	
		
		for(var i = parseInt(id)+1; i <= tablaLength; i++ ){
			var contexto = $("#"+tabla+">tbody>tr#tr_"+i);
			
			$("#div_row_partida"+i,contexto).text(i-1);	
			$("#div_row"+i,contexto).text(i-1);	
			$("#row_"+i,contexto).val(i-1);	
			$("#e_row"+i,contexto).val(i-1);	
			
			$(":input",contexto).each(function(){
				var nameAttr = $(this).attr("name");
				var idAttr = $(this).attr("id");
				nameAttr = String(nameAttr).replace(i,i-1);
				idAttr = String(idAttr).replace(i,i-1);
				
				$(this).attr("name",nameAttr);
				$(this).attr("id",idAttr);
			});						

			$("div",contexto).each(function(){
				var idAttr = $(this).attr("id");
				idAttr = String(idAttr).replace(i,i-1);				
				$(this).attr("id",idAttr);
			});										
			
			$(".editarPartida#"+i,contexto).attr("id",i-1);							
			$(".eliminarPartida#"+i,contexto).attr("id",i-1);	
			$("#tr_"+i, $("#"+tabla)).attr("id","tr_"+(i-1));						
		}		
	}	
	
	function modificarRenglon(renglon, id, tabla){						
		$("#"+tabla+">tbody>tr#tr_"+id).replaceWith(renglon);		
	}	
	
	function validaPoliticas(Parametros, conceptosPoliticasNoches, conceptosPoliticasDias){
		var tablaLength = obtenTablaLength("solicitud_table");
		var conceptos = new Array();				
		for(var i = 1; i <= tablaLength; i++ ){
			var atributos = new Array();
			if($("#CheckHAgencia"+i).val() || $("#CheckTAgencia"+i).val()){
			
				var concepto;
				if($("#CheckHAgencia"+i).val()) concepto = 5;
				if($("#CheckTAgencia"+i).val()) concepto = 10;
				
				var total;
				if($("#CheckHAgencia"+i).val()) total = $("#costoHosp"+i).val();
				if($("#CheckTAgencia"+i).val()) total = $("#costoAuto"+i).val();
				
				atributos[0] = concepto;
				atributos[1] = limpiaCantidad(total)/100;		
				atributos[2] = $("#regionId"+i).val();
				atributos[3] = $("#row"+i).val();							
				atributos[4] = $("#diasRenta"+i).val();		
				atributos[5] = $("#noches"+i).val();		
				
				conceptos.push(atributos);				
			}							
		}
		
		for(var i = 0; i < conceptos.length; i++){
			Parametros['idConcepto'] = conceptos[i][0];
			Parametros['region'] = conceptos[i][2];			
			
			var tablaExcepcionLength = obtenTablaLength("excepcion_table");
			var politica = obtenerPolitica(Parametros);
			if(conceptos[i][0] == 5){
				politica = politica*conceptos[i][5];				
			}
			if(conceptos[i][0] == 10){
				politica = politica*conceptos[i][4];				
			}
			
			var excedente = conceptos[i][1] - politica;
			if(excedente > 0 && politica > 0){			
				var Excepcion = {
					"concepto":		conceptos[i][0],
					"mensaje":		"Se ha excedido el importe permitido por el periodo del viaje en concepto de: "+conceptos[i][0],
					"fecha":		"N/A",
					"referencia":	conceptos[i][3]+"G",
					"totalPartida":	conceptos[i][1],
					"excedente":	excedente.toFixed(2)
				}			
				var renglonExcepcion = crearRenglonExcepcion(Excepcion, tablaLength+tablaExcepcionLength);
				agregaRenglon(renglonExcepcion, "excepcion_table");		
			}
		}			
		obtenPartidasExcepciones1();
	}
	
	function obtenPartidasExcepciones(){
		var tablaLength = obtenTablaLength("excepcion_table");
		var partidas = "";
		for(var i = 0; i < tablaLength; i++){
			var row = $("#excepcion_table>tbody> tr").eq(i);
			var col = $("td", row).eq(0);
			var val = $(":input", col).eq(0).val();
			partidas+= val+",";
		}	
		asignaVal("totalExcepciones", partidas.substring(0, partidas.length-1));		
	}
	function obtenPartidasExcepciones1(){
		var tablaLength = obtenTablaLength("excepcion_table");
		var partidas = "";
		for(var i = 0; i < tablaLength; i++){
			var row = $("#excepcion_table>tbody> tr").eq(i);
			var col = $("td", row).eq(0);
			var val = $(":input", col).eq(0).val();
			partidas+= val+",";
		}	
		asignaVal("totalExcepciones", partidas.substring(0, partidas.length-1));		
	}	
	
	function asignaVal(campo, valor, formato){
		if(formato == "number" ){
			$('#'+campo).priceFormat({
				prefix: '', centsSeparator: '.', thousandsSeparator: ','
			});
		}
		$("#"+campo).val(valor);		
	};
	
	