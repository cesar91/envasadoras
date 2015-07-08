/**
 * Inicializa los Eventos una vez que se ha ter minado de Cargar el DOM (Document Object Model)
 */
var doc = $(document);
doc.ready(init);

function init(){
	
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
	var tramite = $("#idt").val();
	var urlServices = "services/ajax_solicitudes_gastos.php";	
	var urlIndex = ($("#hist").val() == "hist") ? "./historial.php" : "./index.php?docs=docs&type=2";
	var urlReporte = "reporte_solicitud_gastos.php";
	
	/**
	 * Configuracion Default del Objeto AJAX de JQuery
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
	
	//Ocultar Elementos
	ocultarElemento("tr_totalAnticipo", "hide");
	
	cargarExcepciones($._GET("id"));
	obtenExcepcionesPresupuesto($._GET("id"));
		
	/**
	 * Evento, click en el <button Volver>
	 *
	 * 1 Redireccionar al Index de Solicitudes
	 */
	$("#Volver").click(function(){
		Location(urlIndex);
	});
	
	/**
	 * Evento, click en el <button Imprimir>
	 *
	 * 1 Imprimir Reporte PDF de la Solicitud
	 */
	$("#Imprimir").click(function(){
		imprimir_pdf(tramite, urlReporte);
	});
	
	
}