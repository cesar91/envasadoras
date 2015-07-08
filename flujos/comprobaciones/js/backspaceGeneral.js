	function confirmaRegreso(elemento){		
		$("#"+elemento).one("keypress", function(event){
			event.stopImmediatePropagation();
			if (event.which == 8 ) {
				//Preguntar si desea abandonar la página actual
				if(confirm("¿Desea regresar a la página anterior?  Pulse 'Aceptar' para continuar ó \n\t 'Cancelar' para anular la acción.")){
					history.back(-1);
				    return true;
				}else{
				   	return false;
				}
			}
			
			if(window.event.keyCode == 8){
				//Preguntar si desea abandonar la página actual
				if(confirm("¿Desea regresar a la página anterior?  Pulse 'Aceptar' para continuar ó \n\t 'Cancelar' para anular la acción.")){
					history.back(-1);
					return true;
				}else{
					return false;
				}
			}
		});
		
		$("#"+elemento).one("keydown", function(event){
			event.stopImmediatePropagation();
			if (event.which == 8 ) {
				//Preguntar si desea abandonar la página actual
				if(confirm("¿Desea regresar a la página anterior?  Pulse 'Aceptar' para continuar ó \n\t 'Cancelar' para anular la acción.")){
					history.back(-1);
					return true;
				}else{
					return false;
				}
			}
			
			if(window.event.keyCode == 8){
				//Preguntar si desea abandonar la página actual
				if(confirm("¿Desea regresar a la página anterior?  Pulse 'Aceptar' para continuar ó \n\t 'Cancelar' para anular la acción.")){
					history.back(-1);
					return true;
				}else{
					return false;
				}
			}
		});
//		var contador = 0 ;
//		$("#"+elemento).keypress(function(event) {
//			contador = incContador();
//			if (event.which == 8 ) {
//				do{
//					//Preguntar si desea abandonar la página actual
//					if(confirm("¿Desea regresar a la página anterior?  Pulse 'Aceptar' para continuar ó \n\t 'Cancelar' para anular la acción.")){
//						history.back(-1);
//				        return true;
//				   	}else{
//				       	return false;
//				    }
//				}while(contador == 1);
//			}
//			
//			if(window.event.keyCode == 8){
//				do{
//				    //Preguntar si desea abandonar la página actual
//					if(confirm("¿Desea regresar a la página anterior?  Pulse 'Aceptar' para continuar ó \n\t 'Cancelar' para anular la acción.")){
//				        return true;
//				    }else{
//				        return false;
//				    }
//				}while(contador == 1);
//			}
//			
//		});
//		
//		$("#"+elemento).keydown(function(event) {
//			contador = incContador();
//			if (event.which == 8 ) {
//				do{
//					//Preguntar si desea abandonar la página actual
//					if(confirm("¿Desea regresar a la página anterior?  Pulse 'Aceptar' para continuar ó \n\t 'Cancelar' para anular la acción.")){
//						history.back(-1);
//				        return true;
//				   	}else{
//				       	return false;
//				    }
//				}while(contador == 1);
//			}
//			
//			if(window.event.keyCode == 8){
//				do{
//				    //Preguntar si desea abandonar la página actual
//					if(confirm("¿Desea regresar a la página anterior?  Pulse 'Aceptar' para continuar ó \n\t 'Cancelar' para anular la acción.")){
//				        return true;
//				    }else{
//				        return false;
//				    }
//				}while(contador == 1);				
//			}
//			
//		});
	}
