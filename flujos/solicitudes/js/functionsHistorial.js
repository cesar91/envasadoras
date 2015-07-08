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
