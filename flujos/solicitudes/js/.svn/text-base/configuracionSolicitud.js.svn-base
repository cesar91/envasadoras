	/**
	 * Inicializa los Eventos una vez que se ha ter minado de Cargar el DOM (Document Object Model)
	 */
	var doc = $(document);
	doc.ready(init);
	
	function init(){
		$(document).bind("contextmenu", function(e){ e.preventDefault(); });
		
		var urlServices = "services/ajax_solicitudes_gastos.php";
		
		/**
		 * Configuraci贸n Default del Objeto AJAX de JQuery
		 *
		 * @attr url string			=> Indicamos la ruta al servidor que se encarga de atender las peticiones
		 * @attr timeout int		=> Indicamos el tiempo de espera de respuesta del servidor
		 * @attr async string		=> Indicamos el modo de peticion ser谩 sincrono o asincrono
		 * @attr cache boolean		=> Indicamos si guardaremos en la cache las respuestas del servidor
		 * @attr type string		=> Indicamos por que metodo se enviar谩 la informaci贸n al servidor
		 * @attr dataType string	=> Indicamos el tipo de codificaci贸n de los datos que esperamos como respuesta
		 * @attr contentType string => Indicamos el tipo de codificaci贸n de los datos enviados
		 */		 
		$.ajaxSetup({
			'url':			urlServices,
			'timeout':		5000,
			'async':		false,
			'cache':		false,
			'type':			'post',
			'dataType':		'json',			
			'contentType':	'application/x-www-form-urlencoded; charset=utf-8'		
		});
		
		/**
		 * Configuracion Regional de los calendarios en la pantalla.		
		 */
		jQuery(function($){
			$.datepicker.regional['es'] = {
				closeText: 'Cerrar',
				prevText: '<Ant',
				nextText: 'Sig>',
				currentText: 'Hoy',
				monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
				monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
				dayNames: ['Domingo', 'Lunes', 'Martes', 'Mi茅rcoles', 'Jueves', 'Viernes', 'Sabado'],
				dayNamesShort: ['Dom','Lun','Mar','Mi茅','Juv','Vie','Sab'],
				dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
				weekHeader: 'Sm',
				dateFormat: "dd/mm/yy",
				changeMonth: true,
				changeYear: true,
				minDate: obtenFecha(),
				showOn: "both",
				buttonImage: "../../images/b_calendar.png",
				buttonImageOnly: true,	 
				constrainInput: true,
				firstDay: 1,
				showWeek: true,      
				isRTL: false
			};
			$.datepicker.setDefaults($.datepicker.regional['es']);
		});
		
		$("#fecha_sol").datepicker();

		// Carga de tasas
		obtener_tasa();

		/**
     	 * Arreglo de todos los elementos que deben ocultarse una vez cargada la pantalla
		 */
		var ocultos = new Array("excepcion_table",
								"td_Divisa",
								"tr_Anticipo",
								"tasaUSD",
								"tasaEUR",
								"rowCount",
								"idusuario",
								"empresa",
								"tramiteId",
								"totalExcepciones",
								"capaWarning");			
		for(var i = 0; i < ocultos.length; i++)
			ocultarElemento(ocultos[i], "hide");
		
		var tramite_id = gup("id");
		if(tramite_id != ""){							
			fillform(tramite_id);
		}
		
		// Ignorar los eventos en el campo de fecha para evitar que el usuario pueda borrar la fecha
		$('#fecha_sol').keydown(function(e){
			ignoraEventKey(e);
		});
		
		/**
		 * Evento, inicializa <input monto>
		 *
		 * 1 Asgina una mascara al campo
		 */
		$('#monto_solicitado').priceFormat({
			prefix: '', centsSeparator: '.', thousandsSeparator: ','
		});
	}
	
	var valorComidasRepresentacion = 1;
	var usuario = $("#idusuario").val();

	/**
		 * Arreglo de todos los conceptos que tomaran una validacion por fecha
	 */
	var conceptosPoliticasFecha = new Array("13", "23", "66", "69", "75", "83");

	/**
		 * Arreglo de todos los conceptos que tomaran una validacion por fecha
	 */
	var conceptosPoliticasDias = new Array(["66", "1"], ["69", "1"], ["75", "1"], ["83", "1"]);

	/**
		 * Arreglo de todos los conceptos que tomaran una validacion por fecha
	 */
	var conceptosPoliticasNoches = new Array("5");
	
	
