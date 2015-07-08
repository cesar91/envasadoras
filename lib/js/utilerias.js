// Función para evitar que el usuario introduzca caracteres inválidos en los campos. 
function isAlphaNumeric(event){
	if(event.ctrlKey){
		ignoraEventKey(event);
	}else if(event.altKey){
		alert("Lo sentimos, esta tecla se encuentra deshabilitada.");
		ignoraEventKey(event);
	}else if( // Teclas que serán permitidas
			(event.keyCode > 36 && event.keyCode < 41) // Teclas de Navegación
    		|| (event.keyCode > 47 && event.keyCode < 60) // Números del 0 - 9, caracteres :, ; 
    		|| (event.keyCode > 63 && event.keyCode < 91) // Caracter @, alfabeto en Mayúsculas 
    		|| (event.keyCode > 95 && event.keyCode <= 105) // Alfabeto en minúsculas (No se permite */-+.)teclado numerico
    		|| event.keyCode == 8 // Tecla backspace 
    		|| event.keyCode == 32 // Tecla Barra de Espacio 
    		|| event.keyCode == 35 // Tecla # 
    		|| event.keyCode == 35 // Tecla $ 
    		|| event.keyCode == 164 // Tecla ñ
    		|| event.keyCode == 192 // Tecla Ñ
    		|| event.keyCode == 173 // Tecla -
    		|| event.keyCode == 186 // Tecla ´
    		|| event.keyCode == 188 // Tecla ,
    		|| event.keyCode == 190 // Tecla .
    		|| event.keyCode == 110 // Tecla . (teclado numérico)
    		|| event.keyCode == 9 // Tecla tabulador.
    		|| event.keyCode == 46 // Tecla supr.)
    ){
		return true;
	}
}

//Función para evitar que el usuario introduzca caracteres inválidos en los campos. 
function isNumeric(event){
	if(event.ctrlKey){
		ignoraEventKey(event);
	}else if(event.shiftKey){
		ignoraEventKey(event);
	}else if(event.altKey){
		alert("Lo sentimos, esta tecla se encuentra deshabilitada.");
		ignoraEventKey(event);
	}else if( // Teclas que serán permitidas
			(event.keyCode > 34 && event.keyCode < 41) // Teclas de Navegación
    		|| (event.keyCode > 47 && event.keyCode < 60) // Números del 0 - 9, caracteres :, ;
    		|| (event.keyCode > 95 && event.keyCode <= 105) // Reconocimiento de teclado númerico
    		|| event.keyCode == 8 // Tecla backspace
    		|| event.keyCode == 188 // Tecla ,
    		|| event.keyCode == 190 // Tecla .
    		|| event.keyCode == 110 // Tecla . (teclado numérico)
    		|| event.keyCode == 9 // Tecla tabulador.
    		|| event.keyCode == 46 // Tecla supr.
    ){
		return true;
	}
}

function isAlphaNumericRFC(event){
	if(event.ctrlKey){
		ignoraEventKey(event);	
	}else if(event.shiftKey){
		ignoraEventKey(event);
	}else if(event.altKey){
		ignoraEventKey(event);
	}else if( // Teclas que serán permitidas
			(event.keyCode > 36 && event.keyCode < 41) // Teclas de Navegación
    		|| (event.keyCode > 47 && event.keyCode < 60) // Números del 0 - 9, caracteres :, ; 
    		|| (event.keyCode > 63 && event.keyCode < 91) // Caracter @, alfabet en Mayúsculas 
    		|| (event.keyCode > 95 && event.keyCode <= 105) // Alfabeto en minúsculas (No se permite */-+.) reconocimiento de teclado numerico
    		|| event.keyCode == 8 // Tecla backspace 
    		|| event.keyCode == 32 // Tecla Barra de Espacio 
    		|| event.keyCode == 35 // Tecla # 
    		|| event.keyCode == 35 // Tecla $ 
    		|| event.keyCode == 164 // Tecla ñ
    		|| event.keyCode == 192 // Tecla Ñ
    		|| event.keyCode == 173 // Tecla -
    		|| event.keyCode == 186 // Tecla ´
    		|| event.keyCode == 188 // Tecla ,
    		|| event.keyCode == 190 // Tecla .
    		|| event.keyCode == 110 // Tecla . (teclado numérico)
    		|| event.keyCode == 9 // Tecla tabulador.
    		|| event.keyCode == 46 // Tecla supr.
    ){
		return true;
	}
}
function isAlphaNumericProv(event){	
	if(event.ctrlKey){
		ignoraEventKey(event);
	}else if(event.shiftKey){
		ignoraEventKey(event);
	}
	return true;	
}

