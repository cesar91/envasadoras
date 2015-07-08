var valorComidasRepresentacion = 7;

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

// Confirmación de Guardado Previo
function solicitarConfirmPrevio(){
	var frm=document.detallesItinerarios;
	if(confirm("¿Desea guardar esta Solicitud como previo?")){
		
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
    	return true;
    }
}

function validaCampos(){
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
			return validaCamposDetalle();
		}else{
			return false;
		}
	}else{
		return validaCamposDetalle();
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
	// La pasamos por una poderosa expresión regular
	var expresion = new RegExp("^(|([0-9]{1,30}(\\.([0-9]{1,2})?)?))$");
	// Si pasa la prueba, es válida
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
	// Bloquear la pantalla, para completar la carga de la información de la Solicitud
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

// Mosrar u ocultar los campos de Lugar y Ciudad si y solo si el concepto es Comidas de Representación
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
	        $("#empresa_invitado").val("Interno");
	        $("#capaDirector").html("");
	        $("#empresa_invitado").attr("disabled", "disable");
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

function validaZeroIzquierda(monto,campo){			
	if( monto.substring(monto.length-1,monto.length) === "." || monto.substring(monto.length-2,monto.length) === ".0" ){
		$("#"+campo).val(monto);
	}else if(monto == 0 || monto == "" || monto == "NaN"){
		$("#"+campo).val(0);
	}else{
		$("#"+campo).val(parseFloat(monto));
	}
}