/**
 * Verifica si el anticipo que se esta registrando o la suma de este no excede el anticipo asignado a la solicitud
 *
 * @return boolean	=> Devuelve true si aun no se ha comprobado todo el anticipo, de lo contrario false
 */	
function validaPolitica(ObjetoFormulario, Parametros, conceptosPoliticasFecha, previo){
	var regionNacional = 1;
	
	Parametros["idConcepto"] = ObjetoFormulario["concepto"];
	Parametros["region"] = regionNacional;
	
	var validacion = false;
	for(var i = 0; i < conceptosPoliticasFecha.length; i++){
		if(ObjetoFormulario["concepto"] == conceptosPoliticasFecha[i]){
			validacion = true;
			break;
		}
	}
	
	//var tablaLength = obtenTablaLength("comprobacion_table");
	var concepto = new Array();
	if(validacion){
		//for(var i = 1; i <= tablaLength; i++ ){		
			//if( $("#row_concepto"+i).val() == ObjetoFormulario["concepto"] && $("#row_fecha"+i).val() == ObjetoFormulario["fecha"]){
				//if( concepto.length == 0){
					concepto[0] = ObjetoFormulario["concepto"];
					concepto[1] = limpiaCantidad(ObjetoFormulario["montoSolicitado"])/100;
					concepto[2] = ObjetoFormulario["fecha"];
					//concepto[3] = $("#row_"+i).val();
				//}else{
					//concepto[1]+= limpiaCantidad($("#row_totalPartida"+i).val())/100;
					//concepto[3]+= ","+$("#row_"+i).val();
				//}
			//}
		//}
	}else
		return false;
	
	var msg = "";
	var excedente = 0;
	
	var politica = obtenerPolitica(Parametros);
	excedente = concepto[1] - politica;
	
	if(politica == 0 || excedente <= 0)
		return false;
	
	if(!previo)
		alert("Se ha excedido el importe diario permitido por pol&iacute;tica en concepto de: "+ObjetoFormulario["conceptoTexto"]+" para la fecha "+ ObjetoFormulario["fecha"]);						
	
	
	var Excepcion = {
		"concepto":		ObjetoFormulario["concepto"],
		"mensaje":		"Se ha excedido el importe diario permitido por pol&iacute;tica en concepto de: " + ObjetoFormulario["conceptoTexto"]+" para la fecha "+ ObjetoFormulario["fecha"],
		"fecha":		ObjetoFormulario["fecha"], 
		"referencia":	1, // "referencia":	concepto[3], 
		"totalPartida":	ObjetoFormulario["montoSolicitado"], 
		"excedente":	excedente.toFixed(2)
	}
	
	try{
		var idRemover = Excepcion["referencia"].split(",");			
		for(var i = 0; i < idRemover.length; i++)
			$("#excepcion_table>tbody>tr#tr_"+idRemover[i]).fadeOut(350).remove();				
	}catch(e){}
	
	return Excepcion;		
}

/**
 * Funciones para guardar las excepciones
 */
function verificaExcepcion(){
	var usuario = $("#idusuario").val();
	/**
	 * Objeto Parametro
	 *
	 * @attr idUsuario int	=> 	id del usuario en sesion
	 * @attr idConcepto int	=>	id del concepto
	 * @attr flujo int 		=>	id del flujo (Solicitud de Gastos)
	 * @attr region int 	=>	id de la region del viaje a comprobar
	 */		
	var Parametros = {
		'idUsuario': 	usuario,
		'idConcepto':	null,
		'flujo':	 	2,
		'region':	 	null
	};

	$("#excepcion_table tr:gt(0)").remove();
	var tablaLength = obtenTablaLength("excepcion_table");
	var id = tablaLength+1;
	
	var objeto = generaObjetoFormulario();
	var Excepcion = validaPolitica(objeto, Parametros, conceptosPoliticasFecha, false);
	if(Excepcion != false)
		var renglonExcepcion = crearRenglonExcepcion(Excepcion, id);
		agregaRenglon(renglonExcepcion, "excepcion_table");
}

