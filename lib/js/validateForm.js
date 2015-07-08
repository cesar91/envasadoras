//si valida ps es 2; si es mail es 3, los demas son 1
function validateForm(campo,campo2,msj,tipo) {
	if(tipo ==1){
		if($("#"+campo).val()==""){
			alert(msj);
			$("#"+campo).focus();
			return false;
		}
		else return true;
	}
	else if(tipo==2){
		if(validateForm(campo,"",msj,1)){
			if($("#"+campo).val()!=$("#"+campo2).val()){
				alert("La contraseña no coincide, favor de teclearla de nuevo.");
				$("#"+campo).focus();
				return false;
			}
			else return true;
		}
		else return false;
	}
	
	else if(tipo==3){
		if(validateForm(campo,"",msj,1)){
			if ($("#"+campo).val().indexOf('@', 1) == -1 || $("#"+campo).val().indexOf('.',$("#"+campo).val().indexOf('@', 0)) == -1) {
				alert("El email es inválido");
				$("#"+campo).focus();
				return false;
			}
			else return true;
		}
		else return false;
	}
	
	else if(tipo==4){						
		if(campo.length<15 && campo.length>0){
			alert(msj);
			document.getElementById("amex2").focus();
			return false;
		}
		if(campo.length>15){
			alert(msj);
			document.getElementById("amex2").focus();
			return false;
		}
		else return true;
	}		
	else if(tipo==5){						
		if(campo.length<15 && campo.length>0){
			alert(msj);
			document.getElementById("amex2gas").focus();
			return false;
		}
		if(campo.length>15){
			alert(msj);
			document.getElementById("amex2gas").focus();
			return false;
		}
		else return true;
	}
	else if(tipo==6){
        if($("#"+campo).val()=="-1"){
        	alert(msj);
			return false;
		}
		else return true;
		}
}