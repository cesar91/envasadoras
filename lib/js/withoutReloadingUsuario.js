/********************************************************
*         T&E AJAX Actualización de Perfil de usuario	*
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


//función que manda por post la información del nuevo delegado
function sendUpdateGrant(){
	
	//Crear objeto ajax
	var ajax=nuevoAjax();
	
	//Obtiene los datos del formulario flotante

	name_user	=	$("#name_h").val();
	id_user		=	$("#id_user").val();
	coment		=	$("#coment").val();
	
	var stringSend="coment="+coment+"&id_user="+id_user;
	
	ajax.open("POST", "ingreso_sin_recargar_proceso_usuario.php", true);
	
	ajax.onreadystatechange=function() {
		
		if (ajax.readyState==1 || ajax.readyState==2 || ajax.readyState==3)
	   {		  		  
	      document.getElementById("Proceso").style.display = 'block';
		  document.getElementById("Proceso").innerHTML="Guardando, espere...";
	   }

       if (ajax.readyState==4)
       {		   	
			
			if(ajax.responseText=="")
				document.getElementById("Proceso").innerHTML="Guardado correctamente...";
			else
				document.getElementById("Proceso").innerHTML="Error al guardar, verifique que el usuario exista";
				
			$("#Proceso").fadeOut(2700);			
		}
	}	
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send(stringSend);
	
}

//función que manda por post la información de usuario editada
function sendUpdate(){
	
	//Crear objeto ajax
	var ajax=nuevoAjax();
	
	//Obtiene los datos del formulario flotante

	email		=	$("#email").val();
	telefono 	=	$("#telefono").val();
	jefe 		= 	$("#jefe").val();
	passwd		=	$("#passwd").val();
	
	var stringSend="email="+email+"&telefono="+telefono+"&jefe="+jefe;
	
	if(valPs && passwd!="")
		stringSend+="&passwd="+passwd;
	
	ajax.open("POST", "ingreso_sin_recargar_proceso_usuario.php", true);
	
	ajax.onreadystatechange=function() {
		if (ajax.readyState==1 || ajax.readyState==2 || ajax.readyState==3)
	   {		  		  
	      document.getElementById("Proceso").style.display = 'block';
		  document.getElementById("Proceso").innerHTML="Guardando, espere...";
	   }

       if (ajax.readyState==4)
       {
	   		document.getElementById("Proceso").innerHTML="Guardado correctamente...";		   
			$("#Proceso").fadeOut(2700);
			$("#passwd").val("");
			$("#passwd2").val("");
		}
	}	
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send(stringSend);
	
}

var searchJ=true;

function searchJefe(){
	
	//Crear objeto ajax
	var ajax=nuevoAjax();
	var jefe=$("#jefe").val();
	var msj="";
	ajax.open("POST", "ingreso_sin_recargar_proceso_usuario.php", true);
	
	ajax.onreadystatechange=function() {
		if (ajax.readyState==1 || ajax.readyState==2 || ajax.readyState==3)
	   {
		  document.getElementById("Proceso").innerHTML="Buscando...";
	      document.getElementById("Proceso").style.display = 'block';
	   }

       if (ajax.readyState==4)
       { 	//alert(ajax.responseText);
	   		if(ajax.responseText=="no"){
				msj="El número de empleado de jefe no existe";
				searchJ=false;
			}
			else{
				msj="El número de empleado de jefe existe";
				searchJ=true;
			}
			
			document.getElementById("Proceso").innerHTML=msj;
			$("#Proceso").fadeOut(2500);
		}
	}
	//requerido para el metodo Post
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send("idJefe="+jefe);
	
}