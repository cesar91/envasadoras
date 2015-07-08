/**
* Inicializa los Eventos una vez que se ha ter minado de Cargar el DOM (Document Object Model)
*/
var doc = $(document);
doc.ready(init);

function init(){
	blockUI(true);
	/**
	* Funcion Global, permite obtener el valor de un parametro enviado por URL 
	*
	* @param name string 	=> Nombre de la variable a obtener
	* @return string 		=> El valor del parametro si existe este, de lo contrario devuelve 0
	*/
	
	$._GET = function(name){
		var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(top.window.location.href);
		return (results !== null) ? results[1] : 0;
	}
	
	/**
	* Funcion Global, permite centrar un div en pantalla		 
	*/
	jQuery.fn.center = function(){
		this.css("position","absolute");
		this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) + $(window).scrollTop()) + "px");
		this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + $(window).scrollLeft()) + "px");
		return this;
	}
	
	/**
	* Configuracion Default del Objeto AJAX de JQuery
	*
	* @attr url string				=> Indicamos la ruta al servidor que se encarga de atender las peticiones
	* @attr timeout int				=> Indicamos el tiempo de espera de respuesta del servidor
	* @attr async string			=> Indicamos el modo de peticion sera sincrono o asincrono
	* @attr cache boolean			=> Indicamos si guardaremos en la cache las respuestas del servidor
	* @attr type string				=> Indicamos por que metodo se enviara la informacion al servidor
	* @attr dataType string			=> Indicamos el tipo de codificacion de los datos que esperamos como respuesta
	* @attr contentType string 		=> Indicamos el tipo de codificacion de los datos enviados
	*/
	var urlServices = "services/servicesCompGts.php";
	
	$.ajaxSetup({
		'url':			urlServices,
		'timeout':		5000,
		'async':		false,
		'cache':		false,
		'type':			'post',
		'dataType':		'json',			
		'contentType':	'application/x-www-form-urlencoded; charset=utf-8'		
	});
	
	if($._GET("id") != 0){
		var usuario = $("#iu").val();
		var tramite = $._GET("id");
		var json = obtenInformacionGeneral(tramite);
		obtenCentrosCostosFI(usuario);
		obtenInformacionGasolina(tramite);
		
		asignaVal("t_etapa_actual", json["t_etapa_actual"]);
		asignaVal("centro_de_costos_new", json["co_cc_clave"]);
		asignaVal("centro_de_costos_old", json["co_cc_clave"]);
		
		asignaVal("idT", tramite);			
		
		var anticipoCeco = obtenAnticipoCeco(0, tramite);	
		var anticipo = anticipoCeco["sv_total_anticipo"];
		anticipo = (!anticipoCeco ) ? "0.00" : anticipo;
		asignaVal("anticipo", anticipo);	
		asignaText("div_anticipo", anticipo, "number");
		
		var banderaComidasRepresentacion = false;
		ocultarElemento("div_comensales");
		var objetoPartidas = obtenDatosPartida(tramite);
		for(var prop in objetoPartidas){
			var partida = objetoPartidas[prop];
			
			if(partida.concepto == 1){
				banderaComidasRepresentacion = true;
			}
			
			for(var propPar in partida)
				partida[propPar] = (partida[propPar] == "" || partida[propPar] == null) ? "N/A" :  partida[propPar];
			
			var id = obtenTablaLength("comprobacion_table")+1;
			var	renglon = creaRenglonFI(objetoPartidas[prop], id);
			agregaRenglon(renglon, "comprobacion_table");
			obtenConceptosPartida(id);
		}
		$(".button_eliminar").hide();
		
		obtenInformacionDivisa();
		calcularResumen();
		calcularTotalComprobacion();
		
		asignaVal("total_rows",obtenTablaLength("comprobacion_table"));
		
		if(banderaComidasRepresentacion){
			mostrarElemento("div_comensales");
			obtenInvitados(tramite);
			obtenInformacionComidasRepresentacion(tramite);
			remuevecolumnaEliminar();
		}
	}
	
	ocultarElemento("table_divisas");
	
	$("#ventanaDivisa").draggable({containment : "#Interfaz", scroll: false}).center();		
	$("#ventanaCeco").draggable({containment : "#Interfaz", scroll: false}).center();
	
	var etapa = $("#t_etapa_actual").val();
	var perfil = $("#perfil").val();
	var registrosOriginales = obtenTablaLength("comprobacion_table");
	var delegado = $("#delegado").val();
	var privilegios = $("#privilegios").val();
	var tramite = $._GET("id");
	
	deshabilitaElemento();
	habilitaElemento("continuar");
	habilitaElemento("Volver");
	
	validaRefrescarInit(perfil);
	validaHistorialInit(perfil);
	validaColumnasInit(perfil, etapa);
	validaLecturaColumnasInit(perfil);
	validaExcepcionesInit(perfil, tramite);
	validaCecoInit(etapa, perfil);
	obtenExcepciones($._GET("id"));
	obtenExcepcionesPresupuesto($._GET("id"));
		
	$("#continuar").click(function(){
		ocultarElemento("continuar");
		mostrarElemento("ventanaCeco");				
		habilitaElemento("continuar2");
		habilitaElemento("centro_de_costos_new");
	});
	
	$("#centro_de_costos_new").change(function(){
		var val = $(this).val();
		asignaVal("centro_de_costos_new", val);				
		if(val != $("#centro_de_costos_old").val())
			asignaVal("continuar2","     Enviar a CECO");						
		else
			asignaVal("continuar2","     Continuar");	
	});
	
	$("#continuar2").click(function(){
		var val = $.trim($(this).val());
		if(val == "Continuar"){
			ocultarElemento("ventanaCeco");
			validaDivisasInit(etapa, perfil);
			validaCecoInit(etapa, perfil);
			validaBotonesInit(etapa, perfil, delegado, privilegios);
		}else if(val == "Enviar a CECO"){
			if(confirm("Esta seguro que desea reasignar el CECO?")){
				habilitaElemento();
				$("#comprobacion_form").attr("action", "comprobacion_gastos_view_finanzas.php?modo=reasignar");				
				$("#comprobacion_form").submit();		
			}					
		}				
	});
	
	$("#aceptarDivisa").click(function(){
		var requeridos = validaRequeridos($("#ventanaDivisa"));				
		if(requeridos){
			var usd = $("#tasaUSDeditable").val();
			var eur = $("#tasaEUReditable").val();				
			guardarTasas(tramite);
			asignaVal("valorDivisaEUR", eur);
			asignaVal("valorDivisaUSD", usd);
			asignaText("span_tasaDollar", usd, "number");
			asignaText("span_tasaEuro", eur, "number");			
			ocultarElemento("ventanaDivisa");
			habilitaElemento();
			deshabilitaElemento("centro_de_costos_new");
			recalculaTotalPartida();
			calcularTotalComprobacion();
		}				
	});
		
	$("#actualizaResumen").click(function(){
		calcularResumen();
		calcularTotalComprobacion();
		asignaVal("total_rows",obtenTablaLength("comprobacion_table"));
	});
	
	$("#autorizar").click(function(){
		var pendientes = validaPendientes();
		if(pendientes){
			ocultarElemento("botones_table");								
			validarPresupuesto();
			$("#comprobacion_form").attr("action", "comprobacion_gastos_view_finanzas.php?modo=autorizar");				
			$("#comprobacion_form").submit();
		}
	});

	$("#Volver").click(function(){
		ocultarElemento("botones_table");
		location.href="index.php?docs=docs&type=4";
	});

	$("#rechazar").click(function(){
		ocultarElemento("botones_table");
		$("#comprobacion_form").attr("action", "comprobacion_gastos_view_finanzas.php?modo=rechazar");
		$("#comprobacion_form").submit();
	});

	$(".button_eliminar").live('click', function(){
		if(confirm("Confirma que desea eliminar esta partida?")){
			var id = $(this).attr("id");
			var objetoRenglonPersonal = generaObjetoRenglon(id);
			var idOriginal = objetoRenglonPersonal["origenPersonal"];
			var objetoRenglonOrignal = generaObjetoRenglon(idOriginal);
			var objetoSumando = sumaRenglones(objetoRenglonOrignal, objetoRenglonPersonal);
			var renglonModificado = creaRenglonFI(objetoSumando, idOriginal);
			modificarRenglon(renglonModificado, idOriginal,"comprobacion_table");					
			$(".input_editable").priceFormat({
				prefix: '', centsSeparator: '.', thousandsSeparator: ','
			});
			$(".input_total").priceFormat({
				prefix: '', centsSeparator: '.', thousandsSeparator: ','
			});	
			validaColumnasRecalculo(idOriginal,[21]);
			obtenConceptosPartida(idOriginal);
			borrarRenglon(id,"comprobacion_table");
			validaColumnasInit(perfil, etapa);					
			calcularResumen();
			calcularTotalComprobacion();
		}						
	});
	
	$(".button_recalcular").live('click', function(){
		var tablaLength = obtenTablaLength("comprobacion_table");
		var id = $(this).attr("id");
		var idProximo = tablaLength+1;
		var valida = validarLimiteTotal(id);
		if(valida){
			var objetoRenglon = generaObjetoRenglon(id);
			var detalleOrignal = obtenDetalleOriginal(objetoRenglon["dc_id"]);
			var comparacion = comparaValoresDetalle(objetoRenglon, detalleOrignal, id);
			var renglonPersonal = creaRenglonFI(comparacion, idProximo );
			agregaRenglon(renglonPersonal,"comprobacion_table");
			validaLecturaColumna("comprobacion_table", idProximo);					
			validaColumnasInit(perfil, etapa);
			validaColumnasRecalculo(idProximo,[19,20]);
			$("#div_row_monto"+idProximo).trigger("keydown");
			$("#div_row_iva"+idProximo).trigger("keydown");
			$("#div_row_propina"+idProximo).trigger("keydown");
			$("#div_row_impuestoHospedaje"+idProximo).trigger("keydown");
			$("#div_row_total"+idProximo).trigger("keydown");
			$("#div_row_total"+idProximo).trigger("keydown");
			asignaVal("row_pendiente"+id, 0);
			
			calcularResumen();
			calcularTotalComprobacion();
		}
	});
	
	$("#centro_de_costos_new").change(function(){
		if($(this).val() != $("#centro_de_costos_old").val()){
			asignaVal("autorizar", "       Autorizar");
		}		
	});
	
	/**
	 * Evento, inicializa <input monto>
	 *
	 * 1 Asgina una mascara al campo
	 */
	$('#tasaUSDeditable').priceFormat({
		prefix: '', centsSeparator: '.', thousandsSeparator: ','
	});
	
	
	/**
	 * Evento, inicializa <input monto>
	 *
	 * 1 Asgina una mascara al campo
	 */
	$('#tasaEUReditable').priceFormat({
		prefix: '', centsSeparator: '.', thousandsSeparator: ','
	});
	
	$('.input_editable').live('keydown', function(){
		$("#"+id).priceFormat({
			prefix: '', centsSeparator: '.', thousandsSeparator: ','
		});
	});
	
	$('.input_editable').blur(function(){				
		asignaPendiente($(this).attr("id"));
		calculaTotalesPartida(id);			
	});
	
	$('.input_total').live('keydown', function(){				
		var id = $(this).attr("id");
		$("#"+id).priceFormat({
			prefix: '', centsSeparator: '.', thousandsSeparator: ','
		});
	});

	blockUI(false);
}