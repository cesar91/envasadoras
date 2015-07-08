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
	var urlServices = "services/ajax_solicitudes.php";	
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
		'async':		true,
		'cache':		false,
		'type':			'post',
		'dataType':		'json',			
		'contentType':	'application/x-www-form-urlencoded; charset=utf-8'		
	});	
	
	$("#fechaJS").datepicker();
	//$("#fechaFinal").datepicker();
	//$("#fechaInicial").datepicker();
	
	blockUI(false);
}