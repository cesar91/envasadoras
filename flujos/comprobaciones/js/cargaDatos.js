	/**
	 * Registro & Edicion de Comprobaciones => Funciones de Carga y proceso de Datos 
	 * Creado por: IHV 2013-06-13
	 */	 
	
	//================================================================================================================
	/**
	 * Permite mostrar/ocultar una pantalla de espera
	 *
	 * @param accion boolean => Servira para indicar que accion se desea
	 *							true, mostrará la pantalla de espera
	 *							false, ocultará la pantalla de espera
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
	 * Muestra un elemento html
	 *
	 * @param elemento string	=> Recibe el id del elemento al que se mostrará	 
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
	 * Asignará a un elemento una class definida
	 *
	 * @param campo string	=> Recibe el id del elemento al que se añadira la class
	 * @param clase string	=> Recibe el nombre de la class que se añadirá
	 */	
	function asignaClass(campo, clase){
		$("#"+campo).addClass(clase);		
	};
	
	/**
	 * Removera de un elemento una class definida
	 *
	 * @param campo string	=> Recibe el id del elemento al que se removerá la class
	 * @param clase string	=> Recibe el nombre de la class que se removerá
	 */	
	function removeClass(campo, clase){
		$("#"+campo).removeClass(clase);
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
	 * Agrega el atributo readonly a elementos html definidos o dentro de un contexto segun los parametros
	 *
	 * @param elemento string	=> Si se recibe solo este parametro (id del elemento) hara usable el elemento
	 *							   especificado, de lo contrario todos seran usables
	 */	
	function deshabilitaLecturaElemento(contexto){
		$(":input", $("#"+contexto)).each(function(){
			$(this).removeAttr("readonly");
		});					
	}
	
	/**
	 * Remueve el atributo readonly a elementos html definidos o dentro de un contexto segun los parametros
	 *
	 * @param elemento string	=> Si se recibe solo este parametro (id del elemento) hara solo lectura el elemento
	 *							   especificado, de lo contrario todos seran solo lectura
	 */		
	function habilitaLecturaElemento(contexto){
		$(":input", $("#"+contexto)).each(function(){
			$(this).attr("readonly",true);
		});								
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
				//console.log(error);
				//console.log(param);
				//console.log($(this));
				/*var msg = "Error: " + error +
							"\nPeticion: " + param; 
				alert(msg);*/
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
	 * Asigna un texto a un elemento especificado
	 *
	 * @param campo string		=> El id del elemento al cual se asignara el texto
	 * @param valor string		=> El valor que se asignara al elemento
	 * @param formato string	=> Si viene con un valor "number", formateara el valor 
	 */		
	function asignaText(campo, valor, formato ){
		if(formato == "number")
			valor = "$ "+number_format(valor,2,".",",")				
		try{
			$("#"+campo).text(valor).hide().fadeIn("");				
		}catch(e){}
	};
	
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
		return parseFloat(valor/100);		
	}
	
	/**
	 * Borrara todas las filas de una tabla contenidas en un tbody, dejando unicamente las cabeceras th>
	 */
	function borrarfilas(elemento){
		$("#" + elemento + " > tbody").empty();
	}
	
	/**
	 * Valida los elementos dentro de un contenedor
	 *
	 * @param contexto string	=> El id de un elemento contenedor dentro del cual se quiere validar mas elementos
	 * @return boolean			=> Deveulve true en caso de que no haya ningun error, y false al primer error
	 */	
	function validaRequeridos(contexto){
		if(contexto == undefined)
			contexto = $("#Layer1");	
		
		var valida = true
		$(".req", contexto).each(function(e){	
			if(valida){			
				if($(this).val() == "" || $(this).val() == 0){
					alert("Favor de colocar un valor valido para el siguente campo " + $(this).attr("id"))
					$(this).focus();
					valida = false;
				}			
			}
		});	
		return valida;
	}
	
	/**
	 * Realiza una peticion para resetear todos los cargos amex relacionados al usuario 
	 *
	 * @param usuario string	=> El id del usuario del cual se resetearan los cargos
	 */	
	function inicializaAmex(usuario){	
		var param = "inicializaAmex=ok&usuario="+usuario;
		var json = obtenJson(param);
		if(json[0])
			obtenCargosTarjeta(usuario);
	}
	//================================================================================================================
	/**
	 * Obtiene y Agrega las solicitudes a comprobar al combo solicitud
	 *
	 * @param usuario string		=> Recibe el usuario del cual buscara las solicitudes
	 * @param edicionTramite json	=> Si este valor es distinto de 0, devolvera solo una solicitud pues es edicion
	 */		
	function obtenSolicitudes(usuario, edicionTramite){
		var param = "consultaSolicitudes=ok&idusuario="+usuario;
		param+= (parseInt(edicionTramite) > 0) ? "&tramite="+edicionTramite : "";
		var option = '';
		if(edicionTramite == 0){
			option = '<option value="0"> - Seleccione - </option>'+
					 '<option value="-1">N/A - Sin solicitud</option>';
		}				
		try{
			var json = obtenJson(param).rows;
			
			if(json[1].hasOwnProperty("sv_tramite")){
				for(var i = 1; i <= Objectlength(json); i++ )
				option+= '<option value="'+json[i]["sv_tramite"]+'">'+json[i]["sv_tramite"]+" - "+json[i]["sv_motivo"]+'</option>';
			}else if(json[1].hasOwnProperty("co_tramite")){
				option+= '<option value="-1">'+json[1]["co_tramite"]+" - "+json[1]["motivoGasolina"]+'</option>';
				asignaVal("motivo", json[1]["motivoGasolina"]);	
				asignaVal("fechaInicial", json[1]["fechaInicial"]);
				asignaVal("fechaFinal", json[1]["fechaFinal"]);			
				asignaVal("centroCostos", json[1]["ceco"]);							
			}		
		}catch(e){
		}finally{
			$("#solicitud").append(option);		
		}
	}
	
	/**
	 * Obtiene y Agrega los tipos de comprobacion
	 *
	 * @param perfil string	=> Recibe el usuario del cual buscara los tipos de comprobacion permitidos
	 */			
	function obtenTiposComprobacion(perfil){
		var anticipo = $("#anticipo").val();
		var solicitud = $("#solicitud").val();
		var param = "tiposComprobacion=ok&perfil="+perfil+"&solicitud="+solicitud+"&anticipo="+anticipo;
		var json = obtenJson(param).rows;
		var option = '<option value="0"> - Seleccione - </option>';
		for(var i = 1; i <= Objectlength(json); i++ )
			option+= '<option value="'+json[i]["tipo"]+'">'+json[i]["tipo"]+'</option>';
		
		$("#tipoComprobacion").empty();
		$("#tipoComprobacion").append(option);
	}
	
	/**
	 * Obtiene y Agrega los tipos de comprobacion
	 *
	 * @param usuario string	=> El usuario del cual se quiere obtener los CECO's que podra seleccionar
	 */		
	function obtenCentrosCostos(usuario){
		var param = "centrosCostos=ok&idusuario="+usuario;
		var json = obtenJson(param).rows;
		var option = '<option value="0"> - Seleccione - </option>';
		for(var i = 1; i <= Objectlength(json); i++ )
			option+= '<option value="'+json[i]["cc_id"]+'">'+json[i]["cc_centrocostos"]+" - "+json[i]["cc_nombre"]+'</option>';
		
		$("#centroCostos").append(option);
	}
	
	/**
	 * Obtiene el Centro de Costos del Empleado
	 *
	 * @param usuario string	=> El usuario del cual se quiere obtener los CECO's que podra seleccionar
	 */		
	function obtenerCECOempleado(usuario){
		var param = "centrosCostosUsuario=ok&idusuario="+usuario;
		var json = obtenJson(param);
		var ceco = json['cc_id'];
		$("#centroCostos").val(ceco);
	}
	
	/**
	 * Obtiene y Agrega los conceptos de gastos 
	 */	
	function obtenConceptosGastos(){
		var solicitud = $("#solicitud").val();
		var tipoComprobacion = $("#tipoComprobacion").val();		
		var param = "conceptosGastos=ok&solicitud="+solicitud+"&tipoComprobacion="+tipoComprobacion;
		var json = obtenJson(param).rows;
		var option = '<option value="0"> - Seleccione - </option>';
		for(var i = 1; i <= Objectlength(json); i++ )
			option+= '<option value="'+json[i]["cp_id"]+'">'+json[i]["cp_concepto"]+'</option>';
		
		$("#concepto").empty();
		$("#concepto").append(option);
	}	
	
	/**
	 * Obtiene y Agrega las divisas
	 */	
	function obtenDivisas(){
		var param = "divisas=ok";
		var json = obtenJson(param).rows;
		var option = '<option id="0" value="0"> - Seleccione - </option>';
		for(var i = 1; i <= Objectlength(json); i++ )
			option+= '<option id="'+json[i]["div_id"]+'" value="'+json[i]["div_tasa"]+'">'+json[i]["div_nombre"]+'</option>';
		
		$("#divisa").empty();
		$("#divisa").append(option);
	}
	
	//================================================================================================================	
	/**
	 * Obtiene el ceco y el anticipo de la solicitud
	 * 
	 * @param string tramite => el tramite del cual se requiere la informacion
	 * @return json			 => el ceco y el anticipo de la solicitud
	 */	
	function obtenAnticipoCeco(tramite, tramiteEdicion){
		var param = "anticipoCeco=ok&tramite="+tramite+"&tramiteEdit="+tramiteEdicion;
		return obtenJson(param);		
	}
	
	function obtenAnticipoSol(tramite, tramiteEdicion){
		var param = "anticipoSol=ok&tramite="+tramite+"&tramiteEdit="+tramiteEdicion;
		return obtenJson(param);		
	}	
	
	/**
	 * Obtiene el la region, los dias de viaje y el tipo de viaje de la solicitud
	 * 
	 * @param string tramite => el tramite del cual se requiere la informacion
	 * @return json			 => la region, el tipo de viaje y los dias de viaje
	 */	
	function obtenDatosSolicitud(tramite){
		var param = "datosSolicitud=ok&tramite="+tramite;
		return obtenJson(param);		
	}
	
	//================================================================================================================	
	/**
	 * Obtiene el los tipos de Tarjeta 
	 */	
	function obtenTiposTarjeta(){
		var param = "tipoTarjeta=ok";
		var json = obtenJson(param).rows;
		var option = '<option value="0"> - Seleccione - </option>';
		for(var i = 1; i <= Objectlength(json); i++ )
			option+= '<option value="'+json[i]["tipo"]+'">'+json[i]["tipo"]+'</option>';
		
		$("#tipoTarjeta").empty();
		$("#tipoTarjeta").append(option);
	}	
	//================================================================================================================	
	/**
	 * Obtiene el los cargos de las tarjetas
	 *
	 * @param string usuario => id del usuario del que obtendremos los cargos	 
	 */		
	function obtenCargosTarjeta(usuario){
		var tramite = "";
		if($._GET("edit") != 0) 
			tramite = $._GET("edit");
		else if($._GET("idcg") != 0) 
			tramite = $._GET("idcg");
		
		var tipoTarjeta = $("#tipoTarjeta").val();
		var param = "cargosTarjeta=ok&tipoTarjetaAmex="+tipoTarjeta+"&idusuario="+usuario+"&tramite="+tramite;
		var json = obtenJson(param).rows;		
		var msg = (!json) ? "Sin Datos" : "Seleccione" ;
		var option = '<option value="0"> - '+msg+' - </option>';
		for(var i = 1; i <= Objectlength(json); i++ )
			option+= '<option value="'+json[i]["idamex"]+'">'+json[i]["fecha"]+' - '+ json[i]["concepto"]+' - '+ json[i]["monto"]+' '+ json[i]["moneda_local"]+'</option>';
		
		$("#cargoTarjeta").empty();		
		$("#cargoTarjeta").append(option);		
		
		if(!json)
			asignaText("noTarjeta","Tarjeta no encontrada");	
		else
			asignaText("noTarjeta",json[1]["tarjeta"]);	
	}
	
	//================================================================================================================	
	/**
	 * Obtiene el detalle del cargo seleccionado
	 *
	 * @param string usuario => id del usuario del que obtendremos los cargos	 
	 */			
	function obtenDetalleCargo(cargo, asignacion){		
		var param = "datelleCargo=ok&cargo="+cargo;
		var json = obtenJson(param);			
		
		if(asignacion == true)
			 cargaDetallesCargo(json);
		
		return json;
	}
	
	function cargaDetallesCargo(json){	
		var tipoCambio = json["montoAmex"]/json["monto"];
				
		asignaText("div_noTransaccion",json["notransaccion"]);
		asignaText("div_establecimiento",json["concepto"]);			
		asignaText("div_fechaCargo",json["fecha_cargo"]);			
		asignaText("div_rfc",json["rfc_establecimiento"]);		
		
		asignaVal("montoCargo",json["monto"]);
		asignaVal("monedaCargo",json["moneda_local"]);
		asignaText("div_montoCargo", json["monto"], "number");
		$("#div_montoCargo").append(" "+json["moneda_local"]);			
		
		asignaVal("totalAmex",json["montoAmex"]);
		asignaText("div_totalAmex", json["montoAmex"], "number");
		$("#div_totalAmex").append(" "+json["monedaAmex"]);		
		
		asignaVal("totalPesos",json["conversion_pesos"]);
		asignaText("div_totalPesos", json["conversion_pesos"], "number");
		$("#div_totalPesos").append(" MXN");		
		
		asignaVal("tipoCambio",tipoCambio);
		asignaText("div_tipoCambio", tipoCambio, "number");
	}
	
	//================================================================================================================	
	/**
	 * Verifica dependiendo del concepto si el campo de asistentes se mostrar, en tal caso será obligatorio
	 */		
	function validaAsistentes(){
		var concepto = $("#concepto option:selected").text();
		
		if((/Alimentos/.test(concepto))){
			mostrarElemento("tr_asistentes");
			asignaClass("asistentes","req");
			asignaVal("asistentes",1);		
			asignaText("label_asistentesReq","*")
			asignaClass("label_asistentesReq","style1");
		}else
			ocultarElemento("tr_asistentes");				
	}
	
	/**
	 * Verifica dependiendo de los conceptos si el comentario sera obligatorio
	 */	
	function validaComentario(){
		var concepto = $("#concepto option:selected").text();
		var asistentes = $("#asistentes").val();
		
		if((concepto == "Alimentos" && asistentes > 1)|| concepto == "Otros Gastos"){
			asignaClass("comentario","req");
			asignaText("label_comentarioReq","*");
			asignaClass("label_comentarioReq","style1");
		}else{
			removeClass("comentario","req");
			asignaText("label_comentarioReq","")
			removeClass("label_comentarioReq","style1");	
		}
	}
	
	/**
	 * Verifica dependiendo de los conceptos si se mostrara el tipo de comida el cual sería obligatorio
	 */	
	function validaTipoComidas(){
		var concepto = $("#concepto option:selected").text();
		
		if(concepto == "Alimentos"){
			asignaClass("tipoComida","req");
			obtenTipoComida();
			mostrarElemento("tr_tipoComida");				
		}else
			ocultarElemento("tr_tipoComida");		
	}

	/**
	 * Obtiene los tipos de comida cargando el combo
	 */	
	function obtenTipoComida(){
		var param = "tipoComida=ok";
		var json = obtenJson(param).rows;
		var option = '<option value="0"> - Seleccione - </option>';
		for(var i = 1; i <= Objectlength(json); i++ )
			option+= '<option value="'+json[i]["tipo"]+'">'+json[i]["tipo"]+'</option>';
		
		$("#tipoComida").empty();
		$("#tipoComida").append(option);
	}	

	/**
	 * Verifica dependiendo de los conceptos si se mostrara la propina
	 */	
	function validaPropina(){
		var concepto = $("#concepto option:selected").text();
		
		if(concepto == "Alimentos" || concepto == "Hotel" ||  concepto == "Comidas Representaci�n"){
			mostrarElemento("tr_propina");	
			$('#propina').priceFormat({
				prefix: '', centsSeparator: '.', thousandsSeparator: ','
			}).val(0).trigger("blur");			
		}else
			ocultarElemento("tr_propina");
	}

	/**
	 * Verifica dependiendo de los conceptos si se mostrara el impuesto hospedaje
	 */		
	function validaImpuestoHotel(){
		var concepto = $("#concepto option:selected").text();
		
		if(concepto == "Hotel"){
			mostrarElemento("tr_impuestoHospedaje");			
			$('#impuestoHospedaje').priceFormat({
				prefix: '', centsSeparator: '.', thousandsSeparator: ','
			}).val(0).trigger("blur");
		}else
			ocultarElemento("tr_impuestoHospedaje");		
	}
	
	/**
	 * Verifica dependiendo de los conceptos si se mostrara el campo de lugar/Restaurante
	 */		
	function validaLugarRestaurante(){
		var concepto = $("#concepto option:selected").text();
		
		if((/Comidas/.test(concepto))){
			asignaClass("lugar","req");
			mostrarElemento("tr_lugarRestaurante");
		}else
			ocultarElemento("tr_lugarRestaurante");
	}
	
	/**
	 * Verifica dependiendo de los conceptos si se mostrara el campo de Ciudad
	 */		
	function validaCiudad(){
		var concepto = $("#concepto option:selected").text();
		
		if((/Comidas/.test(concepto))){
			asignaClass("ciudad","req");
			mostrarElemento("tr_ciudad");
		}else
			ocultarElemento("tr_ciudad");
	}
	

	//================================================================================================================	
	/**
	 * Obtiene el proveedor del RFC seleccionado
	 */
	function buscaProveedor(li){
		if(li == null)
			return null;		
		var valorLista = li.selectValue;		
		
		$.ajaxSetup({url:"services/catalogo_proveedores.php"});
		var param = "nombre="+valorLista+"&tip=2";
		var json = obtenJson(param);		
		$("#proveedor").val(json);
		var url = obtenUrlService();
		$.ajaxSetup({url:url});
	}

	/**
	 * Obtiene el RFC del proveedor seleccionado
	 */
	function buscaRFC(li){
		if(li==null)
			return null;		 
		var valorLista = li.selectValue;
		
		$.ajaxSetup({url:"services/catalogo_proveedores.php"});
		var param = "nombre="+valorLista+"&tip=1";
		var json = obtenJson(param);		
		$("#rfc").val(json);
		var url = obtenUrlService();
		$.ajaxSetup({url:url});
	}
	
	/**
	 * Valida el formato del RFC
	 * 
	 * @return boolean => devuelve true si el formato es correcto, de lo contrario devuelve false
	 */
	function validaRfc(rfc){		
		var resultado = rfc.match(/[A-Za-z&Ñ]{3,4}[0-9]{6}[A-Za-z0-9]{3}/); 
		var validacion = true;
		
		if(resultado == null){		
			alert("El RFC que intenta ingresar es incorrecto. Favor de verificarlo e intente nuevamente.");
			validacion = false;
		}
		return validacion;		
	}		
		
	/**
	 * Agrega un nuevo proveedor a la BD
	 */
	function agregarProveedor(urlProveedores){		
		var rfc = $("#nuevoRfc").val();
		var proveedor = $("#nuevoProveedor").val();
		var domicilio = $("#nuevoDomicilio").val();					
		var param = "nuevoProveedor=ok&proveedor="+proveedor+"&rfc="+rfc+"&domicilio="+domicilio;
		
		$.ajaxSetup({url: urlProveedores});		
		var json = obtenJson(param);
		alert(json["mensaje"]);		
		var url = obtenUrlService();
		$.ajaxSetup({url:url});
	}
	//================================================================================================================	
	
	/**
	 * Recalcula los totales de partida considerando iva, propina, impuesto, divisa, etc..
	 */
	function recalcularTotales(){
		var monto = limpiaCantidad($("#monto").val());
		var iva = limpiaCantidad($("#iva").val());
		var propina = limpiaCantidad($("#propina").val());
		var impuestoHospedaje = limpiaCantidad($("#impuestoHospedaje").val());		
		var divisa = limpiaCantidad($("#divisa").val());	
		var total = 0;
		var totalPartida = 0;
		
		total = (monto + iva + propina + impuestoHospedaje); 
		totalPartida = (total * divisa);
		total = number_format(total,2,".",",");
		totalPartida = number_format(totalPartida,2,".",",");
		
		$('#total').val(total);
		$('#totalPartida').val(totalPartida);
	}
	
	/**
	 * Valida si el iva se mostrar en pantalla y si será requerido
	 */
	function validaIva(){
		var divisa = $("#divisa option:selected").text();
		var proveedorNacikonal = ($("#proveedorNacional").is(":checked")) ? true : false;
		
		if(divisa == "MXN" || proveedorNacikonal){
			asignaClass("iva","req");
			mostrarElemento("td_iva1");			
			mostrarElemento("td_iva2");			
			var iva = ($('#iva').val() == '') ? 0 : $('#iva').val();
			$('#iva').priceFormat({
				prefix: '', centsSeparator: '.', thousandsSeparator: ','
			}).val(iva).trigger("blur");
		}else{
			removeClass("iva","req");
			ocultarElemento("td_iva1");
			ocultarElemento("td_iva2");
		}	
	}

	//================================================================================================================	
	
	/**
	 * Genera un objeto a partir del formulario, donde los atributos son los inputs, verifica los valores y cuando 
	 * sean vacios o 0, asigna un "N/A"
	 *
	 * @return object	=> Objeto creado en base a el formulario
	 */	
	function generaObjetoFormulario(){
		var contextoAmex  = $("#div_amex");
		var contextoProveedor  = $("#table_proveedor");
		var contextoGasolina = $("#div_gasolina");
		var contextoNuevoProveedor = $("#div_nuevoProveedor");
		var tablaLength1 = obtenTablaLength("comprobacion_table");
		var tl1 = ( tablaLength1 == 0 ) ? 1 : tablaLength1;	
		
		var objetoFormulario = {
			"tipoComprobacion": 	$("#tipoComprobacion").val(),			
			"tipoTarjeta": 			$("#tipoTarjeta", contextoAmex).val(),			
			"cargoTarjeta": 		$("#cargoTarjeta", contextoAmex).val(),
			"noTransaccionTexto":	$("#div_noTransaccion", contextoAmex).text(),			
			"concepto": 			$("#concepto").val(),
			"conceptoTexto":		$("#concepto option:selected").text(),
			"tipoComida": 			$("#tipoComida").val(),
			"fecha": 				$("#fecha").val(),
			"proveedorNacional": 	($("#proveedorNacional").is(":checked")) ? true : false,			
			"folio": 				$("#folio", contextoProveedor).val(),
			"rfc": 					$("#rfc", contextoProveedor).val(),
			"proveedor": 			$("#proveedor", contextoProveedor).val(),
			"monto": 				$("#monto").val(),			
			"divisaTexto": 			$("#divisa option:selected").text(),
			"divisa": 				$("#divisa option:selected").attr("id"),
			"iva": 					$("#iva").val(),
			"total": 				$("#total").val(),
			"totalPartida": 		$("#totalPartida").val(),
			"comentario":			$("#comentario").val(),
			"propina": 				$("#propina").val(),
			"asistentes": 			$("#asistentes").val(),
			"impuestoHospedaje": 	$("#impuestoHospedaje").val(),
			"noInvitados": 			($('#numInvitados').length) ? $("#numInvitados").val() : 0,
			"identificador":			tl1
		}
		
		for(var prop in objetoFormulario)
			objetoFormulario[prop] = (objetoFormulario[prop] == "" || objetoFormulario[prop] == null) ? "N/A" :  objetoFormulario[prop];		
		return objetoFormulario;
	}
	
	/**
	 * Genera un objeto a partir de los campos dela seccion de Comidas de Representacion, donde los atributos son los inputs, 
	 * verifica los valores y cuando sean vacios o 0, asigna un "N/A"
	 *
	 * @return object	=> Objeto creado en base a el formulario
	 */	
	function generaObjetoInvitados(){
		var contexto  = $("#invitados_table");
		
		var objetoFormulario = {
			"nombreInvitado": 	$("#nombre", contexto).val(),
			"puestoInvitado": 	$("#puesto", contexto).val(),
			"empresaInvitado": 	$("#empresaInvitado", contexto).val(),
			"tipoInvitado": 	$("#tipoInvitado", contexto).val()
		}
		
		for(var prop in objetoFormulario)
			objetoFormulario[prop] = (objetoFormulario[prop] == "" || objetoFormulario[prop] == null) ? "N/A" :  objetoFormulario[prop];
		
		return objetoFormulario;
	}
	
	/**
	 * Verifica si el proveedor registrado realmente existe en la BD
	 *
	 * @return boolean	=> Devuelve true si existe el proveedor y false de lo contrario
	 */	
	function validaProveedor(){
		var validacion = true;		
		if($("#proveedorNacional").is(":checked")){
			var rfc = $("#rfc").val();
			var proveedor = $("#proveedor").val();
			var param = "validaProveedor=ok&proveedor="+proveedor+"&rfc="+rfc;
			var json = obtenJson(param);			
			if(json[0] == 0 || json[0] == null){	
			}else if($("#div_rfc").text() != "" && $("#div_rfc").text() != rfc){
				alert("El proveedor ingresado no coincide con el indicado en el cargo AMEX, favor de dar de alta al proveedor.");
				validacion = false;			
			}
		}
		return validacion;
	}
	
	/**
	 * Verifica si el anticipo que se esta registrando o la suma de este no excede el anticipo asignado a la solicitud
	 *
	 * @return boolean	=> Devuelve true si aun no se ha comprobado todo el anticipo, de lo contrario false
	 */	
	function validaSumaAnticipo(modoEdicionPartida){
		var tipoComprobacion = $("#tipoComprobacion").val();
		var validacion = true;
		var sumaAnticipo = 0;
		var tablaLength = obtenTablaLength("comprobacion_table");
		
		var anticipo = limpiaCantidad($("#div_anticipo").text());
		var totalPartida = limpiaCantidad($("#totalPartida").val());
		
		for(var i = 1; i <= tablaLength; i++){
			if($("#div_row_tipoComprobacion"+i).val() == "Anticipo")
				sumaAnticipo+= (modoEdicionPartida == i) ? 0 : limpiaCantidad($("#row_totalPartida"+i).val());
		}
		sumaAnticipo+= totalPartida;
		
		if( tipoComprobacion == "Anticipo" && (totalPartida > anticipo || sumaAnticipo > anticipo) ){
			alert("El valor que se esta ingresando supera el anticipo asignado");
			validacion = false;
		}		
		return validacion;
	}
	
	/**
	 * Verifica si un cargo amex que se esta registrando o la suma de este no excede el total de este
	 *
	 * @return boolean	=> Devuelve true si aun no se ha comprobado todo el cargo, de lo contrario false
	 */			
	function validaSumaCargos(modoEdicionPartida){
		var tipoComprobacion = $("#tipoComprobacion").val();
		var validacion = true;
		var sumaCargo = 0;
		var totalPartida = limpiaCantidad($("#totalPartida").val());
		var totalPesos = $("#totalPesos").val();		
		var cargoTarjeta = $("#cargoTarjeta").val();
		sumaCargo+= validaCargos(cargoTarjeta, modoEdicionPartida) + totalPartida;
		if(tipoComprobacion == "Comprobacion de AMEX" && (totalPartida > totalPesos || sumaCargo > totalPesos) ){			
			alert("El valor que se esta ingresando supera el cargo de la tarjeta comprobado");
			validacion = false;
		}
		return validacion;					
	}
	
	/**
	 * Obtiene el total de un cargo de una tarjeta existente en la tabla de partidas
	 *
	 * @return int	=> Devuelve el total de un cargo dentro de la tabla de partidas
	 */		
	function validaCargos(cargoTarjeta, modoEdicionPartida){
		var sumaCargo = 0;
		var tablaLength = obtenTablaLength("comprobacion_table");
		for(var i = 1; i <= tablaLength; i++){
			if(cargoTarjeta == $("#row_cargoTarjeta"+i).val()){
				sumaCargo+= (modoEdicionPartida == i) ? limpiaCantidad(0) :  limpiaCantidad($("#row_totalPartida"+i).val());
			}
		}				
		return sumaCargo;			
	}
	
	/**
	 * Agrega un renglon a la tabla de partidas
	 */	
	function agregaRenglon(renglon, tabla){		
		$("#"+tabla).append(renglon);
	}
	
	/**
	 * Crea codigo html, siendo un renglon que esta lleno con un objeto creado en base a un formulario
	 *
	 * @return strin	=> codigo de html de un renglon de una tabla
	 */	
	function creaRenglon(objeto, id, traslado, concepto, cpid, impuestoRet,impuesto,divDesc){
		objeto.noTransaccionTexto = objeto.noTransaccionTexto.substring(objeto.noTransaccionTexto.length-10,objeto.noTransaccionTexto.length)
		if(impuestoRet > ""){
			var monto = impuesto;
			var conceptoTexto = concepto;
			var totalPartida = impuesto;
			var objConcepto = cpid;
			var totalPadre = "";
			var editarPartida = (impuestoRet > "") ? '' : '<img class="editarPartida" src="../../images/addedit.png" alt="Edicion" id="'+id+'" style="cursor:pointer"/>';
		}else if (divDesc > ""){
			var monto = impuesto;
			var conceptoTexto = concepto;
			var totalPartida = impuesto;
			var objConcepto = cpid;
			var totalPadre = "";
			var editarPartida = (divDesc > "") ? '' : '<img class="editarPartida" src="../../images/addedit.png" alt="Edicion" id="'+id+'" style="cursor:pointer"/>';
		}else{
			var monto = (traslado > "") ?	impuesto : objeto.monto;
			var conceptoTexto = (traslado > "") ? concepto : objeto.conceptoTexto;
			var totalPartida = (traslado > "") ? impuesto : objeto.monto;
			var objConcepto = (traslado > "") ? cpid : objeto.concepto;
			var totalPadre = (traslado > "") ? "" : '<input type="hidden" class="id_padre" id="row_totalPartida'+id+'" name="row_totalPartida'+id+'" value="'+(limpiaCantidad(objeto.totalPartida))+'" />';
			var editarPartida = (traslado > "") ? '' : '<img style="display:none" class="editarPartida" src="../../images/addedit.png" alt="Edicion" id="'+id+'" style="cursor:pointer"/>';
		}	
		
		var renglon = "";
		renglon+= '<tr id="tr_'+id+'">'
		renglon+= 	'<td><div id="div_row'+id+'">'+id+'</div></td>';
		renglon+= 	'<td><div id="div_row_tipoComprobacion'+id+'">'+objeto.tipoComprobacion+'</div></td>';
		renglon+= 	'<td><div id="div_row_noTransaccion'+id+'">'+objeto.noTransaccionTexto+'</div></td>';
		renglon+= 	'<td><div id="div_row_fecha'+id+'">'+objeto.fecha+'</div></td>';
		renglon+= 	'<td><div id="div_row_conceptoTexto'+id+'">'+conceptoTexto+'</div></td>';
		renglon+= 	'<td><div id="div_row_comentario'+id+'">'+objeto.comentario+'</div></td>';
		renglon+= 	'<td><div id="div_row_asistentes'+id+'">'+objeto.asistentes+'</div></td>';
		renglon+= 	'<td><div id="div_row_rfc'+id+'">'+objeto.rfc+'</div></td>';
		renglon+= 	'<td><div id="div_row_proveedor'+id+'">'+objeto.proveedor+'</div></td>';
		renglon+= 	'<td><div id="div_row_factura'+id+'">'+objeto.folio+'</div></td>';
		renglon+= 	'<td><div id="div_row_total'+id+'">'+monto+'</div></td>';
		renglon+= 	'<td><div id="div_row_divisa'+id+'">'+objeto.divisaTexto+'</div></td>';
		renglon+= 	'<td><div id="div_row_totalPartida'+id+'">'+totalPartida+'</div></td>';		
		renglon+= 	'<td><div id="div_row_editar'+id+'">'+editarPartida+'</div></td>';
		var attrfid = (objeto.factura != undefined) ? objeto.fid : '';
		renglon+= 	'<td><div id="div_row_eliminar'+id+'"><img fidat="'+attrfid+'" ident="'+objeto.identificador+'" class="eliminarPartida attrfid'+attrfid+' identificador'+objeto.identificador+'" src="../../images/delete.gif" alt="Eliminar" id="'+id+'" style="cursor:pointer;"/></div>';
		renglon+=		'<div>';
		renglon+=			'<input type="hidden" id="row_'+id+'" name="row_'+id+'" value="'+id+'" />';
		renglon+=			'<input type="hidden" id="row_tipoComprobacion'+id+'" name="row_tipoComprobacion'+id+'" value="'+objeto.tipoComprobacion+'" />';
		renglon+=			'<input type="hidden" id="row_noTransaccion'+id+'" name="row_noTransaccion'+id+'" value="'+objeto.noTransaccionTexto+'" />';
		renglon+=			'<input type="hidden" id="row_cargoTarjeta'+id+'" name="row_cargoTarjeta'+id+'" value="'+objeto.cargoTarjeta+'" />';
		renglon+=			'<input type="hidden" id="row_tipoTarjeta'+id+'" name="row_tipoTarjeta'+id+'" value="'+objeto.tipoTarjeta+'" />';
		renglon+=			'<input type="hidden" id="row_fecha'+id+'" name="row_fecha'+id+'" value="'+objeto.fecha+'" />';
		renglon+=			'<input type="hidden" class="id_padre" id="row_concepto'+id+'" name="row_concepto'+id+'" value="'+objConcepto+'" />';
		renglon+=			'<input type="hidden" id="row_tipoComida'+id+'" name="row_tipoComida'+id+'" value="'+objeto.tipoComida+'" />';
		renglon+=			'<input type="hidden" id="row_comentario'+id+'" name="row_comentario'+id+'" value="'+objeto.comentario+'" />';
		renglon+=			'<input type="hidden" id="row_asistentes'+id+'" name="row_asistentes'+id+'" value="'+objeto.asistentes+'" />';
		renglon+=			'<input type="hidden" id="row_rfc'+id+'" name="row_rfc'+id+'" value="'+objeto.rfc+'" />';
		renglon+=			'<input type="hidden" id="row_proveedorNacional'+id+'" name="row_proveedorNacional'+id+'" value="'+objeto.proveedorNacional+'" />';
		renglon+=			'<input type="hidden" id="row_proveedor'+id+'" name="row_proveedor'+id+'" value="'+objeto.proveedor+'" />';
		renglon+=			'<input type="hidden" id="row_folio'+id+'" name="row_folio'+id+'" value="'+objeto.folio+'" />';
		renglon+=			'<input type="hidden" id="row_monto'+id+'" name="row_monto'+id+'" value="'+limpiaCantidad(monto)+'" />';
		renglon+=			'<input type="hidden" id="row_iva'+id+'" name="row_iva'+id+'" value="'+limpiaCantidad(monto)+'" />';		
		renglon+=			'<input type="hidden" id="row_propina'+id+'" name="row_propina'+id+'" value="'+limpiaCantidad(objeto.propina)+'" />';		
		renglon+=			'<input type="hidden" id="row_impuestoHospedaje'+id+'" name="row_impuestoHospedaje'+id+'" value="'+limpiaCantidad(objeto.impuestoHospedaje)+'" />';		
		renglon+=			'<input type="hidden" id="row_total'+id+'" name="row_total'+id+'" value="'+limpiaCantidad(monto)+'" />';		
		renglon+=			'<input type="hidden" id="row_divisa'+id+'" name="row_divisa'+id+'" value="'+objeto.divisa+'" />';		
		renglon+=			totalPadre;
		renglon+=		'</div>';
		renglon+=	'</td>';
		if(objeto.factura != undefined){
			renglon+= 	'<td><div id="div_ShowXML'+id+'">'+objeto.factura+'</div></td>';
			renglon+=			'<input type="hidden" id="have_fact'+id+'" name="have_fact'+id+'" value="'+escape(objeto.factura)+'" />';
			renglon+=			'<input type="hidden" id="fid'+id+'" name="fid'+id+'" value="'+objeto.fid+'" />';
		}
		if($("#tablaXML").length != 0) {
		renglon+= 	'<td><div style="display:none;" id="div_XML'+id+'">';
		renglon+= 			'<textarea type="hidden" name="contenidoXML'+id+'" id="contenidoXML'+id+'" rows="4" cols="50">'+$("#contenidoXML").val()+'</textarea>';
		renglon+= 			'<input type="hidden" name="uuid'+id+'" id="uuid'+id+'" value="'+$("#uuid").val()+'"/>';
		renglon+= 			'<input type="hidden" name="fechaTimbrado'+id+'" id="fechaTimbrado'+id+'" value="'+$("#fechaTimbrado").val()+'"/>';
		renglon+= 			'<input type="hidden" name="selloCFD'+id+'" id="selloCFD'+id+'" value="'+$("#selloCFD").val()+'"/>';
		renglon+= 			'<input type="hidden" name="noCertificadoSat'+id+'" id="noCertificadoSat'+id+'" value="'+$("#noCertificadoSat").val()+'"/>';
		renglon+= 			'<input type="hidden" name="selloSat'+id+'" id="selloSat'+id+'" value="'+$("#selloSat").val()+'"/>';
		renglon+= 			'<input type="hidden" name="emisorNombre'+id+'" id="emisorNombre'+id+'" value="'+$("#emisorNombre").val()+'"/>';
		renglon+= 			'<input type="hidden" name="emisorRFC'+id+'" id="emisorRFC'+id+'" value="'+$("#emisorRFC").val()+'"/>';
		renglon+= 			'<input type="hidden" name="emisorDomicilio'+id+'" id="emisorDomicilio'+id+'" value="'+$("#emisorDomicilio").val()+'"/>';
		renglon+=			'<input type="hidden" name="emisorEstado'+id+'" id="emisorEstado'+id+'" value="'+$("#emisorEstado").val()+'"/>';
		renglon+= 			'<input type="hidden" name="emisorPais'+id+'" id="emisorPais'+id+'" value="'+$("#emisorPais").val()+'"/>';
		renglon+= 			'<input type="hidden" name="comprobanteVersion'+id+'" id="comprobanteVersion'+id+'" value="'+$("#comprobanteVersion").val()+'"/>';
		renglon+= 			'<input type="hidden" name="comprobanteSerie'+id+'" id="comprobanteSerie'+id+'" value="'+$("#comprobanteSerie").val()+'"/>';
		renglon+= 			'<input type="hidden" name="comprobanteFolio'+id+'" id="comprobanteFolio'+id+'" value="'+$("#comprobanteFolio").val()+'"/>';
		renglon+= 			'<input type="hidden" name="comprobanteFecha'+id+'" id="comprobanteFecha'+id+'" value="'+$("#comprobanteFecha").val()+'"/>';
		renglon+= 			'<input type="hidden" name="comprobanteSello'+id+'" id="comprobanteSello'+id+'" value="'+$("#comprobanteSello").val()+'"/>';
		renglon+= 			'<input type="hidden" name="comprobanteFPago'+id+'" id="comprobanteFPago'+id+'" value="'+$("#comprobanteFPago").val()+'"/>';
		renglon+= 			'<input type="hidden" name="comprobanteNoCer'+id+'" id="comprobanteNoCer'+id+'" value="'+$("#comprobanteNoCer").val()+'"/>';
		renglon+= 			'<input type="hidden" name="comprobanteCertificado'+id+'" id="comprobanteCertificado'+id+'" value="'+$("#comprobanteCertificado").val()+'"/>';
		renglon+= 			'<input type="hidden" name="comprobanteSubtotal'+id+'" id="comprobanteSubtotal'+id+'" value="'+$("#comprobanteSubtotal").val()+'"/>';
		renglon+= 			'<input type="hidden" name="comprobanteTipoCambio'+id+'" id="comprobanteTipoCambio'+id+'" value="'+$("#comprobanteTipoCambio").val()+'"/>';
		renglon+= 			'<input type="hidden" name="comprobanteMoneda'+id+'" id="comprobanteMoneda'+id+'" value="'+$("#comprobanteMoneda").val()+'"/>';
		renglon+= 			'<input type="hidden" name="comprobanteTotal'+id+'" id="comprobanteTotal'+id+'" value="'+$("#comprobanteTotal").val()+'"/>';
		renglon+= 			'<input type="hidden" name="comprobanteTipoComp'+id+'" id="comprobanteTipoComp'+id+'" value="'+$("#comprobanteTipoComp").val()+'"/>';
		renglon+= 			'<input type="hidden" name="comprobanteMetodoPago'+id+'" id="comprobanteMetodoPago'+id+'" value="'+$("#comprobanteMetodoPago").val()+'"/>';
		renglon+= 			'<input type="hidden" name="comprobanteExpedicion'+id+'" id="comprobanteExpedicion'+id+'" value="'+$("#comprobanteExpedicion").val()+'"/>';		
		$("#tablaXML").empty();
		renglon+= 		'</div>';
		renglon+= 	'</td>';
		}
		renglon+= '</tr>';
		
		return renglon;	
	}
	
	/**
	 * Reemplaza un renglon en una tabla
	 */	
	function modificarRenglon(renglon, id, tabla){				
		$("#"+tabla+">tbody>tr#tr_"+id).replaceWith(renglon);		
	}	
	
	/**
	 * Compara un cargo de amex con el total de la suma de este en las partidas, si es igual, le colocará un estatus 3
	 */	
	function validaCargoAmex(modoEdicionPartida, totalPesosAmex, cargoTarjetaAmex){		
		var totalPesos = (totalPesosAmex == undefined) ? $("#totalPesos").val() : totalPesosAmex;
		var cargoTarjeta = (cargoTarjetaAmex == undefined) ? $("#cargoTarjeta").val() : cargoTarjetaAmex;
		var sumaCargo = validaCargos(cargoTarjeta, modoEdicionPartida);
		if(totalPesos == sumaCargo)
			actualizaIdamex(cargoTarjeta, 3);			
	}
	
	/**
	 * Actualiza el estatus de un cargo amex determinado
	 */		
	function actualizaIdamex(cargoTarjeta, estado){				
		var param = "actualizaIdamex=ok&cargoTarjeta="+cargoTarjeta+"&estado="+estado;		
		var json = obtenJson(param);
		var usuario = $("#idusuario").val();
		if(json[0])
			obtenCargosTarjeta(usuario);
	}	
	
	/**
	 * Genera el calculo de los totales de la comprbacion
	 */	
	function calcularResumen(){
		var anticipo = limpiaCantidad($("#anticipo").val());
		
		var personalComprobado = 0;
		var amexComprobado = 0; 
		var efectivoComprobado = 0;
		var amexExternoComprobado = 0;
		var montoReembolsar = 0;
		var montoDescontar = 0;
		
		var tablaLength = obtenTablaLength("comprobacion_table");
		
		for(var i = 1; i <= tablaLength; i++){		
			if($("#div_row_conceptoTexto"+i).text() == "Personal")
				personalComprobado+= limpiaCantidad($("#row_totalPartida"+i).val());
			else{
				switch( $("#div_row_tipoComprobacion"+i).text() ){
					case "AMEX":
						amexComprobado+= limpiaCantidad($("#row_totalPartida"+i).val());
						break;
					case "Reembolso":
						efectivoComprobado+= limpiaCantidad($("#row_totalPartida"+i).val());
						break;
					case "AMEX externo":
						amexExternoComprobado+= limpiaCantidad($("#row_totalPartida"+i).val());
						break;				
				}
			}
		}		
		
		asignaVal("personalComprobado", personalComprobado);
		asignaVal("amexComprobado", amexComprobado);
		asignaVal("amexExternoComprobado", amexExternoComprobado);
		asignaVal("efectivoComprobado", efectivoComprobado);
		asignaVal("montoReembolsar", montoReembolsar);
		asignaVal("montoDescontar", montoDescontar);
		
		asignaText("div_personalComprobado", personalComprobado,"number");
		asignaText("div_amexComprobado", amexComprobado,"number");
		asignaText("div_amexExternoComprobado", amexExternoComprobado,"number");
		asignaText("div_efectivoComprobado", efectivoComprobado,"number");
		asignaText("div_montoReembolsar", montoReembolsar,"number");
		asignaText("div_montoDescontar", montoDescontar,"number");	
	}
	//================================================================================================================	
	/**
	 * Obtiene la informacion de una partida y la devuelve al formulario,
	 * 
	 * @param int id 	=> El identificador de la partida de la cual obtendremos la informacion
	 * @return object	=> Devuleve un objeto renglon lleno con todos los valores obtenidos de los inputs y divs de la partida
	 */	
	function cargarDatosEdicion(renglon, id){		
		var contextoAmex  = $("#div_amex");
		var contextoProveedor  = $("#table_proveedor");
		var contextoGasolina = $("#div_gasolina");
		var contextoNuevoProveedor = $("#div_nuevoProveedor");
		
		$("#tipoComprobacion").val(renglon["row_tipoComprobacion"+id]).trigger("change");				
		if((renglon["row_tipoTarjeta"+id]) != false){
			$("#tipoTarjeta", contextoAmex).val(renglon["row_tipoTarjeta"+id]).trigger("change");	
			$("#cargoTarjeta", contextoAmex).val(renglon["row_cargoTarjeta"+id]).trigger("change");			
		}		
		$("#concepto").val(renglon["row_concepto"+id]).trigger("change");		
		$("#tipoComida").val(renglon["row_tipoComida"+id]);		
		$("#fecha").val(renglon["row_fecha"+id]);		
		if(renglon["row_proveedorNacional"+id] == 1 || renglon["row_proveedorNacional"+id] == true || renglon["row_proveedorNacional"+id] == "true"){
			$("#proveedorNacional").attr('checked',true).trigger("click").attr('checked',true);
			$("#folio", contextoProveedor).val(renglon["row_folio"+id]);
			$("#rfc", contextoProveedor).val(renglon["row_rfc"+id]);
			$("#proveedor", contextoProveedor).val(renglon["row_proveedor"+id]);		
		}		
		$("#monto").val(renglon["row_monto"+id]);		
		renglon["row_divisa"+id] = $("#divisa option[id="+renglon["row_divisa"+id]+"]").val();		
		$("#divisa").val(renglon["row_divisa"+id]).trigger("change");		
		$("#iva").val(renglon["row_iva"+id]);
		$("#total").val(renglon["row_total"+id]);
		$("#totalPartida").val(renglon["row_totalPartida"+id]);
		$("#comentario").val(renglon["row_comentario"+id]);
		$("#propina").val(renglon["row_propina"+id]);
		$("#asistentes").val(renglon["row_asistentes"+id]);
		$("#impuestoHospedaje").val(renglon["row_impuestoHospedaje"+id]);		
		return renglon;
	}
	
	
	function obtenRenglonTabla(id, tabla){
		var renglon = new Object();		
		$(":input", $("#"+tabla+">tbody>tr#tr_"+id)).each(function(){
			renglon[$(this).attr("id")] = $(this).val();
		});
		
		$("div", $("#"+tabla+">tbody>tr#tr_"+id)).each(function(){
			renglon[$(this).attr("id")] = $(this).text();
		});
		
		for(var prop in renglon)
			renglon[prop] = (renglon[prop] == "N/A" )? "" :  renglon[prop];	
		
		return renglon;	
	}
	//================================================================================================================	
	/**
	 * Borra un renglon especificad, y recorre todos los demas renglones posteriores para tener un consecutivo 
	 * 
	 * @param int id 	=> El identificador de la partida que se borrar
	 */	
	function borrarRenglon(id, tabla){
		var tablaLength = obtenTablaLength(tabla);
		var fila = $("#"+tabla+">tbody>tr#tr_"+id);
		
		fila.fadeOut(350).remove();	
		
		for(var i = parseInt(id)+1; i <= tablaLength; i++ ){
			var contexto = $("#"+tabla+">tbody>tr#tr_"+i);
			
			$("#div_row_partida"+i,contexto).text(i-1);	
			$("#div_row"+i,contexto).text(i-1);	
			$("#row_"+i,contexto).val(i-1);	
			$("#e_row"+i,contexto).val(i-1);	
			
			$(":input",contexto).each(function(){
				var nameAttr = $(this).attr("name");
				var idAttr = $(this).attr("id");
				nameAttr = String(nameAttr).replace(i,i-1);
				idAttr = String(idAttr).replace(i,i-1);
				
				$(this).attr("name",nameAttr);
				$(this).attr("id",idAttr);
			});						

			$("div",contexto).each(function(){
				var idAttr = $(this).attr("id");
				idAttr = String(idAttr).replace(i,i-1);				
				$(this).attr("id",idAttr);
			});										
			
			$(".editarPartida#"+i,contexto).attr("id",i-1);							
			$(".eliminarPartida#"+i,contexto).attr("id",i-1);	
			$("#tr_"+i, $("#"+tabla)).attr("id","tr_"+(i-1));						
		}		
	}
	//================================================================================================================	
	/**
	 * Verifica que todos los cargos de la spartidas esten completos
	 * 
	 * @param int modoEdicionPartida => El identificador requerido por la funcion validaCargos()
	 * @return boolean				 => Devuelve true si todos los cargos estan completos, de lo contrario false
	 */	
	function validaSumaCargosCompletos(modoEdicionPartida){
		var validacion = true;
		var miArray = new Array();
		var tablaLength = obtenTablaLength("comprobacion_table");
		
		for(var i = 1; i <= tablaLength; i++){			
			if($("#div_row_tipoComprobacion"+i).text() == "AMEX"){
				var totalPartida = $("#row_totalPartida"+i).val();				
				var cargoTarjeta = $("#row_cargoTarjeta"+i).val();
				var existe = false;
				
				for(var j = 0; j < miArray.length; j++){
					if(cargoTarjeta == miArray[j][0])
						existe = true;										
				}						
				if(!existe){						
					miArray[miArray.length] = cargoTarjeta;
				}
			}
		}			
		
		for(var k = 0; k < miArray.length; k++){
			var sumaCargo = validaCargos(miArray[k], modoEdicionPartida);								
			var cargoTarjeta = miArray[k];								
			var param = "validaCargoAmex=ok&sumaCargo="+sumaCargo+"&cargoTarjeta="+cargoTarjeta;			
			var json = obtenJson(param);			
			if(json["result"] != 1){
				alert("El total de los conceptos ingresados en la transacción "+ json["result"] +" difiere al Total Amex");	
				validacion = false;				
			}				
		}		
		return validacion;
	}	
	
	/**
	 * Verifica si dentro de las partidas existe algun gasto de gasolina en divisa MXN
	 * @return boolean	 => Devuelve true si existe algun monto de gasolina en mxn, de lo contraio false
	 */	
	function validaGasolina(){
		var tablaLength = obtenTablaLength("comprobacion_table");
		var validacion = false;
		
		for(var i = 1; i <= tablaLength; i++){
			if($("#div_row_conceptoTexto"+i).text() == "Gasolina" && $("#div_row_divisa1").text() == "MXN")
				validacion = true;
		}
		return validacion;
	}	
	
	//================================================================================================================	
	/**
	 * Cargara en el respectivo combo todos los modelos de de auto
	 */
	function obtenModeloAuto(){
		var param = "modeloAuto=ok";
		var json = obtenJson(param).rows;
		var option = '<option id="0" value="0"> - Seleccione - </option>';
		for(var i = 1; i <= Objectlength(json); i++ )
			option+= '<option id="'+json[i]["ma_id"]+'" value="'+json[i]["ma_factor"]+'">'+json[i]["ma_nombre"]+'</option>';
		
		$("#modeloAuto").empty();
		$("#modeloAuto").append(option);
	}	
	
	/**
	 * Muestra en un div especifico el factor de gasolina seleccionado por un modelo de auto
	 */
	function obtenFactor(){
		var modeloAutos = ($("#modeloAuto").val() == 0) ? "" : "Factor gasolina: "+$("#modeloAuto").val();		
		asignaText("div_factor",modeloAutos);
	}
	
	/**
	 * Calcula el monto gasolina obtenido de multiplicar el kilometraje y el factor de gasolina
	 */
	function obtenMontoGasolina(){
		var modeloAuto = $("#modeloAuto").val();
		var kilometraje = $("#kilometraje").val();
		var factor = (limpiaCantidad(modeloAuto) * limpiaCantidad(kilometraje));
		factor = factor.toFixed(2)
		$('#monto_gasolina').val(factor).priceFormat({
			prefix: '', centsSeparator: '.', thousandsSeparator: ','
		}).trigger("blur");			
	}
	
	//================================================================================================================	
	/**
	 * Borra un renglon especificad, y recorre todos los demas renglones posteriores para tener un consecutivo 
	 * 
	 * @param int tramite 	=> El identificador del tramite del cual se obtendran las partidas
	 * @param json		 	=> Devuelve un objeto lleno de objetos capas para agregar o crear partidas en una tabla
	 */	
	function obtenDatosPartida(tramite){
		var param = "tramiteEdicion="+tramite;
		return obtenJson(param).rows;		
	}
	
	/**
	 * Carga los invitados asociados al tramite recibido
	 * @param tramite int 	=> Id del tramite que tiene asociado(s) algun invitado(s)
	 * @returns json		=> Devulelve un objeto con los invitados para crear las partidas de los invitados
	 */
	function obtenDatosInvitados(tramite){
		var param = ($._GET("idcg") == 0 && $._GET("id") == 0) ? "cargaInvitados=Ok&tramiteSolicitud="+tramite : "tramiteEdicioncargaInvitados=Ok&tramiteComprobacion="+tramite;
		var json = obtenJson(param);
		
		if(json == null){
			var usuario = $("#idusuario").val();
			var param = "usuarioInvitado=ok&usuario="+usuario;
			var jsonUsuario = obtenJson(param);
			return jsonUsuario.rows;
		}else
			return json.rows;
	}
	
	//================================================================================================================	
	//================================================================================================================	
	//================================================================================================================	
	function obtenInformacionGeneral(tramite){
		var param = "comprobacionInformacion=ok&tramite="+tramite;
		var json = obtenJson(param);
		asignaText("span_noFolio", json["t_id"]);
		asignaText("span_nombreEmpleado", json["nombre"]);
		asignaText("span_motivo", json["t_etiqueta"]);
		asignaText("span_fechaRegistro", json["t_fecha_registro"]);
		asignaText("span_ceco", json["ceco"]);
		asignaText("span_destino", json["destino"]);
		asignaText("span_fechaViaje", json["fechaViaje"]);
		asignaText("span_etapa", json["et_etapa_nombre"]);

		obtenAutorizadores(tramite);
		obtenHistorialObservaciones(tramite);
		
		return json;				
	}
	
	function activaBotonImprimir(dueno, etapa){
		if(etapa == 1)
			ocultarElemento("imprimir");			
	}
	
	//COMPROBACION_ETAPA_EN_APROBACION_POR_DIRECTOR
	function activaBotones(etapa){				
		if(etapa == 9 && $("#delegado").val() == ""){
			ocultarElemento("aprobar_cv");	
			ocultarElemento("rechazar_cv");			
		}					
	}
	
	function obtenAutorizadores(tramite){
		var param = "nombreAutorizadores=ok&tramite="+tramite;
		var json = obtenJson(param);
		asignaText("span_autorizadores", json["autorizadores"]);
	}
	
	function obtenHistorialObservaciones(tramite){
		var param = "historialObservaciones=ok&tramite="+tramite;
		var json = obtenJson(param);
		asignaVal("campo_historial", json["co_observaciones"]);
	}
	
	
	//================================================================================================================	
	//================================================================================================================	
	//================================================================================================================	
	
	function validaBotonesInit(etapa, perfil, delegado, privilegios){
		ocultarElemento("autorizar","hide");
		ocultarElemento("enviarSupervisor","hide");		
		ocultarElemento("rechazar","hide");		
		if((perfil == 5 || perfil == 1) && (delegado != "" && privilegios == 1) || (delegado == "")){
			mostrarElemento("autorizar");			
			asignaVal("autorizar", "     Autorizar");
		}
		
		if(perfil == 6){
			mostrarElemento("autorizar");			
			asignaVal("autorizar", "     Autorizar");
			mostrarElemento("gerenteFinanzas");
			mostrarElemento("supervisorFinanzas");
			if(etapa !=  7 )//COMPROBACION_ETAPA_APROBADA_POR_SF
				mostrarElemento("enviarSupervisor");
		}
		
		if((delegado != "" && privilegios == 1) || (delegado == ""))
			mostrarElemento("rechazar");
	}	
	
	function validaDivisasInit(etapa, perfil){
		if(perfil == 6 && etapa != 7){
			mostrarElemento("ventanaDivisa");
			obtenDivisasFI();
			deshabilitaElemento();
			habilitaElemento("tasaUSDeditable");
			habilitaElemento("tasaEUReditable");
			habilitaElemento("aceptarDivisa");
			asignaClass("tasaUSDeditable", "req");
			asignaClass("tasaEUReditable", "req");
		}else
			ocultarElemento("ventanaDivisa","hide");
	}
	
	function validaCecoInit(etapa, perfil){
		if(perfil == 5 && etapa != 7)
			ocultarElemento("table_divisas","hide");
		else if(perfil == 6 && etapa != 7)
			mostrarElemento("table_divisas");
		
		asignaText("span_totalComprobacionText","Total comprobado");
	}
	
		
	function validaRefrescarInit(perfil){
		if(perfil != 6)
			ocultarElemento("div_refrescar","hide");	
	}
	
	function validaHistorialInit(perfil){		
		if(perfil == 1 || perfil == 2 || perfil  == 5 || perfil == 6 || perfil == 9 || perfil == 10)
			mostrarElemento("table_historialObservaciones");
		else
			ocultarElemento("table_historialObservaciones","hide");
	}
	
	//funcion que permitira desactival los botones (Eliminar/recalculo)	
	function desactivarBoton(){
		var filas = parseInt($("#comprobacion_table>tbody >tr").length);
		for(var i=1;i<=filas;i++){
			ocultarElemento("recalculo"+i,"hide");
			ocultarElemento("eliminar"+i,"hide");
		}
	}	
	
	function validaColumnasInit(perfil, etapa){
		var tablaLength = obtenTablaLength("comprobacion_table");
		for(var i = 1; i <= tablaLength; i++){
		
				$("#comprobacion_table>thead>tr>th").eq(11).hide();
				$("#tr_"+i+" td").eq(11).hide();
				$("#comprobacion_table>thead>tr>th").eq(9).hide();
				$("#comprobacion_table>thead>tr>th").eq(10).hide();
				$("#comprobacion_table>thead>tr>th").eq(12).hide();
				$("#comprobacion_table>thead>tr>th").eq(13).hide();
				$("#comprobacion_table>thead>tr>th").eq(14).hide();
				$("#comprobacion_table>thead>tr>th").eq(17).hide();
				$("#comprobacion_table>thead>tr>th").eq(18).hide();
				$("#comprobacion_table>thead>tr>th").eq(19).hide();
				$("#comprobacion_table>thead>tr>th").eq(20).hide();
				
				$("#tr_"+i+" td").eq(9).hide();
				$("#tr_"+i+" td").eq(10).hide();
				$("#tr_"+i+" td").eq(11).hide();
				$("#tr_"+i+" td").eq(12).hide();
				$("#tr_"+i+" td").eq(13).hide();
				$("#tr_"+i+" td").eq(14).hide();
				$("#tr_"+i+" td").eq(18).hide();
				$("#tr_"+i+" td").eq(19).hide();
				$("#tr_"+i+" td").eq(20).hide();
		
				$("#comprobacion_table>thead>tr>th").eq(17).hide();
				$("#tr_"+i+" td").eq(17).hide();
				$("#comprobacion_table>thead>tr>th").eq(19).hide();
				$("#comprobacion_table>thead>tr>th").eq(20).hide();
				$("#comprobacion_table>thead>tr>th").eq(21).hide();
				$("#tr_"+i+" td").eq(19).hide();
				$("#tr_"+i+" td").eq(20).hide();
				$("#tr_"+i+" td").eq(21).hide();
		}	
	}
	
	function validaLecturaColumnasInit(perfil){
		if(perfil != 6)
			habilitaLecturaElemento("comprobacion_table");
	}
	
	
	function validaColumnasRecalculo(id, columnas){
		for(var i = 0; i < columnas.length; i++)
			$("#tr_"+id+" td").eq(columnas[i]).children().hide();			
	}
	
	function validaLecturaColumna(table, id){
		var contexto = table+">tbody>tr#tr_"+id;
		habilitaLecturaElemento(contexto);
	}
	
	function validaExcepcionesInit(perfil, tramite){
		if(perfil == 6)
			obtenExcepciones(tramite);		
	}	
	
	function obtenExcepciones(tramite){
		var param = "excepciones=ok&tramite="+tramite;		
		var tr = "";
		var json = obtenJson(param);
		if (json == null)
			ocultarElemento("div_excepcion_table","hide");
		else{
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
			mostrarElemento("div_excepcion_table", "show");
			tr += '<tr><td><font color="FF0000">Presupuesto</font></td><td><strong><font color="FF0000">'+json["mensaje"]+'</font></strong></td><td><font color="FF0000">'+json["excedente"]+'</font></td><td>'+json["tipoExcepcion"]+'</td></tr>';
			$("#excepcion_table").append(tr);
		}
	}
	
	/**
	 * Obtiene y Agrega los tipos de comprobacion
	 *
	 * @param usuario string	=> El usuario del cual se quiere obtener los CECO's que podra seleccionar
	 */		
	function obtenCentrosCostosFI(usuario){
		var param = "centrosCostos=ok&idusuario="+usuario;
		var json = obtenJson(param).rows;		
		var option = "";
		for(var i = 1; i <= Objectlength(json); i++ )
			option+= '<option value="'+json[i]["cc_id"]+'">'+json[i]["cc_centrocostos"]+" - "+json[i]["cc_nombre"]+'</option>';
		
		$("#centro_de_costos_new").append(option);
		$("#cecoVentana").append(option);
	}
	
	
	function obtenInformacionGasolina(tramite){
		var param = "comprobacionGasolina=ok&tramite="+tramite;
		var json = obtenJson(param);
		if(json != false){									
			asignaText("span_modeloAtuo", json["ma_nombre"]);
			asignaText("span_factorGasolina", json["ma_factor"]);
			asignaText("span_kilometraje", json["co_kilometraje"]);
			asignaText("span_montoGasolina", json["co_monto_gasolina"]);
			asignaText("span_totalGasolina", json["totalGasolina"]);
			asignaVal("rutaDetalladaTextArea", json["co_ruta"]);
		}else
			ocultarElemento("table_gasolina","hide");
	}
	
	/**
	 * Obtiene y Agrega las divisas
	 */	
	function obtenDivisasFI(){
		var param = "divisas=ok";
		var json = obtenJson(param).rows;
		
		asignaVal("tasaUSDeditable",json["2"]["div_tasa"]);
		asignaVal("tasaEUReditable",json["3"]["div_tasa"]);
	}
	
	/**
	 * Obtiene y Agrega los tipos de comprobacion
	 *
	 * @param usuario string	=> El usuario del cual se quiere obtener los CECO's que podra seleccionar
	 */		
	function obtenConceptosPartida(id){
		var param = "conceptosGastos=ok";
		var json = obtenJson(param).rows;		
		var option = '<option value="0" id="0">Sin asignar</option>';
		for(var i = 1; i <= Objectlength(json); i++ )
			option+= '<option value="'+json[i]["cp_id"]+'">'+json[i]["cp_concepto"]+'</option>';			
		
		$("#div_row_reasignacion"+id).append(option);
	}
	
	/**
	 * Crea codigo html, siendo un renglon que esta lleno con un objeto creado en base a un formulario
	 *
	 * @return strin	=> codigo de html de un renglon de una tabla
	 */	
	function creaRenglonFI(objeto, id){
		objeto.noTransaccionTexto = objeto.noTransaccionTexto.substring(objeto.noTransaccionTexto.length-10,objeto.noTransaccionTexto.length)
		var renglon = "";
		renglon+= '<tr id="tr_'+id+'">'
		renglon+= 	'<td><div id="div_row'+id+'">'+id+'</div></td>';
		renglon+= 	'<td><div id="div_row_tipoComprobacion'+id+'">'+objeto.tipoComprobacion+'</div></td>';
		renglon+= 	'<td><div id="div_row_noTransaccion'+id+'">'+objeto.noTransaccionTexto+'</div></td>';
		renglon+= 	'<td><div id="div_row_fecha'+id+'">'+objeto.fecha+'</div></td>';
		renglon+= 	'<td><div id="div_row_conceptoTexto'+id+'">'+objeto.conceptoTexto+'</div></td>';
		renglon+= 	'<td><div id="div_row_conceptoAmexTexto'+id+'">'+objeto.conceptoAmex+'</div></td>';
		renglon+= 	'<td><textarea id="div_row_comentario'+id+'" readonly>'+objeto.comentario+'</textarea></td>';
		renglon+= 	'<td><div id="div_row_asistentes'+id+'">'+objeto.asistentes+'</div></td>';
		renglon+= 	'<td><div id="div_row_rfc'+id+'">'+objeto.rfc+'</div></td>';
		renglon+= 	'<td><div id="div_row_proveedor'+id+'">'+objeto.proveedor+'</div></td>';
		renglon+= 	'<td><div id="div_row_factura'+id+'">'+objeto.folio+'</div></td>';		
		renglon+= 	'<td><input class="input_editable" id="div_row_monto'+id+'" name="div_row_monto'+id+'" value="'+objeto.monto+'" size="7" /></td>';
		renglon+= 	'<td><input class="input_editable" id="div_row_iva'+id+'" name="div_row_iva'+id+'" value="'+objeto.iva+'" size="7" /></td>';
		renglon+= 	'<td><input class="input_editable" id="div_row_propina'+id+'" name="div_row_propina'+id+'" value="'+objeto.propina+'" size="7" /></td>';
		renglon+= 	'<td><input class="input_editable" id="div_row_impuestoHospedaje'+id+'" name="div_row_impuestoHospedaje'+id+'" value="'+objeto.impuestoHospedaje+'" size="7" /></td>';		
		renglon+= 	'<td><input class="input_total" id="div_row_total'+id+'" name="div_row_total'+id+'" value="'+objeto.total+'" size="7" readonly /></td>';
		renglon+= 	'<td><div id="div_row_divisa'+id+'">'+objeto.divisaTexto+'</div></td>';
		renglon+= 	'<td><input class="input_total" id="div_row_totalPartida'+id+'" name="div_row_totalPartida'+id+'" value="'+objeto.totalPartida+'" size="7" readonly /></td>';		
		renglon+= 	'<td><textarea id="div_row_excepcion'+id+'" readonly>'+objeto.excepcion+'</textarea></td>';		
		renglon+= 	'<td><select id="div_row_reasignacion'+id+'" name="div_row_reasignacion'+id+'" style="width:90px"></select></td>';
		renglon+= 	'<td><input type="button" class="button_recalcular" alt="Edicion" id="'+id+'" /></td>';
		renglon+= 	'<td><input type="button" class="button_eliminar" alt="Eliminar" id="'+id+'" />';				
		renglon+=		'<div>';
		renglon+=			'<input type="hidden" id="row_'+id+'" name="row_'+id+'" value="'+id+'" />';
		renglon+=			'<input type="hidden" id="row_tipoComprobacion'+id+'" name="row_tipoComprobacion'+id+'" value="'+objeto.tipoComprobacion+'" />';
		renglon+=			'<input type="hidden" id="row_noTransaccion'+id+'" name="row_noTransaccion'+id+'" value="'+objeto.noTransaccionTexto+'" />';
		renglon+=			'<input type="hidden" id="row_cargoTarjeta'+id+'" name="row_cargoTarjeta'+id+'" value="'+objeto.cargoTarjeta+'" />';
		renglon+=			'<input type="hidden" id="row_tipoTarjeta'+id+'" name="row_tipoTarjeta'+id+'" value="'+objeto.tipoTarjeta+'" />';
		renglon+=			'<input type="hidden" id="row_fecha'+id+'" name="row_fecha'+id+'" value="'+objeto.fecha+'" />';
		renglon+=			'<input type="hidden" id="row_concepto'+id+'" name="row_concepto'+id+'" value="'+objeto.concepto+'" />';
		renglon+=			'<input type="hidden" id="row_tipoComida'+id+'" name="row_tipoComida'+id+'" value="'+objeto.tipoComida+'" />';
		renglon+=			'<input type="hidden" id="row_comentario'+id+'" name="row_comentario'+id+'" value="'+objeto.comentario+'" />';
		renglon+=			'<input type="hidden" id="row_asistentes'+id+'" name="row_asistentes'+id+'" value="'+objeto.asistentes+'" />';
		renglon+=			'<input type="hidden" id="row_rfc'+id+'" name="row_rfc'+id+'" value="'+objeto.rfc+'" />';
		renglon+=			'<input type="hidden" id="row_proveedorNacional'+id+'" name="row_proveedorNacional'+id+'" value="'+objeto.proveedorNacional+'" />';
		renglon+=			'<input type="hidden" id="row_proveedor'+id+'" name="row_proveedor'+id+'" value="'+objeto.proveedor+'" />';
		renglon+=			'<input type="hidden" id="row_folio'+id+'" name="row_folio'+id+'" value="'+objeto.folio+'" />';
		renglon+=			'<input type="hidden" id="row_monto'+id+'" name="row_monto'+id+'" value="'+objeto.monto+'" />';
		renglon+=			'<input type="hidden" id="row_iva'+id+'" name="row_iva'+id+'" value="'+objeto.iva+'" />';		
		renglon+=			'<input type="hidden" id="row_propina'+id+'" name="row_propina'+id+'" value="'+objeto.propina+'" />';		
		renglon+=			'<input type="hidden" id="row_impuestoHospedaje'+id+'" name="row_impuestoHospedaje'+id+'" value="'+objeto.impuestoHospedaje+'" />';		
		renglon+=			'<input type="hidden" id="row_total'+id+'" name="row_total'+id+'" value="'+objeto.total+'" />';		
		renglon+=			'<input type="hidden" id="row_divisa'+id+'" name="row_divisa'+id+'" value="'+objeto.divisa+'" />';		
		renglon+=			'<input type="hidden" id="row_totalPartida'+id+'" name="row_totalPartida'+id+'" value="'+objeto.totalPartida+'" />';		
		renglon+=			'<input type="hidden" id="row_origenPersonal'+id+'" name="row_origenPersonal'+id+'" value="'+objeto.origenPersonal+'" />';		
		renglon+=			'<input type="hidden" id="row_id'+id+'" name="row_id'+id+'" value="'+objeto.dc_id+'" />';		
		renglon+=			'<input type="hidden" id="row_pendiente'+id+'" name="row_pendiente'+id+'" value="0" />';				
		renglon+=		'</div>';
		renglon+=	'</td>';
		if(objeto.factura != undefined){
			renglon+= 	'<td><div id="div_ShowXML'+id+'">'+objeto.factura+'</div></td>';
		}		
		renglon+= '</tr>';
		
		return renglon;	
	}
	
	/**
	 * Crea codigo html, siendo un renglon que esta lleno con un objeto creado en base a un formulario
	 *
	 * @return strin	=> codigo de html de un renglon de una tabla
	 */	
	function creaRenglonInvitados(objeto, id){
		var renglon = "";
		renglon+= '<tr id="tr_'+id+'">';
		renglon+= 	'<td><div id="div_row'+id+'">'+id+'</div></td>';
		renglon+= 	'<td><div id="div_row_nombreInvitado'+id+'">'+objeto.nombreInvitado+'</div></td>';
		renglon+= 	'<td><div id="div_row_puestoInvitado'+id+'">'+objeto.puestoInvitado+'</div></td>';
		renglon+= 	'<td><div id="div_row_empresaInvitado'+id+'">'+objeto.empresaInvitado+'</div></td>';
		renglon+= 	'<td><div id="div_row_tipoInvitado'+id+'">'+objeto.tipoInvitado+'</div></td>';
		renglon+= 	'<td align="center" valign="middle"><input type="button" class="button_eliminar" alt="Eliminar" id="'+id+'" />';
		renglon+=		'<div>';
		renglon+=			'<input type="hidden" id="row_invitado'+id+'" name="row_invitado'+id+'" value="'+id+'" />';
		renglon+=			'<input type="hidden" id="row_nombreInvitado'+id+'" name="row_nombreInvitado'+id+'" value="'+objeto.nombreInvitado+'" />';
		renglon+=			'<input type="hidden" id="row_puestoInvitado'+id+'" name="row_puestoInvitado'+id+'" value="'+objeto.puestoInvitado+'" />';
		renglon+=			'<input type="hidden" id="row_empresaInvitado'+id+'" name="row_empresaInvitado'+id+'" value="'+objeto.empresaInvitado+'" />';
		renglon+=			'<input type="hidden" id="row_tipoInvitado'+id+'" name="row_tipoInvitado'+id+'" value="'+objeto.tipoInvitado+'" />';		
		renglon+=		'</div></td>';
		renglon+= '</tr>';
		return renglon;
	}
	
	// Guardara las tasas introducidas por Finanzas
	function guardarTasas(tramite){
		var tasaDollar = $("#tasaUSDeditable").val();
		var tasaEuro = $("#tasaEUReditable").val();
		var param = "guardarTasas=ok&tasaUSD="+tasaDollar+"&tasaEur="+tasaEuro+"&tramite="+tramite;
		var json = obtenJson(param);
		if(json[0])
			calcularResumen();		
	}
	
	/**
	 * Genera un objeto a partir del formulario, donde los atributos son los inputs, verifica los valores y cuando 
	 * sean vacios o 0, asigna un "N/A"
	 *
	 * @return object	=> Objeto creado en base a el formulario
	 */	
	function generaObjetoRenglon(id){
		var objetoRenglon = {
			"tipoComprobacion": 	$("#row_tipoComprobacion"+id).val(),			
			"tipoTarjeta": 			$("#row_tipoTarjeta"+id).val(),			
			"cargoTarjeta": 		$("#row_cargoTarjeta"+id).val(),
			"noTransaccionTexto":	$("#div_row_noTransaccion"+id).text(),			
			"concepto": 			$("#row_concepto"+id).val(),
			"conceptoTexto":		$("#div_row_conceptoTexto"+id).text(),
			"tipoComida": 			$("#row_tipoComida"+id).val(),
			"fecha": 				$("#row_fecha"+id).val(),
			"proveedorNacional": 	$("#row_proveedorNacional"+id).val(),
			"folio": 				$("#row_folio"+id).val(),
			"rfc": 					$("#row_rfc"+id).val(),
			"proveedor": 			$("#row_proveedor"+id).val(),
			"monto": 				$("#div_row_monto"+id).val(),
			"divisaTexto": 			$("#div_row_divisa"+id).text(),
			"divisa": 				$("#row_divisa"+id).val(),
			"iva": 					$("#div_row_iva"+id).val(),
			"total": 				$("#row_total"+id).val(),
			"totalPartida": 		$("#row_totalPartida"+id).val(),
			"comentario":			$("#row_comentario"+id).val(),
			"propina": 				$("#div_row_propina"+id).val(),
			"asistentes": 			$("#row_asistentes"+id).val(),
			"impuestoHospedaje": 	$("#div_row_impuestoHospedaje"+id).val(),
			"excepcion": 			$("#div_row_excepcion"+id).val(),	
			"conceptoAmex": 		$("#div_row_conceptoAmexTexto"+id).text(),	
			"origenPersonal":		$("#row_origenPersonal"+id).val(),
			"dc_id": 				$("#row_id"+id).val()
		}
		
		for(var prop in objetoRenglon)
			objetoRenglon[prop] = (objetoRenglon[prop] == "" || objetoRenglon[prop] == null) ? "N/A" :  objetoRenglon[prop];					
		return objetoRenglon;
	}
	
	function obtenDetalleOriginal(dc_id){		
		var param = "comparaDetalles=ok&dc_id="+dc_id;
		return obtenJson(param);		
	}
	
	function comparaValoresDetalle(renglon, valores, id){
		renglon["monto"] = limpiaCantidad(valores["dc_monto"]) - limpiaCantidad(renglon["monto"]);
		renglon["iva"] = limpiaCantidad(valores["dc_iva"]) -  limpiaCantidad(renglon["iva"]);
		renglon["total"] = limpiaCantidad(valores["dc_total"]) - limpiaCantidad(renglon["total"]);
		renglon["totalPartida"] = limpiaCantidad(valores["dc_total_partida"]) - limpiaCantidad(renglon["totalPartida"]);
		renglon["impuestoHospedaje"] = limpiaCantidad(valores["dc_impuesto_hospedaje"]) - limpiaCantidad(renglon["impuestoHospedaje"]);
		renglon["propina"] = limpiaCantidad(valores["dc_propina"]) - limpiaCantidad(renglon["propina"]);		
		renglon["origenPersonal"] = id;		
		renglon["conceptoTexto"] = "Personal";		
		renglon["concepto"] = 31;		
		
		return renglon;	
	}
	
	function sumaRenglones(renglon, renglonPersonal){
		renglon["monto"] = limpiaCantidad(renglon["monto"]) + limpiaCantidad(renglonPersonal["monto"]);
		renglon["iva"] = limpiaCantidad(renglon["iva"]) + limpiaCantidad(renglonPersonal["iva"]);
		renglon["total"] = limpiaCantidad(renglon["total"]) + limpiaCantidad(renglonPersonal["total"]);
		renglon["totalPartida"] = limpiaCantidad(renglon["totalPartida"]) + limpiaCantidad(renglonPersonal["totalPartida"]);
		renglon["impuestoHospedaje"] = limpiaCantidad(renglon["impuestoHospedaje"]) + limpiaCantidad(renglonPersonal["impuestoHospedaje"]);
		renglon["propina"] = limpiaCantidad(renglon["propina"]) + limpiaCantidad(renglonPersonal["propina"]);		
		
		return renglon;	
	}
	
	function calculaTotalesPartida(id){
		var id = parseInt(limpiaCantidad(id));
		
		var monto = limpiaCantidad($("#div_row_monto"+id).val());
		var iva = limpiaCantidad($("#div_row_iva"+id).val());
		var propina = limpiaCantidad($("#div_row_propina"+id).val());
		var impuestoHospedaje = limpiaCantidad($("#div_row_impuestoHospedaje"+id).val());
		var divisa = limpiaCantidad($("#row_divisa"+id).val());		
		var tasa = 1;
		if(divisa == 2)
			tasa = limpiaCantidad($("#span_tasaDollar").text());
		else if(divisa == 3)
			tasa = limpiaCantidad($("#span_tasaEuro").text());
		
		var total = monto + iva + propina + impuestoHospedaje;
		var totalPartida = total*tasa;				
		
		$("#div_row_total"+id).val(total).trigger("keydown");
		$("#div_row_totalPartida"+id).val(total).trigger("keydown");		
	}
	
	function validarLimiteTotal(id){
		var id = parseInt(limpiaCantidad(id));
		var validacion = true;
		var montoEditable = limpiaCantidad($("#div_row_total"+id).val());
		var montoOriginal = limpiaCantidad($("#row_total"+id).val());
		if(montoEditable >= montoOriginal){		
			alert("Error, no puede asignar un monto mayor o igual al comprobado");
			validacion = false;
		}
		return validacion;
	}
	
	function validarPresupuesto(){
		var ceco = $("#centro_de_costos_new").val();
		var total = limpiaCantidad($("#span_totalComprobacion").text());
		var param = "presupuesto=ok&ceco="+ceco;
		var json = obtenJson(param);
		if(json["presupuesto"] > total)
			alert("El monto de la comprobación excede el presupuesto disponible.");		
	}
	
	function validaPendientes(){	
		var validacion = true;
		var tablaLength = obtenTablaLength("comprobacion_table");			
		for(var i = 1; i <= tablaLength; i++){
			if($("#row_pendiente"+i).val() == 1){
				alert("Pendiente de recalcular la siguiente partida " + i)
				validacion = false;
				break;
			}
		}		
		return validacion;
	}
	
	/**
	 * Buscara los invitados asociados al id del tramite recibido 
	 * @param tramite int	=> Id del tramite del cual se buscaran los invitados
	 */
	function obtenInvitados(tramite){
		var objetoInvitados = obtenDatosInvitados(tramite);
		if(objetoInvitados != null){
			for(var prop in objetoInvitados){
				var partida = objetoInvitados[prop];
				for(var propPar in partida)
					partida[propPar] = (partida[propPar] == "" || partida[propPar] == null) ? "N/A" :  partida[propPar];
				
				var id = obtenTablaLength("invitado_table")+1;
				var	renglon = creaRenglonInvitados(objetoInvitados[prop], id);
				agregaRenglon(renglon, "invitado_table");
			}
			asignaText("span_totalInvitados", id);
			asignaVal("numInvitados", id);
		}
	}
	
	function obtenInformacionComidasRepresentacion(tramite){
		var param = "comidasRepresentacion=ok&tramite="+tramite;
		var json = obtenJson(param);
		asignaText("span_ciudadComprobacion", json["co_lugar"]);
		asignaText("span_lugarComprobacion", json["co_ciudad"]);
		asignaText("span_totalInvitados", json["totalInvitados"]);
		asignaVal("lugar", json["co_lugar"]);
		asignaVal("ciudad", json["co_ciudad"]);
	}
	
	function asignaPendiente(id){
		var id = limpiaCantidad(id);
		var totalRecalculable = limpiaCantidad($("#div_row_total"+id).val());
		var dc_id = $("#row_id"+id).val();
		var detalleOriginal = obtenDetalleOriginal(dc_id);
		var totalOriginal = detalleOriginal["dc_total"];
		if(totalRecalculable < totalOriginal)
			asignaVal("row_pendiente"+id, "1");			
		else
			asignaVal("row_pendiente"+id, "0");			
	}	
	
	function recalculaTotalPartida(){
		var eur = limpiaCantidad($("#valorDivisaEUR").val());
		var usd = limpiaCantidad($("#valorDivisaUSD").val());
		var tablaLength = obtenTablaLength("comprobacion_table");
		
		for(var i = 1; i <= tablaLength; i++){
			var total = limpiaCantidad($("#div_row_total"+i).val());
			var divisa = $("#row_divisa1"+i).val();
			
			if(divisa == 3)
				var recalculo = eur * total;				
			else if(divisa == 2)
				var recalculo = usd * total;		
			else 
				var recalculo = total;		
			
			asignaVal("div_row_totalPartida"+i, recalculo, "number");
		}		
	}
	
	/**
	 * Genera el calculo del total de la comprobacion
	 */	
	function calcularTotalComprobacion(){
		var totalComprobacion = 0;
		
		var eur = $("#valorDivisaEUR").val();
		var usd = $("#valorDivisaUSD").val();
		
		var tablaLength = obtenTablaLength("comprobacion_table");
		
		for(var i = 1; i <= tablaLength; i++){
			var divisa = parseInt($("#row_divisa"+i).val());
			switch(divisa){
				case 2:
					totalComprobacion+= (limpiaCantidad($("#row_totalPartida"+i).val()) * usd);
					break;
				case 3:
					totalComprobacion+= (limpiaCantidad($("#row_totalPartida"+i).val()) * eur);
					break;
				default:
					totalComprobacion+= (limpiaCantidad($("#row_totalPartida"+i).val()));
					break;
			}
		}
		
		asignaVal("totalComprobacion", totalComprobacion);
		
		asignaText("span_totalComprobacion", totalComprobacion,"number");
	}
	
	/**
	 * Obtener el valor de las Divisas en la carga inicial de la Pantalla
	 */
	function obtenInformacionDivisa(){
		obtenDivisasFI();
		var usd = $("#tasaUSDeditable").val();
		var eur = $("#tasaEUReditable").val();
		asignaVal("valorDivisaEUR", eur);
		asignaVal("valorDivisaUSD", usd);
	}
	
	//================================================================================================================	
	/**
	 * Verifica si el anticipo que se esta registrando o la suma de este no excede el anticipo asignado a la solicitud
	 *
	 * @return boolean	=> Devuelve true si aun no se ha comprobado todo el anticipo, de lo contrario false
	 */	 
	function validaPolitica(ObjetoFormulario, Parametros, conceptosPoliticasFecha, previo, conceptosPoliticasAsistentes){
		Parametros["idConcepto"] = ObjetoFormulario["concepto"];
		Parametros["region"] = $("#region").val();
		var dias = ($("#diasViaje").val() > 0 && $("#diasViaje").val() != '') ? $("#diasViaje").val() : 1;
		var noches = (dias > 1) ? dias - 1 : 1;
		
		var validacion = false;
		for(var i = 0; i < conceptosPoliticasFecha.length; i++){
			if(ObjetoFormulario["concepto"] == conceptosPoliticasFecha[i]){
				validacion = true;
				break;
			}
		}
		
		var tablaLength = obtenTablaLength("comprobacion_table");
		var concepto = new Array();
		var partidas = new Array();
		var asist = 0;
		var suma = 0;
		if(validacion){
			for(var i = 1; i <= tablaLength; i++ ){	
				if( $("#row_concepto"+i).val() == ObjetoFormulario["concepto"] && $("#row_fecha"+i).val() == ObjetoFormulario["fecha"] && $( "#row_totalPartida"+i ).hasClass( "id_padre" )){
						concepto[0] = ObjetoFormulario["concepto"];
						concepto[1] = limpiaCantidad(ObjetoFormulario["monto"]);
						concepto[2] = ObjetoFormulario["fecha"];
						concepto[3] = $("#row_"+i).val();
						suma += parseFloat($("#row_monto"+i).val());
						partidas.push($("#row_"+i).val());
						concepto[4] = (ObjetoFormulario["noInvitados"] > 1) ? ObjetoFormulario["noInvitados"] : $("#row_asistentes"+i).val();
						asist += parseInt(concepto[4]);
						concepto[5] = limpiaCantidad($("#row_total"+i).val());
				}
			}
			var tmp = "";
			$.each(partidas, function(index,value) {
				tmp = tmp + partidas[index] + ",";
			});
			partidas = tmp.substring(0,tmp.length - 1);
		}else
			return false;
		
		var msg = "";
		var excedente = 0;
		var politica = obtenerPolitica(Parametros);
		var validaAsistentes = false;
		var contador = 0;
		
		for(contador = 0; contador < conceptosPoliticasAsistentes.length; contador++){
			if(conceptosPoliticasAsistentes[contador] == concepto[0]){
				validaAsistentes = true;
				break;
			}
		}
		console.log("validaAsistentes: "+validaAsistentes);
		console.log("Monto: "+suma);
		console.log("Asistentes: "+asist);
		console.log("Politica: "+politica);
		excedente = (validaAsistentes) ? suma - politica : suma - politica;
		console.log("Excedente: "+excedente);
		if(politica == 0 || excedente <= 0)
			return false;
		
		if(!previo)
			alert("Se ha excedido el importe diario permitido por pol\u00edtica en concepto de: "+ObjetoFormulario["conceptoTexto"]+" para la fecha "+ ObjetoFormulario["fecha"]);
		
		var Excepcion = {
			"concepto":		ObjetoFormulario["concepto"],
			"mensaje":		"Se ha excedido el importe diario permitido por politica en concepto de: " + ObjetoFormulario["conceptoTexto"]+" para la fecha "+ ObjetoFormulario["fecha"],
			"fecha":		ObjetoFormulario["fecha"], 
			"referencia":	partidas, 
			"totalPartida":	suma.toFixed(2), 
			"excedente":	excedente.toFixed(2)
		}
		
		try{
			var idRemover = Excepcion["referencia"].split(",");	
			for(var i = 0; i < idRemover.length; i++){
				$("#excepcion_table>tbody>tr#tr_"+idRemover[i]).fadeOut(350).remove();
			}	
		}catch(e){}
		
		return Excepcion;		
	}
	
	function verificaExcepcion(renglon, id, Parametros, conceptosPoliticasFecha, conceptosPoliticasAsistentes){
		var objeto = new Object();		
		for(var prop in renglon)
			objeto[prop.replace("row_","").replace("div_row_","").substring(0,prop.replace("row_","").replace("div_row_","").length-1)] =  renglon[prop];	

		borrarRenglon(id,"excepcion_table");				
		var Excepcion = validaPolitica(objeto, Parametros, conceptosPoliticasFecha, true, conceptosPoliticasAsistentes);
		try{
			var referencia = Excepcion["referencia"].split(",");
			var ultimaReferenciaActiva = referencia[referencia.length-1];
			
			if(Excepcion != false){			
				var renglonExcepcion = crearRenglonExcepcion(Excepcion, ultimaReferenciaActiva);
				agregaRenglon(renglonExcepcion, "excepcion_table");									
			}
		}catch(e){}		
	}
	
	function crearRenglonExcepcion(Excepcion, id){
		var renglon = "";
		renglon+= '<tr id="tr_'+id+'">'
		renglon+= 	'<td><input id="e_row'+id+'" name="e_row'+id+'" value="'+id+'" /></td>';
		renglon+= 	'<td><input id="e_row_concepto'+id+'" name="e_row_concepto'+id+'" value="'+Excepcion.concepto+'" /></td>';
		renglon+= 	'<td><input id="e_row_mensaje'+id+'" name="e_row_mensaje'+id+'" value="'+Excepcion.mensaje+'" /></td>';
		renglon+= 	'<td><input id="e_row_fecha'+id+'" name="e_row_fecha'+id+'" value="'+Excepcion.fecha+'" /></td>';
		renglon+= 	'<td><input id="e_row_referencia'+id+'" name="e_row_referencia'+id+'" value="'+Excepcion.referencia+'" /></td>';
		renglon+= 	'<td><input id="e_row_totalPartida'+id+'" name="e_row_totalPartida'+id+'" value="'+Excepcion.totalPartida+'"/></td>';
		renglon+= 	'<td><input id="e_row_diferencia'+id+'" name="e_row_diferencia'+id+'" value="'+Excepcion.excedente+'"/></td>';
		renglon+= '</tr>';		
		return renglon;		
	}
	
	/**
	 *
	 *
	 */
	function validarConceptosConAsistentes(concepto, conceptosPoliticasAsistentes){
		var validaAsistentes = false;
		var contador = 0;
		
		for(contador = 0; contador < conceptosPoliticasAsistentes.length; contador++){
			if(conceptosPoliticasAsistentes[contador] == concepto){
				validaAsistentes = true;
				break;
			}
		}
		return validaAsistentes;
	}
	
	/**
	 * Funcion que valida un concepto 
	 * 
	 * @Parametros obj => Recibe un objeto que validara por medio de la funcion excepcionesGenerales():	 
	 */
	function validaPoliticas(Parametros, conceptosPoliticasNoches, conceptosPoliticasDias, conceptosPoliticasInvitados, conceptosPoliticasAsistentes){
		var dias = ($("#diasViaje").val() > 0 && $("#diasViaje").val() != '') ? $("#diasViaje").val() : 1;
		var noches = (dias > 1) ? dias - 1 : 1;
		var tablaLength = obtenTablaLength("comprobacion_table");
		var conceptos = new Array();
		var conceptoGasolinaPolitica = [12];
		var solicitud = $("#solicitud").val();
		var href = $(location).attr('href');
		var editarViaje = $("#guardarPrevio").val();
		var enviarViaje = $("#enviarComprobacion").val();
		
		for(var i = 1; i <= tablaLength; i++ ){
			var atributos = new Array();
			
			if(conceptos.length == 0){
				atributos[0] = $("#row_concepto"+i).val();
				atributos[1] = ($("#row_total"+i).length) ? parseFloat($("#row_total"+i).val()) :  parseFloat(0);
				atributos[2] = "N/A";
				atributos[3] = $("#row_"+i).val();
				atributos[4] = $("#div_row_conceptoTexto"+i).text();
				atributos[5] = ( $("#row_concepto"+i).val() == 1 ) ? parseInt($("#noInvitados", "#respaldo_invitado_table").val()) : parseInt($("#div_row_asistentes"+i).text());
				atributos[6] = validarConceptosConAsistentes(atributos[0], conceptosPoliticasAsistentes) ? parseFloat(atributos[1] / atributos[5]) : parseFloat(0);
				conceptos.push(atributos);
			}else{
				var existe = false;
				for(var j= 0; j < conceptos.length; j++){
					if( conceptos[j][0] == $("#row_concepto"+i).val()){
						conceptos[j][1] += ($("#row_total"+i).length) ? parseFloat($("#row_total"+i).val()) : parseFloat(0);
						conceptos[j][3] += ","+$("#row_"+i).val();
						conceptos[j][5] = ( $("#row_concepto"+i).val() == 1 ) ? parseInt($("#noInvitados", "#respaldo_invitado_table").val()) : parseInt($("#div_row_asistentes"+i).text());
						conceptos[j][6] += validarConceptosConAsistentes(conceptos[j][0], conceptosPoliticasAsistentes) ? parseFloat($("#row_total"+i).val() / conceptos[j][5]) : parseFloat(0);
						existe = true; 
						break;
					}
				}
				
				if(!existe){
					atributos[0] = $("#row_concepto"+i).val();
					atributos[1] = ($("#row_total"+i).length) ?  parseFloat($("#row_total"+i).val()) :  parseFloat(0);
					atributos[2] = "N/A";
					atributos[3] = $("#row_"+i).val();
					atributos[4] = $("#div_row_conceptoTexto"+i).text();
					atributos[5] = ( $("#row_concepto"+i).val() == 1) ? parseInt($("#noInvitados", "#respaldo_invitado_table").val() ) : parseInt($("#div_row_asistentes"+i).text());
					atributos[6] = validarConceptosConAsistentes(atributos[0], conceptosPoliticasAsistentes) ? parseFloat(atributos[1] / atributos[5]) : parseFloat(0);
					conceptos.push(atributos);
				}
			}
		}
		
		for(var i = 0; i < conceptos.length; i++){
			Parametros['idConcepto'] = conceptos[i][0];
			if(solicitud > 0){
				Parametros['solicitud'] = solicitud;
			}
			var tablaExcepcionLength = obtenTablaLength("excepcion_table");
			var politica = obtenerPolitica(Parametros);
			
			for(var j = 0; j < conceptosPoliticasNoches.length; j++){
				if(conceptosPoliticasNoches[j] == conceptos[i][0]){
					politica = politica*noches;
				}
			}
			
			for(var j = 0; j < conceptosPoliticasDias.length; j++){
				if(conceptosPoliticasDias[j][0] == conceptos[i][0]){
					politica = politica*parseInt(dias/conceptosPoliticasDias[j][1]);
					politica = (solicitud > 0) ? politica : 0;
					excedente = (solicitud > 0) ? excedente : 0;


					if((/new=new/.test(href))){
						if(validarConceptosConAsistentes(conceptos[i][0], conceptosPoliticasAsistentes))
							politica = (solicitud > 0) ? politica : 0;
					}
				}
			}
			
			if(conceptosPoliticasInvitados != undefined){
				var invitados = $("#noInvitados").val();
				for(var j = 0; j < conceptosPoliticasInvitados.length; j++){
					if(conceptosPoliticasDias[j] == conceptos[i][0]){
						politica = politica*invitados;
					}
				}
			}
			
			for(var j = 0; j < conceptoGasolinaPolitica.length; j++){
				if(conceptoGasolinaPolitica[j] == conceptos[i][0]){
					politica = politica + limpiaCantidad($("#monto_gasolina").val());
				}
			}
			
			var excedente = (validarConceptosConAsistentes(conceptos[i][0], conceptosPoliticasAsistentes) ) ? conceptos[i][6] - politica : conceptos[i][1] - politica;
			politica = (Parametros['flujo'] == 4) ? 0 : politica;
			excedente = (Parametros['flujo'] == 4) ? 0 : excedente;
			if(editarViaje == "Editar" || enviarViaje){
				politica = (Parametros['flujo'] == 3 && solicitud == "-1") ? 0 : politica;
				excedente = (Parametros['flujo'] == 3 && solicitud == "-1") ? 0 : excedente;
			}
			
			if(excedente > 0 && politica > 0){			
				alert("Se ha excedido el importe permitido por el periodo del viaje en concepto de: "+ conceptos[i][4]);					
				var Excepcion = {
					"concepto":		conceptos[i][0],
					"mensaje":		"Se ha excedido el importe permitido por el periodo del viaje en concepto de: "+conceptos[i][4],
					"fecha":		"N/A",
					"referencia":	"N/A",
					"totalPartida":	conceptos[i][1],
					"excedente":	excedente.toFixed(2)
				}			
				var renglonExcepcion = crearRenglonExcepcion(Excepcion, tablaLength+tablaExcepcionLength);
				agregaRenglon(renglonExcepcion, "excepcion_table");		
			}
			
			if(conceptos[i][0] == 32 && dias < conceptosPoliticasDias[1][1]){
				var Excepcion = {
					"concepto":		conceptos[i][0],
					"mensaje":		"No se superan los días de viaje para el concepto: "+conceptos[i][4],
					"fecha":		"N/A",
					"referencia":	"N/A",
					"totalPartida":	"N/A",
					"excedente":	"N/A"
				}			
				var renglonExcepcion = crearRenglonExcepcion(Excepcion, tablaLength+tablaExcepcionLength);
				agregaRenglon(renglonExcepcion, "excepcion_table");
			}
		}			
		obtenPartidasExcepciones();
	}
	
	function cargartipoInvitados(){
		var tipoInvitados = new Array("Interno", "Externo");
		var opcionestipoInvitados = '<option value="0"> - Seleccione - </option>';
		for(var j = 0; j < tipoInvitados.length; j++){
			opcionestipoInvitados += '<option value="' + tipoInvitados[j] + '">' + tipoInvitados[j] + '</option>';
		}
		
		$("#tipoInvitado").empty();
		$("#tipoInvitado").append(opcionestipoInvitados);
	}
	
	
	function obtenPartidasExcepciones(){
		var tablaLength = obtenTablaLength("excepcion_table");
		var partidas = "";
		for(var i = 0; i < tablaLength; i++){
			var row = $("#excepcion_table>tbody> tr").eq(i);
			var col = $("td", row).eq(0);
			var val = $(":input", col).eq(0).val();
			partidas+= val+",";
		}	
		asignaVal("totalExcepciones", partidas.substring(0, partidas.length-1));
	}
	
	/**
	 * Verifica si el concepto es el seleccionado 
	 */
	function buscaConceptoporID(conceptoaBuscar){
		var conceptoSeleccionado = $("#concepto option:selected").val();
		var conceptoEncontrado = false;
		
		if(conceptoSeleccionado == conceptoaBuscar)
			conceptoEncontrado = true;
		
		return conceptoEncontrado;
	}
	
	/**
	 * Verifica si el concepto es Comidas de Representacion, mostrara la tabla de invitados 
	 */
	function validaComidasRepresentacion(){
		var idConceptoComidaRepresentacion = 1;
		var concepto = buscaConceptoporID(idConceptoComidaRepresentacion);
		
		if(concepto){
			asignaRequeridos();
			mostrarElemento("invitados_table");
			asignaVal("agregar_invitado", "     Agregar Invitado");
			cargartipoInvitados();
		}else{
			ocultarElemento("invitados_table");
		}
	}
	
	/**
	 * Remueve la columna de Eliminar de la tabla de invitados 
	 */
	function remuevecolumnaEliminar(){
		var tablaLength = obtenTablaLength("invitado_table");
		for(var i = 1; i <= tablaLength; i++){
			$("#tr_"+i+" td", $("#invitado_table")).eq(5).hide();
		}
	}
	
	/**
	 * Cargara la Informacion de los invitados a la comida de Representacion en caso de estar asociado una Solicitud, en su defecto
	 * cargara la informacion del empleado en sesión(Nombre, Puesto, Empresa y Tipo)
	 */
	function cargaInvitados(){
		var solicitud = $("#solicitud").val();
		obtenInvitados(solicitud);
	}
	
	/**
	 * Cargara la Informacion de la comida de Representacion en caso de estar asociado una Solicitud
	 */
	function cargarInfoComidasRepresentacion(){
		var solicitud = $("#solicitud").val();
		obtenInformacionComidasRepresentacion(solicitud);
	}
	
	/**
	 * Si el tipo de invitado seleccionado es interno, colocará en nombre de la empresa
	 */
	function validaEmpresadeInvitado(){
		var tipoInvitado = $("#tipoInvitado option:selected").val();
		
		if(tipoInvitado == "Interno"){
			var usuario = $("#idusuario").val();
			var param = "empresaUsuario=ok&usuario="+usuario;
			var json = obtenJson(param);
			asignaVal("empresaInvitado", json['e_nombre']);
			deshabilitaElemento("empresaInvitado");
		}else{
			asignaVal("empresaInvitado", "");
			habilitaElemento("empresaInvitado");
		}
	}
	
	/**
	 * Agrega un Invitado a la listade invitados
	 */
	function agregarInvitado(){
		var tablaLength = obtenTablaLength("invitado_table");
		var id = tablaLength+1;
		var objeto = generaObjetoInvitados();
		var renglon = creaRenglonInvitados(objeto, id);
		agregaRenglon(renglon, "invitado_table");
		
	}
	
	/**
	 * Actualizar el campo de Total de invitados
	 */
	function obtenTotalInvitados(){
		var tablaLength = obtenTablaLength("invitado_table");
		asignaText("span_totalInvitados", tablaLength);
		asignaVal("numInvitados", tablaLength);
	}
	
	/**
	 * Restablecer los campos seleccionados
	 *
	 */
	function restablecerCampos(){
		ocultarElemento("infoComidaRepresentacion");
		mostrarElemento("infoComidaRepresentacion");
	}
	
	/**
	 * Premite clonar la tabla donde se encuentras listados los invitados
	 */
	function resguardarInfoComidasRepresentacion(){
		$('#respaldo_invitado_table').append($('#invitado_table').clone().attr('id', 'listadeInvitados'));
		asignaVal("lugarComida", $("#lugar").val());
		asignaVal("ciudadComida", $("#ciudad").val());
		asignaVal("noInvitados", $("#numInvitados").val());
	}
	
	/**
	 * Si el concepto es Comidas de Representacion debera clonar la tabla de invitados_table
	 */
	function validaConceptoComidasRepresentacion(){
		var idConceptoComidaRepresentacion = 1;
		var encontrado = buscaConceptoporID(idConceptoComidaRepresentacion);
		
		if(encontrado){
			// Eliminar el concepto de Comidas Representacion, para evitar que el usuario intente ingresar dos veces el concepto
			$("#concepto option[value="+idConceptoComidaRepresentacion+"]").remove();
			resguardarInfoComidasRepresentacion();
			ocultarElemento("invitados_table");
		}
	}
	
	/**
	 *  Asignar la clase 'req' para los campos de invitados
	 */
	function asignaRequeridos(){
		asignaClass("nombre","req");
		asignaClass("tipoInvitado","req");
		asignaClass("puesto","req");
		asignaClass("empresaInvitado","req");
	}
	
	/**
	 * Cargar los datos de la Comida de Representacion: Lugar, Ciudad, Lista de Invitados, Total de Invitados
	 * 
	 * 1 Restablecemos las opciones de los conceptos
	 * 2 Seleccionamos el concepto
	 * 3 Limpiamos la tabla anterior de Invitados
	 * 4 Clonamos la tabla respaldada
	 * 5 Asignamos los valores correpondientes
	 * 6 Obtenemos el total de invitados
	 * 7 Mostramos nuevamente la tabla de comidas de representacion
	 * 8 Limpiamos la tabla donde se habia realizado el respaldo de los invitados
	 *  
	 */
	
	function cargaDatosComidarepresentacion(renglon, id){
		var idConceptoComidaRepresentacion = 1;
		
		if(renglon["row_concepto"+id] == idConceptoComidaRepresentacion){
			obtenConceptosGastos();
			$("#concepto").val(renglon["row_concepto"+id]).trigger("change");
			$("#invitado_table").remove();
			$('#td_Invitados').append($('#listadeInvitados').clone().attr('id', 'invitado_table'));
			asignaVal("lugar", $("#lugarComida").val());
			asignaVal("ciudad", $("#ciudadComida").val());
			asignaVal("propina", renglon["row_propina"+id]);
			obtenTotalInvitados();
			mostrarElemento("invitados_table");
			asignaVal("agregar_invitado", "     Agregar Invitado");
			$("#listadeInvitados").remove();
		}
	}
	
	/**
	 * Si el concepto de Comida de Representacion ya ha sido registrado, eliminaremos el concepto del combo
	 * para impedir que se registre nuevamente y con esto se vuelva a generar una segunda lista de invitados. 
	 */
	function verificaExistenciaComidasRepresentacion(idConcepto){
		var tablaLength = obtenTablaLength("comprobacion_table");
		var encontrado = false;
		
		for(var j = 0; j <= tablaLength; j++){
			if($("#row_concepto"+j).val() == idConcepto)
				encontrado = true;
		}
		
		return encontrado;
	}
	
	function remueveConceptos(idConcepto){
		$("#concepto option[value="+idConcepto+"]").remove();
	}
	
	function eliminaInvitados(concepto){
		var comidasRepresentacion = 1;
		if(concepto == comidasRepresentacion){
			borrarfilas("invitado_table");
			$("#listadeInvitados").remove();
			obtenConceptosGastos();
		}
	}
	
	function obtenUrlService(){
		var URL = document.URL;		
		URL = (/comp_solicitud/i.test(URL)) ? "services/servicesCompGts.php" : "services/services.php";
		return URL;		
	}