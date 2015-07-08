var valorComidasRepresentacion = 7;

// Función para restablecer las opciones de las lista de conceptos
function restablecerOpciones(){
	var opciones = "";
	// Eliminar las opciones del combo de conceptos
	$("#select_concepto").empty();
	
	$.ajax({
		type: "POST",
		url: "services/Ajax_comprobacion_gastos.php",
		data: "obtenerConceptos=1",
		dataType: "json",
		timeout: 10000,
		async: false,
		success: function(json){
			opciones = json;
		},
		complete:function(json){
			$("#select_concepto").html(opciones);
		},
		error: function(x, t, m){
			if(t==="timeout") {
				restablecerOpciones();
			} 
		 }
	});
}

// Funciones para Agregar invitados
function verificarDirector(){
	var idusuario = $("#idusuario").val();
	var esDirector = "";
	
	$.ajax({
		type: "POST",
		url: "../solicitudes/services/ajax_solicitudes_gastos.php",
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
        
        $("#numInvitados").val(parseInt($("#numInvitados").val()) + parseInt(1));
        
        var nuevaFila='<tr>';
        nuevaFila+="<td>"+"<div id='renglon"+id+"' name='renglon"+id+"'>"+id+"</div>"+"<input type='hidden' name='row"+id+"' id='row"+id+"' value='"+id+"' readonly='readonly' /></td>";
        nuevaFila+="<td><input type='hidden' name='nombre"+id+"' id='nombre"+id+"' value='"+$("#nombre_invitado").val()+"' readonly='readonly' />"+$("#nombre_invitado").val()+"</td>";
        nuevaFila+="<td><input type='hidden' name='puesto"+id+"' id='puesto"+id+"' value='"+$("#puesto_invitado").val()+"' readonly='readonly' />"+$("#puesto_invitado").val()+"</td>";
        nuevaFila+="<td><input type='hidden' name='empresa"+id+"' id='empresa"+id+"' value='"+$("#empresa_invitado").val()+"' readonly='readonly' />"+$("#empresa_invitado").val()+"</td>";
        nuevaFila+="<td><input type='hidden' name='tipo"+id+"' id='tipo"+id+"' value='"+$("#tipo_invitado").val()+"' readonly='readonly' />"+$("#tipo_invitado").val()+"</td>";
        nuevaFila+="<td><div align='center'><img class='eliminaI' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+id+"del' id='"+id+"del' onmousedown='borrarPartidaInvitados(this.id);' style='cursor:pointer;' /></div><div align='center'>Eliminar Partida</div></td>";
        nuevaFila+= '</tr>';
        
        $("#invitado_table").append(nuevaFila);
        $("#numInvitados").val(parseInt($("#numInvitados").val()));
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

function borrarPartidaInvitados(id){
	var no_partidas = parseInt($("#invitado_table>tbody>tr").length);
	// Quitamos el registro de Invitado
	borrarRenglon(id, "invitado_table", "numInvitados", 0,"renglon", "edit", "del", "");
}

//Funcion para eliminar y cargar invitados
function limpiarTabla(){
	var tabla = document.getElementById("invitado_table");
	for(var i=tabla.rows.length-1;i>=1;i--){
		tabla.deleteRow(i);
	}
}

function LlenarTabla(json){
	$("#numInvitados").val(parseInt(0));
	
	for(var i=0;i<json.length;i++){					
		var toks=json[i].split(":");
		
		//Creamos la nueva fila y sus respectivas columnas
		var nuevaFila='<tr>';
		nuevaFila+="<td>"+"<div id='renglon"+(i+1)+"' name='renglon"+(i+1)+"'>"+(i+1)+"</div>"+"<input type='hidden' name='row"+(i+1)+"' id='row"+(i+1)+"' value='"+(i+1)+"' readonly='readonly' /></td>";
		nuevaFila+="<td><input type='hidden' name='nombre"+(i+1)+"' id='nombre"+(i+1)+"' value='"+toks[0]+"' readonly='readonly' />"+toks[0]+"</td>";
		nuevaFila+="<td><input type='hidden' name='puesto"+(i+1)+"' id='puesto"+(i+1)+"' value='"+toks[1]+"' readonly='readonly' />"+toks[1]+"</td>";
		nuevaFila+="<td><input type='hidden' name='empresa"+(i+1)+"' id='empresa"+(i+1)+"' value='"+toks[2]+"' readonly='readonly' />"+toks[2]+"</td>";
		nuevaFila+="<td ><input type='hidden' name='tipo"+(i+1)+"' id='tipo"+(i+1)+"' value='"+toks[3]+"' readonly='readonly' />"+toks[3]+"</td>";
		nuevaFila+="<td><div align='center'><img class='eliminaI' src='../../images/delete.gif' alt='Click aqu&iacute; para Eliminar' name='"+(i+1)+"del' id='"+(i+1)+"del' onmousedown='borrarPartidaInvitados(this.id);' style='cursor:pointer;' /></div><div align='center'>Eliminar Partida</div></td>";
		nuevaFila+= '</tr>';
		$("#numInvitados").val(parseInt($("#numInvitados").val()) + parseInt(1));
		$("#invitado_table").append(nuevaFila);
	}
}

function cargaDatosUsuarioSesion(){
	var idUsuario = $("#iu").val();
	var arregloUsuario = Array();
	
	$.ajax({
		type: "POST",
		url: "../solicitudes/services/ajax_solicitudes_gastos.php",
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

function cargarInvitadosTramite(origen){ 
	/* origen => 0, cuando proviene del evento onchange de la lista de conceptos
	 * origen => 1, cuando estamos cargando el previo de una comprocación
	 */
	var arregloInvitados = Array();
	var tramite = 0;
	var url = "";
	
	if(origen){
		tramite = $("#tramiteComprobacion").val();
		url = "services/Ajax_comprobacion_gastos.php";
	}else{
		tramite = $("#sol_select").val();
		url = "../solicitudes/services/ajax_solicitudes_gastos.php";
	}
	
	$.ajax({
		type: "POST",
		url: url,
		data: "idTramite="+tramite,
		dataType: "json",
		timeout: 10000,
		async: false,
		success: function(json){
			arregloInvitados = json;
			limpiarTabla();
		},
		complete: function (json){
			cargaSolicitud();
			
			if(arregloInvitados.length > 0){
				LlenarTabla(arregloInvitados);
			}else{
				cargaDatosUsuarioSesion();
			}
			
		},
		error: function(x, t, m) {
			if(t==="timeout"){
				cargarInvitados();
			}
		}
	});
}

//Funciones auxiliares
function buscaConceptoComidasRepresentacion(){
	var i = 0;
	var numFilas = parseInt($("#comprobacion_table>tbody >tr").length);
	var existeComidaRepresentacion = false;
	
	// Recorremos la tabla para saber si ya ingresamos el concepto de comidas de representación
	for(i = 0; i <= numFilas; i++){
		if($("#numConcepto"+i).val() == valorComidasRepresentacion){
			existeComidaRepresentacion = true;
		}
	}
	
	return existeComidaRepresentacion;
}

function restablececamposComidasRepresentacion(){
	$("#seccion_inivitados").css("display","none");
	
	if(!buscaConceptoComidasRepresentacion()){
		// Restablecemos los campos
		$("#lugar").val("");
		$("#ciudad").val("");
		
		// Eliminamos los invitados
		limpiarTabla();
		cargaDatosUsuarioSesion();
	}
}