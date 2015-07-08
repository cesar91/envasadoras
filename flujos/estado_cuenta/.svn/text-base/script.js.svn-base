	//funcion que se agrega a JQUERY checar los checks
	jQuery.fn.getCheckboxValues = function(){
		var values = [];	
		var i = 0;
		this.each(function(){
			if(!$(this).is(':disabled'))
				values[i++] = $(this).val();
		});		
		return values;
	} 
	
	//funcion que se agrega a JQUERY checar los checks
	jQuery.fn.getCheckboxValuesDeshabilitados = function(){
		var values = [];	
		var i = 0;
		this.each(function(){
			if($(this).is(':disabled'))
				values[i++] = $(this).val();
		});		
		return values;
	} 
	
	
	//funcion que se agrega a JQUERY checar los checks
	function desactivaDeshabilitados() {
		var arr = $(".cargo:checked").getCheckboxValuesDeshabilitados();
		for ( var i=0; i < arr.length; i++){			
			$("#cargo"+arr[i]).removeAttr("checked");		
		}
	}
	
		
	//Genera el campo con los cargos que se enviaran si se confirma la accion
	function procesaCargos(){		
		var aceptados = "";
		var rechazados = "";			
		var arr = $(".cargo:checked").getCheckboxValues();
		
		for (var i = 0; i < arr.length; i++){
			aceptados+= arr[i]+"|";
			$("#cargosEnviar").val(aceptados.substring(0,aceptados.length-1));
		}					
		
		if(!confirm("¿Está seguro que desea cancelar los cargos seleccionados?")){
			return false;
		}else
			return true;
	}
	
	//controla el check general
	function seleccionarCargos(){
		if($("#cargos").is(":checked")){
			$(":check").attr("checked",true)
			$("#guardar").fadeIn(500);	
		}else{
			$(":check").removeAttr("checked")
			$("#guardar").fadeOut(500);	
		}	
		desactivaDeshabilitados();
	}
	
	//controla los checks de los cargos con el boton y el general
	function verificaChecks(id,valor){
		var activaGral = true;
		var arr = $(".cargo").getCheckboxValues();							
		for(var i = 0; i < arr.length; i++){
			if(!$("#cargo"+arr[i]).is(":checked")){
				activaGral = false;
				$("#cargos").removeAttr("checked");
			}
		}		
		if(!activaGral){
			$("#cargos").removeAttr("checked");
		}else{ 
			$("#cargos").attr("checked", "checked");
		}
		
		var arr1 = $(".cargo:checked").getCheckboxValues();		
		if (arr1.length == 0){
			$(":check").removeAttr("checked")
			$("#guardar").fadeOut(500);		
		}else if(arr1.length > 0){
			$("#guardar").fadeIn(500);	
		}
	}		
