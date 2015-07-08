/********************************************************
*         T&E AJAX Actualización de Itinerarios y 		*
*		consulta de información de los mismos para CXP	*
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

//función encargada de buscar el id de itinerario y cargar información de esta
function Edit_itinerario(id){
//	alert(id);		
		
		// Creo objeto AJAX y envio peticion al servidor
		var ajax=nuevoAjax();
		ajax.open("GET", "services/ingreso_sin_recargar_proceso.php?idTramite="+id, true);
		ajax.onreadystatechange=function() 		
		 {
			   if (ajax.readyState==1 || ajax.readyState==2 || ajax.readyState==3)
			   {
				  //Cambia contenido de capa 'Proceso' y la activa.
				  document.getElementById("Proceso").innerHTML="Cargando...";
			      document.getElementById("Proceso").style.display = 'block';
			   }

                if (ajax.readyState==4)
                { 
					//desactiva la capa 'Proceso'
					document.getElementById("Proceso").style.display = 'none';
					
					//separa la cadena que trae del php en arreglo
					var arrDatos=ajax.responseText.split("&");
					
                	document.getElementById("labelPasaje").innerHTML="No. de Pasaje "+arrDatos[0];	
										
					document.getElementById("divCostoViaje").innerHTML="$<input type='text' value='"+arrDatos[1]+"' id='costoViaje' style='width:45px' onkeypress='return validaNum (event); activaSave()' onkeyup='activaSave()'>MXP";
					
					document.getElementById("divIva").innerHTML='$<input type="text" name="iva" id="iva" value="'+arrDatos[2]+'" style="width:45px" onkeypress="return validaNum (event); activaSave()" onkeyup="activaSave()" />';
					
					document.getElementById("divTua").innerHTML='<input type="text" name="tua" id="tua" value="'+arrDatos[3]+'" style="width:45px" onkeypress="return validaNum (event); activaSave()" onkeyup="activaSave()" />';
					
					document.getElementById("divNombreHotel").innerHTML=arrDatos[4];
					
					document.getElementById("divTipoHab").innerHTML=arrDatos[5];
					
					document.getElementById("divCostoHotel").innerHTML='$<input type="text" name="costoHotel" id="costoHotel" value="'+arrDatos[6]+'" style="width:45px" onkeypress="return validaNum (event)"/>';
					
					document.getElementById("divOtrocargo").innerHTML='Otros cargos:$<input type="text" name="otrocargo" id="otrocargo" value="'+arrDatos[7]+'" style="width:45px" onkeypress="return validaNum (event)"/>';
					
                } 
        }
		ajax.send(null);			
}

//función que manda por post la información de itinerario editada
function sendUpdate(){
	
	//Crear objeto ajax
	var ajax=nuevoAjax();
	
	//Obtiene los datos del formulario flotante

	costoViaje  =	$("#costoViaje").val();
	iva 		=	$("#iva").val();
	tua 		= 	$("#tua").val();
	costoHotel  = 	$("#costoHotel").val();
	otrocargo   = 	$("#otrocargo").val();
	id 			= 	$("#id_itinerario").val();
	
	ajax.open("POST", "services/ingreso_sin_recargar_proceso.php", true);
	
	ajax.onreadystatechange=function() {
		if (ajax.readyState==1 || ajax.readyState==2 || ajax.readyState==3)
	   {
		  document.getElementById("Proceso").innerHTML="Guardando, espere...";
	      document.getElementById("Proceso").style.display = 'block';
	   }

       if (ajax.readyState==4)
       { 	
	   		document.getElementById("Proceso").style.display = 'none';				
			searchSolicitud();
			//alert(ajax.responseText);
		}
	}
	//requerido para el metodo Post
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send("id="+id+"&costoViaje="+costoViaje+"&iva="+iva+"&tua="+tua+"&costoHotel="+costoHotel+"&otrocargo="+otrocargo);
	//alert(costoViaje+" "+iva + " "+tua +" " +costoHotel +" " +otrocargo);

	flotanteActive(0);

}


function searchSolicitud()
{
	//Crear objeto ajax
	var ajax=nuevoAjax();
	ajax.open("POST", "services/ingreso_sin_recargar_proceso.php", true);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==1 || ajax.readyState==2 || ajax.readyState==3)
	   {
//p		  document.getElementById("Proceso").innerHTML="Cargando, espere...";
//p	      document.getElementById("Proceso").style.display = 'block';
	   }

       if (ajax.readyState==4)
       { 	
//p	   		document.getElementById("Proceso").style.display = 'none';
			
			var arrDatos=ajax.responseText.split("&");
			
			//Imprime en capas totales
			for(i=0; i <$("#cont_itinerarios").val();i++){
//p			document.getElementById("divTotalItinerario"+i).innerHTML="$ "+number_format(arrDatos[i],2,".",",");
			}
			var x=i;
			$("#anticipo").val(number_format(arrDatos[i++],2,".",","));
			$("#ivaT").val(number_format(arrDatos[i++],2,".",","));
			$("#Total").val(number_format(arrDatos[i++],2,".",","));
            $("#divTotalItinerarioC"+x).html("Total de conceptos: $" +number_format(arrDatos[i],2,".",","));
		}
	}
	//requerido para el metodo Post
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send("idTramite="+$("#idT").val());
	
}

function addNewHotel(){
	
	cdSelect=$("#selectCd").val();
	selectHotel=$("#selectHotel").val();
	costoHotel=$("#costoHotel").val();
	
	if(cdSelect==""){
		alert("EL nombre de la ciudad es requerido")
		$("#selectCd").focus();
	}
	else if(selectHotel=="" || selectHotel=="Asignar Hotel..."){
		alert("EL nombre del hotel es requerido")
		$("#selectHotel").focus();
	}
	
	else if(costoHotel==0){
		alert("EL costo del hotel es obligatorio")
		$("#costoHotel").focus();
	}
	
	else{
		var ajax=nuevoAjax();
		
		
		ajax.open("POST", "../solicitudes/services/ingreso_sin_recargar_proceso.php", true);

		ajax.onreadystatechange=function() {
			if (ajax.readyState==1 || ajax.readyState==2 || ajax.readyState==3)
		   {
			  document.getElementById("Proceso").innerHTML="Cargando, espere...";
		      document.getElementById("Proceso").style.display = 'block';
		   }

	       if (ajax.readyState==4)
	       { 	
		   		document.getElementById("Proceso").style.display = 'none';
							onloadHotels();
			}
		}		
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send("cdSelect="+cdSelect+"&selectHotel="+selectHotel+"&costoHotel="+costoHotel);
		
	}
}

function searchSolicitud_amex(){
	//Crear objeto ajax
	var ajax=nuevoAjax();
	ajax.open("POST", "services/ingreso_sin_recargar_proceso.php", true);

	ajax.onreadystatechange=function() {
		if (ajax.readyState==1 || ajax.readyState==2 || ajax.readyState==3)
	   {
		  document.getElementById("Proceso").innerHTML="Cargando, espere...";
	      document.getElementById("Proceso").style.display = 'block';
	   }

       if (ajax.readyState==4)
       {
	   		document.getElementById("Proceso").style.display = 'none';

			var arrDatos=ajax.responseText.split("&");
			//Imprime en capas totales
            for(i=0; i <$("#cont_itinerarios").val();i++){
				document.getElementById("divTotalItinerario"+i).innerHTML="$ "+number_format(arrDatos[i],2,".",",");
			}

			$("#anticipo").val(number_format(arrDatos[i++],2,".",","));
			$("#ivaT").val(number_format(arrDatos[i++],2,".",","));
			$("#Total").val(number_format(arrDatos[i],2,".",","));

		}
	}
	//requerido para el metodo Post
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send("id_tramite_amex="+$("#idT").val());
}

function EditaMontos(id, fila)
{
	var ajax= nuevoAjax();
	IdConcepto = id;
	Fila = fila;
  	$("#monto_conceptos"+Fila).html= "<CENTER> <IMG SRC='imagenes/cargando.gif'> <BR> <SPAN CLASS='Etiqueta'> Cargando... </SPAN> </CENTER>";
	ajax.open("POST", "services/ajxCom.php?", true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    ajax.send("id_concepto="+IdConcepto+"&fila="+Fila);
    ajax.onreadystatechange=function()
	{
		if (ajax.readyState==4)
		{
            $("#monto_conceptos"+Fila).html(ajax.responseText);
			//montos_conceptos.innerHTML=ajax.responseText;
		}
	}
}

function OperaMonto(id, fila)
{
	var ajax= nuevoAjax();
	MontoText = document.getElementById("txtmonto"+fila).value;
    //MontoText = $("#txtmonto"+fila).html();
    $("#monto_conceptos"+Fila).html= "<CENTER> <IMG SRC='imagenes/cargando.gif'> <BR> <SPAN CLASS='Etiqueta'> Cargando... </SPAN> </CENTER>";
	ajax.open("POST", "services/ajxCom.php?", true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    ajax.send("IdConcepto="+id+"&fila="+Fila+"&montoText="+MontoText);
    ajax.onreadystatechange=function()
	{
		if (ajax.readyState==4)
		{
            $("#monto_conceptos"+Fila).html(ajax.responseText);
			//montos_conceptos.innerHTML=ajax.responseText;
		}
	}
}


function changedFields(id){
	
		//$("#Monto1").html("<input type='text'>");
		$("#"+id).attr("readonly",false);
		$("#"+id).focus();
		$("#"+id).css("background-color","#E1E4EC");
		//mouseOut(id);
}

function changedFieldsOut(id){
	//for (i=1;i<=lastRow;i++){
		//	$("#"+id).val(number_format($("#"+id).val(),2,".",","));
	//}
	$("#"+id).css("background-color","white");
	mouseOut(id);
}

function mouseOut(id){

	$("#"+id).val(number_format($("#"+id).val().replace(/,/gi,""),2,".",","));

}

//funciones nuevas para la edicion de texto

function changedFieldsOutText(id){
	//for (i=1;i<=lastRow;i++){
		//	$("#"+id).val(number_format($("#"+id).val(),2,".",","));
	//}
	$("#"+id).css("background-color","white");
	
	mouseOutText(id);
}

function mouseOutText(id){
	$("#"+id).val();
}