// Obtener tasas
function obtener_tasa(){
	var tasaUSD = 0;
	var tasaEUR = 0;
	
	$.ajax({
		type: "POST",
		url: "services/ajax_solicitudes_gastos.php",
		data: "cargaTasas=1",
		dataType: "json",
		timeout: 10000,
		success: function(json){
			tasaUSD = json[0].tasaUSD;
			tasaEUR = json[0].tasaEUR;
		},
		complete: function (json){
			$("#tasaUSD").val(tasaUSD);
			$("#tasaEUR").val(tasaEUR);
		},
		error: function(x, t, m) {
			if(t==="timeout"){
				obtener_tasa();
			}
		}
	});
}

// Habilitar botones de guardado
function habilitaGuardado(){
	if($("#motive").val() != ""){
		$("#guardarSol").removeAttr("disabled");
		$("#guardarprevSol").removeAttr("disabled");
	}else{
		$("#guardarSol").attr("disabled", "disable");
		$("#guardarprevSol").attr("disabled", "disable");
	}
	
	$.unblockUI();
}

//Seleccionar elemento de un combo
function seleccionar(elemento){
	var combo = document.sgastos.ccentro_costos;
	var cantidad = combo.length;
	for (var i = 1; i < cantidad; i++) {
		var toks=combo[i].text.split(" ");
		if (toks[0] == elemento) {
			combo[i].selected = true;
			break;
		}
	}
}

function seleccionarXCampo(campo, elemento){
	alert(campo);
	var combo = document.sgastos.campo;
	var cantidad = $("#" + campo + " option").length;
	for (var i = 1; i < cantidad; i++) {
		var toks=combo[i].text.split(" ");
		if (toks[0] == elemento) {
			combo[i].selected = true;
			break;
		}
	}
}

// Confirmaci髇 de Guardado Previo
function solicitarConfirmPrevio(){
	var frm=document.detallesItinerarios;
	$("#divisa_solicitud").val(1);
	if(confirm("緿esea guardar esta Solicitud como previo?")){
		$("#sgastos").attr("action", "solicitud_gastos.php?guardarprevSol=guardarprevSol");
		$("#sgastos").submit();
	}else{
		return false;
	}
}

function validaCamposComidaRepresentacion(){
	if($("#lugar").val() == ""){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
		$("#lugar").focus();
		return false;
	}else if($("#ciudad").val() == ""){
        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
        $("#ciudad").focus();
        return false;
	}else{
		return true;
	}
}

function validaCamposDetalle(){
	if($("#monto_solicitado").val() == 0){
        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
        $("#monto_solicitado").focus();
        return false;
    }else if($("#divisa_solicitud").val() == -1){
        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
        $("#divisa_solicitud").focus();
        return false;
    }else if($("#ccentro_costos").val() == -1){
        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
        $("#ccentro_costos").focus();
        return false;
    }else if($("#observ").val().length == 0 ){
    	alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
    	$("#observ").focus();
    	return false;
    }else{
	   $("#guardarSol").attr("disabled", "disable");
    	verificaExcepcion();
    	obtenPartidasExcepciones();
    }
}

function validaCampos(){
	$("#divisa_solicitud").val(1);
	
	if($("#motive").val() == ""){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
		$("#motive").focus();
		return false;
	}else if($("#sg_concepto").val() == -1){
        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
        $("#sg_concepto").focus();
        return false;
	}else if($("#sg_concepto option:selected").val() == valorComidasRepresentacion){
		if(validaCamposComidaRepresentacion()){
			validaCamposDetalle();
		}else{
			return false;
		}
	}else{
		validaCamposDetalle();
	}
}

function redondea(valor){
	return (Math.round(valor * Math.pow(10, 2)) / Math.pow(10, 2));
}

function recalculaMontos(){
    var anticipo = parseFloat(($("#monto_solicitado").val()).replace(/,/g,""));
	
    var totalAnticipo = 0;
	var divisas = parseInt($("#divisa_solicitud").val());

	var tasaNueva = 0;
	switch(divisas){
		case 2:
			totalAnticipo = anticipo * parseFloat($("#tasaUSD").val());
			break;
		case 3:
			totalAnticipo = anticipo * parseFloat($("#tasaEUR").val());
			break;
		default:
			totalAnticipo = anticipo * 1;
			break;
	}
	
	$("#tpesos").val(number_format(redondea(totalAnticipo),2,".",",")); // Redondea a 2 decimales
    	
}

function cumpleReglas(simpleTexto){
	// La pasamos por una poderosa expresi髇 regular
	var expresion = new RegExp("^(|([0-9]{1,30}(\\.([0-9]{1,2})?)?))$");
	// Si pasa la prueba, es v醠ida
	if(expresion.test(simpleTexto))
		return true;
	return false;
} // End function checaReglas

