// Esta function se llama al inicio
function inicializarEventos(){
	blockUI(true);
	/**
	 * EXCEPCIONES
	 */
	$._GET = function(name){
		var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(top.window.location.href); 
		return (results !== null) ? results[1] : 0;
	}
	var usuario = $("#idusuario").val();
	var Parametros = {
		'idUsuario': 	usuario,
		'idConcepto':	null,
		'flujo':	 	1,
		'region':	 	null
	};
	var conceptosPoliticasFecha = new Array("6");
	var conceptosPoliticasDias = new Array(["4","7"]);
	var conceptosPoliticasNoches = new Array("5");
	var tramite_id = gup("id");
	if(tramite_id != "")
		fillform(tramite_id, Parametros);
	else
		fillComboConceptos(true,true);
	// Para poder mover las ventanas
	$("#ventanita").draggable({containment : "#Interfaz", scroll: false});
	$("#ventanita_auto").draggable({containment : "#Interfaz", scroll: false});
	$("#fecha").datepick({
		minDate:obtenFecha(),
		onSelect:function(dates){verificaRegion();}
	});			
	$("#fechaLlegada").datepick({
		minDate:obtenFecha()
	});
	$("#llegada").datepick({
		minDate:obtenFecha(),
		onSelect:function(dates) { verificarFechas(); }
	});
	$("#salida").datepick({
		minDate:obtenFecha(),
		onSelect:function(dates) { verificarFechas(); }
	});
	//Seleccionar el centro de costos del usuario actual
	getCECO();
	$('#fecha').keydown(function(e){
		ignoraEventKey(e);
	});
	$('#fechaLlegada').keydown(function(e){
		ignoraEventKey(e);
	});
	$('#llegada').keydown(function(e){
		ignoraEventKey(e);
	});
	$('#salida').keydown(function(e){
		ignoraEventKey(e);
	});
	$("#tipoHotel, #costoNoche, #iva, #selecttipodivisa").bind('keydown keyup change', function(){
		//validarPoliticaHotelEmpleado(84);
	});
	$("#tipoAuto, #diasRenta, #costoDia, #tipoDivisa").bind('keydown keyup change', function(){
		//validarPoliticaAutoEmpleado(999); JUSTIFICAICON
	});
	$("input").bind("keydown", function(e){
		if(!isAlphaNumeric(e)) return false;
	});
	$("#conceptos_table").bind("keydown", function(e){
		var longitud = parseInt($("#conceptos_table>tbody >tr").length);
		if(longitud == 0){
			confirmaRegreso('conceptos_table');
		}
	});
	$("#addConcepto").click(function(){
	});
	$(".eliminar").live('click', function(){
		var id = limpiaCantidad($(this).attr("id"));
		borrarRenglonj(id, "excepcion_table");
	});
	$("#Accion").click(function(){
		if(!$(this).is(":checked"))
			$("#excepcion_table>tbody").html("");
	});
	blockUI(false);
}
function gup(name){
	var regexS = "[\\?&]"+name+"=([^&#]*)";
	var regex = new RegExp ( regexS );
	var tmpURL = window.location.href;
	var results = regex.exec( tmpURL );
	if( results == null )
		return"";
	else
		return results[1];
}
function construirTabla(indicador){
			if(indicador == "1" || indicador == "3" || indicador == "Sencillo" || indicador == "Multidestinos"){			
				var idTable = "solicitud_table";
				var classTable = "tablesorter";
				var styleTable = "cellspacing='1'";
				var th = ["No.", "Zona geogr&aacute;fica", "Regi&oacute;n", "Origen", "Destino", "Fecha de salida", "Hora de salida", "Transporte", "Hospedaje", "Costo hospedaje MXN", "Noches", "Renta de auto", "Costo de auto MXN", "Editar", "Eliminar"];
				var thStyle = ["width='2%'", "width='8%'", "width='8%'", "width='8%'", "width='9%'", "width='10%'", "width='10%'", "width='6%'", "width='6%'", "width='6%'", "width='5%'", "width='6%'", "width='6%'", "width='7%'", "width='4%'"];
			    var cadena = createTableJS(idTable,classTable,styleTable,th,thStyle);
				document.getElementById("solicitud_div").innerHTML = cadena;
			}else{
				var idTable = "solicitud_table";
				var classTable = "tablesorter";
				var styleTable = "cellspacing='1'";
				var th = ["No.", "Zona geogr&aacute;fica", "Regi&oacute;n", "Origen", "Destino", "Fecha de salida", "Hora de salida", "Fecha de regreso","Hora de regreso", "Transporte", "Hospedaje", "Costo hospedaje MXN", "Noches", "Renta de auto", "Costo de auto MXN", "Editar", "Eliminar"];
				var thStyle = ["width='2%'", "width='8%'", "width='8%'", "width='8%'", "width='9%'", "width='10%'", "width='10%'", "width='10%'", "width='10%'", "width='6%'", "width='6%'", "width='6%'", "width='5%'", "width='6%'", "width='6%'", "width='7%'", "width='4%'"];
			    var cadena = createTableJS(idTable,classTable,styleTable,th,thStyle);
				document.getElementById("solicitud_div").innerHTML = cadena;
			}		
		}
function getCECO(){
	if(bandera_edicion == false){
	var parametros1 = idEmpleadoSession;
	var respuesta1 = obtenJson(parametros1);
		if(respuesta1 != false){
			seleccionar(respuesta1);
			rentaAutoId();
		}
	}
}
function fillComboConceptos(hospedaje,auto){
	var parametros2 = "obtenerConceptos=obtenerConceptos";
	var respuesta2 = obtenJson(parametros2);
		if(respuesta2 != false){
			var frm=document.detallesItinerarios;
			LimpiarCombo(frm.select_concepto);
			var k=0;
			while(concepto = respuesta2[k]){						
				if(hospedaje == true && concepto['dc_id'] == "5"){
					$('#select_concepto').append('<option value="'+concepto['dc_id']+'" selected="selected">'+concepto['cp_concepto']+'</option>');
				}else if(hospedaje == false && concepto['dc_id'] == "5"){							
				}else if(auto == true && concepto['dc_id'] == "10"){
					$('#select_concepto').append('<option value="'+concepto['dc_id']+'" selected="selected">'+concepto['cp_concepto']+'</option>');
				}else if(auto == false && concepto['dc_id'] == "10"){
				}else{
					$('#select_concepto').append('<option value="'+concepto['dc_id']+'" selected="selected">'+concepto['cp_concepto']+'</option>');
				}
				k++;
			}
		}
}
function cargaObservacionesCECO(id_solicitud){
	var parametros3 = "observTramite="+id_solicitud;
	var respuesta3 = obtenJson(parametros3);
		if(respuesta3 != false){
			if(respuesta3[0].etapa == 6 || respuesta3[0].etapa == 12){
				// Etapas Solicitud Rechazada y Rechazada por el Director colocarán las Observaciones en el campo Historial de Observaciones
				$("#historial_obser").val(respuesta3[0].observacion);
			}else{
				$("#observ").val(respuesta3[0].observacion);
			}
			if(bandera_edicion == true){
				var parametros4 = "idTramiteEdicion="+id_solicitud;
				var respuesta4 = obtenJson(parametros4);
				if(respuesta4 != false){
					seleccionar(respuesta4);
					etapaRechazar(id_solicitud);
					$.unblockUI();
				}
			}
		}
}
function fillform(id_solicitud, Parametros){
	bandera_edicion=true;
	var frm=document.detallesItinerarios;
	if(id_solicitud != ""){
		var parametros5 = "motivo22="+id_solicitud;
		var respuesta5 = obtenJson(parametros5);
			if(respuesta5 != false){
				$("#motive").val(respuesta5[0].sv_motivo);
				$("#select_tipo_viaje_pasaje").val(respuesta5[0].sv_viaje);
				$("#anticipos").val(respuesta5[0].sv_anticipo);
				document.getElementById("select_tipo_viaje_pasaje").disabled="disable";
				$("#select_tipo_viaje_pasaje_val").val(respuesta5[0].sv_viaje);
				construirTabla(respuesta5[0]["sv_viaje"]);
				var parametros6 = "itinerarios="+id_solicitud;
				var respuesta6 = obtenJson(parametros6);
					if(respuesta6 != false){
						var i=0;
						while(datos = respuesta6[i]){
							agregarItinerarioFromDB(datos);
							idItinerario = datos['svi_id'];
							var numItinerario = parseInt(i) + 1;
							if(datos['svi_hotel']=='true' || datos['svi_hotel_agencia']=='true'){
								dibujaHotelesBD(idItinerario,numItinerario);
							}
							i++;
						}
						revisarComboConceptos();
						if(parseInt($("#anticipos").val()) == 1){
							//Se obtienen y cargan los conceptos
							crear_combo_itinerarios();
							desplegar(Sconcepto);
							desplegar(lConceptos);
							$("#Accion").attr('checked',true);
							deshabilitarEditElim();
							var parametros7 = "conceptos="+id_solicitud;
							var respuesta7 = obtenJson(parametros7);
							if(respuesta7 != false){
								var k=0;
								while(datos3 = respuesta7[k]){
									construyePartidaConcepto2FromDB(datos3);
									k++;
								}
								cargaObservacionesCECO(id_solicitud);
							}
						}
					}else{
						cargaObservacionesCECO(id_solicitud);
					}
			}
	}
}
function dibujaHotelesBD(idItinerario,numItinerario){			
	var parametros8 = "hoteles="+idItinerario;
	var respuesta8 = obtenJson(parametros8);
	if(respuesta8 != false || respuesta8 != null){				
		var j=0;
		while(datos4 = respuesta8[j]){					
			agregarHotelFromDB(numItinerario,datos4);
			j++;
		}
	}
}
//funcion que nos permitira obtener la etapa para la edicion de la solicitud de viaje		
function etapaRechazar(id_solicitud){
	var parametros9 = "etapaRechazo="+id_solicitud;
	var respuesta9 = obtenJson(parametros9);
	var frm=document.detallesItinerarios;
	var sesionDelegado = $("#delegado").val();
	var banderaTransporte = 0;
	var etapaTramite = 0;
	if(respuesta9 != false){
		etapaTramite = respuesta9;
		getTotalBD(id_solicitud);
		if(etapaTramite == 6 || etapaTramite == 12){ // Etapa Rechazada o Rechazada por el Director
			$("#etapaID").val(etapaTramite);//Se envia la etapa rechazada
			$("#guardarprevComp").attr("disabled", "disabled");
			$("#guardarComp").attr('value', '    Enviar a Agencia');
			$("#guardarComp").css({'background':'url(../../images/Arrow_Left.png)','background-repeat':'no-repeat','background-color':'#E1E4EC','background-position':'left'});					
			if(etapaTramite == sol_etapa_rechazada){
				$("#autorizar_cotizacion").css("display","");
			}else{
				$("#envia_a_director").css("display","");
			}
		}
	}
}
function aereoTerrestre(){
	var aereo = 0;
	var terrestre = 0;
	var banderaTT = 0;
	var count_itinerarios = parseInt($("#solicitud_table>tbody >tr").length);
	for(var i =1;i<=count_itinerarios;i++){
		if($('#medio'+i).val() == "Aéreo"){
			aereo++;
		}else if($('#medio'+i).val() == "Terrestre"){
			terrestre++;
		}
	}
	//validacion para el tipo de transporte que tenga nuestros itinerarios al editar una solicitud de viaje
	if((aereo > terrestre) && (terrestre != 0)){
		banderaTT=1;
	}else if( aereo == terrestre){
		banderaTT=1;
	}else if((aereo < terrestre ) && ( aereo != 0)){
		banderaTT=1;
	}else if((aereo > terrestre) && ( terrestre == 0)){
		banderaTT=1;
	}else if((terrestre > aereo) && (aereo == 0)){
		banderaTT=0;
	}
	return banderaTT;
}
//Seleccionar elemento del combo de cat_cecos_cargado
function seleccionar(elemento) {
   var combo = document.detallesItinerarios.cat_cecos_cargado;
   var cantidad = combo.length;
   for (i = 0; i < cantidad; i++) {
	  var toks=combo[i].text.split(" ");
	  if (toks[0] == elemento) {
		 combo[i].selected = true;
		 break;
	  }
   }
}
//
//  Empiezan funciones relacionadas con la fecha
//
// Calcula el no. de dias entre dos fechas
function days_between(date_inicial, date_final) {
	// The number of milliseconds in one day
	var ONE_DAY = 1000 * 60 * 60 * 24;
	date_inicial_splitted = date_inicial.split("/");
	date_final_splitted = date_final.split("/");
	date1 = date_inicial_splitted[1]+'/'+date_inicial_splitted[0]+'/'+date_inicial_splitted[2];
	date2 = date_final_splitted[1]+'/'+date_final_splitted[0]+'/'+date_final_splitted[2];
	// Convert both dates to milliseconds
	var date1_ms = Date.parse(date1); //date1.getTime()
	var date2_ms = Date.parse(date2); //date2.getTime()
	// Calculate the difference in milliseconds
	var difference_ms = (date2_ms - date1_ms);
	// Convert back to days and return
	return Math.round(difference_ms/ONE_DAY);
}
function tomaValid(id){
	editaDia=parseInt(id);
}
//Agregada 30/08/2011 Función que valida el límite de días de anticipación de solicitudes Nacionales e internacionales 
function DiasAnterioresViaje(valor){
	var longTabla = parseInt($("#solicitud_table>tbody >tr").length);
	if(longTabla == 0 || editaDia == 1 ){				
		var zonaGeografica="";
		var frm=document.detallesItinerarios;
		var fechaActual = frm.fechaActual.value;
		var fechaSalida = $("#fecha").val();
		var dias_anticipacion_solicitud_nacional=frm.dias_anticipacion_solicitud_nacional.value;
		var dias_anticipacion_solicitud_internacional=frm.dias_anticipacion_solicitud_internacional.value;
		if(valor == "1"){
			zonaGeografica ="Nacional";
		}else if(valor == "2"){
			zonaGeografica ="Continental";
		}else if(valor == "3"){
			zonaGeografica ="Intercontinental";
		}
		// no. de dias anteriores al viaje
		var dias_de_diferenciaAnt = days_between(fechaActual,fechaSalida);
		$("#diasAnteriores").val(dias_de_diferenciaAnt);
		$("#mensaje_excepcion").val("");
		if(zonaGeografica == "Nacional")
		{
			if(parseFloat(dias_de_diferenciaAnt) < parseFloat(dias_anticipacion_solicitud_nacional))
			{
				alert("La fecha de anticipación esta fuera de política de viaje nacional: "+parseInt(dias_anticipacion_solicitud_nacional)+" días.");
				$("#excepciones").val("1");
				$("#mensaje_excepcion").val("La fecha de anticipación esta fuera de política de viaje nacional: "+parseInt(dias_anticipacion_solicitud_nacional)+" días.");
			}
		}else if (zonaGeografica == "Continental")
		{
			if(parseInt(dias_de_diferenciaAnt) < parseInt(dias_anticipacion_solicitud_internacional))
			{
				alert("La fecha de anticipación esta fuera de política de viaje Continental: "+parseInt(dias_anticipacion_solicitud_internacional)+" días.");
				$("#excepciones").val("1");
				$("#mensaje_excepcion").val("La fecha de anticipación esta fuera de política de viaje Continental : "+parseInt(dias_anticipacion_solicitud_internacional)+" días.");
			}
		}else if (zonaGeografica == "Intercontinental")
		{
			if(parseInt(dias_de_diferenciaAnt) < parseInt(dias_anticipacion_solicitud_internacional))
			{
				alert("La fecha de anticipación esta fuera de política de viaje Intercontinental: "+parseInt(dias_anticipacion_solicitud_internacional)+" días.");
				$("#excepciones").val("1");
				$("#mensaje_excepcion").val("La fecha de anticipación esta fuera de política de viaje Intercontinental: "+parseInt(dias_anticipacion_solicitud_internacional)+" días.");
			}
		}
		if(parseInt(valor)!=1&&parseInt(valor)!=2&&parseInt(valor)!=3){
			alert("Debe seleccionar el rango del viaje");
		}
	}
}
function diasDeViaje(valor, aux){
	var frm=document.detallesItinerarios;
	if(aux == "salida"){
		if(frm.select_tipo_viaje_pasaje.value == 1){
			fecha1 = valor;
			fecha2 = frm.fechaLlegada.value;
			noDiasDeViaje = 1;
		}else{
			fecha1 = valor;
			fecha2 = frm.fechaLlegada.value;
			noDiasDeViaje = days_between(fecha1,fecha2)+1;
		}
	}else if(aux == "llegada"){
		fecha1 = frm.fecha.value;
		fecha2 = valor;
		noDiasDeViaje = days_between(fecha1,fecha2)+1;
	}else if(aux == "otro"){
		if(frm.select_tipo_viaje_pasaje.value != 1){
			fecha1 = frm.fecha.value;
			fecha2 = frm.fechaLlegada.value;
			noDiasDeViaje = days_between(fecha1,fecha2)+1;
		}else{
			fecha1 = frm.fecha.value;
			fecha2 = frm.fechaLlegada.value;
			noDiasDeViaje = 1;
		}
	}
	if(noDiasDeViaje <= 0 ){
		if(frm.select_tipo_viaje_pasaje.value == 1){
			$("#dias_de_viaje2").html("1");
			$("#dias_de_viaje").val("1");
			$("#noches").val("0");
		}else{
			$("#dias_de_viaje2").html("La fecha de llegada es anterior a la de salida.");
			$("#dias_de_viaje").val(noDiasDeViaje);
			$("#noches").val("0");
		}
	}else{
		$("#dias_de_viaje2").html(noDiasDeViaje);
		$("#dias_de_viaje").val(noDiasDeViaje);
		$("#noches").val(noDiasDeViaje-1);
	}
}
//Funcion agregada 04082011
//Muestra u Oculta una sección mediante la la selección de un check (Requerimientos de anticipo y Leyendas hotel, horpedaje 08082011)
function desplegar(Seccion){
	if (Seccion.style.display=="none"){
		Seccion.style.display="";
	}else{
		Seccion.style.display="none";
	}
}
//disabled='disable' Habilita el botón para guardar solicitudes
function habilitar(){
	var frm=document.detallesItinerarios;
	if (frm.cat_cecos_cargado[0].change== true){
		frm.guardarComp.disabled = false;
		frm.guardarprevComp.disabled = false;
	}
	else{
		frm.guardarComp.disabled = false;
		frm.guardarprevComp.disabled = false;
	}
}
// Al parecer esta funcion lo que hace es configurar los calendarios
$.extend(DateInput.DEFAULT_OPTS, {
	stringToDate: function(string) {
		var matches;
		if ((matches = string.match(/^(\d{4,4})-(\d{2,2})-(\d{2,2})$/))) {
			return new Date(matches[1], matches[2] - 1, matches[3]);
		} else {
			return null;
		};
	},
	dateToString: function(date) {
		var month = (date.getMonth() + 1).toString();
		var dom = date.getDate().toString();
		if (month.length == 1) month = "0" + month;
		if (dom.length == 1) dom = "0" + dom;
		return dom + "/" + month + "/" + date.getFullYear();
	},
	rangeStart: function(date) {
		return this.changeDayTo(this.start_of_week, new Date(date.getFullYear(), date.getMonth()), -1);
	},
	rangeEnd: function(date) {
		return this.changeDayTo((this.start_of_week - 1) % 7, new Date(date.getFullYear(), date.getMonth() + 1, 0), 1);
	}
});
//validación campos numericos sin punto
function validaNumSinPunto(valor){
	cTecla=(document.all)?valor.keyCode:valor.which;
	if(cTecla==8) return true;
	patron=/^([0-9]{1,2})?$/;
	cTecla= String.fromCharCode(cTecla);
	return patron.test(cTecla);
}
// Esta funcion muestra u oculta el campo de kilometraje
var ToA=true;
function activaCb(){
	var frm=document.detallesItinerarios;
	if(frm.select_medio_transporte.value=="Terrestre"){
		$("#msg_agencia_viajes").css("display","none");
		$("#select_tipo_transporte").css("display","block");
		$("#tipo_transporte_label").css("display","block");
		ToA=true;
	} else if(frm.select_medio_transporte.value=="Aéreo"){
		$("#msg_agencia_viajes").removeAttr("style");
		$('#kilometraje').val("");
		$('#kilometraje_data').css("display","none");
		$('#kilometraje_data').val("");
		$("#kilometraje_label").css("display","none");
		$("#select_tipo_transporte").css("display","none");
		$("#select_tipo_transporte").val("-1");
		$("#tipo_transporte_label").css("display","none");
		ToA=false;
	} else {
		$("#msg_agencia_viajes").css("display","none");
		$('#kilometraje').val("");
		$('#kilometraje_data').css("display","none");
		$('#kilometraje_data').val("");
		$("#kilometraje_label").css("display","none");
		$("#select_tipo_transporte").css("display","none");
		$("#select_tipo_transporte").val("-1");
		$("#tipo_transporte_label").css("display","none");
		$("#divisa_renta_agencia_label").css("display", "none");
		$("#divisa_renta_agencia_dato").css("display", "none");
		$("#Precio_renta_agencia_label").css("display", "none");
		$("#Precio_renta_agencia_dato").css("display", "none");
		ToA=false;
	}
}
function mostrarPrecioDivisaRentaAuto(){
	var frm=document.detallesItinerarios;
	if(frm.CheckRentaAuto.checked){
		$("#divisa_renta_agencia_label").css("display", "block");
		$("#divisa_renta_agencia_dato").css("display", "block");
		$("#Precio_renta_agencia_label").css("display", "block");
		$("#Precio_renta_agencia_dato").css("display", "block");
	}else{
		$("#divisa_renta_agencia_label").css("display", "none");
		$("#divisa_renta_agencia_dato").css("display", "none");
		$("#Precio_renta_agencia_label").css("display", "none");
		$("#Precio_renta_agencia_dato").css("display", "none");
		$("#divisa_renta_agencia_dato").find('option:first').attr('selected', 'selected').parent('select');
		$("#Precio_renta_agencia_dato").val("0");
	}
}
//funcion que me permitira comparar si la fecha ingresada es mayor o menor
function comparaFechas(fecha, fecha2){
  	var xMes=fecha.substring(3, 5);
    var xDia=fecha.substring(0, 2);
    var xAno=fecha.substring(6,10);
    var yMes=fecha2.substring(3, 5);
    var yDia=fecha2.substring(0, 2);
    var yAno=fecha2.substring(6,10);
    if (xAno> yAno)
    {
        return(true);
    }
    else
    {
      if (xAno == yAno)
      {
        if (xMes > yMes)
        {
            return(true);
        }
        else
        {
          if (xMes == yMes)
          {
            if (xDia> yDia)
              return(true);
            else
              return(false);
          }
          else
            return(false);
        }
      }
      else
        return(false);
    }
}

