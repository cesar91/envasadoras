	function seleccionarTodoApruebInv(){
		var valor = '';	
		$("#aprueb0Inv").is(':checked') ? valor = 1 : valor = 0;		
		if (valor == 1){
			$(".apruebInv").attr("checked", "checked");
			$("#rechaz0Inv").removeAttr("checked", "checked");
			$(".rechazInv").removeAttr("checked", "checked");	
			$("#enviarcinv").show();
		}else{
			$(".apruebInv").removeAttr("checked", "checked");				
		}	
		buscaChecks();
	}

	function seleccionarTodoRechazInv(){
		var valor = '';	
		$("#rechaz0Inv").is(':checked') ? valor = 1 : valor = 0;
		
		if (valor == 1){
			$(".rechazInv").attr("checked", "checked");
			$("#aprueb0Inv").removeAttr("checked", "checked");
			$(".apruebInv").removeAttr("checked", "checked");		
			$("#enviarcinv").show();
		}else{
			$(".rechazInv").removeAttr("checked", "checked");	
		}	
		buscaChecks();
	}
	
	function activarBotonInv(id,valor){
		var patron = /([0-9])/ig;
		var valorPatron = valor.replace(patron, ""); 
		
		if (valorPatron == 'apruebInv'){
			if ($("#apruebInv"+id).is(':checked')){ 
				$("#rechazInv"+id).removeAttr("checked", "checked");
				$("#rechaz0Inv").removeAttr("checked", "checked");		
				$("#enviarcinv").show();		
			}		
		}else{
			if($("#rechazInv"+id).is(':checked')){
				$("#apruebInv"+id).removeAttr("checked", "checked");	
				$("#aprueb0Inv").removeAttr("checked", "checked");				
				$("#enviarcinv").show();
			}
		}	
		buscaChecks();		
	}
	
	function buscaChecks(){
		jQuery.fn.getCheckboxValues = function(){
			var values = [];
			var i = 0;
			this.each(function(){
				values[i++] = $(this).val();
			});		
			return values;
		} 
		
		var arr1 = $(".apruebInv").getCheckboxValues();	
		var cheGralapr = true;		
		for(var i = 0; i < arr1.length; i++ ){
			if(!$("#apruebInv"+arr1[i]).is(':checked')){
				var cheGralapr = false;
			}		
		}
		if (!cheGralapr){
			$("#aprueb0Inv").removeAttr("checked", "checked");
		}else{
			$("#aprueb0Inv").attr("checked", "checked");
		}
		
		var arr2 = $(".rechazInv").getCheckboxValues();
		var cheGralrec = true;
		for(var i = 0; i < arr2.length; i++ ){
			if(!$("#rechazInv"+arr2[i]).is(':checked')){
				var cheGralrec = false;
			}		
		}
		if (!cheGralrec){
			$("#rechaz0Inv").removeAttr("checked", "checked");
		}else{
			$("#rechaz0Inv").attr("checked", "checked");
		}
		
		var arr3 = $(".apruebInv:checked").getCheckboxValues();	
		var arr4 = $(".rechazInv:checked").getCheckboxValues();
		if (arr3.length == 0 && arr4.length == 0){
			$("#enviarcinv").hide();		
		}
	}

// Botón enviar
function enviarNotificacionInv(){
	var aceptados = "";
	var rechazados = "";
	// Se nos permitirá saber cuales comprobaciones fueron aprobadas
	var arr = $(".apruebInv:checked").getCheckboxValues();
	for (var i = 0; i < arr.length; i++){
		aceptados+= arr[i]+"|";
		$("#tramites_aceptados").val(aceptados);
	}
		
	var arr2 = $(".rechazInv:checked").getCheckboxValues();
	for (var i = 0; i < arr2.length; i++){
		rechazados+= arr2[i]+"|";		
		$("#tramites_rechazados").val(rechazados);
	}		
}
/*###########################################################################*/
	function seleccionarTodoApruebCV(){
		var valor = '';			
		$("#apruebCV0").is(':checked') ? valor = 1 : valor = 0;		
		if (valor == 1){
			$(".apruebCV").attr("checked", "checked");
			$("#rechazCV0").removeAttr("checked", "checked");
			$(".rechazCV").removeAttr("checked", "checked");	
			$("#enviarCV").show();
		}else{
			$(".apruebCV").removeAttr("checked", "checked");				
		}	
		buscaChecksCV();
	}

	function seleccionarTodoRechazCV(){
		var valor = '';	
		$("#rechazCV0").is(':checked') ? valor = 1 : valor = 0;		
		if (valor == 1){
			$(".rechazCV").attr("checked", "checked");
			$("#apruebCV0").removeAttr("checked", "checked");
			$(".apruebCV").removeAttr("checked", "checked");		
			$("#enviarCV").show();
		}else{
			$(".rechazCV").removeAttr("checked", "checked");	
		}	
		buscaChecksCV();
	}
	
	function activarBotonInvCV(id,valor){
		var patron = /([0-9])/ig;
		var valorPatron = valor.replace(patron, ""); 
				
		if (valorPatron == 'apruebCV'){
			if ($("#apruebCV"+id).is(':checked')){ 
				$("#rechazCV"+id).removeAttr("checked", "checked");
				$("#rechazCV0").removeAttr("checked", "checked");		
				$("#enviarCV").show();		
			}		
		}else{
			if($("#rechazCV"+id).is(':checked')){
				$("#apruebCV"+id).removeAttr("checked", "checked");	
				$("#apruebCV0").removeAttr("checked", "checked");				
				$("#enviarCV").show();
			}
		}	
		buscaChecksCV();		
	}
	
	function buscaChecksCV(){
		jQuery.fn.getCheckboxValues = function(){
			var values = [];
			var i = 0;
			this.each(function(){
				values[i++] = $(this).val();
			});		
			return values;
		} 
		
		var arr1 = $(".apruebCV").getCheckboxValues();
		var cheGralapr = true;		
		for(var i = 0; i < arr1.length; i++ ){
			if(!$("#apruebCV"+arr1[i]).is(':checked')){
				var cheGralapr = false;
			}		
		}
		if (!cheGralapr){
			$("#apruebCV0").removeAttr("checked", "checked");
		}else{
			$("#apruebCV0").attr("checked", "checked");
		}
		
		var arr2 = $(".rechazCV").getCheckboxValues();
		var cheGralrec = true;
		for(var i = 0; i < arr2.length; i++ ){
			if(!$("#rechazCV"+arr2[i]).is(':checked')){
				var cheGralrec = false;
			}		
		}
		if (!cheGralrec){
			$("#rechazCV0").removeAttr("checked", "checked");
		}else{
			$("#rechazCV0").attr("checked", "checked");
		}
		
		
		var arr3 = $(".apruebCV:checked").getCheckboxValues();
		var arr4 = $(".rechazCV:checked").getCheckboxValues();
		if (arr3.length == 0 && arr4.length == 0){
			$("#enviarCV").hide();		
		}
	}


function enviarNotificacionCV(){
	var aceptados = "";
	var rechazados = "";

	var arr = $(".apruebCV:checked").getCheckboxValues();
	for (var i = 0; i < arr.length; i++){
		aceptados+= arr[i]+"|";
		$("#tramites_aceptadosCV").val(aceptados);
	}
		
	var arr2 = $(".rechazCV:checked").getCheckboxValues();
	for (var i = 0; i < arr2.length; i++){
		rechazados+= arr2[i]+"|";		
		$("#tramites_rechazadosCV").val(rechazados);
	}		
}