// Esta función ignorará cualquier evento sobre el input. (Solución originalmente creada para evitar que el usuario 
// pueda escribir sobre los inputs de tipo fecha.
function ignoraEventKey(e){	
	e.preventDefault();
}

// Gunción para obtener el valor de una variable en la URL
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

//Elimina un renglon con 1 indice sin eventos
function borrarRenglon(id,tabla,rowCount,rowDel,count,edit,del,rowActualOPosible){
	var i=0;
	$("#"+tabla+">tbody").find("tr").eq((parseFloat(id))-1).remove();
	$("#"+rowCount).val(parseInt($("#"+tabla+">tbody>tr").length));
	if(rowActualOPosible != ""){
		$("#"+rowActualOPosible).val(parseInt($("#"+tabla+">tbody>tr").length)+parseInt(1));
	}
	var tope=$("#"+rowCount).val();
	i=parseFloat(id);
	var inicio = i;
	
	tab = document.getElementById(tabla);
	for (var i=parseFloat(inicio);i<=parseFloat(tope);i++){
		fila_act = tab.getElementsByTagName('tr')[i];
		for (j=0; celda_act = fila_act.getElementsByTagName('input')[j]; j++){
			if(celda_act.getAttribute('type') == 'hidden' || celda_act.getAttribute('type') == 'text'){
				var aux = celda_act.getAttribute('id');
				var re = /[0-9]*$/;
				aux = aux.replace(re, "");
				
				var elemento_sig = "#"+aux+((parseInt(i)+(1)));
				var elemento_act =  ""+aux+parseInt(i);
				$(elemento_sig).attr("name",elemento_act);
				$(elemento_sig).attr("id",elemento_act);
			}
		}
		for (j=0; celda_act = fila_act.getElementsByTagName('select')[j]; j++){
			var aux = celda_act.getAttribute('id');
			var re = /[0-9]*$/;
			aux = aux.replace(re, "");
			
			var elemento_sig = "#"+aux+((parseInt(i)+(1)));
			var elemento_act =  ""+aux+parseInt(i);
			$(elemento_sig).attr("name",elemento_act);
			$(elemento_sig).attr("id",elemento_act);
		}
		
		//Recorre el div contador de registro
		if(count != ""){
			count_sig = "#"+count+((parseInt(i)+(1)));
			count_act = ""+count+parseInt(i);
				$(count_sig).html(parseInt(i));
				$(count_sig).attr("name",count_act);
				$(count_sig).attr("id",count_act);
		}
		//Recorre la imagen de editar registro
		if(edit != ""){
			edit_sig = "#"+(parseInt(i)+(1))+edit;
			edit_act = ""+parseInt(i)+edit;
				$(edit_sig).attr("name",edit_act);
				$(edit_sig).attr("id",edit_act);
		}
		//Recorre la imagen de eliminar registro
		if(del != ""){
			del_sig = "#"+(parseInt(i)+(1))+del;
			del_act = ""+parseInt(i)+del;
				$(del_sig).attr("name",del_act);
				$(del_sig).attr("id",del_act);
		}
	}
	return false;
}

// Función para obtener el valor de la politica
function obtenerPolitica(politica){
	// Lo convierto a objeto
    var montoLimitePolitica = 0;
    var montoLimitePoliticaPesos = 0;
    
    // Convierte un valor de JavaScript en una cadena de la notación de objetos JavaScript (JSON).
    politica = JSON.stringify(politica);
	
	$.ajax({
		type: "POST", 
		url: "../../functions/PoliticasGastos.php", 
		data: { jObject: politica },
		dataType: "json",
		async: false, 
		timeout: 10000, 
		success: function(json){
			montoLimitePolitica = parseFloat(json[0].montoPolitica);
		},
		complete : function(json){
			montoLimitePoliticaPesos = montoLimitePolitica;
		},
		error: function(x, t, m){
			if(t==="timeout"){
				validarPoliticas(concepto);
			}
		}
	});
	
	return montoLimitePoliticaPesos;
}


/**
 * Función que permite obtener un parametro del sistema 
 *
 * codigo string => Codigo del parametro a buscar.
 * return object => devulve un objeto con los atributos de un parametro del sistema
 */
function obtenerParametro(parametro){
	var parametroSistema;
    var parametro = JSON.stringify(parametro);
	
	$.ajax({
		type: "POST", 
		url: "../../functions/ParametrosSistema.php", 
		data: {param: parametro},
		dataType: "json",
		async: false, 
		timeout: 10000, 
		success: function(json){
			parametro = (!json) ? false : json;
		},
		complete: function(data){},
		error: function(x, t, m){}
	});
	
	return parametro;
}