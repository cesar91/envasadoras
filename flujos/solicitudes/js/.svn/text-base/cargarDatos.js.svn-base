/**
 * Funcion para bloquear la pantalla
 * @param accion
 */
function blockUI(accion){
	if(accion == true){		
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
	}else
		$.unblockUI();	
}

/**
 * Devuelve la fecha actual
 *
 * @return string => Devuelve un string con la fecha actual con el formato dd/mm/yyyy
 */
function obtenFecha(){
	var date = new Date();
	var anho = date.getUTCFullYear();
	var mes = date.getMonth()+1;
	var dia = date.getDate();		
	mes = (mes < 10) ? "0"+mes : mes;
	dia = (dia < 10) ? "0"+dia : dia;
	
	return (dia+"/"+mes+"/"+anho);
}
	
/**
 * Asigna un valor a un input especificado
 *
 * @param campo string		=> El id del campo al cual se asignara el valor
 * @param valor string		=> El valor que se asignara al campo
 * @param formato string	=> Si viene con un valor "number", formateara el valor 
 */		
function asignaVal(campo, valor, formato){
	if(formato == "number" ){
		$('#'+campo).priceFormat({
			prefix: '', centsSeparator: '.', thousandsSeparator: ','
		});
	}
	$("#"+campo).val(valor);
};

/**
 * Agrega el atributo disabled a elementos html definidos o dentro de un contexto segun los parametros
 *
 * @param elemento string	=> Si se recibe solo este parametro (id del elemento) deshabilitará el elemento
 *							   especificado, de lo contrario deshabilitara todos	 
 */	
function deshabilitaElemento(elemento){
	if(elemento == undefined){
		var contexto = $("#Layer1");
		$(":input", contexto).each(function(){
			$(this).attr("disabled",true);
		});		
	}else
		$("#"+elemento).attr("disabled",true);
}

/**
 * Remueve el atributo disabled a elementos html definidos o dentro de un contexto segun los parametros
 *
 * @param elemento string	=> Si se recibe solo este parametro (id del elemento) habilitará el elemento
 *							   especificado, de lo contrario habilitará todos	 
 */		
function habilitaElemento(elemento){
	if(elemento == undefined){		
		var contexto = $("#Layer1");
		$(":input", contexto).each(function(){
			$(this).removeAttr("disabled");
		});		
	}else
		$("#"+elemento).removeAttr("disabled");
}

/**
 * Limpia un numero de "$" y ","
 *
 * @param valor string	=> El valor que se desea parsear
 * @return float		=> El vvalor limpio de caracteres no numericos
 */		
function limpiaCantidad(valor){
	if(valor == "" || valor == undefined)
		valor = 0;
	valor = String(valor).replace(",","");
	valor = String(valor).replace("$","");
	valor = String(valor).replace(/\D/g,"");
	valor = String(valor).replace("_","");
	return parseFloat(valor);
}

/**
 * Realiza una llamada AJAX al servidor
 *
 * @param param string	=> Recibe una cadena formada con los parametros de busqueda
 * @return json			=> Devuelve la respuesta del Servidor en formato JSON
 */			
function obtenJson(param){	
	blockUI(true);
	var response = false;
	$.ajax({
		data: param, 
		async: false,			
		success: function(json){				
			try{
				response = json;
			}catch(e){
				alert("Ha ocurrido un error Inesperado :" + e )
			}				
		},			
		error: function(error){
//			console.log(error);
//			console.log(param);
//			console.log($(this));
			blockUI(false);
		},
		complete: function(){
			blockUI(false);
		}
	});
	return response;
}

/**
 * Devuelve la cantidad de atributos que tiene un objeto
 *
 * @param object Object	=> Recibe una objeto
 * @return int			=> Devuelve la cantidad de atributos que tiene el objeto
 */		
function Objectlength(object){
	var cont = 0;
	for(var prop in object)
		cont++;		
	return cont;
}

/**
 * Devuelve la cantidad de filas de una tabla
 *
 * @param tabla Object	=> Recibe el nombre de la tabla
 * @return int			=> Devuelve el numero de filas de la tabla
 */		
function obtenTablaLength(tabla){	
	return parseInt($("#"+tabla+">tbody>tr").length);
}

/**
 * Muestra un elemento html
 *
 * @param elemento string	=> Recibe el id del elemento al que se mostrara	 
 */	
function mostrarElemento(elemento){
	$("#"+elemento).fadeIn("slow");
}

/**
 * Oculta algun elemento html, al ocultarlos tambien resetea los campos dentro del contexto definido/creado
 *
 * @param elemento string	=> Recibe el id del elemento que se desea ocultar, tipocamente un div, table, tr o td
 * @param efectoHide string	=> Opcional, si viene este valor, el efecto para esconder el elemento será 
 *							   .hide(), de lo contrario será .fadeOut("slow")
 */	
function ocultarElemento(elemento, efectoHide){
	var contexto = $("#"+elemento);				
	if(efectoHide == undefined)
		contexto.fadeOut("slow");		
	else
		contexto.hide();				
	resetCampos(contexto);		
}

/**
 * Resete todos los elementos input, divs, ademas que para los input remueve la class "req"
 *
 * @param contexto jquery	=> Recibe un objeto jquery, dentro del cual tomaran efecto los cambios
 */	
function resetCampos(contexto){
	$(":input", contexto).each(function(){
		$(this).val("").removeClass("req");
	});		
	$("div", contexto).each(function(){
		$(this).text("");
	});				
}

/**
 * Obtiene el total de las partidas generadas en esta solicitud
 */
