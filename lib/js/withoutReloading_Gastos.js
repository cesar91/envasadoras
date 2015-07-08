/********************************************************
*         T&E AJAX Actualización de Anticipo de para CXP*
*														*
* Creado por:	  Luis Daniel Barojas Cortes			*
* AJAX							                        *
*********************************************************/

function nuevoAjax()
{ 
	/* Crea el objeto AJAX. Esta función es generica para cualquier utilidad de este tipo, por
	lo que se puede copiar tal como esta aqui */
	var xmlhttp=false; 
	try 
	{ 
		// Creacion del objeto AJAX para navegadores no IE
		xmlhttp=new ActiveXObject("Msxml2.XMLHTTP"); 
	}
	catch(e)
	{ 
		try
		{ 
			// Creacion del objeto AJAX para IE 
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); 
		} 
		catch(E) { xmlhttp=false; }
	}
	if (!xmlhttp && typeof XMLHttpRequest!='undefined') { xmlhttp=new XMLHttpRequest(); } 

	return xmlhttp; 
} 


//función que manda por post la información de itinerario editada
function sendUpdate(){
	
	//Crear objeto ajax
	var ajax=nuevoAjax();
	
	//Obtiene los datos del formulario flotante
	totalT		=	$("#totalT").val();
	Total 		=	$("#Total").val(number_format($("#totalT").val(),2,".",","));
	
	ajax.open("POST", "services/ingreso_sin_recargar_proceso_gasto.php", true);
	
	ajax.onreadystatechange=function() {
		if (ajax.readyState==1 || ajax.readyState==2 || ajax.readyState==3)
	   {
		  document.getElementById("Proceso").innerHTML="Guardando, espere...";
	      document.getElementById("Proceso").style.display = 'block';
	   }

       if (ajax.readyState==4)
       { 
	   		document.getElementById("Proceso").style.display = 'none';				
			document.getElementById("TotalAnt").innerHTML="$ "+number_format($("#totalT").val(),2,".",",");
		}
	}
	//requerido para el metodo Post
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send("idT="+$("#idT").val()+"&totalT="+totalT);

	flotanteActive(0);

}