function revisaCadena(textItem){
	// Si comienza con un punto, le agregamos un cero
	if(textItem.value.substring(0,1) == '.') 
		textItem.value = '0' + textItem.value;

	// Si no cumples las reglas, no te dejo escribir
	if(!cumpleReglas(textItem.value))
		textItem.value = textoAnterior;
	else //todo en orden
		textoAnterior = textItem.value;
} // end function revisaCadena

// Funcions para eliminar y cargar invitados
function limpiarTabla(){
	var tabla = document.getElementById("invitado_table");
	for(var i=tabla.rows.length-1;i>=1;i--){
		tabla.deleteRow(i);
	}
}

function LlenarTabla(json){
	$("#rowCount").val(parseInt(0));
	
	for(var i=0;i<json.length;i++){					
		var toks=json[i].split(":");
		
		//Creamos la nueva fila y sus respectivas columnas
		var nuevaFila='<tr>';
		nuevaFila+="<td>"+"<div id='renglon"+(i+1)+"' name='renglon"+(i+1)+"'>"+(i+1)+"</div>"+"<input type='hidden' name='row"+(i+1)+"' id='row"+(i+1)+"' value='"+(i+1)+"' readonly='readonly' /></td>";
		nuevaFila+="<td><input type='hidden' name='nombre"+(i+1)+"' id='nombre"+(i+1)+"' value='"+toks[0]+"' readonly='readonly' />"+toks[0]+"</td>";
		nuevaFila+="<td><input type='hidden' name='puesto"+(i+1)+"' id='puesto"+(i+1)+"' value='"+toks[1]+"' readonly='readonly' />"+toks[1]+"</td>";
		nuevaFila+="<td><input type='hidden' name='empresa"+(i+1)+"' id='empresa"+(i+1)+"' value='"+toks[2]+"' readonly='readonly' />"+toks[2]+"</td>";
		nuevaFila+="<td ><input type='hidden' name='tipo"+(i+1)+"' id='tipo"+(i+1)+"' value='"+toks[3]+"' readonly='readonly' />"+toks[3]+"</td>";
		nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+(i+1)+"del' id='"+(i+1)+"del' onmousedown='borrarPartida(this.id);' style='cursor:pointer;' /></div><div align='center'>Eliminar Partida</div></td>";
		nuevaFila+= '</tr>';
		$("#rowCount").val(parseInt($("#rowCount").val()) + parseInt(1));
		$("#invitado_table").append(nuevaFila);
	}
	
	$("#numInvitados").val(parseInt($("#rowCount").val()));
}

function fillform(tramite_id){
	// Bloquear la pantalla, para completar la carga de la informaci髇 de la Solicitud
	$.blockUI({
		message: '<h1>Espere un momento...</h1>',
		css:{
			border: 'none', 
			padding: '15px', 
			backgroundColor: '#000', 
			'-webkit-border-radius': '10px', 
			'-moz-border-radius': '10px', 
			opacity: .5, 
			color: '#fff'
		}
	});
	
	var motivo = "";
	var fechaGasto = "";
	var lugarGasto = "";
	var montoSolicitado = 0;
	var divisa = -1;
	var ciudad = "";
	var ceco = -1;
	var observaciones = "";
	var observaciones_edicion = '';
	var t_etapa_actual = 0;
	var req_anticipo = 0;
	var concepto = 0;
	var montoenPesos = 0;
	
	$.ajax({
		type: "POST",
		url: "services/ajax_solicitudes_gastos.php",
		data: "noSolicitud="+tramite_id,
		dataType: "json",
		timeout: 10000,
		async: false,
		success: function(json){
			motivo = json[0].motivo;
			fechaGasto = json[0].fecha_gasto;
			lugarGasto = json[0].lugar;
			montoSolicitado = json[0].monto;
			divisa = json[0].divisa;
			ciudad = json[0].ciudad;
			ceco = json[0].ceco;
			observaciones = json[0].observaciones;
			observaciones_edicion = json[0].observaciones_edicion;
			t_etapa_actual = json[0].etapaActual;
			req_anticipo = json[0].requiere_anticipo;
			concepto = json[0].concepto;
			montoenPesos = json[0].montoPesos;
		},
		complete: function (json){
			// Asignar los datos a los campos de texto
			$("#motive").val(motivo);
			$("#fecha_sol").val(fechaGasto);
			$("#lugar").val(lugarGasto);
			$("#monto_solicitado").val(montoSolicitado);
			$("#tpesos").val(montoenPesos);
			$("#ciudad").val(ciudad);
			$("#historial_observaciones").val(observaciones);
			$("#observ").val(observaciones_edicion);
			
			// Seleccionar la Divisa
			$("#divisa_solicitud").val(divisa);
			
			// Seleccionar CECO de la Solicitud
			seleccionar(ceco);
			
			// Seleccionar el check de Anticipo
			if(req_anticipo == 1){
				$("#reqAnticipo").attr("checked", true);
			}else{
				$("#reqAnticipo").attr("checked", false);
			}
			
			// Seleccionar el concepto guardado
			$("#sg_concepto").val(concepto);
			verificaConcepto();
			
			// Cargar Invitados
			if(concepto == valorComidasRepresentacion){
				limpiarTabla();
				cargarInvitados();
			}
			
			habilitaGuardado();
		},
		error: function(x, t, m) {
			if(t==="timeout"){
				location.reload();
			}
		}
	});
}

