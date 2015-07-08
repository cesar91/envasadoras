	/**
	 * Registro & Edicion de Comprobaciones => Configuracion y Eventos, Reglas de las Vistas
	 * Creado por: IHV 2013-06-13
	 */	 
	  
	/**
	 * Inicializa los Eventos una vez que se ha ter minado de Cargar el DOM (Document Object Model)
	 */
	var doc = $(document);
	doc.ready(init);
	
	function init(){
		blockUI(true);
		
		/**
		 * Funcion Global, permite centrar un div en pantalla		 
		 */
		jQuery.fn.center = function () {
			this.css("position","absolute");
			this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) + $(window).scrollTop()) + "px");
			this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + $(window).scrollLeft()) + "px");
			return this;
		}
			
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
		 * Variables Globales
		 */
		var usuario = $("#idusuario").val();
		var perfil = $("#perfil").val();
		var perfil = $("#diasViaje").val(1);
		var tramiteEdicion = $._GET("idcg");
		var urlServices = "services/servicesCompGts.php";	
		var urlProveedores = "services/catalogo_proveedores.php";	
		var modoEdicionPartida = false;
		
		/**
		 * Configuración Default del Objeto AJAX de JQuery
		 *
		 * @attr url string			=> Indicamos la ruta al servidor que se encarga de atender las peticiones
		 * @attr timeout int		=> Indicamos el tiempo de espera de respuesta del servidor
		 * @attr async string		=> Indicamos el modo de peticion será sincrono o asincrono
		 * @attr cache boolean		=> Indicamos si guardaremos en la cache las respuestas del servidor
		 * @attr type string		=> Indicamos por que metodo se enviará la información al servidor
		 * @attr dataType string	=> Indicamos el tipo de codificación de los datos que esperamos como respuesta
		 * @attr contentType string => Indicamos el tipo de codificación de los datos enviados
		 */		 
		$.ajaxSetup({
			'url':			urlServices,
			'timeout':		5000,
			'async':		false,
			'cache':		false,
			'type':			'post',
			'dataType':		'json',			
			'contentType':	'application/x-www-form-urlencoded; charset=utf-8'		
		});
		
		/**
		 * Configuracion Regional de los calendarios en la pantalla.		
		 */
		$(function($){
			$.datepicker.regional['es'] = {
				closeText: 'Cerrar',
				prevText: '<Ant',
				nextText: 'Sig>',
				currentText: 'Hoy',
				monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
				monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
				dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'],
				dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sab'],
				dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
				weekHeader: 'Sm',
				dateFormat: "dd/mm/yy",
				changeMonth: true,
				changeYear: true,
				maxDate: obtenFecha(),
				showOn: "both",
				buttonImage: "../../images/b_calendar.png",
				buttonImageOnly: true,	 
				constrainInput: true,
				firstDay: 1,
				showWeek: true,      
				isRTL: false
			};
			$.datepicker.setDefaults($.datepicker.regional['es']);
		});	
		
		$("#fecha").datepicker();

		/**
     	 * Objeto Parametro
		 *
		 * @attr idUsuario int	=> 	id del usuario en sesion
		 * @attr idConcepto int	=>	id del concepto
		 * @attr flujo int 		=>	id del flujo 
		 * @attr region int 	=>	id de la region del viaje a comprobar
		 */		
		var Parametros = {
			'idUsuario': 	usuario,
			'idConcepto':	null,
			'flujo':	 	4,
			'region':	 	null
		};
		
		
		/**
     	 * Arreglo de todos los conceptos que tomaran una validacion por fecha
		 */
		var conceptosPoliticasFecha = new Array("1","2","6","7","8","10","11","12","13","14","15","16","17","18","19","20","21","23","24","27","28","32","33","34","34","35","36","37","39","40","41","42","43","43","45","47","48","49","50","51","52","53","54","55","56","57","59","60","61","62","63");
		/**
     	 * Arreglo de todos los conceptos que tomaran una validacion por fecha
     	 * var conceptosPoliticasFecha = new Array("6"); Inicial
		 */
		var conceptosPoliticasInvitados = new Array("1");
		
		/**
     	 * Arreglo de todos los conceptos que tomaran una validacion por fecha
		 * El primer elemento del arreglo es el id del concepto, es segundo elemento es la cantidad de dias por la que sera evaluado: [idConcepto, noDias]
		 */
		var conceptosPoliticasDias = new Array(["2", "1"], ["11", "1"], ["20", "1"], ["21", "1"], ["45", "1"], ["57", "1"], ["59", "1"]);
		
		/**
     	 * Arreglo de todos los conceptos que tomaran una validacion por fecha
		 */
		var conceptosPoliticasNoches = new Array("29", "30", "31");
		
		/**
     	 * Arreglo de todos los conceptos que tomaran una validacion por la cantidad de asistentes
		 */
		var conceptosPoliticasAsistentes = new Array("1", "2", "3", "4", "5");
		
		/**
     	 * Arreglo de todos los elementos que deben ocultarse una vez cargada la pantalla
		 */
		var ocultos = new Array("div_nuevoProveedor",
								"div_gasolina",
								"div_amex",
								"tr_gasolina",
								"tr_tipoComida",
								"table_proveedor",
								"tr_asistentes",
								"tr_propina",
								"tr_impuestoHospedaje",
								"tr_lugarRestaurante",
								"tr_ciudad",
								"td_iva1",
								"td_iva2",
								"tr_historialObservaciones",
								"invitados_table",
								"respaldo_invitado_table",
								"idusuario",
								"empresa",
								"perfil", 
								"tipoViaje", 
								"diasViaje",
								"region", 
								"tramiteEdit",
								"totalPartidas",
								"totalExcepciones",
								"excepcion_table",
								"motivoSolicitud");
		for(var i = 0; i < ocultos.length; i++)
			ocultarElemento(ocultos[i], "hide");

		/**
     	 * Arreglo de todos los elementos que deben ser requeridos una vez cargada la pantalla
		 */
		var requeridos = new Array("solicitud",
								"tipoComprobacion",
								"centroCostos",
								"concepto",
								"divisa",
								"monto",
								"total",
								"totalPartida");
		for(var i = 0; i < requeridos.length; i++)
			asignaClass(requeridos[i],"req");
		
		/**
		 * Define elementos con la propiedad drag	
		 */
		$("#div_nuevoProveedor").draggable({containment:"#Interfaz",scroll:false});
		$("#div_gasolina").draggable({containment:"#Interfaz",scroll:false});

		
		/**
		 * Reglas de Pantalla
		 */
		
		// Inicia solo con el campo solictud y el registro de partidas habilitadps
		deshabilitaElemento();
		habilitaElemento("solicitud");		
		habilitaElemento("registrarPartida");		
		
		// Carga de Campos
		obtenSolicitudes(usuario, tramiteEdicion);
		obtenTiposComprobacion(perfil);
		obtenCentrosCostos(usuario);		
		obtenConceptosGastos();
		obtenDivisas();
		asignaVal("fecha", obtenFecha());
		
		/**
		 * Eliminar el concepto de Comidas de Representacion (id: 1) una vez que ya ha sido comprobado
		 */ 
		if(verificaExistenciaComidasRepresentacion(1))
			remueveConceptos(1);
		
		/**
		 * Evento, Cambiar el valor <select solicitud>
		 *
		 * 1 Obtener y asignar el CECO y el anticipo de la Solicitud Seleccionada
		 * 2 Obtener y asignar la region, tipo de viaje y los dias de viaje de la Solicitud Seleccionada		 
		 * 3	a) 	Si la solicitud seleccionada es diferente de 0, 
		 * 3	a)	i)	habilita los elementos, excepto los totales de los montos
		 * 3	a) 	ii)	Recargar el combo de tipos de comprobacion
		 * 3	a) 	iii)Si la longitud de la tabla de partidas es 0, deshabilita los botones de guardarPrevio y enviarComprobacion		 
		 * 3	a) 	iv) i)	Si selecciona la solicitud -1 (gasolina) mostrara y hara requeridos los campos de gasolina
		 * 3	a) 	iv) ii)	Si selecciona la solicitud diferente de -1 ocultara los campos de gasolina
		 * 3	b) 	Si la solicitud seleccionada es 0, deshabilita botones de forma similar a cuando se inicializa
		 * 4 Resetear los campos del registro de partida
		 */
		$("#solicitud").change(function(){
			var solicitud = $(this).val();
			
			var anticipoCeco = obtenAnticipoCeco(solicitud, tramiteEdicion);	
			var anticipo = anticipoCeco["sv_total_anticipo"];
			var ceco = anticipoCeco["sv_ceco_paga"];
			var tablaLength = obtenTablaLength("comprobacion_table");
			
			anticipo = (!anticipoCeco ) ? "0.00" : anticipo;
			ceco = (!anticipoCeco ) ? "0" : ceco;
			
			ocultarElemento("div_amex");			
			asignaVal("anticipo", anticipo);	
			asignaText("div_anticipo", anticipo, "number");			
			asignaVal("centroCostos", ceco);
			
			ocultarElemento("table_regisrtoPartidas", "hide");
			mostrarElemento("table_regisrtoPartidas");
			
			if(solicitud != 0){
				for(var i = 0; i < requeridos.length; i++)
					asignaClass(requeridos[i],"req");
				
				habilitaElemento();														
				deshabilitaElemento("total");				
				deshabilitaElemento("totalPartida");				
				deshabilitaElemento("monto_gasolina");								
				obtenTiposComprobacion(perfil);						
				
				if(tablaLength == 0){
					deshabilitaElemento("guardarPrevio");
					deshabilitaElemento("enviarComprobacion");			
				}
				
				if (solicitud == "-1") {
					mostrarElemento("tr_gasolina");
					asignaClass("motivo","req");
					ocultarElemento("td_fechas","hide");
					/*asignaClass("fechaInicial","req");
					asignaClass("fechaFinal","req");*/
					obtenerCECOempleado(usuario);
					
					/*if($._GET("idcg") == 0){
						asignaVal("fechaInicial", obtenFecha());
						asignaVal("fechaFinal", obtenFecha());
					}*/
				}else{
					ocultarElemento("tr_gasolina");
					var item_text = $("#solicitud option:selected").text();
					asignaVal("motivoSolicitud", item_text);
				}
			}else{
				deshabilitaElemento();
				habilitaElemento("solicitud");		
				habilitaElemento("registrarPartida");	
				for(var i = 0; i < ocultos.length; i++)
					ocultarElemento(ocultos[i]);							
			}
			
			obtenConceptosGastos();
			
			/**
			 * Eliminar el concepto de Comidas de Representacion (id: 1) una vez que ya ha sido comprobado
			 */ 
			if(verificaExistenciaComidasRepresentacion(1))
				remueveConceptos(1);
			
			asignaVal("fecha", obtenFecha());
		});
		
		/**
		 * Evento, Cambiar el valor <select tipoComprobacion>
		 *
		 * 1 Ocultar todos los posibles elementos en pantalla
		 * 2 Desmarcar la casilla de proveedor nacional
		 * 3 Si tipo de comprobacion es AMEX
		 * 3 a)	i)	Muestra el div de amex
		 * 3 a)	ii)	Carga los tipos de tarjeta
		 * 3 a)	iii)Asignas como requeridos los inputs de amex
		 * 3 b) Oculta el div de amez
		 */
		$("#tipoComprobacion").change(function(){
			var tipoComprobacion = $(this).val();
			$("#concepto").val(0);
			$("#monto").val('');
			$("#total").val('');
			$("#divisa").val(0);
			$("#totalPartida").val('');
			$("#iva").val('');
			$("#factura").removeAttr("checked");			
			ocultarElemento("tr_tipoComida");
			ocultarElemento("table_proveedor");
			ocultarElemento("tr_asistentes");
			ocultarElemento("tr_propina");
			ocultarElemento("tr_impuestoHospedaje");
			ocultarElemento("td_iva1");
			ocultarElemento("td_iva2");		
			$("#proveedorNacional").removeAttr("checked");		
			obtenConceptosGastos();
			
			/**
			 * Eliminar el concepto de Comidas de Representacion (id: 1) una vez que ya ha sido comprobado
			 */ 
			if(verificaExistenciaComidasRepresentacion(1))
				remueveConceptos(1);
			
			if(tipoComprobacion == "Comprobacion de AMEX"){
				mostrarElemento("div_amex");
				obtenTiposTarjeta();
				asignaClass("tipoTarjeta","req");
				asignaClass("cargoTarjeta","req");
			}else
				ocultarElemento("div_amex");									
		});
		
		/**
		 * Evento, Cambiar el valor <select tipoTarjeta>
		 *
		 * 1 Limpiar los campos amex
		 * 2 Cargar los cargos de la tarjeta del usuario
		 */
		$("#tipoTarjeta").change(function(){
			var tipoTarjeta = $(this).val();
			ocultarElemento("div_amex", "hide");						
			mostrarElemento("div_amex");
			$(this).val(tipoTarjeta);
			obtenCargosTarjeta(usuario);
			asignaClass("tipoTarjeta","req");
			asignaClass("cargoTarjeta","req");
		});
		
		/**
		 * Evento, Cambiar el valor <select cargoTarjeta>
		 *
		 * 1 Cargar el detalle del cargo de la tarjeta seleccionado
		 */
		$("#cargoTarjeta").change(function(){
			obtenDetalleCargo($(this).val(),true);					
		});		
		
		/**
		 * Evento, Cambiar el valor <select concepto>
		 *
		 * 1 Verifica si se mostrara el campo de asistentes 
		 * 2 Verifica si los comentarios seran obligatorios
		 * 3 Verifica si mostrará los tipos de comida
		 * 4 Verifica si se mostrara la propina
		 * 5 Verifica si se mostrara la el impuesto hospedaje
		 * 6 Borra los invitados que se generan de forma incorrecta 
		 * 7 Verifica si se mostrara la tabla de invitados
		 * 8 Carga invitados o informacion del usuario en sesion
		 */
		$('#concepto').change(function(){
			validaAsistentes();
			validaComentario();
			validaTipoComidas();
			validaPropina();
			validaImpuestoHotel();
			validaLugarRestaurante();
			validaCiudad();
			borrarfilas("invitado_table");
			validaComidasRepresentacion();
			cargarInfoComidasRepresentacion();
			cargaInvitados();
		});
		
		/**
		 * Evento, Cambiar el valor <select tipoInvitado>
		 *
		 * 1 Verifica el tipo de invitado  
		 * 2 Si el invitado es interno, colocara el nombre de la empresa
		 */
		$('#tipoInvitado').change(function(){
			validaEmpresadeInvitado();
		});
		
		/**
		 * Evento click en el <button agregar_invitado>
		 * 1 Validar que los campos no esten vacios
		 * 2 Generar el renglon del invitado agregado
		 * 3 Actualizar el total de invitados
		 * 4 Restablecer campos
		 * 5 Validar Politica  
		 */
		$("#agregar_invitado").click(function(){
			asignaRequeridos();
			var obligatorios = validaRequeridos($("#invitados_table"));
			
			if(obligatorios){
				agregarInvitado();
				obtenTotalInvitados();
				restablecerCampos();
			}
		});
		
		/**
		 * Evento, Click en el <checkbox proveedorNacional>
		 *
		 * 1 a)	Verifica si esta habilitado el checkbox	 
		 * 1 b)	Verifica si no esta habilitado el checkbox, ocultará el div de proveedor
		 * 2 Validara si es necesario mostrar el iva
		 */
		$('#proveedorNacional').click(function(){
			if($(this).is(":checked")){
				mostrarElemento("table_proveedor");
				asignaVal("muestraAgregarProveedor","     Agregar nuevo proveedor");
				asignaClass("folio","req");
				asignaClass("rfc","req");
				asignaClass("proveedor","req");	
			}else
				ocultarElemento("table_proveedor");			
			validaIva();
		});
		
		/**
		 * Evento, autocomplete en el <input proveedor>
		 *
		 * 1 Muestra un lista de posibles proveedores a medida que se va tecleando
		 * 2 Al seleccionar uno se mostrara el RFC en el campo respectivo
		 */		
		$("#proveedor",$("#table_proveedor")).autocomplete(urlProveedores, {				
			minChars:2,
			matchSubset:1,
			matchContains:1,
			cacheLength:false,
			onItemSelect:buscaRFC,
			autoFill:false,			
			extraParams:{tip:1},
			maxItemsToShow: 10
		});

		/**
		 * Evento, autocomplete en el <input rfc>
		 *
		 * 1 Muestra un lista de posibles RFC a medida que se va tecleando
		 * 2 Al seleccionar uno se mostrara el proveedor en el campo respectivo
		 */
		$("#rfc",$("#table_proveedor")).autocomplete(urlProveedores, {				
			minChars:2,
			matchSubset:1,
			matchContains:1,
			cacheLength:false,
			onItemSelect:buscaProveedor,
			autoFill:false,			
			extraParams:{tip:2},
			maxItemsToShow: 10
		});
		
		/**
		 * Evento, click en el <button muestraAgregarProveedor>
		 *
		 * 1 Muestra el Div de nuevo proveedor
		 * 2 Asigno como requeridos los cmapos dentro del div mostrado
		 */
		$("#muestraAgregarProveedor").click(function(){			
			mostrarElemento("div_nuevoProveedor");			
			$("#div_nuevoProveedor").center();
			asignaVal("agregarProveedor","      Agregar");
			asignaVal("cancelarAgregarProveedor","      Cancelar");
			asignaClass("nuevoProveedor", "req");
			asignaClass("nuevoRfc", "req");
			asignaClass("nuevoDomicilio", "req");
		});			
		
		/**
		 * Evento, blur en el <input nuevoRfc>
		 *
		 * 1 valida que el formato del RFC sea correcto
		 */
		$("#nuevoRfc").blur(function(){
			validaRfc($(this).val());					
		});	
		
		/**
		 * Evento, click en el <button agregarProveedor>
		 *
		 * 1 Valida los campos requeridos 
		 * 2 Valida el formato del RFC
		 * 3 Agrega el proveedor a la BD
		 */
		$("#agregarProveedor").click(function(){		
			var requeridos = validaRequeridos($("#div_nuevoProveedor"));
			if(requeridos){
				var rfc = validaRfc($("#nuevoRfc").val());
				if(rfc)
					agregarProveedor(urlProveedores);			
			}
			
		});
		
		/**
		 * Evento, click en el <button cancelarAgregarProveedor>
		 *
		 * 1 Oculta el Div de nuevo proveedor		 
		 */
		$("#cancelarAgregarProveedor").click(function(){
			ocultarElemento("div_nuevoProveedor");			
		});
		
		/**
		 * Evento, click en el <button cerrar_div_nuevoProveedor>
		 *
		 * 1 Oculta el Div de nuevo proveedor		 
		 */
		$("#cerrar_div_nuevoProveedor").click(function(){
			ocultarElemento("div_nuevoProveedor");			
		});
		
		/**
		 * Evento, inicializa <input monto>
		 *
		 * 1 Asgina una mascara al campo
		 */
		$('#monto').priceFormat({
			prefix: '', centsSeparator: '.', thousandsSeparator: ','
		});
		
		/**
		 * Evento, blur en el <input monto>
		 *
		 * 1 Recalcula los totales 
		 */
		$("#monto").blur(function(){
			recalcularTotales();
		});
		
		/**
		 * Evento, change en el <select divisa>
		 *
		 * 1 Verifica si se mostrará el iva
		 * 2 Recalcula los totales 
		 */
		$('#divisa').change(function(){
			validaIva();			
			recalcularTotales();		
		});
		
		/**
		 * Evento, blur en el <input iva>
		 *
		 * 1 Recalcula los totales 
		 */
		$("#iva").blur(function(){
			recalcularTotales();
		});
	
		/**
		 * Evento, change en el <input asistentes>
		 *
		 * 1 Valida si se mostara como obligatorio los comentarios
		 */	
		$('#asistentes').change(function(){
			validaComentario();		
		});		
		
		/**
		 * Evento, blur en el <input impuestoHospedaje>
		 *
		 * 1 Recalcula los totales 
		 */
		$("#propina").blur(function(){
			recalcularTotales();
		});
		
		/**
		 * Evento, blur en el <input impuestoHospedaje>
		 *
		 * 1 Recalcula los totales 
		 */
		$("#impuestoHospedaje").blur(function(){
			recalcularTotales();
		});
		
		/**
		 * Evento, click en el <button registrarPartida>
		 *
		 * 1 Genera un objeto que esta lleno en base a todos los elementos del form
		 * 2 Valida los campos obligatorios
		 * 3 Valida que el proveedor exista
		 * 4 Valida el monto del anticipo
		 * 5 Valida los montos amex
		 * 6 Verifica el modo edicion 
		 * 7 a)	Agrega partida
		 * 7 b) i)	Agrega partida
		 * 7 b) ii)	Muestra botones de edicion
		 * 7 b) iii)Devuelve el estatus de edicion
		 * 8 Verifica los cargos amex para actualizar la info de los mismos
		 * 9 Habilita los campos de guardadoPrevio y envioComprobacion
		 * 10 lanza un triger del evento realizaco cuando se selecciona una solicitud para limpiar los divs necesarios
		 * 11 calcula el resumen
		 * 12 resetea los campos de las partidas
		 */
		$("#registrarPartida").click(function(){
			var tablaLength = obtenTablaLength("comprobacion_table");
			var objeto = generaObjetoFormulario();
			var invitadosGts = $("#span_totalInvitados").html();
			if(objeto["concepto"] == 1){
				objeto["asistentes"] = invitadosGts;
			}
			var impuestoRet = j("#impuestoRetencion").html();
			var traslado = j("#impuestoTraslado").html();
			var divDesc= j("#divDescuento").html();
			var validados = false;			
			var obligatoriosGlobal = validaRequeridos($("#table_infoGeneral"));
			
			if(obligatoriosGlobal){
				if($("#tipoComprobacion option:selected").val() == "AMEX"){
					var obligatoriosAmex = validaRequeridos($("#table_Amex"));
					
					if(obligatoriosAmex){
						var obligatorios = validaRequeridos($("#table_regisrtoPartidas"));
						if(obligatorios)
							validados = true;
					}
				}else{
					var obligatorios = validaRequeridos($("#table_regisrtoPartidas"));
					if(obligatorios)
						validados = true;
				}
			}
			
			if(validados){
				var proveedor = validaProveedor();		
				if(proveedor){
					var anticipo = validaSumaAnticipo(modoEdicionPartida);
					if(anticipo){
						var amex = validaSumaCargos(modoEdicionPartida);	
						if(amex){
						
							if(!modoEdicionPartida){
								
								var id = tablaLength+1;
								var renglon = creaRenglon(objeto, id);	
								agregaRenglon(renglon, "comprobacion_table");
								var Excepcion = validaPolitica(objeto, Parametros, conceptosPoliticasFecha, false, conceptosPoliticasAsistentes);
								if(Excepcion != false){
									var renglonExcepcion = crearRenglonExcepcion(Excepcion, id);
									agregaRenglon(renglonExcepcion, "excepcion_table");									
								}
								if(j.trim(traslado)!=''){
									var inputs = j("#impuestoTraslado").find(j("input") );
									var impuestoRet = false;
									for (i = 0; i < inputs.length; i++) {
										var impuesto = j("#impuestoTraslado"+i).val();
										var tipo = j("#impuestoTraslado"+i).attr('tipo');
											if(tipo == "IVA"){
											var param = "ivaTrasladado=ivaTrasladado";
											var json = obtenJson(param);
											var cpid = json["cp_id"];
											var concepto = json["cp_concepto"];
											}
											if(tipo == "IEPS"){
											var param = "IEPS=IEPS";
											var json = obtenJson(param);
											var cpid = json["cp_id"];
											var concepto = json["cp_concepto"];
											}
									var tablaLength = obtenTablaLength("comprobacion_table");
									var id = tablaLength+1;
									var renglonTraslado = creaRenglon(objeto, id, traslado,concepto,cpid,impuestoRet,impuesto);	
									agregaRenglon(renglonTraslado, "comprobacion_table");											
									}
								}
								if(j.trim(impuestoRet)!=''){
									var inputs = j("#impuestoRetencion").find(j("input") );
									for (i = 0; i < inputs.length; i++) {
										var impuesto = j("#impuestoRetencion"+i).val();
										var tipo = j("#impuestoRetencion"+i).attr('tipo');
										if(tipo == "IVA"){
											var param = "retIVA=retIVA";
											var json = obtenJson(param);
											var cpid = json["cp_id"];
											var concepto = json["cp_concepto"];
										}
										if(tipo == "ISR"){
											var param = "retISR=retISR";
											var json = obtenJson(param);
											var cpid = json["cp_id"];
											var concepto = json["cp_concepto"];
										}
										var tablaLength = obtenTablaLength("comprobacion_table");
										var id = tablaLength+1;
										var renglonRet = creaRenglon(objeto, id, traslado,concepto,cpid,impuestoRet,impuesto);	
										agregaRenglon(renglonRet, "comprobacion_table");									
									}
								}
								if(j.trim(divDesc)!=''){
									var param = "descuento=descuento";
									var json = obtenJson(param);
									var cpid = json["cp_id"];
									var concepto = json["cp_concepto"];									
									var descuento = j("#valDesc").val();
									var tablaLength = obtenTablaLength("comprobacion_table");
									var concepto = "Descuento";
									var impuestoRet = false;
									var id = tablaLength+1;
									var renglonDesc = creaRenglon(objeto, id, traslado,concepto,cpid,impuestoRet,descuento,divDesc);
									agregaRenglon(renglonDesc, "comprobacion_table");
								}
								$("#monto").removeAttr("readonly");
								$("#monto").removeAttr("disabled");
								$("#folio").removeAttr("readonly");
								$("#folio").removeAttr("disabled");
								$("#rfc").removeAttr("readonly");
								$("#rfc").removeAttr("disabled");
								$("#proveedor").removeAttr("readonly");
								$("#proveedor").removeAttr("disabled");
								$("#muestraAgregarProveedor").show();
								$("#factura").attr('checked', false);
								$("#impuestoTraslado").val("");
								j( "#trFact" ).hide();
								j( "#trProveedor" ).hide();
								j( "#impuestoTraslado" ).html("");
								j( "#impuestoRetencion" ).html("");
								j( "#divDescuento" ).html("");
								ocultarElemento("tr_kilometraje");
								j( "#anticipo" ).hide();
								var ant = $( "#anticipo").val();
								var rowCount = j('#comprobacion_table tr').length;
								var rowTotal = 0;
								var rowIVA = 0;
								var rowRetIVA = 0;
								var rowRetISR = 0;
								var rowDesc = 0;
								var rowIeps = 0;

								var rowTotalRbl = 0;
								var rowIVARbl = 0;
								var rowRetIVARbl = 0;
								var rowRetISRRbl = 0;
								var rowDescRbl = 0;
								var rowIepsRbl = 0;
								
								var rowTotalAnt = 0;
								var rowIVAAnt = 0;
								var rowRetIVAAnt = 0;
								var rowRetISRAnt = 0;
								var rowDescAnt = 0;
								var rowIepsAnt = 0;
								
								var rowTotalAMEX = 0;
								var rowIVAAMEX = 0;
								var rowRetIVAAMEX = 0;
								var rowRetISRAMEX = 0;
								var rowDescAMEX = 0;
								var rowIepsAMEX = 0;
								
								var rowConceptoAnt = '';
									for (i = 1; i < rowCount; i++) {
										rowTipoComp = j( "#div_row_tipoComprobacion"+i).html();
										rowTipoComp = j.trim(rowTipoComp);
										rowConcepto = j( "#div_row_conceptoTexto"+i).html();
											switch(rowConcepto) {
												case "Impuesto de IVA":
													rowIVA += parseFloat(j( "#div_row_total"+i).html().replace(/,/g, ""));
													rowIVARbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowIVAAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowIVAAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													break;
												case "Retenciones IVA":
													rowRetIVA += parseFloat(j( "#div_row_total"+i).html().replace(/,/g, ""));
													rowRetIVARbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowRetIVAAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowRetIVAAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													break;
												case "Retenciones ISR":
													rowRetISR += parseFloat(j( "#div_row_total"+i).html().replace(/,/g, ""));
													rowRetISRRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowRetISRAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowRetISRAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													break;													
												case "Descuento":
													rowDesc += parseFloat(j( "#div_row_total"+i).html().replace(/,/g, ""));
													rowDescRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowDescAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowDescAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													break;
												case "IEPS":
													rowIeps += parseFloat(j( "#div_row_total"+i).html().replace(/,/g, ""));
													rowIepsRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowIepsAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowIepsAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													break;							
												default:
													rowTotal += parseFloat(j( "#div_row_total"+i).html().replace(/,/g, ""));
													rowTotalRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowTotalAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													rowTotalAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
													}
									}
									j( "#div_res_subtotal").html("$"+rowTotal.toFixed(2));
									j( "#div_res_desc").html("$"+rowDesc.toFixed(2));
									j( "#div_res_iva").html("$"+rowIVA.toFixed(2));
									j( "#div_res_ret_iva").html("$"+rowRetIVA.toFixed(2));
									j( "#div_res_ret_isr").html("$"+rowRetISR.toFixed(2));
									j( "#div_res_ieps").html("$"+rowIeps.toFixed(2));
									
									j( "#div_res_subtotal_ant").html("$"+rowTotalAnt.toFixed(2));
									j( "#div_res_desc_ant").html("$"+rowDescAnt.toFixed(2));
									j( "#div_res_iva_ant").html("$"+rowIVAAnt.toFixed(2));
									j( "#div_res_ret_iva_ant").html("$"+rowRetIVAAnt.toFixed(2));
									j( "#div_res_ret_isr_ant").html("$"+rowRetISRAnt.toFixed(2));
									j( "#div_res_ieps_ant").html("$"+rowIepsAnt.toFixed(2));
									
									j( "#div_res_subtotal_amex").html("$"+rowTotalAMEX.toFixed(2));
									j( "#div_res_desc_amex").html("$"+rowDescAMEX.toFixed(2));
									j( "#div_res_iva_amex").html("$"+rowIVAAMEX.toFixed(2));
									j( "#div_res_ret_iva_amex").html("$"+rowRetIVAAMEX.toFixed(2));
									j( "#div_res_ret_isr_amex").html("$"+rowRetISRAMEX.toFixed(2));
									j( "#div_res_ieps_amex").html("$"+rowIepsAMEX.toFixed(2));									
										var TotalReembolso = rowTotalRbl - rowDescRbl + rowIVARbl - rowRetIVARbl - rowRetISRRbl - rowIepsRbl;
										j( "#div_total_reembolso").html("$"+TotalReembolso.toFixed(2));
										var TotalAnticipo = rowTotalAnt - rowDescAnt + rowIVAAnt - rowRetIVAAnt - rowRetISRAnt - rowIepsAnt;
										j( "#div_comp_anticipo").html("$"+TotalAnticipo.toFixed(2));
										var TotalAMEX = rowTotalAMEX - rowDescAMEX + rowIVAAMEX - rowRetIVAAMEX - rowRetISRAMEX - rowIepsAMEX;
										j( "#div_comp_amex").html("$"+TotalAMEX.toFixed(2));
										var TotalPendiente = ant - TotalAnticipo;
										j( "#div_pendiente").html("$"+TotalPendiente.toFixed(2));
										var div_totalSuma = TotalReembolso + TotalAnticipo +TotalAMEX;
										j( "#div_totalSuma").html("$"+div_totalSuma.toFixed(2));
										j( "#anticipoComprobado").val(div_totalSuma.toFixed(2));
							}else{
								var renglon = creaRenglon(objeto, modoEdicionPartida);
								modificarRenglon(renglon, modoEdicionPartida, "comprobacion_table");
								
								validaPolitica(objeto, Parametros, conceptosPoliticasFecha, false, conceptosPoliticasAsistentes);
								
								asignaVal("registrarPartida", "     Registrar Gasto");
								for(var i = 1; i <= tablaLength; i++){
									mostrarElemento("div_row_eliminar"+i);
									mostrarElemento("div_row_editar"+i);			
								}				
								modoEdicionPartida = false;				
							}
							
							validaConceptoComidasRepresentacion();
							validaCargoAmex(modoEdicionPartida);
							habilitaElemento("guardarPrevio");
							habilitaElemento("enviarComprobacion");
							$("#tipoComprobacion").val(0).trigger("change");
							calcularResumen();
							ocultarElemento("table_regisrtoPartidas");
							mostrarElemento("table_regisrtoPartidas");
							
							for(var i = 0; i < requeridos.length; i++)
								asignaClass(requeridos[i],"req");
							
							asignaVal("fecha", obtenFecha());
						}				
					}
				}					
			}			
			
		});	
	
		/**
		 * Evento, click en el <button editarPartida>
		 *
		 * 1 Obtiene el cargo de la tarjeta para actualizar su estado y devolverlo a 0 
		 * 2 Restablecemos las opciones del combo Concepto
		 * 3 Cargamos los datos de la partida al formulario
		 * 4 Cargamos los datos del Concepto de Comidas de Representacion
		 * 5 Deshabilita los botones de guardarPrevio y EnviarComprobacion
		 * 6 Oculta los botones para eliminar y editar
		 * 7 Asigna a modoEdicion partida el id de la partida en edicion
		 * 8 Recalcula el Resumen de la comprobacion
		 */
		$(".editarPartida").live('click', function(){
			var id = $(this).attr("id");
			var tablaLength = obtenTablaLength("comprobacion_table");
			
			actualizaIdamex($("#row_cargoTarjeta"+id).val(),0);			
			var renglon =  obtenRenglonTabla(id, "comprobacion_table");
			cargarDatosEdicion(renglon, id);
			cargaDatosComidarepresentacion(renglon, id);
			asignaVal("registrarPartida", "     Guardar Cambios");
			deshabilitaElemento("guardarPrevio");
			deshabilitaElemento("enviarComprobacion");
			for(var i = 1; i <= tablaLength; i++){
				ocultarElemento("div_row_eliminar"+i);
				ocultarElemento("div_row_editar"+i);			
			}
			modoEdicionPartida = id;
			calcularResumen();
		});
		
		/**
		 * Evento, click en el <button eliminarPartida>
		 *
		 * 1 Verificar si el concepto a eliminar es Comidas de Representacion, limpiaremos la tabla de invitados
		 * 2 Actualizamos el estatus amex del cargo relacionado a la partida que se desea eliminar
		 * 3 Borra el renglo seleccionado		 
		 * 4 Verifica si la tabla tiene mninguna partida para desahabilitar los botones de guardarPrevio  y enviarComprobacion
		 * 5 Recalcula el Resumen de la comprobacion		 
		 */
		$(".eliminarPartida").live('click', function(){
			var tablaLength = obtenTablaLength("comprobacion_table");
			var id = $(this).attr("id");
			var fiadt = $(this).attr("fidat");
			var ident = $(this).attr("ident");			
			if(confirm("Se borrara toda la informaci\u00f3n relacionada a esta partida.")){
				if(fiadt > 0){
					var borraFactura = "borraFactura=borraFactura&idF="+fiadt;
					var json = obtenJson(borraFactura);
					alert("Se borro la factura de la partida");
					$('.attrfid'+fiadt).each(function(event){
						var idf = $(this).attr("id");
						actualizaIdamex($("#row_cargoTarjeta"+idf).val(),0);
						var renglon =  obtenRenglonTabla(idf, "comprobacion_table");
						borrarRenglon(idf, "comprobacion_table");
					});
				}else{
					$('.identificador'+ident).each(function(event){
						var idf = $(this).attr("id");
						actualizaIdamex($("#row_cargoTarjeta"+idf).val(),0);
						var renglon =  obtenRenglonTabla(idf, "comprobacion_table");
						borrarRenglon(idf, "comprobacion_table");
					});
				}
				if(tablaLength == 0){
					deshabilitaElemento("guardarPrevio");
					deshabilitaElemento("enviarComprobacion");
				}
						var ant = $( "#anticipo").val();
		var rowCount = j('#comprobacion_table tr').length;
		var rowTotal = 0;
		var rowIVA = 0;
		var rowRetIVA = 0;
		var rowRetISR = 0;
		var rowDesc = 0;
		var rowIeps = 0;

		var rowTotalRbl = 0;
		var rowIVARbl = 0;
		var rowRetIVARbl = 0;
		var rowRetISRRbl = 0;
		var rowDescRbl = 0;
		var rowIepsRbl = 0;
		
		var rowTotalAnt = 0;
		var rowIVAAnt = 0;
		var rowRetIVAAnt = 0;
		var rowRetISRAnt = 0;
		var rowDescAnt = 0;
		var rowIepsAnt = 0;
		
		var rowTotalAMEX = 0;
		var rowIVAAMEX = 0;
		var rowRetIVAAMEX = 0;
		var rowRetISRAMEX = 0;
		var rowDescAMEX = 0;
		var rowIepsAMEX = 0;
		
		var rowConceptoAnt = '';
			for (i = 1; i < rowCount; i++) {
				rowTipoComp = j( "#div_row_tipoComprobacion"+i).html();
				rowTipoComp = j.trim(rowTipoComp);
				rowConcepto = j( "#div_row_conceptoTexto"+i).html();
					switch(rowConcepto) {
						case "Impuesto de IVA":
							rowIVA += parseFloat(j( "#div_row_total"+i).html().replace(/,/g, ""));
							rowIVARbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowIVAAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowIVAAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							break;
						case "Retenciones IVA":
							rowRetIVA += parseFloat(j( "#div_row_total"+i).html().replace(/,/g, ""));
							rowRetIVARbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowRetIVAAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowRetIVAAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							break;
						case "Retenciones ISR":
							rowRetISR += parseFloat(j( "#div_row_total"+i).html().replace(/,/g, ""));
							rowRetISRRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowRetISRAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowRetISRAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							break;													
						case "Descuento":
							rowDesc += parseFloat(j( "#div_row_total"+i).html().replace(/,/g, ""));
							rowDescRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowDescAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowDescAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							break;
						case "IEPS":
							rowIeps += parseFloat(j( "#div_row_total"+i).html().replace(/,/g, ""));
							rowIepsRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowIepsAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowIepsAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							break;							
						default:
							rowTotal += parseFloat(j( "#div_row_total"+i).html().replace(/,/g, ""));
							rowTotalRbl += (rowTipoComp == "Comprobacion de Reembolso") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowTotalAnt += (rowTipoComp == "Comprobacion de Anticipo") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							rowTotalAMEX += (rowTipoComp == "Comprobacion de AMEX") ? parseFloat(j( "#div_row_total"+i).html().replace(/,/g, "")) : 0;
							}
			}
			j( "#div_res_subtotal").html("$"+rowTotal.toFixed(2));
			j( "#div_res_desc").html("$"+rowDesc.toFixed(2));
			j( "#div_res_iva").html("$"+rowIVA.toFixed(2));
			j( "#div_res_ret_iva").html("$"+rowRetIVA.toFixed(2));
			j( "#div_res_ret_isr").html("$"+rowRetISR.toFixed(2));
			j( "#div_res_ieps").html("$"+rowIeps.toFixed(2));
			
			j( "#div_res_subtotal_ant").html("$"+rowTotalAnt.toFixed(2));
			j( "#div_res_desc_ant").html("$"+rowDescAnt.toFixed(2));
			j( "#div_res_iva_ant").html("$"+rowIVAAnt.toFixed(2));
			j( "#div_res_ret_iva_ant").html("$"+rowRetIVAAnt.toFixed(2));
			j( "#div_res_ret_isr_ant").html("$"+rowRetISRAnt.toFixed(2));
			j( "#div_res_ieps_ant").html("$"+rowIepsAnt.toFixed(2));
			
			j( "#div_res_subtotal_amex").html("$"+rowTotalAMEX.toFixed(2));
			j( "#div_res_desc_amex").html("$"+rowDescAMEX.toFixed(2));
			j( "#div_res_iva_amex").html("$"+rowIVAAMEX.toFixed(2));
			j( "#div_res_ret_iva_amex").html("$"+rowRetIVAAMEX.toFixed(2));
			j( "#div_res_ret_isr_amex").html("$"+rowRetISRAMEX.toFixed(2));
			j( "#div_res_ieps_amex").html("$"+rowIepsAMEX.toFixed(2));									
				var TotalReembolso = rowTotalRbl - rowDescRbl + rowIVARbl - rowRetIVARbl - rowRetISRRbl - rowIepsRbl;
				j( "#div_total_reembolso").html("$"+TotalReembolso.toFixed(2));
				var TotalAnticipo = rowTotalAnt - rowDescAnt + rowIVAAnt - rowRetIVAAnt - rowRetISRAnt - rowIepsAnt;
				j( "#div_comp_anticipo").html("$"+TotalAnticipo.toFixed(2));
				var TotalAMEX = rowTotalAMEX - rowDescAMEX + rowIVAAMEX - rowRetIVAAMEX - rowRetISRAMEX - rowIepsAMEX;
				j( "#div_comp_amex").html("$"+TotalAMEX.toFixed(2));
				var TotalPendiente = ant - TotalAnticipo;
				j( "#div_pendiente").html("$"+TotalPendiente.toFixed(2));
				var div_totalSuma = TotalReembolso + TotalAnticipo +TotalAMEX;
				$( "#div_totalSuma").html("$"+div_totalSuma.toFixed(2));
				$( "#anticipoComprobado").val(div_totalSuma.toFixed(2));	
				for (i = 1; i < 50; i++) {
					var renglon =  obtenRenglonTabla(i, "comprobacion_table");
					verificaExcepcion(renglon, i, Parametros, conceptosPoliticasFecha, conceptosPoliticasAsistentes);
				}						
				for (i = 1; i < 100; i++) {
					var renglon =  obtenRenglonTabla(i, "comprobacion_table");
					verificaExcepcion(renglon, i, Parametros, conceptosPoliticasFecha, conceptosPoliticasAsistentes);
				}						
			}
		});
		
		/**
		 * Evento, click en el <button button_eliminar>
		 *
		 * 1 Borra el renglon seleccionado		 
		 * 2 Actualiza el número de invitados
		 * 3 Verifica la politica para pedir que las Observaciones sean obligatorias
		 */
		$(".button_eliminar").live('click', function(){
			var tablaLength = obtenTablaLength("invitado_table");
			var id = $(this).attr("id");
			var renglon =  obtenRenglonTabla(id, "invitado_table");
			borrarRenglon(id, "invitado_table");
			obtenTotalInvitados();
			verificaExcepcion(renglon, id, Parametros, conceptosPoliticasFecha, conceptosPoliticasAsistentes);
		});	
		
		/**
		 * Evento, click en el <button guardarPrevio>
		 *
		 * 1 Pedimos confirmacion de la accion
		 * 2 Ocultamos los botones de enviarComprobacion y guardarPrevio
		 * 3 Realizamos un submit del formulario
		 */
		$("#guardarPrevio").click(function(){			
			if(confirm("Desea Guardar este previo?")){
				ocultarElemento("table_botones");
				validaPoliticas(Parametros, conceptosPoliticasNoches, conceptosPoliticasDias, conceptosPoliticasInvitados, conceptosPoliticasAsistentes);
				habilitaElemento();
				asignaVal("totalPartidas", obtenTablaLength("comprobacion_table"));
				
				$("#form_comprobacion").attr("action", "controller_comprobacion_gastos.php?modo=previo");				
				$("#form_comprobacion").submit();
				
			}			
		});	
		
		/**
		 * Evento, click en el <button enviarComprobacion>
		 *
		 * 1 Pedimos confirmacion de la accion
		 * 2 Validamos que los cargos amex sean completamente comprobados
		 * 3 validamos si existe algun concepto de gasolina
		 * 3 a) si existe concepto de gasolina
		 * 3 a)	i)  Cargamos tipos de auto
		 * 3 a)	ii) Mostramos Div de gasolina
		 * 3 a)	iii)Volvemos requeridos los campos del div
		 * 3 b)	i)  Ocultamos los botones de enviarComprobacion y guardarPrevio
		 * 3 b)	ii)  Realizamos un submit del formulario
		 */
		$("#enviarComprobacion").click(function(){	
			if(confirm("Desea Guardar la Comprobacion?")){
				if(validaSumaCargosCompletos(modoEdicionPartida)){			
					if(!validaGasolina()){
						habilitaElemento();
						ocultarElemento("table_botones");
						habilitaElemento();
						validaPoliticas(Parametros, conceptosPoliticasNoches, conceptosPoliticasDias, conceptosPoliticasInvitados, conceptosPoliticasAsistentes);
						asignaVal("totalPartidas", obtenTablaLength("comprobacion_table"));
						$("#form_comprobacion").attr("action", "controller_comprobacion_gastos.php?modo=comprobacion");
						$("#form_comprobacion").submit();
					}else{			
						obtenModeloAuto();
						mostrarElemento("div_gasolina");
						$("#div_gasolina").center();
						asignaClass("modeloAuto");
						asignaClass("kilometraje");
						asignaVal("enviarComprobacionGasolina", "     Aceptar");
						asignaVal("cancelarGasolina", "     Cancelar");				
					}
				}
			}
		});		
		
		/**
		 * Evento, inicializa <input kilometraje>
		 *
		 * 1 Asgina una mascara al campo
		 */
		$('#kilometraje').priceFormat({
			prefix: '', centsSeparator: '.', thousandsSeparator: ','
		});	
		
		/**
		 * Evento, change en el <select modeloAuto>
		 *
		 * 1 Obtiene le monto factor del modelo seleccionada
		 * 2 Obtiene el monto por la gasolina
		 */
		$("#modeloAuto").change(function(){
			obtenFactor();		
			obtenMontoGasolina();
		});
		
		/**
		 * Evento, blur en el <input kilometraje>
		 *
		 * 1 Obtiene le monto factor del modelo seleccionada
		 * 2 Obtiene el monto por la gasolina
		 */
		$("#kilometraje").blur(function(){
			obtenFactor();		
			obtenMontoGasolina();
		});		
		
		/**
		 * Evento, click en el <button cancelarGasolina>
		 *
		 * 1 Cierra el div gasolina
		 */
		$("#cancelarGasolina").click(function(){
			ocultarElemento("div_gasolina");			
		});
		
		/**
		 * Evento, click en el <button cerrar_div_gasolina>
		 *
		 * 1 Cierra el div gasolina
		 */
		$("#cerrar_div_gasolina").click(function(){
			ocultarElemento("div_gasolina");			
		});				
		
		/**
		 * Evento, click en el <button enviarComprobacionGasolina>
		 *
		 * 1 Confirma ejecuccion de la ccion 
		 * 2 Valida requeridos de la pantalla de gasolina
		 * 3 Envia formulario
		 */
		$("#enviarComprobacionGasolina").click(function(){			
			if(confirm("Desea Guardar la Comprobacion?")){
				var requeridos = validaRequeridos($("#div_gasolina"));	
				if(requeridos){
					validaPoliticas(Parametros, conceptosPoliticasNoches, conceptosPoliticasDias, conceptosPoliticasInvitados, conceptosPoliticasAsistentes);
					habilitaElemento();
					asignaVal("totalPartidas", obtenTablaLength("comprobacion_table"));
					$("#form_comprobacion").attr("action", "controller_comprobacion_gastos.php?modo=gasolina");
					$("#form_comprobacion").submit();	
				}					
			}
		});	
		
		/**
		 * Verifica si es una edicion
		 *	
		 * 1 Selecciona la solicitud lanzando un trigger del evento change del select de solicitud
		 * 2 Obtiene un objeto que tiene como atributos objetos del mismo tipo del objeto de un formulario
		 * 3 Para cada objeto del tipo formulario sustituimos los valores "" y NULL por "N/A"
		 * 4 Para cada objeto del tipo formulario agregamos un renglon a la tabla de partidas
		 * 5 Obtenemos los invitados relacionados a la Comprobacion (Si no existieran invitados, cargara solo el usuario en sesion)
		 * 6 Obtenemos la informacion de lugar y ciudad donde se llevo a cabo la comida de representacion
		 * 7 Clonamos toda la informacion de las comidas de representacion, para que este disponible en una edici�n de una partida
		 * 8 cargamos las opciones de tipos de invitados en el combo correpondiente
		 * 9 Recalculamos el resumen de la comporbacion
		 */
		if($._GET("idcg") != 0){
			inicializaAmex(usuario);
			var solicitud = $("#solicitud").val();
			$("#solicitud").val(solicitud).trigger("change");
			
			asignaVal("tramiteEdit", $._GET("idcg"));
			var objetoPartidas = obtenDatosPartida($._GET("idcg"));
			var identfc = 0;
			var aux = false;			
			for(var prop in objetoPartidas){
				var partida = objetoPartidas[prop];
				for(var propPar in partida)
					partida[propPar] = (partida[propPar] == "" || partida[propPar] == null) ? "N/A" :  partida[propPar];
				
				var id = obtenTablaLength("comprobacion_table")+1;
				if(partida["concepto"] == 32 || partida["concepto"] == 47 || partida["concepto"] == 64 || partida["concepto"] == 65 || partida["concepto"] == 85){
					if(!aux)
					   aux = id-1;				    
				    identfc = aux;
				}else{
					aux = false;
					identfc = id;
				}
				partida["identificador"] = identfc;				
				
				var	renglon = creaRenglon(objetoPartidas[prop], id);
				agregaRenglon(renglon, "comprobacion_table");
				
				var cargoAmex = obtenDetalleCargo(partida["cargoTarjeta"], false);
				validaCargoAmex(modoEdicionPartida, cargoAmex["conversion_pesos"], partida["cargoTarjeta"])				
			}
			
			var solicitud = $("#solicitud").val();
			$("#solicitud").val(solicitud).trigger("change");
			
			if(verificaExistenciaComidasRepresentacion(1)){
				obtenInvitados( $._GET("idcg") );
				obtenInformacionComidasRepresentacion( $._GET("idcg") );
				resguardarInfoComidasRepresentacion();
				cargartipoInvitados();
			}
			
			calcularResumen();
		}
		
		blockUI(false);
	}
	