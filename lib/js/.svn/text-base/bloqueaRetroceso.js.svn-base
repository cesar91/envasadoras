// function to possibly override keypress
trapfunction = function(event){	
	//Detectando si es Firefox
	var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox/') > -1;  
	if (is_firefox ) {
			var target = event.target ? event.target : event.srcElement;
			if(event.which == 8){
				if (target && target.nodeName.toLowerCase () == "input" || target.nodeName.toLowerCase () == "textarea") {
		        	return true;
		    	} else {
		    		//Preguntar si desea abandonar la página actual
					if(confirm("¿Desea regresar a la página anterior?  Pulse 'Aceptar' para continuar ó \n\t 'Cancelar' para anular la acción.")){
						history.back(-1);
		        		return true;
		    		}else{
		        		return false;
		    		}
		    	}
			}
	}

	//Detectando Cualquier version de IE
	if ('\v'=='v') {
			var target = window.event.target ? window.event.target : window.event.srcElement;
			if(window.event.keyCode == 8){
				if (target && target.nodeName.toLowerCase () == "input" || target.nodeName.toLowerCase () == "textarea") {
		        	return true;
		    	} else {
		    		//Preguntar si desea abandonar la página actual
					if(confirm("¿Desea regresar a la página anterior?  Pulse 'Aceptar' para continuar ó \n\t 'Cancelar' para anular la acción.")){
		        		return true;
		    		}else{
		        		return false;
		    		}
		    	}
			}
	}
	 
	//Detectando si es IE6
	var is_ie6 = (window.external && typeof window.XMLHttpRequest == "undefined");  
	if (is_ie6 ) {
			var target = window.event.target ? window.event.target : window.event.srcElement;
			if(window.event.keyCode == 8){
				if (target && target.nodeName.toLowerCase () == "input") {
		        	return true;
		    	} else {
		    		//Preguntar si desea abandonar la página actual
					if(confirm("¿Desea regresar a la página anterior?  Pulse 'Aceptar' para continuar ó \n\t 'Cancelar' para anular la acción.")){
		        		return true;
		    		}else{
		        		return false;
		    		}
		    	}
			}
	}
}

//document.onkeyup = function(event) {
//	document.getElementById("keypressed").innerHTML = ""; // clear the message
//	return true;
//}

document.onkeydown = trapfunction; // IE, Firefox, Safari
document.onkeypress = trapfunction; // only Opera needs the backspace nullifying in onkeypress