function cargarInvitados(){
	var tramite = $("#tramiteId").val();
	var arregloInvitados = Array();
	
	$.ajax({
		type: "POST",
		url: "services/ajax_solicitudes_gastos.php",
		data: "idTramite="+tramite,
		dataType: "json",
		timeout: 10000,
		async: false,
		success: function(json){
			arregloInvitados = json;
		},
		complete: function (json){
			LlenarTabla(arregloInvitados);
		},
		error: function(x, t, m) {
			if(t==="timeout"){
				cargarInvitados();
			}
		}
	});
}

function cargaDatosUsuarioSesion(){
	var idUsuario = $("#idusuario").val();
	var arregloUsuario = Array();
	
	$.ajax({
		type: "POST",
		url: "services/ajax_solicitudes_gastos.php",
		data: "idusuario="+idUsuario,
		dataType: "json",
		timeout: 10000,
		async: false,
		success: function(json){
			arregloUsuario = json;
		},
		complete: function (json){
			LlenarTabla(arregloUsuario);
		},
		error: function(x, t, m) {
			if(t==="timeout"){
				cargaDatosUsuarioSesion();
			}
		}
	});
}

// Mosrar u ocultar los campos de Lugar y Ciudad si y solo si el concepto es Comidas de Representaci髇
function verificaConcepto(){
	var concepto = $("#sg_concepto option:selected").val();
	var conceptoTexto = $("#sg_concepto option:selected").text();
	
	if(concepto == valorComidasRepresentacion){
		//alert("Concepto: " + conceptoTexto + " mostrando campos extras.....");
		$("#lugar_solicitud_etiqueta").css("display","block");
		$("#lugar_solicitud_campo").css("display","block");
		$("#ciudad_solicitud_etiqueta").css("display","block");
		$("#ciudad_solicitud_campo").css("display","block");
		$("#seccion_inivitados").css("display","block");
	}else{
		//alert("Concepto: " + conceptoTexto);
		$("#lugar_solicitud_etiqueta").css("display","none");
		$("#lugar_solicitud_campo").css("display","none");
		$("#ciudad_solicitud_etiqueta").css("display","none");
		$("#ciudad_solicitud_campo").css("display","none");
		$("#seccion_inivitados").css("display","none");
		
		// Restablecemos los campos
		$("#lugar").val("");
		$("#ciudad").val("");
		
		// Eliminamos los invitados
		limpiarTabla();
		cargaDatosUsuarioSesion();
	}
}

// Funciones para agregar invitados
function verificarDirector(){
	var idusuario = $("#idusuario").val();
	var esDirector = "";
	
	$.ajax({
		type: "POST",
		url: "services/ajax_solicitudes_gastos.php",
		data: "idUsuario="+idusuario,
		dataType: "html",
		timeout: 10000,
		async: false,
		success: function(json){
			esDirector = json;
		},
		complete: function (json){
			return esDirector;
		},
		error: function(x, t, m) {
			if(t==="timeout"){
				verificarDirector();
			}
		}
	});
}