function obtenPartidasExcepciones(){
	var tablaLength = obtenTablaLength("excepcion_table");
	asignaVal("totalExcepciones", tablaLength);
	$("#sgastos").attr("action", "solicitud_gastos.php?guardarSol=guardarSol");
	$("#sgastos").submit();
}

/**
 * Genera un objeto a partir del formulario, donde los atributos son los inputs, verifica los valores y cuando 
 * sean vacios o 0, asigna un "N/A"
 *
 * @return object	=> Objeto creado en base a el formulario
 */	
function generaObjetoFormulario(){
	var requiereAnticipo = 0;
	var requiereAnticipoTexto = "No";
	
	if($("#reqAnticipo").is(':checked')){
		requiereAnticipo = 1;
		requiereAnticipoTexto = "Si";
	}
		
	var objetoFormulario = {
		"fecha":					$("#fecha_sol").val(),
		"concepto":					$("#sg_concepto").val(),
		"conceptoTexto":			$("#sg_concepto option:selected").text(),
		"montoSolicitado":			$("#monto_solicitado").val(),
		"divisa":					$("#divisa_solicitud").val(),
		"divisaTexto":				$("#divisa_solicitud option:selected").text(),
		"totalPartida":				$("#tpesos").val(),
		"requiereAnticipo":			requiereAnticipo,
		"requiereAnticipoTexto":	requiereAnticipoTexto,
		"observaciones": 			$("#observ").val()
	}
	
	for(var prop in objetoFormulario)
		objetoFormulario[prop] = (objetoFormulario[prop] == "" || objetoFormulario[prop] == null) ? "N/A" :  objetoFormulario[prop];
	
	return objetoFormulario;
}

/**
 * Crea la fila que contendra los datos de la excepci�n del concepto
 * @param Excepcion Object 	=> Objeto que contiene los elementos de la partida
 * @param id int			=> Id identificador d ela fila
 * @returns {String} 		=> Cadena para formar la fila en la tabla de excepciones
 */
function crearRenglonExcepcion(Excepcion, id){
	var renglon = "";
	renglon+= '<tr id="tr_'+id+'">'
	renglon+= 	'<td><input id="e_row'+id+'" name="e_row'+id+'" value="'+id+'" readonly="readonly" /></td>';
	renglon+= 	'<td><input id="e_row_concepto'+id+'" name="e_row_concepto'+id+'" value="'+Excepcion.concepto+'" readonly="readonly" /></td>';
	renglon+= 	'<td><input id="e_row_mensaje'+id+'" name="e_row_mensaje'+id+'" value="'+Excepcion.mensaje+'" readonly="readonly" /></td>';
	renglon+= 	'<td><input id="e_row_fecha'+id+'" name="e_row_fecha'+id+'" value="'+Excepcion.fecha+'" readonly="readonly" /></td>';
	renglon+= 	'<td><input id="e_row_referencia'+id+'" name="e_row_referencia'+id+'" value="'+Excepcion.referencia+'" readonly="readonly" /></td>';
	renglon+= 	'<td><input id="e_row_totalPartida'+id+'" name="e_row_totalPartida'+id+'" value="'+Excepcion.totalPartida+'" readonly="readonly" /></td>';
	renglon+= 	'<td><input id="e_row_diferencia'+id+'" name="e_row_diferencia'+id+'" value="'+Excepcion.excedente+'" readonly="readonly" /></td>';
	renglon+= '</tr>';		
	return renglon;		
}

/**
 * Agrega un renglon a la tabla de partidas
 */	
function agregaRenglon(renglon, tabla){
	$("#"+tabla).append(renglon);
}

/**
 * Funcion para traer las Excepciones generadas en la Solicitud
 * @param tramite int	=> Id del tramite para llamar sus Excepciones
 * @returns json		=> Devuelve las excepciones de la Solicitud
 */
function cargarExcepciones(tramite){
	var param = "cargarExcepciones=ok&tramite="+tramite;
	var tr = "";
	var json = obtenJson(param);
	
	if (json == null){
		ocultarElemento("row_ExcepcionesSolcitud","hide");
	}else{
		json = json.rows;
		for(var i = 1; i <= Objectlength(json); i++ )
			tr+= '<tr><td>'+json[i]["concepto"]+'</td><td>'+json[i]["mensaje"]+'</td><td>'+json[i]["excedente"]+'</td><td>'+json[i]["tipoExcepcion"]+'</td></tr>';			
		$("#excepcion_table").append(tr);
	}
}

function obtenExcepcionesPresupuesto(tramite){
	var param = "excepcionesPresupuesto=ok&tramite="+tramite;
	var tr = "";
	var json = obtenJson(param);
	
	if (json != null){
		mostrarElemento("row_ExcepcionesSolcitud", "show");
		tr += '<tr><td><font color="FF0000">Presupuesto</font></td><td><strong><font color="FF0000">'+json["mensaje"]+'</font></strong></td><td><font color="FF0000">'+json["excedente"]+'</font></td><td>'+json["tipoExcepcion"]+'</td></tr>';
		$("#excepcion_table").append(tr);
	}
}

/**
 * Redireccionar a Index
 */
function Location(url){
	location.href = url;
}

/**
 *  Imprimir Reporte PDf de una Solicitud
 * @param tramite int => Id del tramite para enviar a impresion en PDF
 * @param url => Archivo requerido para generar el Reporte
 */
function imprimir_pdf(tramite, url){
	window.open(url+"?id="+tramite,"imprimir");
}

