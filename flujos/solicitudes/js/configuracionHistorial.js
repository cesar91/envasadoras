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
	 * Configuracion Regional de los calendarios en la pantalla.		
	 */
	jQuery(function($){
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
			minDate: '15/02/2013',
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
		
	$("#finicial").datepicker();
	$("#ffinal").datepicker();
	
	$("#tabs").tabs();
	
	var href = $(location).attr('href');
	var parametros = href.split('?');
	
	if(parametros.length > 1){
		var primerParametro = parametros[1].split('=');
		var tab = primerParametro[0].substr(1);
		
		switch(tab){
			case 'solicitudGastosltotal':
				$("#a_solicitud_gastos").trigger('click');
				break;
			case 'comprobacionesViajeltotal':
				$("#a_comprobaciones").trigger('click');
				break;
			case 'comprobacionesGastosltotal':
				$("#a_comprobacion_gastos").trigger('click');
				break;
			default:
				$("#a_solicitud_viaje").trigger('click');
				break;
		}
	}
}