function validaCamposDeItinerario(){
	var frm=document.detallesItinerarios;
	var grupo  = document.getElementById("detallesItinerarios").hospedaje;
    var grupo2 = document.getElementById("detallesItinerarios").auto;
	var grupo3 = document.getElementById("detallesItinerarios").enviar_auto_agencia;
	var grupo4 = document.getElementById("detallesItinerarios").enviar_hosp_agencia;
	if(frm.motive.value.length<5) {
		alert("El motivo debe tener al menos 5 caracteres.");
		frm.motive.focus();
		return false;
	}else if(frm.select_tipo_viaje_pasaje.value == "-1"){
		alert("Seleccione el Tipo de viaje");
		return false;
	}else if(frm.select_tipo_viaje.value == "-1"){
		alert("Seleccione la Zona geografíca");
		return false;
	} else if(frm.select_tipo_viaje.value != "1" && frm.select_region_viaje.value == "" ) {
		alert("Seleccione la región");
		return false;
	} else if(frm.origen.value==""){
		alert("Ingrese el origen");
		return false;
	} else if(frm.destino.value==""){
		alert("Ingrese el destino");
		return false;
	} else if(frm.select_medio_transporte.value == "Seleccione"){
		alert("Seleccione el medio de transporte");
		return false;
	} else if (frm.select_medio_transporte.value == "Terrestre" && frm.select_tipo_transporte.value == "-1" ) {
		alert("Seleccione el tipo de transporte");
		return false;
	} else if (frm.select_medio_transporte.value == "Terrestre" && (frm.kilometraje.value == "0.00" || frm.kilometraje.value == "" || frm.kilometraje.value == "0" ) && ToA == true && frm.select_tipo_transporte.value != "3") {
		alert("Ingrese el kilometraje");
		return false;
	}else if(frm.hospedaje[0].checked == false && frm.hospedaje[1].checked == false){
		alert("Los campos con (*) son obligatorios. Favor de llenar el dato Hospedaje.");
		return false;
	}else if(frm.hospedaje[0].checked == true && frm.enviar_hosp_agencia[0].checked == false && frm.enviar_hosp_agencia[1].checked == false){
		alert("Los campos (*) son obligatorios. Favor de seleccionar una opción en el campo \"Enviar a Agencia de Viajes\"");
		return false;
	}else if(frm.auto[0].checked == false && frm.auto[1].checked == false){
		alert("Los campos con (*) son obligatorios. Favor de llenar el dato Renta de auto.");
		return false;
	}else if(frm.auto[0].checked == true && frm.enviar_auto_agencia[0].checked == false && frm.enviar_auto_agencia[1].checked == false){
		alert("Los campos (*) son obligatorios. Favor de seleccionar una opción en el campo \"Enviar a Agencia de Viajes\"");
		return false;
	} else if (!compare_dates(frm.fechaLlegada.value,frm.fecha.value) && frm.select_tipo_viaje_pasaje.value == 2) {
		alert("La Fecha Final es menor que la Fecha Inicial.");
		return false;
	}
		//Funcion que nos permite validar la fecha si se esta editando un itinerario
		if((estatus_en_edicion_de_itinerario == true)){
			var itinerarioEdit=editaDia;
			if(($("#select_tipo_viaje_pasaje_val").val() == 3) || ($("#select_tipo_viaje_pasaje_val").val() == "Multidestinos") ){
				var longitud = parseInt($("#solicitud_table>tbody >tr").length);
				if(longitud > 1){
						if($("#fecha").val() == $("#salida"+itinerarioEdit).val()){
							return true;
						}else{
							if( itinerarioEdit == 1){
									if(!comparaFechas($("#fecha").val(),$("#salida"+(itinerarioEdit+1)).val())){
										return true;
									}else{
										alert("La Fecha del Itinerario que ingresará es mayor a la fecha del itinerario que le precede ("+$("#salida"+(itinerarioEdit+1)).val()+"). Seleccione una fecha correcta.");
										return false;
									}
							}else if(itinerarioEdit == longitud ){										
									if(comparaFechas($("#fecha").val(),$("#salida"+(itinerarioEdit-1)).val())){
										return true;
									}else{
										alert("La Fecha del Itinerario que ingresará es menor a la fecha del itinerario que le antecede ("+$("#salida"+(itinerarioEdit-1)).val()+") . Seleccione una fecha correcta.");
										return false;
									}
							}else{
								if(($("#fecha").val() == $("#salida"+(itinerarioEdit-1)).val()) || ($("#fecha").val() == $("#salida"+(itinerarioEdit+1)).val()) ){
									return true;
								}else{
									if(comparaFechas($("#fecha").val(),$("#salida"+(itinerarioEdit-1)).val())){
										if(!comparaFechas($("#fecha").val(),$("#salida"+(itinerarioEdit+1)).val())){
											return true;
										}else{
											alert("La Fecha del Itinerario que ingresará es mayor a la fecha del itinerario que le precede ("+$("#salida"+(itinerarioEdit+1)).val()+"). Seleccione una fecha correcta.");
											return false;
										}
									}else{
										alert("La Fecha del Itinerario que ingresará es menor  a la fecha del itinerario que le antecede ("+$("#salida"+(itinerarioEdit-1)).val()+"). Seleccione una fecha correcta.");
										return false;
									}
								}
							}
						}
					}
			}
		}else{//Se esta ingresando un nuevo itinerario
			var longitud = parseInt($("#solicitud_table>tbody >tr").length);
			if( ($("#select_tipo_viaje_pasaje_val").val() == 3) || ($("#select_tipo_viaje_pasaje_val").val() == "Multidestinos")){
				if(longitud >= 1){
					for(var i=1;i<=longitud;i++){
						if(!compare_dates($("#fecha").val(),$("#salida"+i).val())){
							alert("La Fecha del Itinerario que ingresará es menor que el/los Itinerario(s) ingresados anteriormente.");
							return false;
						}
					}
				}
			}
		}
}//END validacion de campos de itinerario		
//Checa los radios tanto de hotel como de auto
function checa_radio_hospedaje(hospedaje) {
	for (i = 0; lcheck = hospedaje[i]; i++) {
		if (lcheck.checked) {
		   if(hospedaje[0].checked == true){
				var grupo4 = document.getElementById("detallesItinerarios").enviar_hosp_agencia;
				if(grupo4[0].checked == false && grupo4[1].checked == false){
					return false;
				}else{
					return true;
				}
			}else if(hospedaje[1].checked == true){
				return true;
			}
		}
	}
	return false;
}
function checa_radio_auto(auto) {
	for (i = 0; lcheck = auto[i]; i++) {
		if (lcheck.checked) {
			return true;
		}
	}
	return false;
}
function checa_radio_agencia_auto(enviar_auto_agencia) {
	for (i = 0; lcheck = enviar_auto_agencia[i]; i++) {
		if (lcheck.checked) {
			return true;
		}
	}
	return false;
}
function checa_radio_agencia_hospedaje(enviar_hosp_agencia) {
	for (j = 0; lcheck2 = enviar_hosp_agencia[j]; j++) {
		if (lcheck2.checked) {
				return true;
		}
	}
	return false;
}
function limpiarCamposDeItinerario(){
	var frm=document.detallesItinerarios;
	//restablece los campos
	$("#origen").val("");
	$("#destino").val("");
	$("#dias_de_viaje").val(1);
	$("#dias_de_viaje2").html("1");
	$("noches").html("0");
	$("#noches").val("0");
	$("#fecha").val(obtiene_fecha());
	$("#fechaLlegada").val(obtiene_fecha());
	$("#kilometraje").val("");
	$('#select_hora_salida').find('option:first').attr('selected', 'selected').parent('select');
	$('#select_hora_llegada').find('option:first').attr('selected', 'selected').parent('select');
	$('#select_tipo_viaje').find('option:first').attr('selected', 'selected').parent('select');
	verificar_region(1);
	$('#select_medio_transporte').find('option:first').attr('selected', 'selected').parent('select');
	activaCb();
	$("#tipo_transporte_label").css("display","none");
	$("#select_tipo_transporte").css("display","none");
	$("#select_tipo_transporte").val("-1");
	$("#kilometraje_label").css("display","none");
	$('#kilometraje_data').css("display","none");
	$('#kilometraje_data').val("");
	$("#CheckTAgencia").removeAttr("checked");
	$("#CheckHAgencia").removeAttr("checked");
	frm.hospedaje[1].checked = true;
	frm.auto[1].checked = true;
	radioH(0);
	radioA(0);
	$("#capSelectHotel").fadeOut(1500);
	$('#capSelectHotel').find('option:first').attr('selected', 'selected').parent('select');
	$.post("services/ingreso_sin_recargar_proceso.php",{ idCd:$("#selectCd").val() },function(data){$("#capselectHotel2").html(data);});
	$("#msg_agencia_viajes").css("display","none");
}
function mostrarBotonesEditarEliminar(){
	var tablaLength = parseInt($("#solicitud_table>tbody>tr").length);
	for(var i = 1; i <= tablaLength; i++){
		$("#"+i+"edit").fadeIn("slow");
		$("#"+i+"del").fadeIn("slow");
	}
}
// Esta function se llama cuando se agrega un itinerario
function agregarItinerario(){
	var frm=document.detallesItinerarios;
	$("#"+parseInt(id)+"del").show();
	// Mostramos los botones de Edición y Eliminación
	mostrarBotonesEditarEliminar();
	//Valida que los campos obligatorios esten llenos
	if (validaCamposDeItinerario() == false){
		return false;
	}else if(restringeHotel() == false){
		return false;
	}
	// Obtiene el no. de dias y noches de viaje
	var DiasViaje="d??";//$("#dias_de_viaje").val();
	var NochesViaje= $("#total_noches").val();
	var CostoHospedaje= $("#TotalHospedaje").html();
	var costo_hospedaje_label = "costo h";
	var costo_renta_de_auto = "costo a";
	var destino = $("#destino").val();
	var fecha_de_salida = $("#fecha").val();
	var horario_de_salida = $("#select_hora_salida").val();
	var hospedaje = $("#hospedaje").val();
	var noches = "noches";
	var origen = $("#origen").val();
	var renta_de_auto = $("#auto").val();
	var transporte = $("#select_medio_transporte").val();
	var zona_geografica_id = $("#select_tipo_viaje").val();
	var zona_geografica = frm.select_tipo_viaje.options[frm.select_tipo_viaje.selectedIndex].text;
	if(zona_geografica_id != "1"){
		var region_id = $("#select_region_viaje option:selected").val();
		var region = $("#select_region_viaje option:selected").text();
	}else{
		var region_id = regionNacional; // Región Nacional => 2
		var region = $("#select_tipo_viaje option:selected").text();
	}
	var check1;
	if(frm.hospedaje[0].checked == true){
		check1 = "Si";
	}else{
		check1 = "No";
	}
	var check2;
	if(frm.auto[0].checked == true){
		check2 = "Si";
	}else{
		check2 = "No";
	}
	var km="N/A";
	if(frm.select_medio_transporte.value == "Terrestre"){
		km = frm.kilometraje.value;
	}
	//inicializacion
	var empA;
	var costoDA;
	var TDA;
	var TA;
	var totA;
	var diaR;
	var montoA;
	//Busca el ultimo ID de las partidas y le sumamos uno para el siguiente
	var id = parseInt($("#solicitud_table>tbody >tr").length);
	if(isNaN(id)){
		id=1;
	}else{
		id+=parseInt(1);
	}
	var tramite_id=gup("id");
	if(tramite_id != ""){
		var itinerario=$("#itinerarioActualOPosible").val();
		if(estatus_en_edicion_de_itinerario){
			 empA=$("#empresaAuto"+itinerario).val();
			 costoDA=$("#costoDia"+itinerario).val();
			 TDA=$("#tipoDivisa"+itinerario).val();
			 TA=$("#tipoAuto"+itinerario).val();
			 totA=$("#totalAuto"+itinerario).val();
			 diaRA=$("#diasRenta"+itinerario).val();
			 montoA=$("#montoPesos"+itinerario).val();
		}else{
			empA=$("#empresaAuto").val();
			 costoDA=parseFloat($("#costoDia").val().replace(/,/g,""));
			 TDA=$("#tipoDivisa").val();
			 TA=$("#tipoAuto").val();
			 totA=parseFloat($("#totalAuto").val().replace(/,/g,""));
			 diaRA=$("#diasRenta").val();
			 montoA=parseFloat($("#montoPesos").val().replace(/,/g,""));
		}
	}else{
		 empA=$("#empresaAuto").val();
		 costoDA=parseFloat($("#costoDia").val().replace(/,/g,""));
		 TDA=$("#tipoDivisa").val();
		 TA=$("#tipoAuto").val();
		 totA=parseFloat($("#totalAuto").val().replace(/,/g,""));
		 diaRA=$("#diasRenta").val();
		 montoA=parseFloat($("#montoPesos").val().replace(/,/g,""));
	}
	//Se chaca si se AGREGA o se ACTUALIZA un itinerario.
	if(estatus_en_edicion_de_itinerario){ //Se actualiza itinerario existente
		var total_hospedaje = 0;
		var total_noches = 0;
		id = frm.itinerarioActualOPosible.value;
		var registros = parseInt($("#hotel_table"+id+">tbody>tr").length);
		//==================Calculo
		var totalHCalculado=0;
		var totalDCalculado=0;
		for(i=1; i<=registros; i++){
			//======================HOTEL
			if(isNaN($("#montoP_"+id+"_"+i).val())){
				totalHCalculado= totalHCalculado + 0;
			}else{
				totalHCalculado= totalHCalculado + parseFloat($("#montoP_"+id+"_"+i).val());
			}
			if(isNaN($("#noches_"+id+"_"+i).val())){
				totalDCalculado= totalDCalculado + 0;
			}else{
				totalDCalculado= totalDCalculado + parseFloat($("#noches_"+id+"_"+i).val());
			}
		}
		var redondear = Math.round(totalHCalculado * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
		$("#TotalHospedaje").html(number_format(redondear,2,".",","));
		$("#total_noches").val(totalDCalculado);
		//============================AUTO
		var nuevaFila='';
		nuevaFila+="<td><input type='hidden' name='tipo_v"+id+"' id='tipo_v"+id+"' value='"+frm.select_tipo_viaje_pasaje.value+"'  readonly='readonly' /><div id='renglonS"+id+"' name='renglonS"+id+"'>"+id+"</div><input type='hidden' name='row"+id+"' id='row"+id+"' value='"+id+"' readonly='readonly' /></td>";
		nuevaFila+="<td><input type='hidden' name='zona_geo"+id+"' id='zona_geo"+id+"' value='"+zona_geografica+"' readonly='readonly' />"+zona_geografica+"</td>";
		nuevaFila+="<td><input type='hidden' name='region"+id+"' id='region"+id+"' value='"+region+"' readonly='readonly' /><input type='hidden' name='regionId"+id+"' id='regionId"+id+"' value='"+region_id+"' readonly='readonly' />"+region+"</td>";
		nuevaFila+="<td><input type='hidden' name='origen"+id+"' id='origen"+id+"' value='"+frm.origen.value+"' readonly='readonly' />"+frm.origen.value+"</td>";
		nuevaFila+="<td><input type='hidden' name='destino"+id+"' id='destino"+id+"' value='"+frm.destino.value+"' readonly='readonly' />"+frm.destino.value+"</td>";
		nuevaFila+="<td><div align='left'>"+frm.fecha.value+"<input type='hidden' name='salida"+id+"' id='salida"+id+"' value='"+frm.fecha.value+"' readonly='readonly' /></div></td>";
		nuevaFila+="<td><input type='hidden' name='hora"+id+"' id='hora"+id+"' value='"+frm.select_hora_salida.options[frm.select_hora_salida.selectedIndex].text+"' readonly='readonly' />"+frm.select_hora_salida.options[frm.select_hora_salida.selectedIndex].text+"</td>";
		if(frm.select_tipo_viaje_pasaje.options[frm.select_tipo_viaje_pasaje.selectedIndex].text == "Redondo"){
			nuevaFila+="<td><div align='left'>"+frm.fechaLlegada.value+"<input type='hidden' name='Fechallegada"+id+"' id='Fechallegada"+id+"' value='"+frm.fechaLlegada.value+"' readonly='readonly' /></div></td>";
			nuevaFila+="<td><div align='left'>"+frm.select_hora_llegada.options[frm.select_hora_llegada.selectedIndex].text+"<input type='hidden' name='Horallegada"+id+"' id='Horallegada"+id+"' value='"+frm.select_hora_llegada.options[frm.select_hora_llegada.selectedIndex].text+"' readonly='readonly' /></div></td>";
		}
		nuevaFila+="<td><input type='hidden' name='medio"+id+"' id='medio"+id+"' value='"+frm.select_medio_transporte.value+"' readonly='readonly' />"+frm.select_medio_transporte.value+"</td>";
		nuevaFila+="<td>"+check1+"<input type='hidden' name='CheckHAgencia"+id+"' id='CheckHAgencia"+id+"' value='"+frm.hospedaje[0].checked+"'></td>";
		nuevaFila+="<td><input type='hidden' name='costoHosp"+id+"' id='costoHosp"+id+"' value='"+$("#TotalHospedaje").html()+"' readonly='readonly' />"+$("#TotalHospedaje").html()+"</td>";
		nuevaFila+="<td><input type='hidden' name='noches"+id+"' id='noches"+id+"' value='"+totalDCalculado+"' readonly='readonly' />"+totalDCalculado+"</td>";
		nuevaFila+="<td>"+check2+"<input type='hidden' name='CheckTAgencia"+id+"' id='CheckTAgencia"+id+"' value='"+frm.auto[0].checked+"'></td>";
		nuevaFila+="<input type='hidden' name='kilometraje"+id+"' id='kilometraje"+id+"' value='"+km+"' readonly='readonly' />";
		nuevaFila+="<td><input type='hidden' name='costoAuto"+id+"' id='costoAuto"+id+"' value='"+montoA+"' readonly='readonly' />"+number_format(montoA,2,".",",")+"</td>";
		nuevaFila+="<td><div align='center' name='"+id+"edit' id='"+id+"edit'><img src='../../images/addedit.png' alt='Click aqu&iacute; para editar Itinerario'name='"+id+"edit' id='"+id+"edit' onclick='editarItinerario(this.id);verificar_itinerario();verificar_itinerario2();' style='cursor:pointer'/></div></td>";
		nuevaFila+="<td><div align='center' name='"+id+"del' id='"+id+"del'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+id+"del' id='"+id+"del' onmousedown='if(confirmacion_conceptos_eliminar()){calculoTotal_Anticipo(this.id);borrarTabla(this.id,\"hotel_table\",\"no\",\"\",\"delDiv\",\"area_counts_div\");checa_itinerarios();borrarRenglon4(this.id,\"solicitud_table\",\"rowCount\",\"rowDel\",\"renglonS\",\"edit\",\"del\",\"itinerarioActualOPosible\");verificaNoItinerarios();}' style='cursor:pointer'/></div></td>";
		nuevaFila+="<input type='hidden' name='noDias"+id+"' id='noDias"+id+"' value='0' readonly='readonly' />";
		nuevaFila+="<input type='hidden' name='noDiasAI"+id+"' id='noDiasAI"+id+"' value='0' readonly='readonly' />";
		nuevaFila+="<input type='hidden' name='noche"+id+"' id='noche"+id+"' value='noches' readonly='readonly' />";
		nuevaFila+="<input type='hidden' name='tipo"+id+"' id='tipo"+id+"' value='"+frm.select_tipo_viaje.options[frm.select_tipo_viaje.selectedIndex].text+"'  readonly='readonly' />";
		nuevaFila+="<input type='hidden' name='select_tipo_transporte"+id+"' id='select_tipo_transporte"+id+"' value='"+frm.select_tipo_transporte.options[frm.select_tipo_transporte.selectedIndex].text+"'>";
		//Este valor es para saber si se requiere que agencia cotize el hotel
		nuevaFila+="<input type='hidden' name='CheckHEnviarAgencia"+id+"' id='CheckHEnviarAgencia"+id+"' value='"+frm.enviar_hosp_agencia[0].checked+"'>";
		//Este valor es para saber si se requiere que agencia cotize el auto
		nuevaFila+="<input type='hidden' name='CheckTEnviarAgencia"+id+"' id='CheckTEnviarAgencia"+id+"' value='"+frm.enviar_auto_agencia[0].checked+"'>";
		//FECHA DE LLEGADA Y HORA DE LLEGADA
		nuevaFila+="<input type='hidden' name='fechaLlegada"+id+"' id='fechaLlegada"+id+"' value='"+frm.fechaLlegada.value+"'>";
		nuevaFila+="<input type='hidden' name='select_hora_llegada"+id+"' id='select_hora_llegada"+id+"' value='"+frm.select_hora_llegada.options[frm.select_hora_llegada.selectedIndex].text+"'>";
				nuevaFila+="<input type='hidden' name='empresaAuto"+id+"' id='empresaAuto"+id+"' value='"+empA+"' readonly='readonly' />";
				nuevaFila+="<input type='hidden' name='costoDia"+id+"' id='costoDia"+id+"' value='"+costoDA+"' readonly='readonly' />";
				nuevaFila+="<input type='hidden' name='tipoDivisa"+id+"' id='tipoDivisa"+id+"' value='"+TDA+"' readonly='readonly' />";
				nuevaFila+="<input type='hidden' name='tipoAuto"+id+"' id='tipoAuto"+id+"' value='"+TA+"' readonly='readonly' />";
				nuevaFila+="<input type='hidden' name='totalAuto"+id+"' id='totalAuto"+id+"' value='"+totA+"' readonly='readonly' />";
				nuevaFila+="<input type='hidden' name='diasRenta"+id+"' id='diasRenta"+id+"' value='"+diaRA+"' readonly='readonly' />";
				nuevaFila+="<input type='hidden' name='montoPesos"+id+"' id='montoPesos"+id+"' value='"+montoA+"' readonly='readonly' />";
				nuevaFila+="<input type='hidden' name='idItinerarioDB"+id+"' id='idItinerarioDB"+id+"' value='0' readonly='readonly' />";
		$("#solicitud_table>tbody").find("tr").eq(id-1).html(nuevaFila);
		//Se pone el estatus en edicion
		estatus_en_edicion_de_itinerario = false;
		//Se cambia la etiqueta del boton agregar itinerario por actualizar
		$("#registrar_comp").attr("value","     Registrar Itinerario");
		if(frm.select_tipo_viaje_pasaje.options[frm.select_tipo_viaje_pasaje.selectedIndex].text == "Sencillo" || frm.select_tipo_viaje_pasaje.options[frm.select_tipo_viaje_pasaje.selectedIndex].text == "Redondo"){
			document.getElementById("registrar_comp").disabled="disable";
		}
	}else{ //Se agrega un nuevo itinerario
		//conteo de la partida
		frm.rowCount.value=parseInt(frm.rowCount.value)+parseInt(1);
		//Creamos la nueva fila y sus respectivas columnas
		var nuevaFila='<tr>';
		nuevaFila+="<td><input type='hidden' name='tipo_v"+id+"' id='tipo_v"+id+"' value='"+frm.select_tipo_viaje_pasaje.value+"'  readonly='readonly' /><div id='renglonS"+id+"' name='renglonS"+id+"'>"+id+"</div><input type='hidden' name='row"+id+"' id='row"+id+"' value='"+id+"' readonly='readonly' /></td>";
		nuevaFila+="<td><input type='hidden' name='zona_geo"+id+"' id='zona_geo"+id+"' value='"+zona_geografica+"' readonly='readonly' />"+zona_geografica+"</td>";
		nuevaFila+="<td><input type='hidden' name='region"+id+"' id='region"+id+"' value='"+region+"' readonly='readonly' /><input type='hidden' name='regionId"+id+"' id='regionId"+id+"' value='"+region_id+"' readonly='readonly' />"+region+"</td>";
		nuevaFila+="<td><input type='hidden' name='origen"+id+"' id='origen"+id+"' value='"+frm.origen.value+"' readonly='readonly' />"+frm.origen.value+"</td>";
		nuevaFila+="<td><input type='hidden' name='destino"+id+"' id='destino"+id+"' value='"+frm.destino.value+"' readonly='readonly' />"+frm.destino.value+"</td>";
		nuevaFila+="<td><div align='left'>"+frm.fecha.value+"<input type='hidden' name='salida"+id+"' id='salida"+id+"' value='"+frm.fecha.value+"' readonly='readonly' /></div></td>";
		nuevaFila+="<td><input type='hidden' name='hora"+id+"' id='hora"+id+"' value='"+frm.select_hora_salida.options[frm.select_hora_salida.selectedIndex].text+"' readonly='readonly' />"+frm.select_hora_salida.options[frm.select_hora_salida.selectedIndex].text+"</td>";
		if(frm.select_tipo_viaje_pasaje.options[frm.select_tipo_viaje_pasaje.selectedIndex].text == "Redondo"){
			nuevaFila+="<td><div align='left'>"+frm.fechaLlegada.value+"<input type='hidden' name='Fechallegada"+id+"' id='Fechallegada"+id+"' value='"+frm.fechaLlegada.value+"' readonly='readonly' /></div></td>";
			nuevaFila+="<td><div align='left'>"+frm.select_hora_llegada.options[frm.select_hora_llegada.selectedIndex].text+"<input type='hidden' name='Horallegada"+id+"' id='Horallegada"+id+"' value='"+frm.select_hora_llegada.options[frm.select_hora_llegada.selectedIndex].text+"' readonly='readonly' /></div></td>";
		}
		nuevaFila+="<td><input type='hidden' name='medio"+id+"' id='medio"+id+"' value='"+frm.select_medio_transporte.value+"' readonly='readonly' />"+frm.select_medio_transporte.value+"</td>";
		nuevaFila+="<td>"+check1+"<input type='hidden' name='CheckHAgencia"+id+"' id='CheckHAgencia"+id+"' value='"+frm.hospedaje[0].checked+"'></td>";
		nuevaFila+="<td><input type='hidden' name='costoHosp"+id+"' id='costoHosp"+id+"' value='"+CostoHospedaje+"' readonly='readonly' />"+CostoHospedaje+"</td>";
		nuevaFila+="<td><input type='hidden' name='noches"+id+"' id='noches"+id+"' value='"+NochesViaje+"' readonly='readonly' />"+NochesViaje+"</td>";
		nuevaFila+="<td>"+check2+"<input type='hidden' name='CheckTAgencia"+id+"' id='CheckTAgencia"+id+"' value='"+frm.auto[0].checked+"'></td>";
		nuevaFila+="<input type='hidden' name='kilometraje"+id+"' id='kilometraje"+id+"' value='"+km+"' readonly='readonly' />";
		nuevaFila+="<td><input type='hidden' name='costoAuto"+id+"' id='costoAuto"+id+"' value='"+montoA+"' readonly='readonly' />"+number_format(montoA,2,".",",")+"</td>";
		nuevaFila+="<td><div align='center' name='"+id+"edit' id='"+id+"edit'><img src='../../images/addedit.png' alt='Click aqu&iacute; para editar Itinerario' name='"+id+"edit' id='"+id+"edit'  onclick='editarItinerario(this.id);verificar_itinerario();verificar_itinerario2();' style='cursor:pointer'/></div></td>";
		nuevaFila+="<td><div align='center' name='"+id+"del' id='"+id+"del'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+id+"del' id='"+id+"del' onmousedown='if(confirmacion_conceptos_eliminar()){calculoTotal_Anticipo(this.id);borrarTabla(this.id,\"hotel_table\",\"no\",\"\",\"delDiv\",\"area_counts_div\");checa_itinerarios();borrarRenglon4(this.id,\"solicitud_table\",\"rowCount\",\"rowDel\",\"renglonS\",\"edit\",\"del\",\"itinerarioActualOPosible\");verificaNoItinerarios();}' style='cursor:pointer'/></div></td>";
		nuevaFila+="<input type='hidden' name='noDias"+id+"' id='noDias"+id+"' value='0' readonly='readonly' />";
		nuevaFila+="<input type='hidden' name='noDiasAI"+id+"' id='noDiasAI"+id+"' value='0' readonly='readonly' />";
		nuevaFila+="<input type='hidden' name='noche"+id+"' id='noche"+id+"' value='noches' readonly='readonly' />";
		nuevaFila+="<input type='hidden' name='tipo"+id+"' id='tipo"+id+"' value='"+frm.select_tipo_viaje.options[frm.select_tipo_viaje.selectedIndex].text+"'  readonly='readonly' />";
		nuevaFila+="<input type='hidden' name='select_tipo_transporte"+id+"' id='select_tipo_transporte"+id+"' value='"+frm.select_tipo_transporte.options[frm.select_tipo_transporte.selectedIndex].text+"'>";
		//Este valor es para saber si se requiere que agencia cotize el hotel
		nuevaFila+="<input type='hidden' name='CheckHEnviarAgencia"+id+"' id='CheckHEnviarAgencia"+id+"' value='"+frm.enviar_hosp_agencia[0].checked+"'>";
		//Este valor es para saber si se requiere que agencia cotize el auto
		nuevaFila+="<input type='hidden' name='CheckTEnviarAgencia"+id+"' id='CheckTEnviarAgencia"+id+"' value='"+frm.enviar_auto_agencia[0].checked+"'>";
		//FECHA DE LLEGADA Y HORA DE LLEGADA
		nuevaFila+="<input type='hidden' name='fechaLlegada"+id+"' id='fechaLlegada"+id+"' value='"+frm.fechaLlegada.value+"'>";
		nuevaFila+="<input type='hidden' name='select_hora_llegada"+id+"' id='select_hora_llegada"+id+"' value='"+frm.select_hora_llegada.options[frm.select_hora_llegada.selectedIndex].text+"'>";
				nuevaFila+="<input type='hidden' name='empresaAuto"+id+"' id='empresaAuto"+id+"' value='"+empA+"' readonly='readonly' />";
				nuevaFila+="<input type='hidden' name='costoDia"+id+"' id='costoDia"+id+"' value='"+costoDA+"' readonly='readonly' />";
				nuevaFila+="<input type='hidden' name='tipoDivisa"+id+"' id='tipoDivisa"+id+"' value='"+TDA+"' readonly='readonly' />";
				nuevaFila+="<input type='hidden' name='tipoAuto"+id+"' id='tipoAuto"+id+"' value='"+TA+"' readonly='readonly' />";
				nuevaFila+="<input type='hidden' name='totalAuto"+id+"' id='totalAuto"+id+"' value='"+totA+"' readonly='readonly' />";
				nuevaFila+="<input type='hidden' name='diasRenta"+id+"' id='diasRenta"+id+"' value='"+diaRA+"' readonly='readonly' />";
				nuevaFila+="<input type='hidden' name='montoPesos"+id+"' id='montoPesos"+id+"' value='"+montoA+"' readonly='readonly' />";
				nuevaFila+="<input type='hidden' name='idItinerarioDB"+id+"' id='idItinerarioDB"+id+"' value='0' readonly='readonly' />";
		nuevaFila+= '</tr>';
		$("#solicitud_table").append(nuevaFila);
		//Se chaca si existe la tabla de hotel correspondiente al itinerario;
		if($("#hotel_table"+id).val() == undefined){
			var nuevo_div = document.createElement("div");
			document.getElementById('area_tabla_div').appendChild(nuevo_div);
			var nuevo_div2 = document.createElement("div");
			document.getElementById('area_counts_div').appendChild(nuevo_div2);
		}
		activar_o_desactivar_campos_de_itinerario();
		if($("#select_tipo_viaje_pasaje_val").val() == "Multidestinos"){
			$("#registrar_comp").removeAttr("disabled");
			$("#origen").removeAttr("readonly");
			 $("#destino").removeAttr("readonly");
			 $("#kilometraje").removeAttr("readonly");
		}
		document.getElementById("select_tipo_viaje_pasaje").disabled="disable";
		//Se cran los divs que contendran la tabla y contadores de los conceptos
		crear_divs_area_conceptos();
		crear_tabla_concepto(id);
	}
	// Limpiar el campo de Cotización de Hotel
	$("#TotalHospedaje").html("0.00");
	//Despues de agregar el nuevo itinerario, se guarda el siguiente numero posible de itinerario.
	frm.itinerarioActualOPosible.value = parseInt($("#solicitud_table>tbody >tr").length)+parseInt(1);;
	frm.tipoDeViaje.value = frm.select_tipo_viaje.selectedIndex;
	frm.idDeRegion.value = frm.select_region_viaje.value;
	limpiarCamposDeItinerario();
	// Habilita el boton de "Agregar conceptos"			
	$("#addConcepto").removeAttr("disabled");
	$("#registrar_comp").removeAttr("disabled");
	desactivarDatosGenerales(frm.select_tipo_viaje_pasaje.value);
	frm.hospedaje[0].checked = false;
    frm.hospedaje[1].checked = false;
	frm.auto[0].checked = false;
    frm.auto[1].checked = false;
	frm.enviar_hosp_agencia[0].checked = false;
    frm.enviar_hosp_agencia[1].checked = false;
	frm.enviar_auto_agencia[0].checked = false;
    frm.enviar_auto_agencia[1].checked = false;
	limpiarCamposDeAuto(1,0);
	$("#total_noches").val("0");
	//Dehabilita botones de edicion: Auto,Hotel
	$("#divbotonAuto").css("display", "none");
	$("#divbotonHotel").css("display", "none");
	calculoTotal_Anticipo(0);
	calculoDiasSol();
	revisarComboConceptos();
}
function calculoTotal_Anticipo(indice){
	var suma=0;
	var restaC=0;
	indice = parseInt(indice);
	var id = parseInt($("#solicitud_table>tbody >tr").length);
	for(i=1; i<=id; i++){
		//Tomamos el valor del hospedaje.
		if($("#costoHosp"+i).val()!=undefined){
			if(i!=indice)
				suma = parseFloat(suma) + parseFloat($("#costoHosp"+i).val().replace(/,/g,""));
		}
		//Tomamos el valor del Costo del auto.
		if($("#montoPesos"+i).val()!=undefined){
			if(i!=indice)
				suma = parseFloat(suma)+parseFloat($("#montoPesos"+i).val().replace(/,/g,""));
		}
	}
	var toatalC = parseFloat($("#anticipoC").val().replace(/,/g,""));
	$("#totalanticipo").val(toatalC);
	var longitud = parseInt($("#conceptos_table>tbody >tr").length);
	for(var i=1;i<=longitud;i++){
		//Seleccionaremos  el monto para elconcepto tipo hotel
		if((parseInt(document.getElementById("Concepto"+i).value) == 5) || (parseInt(document.getElementById("Concepto"+i).value) == 10)){
			  restaC=parseFloat(restaC)+parseFloat(document.getElementById("MontoEnPesos"+i).value.replace(/,/g,""));
		}
	}
	if(gup("id") != ""){
		var idEdit=gup("id");
		$("#totalA").val(number_format(((suma+toatalC+parseFloat($("#montoAvionSol").val()))-restaC),2,".",","));
		$("#totalSol").val(number_format(((suma+toatalC+parseFloat($("#montoAvionSol").val()))-restaC),2,".",","));
	}else{
		$("#totalA").val(number_format(((suma+toatalC)-restaC),2,".",","));
		$("#totalSol").val(number_format(((suma+toatalC)-restaC),2,".",","));
	}
}
// Esta function se llama cuando se agrega un itinerario desde la base de datos al editar la solicitud
function agregarItinerarioFromDB(datos){
	var frm=document.detallesItinerarios;
	// Obtiene el no. de dias y noches de viaje
	var NochesViaje= datos['h_noches'];
	var CostoHospedaje= datos['h_total_pesos'];
	var destino = datos['svi_destino'];
	var fecha_de_salida = datos['svi_fecha_salida'];
	var fecha_de_llegada = datos['svi_fecha_llegada'];
	var horario_de_salida = datos['svi_horario_salida'];
	var horario_de_llegada = datos['svi_horario_llegada'];
	var checkHotel = datos['svi_hotel'];
	var checkAuto = datos['svi_renta_auto'];
	var checkHotelEnviarAgencia = datos['svi_hotel_agencia'];
	var checkAutoEnviarAgencia = datos['svi_renta_auto_agencia'];
	var hospedaje = $("#hospedaje").val();
	var noches = "noches";
	var origen = datos['svi_origen'];
	var renta_de_auto = $("#auto").val();
	var TotalAuto = datos['svi_total_pesos_auto'];
	var transporte = datos['svi_tipo_transporte'];
	var Tipotransporte = datos['svi_medio'];
	//var tipoTransporteVal="";
	var zona_geografica = datos['svi_tipo_viaje'];
	var region_id = datos['svi_region'];
	var region = datos['re_nombre'];
	var check1;
	if(checkHotel == 'true'){
		check1 = "Si";
	}else{
		check1 = "No";
	}
	var check2;
	if(checkAuto == 'true'){
		check2 = "Si";
	}else{
		check2 = "No";
	}
	var km="N/A";
	if(transporte == "Terrestre"){
		km = datos['svi_kilometraje'];
	}
	//Busca el ultimo ID de las partidas y le sumamos uno para el siguiente
	var id = parseInt($("#solicitud_table>tbody >tr").length);
	if(isNaN(id)){
		id=1;
	}else{
		id+=parseInt(1);
	}
	//valores para la divisa
	var valorDivisa="";
	if(datos['svi_divisa_auto'] == "MXN"){
		valorDivisa = 1;
	}else if(datos['svi_divisa_auto'] == "USD"){
		valorDivisa= 2;
	}else if(datos['svi_divisa_auto'] == "EUR"){
		valorDivisa= 3;
	}
	//Se agrega un nuevo itinerario
	//conteo de la partida
	frm.rowCount.value=parseInt(frm.rowCount.value)+parseInt(1);
	//Creamos la nueva fila y sus respectivas columnas
	//extraccion de valor de tipo viaje		
	var nuevaFila='<tr>';
	nuevaFila+="<td><input type='hidden' name='tipo_v"+id+"' id='tipo_v"+id+"' value='"+frm.select_tipo_viaje_pasaje.value+"'  readonly='readonly' /><div id='renglonS"+id+"' name='renglonS"+id+"'>"+id+"</div><input type='hidden' name='row"+id+"' id='row"+id+"' value='"+id+"' readonly='readonly' /></td>";
	nuevaFila+="<td><input type='hidden' name='zona_geo"+id+"' id='zona_geo"+id+"' value='"+zona_geografica+"' readonly='readonly' />"+zona_geografica+"</td>";
	nuevaFila+="<td><input type='hidden' name='region"+id+"' id='region"+id+"' value='"+region+"' readonly='readonly' /><input type='hidden' name='regionId"+id+"' id='regionId"+id+"' value='"+region_id+"' readonly='readonly' />"+region+"</td>";
	nuevaFila+="<td><input type='hidden' name='origen"+id+"' id='origen"+id+"' value='"+origen+"' readonly='readonly' />"+origen+"</td>";
	nuevaFila+="<td><input type='hidden' name='destino"+id+"' id='destino"+id+"' value='"+destino+"' readonly='readonly' />"+destino+"</td>";
	nuevaFila+="<td><div align='left'>"+fecha_de_salida+"<input type='hidden' name='salida"+id+"' id='salida"+id+"' value='"+fecha_de_salida+"' readonly='readonly' /></div></td>";
	nuevaFila+="<td><input type='hidden' name='hora"+id+"' id='hora"+id+"' value='"+horario_de_salida+"' readonly='readonly' />"+horario_de_salida+"</td>";
	if($("#select_tipo_viaje_pasaje_val").val() == "Redondo"){
		nuevaFila+="<td><div align='left'>"+fecha_de_llegada+"<input type='hidden' name='fechaLlegada"+id+"' id='fechaLlegada"+id+"' value='"+fecha_de_llegada+"' readonly='readonly' /></div></td>";
		nuevaFila+="<td><div align='left'>"+horario_de_llegada+"<input type='hidden' name='select_hora_llegada"+id+"' id='select_hora_llegada"+id+"' value='"+horario_de_llegada+"' readonly='readonly' /></div></td>";
		nuevaFila+="<td><input type='hidden' name='medio"+id+"' id='medio"+id+"' value='"+transporte+"' readonly='readonly' />"+transporte+"</td>";
	}else{
		nuevaFila+="<td><input type='hidden' name='medio"+id+"' id='medio"+id+"' value='"+transporte+"' readonly='readonly' />"+transporte+"</td>";
	}
	nuevaFila+="<td>"+check1+"<input type='hidden' name='CheckHAgencia"+id+"' id='CheckHAgencia"+id+"' value='"+checkHotel+"'></td>";
	nuevaFila+="<td><input type='hidden' name='costoHosp"+id+"' id='costoHosp"+id+"' value='"+CostoHospedaje+"' readonCostly='readonly' />"+CostoHospedaje+"</td>";
	nuevaFila+="<td><input type='hidden' name='noches"+id+"' id='noches"+id+"' value='"+NochesViaje+"' readonly='readonly' />"+NochesViaje+"</td>";
	nuevaFila+="<td>"+check2+"<input type='hidden' name='CheckTAgencia"+id+"' id='CheckTAgencia"+id+"' value='"+checkAuto+"'></td>";
	nuevaFila+="<td><input type='hidden' name='costoAuto"+id+"' id='costoAuto"+id+"' value='"+TotalAuto+"' readonly='readonly' />"+TotalAuto+"</td>";
	nuevaFila+="<input type='hidden' name='kilometraje"+id+"' id='kilometraje"+id+"' value='"+km+"' readonly='readonly' />";
	nuevaFila+="<td><div align='center'><img src='../../images/addedit.png' alt='Click aqu&iacute; para editar Itinerario' name='"+id+"edit' id='"+id+"edit' onclick='editarItinerario(this.id)' style='cursor:pointer'/></div></td>";
	nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+id+"del' id='"+id+"del' onmousedown='if(confirmacion_conceptos_eliminar()){calculoTotal_Anticipo(this.id);borrarTabla(this.id,\"hotel_table\",\"no\",\"\",\"delDiv\",\"area_counts_div\");checa_itinerarios();borrarRenglon4(this.id,\"solicitud_table\",\"rowCount\",\"rowDel\",\"renglonS\",\"edit\",\"del\",\"itinerarioActualOPosible\");verificaNoItinerarios();}' style='cursor:pointer'/></div></td>";
	nuevaFila+="<input type='hidden' name='noDias"+id+"' id='noDias"+id+"' value='0' readonly='readonly' />";
	nuevaFila+="<input type='hidden' name='noDiasAI"+id+"' id='noDiasAI"+id+"' value='0' readonly='readonly' />";
	nuevaFila+="<input type='hidden' name='noche"+id+"' id='noche"+id+"' value='noches' readonly='readonly' />";
	nuevaFila+="<input type='hidden' name='tipo"+id+"' id='tipo"+id+"' value='"+zona_geografica+"'  readonly='readonly' />";
	nuevaFila+="<input type='hidden' name='select_tipo_transporte"+id+"' id='select_tipo_transporte"+id+"' value='"+Tipotransporte+"'>";
	//Este valor es para saber si se requiere que agencia cotize el hotel
	nuevaFila+="<input type='hidden' name='CheckHEnviarAgencia"+id+"' id='CheckHEnviarAgencia"+id+"' value='"+checkHotelEnviarAgencia+"'>";
	//Este valor es para saber si se requiere que agencia cotize el auto
	nuevaFila+="<input type='hidden' name='CheckTEnviarAgencia"+id+"' id='CheckTEnviarAgencia"+id+"' value='"+checkAutoEnviarAgencia+"'>";
	//FECHA DE LLEGADA Y HORA DE LLEGADA
	nuevaFila+="<input type='hidden' name='fechaLlegada"+id+"' id='fechaLlegada"+id+"' value='"+fecha_de_llegada+"'>";
	nuevaFila+="<input type='hidden' name='select_hora_llegada"+id+"' id='select_hora_llegada"+id+"' value='"+horario_de_llegada+"'>";
			nuevaFila+="<input type='hidden' name='empresaAuto"+id+"' id='empresaAuto"+id+"' value='"+datos['svi_empresa_auto']+"' readonly='readonly' />";
			nuevaFila+="<input type='hidden' name='costoDia"+id+"' id='costoDia"+id+"' value='"+parseFloat(datos['svi_costo_auto'].replace(/,/g,""))+"' readonly='readonly' />";
			nuevaFila+="<input type='hidden' name='tipoDivisa"+id+"' id='tipoDivisa"+id+"' value='"+valorDivisa+"' readonly='readonly' />";
			nuevaFila+="<input type='hidden' name='tipoAuto"+id+"' id='tipoAuto"+id+"' value='"+datos['svi_tipo_auto']+"' readonly='readonly' />";
			nuevaFila+="<input type='hidden' name='totalAuto"+id+"' id='totalAuto"+id+"' value='"+parseFloat(datos['svi_total_auto'].replace(/,/g,""))+"' readonly='readonly' />";
			nuevaFila+="<input type='hidden' name='diasRenta"+id+"' id='diasRenta"+id+"' value='"+datos['svi_dias_renta']+"' readonly='readonly' />";
			nuevaFila+="<input type='hidden' name='montoPesos"+id+"' id='montoPesos"+id+"' value='"+parseFloat(datos['svi_total_pesos_auto'].replace(/,/g,""))+"' readonly='readonly' />";
			nuevaFila+="<input type='hidden' name='idItinerarioDB"+id+"' id='idItinerarioDB"+id+"' value='"+datos['svi_id']+"' readonly='readonly' />";
	nuevaFila+= '</tr>';
	$("#solicitud_table").append(nuevaFila);
	//Se chaca si existe la tabla de hotel correspondiente al itinerario;
	if($("#hotel_table"+id).val() == undefined){
		var nuevo_div = document.createElement("div");
		document.getElementById('area_tabla_div').appendChild(nuevo_div);
		var nuevo_div2 = document.createElement("div");
		document.getElementById('area_counts_div').appendChild(nuevo_div2);
	}
	activar_o_desactivar_campos_de_itinerario();
	if($("#select_tipo_viaje_pasaje_val").val() == "Multidestinos"){
		$("#registrar_comp").removeAttr("disabled");
		$("#origen").removeAttr("readonly");
		 $("#destino").removeAttr("readonly");
		 $("#kilometraje").removeAttr("readonly");
	}
	//Se cran los divs que contendran la tabla y contadores de los conceptos
	crear_divs_area_conceptos();
	crear_tabla_concepto(id);
	//Despues de agregar el nuevo itinerario, se guarda el siguiente numero posible de itinerario.
	frm.itinerarioActualOPosible.value = parseInt($("#solicitud_table>tbody >tr").length)+parseInt(1);;
	frm.tipoDeViaje.value = frm.select_tipo_viaje.selectedIndex;
	frm.idDeRegion.value = frm.select_region_viaje.value;
	// Habilita el boton de "Agregar conceptos"
	$("#addConcepto").removeAttr("disabled");
	//$("#guardarComp").removeAttr("disabled");
	desactivarDatosGenerales(frm.select_tipo_viaje_pasaje.value);
	calculoTotal_Anticipo();
	calculoDiasSol();
}
//limpiar radios en opcion NO
function limpiar_radiosNO(){
	var frm=document.detallesItinerarios;
	frm.enviar_auto_agencia[0].checked = false;
    frm.enviar_auto_agencia[1].checked = true;
}
//Para limpiar los radios
function limpiar_radios(){
	var frm=document.detallesItinerarios;
	frm.enviar_auto_agencia[0].checked = true;
    frm.enviar_auto_agencia[1].checked = false;
}
function ocultarBotonesEditarEliminar(itinerario){
	var tablaLength = parseInt($("#solicitud_table>tbody>tr").length);
	for(var i = 1; i <= tablaLength; i++){
		if(parseInt(itinerario) != i)
			$("#"+i+"edit").fadeOut("slow");
			$("#"+i+"del").fadeOut("slow");
	}
}
// Edita un itinerario
function editarItinerario(id){
	ocultarBotonesEditarEliminar(id);
	//Se checa si exite un itinerario en edicion
	tomaValid(id);
	if(estatus_en_edicion_de_itinerario){
		alert("Se esta editando un itinerario, de click antes en Actualizar Itinerario");
		return false;
	}
	if(!confirmacion_conceptos_editar(id)){
		return false;
	}
	$("#"+parseInt(id)+"del").hide();
	var frm=document.detallesItinerarios;
	id = parseInt(id);
	//Al editar un itinerario, se guarda el numero de itinerario que se va a editar.
	frm.itinerarioActualOPosible.value = id;
	$("#select_tipo_viaje_pasaje").val($("#tipo_v"+id).val()); //Sencillo, Redondo, Multidestinos
	$("#select_tipo_viaje").val($("#zona_geo"+id).val());//nacional, continental, intercontinental
	verificar_region($("#select_tipo_viaje").val(),$("#region"+id).val());
	$("#origen").val($("#origen"+id).val());
	$("#destino").val($("#destino"+id).val());
	$("#fecha").val($("#salida"+id).val());
	$("#select_hora_salida").val($("#hora"+id).val());
	
	if( $("#tipo_v"+id).val() == 2 ){
		$("#llegada_viaje_dato").css("visibility", "visible");
		$("#llegada_viaje_dato2").css("visibility", "visible");
		$("#regreso_titulo").css("visibility", "visible");				
		$("#fechaLlegada").val($("#fechaLlegada"+id).val());
		$("#select_hora_llegada").val($("#Horallegada"+id).val());
		estatus_tipo_de_viaje = false;			
	}
	
	$("#select_medio_transporte").val($("#medio"+id).val());
	activaCb();
	$("#select_tipo_transporte").val($("#select_tipo_transporte"+id).val());
	
	cargar_tipo_transporte($("#select_tipo_transporte").val())
	$("#kilometraje").val($("#kilometraje"+id).val());
	//Carga los options de hospedaje y renta de auto
	if($('#CheckHAgencia'+id).val() == "true"){
		frm.hospedaje[0].checked = true;
		radioH(1);
		var no_de_itinerario = frm.itinerarioActualOPosible.value;
		var no_de_hoteles = parseInt($("#hotel_table"+no_de_itinerario+">tbody >tr").length);
	}else{
		frm.hospedaje[1].checked = true;
		radioH(0);
	}
	if($('#CheckTAgencia'+id).val() == "true"){
		frm.auto[0].checked = true;
		radioA(1);
	}else{
		frm.auto[1].checked = true;
		radioA(0);
	}
	if($('#CheckHEnviarAgencia'+id).val() == "true"){
		frm.enviar_hosp_agencia[0].checked = true;
	}else{
		frm.enviar_hosp_agencia[1].checked = true;
	}
	if($('#CheckTEnviarAgencia'+id).val() == "true"){
		frm.enviar_auto_agencia[0].checked = true;
	}else{
		frm.enviar_auto_agencia[1].checked = true;
	}
	//se muestra el boton para la edicion (Auto/Hotel)
	//1 caso
	if(($('#CheckHAgencia'+id).val() == "true" && $('#CheckTAgencia'+id).val() == "true")){
		if(($('#CheckHEnviarAgencia'+id).val() == "true" && $('#CheckTEnviarAgencia'+id).val() == "true")){
			$("#divbotonHotel").css("display", "none");
			$("#divbotonAuto").css("display","none");
		}else if(($('#CheckHEnviarAgencia'+id).val() == "false" && $('#CheckTEnviarAgencia'+id).val() == "false")){
			$("#divbotonHotel").css("display", "block");
			$("#divbotonAuto").css("display","block");
		}
	}
	//2 caso
	if(($('#CheckHAgencia'+id).val() == "true" && $('#CheckTAgencia'+id).val() == "true")){
		if(($('#CheckHEnviarAgencia'+id).val() == "true" && $('#CheckTEnviarAgencia'+id).val() == "false")){
			$("#divbotonHotel").css("display", "none");
			$("#divbotonAuto").css("display","block");
		}else if(($('#CheckHEnviarAgencia'+id).val() == "false" && $('#CheckTEnviarAgencia'+id).val() == "true")){
			$("#divbotonHotel").css("display", "block");
			$("#divbotonAuto").css("display","none");
		}
	}
	//3 caso
	if(($('#CheckHAgencia'+id).val() == "true" && $('#CheckTAgencia'+id).val() == "false")){								
		if(($('#CheckHEnviarAgencia'+id).val() == "false" && $('#CheckTEnviarAgencia'+id).val() == "false")){
			$("#divbotonHotel").css("display", "block");
			$("#divbotonAuto").css("display","none");
		}
	}else if(($('#CheckHAgencia'+id).val() == "false" && $('#CheckTAgencia'+id).val() == "true")){
		if(($('#CheckHEnviarAgencia'+id).val() == "false" && $('#CheckTEnviarAgencia'+id).val() == "false")){
			$("#divbotonHotel").css("display", "none");
			$("#divbotonAuto").css("display","block");
		}
	}						
	//Se pone el estatus en edicion
	estatus_en_edicion_de_itinerario = true;
	//Se cambia la etiqueta del boton agregar itinerario por actualizar
	$("#registrar_comp").attr("value","     Actualizar Itinerario");
	$("#registrar_comp").removeAttr("disabled");
	activar_campos_de_itinerario();
	//Se debera cargar los datos del auto si es que hay si no hay no habra problema.
	verificar_itinerario();
}
function activar_o_desactivar_campos_de_itinerario(){
	var frm=document.detallesItinerarios;
	id =frm.itinerarioActualOPosible.value;
	if(estatus_tipo_de_viaje == false){
	   $("#registrar_comp").attr("disabled","true");
	   $("#guardarComp").removeAttr("disabled");
	   $("#guardarprevComp").removeAttr("disabled");
	}else{
		if(id <2){
		  	document.getElementById("guardarComp").disabled="disabled";
			document.getElementById("guardarprevComp").disabled="disabled";
		}else if(id >=2){
			$("#guardarComp").removeAttr("disabled");
			$("#guardarprevComp").removeAttr("disabled");
		}
	}
}
function checa_itinerarios(){
	var num_itinerario = parseInt($("#solicitud_table>tbody >tr").length);
	if(num_itinerario == 1)
		activar_campos_de_itinerario();
}
function activar_campos_de_itinerario(){
	$("#origen").removeAttr("readonly");
	$("#destino").removeAttr("readonly");
	$("#registrar_comp").removeAttr("disabled");			
}
function desactivarDatosGenerales(tipoPasaje){
	var frm=document.detallesItinerarios;
	if(tipoPasaje != 3){
	}
	$("#select_tipo_viaje_pasaje").attr("onfocus","this.blur()");
	$("#select_tipo_viaje").attr("onfocus","this.blur()");
	$("#select_region_viaje").attr("onfocus","this.blur()");
	$("#Precio_hosp_agencia_dato").attr("readonly","readonly");
	$("#divisa_hosp_agencia_dato").attr("onfocus","this.blur()");
	$("#Precio_renta_agencia_dato").attr("readonly","readonly");
	$("#divisa_renta_agencia_dato").attr("onfocus","this.blur()");
}
var globalConceptosId = 1;
// Da de alta un concepto de gastos
function construyePartidaConcepto(){
	var frm=document.detallesItinerarios;
	var conceptoId=frm.select_concepto.value;
	//conteo de la partida
	frm.rowCountConceptos.value=parseInt(frm.rowCountConceptos.value)+parseInt(1);
	// Busca el ultimo ID de las partidas y le sumamos uno para el siguiente
	id=parseInt($("#conceptos_table").find("tr:last").find("td").eq(0).html());
	if(isNaN(id)){
		id=1;
	} else {
		id+=parseInt(1);
	}
	id = globalConceptosId;
	globalConceptosId = globalConceptosId + 1;
	// Creamos la nueva fila y sus respectivas columnas
	var row=frm.rowCountConceptos.value;
	var concepto = $("#select_concepto").val();
	var tipoViaje = frm.tipoDeViaje.value;
	var idRegion = frm.idDeRegion.value;
	var tipoPasaje = frm.select_tipo_viaje_pasaje.options[frm.select_tipo_viaje_pasaje.selectedIndex].text;
	//Busca el horario de salida en la primera fila
	var firstRow=1;
	aux1 = document.getElementById("hora"+firstRow).value.split(" ");
	var horaSalida = aux1[0];
	//Busca el horario de llegada en la ultima fila
	var horaLlegada="";
	$('#solicitud_table tr').each(function() {
		var aux1 = $(this).find("td").eq(6).find('input[id=horarioLlegada1]').val();
		if (aux1!=undefined){
			aux2 = aux1.split(" ");
			horaLlegada = aux2[0];
		}
	});
	// Obtiene el no. de dias de viaje
	if(conceptoId == 6){ //comida
		DiasV = getDiasViajePorConcepto(1);
	}else if(conceptoId == 5){ //hotel
		DiasV = getDiasViajePorConcepto(2);
	}else if(conceptoId == 13){ //hotel
		DiasV = getDiasViajePorConcepto(2);
	}else if(conceptoId == 12){ //hotel
		DiasV = getDiasViajePorConcepto(2);
	}else if(conceptoId == 11){ //hotel
		DiasV = getDiasViajePorConcepto(2);
	}else{ //otro concepto
		DiasV = getDiasViajePorConcepto(0);
	}
	// Crea la nueva fila con todos los valores en blanco
	var nuevaFila='<tr>';
	nuevaFila+="<td><input type='hidden' name='idsDeFila"+id+"' id=idsDeFila"+id+" value='"+id+"' readonly='readonly'/><input type='text' name='rowCount"+id+"' id=rowCount"+id+" value='"+id+"' readonly='readonly' size='2' style='border-color:#FFFFFF; text-align:right'/><input type='hidden' name='rowConcepto"+id+"' id=rowConcepto"+id+" value='"+conceptoId+"' readonly='readonly' /></td>";
	nuevaFila+="<td>"+frm.select_concepto.options[frm.select_concepto.selectedIndex].text+"<input type='hidden' name='Concepto"+id+"' id='Concepto"+id+"' value='"+frm.select_concepto.options[frm.select_concepto.selectedIndex].text+"' readonly='readonly' /></td>";
	nuevaFila+="<td align='right'><input type='text' name='Monto"+id+"' id='Monto"+id+"' value='0' onmouseover='changedFields(this.id);' onkeypress='return validaNum (event)' style='border-color:#FFFFFF; text-align:right' onkeyup=recalculaMontos() onchange=recalculaMontos() onblur='changedFieldsOut(this.id)' onmouseout=mouseOut(this.id)  /></td>";
	nuevaFila+="<td align='right'><select name='moneda"+id+"' id='moneda"+id+"' onchange='cambiaTasa();'><option value='1' selected='selected'>MXN</option><option value='2'>USD</option><option value='3'>EUR</option></select><input type='hidden' name='divisaIdAnterior"+id+"' id='divisaIdAnterior"+id+"' value='1' readonly='readonly' style='border-color:#FFFFFF'/></td>";
	nuevaFila+="<td align='right'><input type='text' name='Days"+id+"' id='Days"+id+"' value='"+DiasV+"' onmouseover='changedFields(this.id);' onkeypress='return validaNum (event)' style='border-color:#FFFFFF; text-align:center' onkeyup='recalculaMontos()' onchange='recalculaMontos()' /></td>";
	nuevaFila+="<td align='right'><input disabled type='text' name='MontoTotal"+id+"' id='MontoTotal"+id+"' value='0' readonly='readonly' style='border-color:#FFFFFF; text-align:right'/></td>";
	nuevaFila+="<td align='right'><input type='text' name='MontoDiarioPolitica"+id+"' id='MontoDiarioPolitica"+id+"' value='0' readonly='readonly' style='border-color:#FFFFFF; text-align:right'/></td>";
	nuevaFila+="<td align='right'><input type='text' name='MontoDiarioPolitica"+id+"' id='MontoDiarioPolitica"+id+"' value='0' readonly='readonly' style='border-color:#FFFFFF; text-align:right'/></td>";
	nuevaFila+="<td align='right'><input type='text' name='MontoTotalPolitica"+id+"' id='MontoTotalPolitica"+id+"' value='0' readonly='readonly' style='border-color:#FFFFFF; text-align:right'/></td>";
	nuevaFila+="<td align='right'><input disabled type='text' name='MontoEnPesos"+id+"' id='MontoEnPesos"+id+"' value='0' readonly='readonly' style='border-color:#FFFFFF; text-align:right'/></td>";
	nuevaFila+="<td><div align='center'><img class='eliminar' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+id+"dele' id='"+id+"dele'  onmousedown='borrarPartidaConcepto(this.id);'  style='cursor:pointer' /></div></td>";
	nuevaFila+="</tr>";			
	$("#conceptos_table").append(nuevaFila);
	frm.idsDeFilasConceptos.value += id+":";
	//Calculo del monto diario para comidas segun politicas
	if(conceptoId == 6){ //comida
		if(idRegion == ""){
			re_id=2;
		}else{
			re_id=idRegion;
		}
		if(re_id != ""){
			blockUI(true);
			var parametros10 = "re_id="+re_id;
			var respuesta10 = obtenJson(parametros10);
				if(respuesta10 != false || respuesta10 != null){
						tasaDiaria = parseFloat(respuesta10[0]) * parseFloat(respuesta10[2]);
						montoDesayuno = parseFloat(respuesta10[3]) * parseFloat(respuesta10[5]); //se convierte a la tasa MXN
						montoComida = parseFloat(respuesta10[6]) * parseFloat(respuesta10[8]); //se convierte a la tasa MXN
						montoCena = parseFloat(respuesta10[9]) * parseFloat(respuesta10[11]); //se convierte a la tasa MXN
						//Se saca el monto del primer dia, siempre se hace.
						switch(horaSalida){
							case "Mañana":
								montoPrimerDia = tasaDiaria;
								break;
							case "Tarde":
								montoPrimerDia = tasaDiaria - montoDesayuno;
								break;
							case "Noche":
								montoPrimerDia = montoCena;
								break;
						}
						//Dependiendo que tipo de pasaje sea, se sacan los montos de los dias intermedios y del dia de llegada.
						if(tipoPasaje == "Sencillo"){
							montoTotal = montoPrimerDia;
						}else if((tipoPasaje == "Redondo" || tipoPasaje == "Varias escalas") && DiasV >= 2){
							//Se saca el monto del ultimo dia, siempre que sea pasaje redondo o con escalas.
							switch(horaLlegada){
								case "Mañana":
									montoUltimoDia = montoDesayuno;
									break;
								case "Tarde":
									montoUltimoDia = parseFloat(tasaDiaria) - parseFloat(montoCena);
									break;
								case "Noche":
									montoUltimoDia = tasaDiaria;
									break;
							}
							//Se saca el monto de los dias intermedios
							diasIntermedios = DiasV-2;
							montoDiasIntermedios = parseFloat(tasaDiaria) * parseFloat(diasIntermedios);
							montoTotal = parseFloat(montoPrimerDia) + parseFloat(montoDiasIntermedios) + parseFloat(montoUltimoDia);
						}else if((tipoPasaje == "Redondo" || tipoPasaje == "Varias escalas") && DiasV < 2){
							switch(horaSalida){
								case "Mañana":
									switch(horaLlegada){
										case "Mañana":
											montoTotal = montoDesayuno;
											break;
										case "Tarde":
											montoTotal = parseFloat(tasaDiaria) + parseFloat(montoCena);
											break;
										case "Noche":
											montoTotal = tasaDiaria;
											break;
									}
									break;
								case "Tarde":
									switch(horaLlegada){
										case "Mañana":
											//Error
											break;
										case "Tarde":
											montoTotal = montoComida;
											break;
										case "Noche":
											montoTotal = parseFloat(tasaDiaria) - parseFloat(montoDesayuno);
											break;
									}
									break;
								case "Noche":
									switch(horaLlegada){
										case "Mañana":
											//Error
											break;
										case "Tarde":
											//Error
											break;
										case "Noche":
											montoTotal = montoCena;
											break;
									}
									break;
							}
						}
						mtoTotalPolitica = document.getElementById("MontoTotalPolitica"+id);
						mtoTotal = document.getElementById("MontoTotal"+id);
						mtoDiarioPolitica = document.getElementById("MontoDiarioPolitica"+id);
						mtoDiario = document.getElementById("Monto"+id);
						mtoTotalPolitica.value = number_format((montoTotal),2,".",",");
						mtoTotal.value = number_format((montoTotal),2,".",",");
						mtoDiarioPolitica.value = number_format((tasaDiaria),2,".",",");
						mtoDiario.value = number_format((Math.round((parseFloat(montoTotal)/parseFloat(DiasV)) * Math.pow(10, 2)) / Math.pow(10, 2)),2,".",",");//redondea a 2 decimales
						recalculaMontos();
						blockUI(false);
				}
		}
	}else{ //conceptos diferentes a comida
		if(idRegion == ""){
			re_id=2;
		}else{
			re_id=idRegion;
		}
		if(re_id != ""){
			blockUI(true);
			var parametros11 = "dc_id="+conceptoId+"&region_id="+re_id;
			var respuesta11 = obtenJson(parametros11);
				if(respuesta11 != false || respuesta11 != null){
					mtoTotalPolitica = document.getElementById("MontoTotalPolitica"+id);
					mtoDiarioPolitica = document.getElementById("MontoDiarioPolitica"+id);
					var montoDiarioPoliticaEnMXN = parseFloat(respuesta11[0]) * parseFloat(respuesta11[2]);
					mtoDiarioPolitica.value = number_format((montoDiarioPoliticaEnMXN),2,".",",");
					if(conceptoId == 5){//hotel
						mtoTotalPolitica.value = number_format((montoDiarioPoliticaEnMXN * parseFloat(DiasV)),2,".",",");
					}else{//otro concepto
						mtoTotalPolitica.value = number_format((montoDiarioPoliticaEnMXN),2,".",",");
					}
					recalculaMontos();
				}
				blockUI(false);
		}
	}
	// FIN Calculo del monto diario para comidas segun politicas
	// Calcula los montos
	recalculaMontos();
}
// Elimina un concepto de gastos
function borrarPartidaConcepto(id){
	var firstRow=parseInt($("#conceptos_table").find("tr:first").find("td").eq(0).html());
	var lastRow=parseInt($("#conceptos_table").find("tr:last").find("td").eq(0).html());
	var frm=document.detallesItinerarios;
	$("img.eliminar").click(function(){
			$(this).parent().parent().parent().fadeOut("normal", function () {
				$(this).remove();
				frm.rowCountConceptos.value=parseInt(frm.rowCountConceptos.value)-parseInt(1);
				var i=0;
				for (i=parseInt(id);i<=parseInt(frm.rowCountConceptos.value);i++){
					var nextidsDeFila="idsDeFila"+((parseInt(i)+(1)));
					var idsDeFila="idsDeFila"+parseInt(i);
					var jqueryidsDeFila="#idsDeFila"+parseInt(i);
					var nextrowCount="rowCount"+((parseInt(i)+(1)));
					var rowCount="rowCount"+parseInt(i);
					var jqueryrowCount="#rowCount"+parseInt(i);
					var nextrowConcepto="rowConcepto"+((parseInt(i)+(1)));
					var rowConcepto="rowConcepto"+parseInt(i);
					var jqueryrowConcepto="#rowConcepto"+parseInt(i);
					var nextConcepto="Concepto"+((parseInt(i)+(1)));
					var Concepto="Concepto"+parseInt(i);
					var jqueryConcepto="#Concepto"+parseInt(i);
					var nextMonto="Monto"+((parseInt(i)+(1)));
					var Monto="Monto"+parseInt(i);
					var jqueryMonto="#Monto"+parseInt(i);
					var nextmoneda="moneda"+((parseInt(i)+(1)));
					var moneda="moneda"+parseInt(i);
					var jquerymoneda="#moneda"+parseInt(i);
					var nextdivisaIdAnterior="divisaIdAnterior"+((parseInt(i)+(1)));
					var divisaIdAnterior="divisaIdAnterior"+parseInt(i);
					var jquerydivisaIdAnterior="#divisaIdAnterior"+parseInt(i);
					var nextDays="Days"+((parseInt(i)+(1)));
					var Days="Days"+parseInt(i);
					var jqueryDays="#Days"+parseInt(i);
					var nextMontoTotal="MontoTotal"+((parseInt(i)+(1)));
					var MontoTotal="MontoTotal"+parseInt(i);
					var jqueryMontoTotal="#MontoTotal"+parseInt(i);
					var nextMontoDiarioPolitica="MontoDiarioPolitica"+((parseInt(i)+(1)));
					var MontoDiarioPolitica="MontoDiarioPolitica"+parseInt(i);
					var jqueryMontoDiarioPolitica="#MontoDiarioPolitica"+parseInt(i);
					var nextMontoTotalPolitica="MontoTotalPolitica"+((parseInt(i)+(1)));
					var MontoTotalPolitica="MontoTotalPolitica"+parseInt(i);
					var jqueryMontoTotalPolitica="#MontoTotalPolitica"+parseInt(i);
					var nextMontoEnPesos="MontoEnPesos"+((parseInt(i)+(1)));
					var MontoEnPesos="MontoEnPesos"+parseInt(i);
					var jqueryMontoEnPesos="#MontoEnPesos"+parseInt(i);
					var nextdele=((parseInt(i)+(1)))+"dele";
					var del=parseInt(i)+"dele";
					var jquerydel="#"+parseInt(i)+"dele";
					document.getElementById(nextidsDeFila).setAttribute("id",idsDeFila);
					 $(jqueryidsDeFila).attr("name",idsDeFila);
					 $(jqueryidsDeFila).val(parseInt(i));
					 document.getElementById(nextrowCount).setAttribute("id",rowCount);
					 $(jqueryrowCount).attr("name",rowCount);
					 $(jqueryrowCount).val(parseInt(i));
					 document.getElementById(nextrowConcepto).setAttribute("id",rowConcepto);
					 $(jqueryrowConcepto).attr("name",rowConcepto);
					 document.getElementById(nextMonto).setAttribute("id",Monto);
					 $(jqueryMonto).attr("name",Monto);
					 document.getElementById(nextmoneda).setAttribute("id",moneda);
					 $(jquerymoneda).attr("name",moneda);
					 document.getElementById(nextdivisaIdAnterior).setAttribute("id",divisaIdAnterior);
					 $(jquerydivisaIdAnterior).attr("name",divisaIdAnterior);
					 document.getElementById(nextDays).setAttribute("id",Days);
					 $(jqueryDays).attr("name",Days);
					 document.getElementById(nextMontoTotal).setAttribute("id",MontoTotal);
					 $(jqueryMontoTotal).attr("name",MontoTotal);
					 document.getElementById(nextMontoDiarioPolitica).setAttribute("id",MontoDiarioPolitica);
					 $(jqueryMontoDiarioPolitica).attr("name",MontoDiarioPolitica);
					 document.getElementById(nextMontoTotalPolitica).setAttribute("id",MontoTotalPolitica);
					 $(jqueryMontoTotalPolitica).attr("name",MontoTotalPolitica);
					 document.getElementById(nextMontoEnPesos).setAttribute("id",MontoEnPesos);
					 $(jqueryMontoEnPesos).attr("name",MontoEnPesos);
					 document.getElementById(nextdele).setAttribute("id",del);
					 $(jquerydel).attr("name",del);
					}
				// Calcula los montos
				recalculaMontos();
				//Se restablece numeracion
			});
			return false;
	});
}
function restableNumerosDeFilas(table){
	var count = 1;
	var id = 0;
	$("#"+table+" tr").each(function() {
		var concepto = $(this).find("td").eq(0).find('input').val();
		id = $(this).find("td").eq(0).find('input').val();
		
		if(concepto!=undefined){
			$(this).find("td").eq(0).find('input[id=rowCount'+id+']').val(count);
			count++;
		}
	});
}

// Regresa el total de dia de viajes de todos los itinerarios
function getDiasViaje(){
	var diasTotal = 0;
	$('#solicitud_table tr').each(function() {
		var dias = $(this).find("td").eq(8).find('input').val();
		if (dias!=undefined){
			diasTotal = diasTotal + parseInt(dias);
		}
	});
	return diasTotal;
}

// Regresa el total de noches de hospedaje de todos los itinerarios
function getNochesHospedaje(){
	var nochesTotal = 0;
	$('#solicitud_table tr').each(function() {
		var noches = $(this).find("td").eq(10).find('input').val();
		if (noches!=undefined){
			nochesTotal = nochesTotal + parseInt(noches);
		}
	});
	return nochesTotal;
}
//
//  Esta function calcula el no. de dias de viaje asociados con el concepto. Aqui
// tenemos 3 casos:
//
//      conceptoRecurrente = 0: Significa que no es un concepto recurrente, el no. de dias que se regresa es ""
//      conceptoRecurrente = 1: Significa que es un concepto que se multiplica por el no. de dias (por ejemplo alimentos)
//      conceptoRecurrente = 2: Significa que es un concepto que se multiplica por el no. de noches (por ejemplo hospedaje)
//
function getDiasViajePorConcepto(conceptoRecurrente){
	var diasTotal = getDiasViaje();
	var nochesTotal = getNochesHospedaje();
	if (conceptoRecurrente==0){
		return "";
	} else if (conceptoRecurrente==1){
		return diasTotal;
	} else {
		return nochesTotal;
	}
}
function recalculaMontos(){
	var totalAnticipo = 0;
	var conceptoExcedePoliticas = false;
	$("#capaWarning").html("");
	var mensajeExcedePoliticas = undefined;
	// Itera la tabla de conceptos para calcular el total del anticipo y el monto por concepto
	var id2=0;
	$('#conceptos_table tr').each(function() {
		var concepto = $(this).find("td").eq(0).find('input').val();
		alert(concepto);
		var conceptoNombre = $(this).find("td").eq(1).find('input').val();
		var noDias = $(this).find("td").eq(4).find('input').val();//dias
		var divisaID = $(this).find("td").eq(3).find('select').val();
		if(concepto!=undefined){
			id2 = $(this).find("td").eq(0).find('input').val();
			var tasaNueva = 1;
			if(divisaID != 1){ //Si la divisa es diferente a MXN
				//Se obtiene las tasas de las divisas
				var tasa = tasaDivisa;
				var tasa2 = tasa.split(":");
				alert(tasaDivisa);
				//Se obtiene la tasa de la divisa seleccionada
				for(i=0;i<=tasa2.length;i=i+2){
					if(tasa2[i] == divisaID){
						tasaNueva = tasa2[i+1];
					}
				}
			}
			var arrDatos = concepto.split("&");
			var conceptoId = arrDatos[0];
			var conceptoMontoPorPoliticas = arrDatos[1];
			var conceptoRecurrente = arrDatos[2];
			var montoTotal = 0;
			var montoDiario = 0;
			// Checa que 
			var monto = $(this).find("td").eq(2).find('input').val();
			var conceptoMontoDiario = parseFloat(monto.replace(',', ''));
			var mtoTotal = document.getElementById("MontoTotal"+id2);
			if(isNaN(conceptoMontoDiario)){
				conceptoMontoDiario = "0";
			}
			var monto2 = $(this).find("td").eq(5).find('input').val();
			var conceptoMontoTotal = parseFloat(monto2.replace(',', ''));
			var mtoDiario = document.getElementById("Monto"+id2);
			if(isNaN(conceptoMontoTotal)){
				conceptoMontoTotal = "0";
			}
			var montoDiarioPolitica = $(this).find("td").eq(6).find('input').val().replace(',', '');
			var montoTotalPolitica = $(this).find("td").eq(7).find('input').val().replace(',', '');
			if (((parseFloat(conceptoMontoTotal) > montoTotalPolitica) && (montoTotalPolitica > 0)) || ((parseFloat(conceptoMontoDiario) > montoDiarioPolitica) && (montoDiarioPolitica > 0))) {
				if(mensajeExcedePoliticas==undefined){
					mensajeExcedePoliticas = "<strong><font color='red'>Error:</font> Esta rebasando la pol&iacute;tica del concepto "+conceptoNombre+"<br>El monto m&aacute;ximo es de "+montoDiarioPolitica+".</strong>";
				}else{
					mensajeExcedePoliticas = mensajeExcedePoliticas + "<br><strong><font color='red'>Error:</font> Esta rebasando la pol&iacute;tica del concepto "+conceptoNombre+"<br>El monto m&aacute;ximo es de "+montoDiarioPolitica+".</strong>";
				}
				conceptoExcedePoliticas = true;
				mtoTotal.value = montoTotal = 0;
				mtoDiario.value = montoDiario = 0;
			}else{
				montoTotal = 0;
				montoDiario = 0;
					if(noDias == ""){
						montoTotal = parseFloat(conceptoMontoDiario);
						mtoTotal.value = number_format((montoTotal),2,".",",");
					}else{
						montoTotal = parseFloat(conceptoMontoDiario)* parseFloat(noDias);
						mtoTotal.value = number_format((montoTotal),2,".",",");
					}
			}
			totalAnticipo = parseFloat(totalAnticipo) + parseFloat(montoTotal) * parseFloat(tasaNueva);
			// Se realiza la conversion en MXN
			var mtoEnPesos = document.getElementById("MontoEnPesos"+id2);
			mtoEnPesos.value = number_format((parseFloat(montoTotal)*parseFloat(tasaNueva)),2,".",",");
		}
	});	
	if(conceptoExcedePoliticas){
		$("#capaWarning").html(mensajeExcedePoliticas);
	}			
	var frm=document.detallesItinerarios;
	frm.anticipoC.value = number_format(redondea(totalAnticipo),2,".",",");//redondea a 2 decimales
	$("#totalanticipo").val(totalAnticipo);
}
function redondea(valor){
	return (Math.round(valor * Math.pow(10, 2)) / Math.pow(10, 2));
}
// Esta funcion se llama para activar o desactivar el boton de guardar solicitud
//
//      La solicitud solo se permite guardar si:
//          a) El anticipo es mayor a 0
//          b) Ningun concepto excede las politicas
function activeSaveViaje(totalAnticipo, conceptoExcedePoliticas){
	if (totalAnticipo<=0 || conceptoExcedePoliticas){
		// Deshabilita el boton de guarda
		$("#guardarComp").attr("disabled", "disable");
		$("#guardarprevComp").attr("disabled", "disable");
	} else {
		// Habilita el boton de salvar
		$("#guardarComp").removeAttr("disabled");
		$("#guardarprevComp").removeAttr("disabled");
	}
}
//Cambia los montos en base a la divisa seleccionada
function cambiaTasa(){
	// Itera la tabla de conceptos para calcular cambios en la divisa
	var id3 = 0;
	$('#conceptos_table tr').each(function() {
		var divisaID = $(this).find("td").eq(3).find('select').val();
		var divisaIdAnterior = $(this).find("td").eq(3).find('input').val();
		if(divisaID != undefined && divisaID != divisaIdAnterior){
			id3 = $(this).find("td").eq(0).find('input').val();
			divIdAnterior = document.getElementById("divisaIdAnterior"+id3);
			divIdAnterior.value = divisaID;
			//Se obtiene las tasas de las divisas
			var tasa = tasaDivisa;
			var tasa2 = tasa.split(":");
			//Se obtiene la tasa de la divisa seleccionada
			for(i=0;i<=tasa2.length;i=i+2){
				if(tasa2[i] == divisaID){
					tasaNueva = tasa2[i+1];
				}
			}
			//Se obtiene la tasa de la divisa anterior
			for(i=0;i<=tasa2.length;i=i+2){
				if(tasa2[i] == divisaIdAnterior){
					tasaAnterior = tasa2[i+1];
				}
			}
			var montoDiario = $(this).find("td").eq(2).find('input').val().replace(',', '');
			var montoTotal = $(this).find("td").eq(5).find('input').val().replace(',', '');
			var montoDiarioPolitica = $(this).find("td").eq(6).find('input').val().replace(',', '');
			var montoTotalPolitica = $(this).find("td").eq(7).find('input').val().replace(',', '');
			//Se convierten los montos a pesos segun la tasa anterior en la que hayan estado los montos
			mtoTotal = document.getElementById("MontoTotal"+id3);
			mtoTotal.value = Math.round( (parseFloat(montoTotal)*parseFloat(tasaAnterior)) * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			mtoDiarioPolitica = document.getElementById("MontoDiarioPolitica"+id3);
			mtoDiarioPolitica.value = Math.round( (parseFloat(montoDiarioPolitica)*parseFloat(tasaAnterior)) * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			mtoTotalPolitica = document.getElementById("MontoTotalPolitica"+id3);
			mtoTotalPolitica.value = Math.round( (parseFloat(montoTotalPolitica)*parseFloat(tasaAnterior)) * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			montoDiario = $(this).find("td").eq(2).find('input').val().replace(',', '');
			montoTotal = $(this).find("td").eq(5).find('input').val().replace(',', '');
			montoDiarioPolitica = $(this).find("td").eq(6).find('input').val().replace(',', '');
			montoTotalPolitica = $(this).find("td").eq(7).find('input').val().replace(',', '');
			//Se convierten los montos a la tasa nueva
			mtoTotal = document.getElementById("MontoTotal"+id3);
			mtoTotal.value = number_format((Math.round( (parseFloat(montoTotal)/parseFloat(tasaNueva)) * Math.pow(10, 2)) / Math.pow(10, 2)),2,".",",");//redondea a 2 decimales
			mtoDiarioPolitica = document.getElementById("MontoDiarioPolitica"+id3);
			mtoDiarioPolitica.value = number_format((Math.round( (parseFloat(montoDiarioPolitica)/parseFloat(tasaNueva)) * Math.pow(10, 2)) / Math.pow(10, 2)),2,".",",");//redondea a 2 decimales
			mtoTotalPolitica = document.getElementById("MontoTotalPolitica"+id3);
			mtoTotalPolitica.value = number_format((Math.round( (parseFloat(montoTotalPolitica)/parseFloat(tasaNueva)) * Math.pow(10, 2)) / Math.pow(10, 2)),2,".",",");//redondea a 2 decimales
		}
	});
	recalculaMontos();
}
function ValidaFechaMultidestino(tipoViaje){
	if(tipoViaje == 3){
		var longitud = parseInt($("#solicitud_table>tbody >tr").length);
		if(longitud >= 1){
			for(var i=1;i<=longitud;i++){
				if(!compare_dates($("#fecha").val(),$("#salida"+i).val())){
					alert("La Fecha del Itinerario que ingresará es menor  que el/los Itinerario(s) ingresados anteriormente.");
					return false;
				}
			}
		}
	}
	return true;
}
function compare_dates(fecha,fecha2) {
	if(fecha2==1){
		var now=new Date();
		var yMonth=now.getMonth() + 1;
		var yDay=now.getDate();
		var yYear=now.getFullYear();
	}
	else{
		var yMonth=fecha2.substring(3, 5);
		var yDay=fecha2.substring(0, 2);
		var yYear=fecha2.substring(6,10);
	}
	var xMonth=fecha.substring(3, 5);
	var xDay=fecha.substring(0, 2);
	var xYear=fecha.substring(6,10);
	if (xYear> yYear)
	{
		return(true);
	}
	else
	{
		if (xYear == yYear)
		{
			if (xMonth> yMonth)
			{
				return(true);
			}
			else
			{
				if (xMonth == yMonth)
				{
					if (xDay>= yDay)
						return(true);
					else
						return(false);
				}
				else
					return(false);
			}
		}
		else
			return(false);
	}
}
function radioH(value){
	var frm=document.detallesItinerarios;
	if(value==1){
		$("#enviar_agencia_hotel").css("display", "block");
		$("#enviar_agencia_hotel_input").css("display", "block");
		frm.enviar_hosp_agencia[0].checked = true;
	} else{
		$("#enviar_agencia_hotel").css("display", "none");
		$("#enviar_agencia_hotel_input").css("display", "none");
	}
}
function radioA(value){
	var frm=document.detallesItinerarios;
	if(value==1){
		$("#enviar_agencia_auto").css("display", "block");
		$("#enviar_agencia_auto_input").css("display", "block");
		frm.enviar_auto_agencia[0].checked = true;
	} else{
		$("#enviar_agencia_auto").css("display", "none");
		$("#enviar_agencia_auto_input").css("display", "none");
	}
}
function verificar_region(valor, itemParaSeleccionar){
	var frm=document.detallesItinerarios;
	var opciones = "";
	// Eliminar las opciones del combo de conceptos
	$("#select_region_viaje").empty();
	if(valor != "" && valor != 1 && valor != -1){
		var parametros12 = "no_region="+valor;
		var respuesta12 = obtenJson(parametros12);
			if(respuesta12 != false){
				opciones = respuesta12;
				if(opciones.length = 0){
					$("#select_region_viaje").append(new Option("Sin Datos"));
				}else{
					$("#region_solicitud_viaje").css("display", "block");
					$("#select_region_viaje").css("display", "block");
					$("#select_region_viaje").html(opciones);
					$("#select_region_viaje").val(itemParaSeleccionar);
				}
			}else{
				verificar_region(valor, itemParaSeleccionar);
			}
	}else if(valor == "-1" || valor == "1"){
		$("#region_solicitud_viaje").css("display", "none");
		$("#select_region_viaje").css("display", "none");
	}
}
function LlenarCombo(json, combo){
	combo.options[0] = new Option('Seleccione...', '');
	for(var i=0; i <= json.length; i++){
		var str=json[i];
		var str1=str.slice(str.search(":")+1);
		var str2=str.substr(0,str.search(":"));
		combo.options[combo.length] = new Option(str1,str2);
	}
}
function LimpiarCombo(combo){
	while(combo.length > 0){
		combo.remove(combo.length-1);
	}
}
function obtenerValorSelect(valor){
	construirTabla(valor);
	if( valor == "1" || valor == "Sencillo"){
		$("#select_tipo_viaje_pasaje_val").val(1);
	}else if(valor == "2" || valor == "Redondo"){
		$("#select_tipo_viaje_pasaje_val").val(2);
	}else if(valor == "3" || valor == "Multidestinos"){
		$("#select_tipo_viaje_pasaje_val").val(3);
	}
}
function verificar_tipo_boleto(tipo_boleto){
	obtenerValorSelect(tipo_boleto);
	if(tipo_boleto=="1" || tipo_boleto=="3"){
		$("#llegada_viaje_dato").css("visibility", "hidden");
		$("#llegada_viaje_dato2").css("visibility", "hidden");
		$("#regreso_titulo").css("visibility", "hidden");
		estatus_tipo_de_viaje = (tipo_boleto=="1") ? false : true;
	}else if(tipo_boleto=="2"){
		$("#llegada_viaje_dato").css("visibility", "visible");
		$("#llegada_viaje_dato2").css("visibility", "visible");
		$("#regreso_titulo").css("visibility", "visible");
		estatus_tipo_de_viaje = false;
	}
}
function cargar_tipo_transporte(tipo_transporte){
	var frm=document.detallesItinerarios;
	if(parseInt(tipo_transporte)==1 || parseInt(tipo_transporte)==2){
		$('#kilometraje_data').css("display","block");
		$("#kilometraje_label").css("display","block");
	}else if(parseInt(tipo_transporte)==3 || parseInt(tipo_transporte)==-1){
		if(estatus_en_edicion_de_itinerario){
			var no_de_itinerario = frm.itinerarioActualOPosible.value;
			$("#kilometraje"+no_de_itinerario).val('');
		}
		$("#kilometraje").val('');
		$('#kilometraje_data').css("display","none");
		$("#kilometraje_label").css("display","none");
	}
}
function xxxx(){
	var frm=document.detallesItinerarios;
	var num_posible_itinerario = frm.itinerarioActualOPosible.value;
	var idTable = "hotel_table"+num_posible_itinerario+"";
	var classTable = "tablesorter";
	var styleTable = "cellspacing='1'";
	var th = ["No.", "Ciudad", "Hotel", "Comentarios", "Noches", "Llegada", "Salida", "No. de reservación", "Costo por noche", "IVA", "Total", "Divisa", "Monto en MXN", "Eliminar"];
	var thStyle = ["width='5%'", "width='30%'", "width='30%'", "width='30%'", "width='30%'", "width='30%'", "width='30%'", "width='5%'", "width='20%'", "width='10%'", "width='10%'", "width='10%'", "width='10%'", "width='5%'"];
    var cadena = createTableJS(idTable,classTable,styleTable,th,thStyle);
	var num_childNodes_default = document.getElementById('tipo_explorador').childNodes.length;
	if(num_posible_itinerario>document.getElementById('area_tabla_div').childNodes.length){
		//Se chaca si ya existe un div para contener la tabla de hotel del itinerario actual o posible
		var nuevo_div = document.createElement("div");
		document.getElementById('area_tabla_div').appendChild(nuevo_div);
		//Se chaca si ya existe un div para contener rowCount_hotel y rowDel_hotel correspondientes del itinerario actual o posible
		var nuevo_div2 = document.createElement("div");
		document.getElementById('area_counts_div').appendChild(nuevo_div2);
	}
	document.getElementById('area_tabla_div').childNodes[parseInt(num_posible_itinerario-1+num_childNodes_default)].innerHTML = cadena;
	//Se agregan rowCount_hotel y rowDel_hotel correspondientes al numero de itinerario.
	var cadena1 = "";
	cadena1 += "<input type='hidden' id='rowCount_hotel"+num_posible_itinerario+"' name='rowCount_hotel"+num_posible_itinerario+"' value='0' readonly='readonly'/>";
	cadena1 += "<input type='hidden' id='rowDel_hotel"+num_posible_itinerario+"' name='rowDel_hotel"+num_posible_itinerario+"' value='0' readonly='readonly'/>";
	document.getElementById('area_counts_div').childNodes[parseInt(num_posible_itinerario-1+num_childNodes_default)].innerHTML = cadena1;
}
function flotanteActive(valor){
	var frm=document.detallesItinerarios;
	$("#warning_msg").css("display","none");
	$("#agregar_Hotel").removeAttr("disabled");
	var count_itinerarios = parseInt($("#solicitud_table>tbody>tr").length);
	var num_posible_itinerario = frm.itinerarioActualOPosible.value;
	$("#no_itinerario").html(num_posible_itinerario);
	$("#origen_label").html($("#origen").val());
	$("#destino_label").html($("#destino").val());
	var no_de_itinerario = frm.itinerarioActualOPosible.value;
	var no_de_hoteles = parseInt($("#hotel_table"+no_de_itinerario+">tbody >tr").length);
	if(no_de_hoteles == 0){
		// Desactivar botones
		$("#aceptarHotel").attr("disabled", "disabled");
		$("#cancelarHotel").attr("disabled", "disabled");
	}else{
		$("#enviar_hosp_agencia input[@type=radio]:eq(1)").attr("checked", true);
	}
    if (valor == 1){
		if (bandera_hotel==1){
			agregarTablaHotelN(num_posible_itinerario);
		}else{
		}
		//Hace visible la ventanita emergente.
		document.getElementById('ventanita').style.visibility = 'visible';
		document.getElementById('ventanita').style.display = 'block';
		//Dejamos activa solo la tabla actual o posible
		for(var i=0;i<count_itinerarios;i++){ //Recorre las tablas
			if(document.getElementById("hotel_table"+(i+1))){
				if(num_posible_itinerario != (i+1)){
					document.getElementById("hotel_table"+(i+1)).style.display = 'none';
				}else{
					document.getElementById("hotel_table"+(i+1)).style.display = 'block';
				}
			}
		}
	}else{
		document.getElementById('ventanita').style.visibility = 'hidden';
		document.getElementById('ventanita').style.display = 'block';
    }
}
function agregarTablaHotelN(num_posible_itinerario){
	
	var idTable = "hotel_table"+num_posible_itinerario+"";
	var classTable = "tablesorter";
	var styleTable = "cellspacing='1'";
	var th = ["No.", "Ciudad", "Hotel", "Comentarios", "Tipo", "Noches", "Llegada", "Salida", "No. de reservación", "Costo por noche", "IVA", "Total", "Divisa", "Monto en MXN", "Eliminar"];
	var thStyle = ["width='5%'", "width='30%'", "width='30%'", "width='30%'", "width='30%'", "width='30%'", "width='30%'", "width='30%'", "width='5%'", "width='20%'", "width='10%'", "width='10%'", "width='10%'", "width='10%'", "width='5%'"];
    var cadena = createTableJS(idTable,classTable,styleTable,th,thStyle);
	var num_childNodes_default = document.getElementById('tipo_explorador').childNodes.length;
	if(num_posible_itinerario>document.getElementById('area_tabla_div').childNodes.length){
		//Se chaca si ya existe un div para contener la tabla de hotel del itinerario actual o posible
		var nuevo_div = document.createElement("div");
		document.getElementById('area_tabla_div').appendChild(nuevo_div);
		//Se chaca si ya existe un div para contener rowCount_hotel y rowDel_hotel correspondientes del itinerario actual o posible
		var nuevo_div2 = document.createElement("div");
		document.getElementById('area_counts_div').appendChild(nuevo_div2);
	}
	document.getElementById('area_tabla_div').childNodes[parseInt(num_posible_itinerario-1+num_childNodes_default)].innerHTML = cadena;
	//Se agregan rowCount_hotel y rowDel_hotel correspondientes al numero de itinerario.
	var cadena1 = "";
	cadena1 += "<input type='hidden' id='rowCount_hotel"+num_posible_itinerario+"' name='rowCount_hotel"+num_posible_itinerario+"' value='0' readonly='readonly'/>";
	cadena1 += "<input type='hidden' id='rowDel_hotel"+num_posible_itinerario+"' name='rowDel_hotel"+num_posible_itinerario+"' value='0' readonly='readonly'/>";
	document.getElementById('area_counts_div').childNodes[parseInt(num_posible_itinerario-1+num_childNodes_default)].innerHTML = cadena1;
}
function flotanteActive2(valor){
	if(valor == 1){
		document.getElementById('ventanita_auto').style.visibility = 'visible';
		document.getElementById('ventanita_auto').style.display = 'block';
    }else{
	    document.getElementById('ventanita_auto').style.visibility = 'hidden';
	    document.getElementById('ventanita_auto').style.display = 'none';
	}
}
function limpiaFechaL(){
	verificarFechas();
	calculoNoches();
	if($("#llegada").val() == ""){
		$("#noches").val("");
	}
}
function verificarFechas(){
	var fechaS=$("#salida").val();
	var fechaL=$("#llegada").val();
	if(fechaL == "" ){
		alert("Seleccione una fecha de llegada.");
		$("#noches").val("");
		$("#salida").val("");
	}else if(fechaS == ""){
		$("#noches").val("");
	}else if(compare_dates(fechaL,fechaS)){//true si mayor
		alert("La fecha de salida es menor ó igual a la fecha de llegada. Favor de llenar los datos correctamente.");
		$("#salida").val("");
		$("#llegada").val("");
		$("#noches").val("");
	}else{ //realizara el calculo de noches
		calculoNoches();
	}
}
function calculoNoches(){
	var diferencia=0;
	var llegada= $("#llegada").val();
	var salida= $("#salida").val();
	if($("#salida").val() == ""){
		$("#noches").val("");
	}else{
		var dias = days_between(llegada, salida);
		$("#noches").val(dias);
	}
}
function verificar(){
        if($("#ciudad").val()==""){
            alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
            $("#ciudad").focus();
            return false;
        } else if($("#noches").val()==""){
            alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
            $("#noches").focus();
            return false;
        } else if($("#hotel").val()==""){
            alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
            $("#hotel").focus();
            return false;
        }else if($("#costoNoche").val()=="" || $("#costoNoche").val()=="0.00"){
        	if($("#costoNoche").val()=="0.00"){
				alert('El campo "Costo por Noche" no puede tener un valor 0.00, ingrese un valor correcto.' );
			}else{
				alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
			}
			$("#costoNoche").focus();
            return false;
        }else if($("#selecttipodivisa").val()==""){
            alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
            $("#selecttipodivisa").focus();
            return false;
        }else if($("#tipoHotel option:selected").val() == -1){
            alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
            $("#tipoHotel").focus();
            return false;
        }else if($("#llegada").val()==""){
            alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
            $("#llegada").focus();
            return false;
        }else if($("#subtotal").val()=="" || $("#subtotal").val()=="0.00"){
            alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
            $("#subtotal").focus();
            return false;
        }else if($("#salida").val()==""){
            alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
            $("#salida").focus();
            return false;
        }else if($("#iva").val()==""){
            alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
            $("#iva").focus();
            return false;
        }else if($("#comentario").val() == "" && $("#excedeMontoHospedaje").val() == 1){
            alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
            $("#comentario").focus();
            return false;
        }else{
        	$("#warning_msg").html("");
			$("#warning_msg").css("display", "none");
            return true;
        }
}
function verificar_Auto(){
        if($("#empresaAuto").val()== ""){
            alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
            $("#empresaAuto").focus();
            return false;
        } else if($("#tipoAuto").val()==""){
            alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
            $("#tipoAuto").focus();
            return false;
        } else if($("#costoDia").val()==""){
            alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
            $("#costoDia").focus();
            return false;
        }else if($("#tipoDivisa").val()==""){
            alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
            $("#tipoDivisa").focus();
            return false;
        }else if($("#diasRenta").val()==""){
            alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
            $("#diasRenta").focus();
            return false;
        }else{
            return true;
        }
    }
function agregarHotel(){
		banderaCancelaHotel = 1;
        var frm=document.detallesItinerarios;
		var no_de_itinerario = $("#no_itinerario").html();
		//Busca el ultimo ID de las partidas y le sumamos uno para el siguiente
		var id = parseInt($("#hotel_table"+no_de_itinerario+">tbody >tr").length);
        if(verificar()){
            if(isNaN(id)){
                id=1;
            }else{
                id+=parseInt(1);
            }
			$("#rowCount_hotel"+no_de_itinerario).val(parseInt($("#rowCount_hotel"+no_de_itinerario).val())+parseInt(1));
            var nuevaFila='<tr>';
            nuevaFila+="<td>"+"<div id='no_"+no_de_itinerario+"_"+id+"' name='no_"+no_de_itinerario+"_"+id+"'>"+id+"</div>"+"<input type='hidden' name='row_"+no_de_itinerario+"_"+id+"' id='row_"+no_de_itinerario+"_"+id+"' value='"+id+"' readonly='readonly' /></td>";
            nuevaFila+="<td><input type='hidden' name='ciudad_"+no_de_itinerario+"_"+id+"' id='ciudad_"+no_de_itinerario+"_"+id+"' value='"+frm.ciudad.value+"' readonly='readonly' />"+frm.ciudad.value+"</td>";
            nuevaFila+="<td><input type='hidden' name='hotel_"+no_de_itinerario+"_"+id+"' id='hotel_"+no_de_itinerario+"_"+id+"' value='"+frm.hotel.value+"' readonly='readonly' />"+frm.hotel.value+"</td>";
            nuevaFila+="<td><input type='hidden' name='comentario_"+no_de_itinerario+"_"+id+"' id='comentario_"+no_de_itinerario+"_"+id+"' value='"+frm.comentario.value+"' readonly='readonly' />"+frm.comentario.value+"</td>";
            nuevaFila+="<td><input type='hidden' name='tipoHotel_"+no_de_itinerario+"_"+id+"' id='tipoHotel_"+no_de_itinerario+"_"+id+"' value='"+$("#tipoHotel").val()+"' readonly='readonly' />"+$("#tipoHotel option:selected").text()+"</td>";
            nuevaFila+="<td><input type='hidden' name='noches_"+no_de_itinerario+"_"+id+"' id='noches_"+no_de_itinerario+"_"+id+"' value='"+frm.noches.value+"' readonly='readonly' />"+frm.noches.value+"</td>";
			nuevaFila+="<td><input type='hidden' name='llegada_"+no_de_itinerario+"_"+id+"' id='llegada_"+no_de_itinerario+"_"+id+"' value='"+$("#llegada").val()+"' readonly='readonly' />"+$("#llegada").val()+"</td>";
			nuevaFila+="<td><input type='hidden' name='salida_"+no_de_itinerario+"_"+id+"' id='salida_"+no_de_itinerario+"_"+id+"' value='"+$("#salida").val()+"' readonly='readonly' />"+$("#salida").val()+"</td>";
			nuevaFila+="<td><input type='hidden' name='noreservacion_"+no_de_itinerario+"_"+id+"' id='noreservacion_"+no_de_itinerario+"_"+id+"' value='"+frm.noreservacion.value+"' readonly='readonly' />"+frm.noreservacion.value+"</td>";
			nuevaFila+="<td><input type='hidden' name='costoNoche_"+no_de_itinerario+"_"+id+"' id='costoNoche_"+no_de_itinerario+"_"+id+"' value='"+frm.costoNoche.value.replace(/,/g,"")+"' readonly='readonly' />"+number_format(frm.costoNoche.value.replace(/,/g,""),2,".",",")+"</td>";
			nuevaFila+="<td><input type='hidden' name='iva_"+no_de_itinerario+"_"+id+"' id='iva_"+no_de_itinerario+"_"+id+"' value='"+frm.iva.value.replace(/,/g,"")+"' readonly='readonly' />"+number_format(frm.iva.value,2,".",",")+"</td>";
			nuevaFila+="<td><input type='hidden' name='total_"+no_de_itinerario+"_"+id+"' id='total_"+no_de_itinerario+"_"+id+"' value='"+frm.total.value.replace(/,/g,"")+"' readonly='readonly' />"+frm.total.value+"</td>";
			nuevaFila+="<td><input type='hidden' name='selecttipodivisa_"+no_de_itinerario+"_"+id+"' id='selecttipodivisa_"+no_de_itinerario+"_"+id+"' value='"+$("#selecttipodivisa").val()+"' readonly='readonly' />"+$("#selecttipodivisa option:selected").text()+"</td>";
			nuevaFila+="<td><input type='hidden' name='montoP_"+no_de_itinerario+"_"+id+"' id='montoP_"+no_de_itinerario+"_"+id+"' value='"+frm.montoP.value.replace(/,/g,"")+"' readonly='readonly' />"+frm.montoP.value+"</td>";
            nuevaFila+="<td><div id='delDiv_"+no_de_itinerario+"_"+id+"' align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+id+"delHotel' id='"+id+"delHotel' onmousedown='borrarHotel(this.id,"+no_de_itinerario+",0);' style='cursor:pointer;' /></div><div align='center'>Eliminar Partida</div></td>";
			nuevaFila+= '</tr>';
			$("#hotel_table"+no_de_itinerario).append(nuevaFila);
			$("#no_itinerario").val(parseInt(frm.rowCount.value));
			limpiarCamposDeHotel();
			calculaTotalHospedaje();
			document.getElementById('aceptarHotel').disabled = false;
            document.getElementById('cancelarHotel').disabled = false;
        }
    }
function agregarHotelFromDB(numItinerario,datos){
        verificar_itinerario2FromDB(numItinerario);
        var frm=document.detallesItinerarios;
		var no_de_itinerario = numItinerario;
		//Busca el ultimo ID de las partidas y le sumamos uno para el siguiente
		var id = parseInt($("#hotel_table"+no_de_itinerario+">tbody >tr").length);
		if(isNaN(id)){
			id=1;
		}else{
			id+=parseInt(1);
		}
		$("#rowCount_hotel"+no_de_itinerario).val(parseInt($("#rowCount_hotel"+no_de_itinerario).val())+parseInt(1));
		var nuevaFila='<tr>';
		nuevaFila+="<td>"+"<div id='no_"+no_de_itinerario+"_"+id+"' name='no_"+no_de_itinerario+"_"+id+"'>"+id+"</div>"+"<input type='hidden' name='row_"+no_de_itinerario+"_"+id+"' id='row_"+no_de_itinerario+"_"+id+"' value='"+id+"' readonly='readonly' /></td>";
		nuevaFila+="<td><input type='hidden' name='ciudad_"+no_de_itinerario+"_"+id+"' id='ciudad_"+no_de_itinerario+"_"+id+"' value='"+datos['h_ciudad']+"' readonly='readonly' />"+datos['h_ciudad']+"</td>";
		nuevaFila+="<td><input type='hidden' name='hotel_"+no_de_itinerario+"_"+id+"' id='hotel_"+no_de_itinerario+"_"+id+"' value='"+datos['h_nombre_hotel']+"' readonly='readonly' />"+datos['h_nombre_hotel']+"</td>";
		nuevaFila+="<td><input type='hidden' name='comentario_"+no_de_itinerario+"_"+id+"' id='comentario_"+no_de_itinerario+"_"+id+"' value='"+datos['h_comentarios']+"' readonly='readonly' />"+datos['h_comentarios']+"</td>";
		nuevaFila+="<td><input type='hidden' name='tipoHotel_"+no_de_itinerario+"_"+id+"' id='tipoHotel_"+no_de_itinerario+"_"+id+"' value='"+datos['h_tipoHotel_id']+"' readonly='readonly' />"+datos['h_tipo_hotel_txt']+"</td>";
		nuevaFila+="<td><input type='hidden' name='noches_"+no_de_itinerario+"_"+id+"' id='noches_"+no_de_itinerario+"_"+id+"' value='"+datos['h_noches']+"' readonly='readonly' />"+datos['h_noches']+"</td>";
		nuevaFila+="<td><input type='hidden' name='llegada_"+no_de_itinerario+"_"+id+"' id='llegada_"+no_de_itinerario+"_"+id+"' value='"+datos['h_fecha_llegada']+"' readonly='readonly' />"+datos['h_fecha_llegada']+"</td>";
		nuevaFila+="<td><input type='hidden' name='salida_"+no_de_itinerario+"_"+id+"' id='salida_"+no_de_itinerario+"_"+id+"' value='"+datos['h_fecha_salida']+"' readonly='readonly' />"+datos['h_fecha_salida']+"</td>";
		nuevaFila+="<td><input type='hidden' name='noreservacion_"+no_de_itinerario+"_"+id+"' id='noreservacion_"+no_de_itinerario+"_"+id+"' value='"+datos['h_no_reservacion']+"' readonly='readonly' />"+datos['h_no_reservacion']+"</td>";
		nuevaFila+="<td><input type='hidden' name='costoNoche_"+no_de_itinerario+"_"+id+"' id='costoNoche_"+no_de_itinerario+"_"+id+"' value='"+datos['h_costo_noche'].replace(/,/g,"")+"' readonly='readonly' />"+datos['h_costo_noche']+"</td>";
		nuevaFila+="<td><input type='hidden' name='iva_"+no_de_itinerario+"_"+id+"' id='iva_"+no_de_itinerario+"_"+id+"' value='"+datos['h_iva'].replace(/,/g,"")+"' readonly='readonly' />"+datos['h_iva']+"</td>";
		nuevaFila+="<td><input type='hidden' name='total_"+no_de_itinerario+"_"+id+"' id='total_"+no_de_itinerario+"_"+id+"' value='"+datos['h_total'].replace(/,/g,"")+"' readonly='readonly' />"+datos['h_total']+"</td>";
		nuevaFila+="<td><input type='hidden' name='selecttipodivisa_"+no_de_itinerario+"_"+id+"' id='selecttipodivisa_"+no_de_itinerario+"_"+id+"' value='"+datos['div_id']+"' readonly='readonly' />"+datos['h_divisa_hotel_txt']+"</td>";
		nuevaFila+="<td><input type='hidden' name='montoP_"+no_de_itinerario+"_"+id+"' id='montoP_"+no_de_itinerario+"_"+id+"' value='"+datos['h_total_pesos'].replace(/,/g,"")+"' readonly='readonly' />"+datos['h_total_pesos']+"</td>";
		nuevaFila+="<td><div id='delDiv_"+no_de_itinerario+"_"+id+"' align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+id+"delHotel' id='"+id+"delHotel' onmousedown='borrarHotel(this.id,"+no_de_itinerario+",0);' style='cursor:pointer;' /></div><div align='center'>Eliminar Partida</div></td>";
		nuevaFila+="<input type='hidden' name='subtotal_"+no_de_itinerario+"_"+id+"' id='subtotal_"+no_de_itinerario+"_"+id+"' value='"+datos['h_subtotal'].replace(/,/g,"")+"' readonly='readonly' />";//var no_noches = parseFloat(($("#noches").val()).replace(/,/g,""));
		nuevaFila+= '</tr>';
		$("#hotel_table"+no_de_itinerario).append(nuevaFila);
		$("#no_itinerario").val(parseInt(frm.rowCount.value));
		if($("#hotel_table"+no_de_itinerario+">tbody>tr").length > 0){
			$("#aceptarHotel").removeAttr("disabled");
			$("#cancelarHotel").removeAttr("disabled");
		}
		calculaTotalHospedaje();
    }
	function verificar_itinerario(){
		var frm=document.detallesItinerarios;
		var num_posible_itinerario = parseInt($("#itinerarioActualOPosible").val());
		var count_auto = parseInt($("#solicitud_table>tbody >tr").length);
		if(num_posible_itinerario > count_auto){
			bandera_auto=1; //1 para indicar que se agregara un nuevo registro en la tabla de cotizacion de auto.
		}else if(num_posible_itinerario <= count_auto){
			$("#empresaAuto").val($("#empresaAuto"+num_posible_itinerario).val());
			$("#costoDia").val(number_format($("#costoDia"+num_posible_itinerario).val(),2,".",","));
			$("#tipoDivisa").val($("#tipoDivisa"+num_posible_itinerario).val());
			$("#tipoAuto").val($("#tipoAuto"+num_posible_itinerario).val());
			$("#totalAuto").val(number_format($("#totalAuto"+num_posible_itinerario).val(),2,".",","));
			$("#diasRenta").val($("#diasRenta"+num_posible_itinerario).val());
			$("#montoPesos").val(number_format($("#montoPesos"+num_posible_itinerario).val(),2,".",","));
			bandera_auto=0; //0 para indicar que solo se actualizara un registro en la tabla de cotizacion de auto.
		}
	}
	function verificar_itinerario2(){
		$("#salida").val("");
		$("#llegada").val("");
		$("#noches").val("");
		$("#costoNoche").val("");
		$("#iva").val("");
		var frm=document.detallesItinerarios;
		var num_posible_itinerario = frm.itinerarioActualOPosible.value;
		if($("#hotel_table"+num_posible_itinerario).val() == undefined){
			bandera_hotel=1; //1 para indicar que se agregara una nueva tabla de hoteles al itinerario posible.
		}else{
			bandera_hotel=0; //0 para indicar que solo se actualizara la tabla de hotel existente.
		}
	}
	function verificar_itinerario2FromDB(numItinerario){
		var num_posible_itinerario = numItinerario;
		if($("#hotel_table"+num_posible_itinerario).val() == undefined){
			agregarTablaHotelN(num_posible_itinerario);
		}else{
		}
	}
	function agregarAuto(){
		var frm=document.detallesItinerarios;
		//Busca el ultimo ID de las partidas y le sumamos uno para el siguiente
		var num_posible_itinerario = frm.itinerarioActualOPosible.value;
		if(bandera_auto == 1){
			if(($("#empresaAuto").val() != "" || $("#empresaAuto").val() != 0) ){
				if(($("#diasRenta").val() == "" || $("#diasRenta").val() == 0) || ($("#costoDia").val() == "" || $("#costoDia").val() == 0)){
					alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
					return false;
				}
				flotanteActive2(0);
			}else{
				alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
			}
		}else if(bandera_auto == 0){
			if($("#empresaAuto").val()!="" || $("#empresaAuto").val()!=0){
				if(($("#diasRenta").val() == "" || $("#diasRenta").val() == 0) || ($("#costoDia").val() == "" || $("#costoDia").val() == 0)){
					return false;
				}
				$("#empresaAuto"+num_posible_itinerario).val($("#empresaAuto").val());
				$("#costoDia"+num_posible_itinerario).val(parseFloat($("#costoDia").val().replace(/,/g,"")));
				$("#tipoDivisa"+num_posible_itinerario).val($("#tipoDivisa").val());
				$("#tipoAuto"+num_posible_itinerario).val($("#tipoAuto").val());
				$("#totalAuto"+num_posible_itinerario).val(parseFloat($("#totalAuto").val().replace(/,/g,"")));
				$("#diasRenta"+num_posible_itinerario).val($("#diasRenta").val());
				$("#montoPesos"+num_posible_itinerario).val(parseFloat($("#montoPesos").val().replace(/,/g,"")));
				flotanteActive2(0);
			}else{
				alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
			}
		}
    }
	function cancelar_cotizacion_hospedaje(limpia){
		var frm=document.detallesItinerarios;
		document.getElementById('enviar_hosp_agencia').checked = true;
		var no_de_itinerario = frm.itinerarioActualOPosible.value;
		var no_de_hoteles = parseInt($("#hotel_table"+no_de_itinerario+">tbody >tr").length);
		if(limpia == 1){
			for(i=1; i<=no_de_hoteles; i++){
				$("#hotel_table"+no_de_itinerario).find("tr:gt(0)").remove();
			}
			//Eliminar el div y la tabla hotel correspondiente al itinerario que se iba a cotizar
			if(no_de_itinerario==document.getElementById('area_tabla_div').childNodes.length){
				if(!estatus_en_edicion_de_itinerario){
					document.getElementById('area_tabla_div').removeChild(document.getElementById('area_tabla_div').childNodes[parseInt(no_de_itinerario-1)]);
				}else{
					document.getElementById('area_tabla_div').childNodes[parseInt(no_de_itinerario-1)].removeChild(document.getElementById('area_tabla_div').childNodes[parseInt(no_de_itinerario-1)].childNodes[0]);
				}
			}else if(no_de_itinerario<document.getElementById('area_tabla_div').childNodes.length){
					document.getElementById('area_tabla_div').childNodes[parseInt(no_de_itinerario-1)].removeChild(document.getElementById('area_tabla_div').childNodes[parseInt(no_de_itinerario-1)].childNodes[0]);
			}
			//Eliminar su correspondiente rowCount_Hotel y su rowDel_Hotel
			if(no_de_itinerario==document.getElementById('area_counts_div').childNodes.length){
				if(!estatus_en_edicion_de_itinerario){
					document.getElementById('area_counts_div').removeChild(document.getElementById('area_counts_div').childNodes[parseInt(no_de_itinerario-1)]);
				}else{
					document.getElementById('area_counts_div').childNodes[parseInt(no_de_itinerario-1)].removeChild(document.getElementById('area_counts_div').childNodes[parseInt(no_de_itinerario-1)].childNodes[1]);
					document.getElementById('area_counts_div').childNodes[parseInt(no_de_itinerario-1)].removeChild(document.getElementById('area_counts_div').childNodes[parseInt(no_de_itinerario-1)].childNodes[0]);
				}
			}else if(no_de_itinerario<document.getElementById('area_counts_div').childNodes.length){
					document.getElementById('area_counts_div').childNodes[parseInt(no_de_itinerario-1)].removeChild(document.getElementById('area_counts_div').childNodes[parseInt(no_de_itinerario-1)].childNodes[1]);
					document.getElementById('area_counts_div').childNodes[parseInt(no_de_itinerario-1)].removeChild(document.getElementById('area_counts_div').childNodes[parseInt(no_de_itinerario-1)].childNodes[0]);
			}
			//Limpiar los campos del hotel
			limpiarCamposDeHotel();
			frm.hospedaje[0].checked = true;
			frm.hospedaje[1].checked = false;
			frm.enviar_hosp_agencia[0].checked = true;
			frm.enviar_hosp_agencia[1].checked = false;
			$("#divbotonHotel").css("display", "none");
		}else if(limpia == 0 && no_de_hoteles > 0){
			no_hotel_nuevo = parseInt($("#hotel_table"+no_de_itinerario+">tbody >tr").length);
			limpiarCamposDeHotel();
			frm.hospedaje[0].checked = true;
			frm.hospedaje[1].checked = false;
			frm.enviar_hosp_agencia[0].checked = false;
			frm.enviar_hosp_agencia[1].checked = true;
			$("#divbotonHotel").css("display", "block");
		}else{
			limpiarCamposDeHotel();
			$("#divbotonHotel").css("display", "none");
		}
	}
	function calculaTotalHospedaje(){
		//Aqui se saca el monto total de hoteles
		var no_de_itinerario = $("#no_itinerario").html();
		var id = parseInt($("#hotel_table"+no_de_itinerario+">tbody >tr").length);
		var total_hospedaje = 0;
		var total_noches = 0;
		for(i=1; i<=id; i++){
			total_hospedaje = parseFloat(total_hospedaje) + parseFloat(($("#montoP_"+no_de_itinerario+"_"+i).val()).replace(/,/g,""));
			total_noches = parseInt(total_noches) + parseInt(($("#noches_"+no_de_itinerario+"_"+i).val()));
		}
		var redondear = Math.round(total_hospedaje * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
		$("#TotalHospedaje").html(number_format(redondear,2,".",","));
		$("#total_noches").val(total_noches);
	}
	function limpiarCamposDeHotel(){
		$("#ciudad").val("");
		$("#hotel").val("");
		$("#llegada").val("");
		$("#noches").val("0");
		$("#costoNoche").val("0");
		$("#subtotal").val("0.00");
		$("#salida").val("");
		$("#iva").val("0");
		$("#noreservacion").val("");
		$("#total").val("0.00");
		$("#montoP").val("0.00");
		$("#comentario").val("");
		$("#selecttipodivisa").val("1");
		$("#tipoHotel").val(-1);
	}
	function borrarTodo(bandera){
		var frm=document.detallesItinerarios;
		var num_posible_itinerario = frm.itinerarioActualOPosible.value
		//reinicio de valor de campos
		$("#empresaAuto").val("");
		$("#costoDia").val("");
		$("#tipoDivisa").val("1");
		$("#tipoAuto").val("1");
		$("#totalAuto").val("0.00");
		$("#diasRenta").val("");
		$("#montoPesos").val("0.00");
		//reinicio de valor de campos ocultos
		$("#empresaAuto"+num_posible_itinerario).val("");
		$("#costoDia"+num_posible_itinerario).val("");
		$("#tipoDivisa"+num_posible_itinerario).val("1");
		$("#tipoAuto"+num_posible_itinerario).val("1");
		$("#totalAuto"+num_posible_itinerario).val("0.00");
		$("#diasRenta"+num_posible_itinerario).val("");
		$("#montoPesos"+num_posible_itinerario).val("0.00");
		if(bandera == 1){
			limpiar_radiosNO();
		}else{
			limpiar_radios();
		}
		$("#divbotonAuto").css("display", "none");
		$("#aceptarAuto").attr("disabled", "disabled");
		$("#cancelarAuto").attr("disabled", "disabled");
	}
	function limpiarCamposDeAuto(limpiar, cerrar){
		var frm=document.detallesItinerarios;
		var num_posible_itinerario = frm.itinerarioActualOPosible.value;
		if(limpiar == 1){
			if(estatus_en_edicion_de_itinerario){
				if($("#empresaAuto").val() != ""){
					if($("#empresaAuto"+num_posible_itinerario).val() == ""){
						frm.auto[0].checked = true;
						frm.enviar_auto_agencia[0].checked = true;
						$("#divbotonAuto").css("display", "none");
					}else{
						frm.auto[0].checked = true;
						frm.enviar_auto_agencia[1].checked = true;
						$("#divbotonAuto").css("display", "block");
					}
				}else{
					limpiar_radios();
					$("#divbotonAuto").css("display", "none");
				}
			}else{
				var frm=document.detallesItinerarios;											
				$("#empresaAuto").val("");
				$("#costoDia").val("");
				$("#tipoDivisa").val("1");
				$("#tipoAuto").val("1");
				$("#totalAuto").val("0.00");
				$("#diasRenta").val("");
				$("#montoPesos").val("0.00");
				frm.enviar_auto_agencia[0].checked = true;
			    frm.enviar_auto_agencia[1].checked = false;
				$("#divbotonAuto").css("display", "none");
			}
		}else if(cerrar == 1 && ($("#empresaAuto").val() == "" || ($("#costoDia").val() == 0.00 || $("#costoDia").val() == ""))){
			frm.auto[0].checked = true;
			frm.enviar_auto_agencia[0].checked = true;
			$("#divbotonAuto").css("display", "none");
		}else if(cerrar == 1 && ($("#empresaAuto").val() != "" || ($("#costoDia").val() != 0.00 || $("#costoDia").val() != ""))){
			if($("#empresaAuto"+num_posible_itinerario).val() == ""){
				frm.auto[0].checked = true;
				frm.enviar_auto_agencia[0].checked = true;
				$("#divbotonAuto").css("display", "none");
			}
		}
	}
	//
	//  Esta funcion recalcula los montos de la tabla de conceptos
	//
	/****************VALIDACIONES********************************************/
	/***************************************************************************/
	//VARIABLE GLOBAL
	var textoAnterior = '';
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
		function getSubtotal(){
			var no_noches = parseFloat(($("#noches").val()).replace(/,/g,""));
			var co_noches = parseFloat(($("#costoNoche").val()).replace(/,/g,""));
			var resultado= no_noches*co_noches;
			document.detallesItinerarios.subtotal.value = number_format(resultado,2,".",",");
			var subtot = parseFloat(($("#subtotal").val()).replace(/,/g,""));
			var total_iva = parseFloat(($("#iva").val()).replace(/,/g,""));
			if(isNaN(total_iva)){
				total_iva=0;
			}
			var resultado2 = subtot+total_iva;
			document.detallesItinerarios.total.value = number_format(resultado2,2,".",",");
		    var total = parseFloat(($("#total").val()).replace(/,/g,""));
			var divisas = $("#selecttipodivisa").val();
			var tasaNueva = 1;
			if(divisas != 1){ //Si la divisa es diferente a MXN
				//Se obtiene las tasas de las divisas
				var tasa = tasaDivisa;
				var tasa2 = tasa.split(":");
				//Se obtiene la tasa de la divisa seleccionada
				for(i=0;i<=tasa2.length;i=i+2){
					if(tasa2[i] == divisas){
						tasaNueva = tasa2[i+1];
					}
				}
			}
			var redondear = 0;
			var totalTotal = total * parseFloat(tasaNueva);
			var redondear = Math.round(totalTotal * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			document.detallesItinerarios.montoP.value = number_format(redondear,2,".",",");
		}
		function getTotal_Auto(){
			var dias_renta = parseFloat(($("#diasRenta").val()).replace(/,/g,""));
			var costo_dia = parseFloat(($("#costoDia").val()).replace(/,/g,""));
			var resultado= dias_renta*costo_dia;
			document.detallesItinerarios.totalAuto.value = number_format(resultado,2,".",",");
			var divisaAuto = $("#tipoDivisa").val();
			var tasaNueva = 1;
			if(divisaAuto != 1){ //Si la divisa es diferente a MXN
				//Se obtiene las tasas de las divisas
				var tasa = tasaDivisa;
				var tasa2 = tasa.split(":");
				//Se obtiene la tasa de la divisa seleccionada
				for(i=0;i<=tasa2.length;i=i+2){
					if(tasa2[i] == divisaAuto){
						tasaNueva = tasa2[i+1];
					}
				}
			}
			var redondear = 0;
			var totalTotal = resultado * parseFloat(tasaNueva);
			var redondear = Math.round(totalTotal * Math.pow(10, 2)) / Math.pow(10, 2);//redondea a 2 decimales
			document.detallesItinerarios.montoPesos.value = number_format(redondear,2,".",",");
		}
		//funcion que realizara la persistencia de la fecha de llegada en base al tipo de viaje
		function fechaLlegadaReinicio(){
			var longitud = parseInt($("#solicitud_table>tbody >tr").length);
			if($("#select_tipo_viaje_pasaje option:selected").text() == "Multidestinos"){
				for(var i=1;i<=longitud;i++){
					$('#fechaLlegada'+i).val($('#salida'+(i+1)).val());
					if(i == longitud){
						$('#fechaLlegada'+i).val($('#salida'+i).val());
					}
				}
			}else if($("#select_tipo_viaje_pasaje option:selected").text() == "Sencillo"){
				for(var i=1;i<=longitud;i++){
					$('#fechaLlegada'+i).val($('#salida'+i).val());
				}
			}
		}
		function obtiene_fecha() {
			var fecha_actual = new Date();  
			var dia = fecha_actual.getDate();
			var mes = fecha_actual.getMonth() + 1;
			var ano = fecha_actual.getFullYear();
			if (mes < 10)
				mes = '0' + mes;
			if (dia < 10)
				dia = '0' + dia;
			return (dia + "/" + mes + "/" + ano);
		}
		function validaMinimoAnticipos(){
			var valor = true;
			var contAnticipos = 0;
			var no_conceptos = $("#conceptos_table>tbody>tr").length;
			if($("#Accion").is(":checked") == true && no_conceptos  < 1 ){
				alert("No puede guardar la solicitud sin anticipos, agregué un anticipo o desmarque la casilla de Requerimientos de anticipo.");
				valor = false;
			}else{
				for(var i=1;i<=no_conceptos;i++){
					if($("#itinerarios_cbx"+i).val() == 0){
						contAnticipos++;
					}
				}
				if(contAnticipos != 0){
					alert("No se puede guardar la solicitud ya que 1 o más anticipos no han sido asignados a un itinerario.");
					valor = false;
				}
			}
			return valor;
		};
		function confirmacion(valor){
			var frm=document.detallesItinerarios;
			if(!validaMinimoAnticipos()) return false;
			var texto = "";
			$("#select_tipo_viaje_pasaje").removeAttr("disabled");
			var tramite_id=gup("id");
			var delegado = $("#delegado").val();
			if(frm.motive.value.length<5) {
				alert("El motivo debe tener al menos 5 caracteres.");
				frm.motive.focus();
				return false;
			}else if(tramite_id != ""){
				if(valor == "autorizar_cotizacion"){
					texto ="AVISO: ¿Está seguro que desea enviar la solicitud a los Autorizadores correspondientes ?";
				}else if(delegado != 0){
					texto ="AVISO: ¿Está seguro que desea enviar la Solicitud?";
				}else{
					if($.trim($('#guardarComp').val()) != "Enviar Solicitud"){
						texto ="AVISO: ¿Está seguro que desea enviar nuevamente la solicitud a la Agencia de Viajes ?";
					}else{
						texto ="AVISO: ¿Está seguro que desea enviar la Solicitud?";
					}
				}
			}else{
				texto ="AVISO: ¿Está seguro que desea enviar la Solicitud?";
			}
			if(confirm(texto)){
				var usuario = $("#idusuario").val();
				var Parametros = {
					'idUsuario': 	usuario,
					'idConcepto':	null,
					'flujo':	 	1,
					'region':	 	null
				};
				validaPoliticas(Parametros);
				//Valores de  las fechas de llegada correspondientes
				fechaLlegadaReinicio();
				//realizaremos la validacion del presupuesto
				var diferencia=0;
				var TotalSolicitud = parseFloat($("#totalSol").val().replace(/,/g,""));
				var parametros13 = "totalPresupuesto="+TotalSolicitud+"&cecoSel="+$("#cat_cecos_cargado").val();
				var respuesta13 = obtenJson(parametros13);
					if(respuesta13 != false || respuesta13 != 0){
						alert("El monto de la solicitud excede el presupuesto disponible.");
					}
				return true;
			}else{
				return false;
			}
		}
		function solicitarConfirmPrevio(){
			if(!validaMinimoAnticipos()) return false;
			var frm=document.detallesItinerarios;
			if(confirm("¿Desea guardar esta Solicitud como previo?")){
				var usuario = $("#idusuario").val();
				var Parametros = {
					'idUsuario': 	usuario,
					'idConcepto':	null,
					'flujo':	 	1,
					'region':	 	null
				};
				validaPoliticas(Parametros);
			}else{
				return false;
			}
		}
		function verificaNoItinerarios(){
			var id = parseInt($("#solicitud_table>tbody >tr").length);
			var tipoViaje = $("#select_tipo_viaje_pasaje option:selected").val();
			var viajeText = $("#select_tipo_viaje_pasaje option:selected").text();
			switch (tipoViaje){
				case "-1":
					alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
					$("#select_tipo_viaje_pasaje").focus();
					$("#registrar_comp").removeAttr('disabled');
					$("#Accion").attr('checked',false);
					$("#guardarprevComp").attr('disabled','disabled');
					$("#guardarComp").attr('disabled','disabled');
					return false;
					break;
				case "1":
					if(id == 0){
						alert("Ha seleccionado un viaje: " + viajeText + ", ingrese al menos un itinerario.");
						$("#registrar_comp").removeAttr('disabled');
						$("#Accion").attr('checked',false);
						$("#guardarprevComp").attr('disabled','disabled');
						$("#guardarComp").attr('disabled','disabled');
						return false;
					}
					break;
				case "2":
					if(id == 0){
						alert("Ha seleccionado un viaje: " + viajeText + ", ingrese al menos un itinerario.");
						$("#registrar_comp").removeAttr('disabled');
						$("#Accion").attr('checked',false);
						$("#guardarprevComp").attr('disabled','disabled');
						$("#guardarComp").attr('disabled','disabled');
						return false;
					}
					break;
				case "3":
					if(id < 2){
						alert("Ha seleccionado un viaje: " + viajeText + ", ingrese al menos dos itinerarios.");
						$("#registrar_comp").removeAttr('disabled');
						$("#Accion").attr('checked',false);
						$("#guardarprevComp").attr('disabled','disabled');
						$("#guardarComp").attr('disabled','disabled');
						return false;
					}
					break;
			}
		}
		function verificar_conceptos2(){
			var id = parseInt($("#solicitud_table>tbody>tr").length);
			var tipoViaje = $("#select_tipo_viaje_pasaje").val();
			var viajeText = $("#select_tipo_viaje_pasaje option:selected").text();
			if($("#Accion").is(":checked")){
				if(estatus_en_edicion_de_itinerario){
					alert("Se esta editando un itinerario, de click antes en Actualizar Itinerario");
					$("#Accion").attr('checked',false);
					return false;
				}
				switch (tipoViaje){
					case "-1":
						alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
						$("#select_tipo_viaje_pasaje").focus();
						$("#Accion").attr('checked',false);
						return false;
						break;
					case "1":
						if(id != 0){
							if(confirm("AVISO: ¿La información de cada itinerario es correcta?")){
								deshabilitarEditElim();
								crear_combo_itinerarios();
								desplegar(Sconcepto);
								desplegar(lConceptos);
								//Se crean los divs que contendran la tabla y contadores de los conceptos
								if($("#desactivandoCheck").val() == 1){
									var no_de_itinerarios = parseInt($("#solicitud_table>tbody >tr").length);
									for(i=1; i<=no_de_itinerarios; i++){
										crear_divs_area_conceptos();
										crear_tabla_concepto(i);
									}
								}
							}else{
								$("#Accion").attr('checked',false);
							}
						}else{
							alert("Ha seleccionado un viaje: " + viajeText + ", ingrese al menos un itinerario.");
							$("#Accion").attr('checked',false);
							return false;
						}
						break;
					case "2":
						if(id >= 1){
							if(confirm("AVISO: ¿La información de cada itinerario es correcta?")){
								deshabilitarEditElim();
								crear_combo_itinerarios();
								desplegar(Sconcepto);
								desplegar(lConceptos);
								//Se crean los divs que contendran la tabla y contadores de los conceptos
								if($("#desactivandoCheck").val() == 1){
									var no_de_itinerarios = parseInt($("#solicitud_table>tbody >tr").length);
									for(i=1; i<=no_de_itinerarios; i++){
										crear_divs_area_conceptos();
										crear_tabla_concepto(i);
									}
								}
							}else{
								$("#Accion").attr('checked',false);
							}
						}else{
							alert("Ha seleccionado un viaje: " + viajeText + ", ingrese al menos un itinerario.");
							$("#Accion").attr('checked',false);
							return false;
						}
						break;
					case "3":
						$("#registrar_comp").attr('disabled','disabled');
						if(id >= 2){
							if(confirm("AVISO: ¿La información de cada itinerario es correcta?")){
								deshabilitarEditElim();
								crear_combo_itinerarios();
								desplegar(Sconcepto);
								desplegar(lConceptos);
								//Se crean los divs que contendran la tabla y contadores de los conceptos
								if($("#desactivandoCheck").val() == 1){
									var no_de_itinerarios = parseInt($("#solicitud_table>tbody >tr").length);
									for(i=1; i<=no_de_itinerarios; i++){
										crear_divs_area_conceptos();
										crear_tabla_concepto(i);
									}
								}
							}else{
								$("#Accion").attr('checked',false);
							}
						}else{
							alert("Ha seleccionado un viaje: " + viajeText + ", ingrese al menos dos itinerarios.");
							$("#registrar_comp").removeAttr('disabled');
							$("#Accion").attr('checked',false);
							return false;
						}
						break;
				}
			}else{
				if(confirm("AVISO: Si se realiza la edición del itinerario, las tablas de los conceptos asignados serán eliminadas")){
					//Se tomara el valor de los demas anticipos
					var no_de_conceptos = parseInt($("#conceptos_table>tbody >tr").length);
					var sumAnticipos = 0;
					var anticiposCalculados = 0;
					for(var i=1;i<=no_de_conceptos;i++){
						if($("#Concepto"+i).val() != "5" && $("#Concepto"+i).val() != "10"){
							sumAnticipos += parseFloat($("#MontoEnPesos"+i).val().replace(/,/g,""));
						}
					}
					// Borrar los conceptos asignados a los itinerarios, así como lso mensajes de excepciones generados por rebasar el límites de las políticas.
					$("#desactivandoCheck").val(1);
					$("#area_tablas_conceptos_div").html("");
					$("#area_counts_conceptos_div").html("");
					$("#capaWarning").html("");
					habilitarEditElim();
					$("#conceptos_table>tbody").html("");
					$("#anticipoC").val("0.00");
					$("#totalanticipo").val(0.00);
					anticiposCalculados = parseFloat($("#totalSol").val().replace(/,/g,"")) - sumAnticipos;
					$("#totalA").val(number_format(anticiposCalculados,2,".",","));
					$("#totalSol").val(number_format(anticiposCalculados,2,".",","));
					desplegar(Sconcepto);
					desplegar(lConceptos);
					if(tipoViaje == 3){
						$("#registrar_comp").removeAttr('disabled');
					}
					var longitud = parseInt($("#solicitud_table>tbody >tr").length);
					for(var i=1;i<=longitud;i++){
						//Se cran los divs que contendran la tabla y contadores de los conceptos
						crear_divs_area_conceptos();
						crear_tabla_concepto(i);
					}
				}else{
					$("#Accion").attr('checked',true);
				}
			}
		}
		function verificar_conceptos(){
			var no_de_conceptos = parseInt($("#conceptos_table>tbody >tr").length);
			if(no_de_conceptos<=0){
				if(confirm("AVISO: ¿La informacion de cada itinerario es correcta?")){
					crear_combo_itinerarios();
					desplegar(Sconcepto);
					desplegar(lConceptos);
				}
			}else{
				if(confirm("AVISO: Si se realiza la edición del itinerario, las tablas de los conceptos asignados serán eliminadas")){
					var no_de_conceptos = parseInt($("#conceptos_table>tbody >tr").length);
					for(i=1; i<=no_de_conceptos; i++){
						$("#conceptos_table").find("tr:gt(0)").remove();
					}
				}
			}
		}
		function mensaje_confirmacion_auto2(){
			if($("#empresaAuto").val() != ""){
				if(confirm("AVISO: Si se cambia la opción se perderá la información correspondiente a la Renta de auto ¿Desea continuar?")){
					borrarTodo(0);
				}else{
					var frm=document.detallesItinerarios;
					frm.enviar_auto_agencia[1].checked = true;
				}
			}
		}
		function mensaje_confirmacion_auto1(){
			if($("#empresaAuto").val() != ""){
				if(confirm("AVISO: Si se cambia la opción se perderá la información correspondiente a la Renta de auto ¿Desea continuar?")){
					borrarTodo(1);
				}else{
					var frm=document.detallesItinerarios;
					frm.auto[0].checked = true;
					frm.enviar_auto_agencia[1].checked = true;
					$("#divbotonAuto").css("display", "block");
				}
			}
		}
		function mensaje_confirmacion_hotel2(){
			var id=0;
			var frm=document.detallesItinerarios;
			if(estatus_en_edicion_de_itinerario){
				id= frm.itinerarioActualOPosible.value;
			}else{
				id = parseInt($("#solicitud_table>tbody >tr").length);
				if(isNaN(id)){
					id=1;
				}else{
					id+=parseInt(1);
				}
			}
			var no_hoteles = parseInt($("#hotel_table"+id+">tbody >tr").length);
			if(no_hoteles >0){
				if(confirm("AVISO: Si se cambia la opción se perderá la información correspondiente al Hospedaje ¿Desea continuar?")){
					cancelar_cotizacion_hospedaje(1);
					$("#divbotonHotel").css("display", "none");
				}else{
					var frm=document.detallesItinerarios;
					frm.enviar_hosp_agencia[1].checked = true;
				}
			}
		}
		function mensaje_confirmacion_hotel1(){
			var frm=document.detallesItinerarios;
			var id = frm.itinerarioActualOPosible.value;
			var no_hoteles = parseInt($("#hotel_table"+id+">tbody >tr").length);
			if(no_hoteles >0){
				if(confirm("AVISO: Si se cambia la opción se perderá la información correspondiente al Hospedaje ¿Desea continuar?")){
					cancelar_cotizacion_hospedaje(1);
					$("#divbotonHotel").css("display", "none");
					$("#enviar_agencia_hotel").css("display", "none");
					$("#enviar_agencia_hotel_input").css("display", "none");
					frm.hospedaje[1].checked = true;
					frm.enviar_hosp_agencia[1].checked = true;
				}else{
					var frm=document.detallesItinerarios;
					frm.hospedaje[0].checked = true;
					frm.enviar_hosp_agencia[1].checked = true;
					$("#divbotonHotel").css("display", "block");
				}
			}else{
				radioH(0);
			}
		}
		function confirmacion_conceptos_editar(id){
			var no_conceptos = parseInt($("#conceptos_table>tbody>tr").length);
			if(no_conceptos > 0){
				if(confirm("AVISO: Si se realiza la edición del itinerario, la tabla de los conceptos asignados sera eliminada ¿Desea continuar?")){
					eliminarCalculosDeConceptos();
					return true;
				}else{
					//no hace nada se queda en la misma parte
					return false;
				}
			}else{
				return true;
			}
		}
		function confirmacion_conceptos_eliminar(){
			var no_conceptos = parseInt($("#conceptos_table>tbody>tr").length);
			if(no_conceptos > 0){
				if(confirm("AVISO: Si se realiza la edición del itinerario, la tabla de los conceptos asignados sera eliminada ¿Desea continuar?")){
					eliminarCalculosDeConceptos();
					return true;
				}else{
					//no hace nada se queda en la misma parte
					return false;
				}
			}else{
				return true;
			}
		}
		function confirmacion_requerimientos() {
			return confirm('AVISO: ¿La información de cada itinerario es correcta?');
		}
		function alertPresupuesto(){
			alert("El monto de la solicitud excede el presupuesto disponible.");
		}
		function deshabilitarEditElim(){
			var longitud = parseInt($("#solicitud_table>tbody >tr").length);
				for(var i=1;i<=longitud;i++){
					$("#"+i+"edit").css("display", "none");
					$("#"+i+"del").css("display", "none");
				}
		}
		function habilitarEditElim(){
			var longitud = parseInt($("#solicitud_table>tbody >tr").length);
			for(var i=1;i<=longitud;i++){
				$("#"+i+"edit").css("display", "block");
				$("#"+i+"del").css("display", "block");
			}
		}
		function calculoDiasSol(){
			var diasSolicitud = 0;
			var diasItinerario = 0;
			var count_renglones = parseInt($("#solicitud_table>tbody>tr").length);
			for(var i=1;i<=count_renglones;i++){
				diasItinerario = 0;
				diasItinerario = get_dias(i);
				diasSolicitud += diasItinerario;
				$("#noDias"+i).val(diasItinerario);
			}
		}
		function revisarComboConceptos(){
			var hopedaje_bandera = false;
			var auto_bandera = false;
			var count_renglones = parseInt($("#solicitud_table>tbody>tr").length);
			// Recorre la tabla de los itinerarios Ingresados para saber cuales serán enviados a agencia caso(Hotel/Renta de Auto).
			for(var i=1;i<=count_renglones;i++){
				if($("#CheckHAgencia"+i).val() == "true"){
					hopedaje_bandera = true;
				}
				if($("#CheckTAgencia"+i).val() == "true"){
					auto_bandera = true;
				}
			}
			fillComboConceptos(hopedaje_bandera,auto_bandera);
		}
		function validaZeroIzquierda(monto,campo){		
			if(monto == 0 || monto == "" || monto == "NaN"){
				$("#"+campo).val(0);
			}else if( monto.substring(monto.length-1,monto.length) === "." || monto.substring(monto.length-2,monto.length) === ".0"){
				$("#"+campo).val(monto);
			}else{
				$("#"+campo).val(parseFloat(monto));
			}
		}
		function mensaje_confirmacion_rechazadoHotel(){
			var frm=document.detallesItinerarios;
			var id = frm.itinerarioActualOPosible.value;
			var no_hoteles = parseInt($("#hotel_table"+id+">tbody >tr").length);
			if(no_hoteles >0){
				if(confirm("AVISO: Agencia ya ha realizado la cotización correspondiente al Hotel, si cambia la opción se perderán los datos  ¿Desea continuar?")){
					cancelar_cotizacion_hospedaje(1);
					frm.hospedaje[0].checked = true;
					$("#enviar_agencia_hotel").css("display", "none");
					$("#enviar_agencia_hotel_input").css("display", "none");
				}else{
					frm.enviar_hosp_agencia[0].checked = true;
				}
			}else{
				flotanteActive(1);
				$("#divbotonHotel").css("display", "block");
			}
		}
		function mensaje_confirmacion_rechazadoAuto(){
			var frm=document.detallesItinerarios;
			if($("#empresaAuto").val() != ""){
				if(confirm("AVISO: Agencia ya ha realizado la cotización correspondiente al Auto, si cambia la opción se perderán los datos  ¿Desea continuar?")){
					borrarTodo(1);
					frm.auto[0].checked = true;
					$("#enviar_agencia_auto").css("display", "none");
					$("#enviar_agencia_auto_input").css("display", "none");
				}else{
					frm.enviar_auto_agencia[0].checked = true;
				}
			}else{
				flotanteActive2(1);
				$("#divbotonAuto").css("display", "block");
			}
		}
		//Funcion que permitira obtener el total de la solicitud cuando es una edicion (Rechazada/Guar-previo)
		function getTotalBD(tramiteEdit){
			var parametros14 = "totalBD="+tramiteEdit;
			var respuesta14 = obtenJson(parametros14);
			if(respuesta14 != false){
				$("#totalA").val(number_format(respuesta14,2,".",","));
				$("#totalSol").val(number_format(respuesta14,2,".",","));
				montoTotalAvion(tramiteEdit);
			}
		}
		function verificaRegion(){
			var viaje = $("#select_tipo_viaje option:selected").val();
			if(viaje != -1){
				DiasAnterioresViaje(viaje);
			}
		}