function verificar_tipo_invitado(){
	var esDirector = verificarDirector();
	
	if($("#tipo_invitado").val()=="-1"){
		$("#empresa_invitado").val("");
		$("#capaDirector").html("");
		$("#empresa_invitado").attr("disabled", "disable");
    }else{    
	    if($("#tipo_invitado").val()=="Interno"){
	    	var usuario = $("#idusuario").val();
			var param = "empresaUsuario=ok&usuario="+usuario;
			var json = obtenJson(param);
			asignaVal("empresa_invitado", json['e_nombre']);
			deshabilitaElemento("empresa_invitado");
	        $("#capaDirector").html("");
	    } else if ($("#tipo_invitado").val() == "Gobierno" && !esDirector){
	    	$("#empresa_invitado").val("");
	        $("#empresa_invitado").removeAttr("disabled");
			$("#capaDirector").html("<strong>La solicitud requerir&aacute; ser validada por el Dir. General</strong>");                    
	    }else{
	        $("#empresa_invitado").val("");
	        $("#capaDirector").html("");
	        $("#empresa_invitado").removeAttr("disabled");
	    }
	}
}

function verificaCamposInvitados(){
	if($("#lugar").val() == ""){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
		$("#lugar").focus();
	}else if($("#ciudad").val() == ""){
        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
        $("#ciudad").focus();
        return false;
	}else if($("#nombre_invitado").val() == ""){
		alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
		$("#nombre_invitado").focus();
	}else if($("#tipo_invitado").val() == -1){
        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
        $("#tipo_invitado").focus();
        return false;
	}else if($("#puesto_invitado").val() == ""){
        alert("Los campos con (*) son obligatorios. Favor de llenar los datos faltantes.");
        $("#puesto_invitado").focus();
        return false;
	}else{
		return true;
	}
}

function agregarInvitado(){
    id = parseInt($("#invitado_table").find("tr:last").find("div").eq(0).html());
    
    if(verificaCamposInvitados()){
        if(isNaN(id)){
            id=1;
        }else{
            id+=parseInt(1);
        }
        
        $("#rowCount").val(parseInt($("#rowCount").val()) + parseInt(1));
        
        var nuevaFila='<tr>';
        nuevaFila+="<td>"+"<div id='renglon"+id+"' name='renglon"+id+"'>"+id+"</div>"+"<input type='hidden' name='row"+id+"' id='row"+id+"' value='"+id+"' readonly='readonly' /></td>";
        nuevaFila+="<td><input type='hidden' name='nombre"+id+"' id='nombre"+id+"' value='"+$("#nombre_invitado").val()+"' readonly='readonly' />"+$("#nombre_invitado").val()+"</td>";
        nuevaFila+="<td><input type='hidden' name='puesto"+id+"' id='puesto"+id+"' value='"+$("#puesto_invitado").val()+"' readonly='readonly' />"+$("#puesto_invitado").val()+"</td>";
        nuevaFila+="<td><input type='hidden' name='empresa"+id+"' id='empresa"+id+"' value='"+$("#empresa_invitado").val()+"' readonly='readonly' />"+$("#empresa_invitado").val()+"</td>";
        nuevaFila+="<td><input type='hidden' name='tipo"+id+"' id='tipo"+id+"' value='"+$("#tipo_invitado").val()+"' readonly='readonly' />"+$("#tipo_invitado").val()+"</td>";
        nuevaFila+="<td><div align='center'><img class='elimina' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+id+"del' id='"+id+"del' onmousedown='borrarPartida(this.id);' style='cursor:pointer;' /></div><div align='center'>Eliminar Partida</div></td>";
        nuevaFila+= '</tr>';
        
        $("#invitado_table").append(nuevaFila);
        $("#numInvitados").val(parseInt($("#rowCount").val()));
        $("#nombre_invitado").val("");
        $("#puesto_invitado").val("");
        $("#empresa_invitado").val("");
        $("#tipo_invitado").val("-1");
        $("#guardarComp").removeAttr("disabled");
        $("#guardarCompprev").removeAttr("disabled");
		$("#capaDirector").html("");
		document.getElementById("empresa_invitado").disabled="disable";
    }
}

function borrarPartida(id){
	var no_partidas = parseInt($("#invitado_table>tbody>tr").length);
	
	// Quitamos el registro de Invitado
	borrarRenglon(id, "invitado_table", "rowCount", 0,"renglon", "edit", "del", "");
	$("#numInvitados").val(parseInt(no_partidas - 1));
	$("#rowCount").val(parseInt(no_partidas - 1));
}
